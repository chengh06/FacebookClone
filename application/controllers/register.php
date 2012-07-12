<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

Class Register extends CI_Controller
{
	public function __construct()
	{
		parent :: __construct();
	}
	function index()
	{
		$this->load->model('common');
		$this->load->helper('common');
		$this->load->helper('captcha');  //ÑéÖ¤Âë°ïÖúº¯Êý
		$data = array();
		$datas	= $this->common->basicvars();
		$data	= $datas;
		$now = date('Y:m:d h:i:s');
		$siteVars	= $this->common->getSiteVars();
		$adminName	= $siteVars['adminName'];
		$adminEmail	= $siteVars['adminEmail'];
		$siteName	= $siteVars['siteName'];
		$siteTitle	= $siteVars['siteTitle'];
		$data['siteTitle']			= $siteTitle;
		//$data['loggedIn']           = false;
		$randWord	= RandomName(rand(4,5));
		$vals = array(
					'word'		 => $randWord,
					'img_path'	 => BASEPATH . '../images/captcha/',
					'img_url'	 => base_url() . 'images/captcha/',
					'expiration' => 7200,
					'font_path'	 => BASEPATH . '../images/fonts/timesbd.ttf'
				);
		$cap = create_captcha($vals);
		$data['captImage']	= $cap['image'];
		$data['randWord']	= $randWord;
		$data['birthDay']	= selectBox('birthday_day', createDay());//create birthday day select box
		$data['birthMonth']	= selectBox('birthday_month', createMon());//create birthday month select box
		$data['birthYear']	= selectBox('birthday_year', array_reverse(createYear(),true));//create birthday year select box
		$data['siteUrl']	= site_url();//store site url
		$query				= "SELECT lifestage_id,content FROM lifestage";
		$count				= getTotRec("lifestage_id","lifestage");
		$data['lifestage']	= selectBoxQuery($query, 'lifestage', '','size="'.$count.'" class="select" onchange="lifestage_choosen();"');
		$query					= "SELECT school_status_id,status FROM school_status";
		$count					= getTotRec("school_status_id","school_status");
		$data['schoolStatus']	= selectBoxQuery($query, 'schoolStatus', '','size="'.$count.'" class="select" onchange="schoolstatus_choosen();"');
		$data['currentPage']	= base_url();
		$this->load->view('register_view',$data);		
	}
	function confirm()
	{
		$now		= date('Y-m-d h:i:s');//crrent date stamp
		$data		= array();//initialize array
		$this->load->helper('common');
		$actvKey	= $this->uri->segment(3);
		$isValid	= getTotRec("user_id","users","activation_key='$actvKey'");
		if($isValid<1)
		{
			$data['invalid_page']	= true;
			$this->load->view('register_view',$data);
		}
		else
		{
			$rs			= getRow("users","user_id,email,password","activation_key='$actvKey'");
			$email		= $rs['email'];
			$password	= $rs['password'];
			$userId		= $rs['user_id'];
			updateRecords("users","user_status='active',last_login='$now'","activation_key='$actvKey'");
			$_SESSION['user_id']	= $userId;
			if(isset($_SESSION['loginUrl']))
				{
					$loginUrl	= $_SESSION['loginUrl'];
					header("Location: ".$loginUrl);
				}
			else
				header("Location: ".base_url()."index.php/home");
		}
	}
	
}