<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
##################################################################
//File			: login.php
//Description	: user login module
//Author		: chh
//Created On	: 2012-5-16
//Last Modified	: 2012-5-16
##################################################################
Class Login extends CI_Controller
{
	public function __construct()
	{
		parent ::__construct();
	}
	function index()
	{
		$this->load->model("common");
		$this->load->helper("common");
		$data = array();
		$now = date('Y-m-d h:i:s');
		$datas	= $this->common->basicvars();
		$data	= $datas;    //为什么都要先将数据传送给datas,然后再传给data?
		$error = '';
		if(isset($_POST['login_submit']))//check login form submitted
		{
			$email		= trim($_POST['email']);
			$password 	= md5(trim($_POST['pass']));
			if($email == '')
				$error	= "Login Failed : Please give the email !";
			elseif($password == '')
				$error	= "Login Failed : Please give the password !";
			else
			{
				$condition = "email = '$email' AND password='$password' AND user_status='active'";//creat condition for record exit query
				$fieldName = "user_id";//filed name to be given while processing record exit function
			
				if(IsRecordExist4Add('users',$fieldName,$condition))
				{
					$fieldList			= ' user_id ';
					$user_rs			= getRow('users', $fieldList, $condition);
					updateRecords('users',"last_login='$now'","user_id='$user_rs[user_id]'");
					$_SESSION['user_id']= $user_rs['user_id'];
					if(isset($_SESSION['loginUrl']))
					{
						$loginUrl	= $_SESSION['loginUrl'];
						header("Location: ".$loginUrl);
					}
					else
						header("Location:".base_url()."index.php/home");
				}
				elseif(getTotRec("user_id","users","email = '$email' AND password='$password' AND user_status='pending'"))
					$error	= "Login Failed : Please activate your account!";
				else
					$error	= "Login Failed : Invalid email/password !";
			}
		}
		if($error!='')
		{
			$data['error']=	'<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="splborder">
								<tr>
									<td height="41" bordercolor="#ec8a00" bgcolor="#feffcf"><span class="blktitle">'.$error.'</span><br />
									</td>
								</tr>
							</table>';
		}
		$data['siteUrl']		= site_url();
		$data['center_login']	= true;
		$this->load->view('login_view',$data);
	}	
}
?>