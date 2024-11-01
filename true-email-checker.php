<?php
/* 
Plugin Name: WP Email Verify
Plugin URI: https://verify.hostandsoft.com
Description: Verify Emails in real-time without sending any actual email at Contact Form 7, Ninja Form, Gravity Forms, Comments, Registrations, Woocommerce Orders, Subscribers. Kick SPAM & Verify for Real Valid Emails and detect Hard Bounced Emails. A complete validation tool of its kind.
Author: Host & Soft
Author URI:  https://hostandsoft.com
Version: 2.0.4
*/

/*  Copyright 2016  Naqi Rizvi  (email : mohammednaqi@gmail.com)
	
    This program is free software; you are not allowed to redistribute it or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
include('plugin_interface.php');
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'ec_plugin_action_links' );
function ec_plugin_action_links( $links ) {
   $links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=wp-email-verify/plugin_interface.php') ) .'">Settings</a>';
   return $links;
}
function silent_update_check() {
			$result   = _check_for_update( true ); // full response if possible is returned
			$response = $result[0];
			if ( false === $response ) {
				show_message( false, 'Error while checking for update. Can\'t reach update server. Message: ' . $result[1] );
				return;
			}
			if ( isset( $response->result ) && $response->result == 'ko' ) {
				show_message( false, $response->message );

				return;
			}
			$nv              = $response->version;
			$url             = $response->url;
			$current_version = _plugin_get( 'Version' );
			if ( $current_version == $nv || version_compare( $current_version, $nv, '>' ) ) {
				return;
			}
			$plugin_file = 'email-verification/email-verification.php';
			$upgrade_url = wp_nonce_url( 'update.php?action=upgrade-plugin&amp;plugin=' . urlencode( $plugin_file ), 'upgrade-plugin_' . $plugin_file );
			$message     = 'There is a new version of LeadPages plugin available! ( ' . $nv . ' )<br>You can <a href="' . $upgrade_url . '">update</a> to the latest version automatically or <a href="' . $url . '">download</a> the update and install it manually.';
			show_messages( true, $message );
		}
		
		function _check_for_update( $full = false ) {
			if ( defined( 'WP_INSTALLING' ) ) {
				return false;
			}
			$result   = lb_api_call( 'update-check' );
			$response = $result[0];
			if ( $full === true ) {
				return $result; // giving the full response ...
			}
			if ( $response === false ) { // we have a problem
				return array( false, $result[1] );
			}
			$current_version = _plugin_get( 'Version' );
			if ( $current_version == $response->version ) {
				return false;
			}
			if ( version_compare( $current_version, $response->version, '>' ) ) {
				return array( true, 'You have the latest version!' );
			}

			return array( $response->version, 'There is a newer version!' );
		}
static $message = false;

		function show_messages( $not_error, $message ) {
			$message = $message;
			if ( $not_error ) {
				add_action( 'admin_notices', array( &$this, 'showMessage' ) );
			} else {
				add_action( 'admin_notices', array( &$this, 'showErrorMessage' ) );
			}
		}

		function showMessage() {
			echo '<div id="message" class="updated">';
			echo '<p><strong>' . $message . '</strong></p></div>';
		}

		function showErrorMessage() {
			echo '<div id="message" class="error">';
			echo '<p><strong>' . $message . '</strong></p></div>';
		}
?>