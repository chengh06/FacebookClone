<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
//第一版主要参考原版的common文件
//第二版将会将Common拆散成不同的模块
//

/*
1.authenticate
2.basicvars
3.homeFriendsRequest
4.getAvatar
5.listNetworks
6.getFriends
7.getUsername
8.getFooterLink
9.getSiteVars
10.validMonthDay

*/

class Common extends CI_Model
{
	function __construct()
	{
		parent:: __construct();
	}
	
	
	#########################################################################################
	
	function authenticate()
	{
		$this->load->helper('common');
		$datas		= array();
		$siteVars	= $this->getSiteVars();    //得到网站的基本参数 从数据库中得到  此函数在本文件中
		if(isset($_SESSION['user_id']))
		{
			$datas['userId']			= $_SESSION['user_id'];				//store current user id
			$datas['currentPage']		= base_url();						//store current site base url 在config.php中定义了base_url的值
			$datas['loggedIn']			= true;								//set login status as true
			$datas['siteTitle']			= $siteVars['siteTitle'];
			$datas['siteName']			= $siteVars['siteName'];
			
			$userName					= explode(" ",$this->getUsername($_SESSION['user_id']));  //explode函数，把字符串分割为数组 此处以“ ”为标记
			$datas['userName']			= $userName[0];
			$datas['footerLink']		= $this->getFooterLink();
		}
		else
		{
			$currentUrl				= $_SERVER['REQUEST_URI'];	//assign current url
			$_SESSION['loginUrl']	= $currentUrl;				//create session using current url
		}
		return $datas;								
		
	}
	
	###################################################################################################
	#Method			: basicvars()
	#Type			: Sub
	#Description	: laods help library and returns array of needed config vars with checks login
	#				  values
	##################################################################################################
	function basicvars()
	{
		$this->load->helper('common');
		$datas		= array();
		$siteVars	= $this->getSiteVars();   //得到网站的基本参数 从数据库中得到  此函数在本文件中
		if(isset($_SESSION['user_id']))
			$datas['loggedIn']		= true;	
		else
			$datas['loggedIn']		= false;
			
		$datas['currentPage']		= base_url();						//store current site base url
		$datas['siteTitle']			= $siteVars['siteTitle'];
		$datas['footerLink']		= $this->getFooterLink();
		$datas['siteName']			= $siteVars['siteName'];
		return $datas;
	}
	
	
	###################################################################################################
	#Method			: homeFriendsRequest()
	#Type			: Sub
	#Description	: list all request from friends
	#Modified by	: Kiruthika
	#Modified Date	: 13-09-2007
	#Note			: Checking for blocked users and deleting from friend list if the friend is blocked
	##################################################################################################
	function homeFriendsRequest($userId)
	{
		$friendRequestUrl	= base_url().'index.php/friends/request';
		$mainContent= '';
		$sql=mysql_query("SELECT user_id FROM `friends_list` WHERE `friend_id` =$userId and approved_status='no'"); //fetching the list request friend
		if ($sql)
		{
			while($rs=mysql_fetch_object($sql))
			{
				$sqlrec	=mysql_query("select user_id,blocked_user_id from block_users where user_id=".$userId." and  blocked_user_id=".$rs->user_id); //checking if the requested friend is blocked user or not
				$cnt	= mysql_num_rows($sqlrec);
				if($sqlrec)
				{
					while($res=mysql_fetch_object($sqlrec))
					{
						$qry=mysql_query("delete from friends_list where user_id=".$res->blocked_user_id." and friend_id=".$res->user_id); // deleting the requested friend if the requested friend is blocked person 
					}
				}
				$tot		=getTotRec("user_id","friends_list","friend_id='$userId' AND approved_status='no'");
				$totRequest	= $tot-$cnt;
				
				if($totRequest>0)
				{
					$title		= 	'<tr>
									<td bgcolor="#e9e9e9">&nbsp;&nbsp;<span class="blktxt"><strong>Requests</strong></span></td>
									</tr>';
					$content	= 	'<tr><td><div><a href="'.$friendRequestUrl.'" class="BlueLink"><img src="'.base_url().'application/images/friend.gif" border="0"> '.$totRequest.' friend request(s)</a></div></td></tr>';
					$mainContent= 	'<table width="100%" border="0" cellspacing="4" cellpadding="0">'.$title.$content.'</table>';
				}				
			}
		}			
		return $mainContent;
	}
	
	
	
