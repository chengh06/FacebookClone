<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
// Myfacebook view header
// Author: chh
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title> Welcome to <?php echo $siteTitle; ?> </title>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/css.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/style.css" />
		<link rel="stylesheet" media="screen" href="<?php echo base_url();?>scripts/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css" />
		<script type="text/javascript">
			var baseUrl	= '<?php echo base_url();?>';
		</script>
		<script type="text/javascript" src="<?php echo base_url();?>scripts/prototype.js"></script>
		<script type="text/javascript" src="<?php echo base_url();?>scripts/ajax.js"></script>
		<script type="text/javascript" src="<?php echo base_url();?>scripts/functions.js"></script>
		<script type="text/javascript" src="<?php echo base_url();?>scripts/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js"></script>
		<script type="text/javascript" src="<?php echo base_url();?>images/dropdown/js/menu-for-applications.js"></script>		
		
	</head>
	<body>
	<table width="778" border="0" align="center" cellpadding="0" cellspacing="0">
	<?php if($loggedIn): ?>
		<tr>
			<td width="133" background="<?php echo base_url();?>images/headerBG.jpg"><img src="<?php echo base_url();?>images/logo.jpg" width="133" height="55" /></td>
			<td colspan="2" background="<?php echo base_url();?>images/headerBG.jpg">
				<table width="627" height="43" border="0" cellpadding="0" cellspacing="0">
				<tr>
				<td width="438" height="25">
					<table width="86%" border="0" cellpadding="0" cellspacing="0" class="Whitetxt">
						<tr>
							<td width="15%" class="Whitetxt">
								<div align="center">
								<a href="<?php echo base_url();?>index.php/profile/user/<?php if(isset($userId))echo $userId;?>" class="Whitelink">
								<strong>Profile</strong>
								</a>
								</div>														
							</td>
							<td width="11%">
								<div align="center">
								<a href="<?php echo base_url();?>index.php/editprofile" class="Whitelink"><strong>Edit</strong></a>
								</div>
							</td>
							<td width="20%">
								<div id="friendsMenu"></div>
								<script type="text/javascript">
								var menuModel2 = new DHTMLSuite.menuModel();
								menuModel2.addItem(101,'<a href="<?php echo base_url();?>index.php/friends" class="Whitelink"><strong>Friends</strong></a>','','',false);
								menuModel2.addItem(1011,'<a href="<?php echo base_url();?>index.php/friends/status" class="BlueLink"><strong>Status Updates</strong></a>','','',101);
								menuModel2.addItem(1012,'<a href="<?php echo base_url();?>index.php/friends/onlineNow" class="BlueLink"><strong>Online Now</strong></a>','','',101);
								menuModel2.addItem(1013,'<a href="<?php echo base_url();?>index.php/friends/view" class="BlueLink"><strong>All Friends</strong></a>','','',101);
								menuModel2.addItem(1014,'<a href="<?php echo base_url();?>index.php/friends/invite" class="BlueLink"><strong>Invite Friends</strong></a>','','',101);
								menuModel2.setSubMenuWidth(101,150);
								menuModel2.init();
								var menuBar2 = new DHTMLSuite.menuBar();
								menuBar2.addMenuItems(menuModel2);
								menuBar2.setTarget('friendsMenu');
								menuBar2.init();
								</script>
							</td>
							<td width="20%">
								<div id="networksMenu"></div>
								<script type="text/javascript">
								/* Networks menu model */
								var menuModel3 = new DHTMLSuite.menuModel();
								menuModel3.addItem(201,'<a href="<?php echo base_url();?>index.php/networks" class="Whitelink"><strong>Networks</strong></a>','','',false);
								menuModel3.addItem(2011,'<a href="<?php echo base_url();?>index.php/networks/regions" class="BlueLink"><strong>Browse All Networks</strong></a>','','',201);
								menuModel3.addItem(2012,'<a href="<?php echo base_url();?>index.php/networks/join_network" class="BlueLink"><strong>Join a Network</strong></a>','','',201);
								menuModel3.setSubMenuWidth(201,150);
								menuModel3.init();
								var menuBar3 = new DHTMLSuite.menuBar();
								menuBar3.addMenuItems(menuModel3);
								menuBar3.setTarget('networksMenu');
								menuBar3.init();
								</script>
							</td>
							<td width="20%">
								<div id="inboxMenu"></div>
								<script type="text/javascript">
								/* Inbox menu model */
								var menuModel4 = new DHTMLSuite.menuModel();
								menuModel4.addItem(301,'<a href="<?php echo base_url();?>index.php/messages/inbox" class="Whitelink"><strong>Message</strong></a>','','',false);
								menuModel4.addItem(3011,'<a href="<?php echo base_url();?>index.php/messages/inbox" class="BlueLink"><strong>Message Inbox</strong></a>','','',301);
								menuModel4.addItem(3012,'<a href="<?php echo base_url();?>index.php/messages/sent" class="BlueLink"><strong>Sent Messages</strong></a>','','',301);
								menuModel4.addItem(3013,'<a href="<?php echo base_url();?>index.php/messages/compose" class="BlueLink"><strong>Compose Message</strong></a>','','',301);
								menuModel4.setSubMenuWidth(301,150);
								menuModel4.init();
								var menuBar4 = new DHTMLSuite.menuBar();
								menuBar4.addMenuItems(menuModel4);
								menuBar4.setTarget('inboxMenu');
								menuBar4.init();
								</script>
							</td>
				        </tr>
					</table>
				</td>
				<td width="189">
					<table width="100%" border="0" align="right" cellpadding="0" cellspacing="0">
						<tr>
							<td><div align="center"><a href="<?php echo base_url();?>index.php/home" class="Lbluelink">Home</a></div></td>
							<td><div align="center"><a href="<?php echo base_url();?>index.php/account" class="Lbluelink">Account</a></div></td>
							<td><div align="center"><a href="<?php echo base_url();?>index.php/privacy" class="Lbluelink">Privacy</a></div></td>
							<td><div align="center"><a href="<?php echo base_url();?>index.php/logout" class="Lbluelink">Logout</a></div></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			</table>
			</td>
			<td width="18" background="<?php echo base_url();?>images/headerBG.jpg"><img src="<?php echo base_url();?>images/header-right.jpg" width="18" height="55" /></td>	
		</tr>
		<tr>
			<td height="108" valign="top">
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="loginBox">
					<tr>
						<td height="110" bgcolor="#f7f7f7" style="padding:.5em">
							<table width="121" border="0" align="center" cellpadding="0" cellspacing="0">
								<tr>
									<td width="121" height="16" class="blktxt"><strong>Search&nbsp;<img src="<?php echo base_url();?>images/search-DA.jpg" width="7" height="7" /></strong></td>
								</tr>
								<tr>
									<td height="32" valign="top">
									<form method="post" action="<?php echo base_url();?>index.php/search">
									<input type="hidden" name="submit_left_search" id="submit_left_search" value="1">
									<input name="search_for_left" id="search_for_left" type="text" size="14"  style="background-image:url(<?php echo base_url();?>images/search-icon.jpg); background-repeat:no-repeat; background-position:.5em center">
									</form>
									</td>
								</tr>
								<tr>
									<td height="24" class="blktxt">
										<table width="100%" border="0" cellspacing="4" cellpadding="0">
											<tr>
												<td width="18%" height="19"><a href="#" class="BlueLink" onMouseDown="new track_moveable(this.parentNode, this);"><img src="<?php echo base_url();?>images/photo-icon.jpg" width="18" height="16" border="0" /></a></td>
												<td width="82%"><a href="<?php echo base_url();?>index.php/photos" class="BlueLink" onMouseDown="new track_moveable(this.parentNode, this);">Photos</a></td>
											</tr>
											<tr>
												<td><div align="center"><img src="<?php echo base_url();?>images/groups-icon.jpg" width="16" height="13" /></div></td>
												<td><a href="<?php echo base_url();?>index.php/groups" class="BlueLink" onMouseDown="new track_moveable(this.parentNode, this);">Groups</a></td>
											</tr>
											<tr>
												<td><div align="center"><img src="<?php echo base_url();?>images/events-icon.jpg" width="15" height="15" /></div></td>
												<td><a href="<?php echo base_url();?>index.php/events" class="BlueLink" onMouseDown="new track_moveable(this.parentNode, this);">Events</a></td>
											</tr>
											<tr>
												<td><div align="center"><img src="<?php echo base_url();?>images/video-icon.gif" width="15" height="15" /></div></td>
												<td><a href="<?php echo base_url();?>index.php/vblog" class="BlueLink" onMouseDown="new track_moveable(this.parentNode, this);">Videos</a></td>
											</tr>
											
										</table>
										<div id="app_list">
											<div>
												<div id="2305272732"><a href="#" class="BlueLink" onMouseDown="new track_moveable(this.parentNode, this);"></a></div>
											</div>
											<div>
												<div id="2361831622"><a href="#" class="BlueLink" onMouseDown="new track_moveable(this.parentNode, this);"></a></div>
											</div>
											<div>
												<div id="2344061033"><a href="#" class="BlueLink" onMouseDown="new track_moveable(this.parentNode, this);"></a></div>
											</div>
											<div>
												<div id="2328908412"><a href="#" class="BlueLink" onMouseDown="new track_moveable(this.parentNode, this);"></a></div>
											</div>
										</div>
									</td>
								</tr>
								
							</table>
						</td>
					</tr>				
				</table>
			</td>
			<td width="10" rowspan="2" background="<?php echo base_url();?>images/body-left.jpg">&nbsp;</td>
			<td width="617" rowspan="2" valign="top">					
	<?php endif;?>
	<?php if($loggedIn!=true):?>
	<tr>
		<td width="133" background="<?php echo base_url();?>images/headerBG.jpg"><img src="<?php echo base_url();?>images/logo.jpg" width="133" height="55" /></td>
		<td colspan="2" background="<?php echo base_url();?>images/headerBG.jpg">
			<table width="100%" height="43" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%" height="25">&nbsp;</td>
					<td width="50%">
						<table width="41%" border="0" align="right" cellpadding="0" cellspacing="0">
							<tr>
								<td><div align="center"><a href="<?php echo base_url();?>index.php/login" class="Lbluelink">Login</a></div></td>
								<td><div align="center"><a href="<?php echo base_url();?>index.php/register" class="Lbluelink">Register</a></div></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</td>
		<td width="18" background="<?php echo base_url();?>images/headerBG.jpg"><img src="<?php echo base_url();?>images/header-right.jpg" width="18" height="55" /></td>
	</tr>
	<tr>
		<td height="217" valign="top">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="loginBox">
		<?php if(isset($center_login)):?>
		<!--<tr height="50"><td></td></tr> -->
		<tr height="50"><td><a href="<?php echo base_url();?>index.php/register" class="BlueLink"><strong>Sign Up</strong><br>Everyone can join</a></td></tr>
		<?php endif; ?>
		<?php if(!isset($center_login)):?>
		<tr>
        	<td height="110" bgcolor="#f7f7f7" style="padding:.5em">
				<form method="post" action="<?php echo base_url();?>index.php/login" name="login_form" id="login_form">
				<input type="hidden" name="login_submit" value="1">
				<table width="93%" border="0" align="center" cellpadding="0" cellspacing="0">
					<tr>
					<td height="16" class="blktxt"><strong>Email:</strong></td>
					</tr>
					<tr>
					<td><input name="email" type="text" class="txtfiled" id="email" size="14" /></td>
					</tr>
					<tr>
					<td height="16" class="blktxt"><strong>Password:</strong></td>
					</tr>
					<tr>
					<td><input name="pass" type="password" class="txtfiled" id="pass" size="14" /></td>
					</tr>
					<tr>
					<td height="29"><label>
					  <input onClick="document.login_form_submit" type="image" name="imageField" id="imageField" src="<?php echo base_url();?>images/login-btn.jpg" />
					</label></td>
					</tr>
					<tr>
					<td height="19"><a href="<?php echo base_url();?>index.php/reset" class="BlueLink">Forgot Password?</a></td>
					</tr>
				</table>
				</form>
			</td>
		</tr>
		<?php endif;?>
		</table>
		</td>
		<td width="10" background="<?php echo base_url();?>images/body-left.jpg">&nbsp;</td>
        <td width="617" valign="top">
	<?php endif;?>
		
