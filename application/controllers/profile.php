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
				$this->smartyextended->view('profile',$data);
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