	###################################################################################################
	#Method			: getAvatar()
	#Type			: sub
	#Description	: function to get the user photo
	#Arguments		: $userid
	##################################################################################################
	function getAvatar($userid)
	{
		$isExist	= getTotRec("user_id","picture_profile","user_id='$userid'");
		$profileRs	= getRow("picture_profile","thumb_path","user_id='$userid'");
		if($profileRs['thumb_path']=='' or !$isExist)
			$userAvatar	= base_url().'application/images/t_default.jpg';
		else
			$userAvatar	= $profileRs['thumb_path'];
		//echo $userAvatar;
		return $userAvatar;
	}//end getAvatar()
	
	###################################################################################################
	#Method			: listNetworks()
	#Type			: sub
	#Description	: to list all networks joined by the current user, also list the networks waiting 
	#				  for approval.
	##################################################################################################
	function listNetworks($userId)
	{
		$totPending	= getTotRec("network_user_id","network_users","user_status='pending' AND user_id='$userId' AND is_deleted='0'");
		$totApproved= getTotRec("network_user_id","network_users","user_status='approved' AND user_id='$userId' AND is_deleted='0'");
			
		//prepares pending networks
		if($totPending>0)
		{
			$query	= "SELECT  network_user_id, network_id, user_email, network_type FROM network_users WHERE 
						user_status='pending' AND user_id='$userId' AND is_deleted='0'";
			$res	= mysql_query($query);
			while($rs=mysql_fetch_object($res))
			{
				$netRs		= getRow("networks","network_name","network_id='$rs->network_id'");
				$networkName= $netRs['network_name'];
				$pendingContent	.= '<tr><td>
									<div style="border-width:1px; border-color:#999999; border-style:solid;">
										<table>
											<tr><td><strong>'.$networkName.'</strong></td></tr>
											<tr><td><small>We sent confirmation mailto <strong>'.$rs->user_email.'</strong>
												Follow the confirmation link in that email and we\'ll add you to the '.$networkName.' network.</small></td></tr>
											<tr><td><a style="text-decoration:none;" href="javascript: void(0);" onclick="resend_network_confirm(\''.$rs->network_user_id.'\',\''.base_url().'\');">Resend Confirmation Email</a>&nbsp;|&nbsp;
												<a style="text-decoration:none;" href="javascript: void(0);" onclick="show_cancel_network_dialog(\''.$rs->network_user_id.'\');">Cancel Request</a></td></tr>
										</table>
									</div>
									</td>
									<tr>';				
						}//end while
				$pendingContent	= '<table>
									<tr><td><strong>You have '.$totPending.' network waiting for confirmation</strong></td></tr>'.$pendingContent.'</table>';
			}//end pending
			
			//prepares approved networks
			if($totApproved>0)
			{
				$query	= "SELECT network_user_id, network_id, user_email, network_type FROM network_users WHERE 
							user_status='approved' AND user_id='$userId' AND is_deleted='0'";
				$res	= mysql_query($query);
				while($rs=mysql_fetch_object($res))
				{
					$netRs				= getRow("networks","network_name,network_city,network_type","network_id='$rs->network_id'");
					if($netRs['network_type']=='region')
						$networkName	= $netRs['network_city'];
					else
						$networkName		= $netRs['network_name'];
						$totUsers			= getTotRec("network_user_id","network_users","network_id='$rs->network_id' AND user_status='approved'");
						$approvedContent	.= '<tr><td>
												<div style="border-width:1px; border-color:#999999; border-style:solid;">
													<table>
														<tr><td colspan="2"><strong>'.$networkName.'</strong></td></tr>
														<tr>
															<td valign="top"><small>There are <strong>'.$totUsers.'</strong> people in the <strong>'.$networkName.'</strong> network.</td>
															<td valign="top"><a style="text-decoration:none;" href="javascript:void(0);" onclick="show_leave_network_dialog(\''.$rs->network_user_id.'\');"><small>LeaveNetwork</small></a></td>
														</tr><!---->
													</table>
												</div>
												</td>
												<tr>';				
				}//end while
				$approvedContent	= '<table>
										<tr><td><strong>You are in '.$totApproved.' network</strong></td></tr>'.$approvedContent.'</table>';
			}//end approved
			$content	='';
			if(isset($pendingContent) || isset($approvedContent))
			$content	= $pendingContent.$approvedContent;
			if($content=='')
				$content	= '<table><tr><td>You have not yet joined any network</td></tr></table>';
			return $content;
		}//end listNetworks()
	
	
	
