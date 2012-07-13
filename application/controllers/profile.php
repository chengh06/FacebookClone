<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
##################################################################
//File			: profile.php
//Description	: user profile mangement
//Author		: ilayaraja_22ag06;chh
//Created On	: 12-Apr-2007
//Last change	: 4-July-2012
##################################################################
Class Profile extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		redirect("home");
	}
	
	function user()
	{
		$this->loadSettings();
		$datas		= $this->common->authenticate();
		global $userId,$now;
		if(count($datas)<1)
		{
			$datas	= $this->common->basicvars();
			$data	= $datas;
			$data['siteUrl']		= site_url();
			$data['center_login']	= true;
			$this->load->view('login_view',$data);
		}
		else
		{
			$data		= array();
			$data		= $datas;

			$profileId		= $this->uri->segment(3);//get profile id as user id
			$requestType		= $this->uri->segment(4);//get profile id as user id
			if($requestType=="msg")
			{
				$data['user_avatar']		= '<img src="'.$this->common->getLargeAvatar($profileId).'" border="0">';//get user photo
				$data['user_information']	= $this->userInformation($profileId,$userId,$requestType='msg');//get contact information

				$msgBasicStatus=$this->checkMessageSetting($no=1,$profileId);
				if ($msgBasicStatus=="yes")
				{
					$data['user_baic_info']		= $this->userBasicInfo($profileId);//basic info
				}
				$msgWorkStatus=$this->checkMessageSetting($no=5,$profileId);
				if ($msgWorkStatus=="yes")
				{
					$data['work_information']	= $this->workInformation($profileId,$userId);//get work information
				}
				$msgWallStatus=$this->checkMessageSetting($no=6,$profileId);
				if ($msgWallStatus=="yes")
				{
					$data['wall_post']			= $this->showWall($profileId,$userId,$userId);//get wall information
				}
				$msgUserStatus=$this->checkMessageSetting($no=7,$profileId);
				if ($msgUserStatus=="yes")
				{
					$data['user_status']		= $this->showStatus($profileId);//get online status
				}
				$msgScreenStatus=$this->checkMessageSetting($no=8,$profileId);
				if ($msgScreenStatus=="yes")
				{
					$data['screen_select']		= $this->userStatusSelect($profileId);
				}
				$msgFriendsStatus=$this->checkMessageSetting($no=9,$profileId);
				if ($msgFriendsStatus=="yes")
				{
					$data['friends']			= $this->showFriends($profileId,$userId);			//show friends on profile page
				}
				$msgGroupStatus=$this->checkMessageSetting($no=10,$profileId);
				if ($msgGroupStatus=="yes")
				{
					$data['groups']				= $this->showGroups($profileId,$userId);			//show groups of profile id
				}
				$data['view_profile']		= true;
				$this->load->view('profile_view',$data);
				exit;
			}
			$profileExist	= getTotRec("user_id","users","user_id='$profileId'");//checks whether user exist or not
			if($profileExist)//if exist prepare user profile home page content
			{
				if($profileId==$userId)
				{
					$uploadUrl		= base_url().'index.php/editprofile/picture';
					$isPhotoExist	= getTotRec("picture_id","picture_profile","picture_path<>'' AND user_id='$profileId'");
					if($isPhotoExist)
						$data['user_avatar']		= '<img src="'.$this->common->getLargeAvatar($profileId).'" border="0">';//get user photo
					else
						$data['user_avatar']		= '<div style="border:0px solid;background-color:#FFFFCC;"> <a href="'.$uploadUrl.'" style="text-decoration:none;"><img src="'.$this->common->getLargeAvatar($profileId).'" border="0"></a><br>To upload a picture so that your friends can find you, <a href="'.$uploadUrl.'" style="text-decoration:none;">click here.</a></div>';//get user photo
					$data['friends']			= $this->showFriends($profileId,$userId);			//show friends on profile page
					$data['wall_post']			= $this->showWall($profileId,$userId,$userId);//get contact information
					$data['user_status']		= $this->showStatus($profileId);
					$data['screen_select']		= $this->userStatusSelect($profileId);
				}
				else
				$data['user_avatar']		= '<img src="'.$this->common->getLargeAvatar($profileId).'" border="0">';//get user photo
				$data['profile_id']			= $profileId;
				$data['network_friends']	= $this->networkFriends($profileId,$userId);		//show friends in other networks
				$check3=$this->checkProfileSetting($no=3,$profileId);//check profile setting for showing friends
				if ($check3=="yes")
				{
					$data['friends']			= $this->showFriends($profileId,$userId);			//show friends on profile page
				}

				$data['photos']				= $this->showPhotos($profileId,$userId);			//show photos of profile id
				$data['groups']				= $this->showGroups($profileId,$userId);			//show groups of profile id
				$data['user_name']			= ucwords($this->common->getUsername($profileId));	//get user photo
				$check2=$this->checkProfileSetting($no=2,$profileId);//check profile setting for showing user_status
				if ($check2=="yes")
				{
					$data['user_status']		= $this->showStatus($profileId);
				}
				$data['user_baic_info']		= $this->userBasicInfo($profileId);
				$data['eduInfo']				= $this->getEduInfo($profileId,$userId);

				$data['work_information']	= $this->workInformation($profileId,$userId);//get contact information
				//$data['edu_information']	= $this->eduInformation($profileId,$userId);//get contact information

				$data['user_mini_feed']		= $this->showMiniFeed($profileId);
				$data['user_information']	= $this->userInformation($profileId,$userId);//get contact information
				$check4=$this->checkProfileSetting($no=4,$profileId);//check profile setting for  showing  walls
				if ($check4=="yes")
				{
					$data['wall_post']			= $this->showWall($profileId,$userId,$userId);//get contact information
				}
				$check1=$this->checkProfileSetting($no=1,$profileId);//check profile setting for  showing  profile
				if (($check1=="yes") || ($profileId==$userId))
				{
					$data['view_profile']		= true;
					$this->load->view('profile_view',$data);
				}
				else
					redirect("home");
			}
			else//if not exist redirect to home page
				redirect("home");
		}		
	}
	
	function checkProfileSetting($no,$profileId)
	{
		$this->loadSettings();
		$datas	= $this->common->authenticate();
		global $userId,$now;
		if(count($datas)<1)
		{
			$datas	= $this->common->basicvars();
			$data	= $datas;
			$data['siteUrl']		= site_url();
			$data['center_login']	= true;
			$this->load->view('login_view',$data);
		}
		else
		{
			$data		= array();
			$data		= $datas;
			$friendList=$this->common->getFriends($profileId);
			if(in_array($userId,$friendList))
			{
				$rec=mysql_query("select * from profile_setting_status where user_id=$profileId");
				if(($rec) && (mysql_num_rows($rec)!=0))
				{
					while($rec_res=mysql_fetch_object($rec))
					{
						if($rec_res->profile_setting_id==$no)
						{
							if(($rec_res->all_my_networks_and_all_my_friends=="yes") || ($rec_res->only_my_friends=="yes"))
							{
								$status="yes";
							}
							else
							{
								$status="no";
							}
						}
					}
				 }
			    else
			    {
				   $status="yes";
			    }
			}
            return $status;
		}
	}
	function checkMessageSetting($no,$profileId)
	{
		$this->loadSettings();
		$datas	= $this->common->authenticate();
		global $userId,$now;
		if(count($datas)<1)
		{
			$datas	= $this->common->basicvars();
			$data	= $datas;
			$data['siteUrl']		= site_url();
			$data['center_login']	= true;
			$this->load->view('login_view',$data);
		}
		else
		{
			$data		= array();
			$data		= $datas;
			$rec=mysql_query("select * from message_setting_status where user_id=$profileId");
			if(($rec) && (mysql_num_rows($rec)!=0))
			{
				while($rec_res=mysql_fetch_object($rec))
				{
					if($rec_res->message_id==$no)
					{
						if ($rec_res->message_status=="yes")
							$status="yes";
						else
							$status="no";
					}
				}
			}
			else
			    $status="yes";
			return $status;
		}
	}

	###################################################################################################
	#Method			: showFriends()
	#Type			: sub
	#Description	: show friends of given userid on user profile page
	##################################################################################################
	function showFriends($profileId,$userId)
	{
		$totFriends	= count($this->common->getFriends($profileId));
		//$totFriends	= getTotRec("friend_id","friends_list", "user_id='$profileId' AND approved_status='yes'");//get total friends for this profile id(user_id)
		if($totFriends>0)
		{
			$query		= "	SELECT friend_id AS friend_id FROM `friends_list` WHERE user_id='$profileId' and approved_status='yes'
							UNION
							SELECT user_id as friend_id FROM `friends_list` WHERE friend_id='$profileId' and approved_status='yes' ";
			$res		= mysql_query($query);
			//if($userId==$profileId);
			//$editUrl	= '<a href="'.base_url()."index.php/friends/".$profileId.'" style="text-decoration:none;">edit</a>';
			$seeAll	= base_url()."index.php/friends/view/".$profileId;//url to show all friends for this profile id
			$loop		= 1;
			$imgContent	= "<tr>";
			while($rs=mysql_fetch_object($res))
			{
				$profileUrl	= base_url()."index.php/profile/user/".$rs->friend_id;
				$imagePath	= $this->common->getAvatar($rs->friend_id);		//function which returns the avatar of the given userid
				$username	= $this->common->getUsername($rs->friend_id);	//function which returns the user name of the given userid
				$imgContent	.='<td><a href="'.$profileUrl.'" style="text-decoration:none;"><img src="'.$imagePath.'" width="50" border="0"></a><br>
								<a href="'.$profileUrl.'" style="text-decoration:none;">'.$username.'</a></td>';
				if($loop==3)//execute loop for show 3 friends per line
				{
					$loop=1;
					$imgContent	.='</tr><tr>';
				}
				else
					$loop++;
				}//end while
				$imgContent	= '<table><tr>'.$imgContent.'</tr></table>';
				$content	= '<table width="100%">
								<tr><td><strong>Friends</strong></td>
									<td align="right">'.$editUrl.'</td>
									<td align="right">
										<span id="show_friend" style="display:none;"><a href="javascript: void(0);" onclick="show_div(\'hide_friend\');show_div(\'friends_content\');close_div(\'show_friend\');" style="text-decoration:none;">show</a></span>
										<span id="hide_friend"><a href="javascript: void(0);" onclick="show_div(\'show_friend\');close_div(\'friends_content\');close_div(\'hide_friend\');" style="text-decoration:none;">hide</a></span>
									</td></tr>
								<tr><td colspan="3">
											<span id="friends_content"><table width="100%">
												<tr><td>'.$totFriends.' Friends</td><td align="right"><a href="'.$seeAll.'" style="text-decoration:none;">See All</a></td></tr>
												<tr><td colspan="2">'.$imgContent.'</td></tr></table></span>
											</td></tr>
									</table>';

		}
		else
			$content	= '';
		return $content;
	}//end showFriends()
	
	###################################################################################################
	#Method			: networkFriends()
	#Type			: sub
	#Description	: show friends in other networks of given userid on user profile page
	##################################################################################################
	function networkFriends($profileId,$userId)
	{
		$friends		= '';
		$allFriendUrl	= base_url()."index.php/friends/view/".$profileId;
		$query		=	"SELECT count( network_user_id ) AS friend_count,network_id FROM `network_users`
						WHERE user_status='approved' AND user_id IN
									(SELECT friend_id FROM friends_list WHERE user_id='$profileId' AND approved_status = 'yes')
						GROUP BY network_id";
		$res		= mysql_query($query);
		if($res)
		while($rs=mysql_fetch_object($res))
		{//get all network friends
			$netRs		= getRow("networks","network_name,network_type,network_city","network_id='$rs->network_id'");
			if($netRs['network_type']=='work')
				$network	= $netRs['network_name'];
			else
				$network	= $netRs['network_city'];
			$frndCount	= $rs->friend_count;
			$friends	.='<div><small>'.$network.'('.$frndCount.')</small></div>';
		}//end while
		//prepare network friends content to show on profile page
		$content	= 	'<table width="100%">
							<tr>
								<td><strong>Friends in other networks</strong></td>
								<td align="right">
									<span id="show_nw_friend" style="display:none;"><a href="javascript: void(0);" onclick="show_div(\'hide_nw_friend\');show_div(\'nw_friends_content\');close_div(\'show_nw_friend\');" style="text-decoration:none;">show</a></span>
									<span id="hide_nw_friend" ><a href="javascript: void(0);" onclick="show_div(\'show_nw_friend\');close_div(\'nw_friends_content\');close_div(\'hide_nw_friend\');" style="text-decoration:none;">hide</a></span>
								</td>
							</tr>
							<tr><td colspan="2"><span id="nw_friends_content">
									<table width="100%">
										<tr><td colspan="2"><strong><small>Networks with the most friends</small></strong></td></tr>
												<tr><td colspan="2">'.$friends.'</td></tr>
												<tr><td colspan="2" align="right"><a href="'.$allFriendUrl.'" style="text-decoration:none;">Show all friends</a></td></tr>
										</table></span>
									</td>
								</tr>
							</table>';
		return $content;
	}//end networkFriends()
	
	###################################################################################################
	#Method			: showPhotos()
	#Type			: sub
	#Description	: show photos of given userid on user profile page
	##################################################################################################
	function showPhotos($profileId,$userId)
	{
		$totAlbum	= getTotRec("album_id","photo_album", "user_id='$profileId'");//get total albums for this profile id(user_id)
		if($totAlbum>0)
		{
			$siteVars		= $this->common->getSiteVars();
			$albumsOnProfile= $siteVars['albumsOnProfile'];//toptal topics should appear on events home page
			$query		= "	SELECT album_id,name,datestamp FROM photo_album WHERE user_id='$profileId' LIMIT 0,".$albumsOnProfile;
			$res		= mysql_query($query);
			$seeAll		= '<a href="'.base_url().'index.php/photos/myphotos" style="text-decoration:none;">See All</a>';
			//if($userId==$profileId)
			//$editUrl	= '<a href="'.base_url().'index.php/photos/myphotos/" style="text-decoration:none;">edit</a>';	//url to show all albums for this profile id
			while($rs=mysql_fetch_object($res))
			{
				$viewUrl		= base_url()."index.php/photos/album/".$rs->album_id;//url to view all photos of this album
				//get the album cover
				$photoRs		= getRow("photos","thumb_photo","album_id='$rs->album_id' AND album_cover='yes'");
				$createdDate	= date("F j Y",strtotime($rs->datestamp));
				if($photoRs['thumb_photo']!='')
					$album	= $photoRs['thumb_photo'];
				else
					$album	= base_url()."images/t_default.jpg";
				$albumContent	.= '<tr><td><table><tr>
										<td><a href="'.$viewUrl.'" style="text-decoration:none;"><img src="'.$album.'" border="0" width="50"></a></td>
										<td><small><a href="'.$viewUrl.'" style="text-decoration:none;">'.ucwords($rs->name).'</a><br><strong>Created</strong> '.$createdDate.'</small></td>
										</tr></table></td></tr>';
			}//end while
			$albumContent= '<table>'.$albumContent.'</table>';
			//prepare album(photo) content to show on profile page
			$content	= '<table width="100%">
								<tr><td><strong>Photos</strong></td>
								<td align="right">'.$editUrl.'</td>
								<td align="right">
									<span id="show_photo" style="display:none;"><a href="javascript: void(0);" onclick="show_div(\'hide_photo\');show_div(\'photos_content\');close_div(\'show_photo\');" style="text-decoration:none;">show</a></span>
									<span id="hide_photo"><a href="javascript: void(0);" onclick="show_div(\'show_photo\');close_div(\'photos_content\');close_div(\'hide_photo\');" style="text-decoration:none;">hide</a></span>
								</td></tr>
								<tr><td colspan="3">
										<span id="photos_content"><table width="100%">
											<tr><td>'.$totAlbum.' Album(s)</td><td align="right">'.$seeAll.'</td></tr>
											<tr><td colspan="2">'.$albumContent.'</td></tr></table></span>
										</td></tr>
								</table>';
		}
		else
			$content	= '';
		return $content;
	}//end showPhotos()
	
	###################################################################################################
	#Method			: showGroups()
	#Type			: sub
	#Description	: show groups of given userid on user profile page
	##################################################################################################
	function showGroups($profileId,$userId)
	{
		$totGroups	= getTotRec("group_id","groups", "user_id='$profileId'");//get total albums for this profile id(user_id)
		if($totGroups>0)
		{
			$query		= "	SELECT group_id,group_name FROM groups WHERE user_id='$profileId' ";
			$res		= mysql_query($query);
			//if($userId==$profileId)
			//$editUrl	= '<a href="'.base_url().'index.php/groups" style="text-decoration:none;">edit</a>';	//url to show all albums for this profile id
			$allUrl		= base_url()."index.php/groups/mygroups/".$profileId;
			while($rs=mysql_fetch_object($res))
			{
				$viewUrl		= base_url()."index.php/groups/view/".$rs->group_id;//url to view all photos of this album
	       //   $totMembers	=getTotRec("group_invitation_id","groups_invitation","group_id=".$rs->group_id);//tot members of this group
				$groupContent	.= '<a href="'.$viewUrl.'" style="text-decoration:none;"><small>'.ucwords($rs->group_name).'</small></a> - ';
			}//end while
			//$groupContent= '<table><tr><td>'.$groupContent.'</td></tr></table>';
			//prepare album(photo) content to show on profile page
			$content	= '<table width="100%">
							<tr>
								<td><strong>Groups</strong></td>
								<td align="right">'.$editUrl.'</td>
								<td align="right">
								<span id="show_group" style="display:none;"><a href="javascript: void(0);" onclick="show_div(\'hide_group\');show_div(\'groups_content\');close_div(\'show_group\');" style="text-decoration:none;">show</a></span>
								<span id="hide_group"><a href="javascript: void(0);" onclick="show_div(\'show_group\');close_div(\'groups_content\');close_div(\'hide_group\');" style="text-decoration:none;">hide</a></span>
								</td>
							</tr>
							<tr>
								<td colspan="3">
								<span id="groups_content">
								<table width="100%">
								<tr><td><a href="'.$allUrl.'" style="text-decoration:none;">'.$totGroups.' Group(s)</a></td><td align="right"><a href="'.$allUrl.'" style="text-decoration:none;">See All</a></td></tr>
								<tr><td colspan="2">'.$groupContent.'</td></tr>
								</table>
								</span>
								</td>
							</tr>
							</table>';
		}
		else
			$content	= '';
		return $content;
	}//end showGroups()
	
	###################################################################################################
	#Method			: showStatus()
	#Type			: sub
	#Description	: show the user status of the given profile id
	##################################################################################################
	function showStatus($profileId)
	{
		$rs	= getRow("users","screen_status_id,screen_status_changed_date","user_id='$profileId'");
		if($rs['screen_status_id']>0)
		{
			$rs			= getRow("screen_status","status","screen_status_id=".$rs['screen_status_id']);
			$status		= $rs['status'].' <a href="javascript: void(0);" style="text-decoration:none;" onclick="show_div(\'screen_status_change_div\');close_div(\'screen_status_div\');"><small>edit</small></a>';
		}
		else
			$status	= '<a href="javascript: void(0);" style="text-decoration:none;" onclick="show_div(\'screen_status_change_div\');close_div(\'screen_status_div\');">Update your status...</a>';
		return $status;
	}
	
	###################################################################################################
	#Method			: userStatusSelect()
	#Type			: sub
	#Description	: prepare user status select box
	##################################################################################################
	function userStatusSelect($profileId)
	{
		$query			= "SELECT screen_status_id,status FROM screen_status";
		$extra			= 'onchange="changeUserStatus(\''.$profileId.'\',\''.base_url().'\',\'store\')"';
		$screenSelect	= selectBoxQuery($query,'screenStatusSelect','',$extra);
		return $screenSelect;
	}

	###################################################################################################
	#Method			: userBasicInfo()
	#Type			: sub
	#Description	: prepare user basic information
	##################################################################################################
	function userBasicInfo($profileId)
	{
		$fieldList	= "sex,interested_in,relation_id,looking_for_id,birthday,birthday_visibility_id,hometown,state,country,political_id,religious_view";
		$rs			= getRow("basic_profile",$fieldList,"user_id='$profileId'");

		if($rs[sex]!='none' and $rs[sex]!='')//prepare sex
			$sex			= '<tr><td>Sex:</td><td>'.$rs[sex].'</td>';
		if($rs[interested_in]!='')//prepares interested in info
			$interestedIn	= '<tr><td>Interested in:</td><td>'.ucwords(trim($rs[interested_in],",")).'</td></tr>';
		if($this->getRelation($rs[relation_id])!='')
			$relation		= '<tr><td>Relation:</td><td>'.$this->getRelation($rs[relation_id]).'</td></tr>';
		if($this->getLookingFor($rs[looking_for_id])!='')
			$lookingFor		= '<tr><td valign="top">Looking for:</td><td valign="top">'.$this->getLookingFor($rs[looking_for_id]).'</td></tr>';
		if($rs[country]=='US' or $rs[country]=='CA')
			$homeTown	= $rs[hometown].' '.$rs[state];
		else
			$homeTown	= $rs[hometown].' '.$this->getCountry($rs[country]);
		if(trim($homeTown)!='')
			$hometown	= '<tr><td>Hometown:</td><td>'.$homeTown.'</td></tr>';

		if($this->getPoliticalView($rs[political_id])!='')
			$politicalView	= '<tr><td>Political Views:</td><td>'.$this->getPoliticalView($rs[political_id]).'</td></tr>';
		if($rs[religious_view]!='')//get religious view
			$religiousView	= '<tr><td>Religious View:</td><td>'.$rs[religious_view].'</td></tr>';
		$birthday	= $this->getBirthday($rs[birthday_visibility_id],$rs[birthday]);
		$content	= 	'<table width="100%">'.$sex.$interestedIn.$relation.$lookingFor.$hometown.$birthday.$state.$politicalView.$religiousView.'</table>';
		return $content;
	}
	
	###################################################################################################
	#Method			: getRelation()
	#Type			: sub
	#Description	: prepare relation name for the relation id
	##################################################################################################
	function getRelation($relationId)
	{
		$relation	= '';
		if($relationId>0)//prepares relation status
		{
			$relRs			= getRow("relation_status","relation","relation_id=".$relationId);
			$relation		= $relRs[relation];
		}
		return $relation;
	}
	
	###################################################################################################
	#Method			: getLookingFor()
	#Type			: sub
	#Description	: prepare looking for value for the looking for id
	##################################################################################################
	function getLookingFor($lookingForId)
	{
		$lookingFor	= '';
		if($lookingForId!='')//prepares looking for
		{
			$lookingId		= explode(",",trim($lookingForId,","));
			$lookingFor		= '';
			for($i=0;$i<count($lookingId);$i++)
			{
				$lookingRs		= getRow("looking_for","lookingfor","looking_id=".$lookingId[$i]);
				$lookingFor		.= $lookingRs[lookingfor].'<br>';
			}
		}
		return $lookingFor;
	}
	
	###################################################################################################
	#Method			: getPoliticalView()
	#Type			: sub
	#Description	: prepare political view value for the political id
	##################################################################################################
	function getPoliticalView($politicalId)
	{
		$polticalView	= '';
		if($politicalId>0)//prepares political views
		{
			$polRs			= getRow("political_views","political_view","political_id=".$politicalId);
			$politicalView	= $polRs[political_view];
		}
		return $polticalView;
	}
	
	###################################################################################################
	#Method			: getBirthday()
	#Type			: sub
	#Description	: prepare birthday to show profile page
	##################################################################################################
	function getBirthday($visible,$bday)
	{
		$bsplit	= explode("-",$bday);
		if($visible==1 and $bday!='')//show full birthday
			$birthday	= '<tr><td>Birthday</td><td>'.date("jS F Y",mktime(0,0,0,$bsplit[1],$bsplit[0],$bsplit[2])).'</td></tr>';//getting unix timestamp value
		elseif($visible==2 and $bday!='')//show day and month
			$birthday	= '<tr><td>Birthday</td><td>'.date("F d",mktime(0,0,0,$bsplit[1],$bsplit[0],$bsplit[2])).'</td></tr>';//getting unix timestamp value
		elseif($visible==3)//dont show birthda
			$birthday	= '';
		return $birthday;
	}
	
	###################################################################################################
	#Method			: getState()
	#Type			: sub
	#Description	: returns state name corresponding to the state symbol
	##################################################################################################
	function getState($stateSymbol)
	{
		$state	= '';
		if($stateSymbol!='')//prepares political views
		{
			$rs		= getRow("state_province","state_name","state_symbol='$stateSymbol'");
			$state	= $rs[state_name];
		}
		return $state;
	}
	
	
	###################################################################################################
	#Method			: getCountry()
	#Type			: sub
	#Description	: returns country name corresponding to the country symbol
	##################################################################################################
	function getCountry($countrySymbol)
	{
		$country	= '';
		if($countrySymbol!='')//prepares political views
		{
			$rs		= getRow("country","country_name","country_symbol='$countrySymbol'");
			$country	= $rs[country_name];
		}
		return $country;
	}
	
	###################################################################################################
	#Method			: eduInformation()
	#Type			: sub
	#Description	: returns users contact information to show on profile page for this profileId
	##################################################################################################
	function getEduInfo($profileId,$userId)
	{
		$fieldList		= "school_name,class_year,concentration1,concentration2,concentration3,class_year";
		$clgrs				= getRow("school_education",$fieldList,"user_id='$profileId'");
		$rs				= getRow("education_profile","high_school,datestamp","user_id='$profileId'");
		$usrRs			= getRow("users","email","user_id='$profileId'");
		$clgyear = substr($clgrs[class_year], -2);
		if($clgrs[school_name]!="")
			$eduInfo	.= '<tr><td>College:</td><td>'.$clgrs[school_name].' \''.$clgyear.'<br>'.$clgrs[concentration1].'<br>'.$clgrs[concentration2].'<br>'.$clgrs[concentration3].'</td></tr>';
		if($rs[high_school]!="")
			$eduInfo	.= '<tr><td>High School:</td><td>'.$rs[high_school].'</td></tr>';
		if($userId==$profileId)
		{
		$editUrl	= '<a href="'.base_url().'index.php/editprofile/education" style="text-decoration:none;">[edit]</a>';
		}

		if($eduInfo!="")
		{
			$title	= '<table width="100%">
						<tr>
							<td><strong>Education Info</strong></td>
							<td align="right">
								<span id="show_edu" style="display:none;"><a href="javascript: void(0);" onclick="show_div(\'hide_edu\');show_div(\'edu_content\');close_div(\'show_edu\');" style="text-decoration:none;">show</a></span>
								<span id="hide_edu"><a href="javascript: void(0);" onclick="show_div(\'show_edu\');close_div(\'edu_content\');close_div(\'hide_edu\');" style="text-decoration:none;">hide</a></span>
							</td>
						</tr>
						<tr><td colspan="2">
							<span id="edu_content"><table width="100%">'.$eduInfo.'</table></span>
						</td>
						</tr>
						</table>';
		}
		return $title;
	}//eduInformation()

	###################################################################################################
	#Method			: userInformation()
	#Type			: sub
	#Description	: returns users contact information to show on profile page for this profileId
	##################################################################################################
	function userInformation($profileId,$userId,$reqtype='')
	{
		if($reqtype=="msg")
		{
			$msgContactStatus=$this->checkMessageSetting($no=2,$profileId);
			if ($msgContactStatus=="yes")
			{
			 	$contactInfo	= $this->getContactInfo($profileId,$userId,$reqtype="msg");
			}
			$msgPersonalStatus=$this->checkMessageSetting($no=3,$profileId);
			if ($msgPersonalStatus=="yes")
			{
			 	$personalInfo	= $this->getPersonalInfo($profileId,$userId);
			}
			$msgContactStatus=$this->checkMessageSetting($no=4,$profileId);
			if ($msgContactStatus=="yes")
			{
			 	$contactInfo	= $this->getEduInfo($profileId,$userId);
			}
		}
		else
		{
			if($userId==$profileId)
			$editInfoUrl= '<a href="'.base_url().'index.php/editprofile" style="text-decoration:none;">edit</a>';
			$contactInfo	= $this->getContactInfo($profileId,$userId);
			$personalInfo	= $this->getPersonalInfo($profileId,$userId);
		}
		if(($contactInfo!="") or ($personalInfo!=""))
		{
			$content	= '<table width="100%">
							<tr>
								<td><strong>Information</strong></td>
								<td align="right">
									<span id="show_contact" style="display:none;"><a href="javascript: void(0);" onclick="show_div(\'hide_contact\');show_div(\'contact_content\');close_div(\'show_contact\');" style="text-decoration:none;">show</a></span>
									<span id="hide_contact"><a href="javascript: void(0);" onclick="show_div(\'show_contact\');close_div(\'contact_content\');close_div(\'hide_contact\');" style="text-decoration:none;">hide</a></span>
								</td>
							</tr>
							<tr><td colspan="2">
								<span id="contact_content"><table width="100%">'.$contactInfo.$personalInfo.'</table></span>
								</td>
							</tr>
							</table>';
			}
		return $content;
	}//userInformation()
	
	###################################################################################################
	#Method			: getContactInfo()
	#Type			: sub
	#Description	: get users contact information
	##################################################################################################
	function getContactInfo($profileId,$userId,$reqtype='')
	{
		$fieldList		= "mobile,mobile_privacy,land_line,land_line_privacy,address,city,state_province,country,zip_code,zip_code_privacy,website,website_privacy,email_privacy,screen_privacy";
		$rs				= getRow("contact_profile",$fieldList,"user_id='$profileId'");
		$usrRs			= getRow("users","email","user_id='$profileId'");
		//$title	= '<tr><td colspan="2"><strong>Contact Info'.$editUrl.'</strong></td></tr>';
		if($reqtype=="msg")
		{
			$contactInfo	.= '<tr><td>Email:</td><td>'.$usrRs[email].'</td></tr>';
			$contactInfo	.= $this->getAIM($profileId);
			if($rs[mobile]!='')
				$contactInfo	.= '<tr><td>Mobile:</td><td>'.$rs[mobile].'</td></tr>';
			$address	= trim($this->getAddress($rs[address],$rs[city],$rs[state_province],$rs[country],$rs[zip_code]));
			if($address!='')
				$contactInfo	.= '<tr><td valign="top">Current Address:</td><td>'.$address.'</td></tr>';
			if($rs[website]!='')
				$contactInfo	.= '<tr><td>Website:</td><td>'.$rs[website].'</td></tr>';
		}
		if($userId==$profileId)
		{
			$editUrl	= '<a href="'.base_url().'index.php/editprofile/contact" style="text-decoration:none;">[edit]</a>';
		}
		$check9=$this->checkProfileSetting($no=9,$profileId);
		if (($check9=="yes") || ($profileId==$userId))
		{
			$contactInfo	.= '<tr><td>Email:</td><td>'.$usrRs[email].'</td></tr>';
		}
		$check10=$this->checkProfileSetting($no=10,$profileId);
		if (($check10=="yes") || ($profileId==$userId))
		{
			$contactInfo	.= $this->getAIM($profileId);
		}
		$check5=$this->checkProfileSetting($no=5,$profileId);
		if (($check5=="yes") || ($profileId==$userId))
		{
			if($rs[mobile]!='')
				$contactInfo	.= '<tr><td>Mobile:</td><td>'.$rs[mobile].'</td></tr>';
		}
		$check6=$this->checkProfileSetting($no=6,$profileId);
		if (($check6=="yes") || ($profileId==$userId))
		{
			if($rs[mobile]!='')
				$contactInfo	.= '<tr><td>Mobile:</td><td>'.$rs[mobile].'</td></tr>';
		}
		$check7=$this->checkProfileSetting($no=7,$profileId);
		if (($check7=="yes") || ($profileId==$userId))
		{
			$address	= trim($this->getAddress($rs[address],$rs[city],$rs[state_province],$rs[country],$rs[zip_code]));
			if($address!='')
				$contactInfo	.= '<tr><td valign="top">Current Address:</td><td>'.$address.'</td></tr>';
		}
		$check8=$this->checkProfileSetting($no=8,$profileId);
		if (($check8=="yes") || ($profileId==$userId))
		{
			if($rs[website]!='')
				$contactInfo	.= '<tr><td>Website:</td><td>'.$rs[website].'</td></tr>';
		}
		if($contactInfo!="")
		{
			$title	= '<tr><td colspan="2"><strong>Contact Info'.$editUrl.'</strong></td></tr>';
		}
		return $title.$contactInfo;
	}//getContactInfo()
	
		###################################################################################################
	#Method			: getPersonalInfo()
	#Type			: sub
	#Description	: get users perosnal information
	##################################################################################################
	function getPersonalInfo($profileId,$userId)
	{
		$personalInfo	= '';
		if($userId==$profileId)
			$editUrl	= '<a href="'.base_url().'index.php/editprofile/personal" style="text-decoration:none;">[edit]</a>';
		$fieldList		= "activities,interests,favorite_music,favorite_tv_shows,favorite_movies,favorite_books,favorite_quotes,about_me";
		$rs				= getRow("member_personal",$fieldList,"user_id='$profileId'");

			if($rs[activities]!='')
				$personalInfo	= '<tr><td valign="top">Activities:</td><td>'.$rs[activities].'</td></tr>';
			if($rs[interests]!='')
				$personalInfo	.= '<tr><td valign="top">Interests:</td><td>'.$rs[interests].'</td></tr>';
			if($rs[favorite_music]!='')
				$personalInfo	.= '<tr><td valign="top">Favorite Music:</td><td>'.$rs[favorite_music].'</td></tr>';
			if($rs[favorite_tv_shows]!='')
				$personalInfo	.= '<tr><td valign="top">Favorite TV Shows:</td><td>'.$rs[favorite_tv_shows].'</td></tr>';
			if($rs[favorite_movies]!='')
				$personalInfo	.= '<tr><td valign="top">Favorite Movies:</td><td>'.$rs[favorite_movies].'</td></tr>';
			if($rs[favorite_books]!='')
				$personalInfo	.= '<tr><td valign="top">Favorite Books:</td><td>'.$rs[favorite_books].'</td></tr>';
			if($rs[favorite_quotes]!='')
				$personalInfo	.= '<tr><td valign="top">Favorite Quates:</td><td>'.$rs[favorite_quotes].'</td></tr>';
			if($rs[about_me]!='')
				$personalInfo	.= '<tr><td>About Me:</td><td>'.$rs[about_me].'</td></tr>';
			if($personalInfo!='')
				$personalInfo	= '<tr><td colspan="2"><strong>Personal Info'.$editUrl.'</strong></td></tr>'.$personalInfo;
			return $personalInfo;
	}//getPersonalInfo()
	
	###################################################################################################
	#Method			: getAddress()
	#Type			: sub
	#Description	: prepares address to show on profile page for this $address,$city,$state,$country,$zip
	##################################################################################################
	function getAddress($address,$city,$state,$country,$zip)
	{
		if($address!='')
			$contact	= $address.'<br>';
		if($city!='')
			$contact	.= $city.'<br>';
		if($state!='' and $state!='0')
		{
			$rs		= getRow("state_province","state_name","state_symbol='$state' AND country_symbol='$country'");
			$state	= $rs[state_name];
			$contact	.= $state.'<br>';
		}
		if($country!=''  and $country!='0')
		{
			$rs		= getRow("country","country_name","country_symbol='$country'");
			$country= $rs[country_name];
			$contact	.= $country.'<br>';
		}
		if($zip!='')
			$contact	.= $zip.'<br>';
		return $contact;
	}//getAddress()
	
	###################################################################################################
	#Method			: getAIM()
	#Type			: sub
	#Description	: get screen name and status for this profile
	##################################################################################################
	function getAIM($profileId)
	{
		$res	= mysql_query("SELECT im_id,screen_name FROM member_im_screen_name WHERE user_id='$profileId' ORDER BY im_id");
		if($res)
		while($rs=mysql_fetch_object($res))
		{
			$scRs		= getRow("member_online_im","im_name","im_id=".$rs->im_id);
			$content	.= '<tr><td>'.$scRs[im_name].':</td><td>'.$rs->screen_name.'</td></tr>';
		}
		return $content;
	}//getAIM()
	
	###################################################################################################
	#Method			: showWall()
	#Type			: sub
	#Description	: To show wall posts for the given user id
	##################################################################################################
	function showWall($profileId,$userId)
	{
		$siteVars		= $this->common->getSiteVars();
		$postsOnProfile	= $siteVars['albumsOnProfile'];//toptal topics should appear on events home page
		$query	= "SELECT wall_post,posted_by,datestamp FROM profile_wall WHERE posted_to='$profileId' ORDER BY datestamp DESC LIMIT 0,".$postsOnProfile;
		$res	= mysql_query($query);
		if($res)
		{
			$totRec	= mysql_num_rows($res);
			while($rs=mysql_fetch_object($res))
			{
				$avatar		= $this->common->getAvatar($rs->posted_by);
				$postTime	= date("h:i a",$rs->datestamp);
				$postedBy	= $this->common->getUsername($rs->posted_by);
				$profileUrl	= base_url().'index.php/profile/user/'.$rs->posted_by;
				$content	.= '<tr><td>
								<table><tr>
									<td valign="top"><a href="'.$profileUrl.'" style="text-decoration:none;"><img src="'.$avatar.'" border="0" width="50"></td>
									<td valign="top">'.$postedBy.' wrote<br>at'.$postTime.'<br>'.$rs->wall_post.'</td>
									</tr>
								</table></td></tr>';
			}//end while
		}
		$postInterface	= $this->postWall2Profile($userId,$profileId);
		$seeAllUrl		= base_url().'index.php/profile/allpost/'.$profileId;
		$content	= '<table width="100%">
						<tr>
							<td><strong>The wall</strong></td>
							<td align="right"></td>
							<td align="right">
								<span id="show_post" style="display:none;"><a href="javascript: void(0);" onclick="show_div(\'hide_post\');show_div(\'post_content_div\');close_div(\'show_post\');" style="text-decoration:none;">show</a></span>
								<span id="hide_post"><a href="javascript: void(0);" onclick="show_div(\'show_post\');close_div(\'post_content_div\');close_div(\'hide_post\');" style="text-decoration:none;">hide</a></span>
							</td>
							</tr>
							</table>
							<div id="post_content_div">
							<table width="100%">
							<tr><td><span class="grytxt" id="total_posts_on_profile">Displaying '.$totRec. ' posts</span></td>
								<td align="right"><a href="'.$seeAllUrl.'" class="BlueLink">See All</a></tr>
							<tr><td>

								'.$postInterface.'<table width="100%">'.$content.'</table>
								</td>
							</tr>
						</table></div>';
		return $content;
	}//showWall()
	
	###################################################################################################
	#Method			: allpost()
	#Type			: Main
	#Description	: To show all wall posts for the given user id
	##################################################################################################
	function allpost()
	{
		$this->loadSettings();
		$datas	= $this->common->authenticate();
		global $userId,$now;
		if(count($datas)<1)
		{
			$datas	= $this->common->basicvars();
			$data	= $datas;
			$data['siteUrl']		= site_url();
			$data['center_login']	= true;
			$this->load->view('login_view',$data);
		}
		else
		{
			$data		= array();
			$data		= $datas;
			$profileId	= $this->uri->segment(3);
			$query	= "SELECT wall_post,posted_by,datestamp FROM profile_wall WHERE posted_to='$profileId' ORDER BY datestamp DESC ";
			$res	= mysql_query($query);
			if($res)
			{
				$totRec	= mysql_num_rows($res);
				while($rs=mysql_fetch_object($res))
				{
					$avatar		= $this->common->getAvatar($rs->posted_by);
					$postTime	= date("h:i a",$rs->datestamp);
					$postedBy	= $this->common->getUsername($rs->posted_by);
					$profileUrl	= base_url().'index.php/profile/user/'.$rs->posted_by;
					$content	.= '<tr><td>
										<table><tr>
											<td valign="top"><a href="'.$profileUrl.'" style="text-decoration:none;"><img src="'.$avatar.'" border="0" width="50"></td>
											<td valign="top">'.$postedBy.' wrote<br>at'.$postTime.'<br>'.$rs->wall_post.'</td>
										</tr>
									</table></td></tr>';
				}//end while
			}
			$postInterface	= $this->postWall2Profile($userId,$profileId,'all');
			$content	= '<span id="post_content_div">
										<table width="100%">
										<tr>
											<td><span class="grySubTxt" id="total_posts_on_profile">Displaying '.$totRec.' posts</span></td>
										</tr>
										<tr><td>
											'.$postInterface.'<table width="100%">'.$content.'</table>

											</td>
										</tr>
										</table></span>';
			$userAvatar	= $this->common->getAvatar($profileId);
			if($profileId==$userId)
			{
				$backUrl	= '<a href="'.base_url().'index.php/profile/user/'.$profileId.'" class="BlueLink">Back to My Profile</a>';
				$header	= '<table><tr height="75"><td><img src="'.$userAvatar.'" border="0" width="50"></td><td><strong>My Wall</strong><br>'.$backUrl.'</td></tr></table>';
			}
			else
			{
				$userName	= $this->common->getUsername($profileId);
				$backUrl	= '<a href="'.base_url().'index.php/profile/user/'.$profileId.'" class="BlueLink">'.ucwords($userName).'\'s Profile</a>';
				$header	= '<table><tr height="75"><td><img src="'.$userAvatar.'" border="0" width="50"></td><td><strong>'.ucwords($userName).'\'s Wall</strong><br>'.$backUrl.'</td></tr></table>';
			}

			$data['all_post_content']	= $header.$content;
			$data['my_wall']			= true;
			$this->load->view('profile_view',$data);
		}
	}
	
	###################################################################################################
	#Method			: postWall2Profile()
	#Type			: sub
	#Description	: create an interface to post a wall for the given user
	##################################################################################################
	function postWall2Profile($postedBy,$postedTo,$type='')
	{
		$currentPage	= base_url();
		$ajaxLoader		= base_url().'application/images/indicator_arrows_black.gif';
		$content	= '<table width="100%">
						<tr><td align="center"><div id="wall_post_ajax_loader" style="display:none;"><img src="'.$ajaxLoader.'"></div</td></tr>
						<tr><td><div id="wall_post_result"></div></td></tr>
						<tr><td><textarea cols="35" rows="3" name="post_content" id="post_content" onblur="if(document.getElementById(\'post_content\').value==\'\') document.getElementById(\'post_content\').value=\'Write something...\';" onclick="document.getElementById(\'post_content\').value=\'\';">Write something...</textarea></td></tr>
						<tr><td><input type="button" name="post_wall" id="post_wall" value="Post" onclick="post2Profile(\''.$postedBy.'\',\''.$postedTo.'\',\''.$currentPage.'\',\''.$type.'\');"></td></tr></table>';
		return $content;
	}//getAddress()
	
	
	###################################################################################################
	#Method			: workInformation()
	#Type			: sub
	#Description	: returns users work(job) information to show on profile page for this profileId
	##################################################################################################
	function workInformation($profileId,$userId)
	{
		if($userId==$profileId)
			$editInfoUrl= '<a href="'.base_url().'index.php/editprofile" style="text-decoration:none;">edit</a>';
		$content	= '';
		$workInfo	= $this->getWorkInfo($profileId,$userId);
		if($workInfo!='')
		$content	= '<table width="100%">
								<tr>
									<td><strong>Work Information</strong></td>
									<td align="right">
										<span id="show_work" style="display:none;"><a href="javascript: void(0);" onclick="show_div(\'hide_work\');show_div(\'work_content\');close_div(\'show_work\');" style="text-decoration:none;">show</a></span>
										<span id="hide_work"><a href="javascript: void(0);" onclick="show_div(\'show_work\');close_div(\'work_content\');close_div(\'hide_work\');" style="text-decoration:none;">hide</a></span>
									</td>
								</tr>
								<tr><td colspan="2">
									<span id="work_content"><table width="100%">'.$workInfo.'</table></span>
									</td>
								</tr>
								</table>';
		return $content;
	}//workInformation()

	###################################################################################################
	#Method			: getWorkInfo()
	#Type			: sub
	#Description	: prepares workinformation
	##################################################################################################
	function getWorkInfo($profileId,$userId)
	{
		$content= '';
		$query	= 	"SELECT
						employer,position,description,city,state_province,country,current_job,start_month,start_year,end_month,end_year
					FROM work_profile
					WHERE user_id='$profileId'";
		$res	= mysql_query($query);
		$totRec	= mysql_num_rows($res);
		if($totRec>0)
		{
			if($userId==$profileId)
				$editUrl	= '<a href="'.base_url().'index.php/editprofile/work" style="text-decoration:none;">[edit]</a>';
			$content= '<tr><td colspan="2"><strong>Work Info'.$editUrl.'</strong></td></tr>';
			while($rs=mysql_fetch_object($res))
			{
				$content	.=	'<tr><td valign="top">Employer:</td><td>'.$rs->employer.'</td></tr>';
				if($rs->position!='')
					$content	.=	'<tr><td valign="top">Position:</td><td>'.$rs->position.'</td></tr>';
				if($rs->description!='')
					$content	.=	'<tr><td valign="top">Description:</td><td>'.$rs->description.'</td></tr>';
				if($rs->city!='')
					$city	= $rs->city.', ';
				$location	= $city.$rs->country;
				if($location!='')
					$content	.=	'<tr><td valign="top">Location:</td><td>'.$location.'</td></tr>';
				$startPeriod	= $rs->start_month.' '.$rs->start_year;
				$endPeriod		= $rs->end_month.' '.$rs->end_year;
				if(trim($startPeriod)!='' and trim($endPeriod)!='')
				{
					$period		= $startPeriod.'-'.$endPeriod;
					$content	.=	'<tr><td valign="top">Period:</td><td>'.$period.'</td></tr>';
				}
				if($content!='')
				$content	.= '<tr><td colspan="2"></td></tr>';

			}//end while
		}//end res
		return $content;
	}//workInformation()

	###################################################################################################
	#Method			: showMiniFeed()
	#Type			: sub
	#Description	: show minifeed of given userid on user profile page
	#Modified by	: Kiruthika
	#Modified Date	: 18-09-2007
	#Note			: done correction in Time displays in Mini feed
	##################################################################################################
	function showMiniFeed($profileId)
	{
		$query	= "SELECT mini_feed_log_id,log_content,datestamp FROM mini_feed_log WHERE user_id='$profileId' AND hide_feed='0' ORDER BY datestamp DESC";
		$res	= mysql_query($query);
		if($res)
		{
			$totalFeeds	= getTotRec("mini_feed_log_id","mini_feed_log", "user_id='$profileId' AND hide_feed='0'");//get total logs for this profile id(user_id)
			if($totalFeeds>0)
			{
				$headerTime	= '';
				$userName	= $this->common->getUsername($profileId);
				$logRs		= getRow("mini_feed_settings","mini_feed_status","mini_feed_id=12 AND user_id='$profileId'");
				while($rs=mysql_fetch_object($res))
				{
					$confirmDivId	= 'feed_confirm_div_'.$rs->mini_feed_log_id;
					$feedHideUrl	= base_url().'index.php/profile/hidefeed/'.$rs->mini_feed_log_id."/".$profileId;
					if($headerTime != date("F d Y",$rs->datestamp))
					{
						$headerTime		= date("F d Y",$rs->datestamp);
						$displayTime	= '<tr><td colspan="2" class="bodyLine"><span class="grytxt">'.$headerTime.'</span></td></tr>';
					}
					else
						$displayTime	= '';
					if($logRs[mini_feed_status]=='1' or $logRs[mini_feed_status]=='')
					{
						$logTime	= ' <span class="grytxt">'.date("g:i a",$rs->datestamp).'<span>';
					}
					else
						$logTime	='';
						$logContent=$rs->log_content;
						$feedContent	.=	$displayTime.'<tr><td>'.$userName.' <span>'.$logContent.'</span>'.$logTime.'</td><td align="right"><a href="javascript:void(0);" class="BlueLink" onclick="show_div(\''.$confirmDivId.'\');">X</a></td></tr><tr><td colspan="2"><div class="splborder2" id="'.$confirmDivId.'" style="display:none;background:#CCCCCC;">Do you want remove thie mini feed story?<br><input type="button" value="ok" onclick="window.location=\''.$feedHideUrl.'\'"><input type="button" value="cancel" onclick="close_div(\''.$confirmDivId.'\');"></div> </td></tr>';
				}//end while
				//prepare album(photo) content to show on profile page
				$content	= '<table width="100%" cellspacing="0" cellpadding="0">
												<tr ><td bgcolor="#e9e9e9" >&nbsp;&nbsp;<span class="blktxt"><strong>Mini-Feed</strong></span></td>
													<td align="right" bgcolor="#e9e9e9">
														<span id="show_feed" style="display:none;"><a href="javascript: void(0);" onclick="show_div(\'hide_feed\');show_div(\'feed_content\');close_div(\'show_feed\');" style="text-decoration:none;">show</a></span>
														<span id="hide_feed"><a href="javascript: void(0);" onclick="show_div(\'show_feed\');close_div(\'feed_content\');close_div(\'hide_feed\');" style="text-decoration:none;">hide</a></span>
													</td></tr>
												<tr><td colspan="2" width="100%">
													<span id="feed_content">
														<table width="100%">
															<tr><td><span class="blktxt">Displaying '.$totalFeeds.' stories</span></td><td align="right">'.$seeAll.'</td></tr>
															<tr>
																<td colspan="2" width="100%">
																<table width="100%">'.$feedContent.'</table>
																</td>
															</tr>
														</table>
													</span>
													</td>
												</tr>
											</table>';
			}
			else
				$content	= '';
		}//end if
		return $content;
	}//end showPhotos()

	###################################################################################################
	#Method			: hidefeed()
	#Type			: sub
	#Description	: show minifeed of given userid on user profile page
	##################################################################################################
	function hidefeed()
	{
		$feedId	= $this->uri->segment(3);
		$userId	= $this->uri->segment(4);
		$query	= "UPDATE mini_feed_log SET hide_feed='1' WHERE mini_feed_log_id='$feedId'";
		mysql_query($query);
		header("Location: ".base_url()."index.php/profile/user/".$userId);
	}
	
	###################################################################################################
	#Method			:loadSettings()
	#Type			:sub
	#Description	:load all common variables from config and assign to the global variables
	##################################################################################################
	function loadSettings()
	{
		global $userId,$now,$mplayer,$mencoder,$rootPath,$adminName,$adminEmail,$siteName,$siteTitle;
		$this->load->model('common');
		$userId		= $this->config->item('userId');
		$now		= $this->config->item('now');
		$mencoder	= $this->config->item('mencoder');
		$mplayer	= $this->config->item('mplayer');
		$rootPath	= $this->config->item('rootPath');

		$siteVars	= $this->common->getSiteVars();
		$adminName	= $siteVars['adminName'];
		$adminEmail	= $siteVars['adminEmail'];
		$siteName	= $siteVars['siteName'];
		$siteTitle	= $siteVars['siteTitle'];
	}
	
}
