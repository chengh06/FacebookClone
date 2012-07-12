<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

##################################################################
//File			: account.php
//Description	: edit user account module
//Author		: ilayaraja_22ag06;chh
//Created On	: 2012-6-25
//Last Modified	: 2012-6-25
##################################################################

class Account extends CI_Controller{

	public function __construct()
	{
		parent::__construct();
	}
	
	###################################################################################################
	#Method			: index()
	#Type			: Main
	#Description	: This is default method, to create/edit user account
	#Arguments		: nothing
	##################################################################################################
	
	function index()
	{
		$this->settings();
	}
	
	###################################################################################################
	#Method			: settings()
	#Type			: Main
	#Description	: This is default method, to create/edit user account
	#Arguments		: nothing
	##################################################################################################
	
	function settings()
	{
		$this->load_settings();
		$datas	= $this->common->authenticate();
		global $userId,$now;
		
		if(count($datas)<1)
		{
			$data['currentPage']	= base_url();
			$this->load->view('login_view',$data);
		}
		else
		{
			$data		= array();
			$data		= $datas;
			
			$usrRs		= getRow('users','email,password,username',"user_id='$userId'");
			$userEmail	= $usrRs['email'];
			
			$data['currentEmail']	= trim($userEmail);			
		
		
			if(isset($_POST['password_setting']))
			{
				$oldPassword	= trim($_POST['old_password']);
				$newPassword	= trim($_POST['new_password']);
				$confPassword	= trim($_POST['confirm_password']);
				
				$usrRs		= getRow('users','show_password',"user_id='$userId'");
				$usrPass	= $usrRs['show_password'];
				
				if($oldPassword !='' or $newPassword !='')
				{
					if($oldPassword !='' and $newPassword=='')
						$data['error']	= "You cannot use blank password !";
					elseif($oldPassword!=$usrPass)
						$data['error']	= "Your old password was incorrectly typed !";
					elseif(strlen($newPassword)<6)
						$data['error']	= "Password must be at least 6 characters long.";
					elseif($confPassword == '')
						$data['error']	= "You must enter the same password twice in order to confirm it.";
					elseif($confPassword != $newPassword)
						$data['error']	= "You must enter the same password twice in order to confirm it.";
					else
					{
						if(updateRecords('users',"show_password='$newPassword',password='".md5($newPassword)."'","user_id='$userId'"))
							$data['success']	= "Password successfully changed !";
						else
							$data['error']	= "Technical problem !";
					}				
				}			
			}
			elseif(isset($_POST['timezone_settings']))
			{
				$timezone	= $_POST['tz'];
				if($timezone !='')
				{
					if(updateRecords('users',"zone_id='$timezone'","user_id='$userId'"))
						$data['success']	= "Time Zone Successfully Changed.";
					else
						$data['error']	= "Technical problem !";
				}			
			}
			elseif(isset($_POST['security_settings']))
			{
				$securityQuestion	= $_POST['question'];
				$securityAnswer		= trim($_POST['answer']);
				
				if($securityQuestion == '')
					$data['error']	=	"Please select a security question.";
				elseif($securityAnswer == '')
					$data['error']	=	"Please enter a security answer.";
				else
				{
					if(updateRecords('users',"question_id='$securityQuestion', security_answer='$securityAnswer'","user_id='$userId'"))
						$data['success'] = "Security Question Successfully Changed.";
					else
						$data['error'] = "Technical problem !";
				}			
			}
			elseif(isset($_POST['username_settings']))
			{
				$oldUserName	= $usrRs['username'];
				$newUserName	= $_POST['new_name'];
				if($newUserName == '')
					$data['error']	=	"Please give the new name to change.";
				if($newUserName == $oldUserName)
					$data['error']	=	"Your old name new are same.";
				else
				{
					if(updateRecords('users',"username='$newUserName'","user_id='$userId'"))
					{
						$data['success']= "Your name has been changed successfully.";
						$dirName1	= './images/album/'.$oldUserName.$userId;
						$dirName2	= './images/album/'.str_replace(' ','_',$oldUserName).$userId;
						if(isFileExists($dirName1))
							$dirName	= $dirName1;
						else
							$dirName	= $dirName2;
						$newDirName	= './images/album/'.str_replace(' ','_',$newUserName).$userId;
						if(isFileExists($dirName))
						{
							rename($dirName,$newDirName);
							$query	= "SELECT large_photo, thumb_photo, photo_id FROM photos WHERE user_id='$userId'";
							$res	= $this->db->query($query);
							
							if($res->num_rows()>0)
							{
								foreach($res->result() as $rs)
								{
									$photoId	= $rs->photo_id;
									$largeImg	= explode("/",$rs->large_photo);
									$thumbImg	= explode("/",$rs->thumb_photo);
									$lphoto		= $largeImg[count($largeImg)-1];
									$tphoto		= $thumbImg[count($thumbImg)-1];
									$newLphoto	= base_url().$newDirName."/".$lphoto;
									$newTphoto	= base_url().$newDirName."/".$tphoto;
									updateRecords("photos","large_photo='$newLphoto',thumb_photo='$newTphoto'","photo_id='$photoId'");
								}
							}						
						}					
					}
					else
						$data['error']	=	"Technical problem !";
				}
			}
			
			//get user details
			$usrRs		= getRow('users','email,zone_id,question_id,username',"user_id='$userId'");
			$usrEmail	= $usrRs['email'];
			$timeZone	= $usrRs['zone_id'];
			$data['user_name']	= $usrRs['username'];
			
			if($timeZone!='')
				$data['currentTimeZone']	= $timeZone;
			$data['currentEmail']	= $usrEmail;
			$data['headerMessage']	= "My Account";
			
			$questQuery		= "SELECT * FROM security_question";
			if($usrRs['question_id']>0)
				$questionSelect	= $usrRs['question_id'];
			else $questionSelect = 0;
			$data['question']= selectBoxQuery($questQuery,'question',$questionSelect);
			
			$zoneRes	= mysql_query("SELECT * FROM timezone");
			$i=1;
			if($zoneRes)
			while($zoneRs= mysql_fetch_object($zoneRes))
			{
				$data['timezone'][$i]	= array('zoneId'=>$zoneRs->zone_id,'timeZone'=>$zoneRs->time_zone);
				$i++;
			}
			$data['userId']	= $userId;
			$this->load->view('account_view',$data);			
		}		
	}
	