	###################################################################################################
	#Method			: getFriends()
	#Type			: sub
	#Description	: returns friends array for the given user
	##################################################################################################
	function getFriends($userId)
	{
		$friendsArray	= array(); //声明变量
		$query	= 	"SELECT friend_id AS FriendId FROM friends_list WHERE user_id='$userId' and approved_status='yes' 
					UNION
					SELECT user_id as FriendId FROM friends_list WHERE friend_id='$userId' and approved_status='yes' ";
		$res	= mysql_query($query);
		if($res)
		{
			$totFriends	= mysql_num_rows($res);
			if($totFriends>0)
			while($rs=mysql_fetch_object($res))
							$friendsArray[]	= $rs->FriendId;
		}
		return $friendsArray;
	}
		
	
	###################################################################################################
	#Method			: getUsername()
	#Type			: sub
	#Description	: function to get the user name for the given user id
	#Arguments		: $userid
	##################################################################################################
	function getUsername($userid)
	{
		$userRs		= getRow("users","username","user_id='$userid'");  //提取数据的函数，在本文件中
		return $userRs['username'];
	}

	###################################################################################################
	#Method			: getFooterLink()
	#Type			: sub
	#Description	: function to get the footer link
	##################################################################################################
	function getFooterLink()
	{
		$siteVars		= $this->getSiteVars();
		$recPerPage		= $siteVars['recordsPerPage'];
		$currentPage	= base_url();
		$query			= mysql_query("SELECT title,page_name FROM manage_static_page ORDER BY page_index");
		if($query)			
		{
			$totRec=mysql_num_rows($query);
			if($totRec>0)
			{
				while($rs=mysql_fetch_object($query))
				{
					$title 		= $rs->title;
					$pageName	= $rs->page_name;
					$content   .= '<a href="'.$currentPage.'index.php/'.$pageName.'" class="BlueLink">'.$title.'</a>&nbsp;&nbsp;&nbsp;&nbsp;';
				}
				
			}
			else
			$content="";
		}
		return $content;
	}	
	
	
	
