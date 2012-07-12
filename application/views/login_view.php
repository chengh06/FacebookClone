<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$this->load->view('header');
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr><td colspan="2"><div class="blktitle"><?php echo $siteTitle;?> Login</div></td></tr>
	<tr><td colspan="2" width="90%"><br /><?php if(isset($error)) echo $error ;?><br /></td></tr>
	<tr>
		<td width="20%"></td>
		<td width="80%">
			<form method="post" action="<?php echo base_url();?>index.php/login" name="center_login_form" id="center_login_form">
			<input type="hidden" name="login_submit" value="1">
			<table width="100%" align="center">
				<tr>
					<td width="20%">Email</td><td ><input type="text" id="email" name="email" class="YellowTextField" size="33"/></td>
				</tr>
				<tr><td>Password</td><td><input type="password" id="pass" name="pass" value="" class="YellowTextField" size="33"/></td></tr>
				<tr><br /><td></td>
				<td height="29"><label>
					  <input onClick="document.center_login_form" type="image" name="imageField" id="imageField" src="<?php echo base_url();?>images/login-btn.jpg" />
					</label> or <a href="<?php echo base_url();?>index.php/register" class="BlueLink"><strong>Sign up for <?php echo $siteTitle;?></strong></td>					
				</tr>
				<tr><td></td>
					<td><a href="<?php echo base_url();?>index.php/reset" class="BlueLink">Forgot your password</a></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>


<?php
$this->load->view('footer');
?>