	###################################################################################################
	#Method			: networks()
	#Type			: Main
	#Description	: show networks for the current user
	#Arguments		: nothing
	##################################################################################################
	function networks()
	{
		$this->load_settings();
		$datas	= $this->common->authenticate();
		global $userId,$now,$adminName,$adminEmail,$siteTitle;
		if(count($datas)<1)
		{
			$data['currentPage']	= base_url();
			$this->load->view('login_view',$data);
		}
		else
		{
			$data		= array();
			$data		= $datas;
			$data['account_networks']	 = true;
			
			if(isset($_POST['save_network']))
			{
				$allowed		= false;
				$joinEmail		= $_POST['network_email'];
				$networkType	= $_POST['network_type'];
				$query			= "SELECT network_name, network_email, network_id FROM networks
									WHERE network_type='$networkType' AND network_status='enabled'";
				$res	= mysql_query($query);
				while($rs=mysql_fetch_object($res))
				{
					$networkEmail	= $rs->network_email;
					$netSplit		= explode("@",$networkEmail);
					$joinSplit		= explode("@",$joinEmail);
					if($joinSplit[1]==$netSplit[1])
					{
						$allowed	= true;
						$networkId	= $rs->network_id;
						$networkName= $rs->network_name;
						break;
					}					
				}
				if($allowed)
				{
					$confirmKey		= rand(1,999999);
					$fields		= "network_id='$networkId',user_id='$userId',user_email='$joinEmail',network_type='$networkType',datestamp='$now',confirm_key='$confirmKey'";
					if(IsRecordExist4Add('network_users','network_user_id',"user_id='$userId' AND network_id='$networkId'"))
						$data['error']	= 'You already joined '.$networkType.' group.';
					else
					{
						$networkConfirmUrl	= base_url().'index.php/networks/confirm/'.$confirmKey.'/'.$userId;
						$this->load->library('email');
						$config['mailtype'] = 'html';
						$this->email->initialize($config);
						$this->email->from($adminEmail, $adminName);
						$this->email->to($joinEmail);
						$mailSubject	= 'Confirmation of joining network.';
						$mailContent	= 'You have joined '.$networkName.' successfully.<br><br>
											To continue your access please use the following url:<br>'.
											$networkConfirmUrl.'<br><br>
											Thanks,<br>
											The '.$siteTitle.' Team';
						$this->email->subject($mailSubject);
						$this->email->message($mailContent);
						
						if($this->email->send())
						{
							if(insertRecord('network_users',$fields))
								$data['success']	= 'We have sent a confirmation mail to '.$joinEmail;
							else
								$data['error']	= 'Technical problem.';
						}
						else
							$data['error']	= 'Error in sending confirmation mail.';
					}					
				}
				else
					$data['error']	= 'Sorry!Your email doesnt match with this network.';
			}
			$data['network_content']	= $this->common->listNetworks($userId);
			$this->load->view('account_view',$data);
		}		
	}
	
