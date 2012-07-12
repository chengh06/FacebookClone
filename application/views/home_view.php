<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$this->load->view('header');
?>

	<?php if($loggedIn): ?>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="73%" height="273" valign="top">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td height="82" valign="top">
								<table width="86%" border="0" align="center" cellpadding="0" cellspacing="0" class="splborder">
									<tr>
										<td height="27" bordercolor="#ec8a00" bgcolor="#feffcf"><div align="center" class="title2">
											Welcome, Your account has been created!</div>
										</td>
									</tr>
									
									<tr>
										<td height="41" bordercolor="#ec8a00" bgcolor="#feffcf"> <p align="center"><span class="blktxt">You should set up your account by <a href="<?php echo base_url();?>index.php/account" class="BlueLink">clicking </a>this.   </span><br />
										</p>   </td>
									</tr>	
								</table>
							</td>
						</tr>
						<tr><td height="200" colspan="2" align="center" ><img width="205" height="245" border="0" src="<?php echo base_url();?>images/friends4.jpg"/></td></tr>
					</table>
				</td>
				<td width="27%" valign="top" bgcolor="#f7f7f7" class="leftborder">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td>
							<?php echo $friends_request;?>
							<?php echo $friends; ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table> 
		<?php endif;?>
		<?php if($loggedIn!=true):?>
		<table width="93%" border="0" align="right" cellpadding="0" cellspacing="0">
			<tr>
			<td width="37%" height="36">&nbsp;</td>
			<td width="63%" class="loginBox"><p align="right"><span class="grytxt">Already a Member?</span> <a href="<?php echo base_url();?>index.php/login" class="BlueLink"><strong>Login</strong></a></p></td>
			</tr>
			<tr>
			<td><div align="left"><img src="<?php echo base_url();?>images/img.jpg" width="198" height="98" /></div></td>
			<td class="loginBox"><div align="left" class="titletxt"><?php echo $siteTitle;?> is a <strong>social utility</strong> that <strong>connects <br />
			  you</strong> with the people around you.</div></td>
			</tr>
			<tr>
			<td height="43">&nbsp;</td>
			<td><div id="register_content">
				<table width="218" align="right">
				<tbody>
				<tr>
				<td width="137">
					Everyone Can Join
				</td>
				<td width="69"><label>
				  <input type="image" name="imageField2" id="imageField2" src="<?php echo base_url();?>images/signup-btn.jpg" onclick="window.location='<?php echo base_url();?>index.php/register'" />
				</label></td>
				</tr>
				</tbody>
				</table>
				</div>
			 	<div align="right"></div>
			</td>
			</tr>
			<tr><td height="91" colspan="2" align="left" ><img width="400" height="160" border="0" src="<?php echo base_url();?>images/friend.png"/></td></tr>
		</table>
		<?php endif;?>

<?php 
$this->load->view('footer');
?>