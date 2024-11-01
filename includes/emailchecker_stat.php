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
                        <td><strong><?php echo get_option('rem_emailchecker_credit'); ?></strong></td>                    
                    </tr>
                    
                    <tr>
                        <td width="200">Total Email Checked</td>                    
                        <td><strong><?php echo get_option('all_email_checked'); ?></strong></td>                    
                    </tr>
                    
                    <tr>
                        <td width="200">Email Checked Today</td>                    
                        <td><strong><?php echo @$day_email_check_stat[date('Y-m-d')]['failed'] + $day_email_check_stat[date('Y-m-d')]['passed']; ?></strong></td>                    
                    </tr>
                    
                    
                    <tr>
                    	<td colspan="2" align="center";>
                        	<?php if (!empty($day_email_check_stat)): 
							ksort($day_email_check_stat);
							?>
							<script type="text/javascript" src="https://www.google.com/jsapi"></script>
							<script type="text/javascript">
                              google.load("visualization", "1", {packages:["corechart"]});
                              google.setOnLoadCallback(drawChart);
                              function drawChart() {
                                 var data = google.visualization.arrayToDataTable([
								  ['Date', 'Email Passed', 'Email Failed'],
								  <?php foreach ($day_email_check_stat as $date=>$emailcheckerSend): ?>
								  	['<?php echo $date; ?>',<?php echo $emailcheckerSend['passed']; ?>,<?php echo $emailcheckerSend['failed']; ?>],
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