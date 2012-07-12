<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$this->load->view('header');
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<?php if(isset($result_msg)):?>
				<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="splborder">
					<tr>
						<td height="41" bordercolor="#ec8a00" bgcolor="#feffcf"><span class="blktitle"><?php echo $result_msg;?></span> </td>
					</tr>
				</table>
			<?php endif;?>
			<?php if(isset($account_notifications)):?>
				<table width="100%" border="0">
					<tr height="50">
						<td>
							<span id="view_all" class="tab">
								<a href="<?php echo base_url();?>index.php/account/settings"  class="TabBlackLink">Settings</a>
							</span>
							<span id="create_blog" class="tab">
								<a href="<?php echo base_url();?>index.php/account/networks" class="TabBlackLink">Networks</a>
							</span>
							<span id="notify" class="tabactive">
								<a href="<?php echo base_url();?>index.php/account/notifications" class="TabWhiteLink">Notifications</a>
							</span>
						</td>					
					</tr>
				</table>
				<?php echo $notification_content;?>
			
			<?php elseif(isset($account_networks)):?>
				<table width="100%">
					<tr height="50">
						<td>
							<span id="view_all" class="tab">
								<a href="<?php echo base_url();?>index.php/account/settings"  class="TabBlackLink">Settings</a>
							</span>
							<span id="create_blog" class="tabactive">
								<a href="<?php echo base_url();?>index.php/account/networks" class="TabWhiteLink">Networks</a>
							</span>
							<span id="notify" class="tab">
								<a href="<?php echo base_url();?>index.php/account/notifications" class="TabBlackLink">Notifications</a>
							</span>
						</td>
					</tr>				
				</table>
				<div class="editor_panel clearfix">
					<div class="account_note"><?php echo $siteTitle; ?> is made up of many networks, each based around a <a href="<?php echo $currentPage;?>index.php/networks/workplace">workplace</a>, 
					<a href="<?php echo $currentPage;?>index.php/networks/region">region</a>,
					<a href="<?php echo $currentPage;?>index.php/networks/school">high school</a> or 
					<a href="<?php echo $currentPage;?>index.php/networks/college">college</a>.  Join a network to discover the people who work, live or study around you.
					</div>
					<table>
						<tr>
							<td width="100" align="center" colspan="2">
								<div id="confirm_dialog" style="border-width:medium; border-color:#999999; border-style:solid; display:none;" align="center">
									<table height="100">
										<tr><td>Do you really want to leave from this network?</td></tr>
										<tr>
											<td>
												<form name="leave_dialog_form" id="leave_dialog_form">
												<input type="hidden" name="leave_network_id" id="leave_network_id">
												<input type="button" name="leave_network_button" id="leave_network_button" value="Yes" onClick="leave_network('<?php echo $currentPage;?>');">
												<input type="button" value="Cancel" onClick="close_div('confirm_dialog')">
												</form>
											</td>
										</tr>
									</table>
								</div>
								<div id="cancel_dialog" style="border-width:medium; border-color:#999999; border-style:solid; display:none;" align="center">
									<table height="100">
										<tr><td>Do you really want to cancel your request?</td></tr>
										<tr>
											<td>
												<form name="cancel_dialog_form" id="cancel_dialog_form">
												<input type="hidden" name="cancel_network_id" id="cancel_network_id">
												<input type="button" name="cancel_network_button" id="cancel_network_button" value="Yes" onClick="cancel_network('<?php echo $currentPage;?>');">
												<input type="button" value="Cancel" onClick="close_div('cancel_dialog')">
												</form>
											</td>
										</tr>
									</table>
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top" width="60%">
								<div id="network_left_column" class="column left">
								<?php echo $network_content;?>								
								</div>
							</td>
							<td  width="40%">
								<div class="column right">
									<div><?php if(isset($success)):?><table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="splborder"><tr><td height="41" bordercolor="#ec8a00" bgcolor="#feffcf"><span class="blktitle"><?php echo $success;?></span> </td></tr></table><?php endif;?></div>
									<div><?php if(isset($error)): ?><table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="splborder"><tr><td height="41" bordercolor="#ec8a00" bgcolor="#feffcf"><span class="blktitle"><?php echo $error;?></span> </td></tr></table><?php endif;?></div>
									<div class="action_box" style="border-width:medium; border-color:#999999; border-style:solid;">
										<h4>Join a High School Network</h4>
										<p>Enrolling at a new school? 
											<span>Enter your new email address to join the school's network.</span>
										</p>
										<div id="join_school_result"></div>
										<form method="post" action="<?php echo $currentPage;?>index.php/account/networks" name="school_network_form" id="school_network_form">
										<input type="hidden" name="network_type" id="network_type" value="school">
										<label >School Email:</label><input type="text" class="inputtext" id="network_email" name="network_email" value="" />
										<div class="buttons"><input type="submit" class="inputsubmit" id="save_network" name="save_network" value="Join School Network" />
										</div>
										</form>
									</div>
									<div class="action_box" style="border-width:medium; border-color:#999999; border-style:solid;">
										<h4>Join a College Network</h4>
										<p>Enrolling at a new college? 
										<span>Enter your new email address to join the college network.</span>
										</p>
										<form method="post" action="<?php echo $currentPage;?>index.php/account/networks" name="college_network_form" id="college_network_form">
										<input type="hidden" name="network_type" id="network_type" value="college">
										<label >College Email:</label><input type="text" class="inputtext" id="network_email" name="network_email" value="" />
										<div class="buttons"><input type="submit" class="inputsubmit" id="save_network" name="save_network" value="Join College Network" />
										</div>
										</form>
									</div>
									<div class="action_box" style="border-width:medium; border-color:#999999; border-style:solid;">
										<h4>Join a Work Network</h4>
										<p>Working somewhere new? 
										<span>Enter your new work email to join the workplace's network.</span>
										</p>
										<form method="post" action="<?php echo $currentPage;?>index.php/account/networks" name="company_network_form" id="company_network_form">
										<input type="hidden" name="network_type" id="network_type" value="work">
										<label >Work Email:</label><input type="text" class="inputtext" id="network_email" name="network_email" value="" />
										<div class="buttons"><input type="submit" class="inputsubmit" id="save_network" name="save_network" value="Join Work Network" />
										</div>
										</form>
									</div>
								</div>
							</td>
						</tr>
					</table>
				</div>
			<?php else:?>
				<table width="100%">
				<tr>
					<td>
					<?php if(isset($success)):?>
					<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="splborder"><tr><td height="41" bordercolor="#ec8a00" bgcolor="#feffcf"><span class="blktitle"><?php echo $success;?></span> </td></tr></table>
					<?php endif;?>
					</td>
				</tr>
				<tr>
					<td>
					<?php if(isset($error)):?>
					<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="splborder"><tr><td height="41" bordercolor="#ec8a00" bgcolor="#feffcf"><span class="blktitle"><?php echo $error;?></span> </td></tr></table>
					<?php endif;?>
					</td>
				</tr>
				<tr height="50">
					<td>
					<span id="view_all" class="tabactive">
						<a href="<?php echo $currentPage;?>index.php/account/settings"  class="TabWhiteLink">Settings</a>
					</span>
					<span id="create_blog" class="tab">
						<a href="<?php echo $currentPage;?>index.php/account/networks" class="TabBlackLink">Networks</a>
					</span>
					<span id="create_blog" class="tab">
						<a href="<?php echo $currentPage;?>index.php/account/notifications" class="TabBlackLink">Notifications</a>
					</span>
					</td>
				</tr>
				</table>
				<table width="100%">
				<tr>
				<td width="10%"></td>
				<td width="80%">
				
					<div class="editor_panel clearfix">
					<?php if(isset($currentEmail)):?>
						<div id="password" class="settings_panel">
							<h4>Change Password</h4>
							<ul><li><span> Make sure your CAPS-lock key isn't on</span></li><li><span> It must be at least 6 characters long, but more is better</span></li><li><span> Using both letters and numbers is even better</span></li><li><span> 'QUAils' isn't the same password as 'quails'</span></li></ul>
							<form method="post" action="<?php echo $currentPage;?>index.php/account/settings" name="password_form">
								<input id="password_settings" name="password_settings" value="1" type="hidden">
								<table class="editor" border="0" cellspacing="0">
									<tbody>
										<tr class="password old_password">
											<td class="label">Old Password:</td>
											<td><input class="inputpassword" id="old_password" name="old_password" value="" type="password"></td>
										</tr>
								
										<tr class="password new_password">
											<td class="label">New Password:</td>
											<td><input class="inputpassword" id="new_password" name="new_password" value="" type="password"></td>
										</tr>
								
										<tr class="password confirm_password">
											<td class="label">Confirm:</td>
											<td><input class="inputpassword" id="confirm_password" name="confirm_password" value="" type="password"></td>
										</tr>
										<tr>
											<td colspan="2"><div class="buttons"><input class="inputsubmit" id="save_password" name="save_password" value="Change Password" type="submit"></div></td>
										</tr>
									</tbody>
								</table>
							</form>
						</div>
					<?php endif;?>
						<div id="secq" class="settings_panel"><h4>Change Security Question</h4><p>You can change your security question and answer here.  We use these to help identify you as the owner of your <?php echo $siteTitle;?>account if you ever need to write us for help.</p>
							<form method="post" action="<?php echo $currentPage;?>index.php/account/settings" name="security_form">
							<input id="security_settings" name="security_settings" value="1" type="hidden">
							<table class="editor" border="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="label">Question:</td>
										<td><?php echo $question;?></td>
									</tr>
									<tr class="text answer">
										<td class="label">Answer:</td>
										<td><input id="answer" name="answer" class="inputtext" value="" type="text"></td></tr>
									<tr>
										<td colspan="2"><div class="buttons"><input class="inputsubmit" id="save_question" name="save_question" value="Change Security Question" type="submit"></div></td>
									</tr>
								</tbody>
							</table>
							</form>
						</div>
						<div id="time_zone" class="settings_panel"><h4>Change Time Zone</h4>
							<form method="post" action="<?php echo $currentPage;?>index.php/account/settings" name="timezone_form">
							<input id="timezone_settings" name="timezone_settings" value="1" type="hidden">
							<table class="editor" border="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="label">Time Zone:</td>
										<td><select class="inputtext" name="tz" id="tz">
											<option value=""></option>
											
													<option value="{$timezone[$smarty.foreach.outer.iteration].zoneId}" selected>{$timezone[$smarty.foreach.outer.iteration].timeZone}</option>
												
													<option value="{$timezone[$smarty.foreach.outer.iteration].zoneId}">{$timezone[$smarty.foreach.outer.iteration].timeZone}</option>
											
											</select>
										</td>
									</tr>
									<tr>
										<td colspan="2">
										<div class="buttons">
										<input class="inputsubmit" id="save_timezone" name="save_timezone" value="Change Time Zone" type="submit">
										</div>
										</td>
									</tr>
								</tbody>
							</table>
							</form>
						</div>
						<div id="change_name" class="settings_panel"><h4>Change Name</h4>
							<p>You can request to change your name here. We confirm all name changes before they take effect. Sometimes this takes a little while, so please be patient.</p>
							<form method="post" action="<?php echo $currentPage;?>index.php/account/settings" name="username_form">
							<input id="username_settings" name="username_settings" value="1" type="hidden">
							<table class="editor" border="0" cellspacing="0">
								<tbody>
									<tr><td colspan="2"><span>Your real name &nbsp;<?php echo $user_name;?></span></td></tr>
									<tr class="text new_name">
										<td class="label">New Name:</td>
										<td><input id="new_name" name="new_name" class="inputtext" value="" type="text"></td>
									</tr>
									<tr>
										<td colspan="2">
										<div class="buttons">
										<input class="inputsubmit" id="save_name" name="save_name" value="Change Name" type="submit">
										</div>
										</td>
									</tr>
								</tbody>
							</table>
							</form>
						</div>
					</div>
				</td>
				<td width="10%"></td>
				</tr>
				</table>				
			<?php endif;?>
		</td>	
	</tr>
</table>
<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$this->load->view('footer');
?>