	###################################################################################################
	#Method			: getSiteVars()
	#Type			: Sub
	#Description	: get all site variables from database
	##################################################################################################
	function getSiteVars()
	{
			$data            = array();
			$data['adminName']		= 'FaceBook Team';				//Admin Name
			$data['adminEmail']		= 'chengh06@gmail.com';
			$data['siteTitle']		= 'FaceBook';
			$data['siteName']		= 'FacebookClone.tld';				//site name
			$data['recordsPerPage']	= 10;	
			$data['albumsOnProfile']= 5;
			$data['postsOnProfile']	= 5;	
			$data['postsCommon']	= 5;
			$data['listOnHome']		= 5;
			$data['totalAlbumsPerUser']		= 5;
			$data['totalPhotosPerAlbum']	= 5;
			$data['totalGroupsPerUser']		= 5;
			$data['totalUsersPerGroup']		= 5;
			$data['totalEventsPerUser']		= 5;
			$data['totalUsersPerEvents']	= 5;
			$data['mencoder']				= "/usr/local/bin/mencoder";
			$data['mplayer']				= "/usr/local/bin/mplayer";
			$data['videoCaptureTime']		= 30;//seconds
			$datas['recorderPath']			= '/application/skin/';	//set recorder path for video capturing
			$datas['red5SettingsPath']		= '/application/settings/';//set path to settings.xml and skin.xml
                                             // http://dev5.a.g.r..i.y.a.in/agbook1.1/application/settings/
			$datas['red5FlvPath']			= '/opt/red5/webapps/SOSample/streams/';//red5 flv stored path
			$datas['flvTool2Path']			= '/usr/bin/flvtool2';//red5 flv stored path
			$datas['red5ServerPath']		= 'rtmp://localhost/SOSample';//red5 flv stored path // rtmp://dev5.a.g.r.i.y.a.in/SOSample
			
			//再从数据库中读取数据
			$query = "SELECT 
						admin_name, admin_email, site_name, site_title, records_per_page, albums_on_profile, posts_on_profile,
						posts_common, list_on_home, total_albums_per_user, total_photos_per_album, total_groups_user_create,
						total_groups_user_join, total_events_user_create, total_events_user_join, mencoder_path, mplayer_path,
						webcam_capture_time,recorder_path,red5_path,red5_flv_path,flvtool2_path,red5_server_path
						FROM admin_settings";
						
			$res = mysql_query($query);
			if($res)
			while($rs= mysql_fetch_object($res))
			{
				if($rs->admin_name!='')
						$data['adminName']	= $rs->admin_name;
					if($rs->admin_email!='')
						$data['adminEmail']	= $rs->admin_email;
					if($rs->site_title!='')
						$data['siteTitle']	= $rs->site_title;
					if($rs->site_name!='')
						$data['siteName']	= $rs->site_name;
					if($rs->records_per_page>0)
						$data['recordsPerPage']	= $rs->records_per_page;
					if($rs->albums_on_profile>0)
						$data['albumsOnProfile']	= $rs->albums_on_profile;
					if($rs->posts_on_profile>0)
						$data['postsOnProfile']	= $rs->posts_on_profile;
					if($rs->posts_common>0)
						$data['postsCommon']	= $rs->posts_common;
					if($rs->list_on_home>0)
						$data['listOnHome']	= $rs->list_on_home;
					
					if($rs->total_albums_per_user>0)
						$data['totalAlbumsPerUser']		= $rs->total_albums_per_user;
					if($rs->total_photos_per_album>0)
						$data['totalPhotosPerAlbum']	= $rs->total_photos_per_album;
					if($rs->total_groups_user_create>0)
						$data['totalGroupsPerUser']		= $rs->total_groups_user_create;
					if($rs->total_groups_user_join>0)
						$data['totalUsersPerGroup']		= $rs->total_groups_user_join;
					if($rs->total_events_user_create>0)
						$data['totalEventsPerUser']		= $rs->total_events_user_create;
					if($rs->total_events_user_join>0)
						$data['totalUsersPerEvents']	= $rs->total_events_user_join;
					if($rs->mencoder_path!='')
						$data['mencoder']				= $rs->mencoder_path;
					if($rs->mplayer_path!='')
						$data['mplayer']				= $rs->mplayer_path;
					if($rs->webcam_capture_time>0)
						$data['videoCaptureTime']		= $rs->webcam_capture_time;
					if($rs->red5_path!='')
						$data['red5SettingsPath']		= $rs->red5_path;
					if($rs->mplayer_path!='')
						$data['recorderPath']			= $rs->recorder_path;
					if($rs->red5_flv_path!='')
						$data['red5FlvPath']			= $rs->red5_flv_path;
					if($rs->flvtool2_path!='')
						$data['flvTool2Path']			= $rs->flvtool2_path;
					if($rs->red5_server_path!='')
						$data['red5ServerPath']			= $rs->red5_server_path;
			}
			return $data;
	}
	###################################################################################################
	#Method			: validMonthDay()
	#Type			: sub
	#Description	: check valid day for the given month and day
	##################################################################################################
	function validMonthDay($month,$day)
	{
		//echo $month;
		$valid	= true;
		if(strtoupper($month)=='02' and $day>28)
			$valid=false;
		if(strtoupper($month)=='04' and $day>30)
			$valid=false;
		if(strtoupper($month)=='06' and $day>30)
			$valid=false;
		if(strtoupper($month)=='09' and $day>30)
			$valid=false;
		if(strtoupper($month)=='11' and $day>30)
			$valid=false;
		return $valid;
	}//end validMonthDay()
	###################################################################################################
	#Method			: getLargeAvatar()
	#Type			: sub
	#Description	: function to get the user large photo
	#Arguments		: $userid
	##################################################################################################
	function getLargeAvatar($userid)
		{
			$profileRs	= getRow("picture_profile","picture_path","user_id='$userid'");
			if($profileRs['picture_path']=='')
				$userAvatar	= base_url().'application/images/s_default.jpg';
			else
			{
				$userAvatar	= $profileRs['picture_path'];
			}
			return $userAvatar;
		}//end getAvatar()
	
	
}