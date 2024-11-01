<?php 
if( current_user_can('administrator') ) {
$server_status 	= get_option('emailchecker_server_status');
if ($_POST['test_server'] || empty($server_status)){
		$test_code	= date('ymdhis');
		if (check_admin_referer('register_nonce')){
		$response	= wp_remote_get('https://verify.hostandsoft.com/api/server-check.php?test_code='.$test_code);
		if ( !is_wp_error( $response ) ) {
			if ($response['body'] == $test_code){
				$server_err_stat	= 'test_successfull';
				$server_err_msg		= '';
			} else {
				$server_err_stat	= 'test_error';
				$server_err_msg 	= '<strong>Error</strong>: Sorry couldnot get response back from the server.';	
			}			
		} else {
			$server_err_stat	= 'test_error';
			$server_err_msg 	= '<strong>Error</strong>:'. $response->get_error_message();
		}
		}
		update_option('emailchecker_server_status', sanitize_text_field($server_err_stat));
		update_option('emailchecker_server_msg', sanitize_text_field($server_err_msg));
}
$server_status 	= get_option('emailchecker_server_status');
$server_message = get_option('emailchecker_server_msg');
}
?>
        <br/>
        <table class="wp-list-table widefat fixed bookmarks">
            	<thead>
                <tr>
                	<th>Instruction</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                	<td>
                    	<ol>
                        	<li>Get API key from <a href="https://verify.hostandsoft.com" target="_blank">here</a>. You can select the WP Email Verify package or get the Free Test API Key.
                            </li>
                            
                            <li>Activate it and you are done.</li>                            
                        </ol>
                    </td>
                </tr>
                </tbody>
            </table>
        
        </td>
        <td width="15">&nbsp;</td>
        <td width="250" valign="top">
        	<table class="wp-list-table widefat fixed bookmarks">
            	<thead>
                <tr>
                	<th>Server Connectivity Test</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                	<td>
                    	<div id="server_status" class="<?php echo $server_status; ?>">
                        	<?php echo str_replace('_',' ',$server_status); ?>
                        </div>						
                        
                        <?php if ($server_status == 'test_error'): ?>
						<div class="emailchecker_test_msg"><?php echo $server_message; ?></div>
                        <?php endif; ?>
                        <?php $url=wp_nonce_url('options-general.php?page=wp-email-verify/plugin_interface.php', 'register_nonce');?>
                        
                        <form action="<?=$url;?>" method="post">
                        	<p align="center">
                            <input type="submit" value="Test Again" class="button-primary" name="test_server" />
                            </p>
                        </form>
                    </td>
                </tr>
                </tbody>
            </table>
            <br/>
            <table class="wp-list-table widefat fixed bookmarks">
            	<thead>
                <tr>
                	<th>Quick Links</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                	<td>
                    <ul class="emailchecker_list">
                    	<li><a href="https://hostandsoft.com" target="_blank">Visit Main Site</a></li>
                        <li><a href="https://hostandsoft.com/web-hosting" target="_blank">Web Hosting</a></li>
                        <li><a href="https://hostandsoft.com/wordpress-hosting" target="_blank">Wordpress Hosting</a></li>
                        <li><a href="https://hostandsoft.com/vps-hosting" target="_blank">VPS Server</a></li>
                        <li><a href="https://hostandsoft.com/dedicated-servers" target="_blank">Dedicated Server</a></li>
                        <li><a href="https://hostandsoft.com/email-marketing" target="_blank">Email Marketing</a></li>
                        <li><a href="https://hostandsoft.com/contact-us" target="_blank">Contact us</a></li>
                    </ul>
                    </td>
                </tr>
                </tbody>
            </table>
            <br/>
            <table class="wp-list-table widefat fixed bookmarks">
            	<thead>
                <tr>
                	<th>Facebook</th>
                </tr>
                </thead>
                <tbody>
                <tr>
				
                	<td><iframe src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Fhostandsoftcom%2F&tabs=timeline&width=229&height=500&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=false&appId=1924287537784448" width="100%" height="500" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
                    </td>
                </tr>
                </tbody>
            </table>
            <br/>
            
        </td>
    </tr>
</table>
</div>
