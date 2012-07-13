<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
##################################################################
//File			: editprofile.php
//Description	: user profile mangement
//Author		: ilayaraja_22ag06;chh
//Created On	: 12-Apr-2007
//Last change	: 2012-7-10
##################################################################
Class Editprofile extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	function index()
	{
		$this->basic();
	}
	
	###################################################################################################
	#Method			: basic()
	#Type			: Main
	#Description	: Create/Edit user profile
	#Arguments		: nothing
	##################################################################################################
	function basic()
	{
		global $userId,$now;
		$this->load_settings();
		$datas	= $this->common->authenticate();
		$userId = $datas['userId'];
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

			//get basic profile info for the current user
			$basicRs	= getRow('basic_profile','*', "user_id='$userId'");
			//get sex
			$sex		= $basicRs['sex'];
			$female='';
			$male='';
			$sexNone='';
			$menCheck='';
			$womenCheck='';
			if($sex=='female')
				$female	= "selected";
			elseif($sex=='male')
				$male	= "selected";
			else
				$sexNone= "selected";
			//get interested in
			$meetingList= explode(",",$basicRs['interested_in']);
			if(in_array('men',$meetingList))
				$menCheck	= 'checked';
			if(in_array('women',$meetingList))
				$womenCheck	= 'checked';
			//get relation	
			$relation	= $basicRs['relation_id'];
			$lookingList= explode(',',trim($basicRs['looking_for_id'],","));
			//prepare birth day
			$birthday	= $basicRs['birthday'];
			if($birthday !='')
			{
				$bdList		= explode("-", $birthday);
				$bd			= $bdList[0];
				$bm			= $bdList[1];
				$by			= $bdList[2];
			}
			else
			{
				$bd			= '-1';
				$bm			= '-1';
				$by			= '-1';
			}
			$bdVisible	= $basicRs['birthday_visibility_id'];
			$hometown	= $basicRs['hometown'];
			$country	= $basicRs['state'];
			$politics	= $basicRs['political_id'];
			$religion	= $basicRs['religious_view'];
			//end basic profile nformation
				
			$data['birthDay']	= selectBox('birthday_day', createDay(),$bd);//create birthday day select box
			$data['birthMonth']	= selectBox('birthday_month', createMon(),$bm);//create birthday month select box
			$data['birthYear']	= selectBox('birthday_year', array_reverse(createYear(),true),$by);//create birthday year select box
			$data['siteUrl']	= site_url();//store site url
			$data['userId']		= $userId;
			
			
			//create relationship status select box
			$res				= mysql_query("SELECT relation_id, relation FROM relation_status");
			$relationshipStatus	=array();
			$relationshipStatus[0]	= "Select Status:";
			if($res)
			while($rs=mysql_fetch_object($res))
				$relationshipStatus[$rs->relation_id]	= $rs->relation;
			$data['relationshipStatus']	= selectBox('relationship', $relationshipStatus,$relation,'class="select"');

			if($basicRs['country']!='')
				$selectCountry	= $basicRs['country'];
			else
				$selectCountry	= '-1';
			if($selectCountry=='US')
			{
				$data['basic_profile_state_us_select']	= selectBox('basic_profile_state_us_select',createState('US'),$basicRs['state']);
				$data['basic_country']	= 'US';
			}
			elseif($selectCountry=='CA')
			{
				$data['basic_profile_state_ca_select']	= selectBox('basic_profile_state_ca_select',createState('CA'),$basicRs['state']);
				$data['basic_country']	= 'CA';
			}
				
			else
			{
				$data['basic_profile_state_us_select']	= selectBox('basic_profile_state_us_select',createState('US'));
				$data['basic_profile_state_ca_select']	= selectBox('basic_profile_state_ca_select',createState('CA'));
				$data['basic_country']	= '-1';
			}
				
			$countryArr	= createCountry();//create country array
			$data['basic_profile_country_select']	= selectBox('basic_profile_country_select',$countryArr,$selectCountry,'onchange="selectState(\'basic_profile\');"');
			//create birthday visibility select box
			$res				= mysql_query("SELECT birthday_visibility_id, birthday_option FROM birthday_visibility");
			$birthdayVisibility	=array();
			if($res)
			while($rs=mysql_fetch_object($res))
				$birthdayVisibility[$rs->birthday_visibility_id]	= $rs->birthday_option;
			$data['birthdayVisibility']	= selectBox('birthday_visibility', $birthdayVisibility,$bdVisible,'class="select"');
				
			//create political view select box
			$res				= mysql_query("SELECT political_id,political_view FROM political_views");
			$politicalView		= array();
			$politicalView[0]	= "Select Political Views:";
			if($res)
			while($rs=mysql_fetch_object($res))
				$politicalView[$rs->political_id]	= $rs->political_view;
			$data['politicalView']	= selectBox('political_view', $politicalView, $politics, 'class="select"');
					
					//create looking for check box
			$res		= mysql_query("SELECT looking_id, lookingfor FROM looking_for");
			$lookingFor	= "<tr>";
			$loop		= 1;
			if($res)
			while($rs=mysql_fetch_object($res))
			{
				//echo $rs->lookingfor;
				if(in_array($rs->looking_id,$lookingList))
					 $lookCheck	="checked";
				else
					$lookCheck	='';
				$lookingFor	.= "	<td><input name='meeting_for".$loop."' id='meeting_for".$loop."' type='checkbox' value='".$rs->looking_id."' ".$lookCheck."></td>
									<td><label for='meeting_for".$loop."' >".$rs->lookingfor."</label></td>";
				if($loop%2==0)
					$lookingFor .="</tr><tr>";
				$loop++;
			}
			$lookingFor 		.="</tr>";
			$data['lookingFor']	= $lookingFor;
			$data['totMeets']	= $loop-1;
					
			$data['female']			= $female;
			$data['male']			= $male;
			$data['sexNone']		= $sexNone;
			$data['menCheck']		= $menCheck;
			$data['womenCheck']		= $womenCheck;
			$data['hometown']		= $hometown;
			$data['religion']		= $religion;
			$this->load->view('basicProfile_view',$data);
		}
	}//end basic method
	
	###################################################################################################
	#Method			: picture()
	#Type			: Main
	#Description	: Create/Edit user picture profile
	#Arguments		: nothing
	##################################################################################################
	function picture()
	{
		$this->load_settings();
		$datas	= $this->common->authenticate();
		global $userId,$now,$rootPath;
		
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
			//remove user photo both from database as well as the exact directory
			if($this->uri->segment(3)=='remove')
			{
				$picRs		= getRow('picture_profile', 'picture_path', "user_id='$userId'");
				$dirName	= './images/pictures/'.$userId;
				if($picRs['picture_path']=='')
					$data['error']	= 'You have no image to remove.';
				else
				{
					if(mysql_query("DELETE FROM picture_profile WHERE user_id='$userId'"))
					{
						removeFiles($dirName);
						$data['success']	= 'Successfully removed !';
					}
					else
						$data['error']	='Error while removing image !';
				}
			}//end image remove process
			elseif($this->uri->segment(3)=='upload')
			{
				if(!$_POST['agree'])
					$data['error']	= 'You must agree the terms and conditions.';
				else
				{
					$dirName	= './images/pictures/'.$userId;
					//$dirName1	= $rootPath.'application/images/pictures/'.$userId;
					if(isFileExists($dirName))//if directory exist for this user, remove image
						recursive_remove_directory($dirName,true);
					else
						createDir($dirName);//else create new directory for this user
									
					//upload image settings
					$config['upload_path'] 		= $dirName."/";
					$config['allowed_types'] 	= 'gif|jpg|png';
					$config['max_size']			= '4096';//in KB, 4 MB
					//$config['max_width']		= '1024';// in pixels
					//$config['max_height']		= '768';//  in pixels
					//load upload library with new settings
					$this->load->library('upload', $config);
								
					if ( ! $this->upload->do_upload())//if file upload failed
						$data['error']	= $this->upload->display_errors();
					else// if file uplaod succeed
					{
						$data['success']	= "Image has been successfully uploaded.";
						$uploadedData		= $this->upload->data();
						$baseUrl			= base_url();
						$picturePath		= $dirName."/".$uploadedData['file_name'];
										
						//Thumb creation
						$thumbConfig['image_library']		= 'GD2';
						$thumbConfig['source_image'] 		= $picturePath;
						$thumbConfig['create_thumb'] 		= TRUE;
						$thumbConfig['maintain_ratio'] 		= TRUE;
						$thumbConfig['width'] 				= 200;
						$thumbConfig['height'] 				= 200;
						$this->load->library('image_lib',$thumbConfig);
						if(! $this->image_lib->resize())
							echo $this->image_lib->display_errors();
						else
							$thumbPath		= base_url().$dirName."/".$uploadedData['raw_name']."_thumb".$uploadedData['file_ext'];
										 
										//large photo creation
									/*	$imgConfig['image_library']		= 'GD2';
										$imgConfig['source_image'] 		= $picturePath;
										$imgConfig['create_thumb'] 		= FALSE;
										$imgConfig['maintain_ratio'] 	= TRUE;
										$imgConfig['width'] 			= 200;
										$imgConfig['height'] 			= 200;
										//$imgConfig['new_image'] 		= $dirName.'/'.$uploadedData['raw_name']."_large".$uploadedData['file_ext'];
										$this->load->library('image_lib',$imgConfig);
										if(! $this->image_lib->resize())
											echo $this->image_lib->display_errors();
										else
											$largePath		= base_url().$dirName."/".$uploadedData['raw_name']."_large".$uploadedData['file_ext'];*/
									
						if(IsRecordExist4Add('picture_profile','picture_id',"user_id='$userId'"))
							updateRecords('picture_profile', "picture_path='$thumbPath', thumb_path='$thumbPath', datestamp='$now'","user_id='$userId'");
						else
							insertRecord('picture_profile', "user_id='$userId', picture_path='$thumbPath', thumb_path='$thumbPath', datestamp='$now'");
					}
				}
			}//end upload
					//get user photo
			$picRs					= getRow('picture_profile', 'picture_path', "user_id='$userId'");
			$data['picturePath']	= $picRs['picture_path'];	
			$data['currentPage']	= base_url();
			$this->load->view('pictureProfile_view',$data);
		}
	}//end picture()
	
	###################################################################################################
	#Method			: personal()
	#Type			: Main
	#Description	: Create/Edit user personal profile
	#Arguments		: nothing
	##################################################################################################
	function personal()
	{
		$this->load_settings();
		$datas	= $this->common->authenticate();
		global $userId,$now;
			
		if(count($datas)<1)//if authentication failed
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
			if($_POST['submit_personal'])
			{
				if($this->storePersonal())
					$data['success']	= "Personal information successfully stored.";
				else
				{
					$data['clubs']		= $_POST['clubs'];
					$data['interests']	= $_POST['interests'];
					$data['music']		= $_POST['music'];
					$data['tv']			= $_POST['tv'];
					$data['movies']		= $_POST['movies'];
					$data['books']		= $_POST['books'];
					$data['quote']		= $_POST['quote'];
					$data['about_me']	= $_POST['about_me'];
					$data['error']		= "Personal information has not been stored.";
				}//end else
			}//end submit
			$dat	= array_merge($data,$this->getPersonal());
			$this->load->view('personalProfile_view',$dat);
		}
	}
	
	###################################################################################################
	#Method			: storePersonal()
	#Type			: sub
	#Description	: store personal information to database
	##################################################################################################
	function storePersonal()
	{
		$this->load_settings();
		global $userId,$now;
			
		if(trim($_POST['clubs'])!='')
			$fields	= "activities='$_POST[clubs]'";
		if(trim($_POST['interests'])!='')
			$fields	.= ",interests='$_POST[interests]'";
		if(trim($_POST['music'])!='')
			$fields	.= ",favorite_music='$_POST[music]'";
		if(trim($_POST['tv'])!='')
			$fields	.= ",favorite_tv_shows='$_POST[tv]'";
		if(trim($_POST['movies'])!='')
			$fields	.= ",favorite_movies='$_POST[movies]'";
		if(trim($_POST['books'])!='')
			$fields	.= ",favorite_books='$_POST[books]'";
		if(trim($_POST['quote'])!='')
			$fields	.= ",favorite_quotes='$_POST[quote]'";
		if(trim($_POST['about_me'])!='')
			$fields	.= ",about_me='$_POST[about_me]'";
		
		if(IsRecordExist4Add('member_personal','personal_id',"user_id='$userId'"))
			$result	= updateRecords('member_personal',$fields,"user_id='$userId'");
		else
		{
			if($fields!='')
				$fields .=",user_id='$userId',datestamp='$now'";
			else
				$fields ="user_id='$userId',datestamp='$now'";
			$result	= insertRecord('member_personal',$fields);
		}
		return $result;
	}//end storePersonal()
		
	###################################################################################################
	#Method			: getPersonal()
	#Type			: sub
	#Description	: get all personal information to show on personal profile page
	#Arguments		: nothing
	##################################################################################################
	function getPersonal()
	{
		$this->load_settings();
		global $userId,$now;
		$data	= array();
		
		$query	= "SELECT 
					activities, interests, favorite_music, favorite_tv_shows, favorite_movies, favorite_books, favorite_quotes,about_me
					FROM member_personal
					WHERE user_id='$userId'";
		$res	= mysql_query($query);
		if($res)
		while($rs=mysql_fetch_object($res))
		{
			$data['clubs']		= $rs->activities;
			$data['interests']	= $rs->interests;
			$data['music']		= $rs->favorite_music;
			$data['tv']			= $rs->favorite_tv_shows;
			$data['movies']		= $rs->favorite_movies;
			$data['books']		= $rs->favorite_books;
			$data['quote']		= $rs->favorite_quotes;
			$data['about_me']	= $rs->about_me;
		}
		return $data;				
	}

	###################################################################################################
	#Method			: education()
	#Type			: Main
	#Description	: Create/Edit user education profile
	#Arguments		: nothing
	##################################################################################################
	function education()
	{
		$this->load_settings();
		$datas	= $this->common->authenticate();
		global $userId,$now;
		
		if(count($datas)<1)//if authentication failed
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
			//create education year select box
			$data['education_1_year_select']	= selectBox('education_1_year',createSchoolYear());
			$data['education_2_year_select']	= selectBox('education_2_year',createSchoolYear());
			$data['education_3_year_select']	= selectBox('education_3_year',createSchoolYear());
			$data['education_4_year_select']	= selectBox('education_4_year',createSchoolYear());
						
			if($_POST['submit_education'])
			{
				if($this->storeEducation())
					$data['success']	= "Education information successfully stored.";
				else
				{
					$data['error']	= "Education information has not been stored.";
				}//end else
			}//end submit
					
			$dat	= array_merge($data,$this->getEducation());
			//echo "<prev>";
			//print_r($dat);
			//echo "</prev>";
			$this->smartyextended->view('educationProfile',$dat);
		}
	}
	
	###################################################################################################
	#Method			: storeEducation()
	#Type			: sub
	#Description	: store education profile to database
	##################################################################################################
	function storeEducation()
	{
		$this->load_settings();
		global $userId,$now;
			
		$totalSchools	= $_POST['school_section_count'];
		for($i=1;$i<=$totalSchools;$i++)
		{
			if(trim($_POST['education_'.$i.'_school_name'])!='')
				$field1	= "school_name='".$_POST['education_'.$i.'_school_name']."'";
			if($_POST['education_'.$i.'_year']>0)
				$field1	.= ",class_year='".$_POST['education_'.$i.'_year']."'";
			$field1	.= ",attended_for='".$_POST['education_'.$i.'_school_type']."'";
			if($_POST['education_'.$i.'_school_type']=='gradschool')
				$field1	.= ",degree='".$_POST['education_'.$i.'_degree_name']."'";
			if(trim($_POST['education_'.$i.'_concentration1_name'])!='')
				$field1	.= ",concentration1='".$_POST['education_'.$i.'_concentration1_name']."'";
			if(trim($_POST['education_'.$i.'_concentration2_name'])!='')
				$field1	.= ",concentration2='".$_POST['education_'.$i.'_concentration2_name']."'";
			if(trim($_POST['education_'.$i.'_concentration3_name'])!='')
				$field1	.= ",concentration3='".$_POST['education_'.$i.'_concentration3_name']."'";
					
			$schoolType	= 'education'.$i;
					//$field1		.= ",school_type='$schoolType'";
					
			if(IsRecordExist4Add('school_education','school_edu_id',"user_id='$userId' AND school_type='$schoolType'"))
				$result1	= updateRecords('school_education',$field1,"user_id='$userId' AND school_type='$schoolType'");
			else
			{
				if($field1!='')
					$field1 .=",user_id='$userId',school_type='$schoolType'";
				else
					$field1 ="user_id='$userId',school_type='$schoolType'";
				$result1	= insertRecord('school_education',$field1);
			}
		}
					
		$highSchool	= $_POST['highschool'];
		if(IsRecordExist4Add('education_profile','education_id',"user_id='$userId'"))
			$result2	= updateRecords('education_profile',"high_school='$highSchool'","user_id='$userId'");
		else
			$result2	= insertRecord('education_profile',"high_school='$highSchool',datestamp='$now',user_id='$userId'");
		if($result1 and $result2)
			return true;
		else
			return false;
	}//end storeEducation()
	
	###################################################################################################
	#Method			: getEducation()
	#Type			: sub
	#Description	: get all education information
	#Arguments		: nothing
	##################################################################################################
	function getEducation()
	{
		$this->load_settings();
		global $userId,$now;
		$datas		= array();
		$eduRs		= getRow("education_profile","high_school","user_id='$userId'");
		//create education year select box
		$datas['education_1_year_select']	= selectBox('education_1_year',createSchoolYear());
		$datas['education_2_year_select']	= selectBox('education_2_year',createSchoolYear());
		$datas['education_3_year_select']	= selectBox('education_3_year',createSchoolYear());
		$datas['education_4_year_select']	= selectBox('education_4_year',createSchoolYear());
			
		$query	= "SELECT 
					school_name,class_year,attended_for,degree,concentration1,concentration2,concentration3,school_type
					FROM school_education
					WHERE user_id='$userId'";
		$totSec	= getTotRec('school_edu_id',"school_education","user_id='$userId'");
		$res	= mysql_query($query);
		$i		= 1;
		if($res)
		while($rs=mysql_fetch_object($res))
		{
			if($rs->school_type=='education'.$i)
			{
				$datas['att_nothing_'.$i]	= '';
				$datas['att_college_'.$i]	= '';
				$datas['att_school_'.$i]	= '';
							
				$datas['education_'.$i]		= true;
				$datas['school_name_'.$i]	= $rs->school_name;	
				if($rs->attended_for=='')
					$datas['att_nothing_'.$i]	= "selected";
				elseif($rs->attended_for=='college')
					$datas['att_college_'.$i]	= "selected";
				elseif($rs->attended_for=='gradschool')
					$datas['att_school_'.$i]	= "selected";
							
				$datas['degree_'.$i]		= $rs->degree;
				$datas['concentration1_'.$i]= $rs->concentration1;
				$datas['concentration2_'.$i]= $rs->concentration2;
				$datas['concentration3_'.$i]= $rs->concentration3;
				if($rs->class_year>0)
					$selectYear	= $rs->class_year;
				else
					$selectYear	= '-1';
				
				$datas['education_'.$i.'_year_select']	= selectBox('education_'.$i.'_year',createSchoolYear(),$selectYear);
			}
			$i++;
		}
			
		$datas['total_school']	= $totSec;
		$datas['high_school']	= $eduRs['high_school'];
		return $datas;
	}
	
	###################################################################################################
	#Method			: work()
	#Type			: Main
	#Description	: Create/Edit Work profile
	#Arguments		: nothing
	#Modified by	: Kiruthika
	#Modified Date	: 14-09-2007
	#Note			: Time period validation done 
	##################################################################################################
	function work()
	{
		$this->load_settings();
		$datas	= $this->common->authenticate();
		global $userId,$now;
			
		if(count($datas)<1)//if authentication failed
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
			$dat		= array_merge($data,$this->loadWorkSettings());
			if($_POST['submit_work'])
			{
				$dat['success']	= $this->storeWork();
			}//end submit
			$dat1	= array_merge($dat,$this->getWork());
			$this->load->view('workProfile_view',$dat1);
		}
	}//end work()
	
	###################################################################################################
	#Method			: storeWork()
	#Type			: sub
	#Description	: store work profile to database
	#Modified by	: Kiruthika
	#Modified Date	: 14-09-2007
	#Note			: Time period validation done 
	##################################################################################################
	function storeWork()
	{
		$this->load_settings();
		global $userId,$now;
		$result	= true;
		$totalJobs	= $_POST['work_section_count'];
		$j=0;
		for($i=1;$i<=$totalJobs;$i++)
		{
			if(trim($_POST['work_history_'.$i.'_company'])!='')
				$field1	= "employer='".$_POST['work_history_'.$i.'_company']."'";
			if(trim($_POST['work_history_'.$i.'_position'])!='')
				$field1	.= ",position='".$_POST['work_history_'.$i.'_position']."'";
			if(trim($_POST['work_history_'.$i.'_description'])!='')
				$field1	.= ",description='".$_POST['work_history_'.$i.'_description']."'";
			if(trim($_POST['work_history_'.$i.'_city'])!='')
				$field1	.= ",city='".$_POST['work_history_'.$i.'_city']."'";
			if($_POST['work_history_'.$i.'_state_us_select']>0)
				$field1	.= ",state_province='".$_POST['work_history_'.$i.'_state_us_select']."'";
			if($_POST['work_history_'.$i.'_state_ca_select']>0)
				$field1	.= ",state_province='".$_POST['work_history_'.$i.'_state_ca_select']."'";
			if($_POST['work_history_'.$i.'_country_select']!='-1')
				$field1	.= ",country='".$_POST['work_history_'.$i.'_country_select']."'";
			if($_POST['work_history_'.$i.'_workspan_current']=='on')
			{
				$curmonth= date("m");
				if(($_POST['work_history_'.$i.'_start_month_select']>0)  && ($curmonth>=$_POST['work_history_'.$i.'_start_month_select']))
					$field1	.= ",start_month='".$_POST['work_history_'.$i.'_start_month_select']."'";
				else
					$j++;
			}
			else
			{
				if($_POST['work_history_'.$i.'_start_month_select']>0) 
					$field1	.= ",start_month='".$_POST['work_history_'.$i.'_start_month_select']."'";
				if($_POST['work_history_'.$i.'_start_year_select']>0)
					$field1	.= ",start_year='".$_POST['work_history_'.$i.'_start_year_select']."'";
				$curmonth= date("m");
				if(($_POST['work_history_'.$i.'_end_month_select']>0)  && ($curmonth>=$_POST['work_history_'.$i.'_end_month_select']))
					$field1	.= ",end_month='".$_POST['work_history_'.$i.'_end_month_select']."'";
				else
					$j++;
				if(($_POST['work_history_'.$i.'_end_year_select']>0) && ($_POST['work_history_'.$i.'_start_year_select']<=$_POST['work_history_'.$i.'_end_year_select']) )
				{
					if($_POST['work_history_'.$i.'_start_year_select']==$_POST['work_history_'.$i.'_end_year_select'])
					{
						if($_POST['work_history_'.$i.'_start_month_select']<=$_POST['work_history_'.$i.'_end_month_select'])
							$field1	.= ",end_year='".$_POST['work_history_'.$i.'_end_year_select']."'";
						else
							$j++;
					}
					else
						$j++;
				}	
				else
					$j++;
			}
			if($_POST['work_history_'.$i.'_workspan_current']=='on')
				$field1	.= ",current_job='yes'";
			else
				$field1	.= ",current_job='no'";
			$jobType	= 'job'.$i;
			$field1		.= ",datestamp='$now'";
			if(IsRecordExist4Add('work_profile','work_id',"user_id='$userId' AND job_type='$jobType'"))
			{
				if($j==0)
				{
					$result1	= updateRecords('work_profile',$field1,"user_id='$userId' AND job_type='$jobType'");
					if($result1)
						$errmsg="Work information successfully stored.";
					else
						$errmsg="Work information has not been stored.";
				}
				else
				{
					$errmsg="Please enter correct time period.";
				}
							
			}
			else
			{
				if($field1!='')
					$field1 .=",user_id='$userId',job_type='$jobType'";
				else
					$field1 ="user_id='$userId',job_type='$jobType'";
				if($j==0)
				{
					$result1	= insertRecord('work_profile',$field1);
					if($result1)
						$errmsg="Work information successfully stored.";
					else
						$errmsg="Work information has not been stored.";
				}
				else
				{
					$errmsg="Please enter correct time period.";
				}
			}
			if(!$result1)
				$result	= false;
		}
				return $errmsg;
	}//end storeWork()
	
	###################################################################################################
	#Method			: getWork()
	#Type			: sub
	#Description	: get all job information to diplay on work profile page
	#Arguments		: nothing
	##################################################################################################
	function getWork()
	{
		$this->load_settings();
		global $userId,$now;
		$datas		= array();
		$datas		= $this->loadWorkSettings();
		$query	= "SELECT 
						employer,position,description,city,state_province,country,current_job,start_month,start_year,end_month,end_year,job_type
						FROM work_profile
						WHERE user_id='$userId' ORDER BY work_id";
		$totSec	= getTotRec('work_id',"work_profile","user_id='$userId'");
		$res	= mysql_query($query);
		$i		= 1;
		if($res)
		while($rs=mysql_fetch_object($res))
		{
			if($rs->job_type=='job'.$i)
			{
				$datas['job_'.$i]		= true;
				$datas['employer'.$i]	= $rs->employer;
				$datas['position'.$i]	= $rs->position;
				$datas['description'.$i]= $rs->description;
				$datas['city'.$i]		= $rs->city;
				if($rs->current_job=='yes')
					$datas['work_history_'.$i.'_current_job_checked']	= 'checked';
				elseif($rs->current_job=='no')
					$datas['work_history_'.$i.'_current_job_checked']	= '';
				if($rs->country!='')
					$selectCountry	= $rs->country;
				else
					$selectCountry	= '-1';
				if($rs->start_month!='')
					$selectSmonth	= $rs->start_month;
				else
					$selectSmonth	= '-1';
				if($rs->end_month!='')
					$selectEmonth	= $rs->end_month;
				else
					$selectEmonth	= '-1';
				if($rs->start_year!='')
					$selectSyear	= $rs->start_year;
				else
					$selectSyear	= '-1';
				if($rs->end_year!='')
					$selectEyear	= $rs->end_year;
				else
					$selectEyear	= '-1';
				$countryArr	= createCountry();//create country array
							//create country select box
				$workName	= 'work_history_'.$i;
				$datas['work_history_'.$i.'_country']		= selectBox('work_history_'.$i.'_country_select',$countryArr,$selectCountry,'onchange="selectState(\''.$workName.'\');"');
				if($rs->country!='' and $rs->country=='US')
					$datas['work_history_'.$i.'_state_us']	= selectBox('work_history_'.$i.'_state_us_select',createState('US'),$rs->state_province);
				if($rs->country!='' and $rs->country=='CA')
					$datas['work_history_'.$i.'_state_ca']	= selectBox('work_history_'.$i.'_state_ca_select',createState('CA'),$rs->state_province);
				$datas['work_history_'.$i.'_start_month']	= selectBox('work_history_'.$i.'_start_month_select',createMon(),$selectSmonth);
				$datas['work_history_'.$i.'_end_month']		= selectBox('work_history_'.$i.'_end_month_select',createMon(),$selectEmonth);
				$datas['work_history_'.$i.'_start_year']	= selectBox('work_history_'.$i.'_start_year_select',createWorkYear(),$selectSyear);
				$datas['work_history_'.$i.'_end_year']		= selectBox('work_history_'.$i.'_end_year_select',createWorkYear(),$selectEyear);
			}
			$i++;
		}
			
		if($totSec>0)
			$datas['total_jobs']	= $totSec;
		else
			$datas['total_jobs']	= 1;
		$datas[tot_jobs]	= $totSec;
		return $datas;
					
	}
	
	###################################################################################################
	#Method			: loadWorkSettings()
	#Type			: sub
	#Description	: create an array for the default select box values
	##################################################################################################
	function loadWorkSettings()
	{
		$data	= array();
		//create country select box
		$countryArr	= createCountry();//create country array
		for($i=1;$i<=4;$i++)
		{
			//create country select box
			$workName	= 'work_history'.$i;
			$data['work_history_'.$i.'_country']	= selectBox('work_history_'.$i.'_country_select',$countryArr,'','onchange="selectState(\''.$workName.'\');"');
			$data['work_history_'.$i.'_state_us']	= selectBox('work_history_'.$i.'_state_us_select',createState('US'));
			$data['work_history_'.$i.'_state_ca']	= selectBox('work_history_'.$i.'_state_ca_select',createState('CA'));
			$data['work_history_'.$i.'_start_month']= selectBox('work_history_'.$i.'_start_month_select',createMon());
			$data['work_history_'.$i.'_end_month']	= selectBox('work_history_'.$i.'_end_month_select',createMon());
			$data['work_history_'.$i.'_start_year']	= selectBox('work_history_'.$i.'_start_year_select',createWorkYear());
			$data['work_history_'.$i.'_end_year']	= selectBox('work_history_'.$i.'_end_year_select',createWorkYear());
			$data['work_history_'.$i.'_current_job_checked']	= 'checked';
		}			
		return $data;
	}//end loadWorkSettings()
	
	###################################################################################################
	#Method			: contact()
	#Type			: Main
	#Description	: Create/Edit Contact profile
	#Arguments		: nothing
	##################################################################################################
	function contact()
	{
		$this->load_settings();
		$datas	= $this->common->authenticate();
		global $userId,$now;
			
		if(count($datas)<1)//if authentication failed
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
			$dat		= array_merge($data,$this->loadContactSettings());
			if($_POST['submit_contact'])
			{
				if($this->storeContact())
						$dat['success']	= "Contact information successfully stored.";
				else
					$dat['error']	= "Contact information has not been stored.";
			}//end submit
			$dat1	= array_merge($dat,$this->getContact());
			$this->load->view('contactProfile_view',$dat1);
		}
	}//end contact()
	
	
		###################################################################################################
	#Method			: storeContact()
	#Type			: sub
	#Description	: store contact profile to database
	##################################################################################################
	function storeContact()
	{
		$this->load_settings();
		global $userId,$now;
		$result1	= true;
		$result2	= true;
		$screenCount	= $_POST['contact_screen_count'];
		for($i=1;$i<=$screenCount;$i++)
		{
			$imName		= $_POST['screen_name_'.$i.'_select'];			//get im online id
			$screenName	= $_POST['new_sn_'.$i];							//get screenname
			$screenType	= 'screen'.$i;									//prepare screen type(screen1,screen2...screen5)
			$field1 	= "im_id='$imName', screen_name='$screenName'";	//prepare field list
			if(IsRecordExist4Add('member_im_screen_name','screen_id',"user_id='$userId' AND screen_type='$screenType'"))
				$result1	= updateRecords('member_im_screen_name',$field1,"user_id='$userId' AND screen_type='$screenType'");
			else
			{
				if($field1!='')
					$field1 .=",user_id='$userId',screen_type='$screenType'";
				else
					$field1 ="user_id='$userId',screen_type='$screenType'";
					//echo $field1;
				$result1	= insertRecord('member_im_screen_name',$field1);
			}
		}
		if($_POST['contact_profile_state_us_select']>0)
			$state	= $_POST['contact_profile_state_us_select'];
		if($_POST['contact_profile_state_ca_select']>0)
			$state	= $_POST['contact_profile_state_ca_select'];
		//echo $_POST['work_history_'.$i.'_country_select'];
		if($_POST['contact_profile_country_select']!='-1')
			$country= $_POST['contact_profile_country_select'];
		
		$field2	= "mobile='".$_POST['mobile']."',mobile_privacy='".$_POST['mobile_privacy_select']."',land_line='".$_POST['other_phone']."',
						land_line_privacy='".$_POST['phone_privacy_select']."'
						,address='".$_POST['address']."',city='".$_POST['city']."',state_province='$state',country='$country'
						,zip_code='".$_POST['zip']."',zip_code_privacy='".$_POST['zip_privacy_select']."',website='".$_POST['website']."'
						,website_privacy='".$_POST['website_privacy_select']."',email_privacy='".$_POST['email_privacy_select']."'
						,screen_privacy='".$_POST['screen_privacy_select']."',datestamp='$now'";
		if(IsRecordExist4Add('contact_profile','contact_id',"user_id='$userId'"))
			$result2	= updateRecords('contact_profile',$field2,"user_id='$userId'");
		else
		{
			$field2 .=",user_id='$userId'";
			$result2= insertRecord('contact_profile',$field2);
		}
		if($result1 and $result2)
			return true;
		else
			return false;
	}//end storeWork()
	
	###################################################################################################
	#Method			: getContact()
	#Type			: sub
	#Description	: get all contact information to diplay on contact profile page
	#Arguments		: nothing
	##################################################################################################
	function getContact()
	{
		$this->load_settings();
		global $userId,$now;
		$datas		= array();
			
		//create screen name array
		$res			= mysql_query("SELECT im_id,im_name FROM member_online_im WHERE status='enabled'");
		$screenArray	=array();
		if($res)
		while($rs=mysql_fetch_object($res))
			$screenArray[$rs->im_id]	= $rs->im_name;
			
		//privacy name array
		$res			= mysql_query("SELECT privacy_id,privacy_name FROM privacy");
		$privacyArray	=array();
		if($res)
		while($rs=mysql_fetch_object($res))
			$privacyArray[$rs->privacy_id]	= $rs->privacy_name;
				
		//create country select box
		$countryArr	= createCountry();//create country array
			
		//create screen name array
		$res			= mysql_query("SELECT im_id,screen_name,screen_type FROM member_im_screen_name WHERE user_id='$userId'");
		$screenNameArray=array();
		if($res)
		while($rs=mysql_fetch_object($res))
		{
			for($i=1;$i<=5;$i++)
			{
				if($rs->screen_type=='screen'.$i)
				{
					//create country select box
					$screenname			= 'screen_name_'.$i.'_select';
					$datas[$screenname]	= selectBox($screenname,$screenArray);
					$datas['screen_name_'.$i]		= true;
					$datas['screen_name_'.$i.'_value']= $rs->screen_name;
				}
				else
				{
					//create country select box
					$screenname			= 'screen_name_'.$i.'_select';
					$datas[$screenname]	= selectBox($screenname,$screenArray);
				}
			}
		}
			
		$usrRs	= getRow("users","email","user_id='$userId'");
		$datas[user_email]	= $usrRs['email'];
			
		$query	= "SELECT 
						mobile, mobile_privacy, land_line, land_line_privacy, address, city, state_province, country,
						zip_code, zip_code_privacy, website, website_privacy, email_privacy, screen_privacy
						FROM contact_profile
						WHERE user_id='$userId'";
		$res	= mysql_query($query);
		if($res)
		while($rs=mysql_fetch_object($res))
		{
			$datas['contact_mobile']		= $rs->mobile;
			$datas['contact_landline']		= $rs->land_line;
			$datas['contact_address']		= $rs->address;
			$datas['contact_city']			= $rs->city;
			$datas['contact_state_province']= $rs->state_province;
			$datas['contact_zip_code']		= $rs->zip_code;
			$datas['contact_website']		= $rs->website;
			$datas['contact_country']		= $rs->country;
				
			if($rs->country!='')
				$selectCountry	= $rs->country;
			else
				$selectCountry	= '-1';
					
			$datas['email_privacy_select']	= selectBox('email_privacy_select',$privacyArray,$rs->email_privacy);	
			$datas['screen_privacy_select']	= selectBox('screen_privacy_select',$privacyArray,$rs->screen_privacy);
			$datas['mobile_privacy_select']	= selectBox('mobile_privacy_select',$privacyArray,$rs->mobile_privacy);
			$datas['phone_privacy_select']	= selectBox('phone_privacy_select',$privacyArray,$rs->land_line_privacy);
			$datas['zip_privacy_select']		= selectBox('zip_privacy_select',$privacyArray,$rs->zip_code_privacy);
			$datas['website_privacy_select']	= selectBox('website_privacy_select',$privacyArray,$rs->website_privacy);
			$datas['contact_profile_country_select']		= selectBox('contact_profile_country_select',$countryArr,$selectCountry,'onchange="selectState(\'contact_profile\');"');
			if($selectCountry=='US')
				$datas['contact_profile_state_us_select']	= selectBox('contact_profile_state_us_select',createState('US'),$rs->state_province);
			elseif($selectCountry=='CA')
				$datas['contact_profile_state_ca_select']	= selectBox('contact_profile_state_ca_select',createState('CA'),$rs->state_province);
		}//end while
		return $datas;
	}//end getContact()
	
	###################################################################################################
	#Method			: loadContactSettings()
	#Type			: sub
	#Description	: create an array for the default select box values for contact profile
	##################################################################################################
	function loadContactSettings()
	{
		$data	= array();
		//create screen name array
		$res			= mysql_query("SELECT im_id,im_name FROM member_online_im WHERE status='enabled'");
		$screenArray	=array();
		if($res)
		while($rs=mysql_fetch_object($res))
			$screenArray[$rs->im_id]	= $rs->im_name;
			
		//privacy name array
		$res			= mysql_query("SELECT privacy_id,privacy_name FROM privacy");
		$privacyArray	=array();
		if($res)
		while($rs=mysql_fetch_object($res))
			$privacyArray[$rs->privacy_id]	= $rs->privacy_name;
				
		//create country select box
		$countryArr	= createCountry();//create country array
			
		for($i=1;$i<=5;$i++)
		{
			//create country select box
			$screenname	= 'screen_name_'.$i.'_select';
			$data[$screenname]	= selectBox($screenname,$screenArray);
		}
		$data['email_privacy_select']	= selectBox('email_privacy_select',$privacyArray);	
		$data['screen_privacy_select']	= selectBox('screen_privacy_select',$privacyArray);
		$data['mobile_privacy_select']	= selectBox('mobile_privacy_select',$privacyArray);
		$data['phone_privacy_select']	= selectBox('phone_privacy_select',$privacyArray);
		$data['zip_privacy_select']		= selectBox('zip_privacy_select',$privacyArray);
		$data['website_privacy_select']	= selectBox('website_privacy_select',$privacyArray);
		$data['contact_profile_country_select']		= selectBox('contact_profile_country_select',$countryArr,'','onchange="selectState(\'contact_profile\');"');
		$data['contact_profile_state_us_select']	= selectBox('contact_profile_state_us_select',createState('US'));
		$data['contact_profile_state_ca_select']	= selectBox('contact_profile_state_ca_select',createState('CA'));
		return $data;
	}//end loadContactSettings()
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