	function notifications()
	{
		$this->load_settings();
		$datas	= $this->common->authenticate();
		global $userId,$now;
		
		if(count($datas)<1)
		{
			$data['currentPage']	= base_url();
			$this->load->view('login_view',$data);
		}
		else
		{
			$data		= array();
			$data		= $datas;
			//if($_POST['notify_settings_submit'])
				$resultMsg	= $this->_storeNotifySettings($userId);
			$data['notification_content']	= $this->_showNotifySettings($userId);
			$data['show_result']			= true;
			$data['result_msg']				= $resultMsg;
			$data['account_notifications']	= true;
			$this->load->view('account_view',$data);
		}		
	}
	
	###################################################################################################
	#Method			: _storeNotifySettings()
	#Type			: Sub
	#Description	: store notification settings to table
	#Arguments		: nothing
	##################################################################################################
	function _storeNotifySettings($userId)
	{
		$query	= "SELECT notification_id, notification_name FROM notifications";
		$res	= mysql_query($query);
		if($res)
		while($rs=mysql_fetch_object($res))
		{
			$status		= 0;
			//$checkBoxId = "notify_check_".$rs->notification_id;
			//if($_POST[$checkBoxId])
				//$status	= 1;
			$isExist	= getTotRec("id","users_status","notification_id=".$rs->notification_id." AND user_id=".$userId);
			if($isExist)
			{
				$up_qry="UPDATE users_status SET notification_status='$status' WHERE user_id='$userId' AND notification_id=".$rs->notification_id;
				if(mysql_query($up_qry))
					$resultMsg	= "Your changes have been saved.!";
				else
					$resultMsg	= "Sorry! Problem in saving changes<br> ".mysql_error();
			}
			else
			{
				$ins_qry="INSERT INTO users_status SET user_id='$userId', notification_id=".$rs->notification_id.", notification_status='$status'";
				if(mysql_query($ins_qry))
					$resultMsg	= "Your changes have been saved.!"; 
				else
					$resultMsg	= "Sorry! Problem in saving changes<br> ".mysql_error();
			}			
		}
		return $resultMsg;
		
	}
	
	
	###################################################################################################
	#Method			: _showNotifySettings()
	#Type			: Sub
	#Description	: show list notify settings available
	#Arguments		: nothing
	##################################################################################################
	function _showNotifySettings($userId)
	{
		global $userId,$now,$mplayer,$mencoder,$rootPath,$adminName,$adminEmail,$siteName,$siteTitle;
		$usrRs			= getRow("users","email","user_id='$userId'");
		$emailContent	=	'<p>Email me at <strong>'.$usrRs['email'].'</strong> when someone...</p>';
		$query	= "SELECT notification_id, notification_name FROM notifications";
		$res	= mysql_query($query);
		if($res)
		$content = '';
		while($rs=mysql_fetch_object($res))
		{
			$chechBoxId	= "notify_check_".$rs->notification_id;
			$feedRs		= getRow("users_status","notification_status","notification_id=".$rs->notification_id." AND user_id='$userId'");
			$isChecked	= getTotRec("id","users_status","notification_id=".$rs->notification_id." AND user_id='$userId'");
			if($isChecked)
			{
				if($feedRs['notification_status'])
					$checked	= 'checked';
				else 
					$checked	= '';
			}
			else
				$checked = 'checked';
			$content .= '<input type="checkbox" id="'.$chechBoxId.'" name="'.$chechBoxId.'" '.$checked.'>'.$rs->notification_name.'<br>';
		}
		$cancelUrl	= base_url().'index.php/account/notifications';
		$content	= '<form name="notify_settings_form" method="post" >
						<table>
							<tr height="50"><td><span class="blktitle">Email Notifications</span></td></tr>
							<tr><td><p>'.$siteTitle.'  notifies you by email whenever actions are taken on '.$siteTitle.' that  involve you. You can control which email notifications you receive.</p></td></tr>
							<tr><td>'.$emailContent.'</td></tr>
							<tr><td>'.$content.'</td></tr>
							<tr height="50"><td>
						<input type="hidden" id="notify_settings_submit" name="notify_settings_submit" value="1">
						<input type="submit" value="Save Changes">
						<input type="button" onclick="window.location=\''.$cancelUrl.'\'" value="Cancel">
						</td></tr></table>
						</form>';
		return $content;
	}//end showFeedSettings()
	
	
	
	###################################################################################################
	#Method			: load_settings()
	#Type			: sub
	#Description	: load all common variables from config and assign to the global variables
	##################################################################################################
	function load_settings()
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