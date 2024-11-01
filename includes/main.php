<?php 
$api_key = get_option('emailchecker_api_key');
	if( current_user_can('administrator') ) {
		if (!empty($api_key)){
	$api_key_return = wp_remote_get('http://verify.hostandsoft.com/api/validate-key.php?license_key='.$api_key, array( 'timeout' => 60 ));
	//{"update_emailchecker_credit":"yes","status":"ok","remaining_credit":100,"usd_balance":2.01,"rate":0.0018}
	if ( !is_wp_error( $api_key_return ) ) {
		$api_key_return = json_decode($api_key_return['body']);
		update_option('emailchecker_credit', sanitize_text_field($api_key_return->remaining_credit));
		update_option('emailchecker_used_credits', sanitize_text_field($api_key_return->used_credits));
		update_option('emailchecker_usd_balance', sanitize_text_field($api_key_return->usd_balance));
		update_option('emailchecker_rate', sanitize_text_field($api_key_return->rate));
		}
	}
	}
	
if( current_user_can('administrator') ) {  
if ($_POST['emailchecker_api_key_submit']){
	$api_key_return = wp_remote_get('https://verify.hostandsoft.com/api/validate-key.php?license_key='.$_POST['emailchecker_api_key']);
	if ( !is_wp_error( $api_key_return ) ) {
		$api_key_return = json_decode($api_key_return['body']);
		if (!empty($api_key_return)){
			if ($api_key_return->status == 'success'){
				update_option('emailchecker_api_key', sanitize_text_field($_POST['emailchecker_api_key']));
				update_option('emailchecker_credit', sanitize_text_field($api_key_return->remaining_credit));
				update_option('emailchecker_used_credits', sanitize_text_field($api_key_return->used_credits));
				update_option('emailchecker_usd_balance', sanitize_text_field($api_key_return->usd_balance));
				update_option('emailchecker_rate', sanitize_text_field($api_key_return->rate));
			}
			$emailchecker_api_msg_status	= 'updated';
			$emailchecker_api_message 		= $api_key_return->msg;
		} else {
			$emailchecker_api_msg_status	= 'error';
			$emailchecker_api_message 	= 'Sorry there was an error. Please try again.';
		}
	} else {
		$emailchecker_api_msg_status	= 'error';
		$emailchecker_api_message 		= $api_key_return->get_error_message();
	}
}

if ($_POST['emailchecker_api_key_remove']){
	delete_option('emailchecker_api_key');
	delete_option('emailchecker_credit');
	delete_option('emailchecker_used_credits');
	delete_option('emailchecker_usd_balance');
	delete_option('emailchecker_rate');
	$emailchecker_api_message 		= 'Your Activation key has been removed';
	$emailchecker_api_msg_status	= 'updated';
}

if ($_POST['emailchecker_list_submit']){
	update_option('emailchecker_blocked_domains_list', sanitize_text_field($_POST['emailchecker_blocked_domains_list']));
	update_option('emailchecker_blocked_emails_list', sanitize_text_field($_POST['emailchecker_blocked_emails_list']));
	update_option('emailchecker_allowed_domains_list', sanitize_text_field($_POST['emailchecker_allowed_domains_list']));
	update_option('emailchecker_allowed_emails_list', sanitize_text_field($_POST['emailchecker_allowed_emails_list']));
	$emailchecker_api_message 		= "Email Verification Lists updated successfully";
	$emailchecker_api_msg_status	= 'updated';
}
$emailchecker_blocked_domains_list = get_option('emailchecker_blocked_domains_list');
$emailchecker_blocked_emails_list = get_option('emailchecker_blocked_emails_list');
$emailchecker_allowed_domains_list = get_option('emailchecker_allowed_domains_list');
$emailchecker_allowed_emails_list = get_option('emailchecker_allowed_emails_list');
}
$emailchecker_credit = get_option('emailchecker_credit');
$emailchecker_used_credits = get_option('emailchecker_used_credits');
$emailchecker_usd_balance = get_option('emailchecker_usd_balance');
$emailchecker_rate = get_option('emailchecker_rate');
$emailchecker_api_key			=	get_option('emailchecker_api_key');
?>
<?php if (!empty($emailchecker_api_message)):?>
	<div class="<?php echo $emailchecker_api_msg_status ?>" id="message"><p><?php echo $emailchecker_api_message ?></p></div>
