<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
/*
Myfacebook的首页
导引到登陆页面和注册页面
同时可以在首页中直接登陆
*/

Class Home extends CI_Controller{
	
	public function __construct()
	{
		parent:: __construct();		
	}
	
	function index()
	{
		global $userId,$now,$rootPath;
		$data	= array();
		$this->loadSettings();   //loadSettings函数在本文件中
		$datas	= $this->common->authenticate();  //判断用户权限
		
		if(count($datas)>0)
		{
			$data = $datas;
			$friendsArray		= $this->common->getFriends($userId); //从数据库中得到friends的信息
			$data['friends']	= $this->homeFriends($friendsArray);
			$data['friends_request']	= $this->common->homeFriendsRequest($userId);
		}
		else
		{
			$datas	= $this->common->basicvars();
			$data	= $datas;
		}
		$this->load->view('home_view',$data);
		
	}
	
	###################################################################################################
	#Method			: homeFriends()
	#Type			: sub
	#Description	: prepares friends list to show one home page
	##################################################################################################
	function homeFriends($friendsArray)
	{
		$currentPage	= base_url();
		$mainContent	= '';
		if(!empty($friendsArray))
		{
			$siteVars = $this->common->getSiteVars();
			$listOnHome	= $siteVars['listOnHome'];
			
			$friendTitle	= '<tr>
									<td bgcolor="#e9e9e9">&nbsp;&nbsp;<span class="blktxt"><strong>Friends</strong></span></td>
								</tr>';
			for($i=0;$i<count($friendsArray) && $i<$listOnHome;$i++)
			{
				$friendPhoto= $this->common->getAvatar($friendsArray[$i]); //得到头像 从数据库中
				$friendName	= $this->common->getUsername($friendsArray[$i]);  //得到username
				$viewProfile= base_url().'index.php/profile/user/'.$friendsArray[$i];  //连接
				$viewFriends= base_url().'index.php/friends/view/'.$friendsArray[$i];
				$content	.= '<tr>
									<td width="37%" height="65"><div align="left"><a href="'.$viewProfile.'" class="BlueLink"><img src="'.$friendPhoto.'" width="50" border="0" /></a></div></td>
									<td width="63%" class="BluSubtxt"><strong><a href="'.$viewProfile.'" class="BlueLink">'.ucwords($friendName).'</a></strong><br />
									<a href="'.$viewFriends.'" class="BlueLink">View Friends</a></td>
								</tr>';
			}
			$content	=	'<tr>
								<td>
									<table width="100%" border="0" cellspacing="0" cellpadding="0">'.$content.'
									</table>
								</td>
							</tr>';
			$mainContent	= '<table width="100%" border="0" cellspacing="4" cellpadding="0">'.$friendTitle.$content.'</table>';
		}
		return $mainContent;
	}
	
	
	
	
	
	
	###################################################################################################
	#Method			: load_settings()
	#Type			: sub
	#Description	: load all common variables from config and assign to the global variables
	##################################################################################################	
	function loadSettings()
	{
		global $userId,$now,$mplayer,$mencoder,$rootPath,$adminName,$adminEmail,$siteName,$siteTitle;
		$this->load->model('common');
		$userId		= $this->config->item('userId');    //在autoload中已经导入了setting的config，所以这里可以直接加载item
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

