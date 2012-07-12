<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$this->load->view('header');
?>
<?php if(isset($invalid_page)):?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr><td><font color="red">Invalid Page!</font></td></tr>
	</table>
<?php endif;?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" >
	<tr>
		<td width="100%">
		<table ><tr><td width="10%"></td><td width="90%">
		<div id="registerSuccess" style="display:none;" align="left">Confirm Your Email Address<br><br>
												Thanks for registering! We just sent you a confirmation email to <div id="registerEmail"></div>.<br><br>
												Click on the confirmation link in the email to complete your registration.
		</div>
		<div id="registerResult" align="left"></div>
		<div id="registration">
			<table class="editor" border="0" cellspacing="0">
				<tr>
					<td colspan="2" align="left">
						<h2 style="border-bottom: 1px solid #D8DFEA;">Register and Start using <?php echo $siteTitle; ?></h2>
						<h3 style="padding: 10px 0px 0px 0px;">Once you join <?php echo $siteTitle;?>, you'll be able to look up and <strong>connect with your friends</strong>, <strong>share photos</strong>, and <strong>create your own profile</strong>.  To get started, fill out the form below <small>(all fields are required to register).</small></h3>
					</td>
				</tr>
			</table>
			<form method="post" action="" name="r_form" id="r_form">
			<table class="editor" border="0" cellspacing="0" >
				<tr><td colspan="2" align="center">
				<div id="ajax_loader" style="display:none;"><img src="<?php echo base_url();?>images/progressbar_long.gif" border="0"></div>
				</td></tr>
				<tr class="text name">
					<td class="label" align="right">Full Name:</td>
					<td>
						<input type="text" id="name" name="name" class="inputtext" />
					</td>
				</tr>
				<tr class="select ls">
					<td class="label" align="right">I am:</td>
					<td><?php echo $lifestage?></td>
				</tr>
				<tr class="hiddenrow" id="college_status_row">
					<td class="label"><div id="college_status_col_1" style="display:none;" align="right">School Status:</div></td>
					<td><div id="college_status_col_2" style="display:none;"><?php echo $schoolStatus;?></div></td>
				</tr>
				<tr class="hiddenrow" id="college_year_row" >
					<td class="label" align="right">
						<div id="college_year_col_1" style="display:none;">Class Year:</div>
					</td>
					<td>
						<div style="display:none;" id="college_year_col_2">
						<select id='college_year' name='college_year' class="select">
						<option value="">Select Year:</option>
						<option value="2007">2007</option>
						<option value="2008">2008</option>
						<option value="2009">2009</option>
						<option value="2010">2010</option>
						<option value="2011">2011</option>
						<option value="2012">2012</option>
						</select>
						</div>
					</td>
				</tr>
				<tr class="network_selector hiddenrow" id="high_school_network_row">
					<td class="label" align="right"><div id="high_school_network_col_1" style="display:none;">High School:</div></td>
					<td><div id="high_school_network_col_2" style="display:none;">
						<input id="high_school" name="high_school" class="inputtext" size="25" autocomplete="off"></div>
					</td>
				</tr>
				<tr class="hiddenrow" id="high_school_year_row">
					<td class="label" align="right"><div id="high_school_year_col_1" style="display:none;">Class Year:</div></td>
					<td><div id="high_school_year_col_2" style="display:none;">
						<select id='high_school_year' name='high_school_year' class="select">
						<option value="">Select Year:</option>
						<option value="2007">2007</option>
						<option value="2008">2008</option>
						<option value="2009">2009</option>
						<option value="2010">2010</option>
						</select></div>
					</td>
				</tr>
				<tr class="email">
					<td class="label" align="right">Email:</td>
					<td>
						<div><input type="text" id="register_email" class="inputtext" name="register_email"  size="30"/></div>
					</td>
				</tr>
				<tr class="password sparges" >
					<td class="label" align="right">Password:</td>
					<td>
						<input type="password" class="inputpassword" id="register_password" name="register_password" value=""/>
					</td>
				</tr>
				<tr class="birthday tallrow">
					<td class="label" align="right">Birthday:</td>
					<td>
						<?php echo $birthMonth; ?>
						<?php echo $birthDay; ?>
						<?php echo $birthYear; ?>
					</td>
				</tr>
				<tr class="information">
					<td class="label"></td>
					<td>
						<div id="security_ajax_loader" style="display:none;" align="center"><img src="<?php echo base_url();?>images/indicator_mozilla_yellow.gif" border="0"></div>
						<div id="captcha" class="captcha_registration captcha">
						<fieldset>
							<legend>Security Check</legend>
							<div id="captch_image" class="captcha_challenge">
							<?php echo $captImage; ?>
							</div>
							<div class="captcha_refresh">Can't read the text? <a href="javascript: void(0);" class="BlueLink" name="growth_captcha_refresh" onclick="generateSecurityImage('http://localhost:8080/Myfacebook/','captch_image');">Try another</a>.</div>
							<div class="captcha_input"><label>Text in the box:</label>
							<input type="text" name="captcha_response" id="captcha_response" />
							<input type="text"  style="display: none" name="randWord" id="randWord" value="<?php echo $randWord;?>" />
							<p>
								<?php echo $randWord;?>
							<p>
							</div>
						</fieldset>
						</div>
					</td>
				</tr>
				
				<tr class="checkbox terms tallrow">
					<td class="label"></td>
					<td>
						<table class="option_field" border="0" cellspacing="0">
							<tr>
								<td>
								<input type="checkbox" name="terms" id="terms"> 
								</td>
								<td><label for="terms">I have read and agree to the Terms of Use.</label>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2">
					<div class="buttons">
						<input type="button" class="inputbutton" id="submit_button" name="submit_button" value="Register Now!" onClick="register();" />
						<div style="margin: auto; width: 300px; padding-top: 10px; align: center; font-size: 9px; color: #333;">Problems with registration?  Email <a href="mailto:info@facebookclone.tld?subject=<?php echo $siteTitle; ?> Registration" class="BlueLink">info@facebookclone.tld</a> from the email address with which you are attempting to register.</div>
					</div>
					</td>
				</tr>
			</table>
			<input type="hidden" name="register_submit" value="1">
			</form>
		</div>
		</td></tr></table>
		</td>
	</tr>
</table>


<?php 
$this->load->view('footer');
?>