<?php endif; ?>
<?php 
			if( current_user_can('administrator') ) {
			$server_status 	= get_option('emailchecker_server_status');
			if (isset($_POST['test_server']) || empty($server_status)){
					$test_code	= date('ymdhis');
					//if (check_admin_referer('register_nonce')){
					$response	= wp_remote_get('http://verify.hostandsoft.com/api/server-check.php?test_code='.$test_code);
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
					//}
					update_option('emailchecker_server_status', sanitize_text_field($server_err_stat));
					update_option('emailchecker_server_msg', sanitize_text_field($server_err_msg));
			}
			$server_status 	= get_option('emailchecker_server_status');
			$server_message = get_option('emailchecker_server_msg');
			}
?>
<div class="wrap">
<h2>WP Email Verify</h2>
<table width="100%">
	<tr>
    	<td valign="top">
		<?php if( current_user_can('administrator') ) {  ?> 
            <table class="wp-list-table widefat fixed bookmarks">
                <thead>
                    <tr>
                        <th>API KEY</th>
                    </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                    	<form action="options-general.php?page=wp-email-verify/plugin_interface.php" method="post" >
                        API KEY :
                    	<?php if (empty($emailchecker_api_key)): ?>
                        <input name="emailchecker_api_key" type="text" style="width:350px; margin-left:50px;" maxlength="50"/>
                        <input type="submit" name="emailchecker_api_key_submit" class="button-primary" value="Verify" style="padding:2px;" />
                        <br/> <br/>                       
                        Please keep the API key to start using this plugin. Select your package or get Free TEST API key from <a href="https://verify.hostandsoft.com/" target="_blank">here</a>.<br/>
                        <?php else: ?>
                        	<span class="active_key"><?php echo $emailchecker_api_key;  ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Active</span>							<input type="submit" name="emailchecker_api_key_remove" class="button-primary" value="Remove Key" style="padding:2px; margin-left:20px;" onclick="if(!confirm('Are you sure ?')){return false;}" />
                        <?php endif;?>
                        </form>
                        <br/>                        
                        <strong>Note</strong> : Your Email Verify credits and authentication are handle by API key.
                        <br/><br/>
                   	</td>
                </tr>
                </tbody>
            </table>
			<?php } ?>
            <br/>
			<?php 
			$day_email_check_stat =	get_option('day_email_check_stat');
			if (empty($day_email_check_stat)):
				$day_email_check_stat	= array();
			else:
				$day_email_check_stat	= json_decode($day_email_check_stat, true);
			endif;	
			?>
			<table class="wp-list-table widefat fixed bookmarks">
                <thead>
                    <tr>
                        <th colspan="2">Email Verify Statistics</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="200">Remaining Verification Credit</td>                    
                        <td><strong><?php echo $emailchecker_credit; ?></strong></td>                    
                    </tr>
                    <tr>
                        <td width="200">Total Emails Verified</td>                    
                        <td><strong><?php echo $emailchecker_used_credits; ?></strong></td>                    
                    </tr>
                    <tr>
                        <td width="200">Email Checked Today</td>                    
                        <td><strong><?php echo $day_email_check_stat[date('Y-m-d')]['not_ok'] + $day_email_check_stat[date('Y-m-d')]['ok']; ?></strong></td>                    
                    </tr>
					<tr>
                        <td width="200">Balance (USD)</td>                    
                        <td><strong><?php echo $emailchecker_usd_balance; ?> USD</strong></td>                    
                    </tr>
					<tr>
                        <td width="200">Rate</td>                    
                        <td><strong>$ <?php echo $emailchecker_rate; ?> per email verified</strong></td>                    
                    </tr>
                    <tr>
                    	<td colspan="2" align="center";>
                        	<?php if (!empty($day_email_check_stat)): 
							ksort($day_email_check_stat);
							?>
							<script type="text/javascript" src="http://www.google.com/jsapi"></script>
							<script type="text/javascript">
                              google.load("visualization", "1", {packages:["corechart"]});
                              google.setOnLoadCallback(drawChart);
                              function drawChart() {
                                 var data = google.visualization.arrayToDataTable([
								  ['Date', 'Email OK', 'Email Not OK'],
								  <?php foreach ($day_email_check_stat as $date=>$emailcheckerSend): ?>
								  	['<?php echo $date; ?>',<?php echo $emailcheckerSend['ok']; ?>,<?php echo $emailcheckerSend['not_ok']; ?>],
								  <?php endforeach;?>
								]);
                        
                                var options = {
                                  title: 'Email Check Stat (Last 30 days only)',
                                  hAxis: {title: 'Date',  titleTextStyle: {color: 'red'}}
                                };
                                var chart = new google.visualization.ColumnChart(document.getElementById('emailchecker_chart_div'));
                                chart.draw(data, options);
                              }
                            </script>
                            <div id="emailchecker_chart_div" style="width:100%; height:400px; margin-top:15px; margin-bottom:10px;">
                            </div>
                            <?php else: ?>
                            	No Email Verify records found.
                            <?php endif;?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br/>
        <br/>
		<?php if( current_user_can('administrator') ) {  ?> 
 <p><strong>Note</strong> : These <strong><em>Email Verification Settings</em></strong> will override the Email Verifiation API results it works separately on each Wordpress site. However our Email Verification service will still work apart from these fields. Leave them blank, If you dont know what you are doing.
</p>
            <table class="wp-list-table widefat fixed bookmarks">
			<form action="options-general.php?page=wp-email-verify/plugin_interface.php" method="post" >
                <thead>
                    <tr>
                        <th>Email Verification Settings</th><th><input type="submit" name="emailchecker_list_submit" class="button-primary" value="Save" style="padding:2px;align:right;float: right;width: 100px;"></th>
                    </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        Blocked Domains:
						<textarea name="emailchecker_blocked_domains_list" cols="40" rows="5" style="width:350px; margin-left:50px;" value=""><?php echo $emailchecker_blocked_domains_list; ?></textarea>
                        <br/> <br/>                       
                        List domains you which to block emails from (seprated by new line or space)<br/>
                        <br/>                        
                        <br/><br/>
                   	</td>
                    <td>
                        Blocked Emails:
						<textarea name="emailchecker_blocked_emails_list" cols="40" rows="5" style="width:350px; margin-left:50px;" value=""><?php echo $emailchecker_blocked_emails_list; ?></textarea>
                        <br/> <br/>                       
                        List all email addresses you which to block (seprated by new line or space)<br/>
                        <br/>                        
                   	<br/><br/>
					</td>
                </tr>
				<tr>
                    <td>
                        Allowed Domains:
						<textarea name="emailchecker_allowed_domains_list" cols="40" rows="5" style="width:350px; margin-left:50px;" value=""><?php echo $emailchecker_allowed_domains_list; ?></textarea>
                        <br/> <br/>                       
                        List all domains you which to allow (seprated by new line or space)<br/>
                        <br/>                        
                        <br/><br/>
                   	</td>
                    <td>
                        Allowed Emails:
						<textarea name="emailchecker_allowed_emails_list" cols="40" rows="5" style="width:350px; margin-left:50px;" value=""><?php echo $emailchecker_allowed_emails_list; ?></textarea>
                        <br/> <br/>                       
                        List all email addresses you which to allow (seprated by new line or space)<br/>
                        <br/>                        
                        <br/><br/>
                   	</td>
                </tr>
                </tbody></form>
            </table>
			<?php } ?>
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
                        	<li>Add your API key from <a href="https://verify.hostandsoft.com" target="_blank">here</a>. you are ready to go with Contact Form 7, Ninja Form, CladeraForm,Gravitu Form, Calculated form field etc.
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
			