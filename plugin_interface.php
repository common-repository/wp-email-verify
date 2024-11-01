<?php
add_action('admin_menu', 'email_checker_create_menu');
add_action("admin_print_styles", 'email_checker_adminCsslibs');
add_filter( 'is_email', 'email_checker_validate');

if ( ! class_exists( 'Email_Validation_HnS' ) ) {
	class Email_Validation_HnS {
		private $options = NULL;
		var $slug;
		var $basename;

		public function __construct() {
			add_action( 'init', array( &$this, 'plugin_init' ) );
		}

		public function plugin_init() {
			//load_plugin_textdomain( $this->slug, FALSE, $this->slug . '/languages' );
			add_filter( 'ninja_forms_submit_data', array( $this, 'forms_submit_data'));
		}

		public function forms_submit_data( $form_data ) {
		    foreach( $form_data[ 'fields' ] as $key=>$field ) { // Field settigns, including the field key and value.
		    	$value = $field['value'];
		    	if(preg_match('/@.+\./', $value)){
		    		if(!email_checker_validate($value)){
						// This is the email field
		    			$field_id = $field['id'];
		    			// validate
			    		$form_data['errors']['fields'][$field_id] = __( 'Please enter an email address to validate', $this->slug );
		    		}		    		
		    	}
			}
			return $form_data;
		}
	}

	$email_validation_HnS = new Email_Validation_HnS();
}

function email_checker_adminCsslibs(){
	wp_register_style('emailchecker-admin-style', plugins_url('/css/email-check_admin.css', __FILE__));
    wp_enqueue_style('emailchecker-admin-style');
}
		
function email_checker_create_menu() {
	add_options_page('WP Email Verify', 'WP Email Verify', 'administrator', __FILE__, 'email_checker_settings_page');	
}

function email_checker_settings_page() {
	include('includes/main.php');
}

function email_checker_validate($emailAddress){
	$emailchecker_credit 	= get_option('emailchecker_credit');
	$api_key					= get_option('emailchecker_api_key');
	if (!empty($api_key) && $emailchecker_credit > 0){
		if ($emailchecker_credit > 0){
		$verfication_url	= 'http://verify.hostandsoft.com/api/api.php?secret='.$api_key.'&email='.$emailAddress;
		$verfication_response = wp_remote_get( $verfication_url, array( 'timeout' => 60 ) );
		
		if ( !is_wp_error( $verfication_response ) ) {
			$verfication_response = json_decode($verfication_response['body'],true);
			if ($verfication_response['update_emailchecker_credit'] == 'yes'){
				update_option('emailchecker_credit', sanitize_text_field($verfication_response['remaining_credit']));
				update_option('emailchecker_used_credits', sanitize_text_field($verfication_response['used_credits']));
				update_option('emailchecker_usd_balance', sanitize_text_field($verfication_response['usd_balance']));
				update_option('emailchecker_rate', sanitize_text_field($verfication_response['rate']));
				email_checker_update_stats($verfication_response['status']);
			}
$emailchecker_blocked_domains_list = get_option('emailchecker_blocked_domains_list');
$emailchecker_blocked_emails_list = get_option('emailchecker_blocked_emails_list');
$emailchecker_allowed_domains_list = get_option('emailchecker_allowed_domains_list');
$emailchecker_allowed_emails_list = get_option('emailchecker_allowed_emails_list');
			if ($verfication_response['status'] == "ok"){


				//if you mean to get domain from email address , then this should work
				if(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/" , $emailAddress))
				{
				list($username,$domain)=split('@',$emailAddress);
					if (strpos($emailchecker_blocked_domains_list, $domain) !== false) {
						if (strpos($emailchecker_allowed_emails_list, $emailAddress) !== false) {
							return true;
						}else{
							return false;
						}
						//return false;
					}else{
						if (strpos($emailchecker_blocked_emails_list, $emailAddress) !== false) {
							return false;
						}else{
							return true;
						}
					}
				}	
			}else{
								//if you mean to get domain from email address , then this should work
				if(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/" , $emailAddress))
				{
				list($username,$domain)=split('@',$emailAddress);
					if(strpos($emailchecker_allowed_domains_list, $domain) !== false){
						
						if (strpos($emailchecker_blocked_emails_list, $emailAddress) !== false) {
							return false;
						}else{
							return true;
						}
						
						//return true;	
					}else{
						if (strpos($emailchecker_allowed_emails_list, $emailAddress) !== false) {
							return true;
						}else{
							return false;
						}
					}
				}
				//return false;	
			}}else{
				return true;	
			}
	}}
}

// FOR GRAVITY FORM
add_filter( 'gform_field_validation', 'email_checker_gform_field_validation' , 10, 4 );
function email_checker_gform_field_validation ( $result, $value, $form, $field ) {
    if ( $field->type == 'email' && $result['is_valid'] ) {
		if (email_checker_validate($value) == false){
			 $result['is_valid'] = false;
             $result['message']  = empty( $field->errorMessage ) ? "Email address doesn't exists." : $field->errorMessage;
		}
    }
    return $result;
}

function email_checker_update_stats($status){
	$all_email_checked =	get_option('all_email_checked');
	if (empty($all_email_checked)):
		$all_email_checked = 0;
	endif;
	$all_email_checked++;
	update_option('all_email_checked', sanitize_text_field($all_email_checked));
	
	$day_email_check_stat =	get_option('day_email_check_stat');
	if (empty($day_email_check_stat)):
		$day_email_check_stat	= array();
	else:
		$day_email_check_stat	= json_decode($day_email_check_stat, true);
	endif;
	
	if (array_key_exists(date('Y-m-d'),$day_email_check_stat)){
		$day_email_check_stat[date('Y-m-d')][$status]++;
	} else {
		$day_email_check_stat[date('Y-m-d')][$status] = 1;
	}
	krsort($day_email_check_stat);
	$day_email_check_stat = array_slice($day_email_check_stat, 0, 30);
	update_option('day_email_check_stat', sanitize_text_field(json_encode($day_email_check_stat)));	
}