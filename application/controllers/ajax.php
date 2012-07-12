<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
##################################################################
//File			: ajax.php
//Description	: to manage ajax functions
//Author		: ilayaraja_22ag06
//Created On	: 11-Apr-2007
//Last Modified	: 11-Apr-2007
##################################################################
class Ajax extends CI_Controller
{

	public function __construct()
	{
		parent :: __construct();
	}

	function index()
		{
			$this->loadSettings();
			$this->load->helper('captcha'); //调用helper中的captcha
			$datas	= $this->common->basicvars();
			global $userId,$now,$mplayer,$mencoder,$rootPath,$adminName,$adminEmail,$siteName,$siteTitle,$recorderPath,$red5SettingsPath;

			if($_POST['action']=='register')
				{
					$uname			= $_POST['uname'];
					$lifestage		= $_POST['lifestage'];
					$schoolStatus	= $_POST['schoolStatus'];
					$collegeYear	= $_POST['collegeYear'];
					$highSchool		= $_POST['highSchool'];
					$schoolYear		= $_POST['schoolYear'];
					$email			= $_POST['email'];
					$pass			= $_POST['pass'];
					$bd				= $_POST['bd'];
					$bm				= $_POST['bm'];
					$by				= $_POST['by'];
					$terms			= $_POST['terms'];
					$captcha		= $_POST['captcha'];
					$randWord		= $_POST['randWord'];
					$isRecordExist	= getTotRec("user_id","users","email='$email'");

					if($uname=='' or $lifestage=='' or $email=='' or $pass=='' or $bd=='-1' or $bm=='-1' or $by=='-1' or $captcha=='')
						$result	= "You must fill in all of the fields.";
					elseif($lifestage=='1' and ($schoolStatus=='' or $collegeYear==''))
						$result	= "You must fill in all of the fields.";
					elseif($lifestage=='3' and ($highSchool=='' or $schoolYear==''))
						$result	= "You must fill in all of the fields.";
					elseif(!isValidEmail($email))
						$result= "Please give the valid email !";
					elseif(strlen($pass)<6)
						$result= "Your password must be at least 6 characters long. Please try another.";
					elseif(!$this->common->validMonthDay($bm,$bd))
						$result= "Please give the valid birthday.";
					elseif($captcha!=$randWord)
						{
						$result= "You didn't correctly type the text in the box.";
						echo $captcha;
						echo $randWord;
						}
					elseif(!$terms)
						$result= "Please accept the terms !";
					elseif($isRecordExist)
						$result	= "This email is already shared by another user !";
					else
						{
							$encPass	= md5($pass);//encrypted password
							$birthday	= $bm."-".$bd."-".$by;
							$randNo		= rand();
							$fieldList	= "username='$uname', password='$encPass', show_password='$pass', email='$email', registered_date='$now', lifestage_id='$lifestage', school_status_id='$schoolStatus', college_year='$collegeYear', high_school='$highSchool', school_year='$schoolYear', birthday='$birthday',activation_key='$randNo'";
							if(insertRecord('users', $fieldList))
								{
									$mailFrom		= $adminEmail;
									$mailTo			= $email;//receiver email id
									$url			= '<a href="'.base_url() .'index.php/register/confirm/'.$randNo.'">'.base_url() .'index.php/register/confirm/'.$randNo.'</a>';
									$tempRs			= getRow("mail_templates","template_subject,template_content","template_id=1");//for account confirmation(template_id=1)
									$spArray		= array("~~receiverName~~"=>$uname,"~~siteName~~"=>$siteName,"~~siteTitle~~"=>$siteTitle,"~~attachUrl~~"=>$url,"~~adminEmail~~"=>$adminEmail,"~~adminName~~"=>$adminName);
									$subject		= nl2br(replaceSpVariables($spArray,$tempRs['template_subject']));//mail subject
									$content		= nl2br(replaceSpVariables($spArray,$tempRs['template_content']));//mail subject
									//check whether anyone send friend request
									//if so, update friend_id to the registered user id onthis table
									$haveRequest	= getTotRec("user_id","friends_list","friend_mail='$email'");
									$currentUsrId	= mysql_insert_id();
									if($haveRequest)
										updateRecords("friends_list","friend_id='$currentUsrId'","friend_email='$email'");
									//send the mail
									
									if(sendMail($content,$subject,$mailFrom,$mailTo))
										$result	= "success";
									else
										$result	= "Error in sending confrimation mail.";
								}
							else
								$result	= "Sorry! Technical problem.<br>Mysql Error: ".mysql_error();
						}
					echo $result;
				}//end register process
			elseif($_POST['action']=="basicProfile")
				{
					$result		= "Data has not been saved .";

					$userId		= $_POST['userId'];
					$sex		= $_POST['sex'];
					$meeting	= $_POST['meeting'];
					$relation	= $_POST['relation'];
					$lookingFor	= $_POST['lookingFor'];
					$bd			= $_POST['bd'];
					$bm			= $_POST['bm'];
					$by			= $_POST['by'];
					if($bd !='-1' and $bm!='-1' and $by!='-1')
						$birthday	= $bd."-".$bm."-".$by;
					else
						$birthday	= '';
					$bdVisible	= $_POST['bd_visible'];
					$hometown	= $_POST['hometown'];
					$country	= $_POST['country'];
					$state		= $_POST['state'];
					$politics	= $_POST['politics'];
					$religion	= $_POST['religion'];
					$fieldList	= " user_id='$userId', sex='$sex', interested_in='$meeting', relation_id='$relation',
									looking_for_id='$lookingFor', birthday='$birthday', birthday_visibility_id='$bdVisible',
									hometown='$hometown', country='$country', state='$state', political_id='$politics', religious_view='$religion' ";

					if(IsRecordExist4Add('basic_profile','basic_profile_id',"user_id='$userId'"))
						{
							if(updateRecords('basic_profile', $fieldList,"user_id='$userId'"))
								$result	= "Data has been saved .";
						}
					else
						{
							if(insertRecord('basic_profile', $fieldList))
								$result	= "Data has been saved .";
						}
					echo $result;
				}//end basic profile edit
			elseif($_POST['action']=="message_mark_unread" or $_POST['action']=="message_mark_read" or $_POST['action']=="message_move_to_trash" or $_POST['action']=="message_select_action")
				{
					$doit	= $_POST['action'];
					$msgIds	= $_POST['msgIds'];
					$folder	= $_POST['folder'];
					//$result='nothing';
					if($msgIds =='')
						$result ="<p align='center'>Please select atleast one message to process !</p>";
					else
						{
							$msgIds	= explode(",",$msgIds);
							$totMsg	= count($msgIds)-1;
							$condition 		= '';
							$inboxCondition = '';
							$sentCondition 	= '';

							if($folder=='inbox')
								{
									$fieldName	= " inbox_message_id ";
									$table		= "inbox_messages";
								}
							else
								{
									$fieldName	= " message_id ";
									$table		= "messages";
								}
							for($i=0;$i<$totMsg;$i++)
								{
									if($i == $totMsg-1)
										$sentCondition .= $fieldName."=".$msgIds[$i];
									else
										$sentCondition .= $fieldName."=".$msgIds[$i]." OR ";
								}

							if($doit =='message_mark_read' and $sentCondition !='')
								$query	= "UPDATE ".$table." SET message_status='read' WHERE ".$sentCondition." AND user_id='$userId'";
							elseif($doit =='message_mark_unread' and $sentCondition !='')
								$query	= "UPDATE ".$table." SET message_status='unread' WHERE ".$sentCondition." AND user_id='$userId'";
							elseif($doit =='message_move_to_trash' and $sentCondition !='')
								$query	= "UPDATE ".$table." SET message_status='trash' WHERE ".$sentCondition." AND user_id='$userId'";
							else
								$query	= '';
							//if($query!='')
							mysql_query($query);
							if($folder=='inbox')
								{
									$selQuery	= 	"SELECT subject,from_id,datestamp,inbox_message_id,message_status
													FROM inbox_messages
													WHERE user_id='$userId' AND is_deleted=0 AND message_status<>'trash' ORDER BY datestamp DESC";
									$result		= $this->common->listMessage($selQuery,'inbox');
								}
							else
								{
									$selQuery	= 	"SELECT subject,to_id ,datestamp,message_id,message, message_status,to_name, to_type
													FROM messages
													WHERE user_id='$userId' AND message_status<>'trash' AND is_deleted=0  ORDER BY datestamp DESC";
									$result		= $this->common->listMessage($selQuery,'sent');
								}
									//$result	= "<p align='center'>The selected message has been marked as unread !</p>";
									//else
										//$result	= "<p align='center'>Problem in changing message status as unread !</p>";
								//}
							//else
								//$result	= "<p align='center'>Nothing has been done !</p>";
							//echo $result;
						}
					echo $result;

				}//end markread, mark unread
			elseif($_POST['action']=="trash_delete")
				{
					$inboxMsgIds	= $_POST['inboxMsgIds'];
					$sentMsgIds		= $_POST['sentMsgIds'];
					if($inboxMsgIds =='' and $sentMsgIds=='')
						$result ="<p align='center'>Please select atleast one message to process !</p>";
					else
						{
							$inboxMsgIds	= explode(",",$inboxMsgIds);
							$sentMsgIds		= explode(",",$sentMsgIds);
							$totInboxMsg	= count($inboxMsgIds);
							$totSentMsg		= count($sentMsgIds);
							if($totInboxMsg>1)
								{
									$totInboxMsg = $totInboxMsg-1;
									for($i=0;$i<$totInboxMsg;$i++)
										{
											if($i == $totInboxMsg-1)
												$inboxCondition .= " inbox_message_id = ".$inboxMsgIds[$i];
											else
												$inboxCondition .= " inbox_message_id = ".$inboxMsgIds[$i]." OR ";
										}
									$query	= "UPDATE inbox_messages SET is_deleted='1' WHERE ".$inboxCondition." AND user_id='$userId'";
									mysql_query($query);
								}//end if
							if($totSentMsg>1)
								{
									$totSentMsg = $totSentMsg-1;
									for($i=0;$i<$totSentMsg;$i++)
										{
											if($i == $totSentMsg-1)
												$sentCondition .= " message_id = ".$sentMsgIds[$i];
											else
												$sentCondition .= " message_id = ".$sentMsgIds[$i]." OR ";
										}
									$query	= "UPDATE messages SET is_deleted='1' WHERE ".$sentCondition." AND user_id='$userId'";
									mysql_query($query);
								}//end if

							$query		= array();
							//echo "SELECT subject,to_id  AS userId,datestamp,message_id AS msgId FROM messages WHERE user_id='$userId' AND message_status='trash' AND is_deleted=0";
							$query[]	= "SELECT subject,to_id  AS userId,datestamp,message_id AS msgId FROM messages WHERE user_id='$userId' AND message_status='trash' AND is_deleted=0";
							//echo "SELECT subject,from_id AS userId,datestamp,inbox_message_id AS msgId FROM inbox_messages WHERE user_id='$userId' AND message_status='trash' AND is_deleted=0";;
							$query[]	= "SELECT subject,from_id AS userId,datestamp,inbox_message_id AS msgId FROM inbox_messages WHERE user_id='$userId' AND message_status='trash' AND is_deleted=0";
							$result	= $this->listTrash($query);
						}//end if

					//return result
					echo $result;
				}//end action
			elseif($_POST['action']=="msgSingleDel")
				{
					$splitId	= explode("_",$_POST['delId']);
					//print_r($splitId);
					if($splitId[0]=='inbox')
						{
							$query		= "UPDATE inbox_messages SET message_status='trash' WHERE inbox_message_id=".$splitId[1];
							mysql_query($query);
							$selQuery	= 	"SELECT subject,from_id,datestamp,inbox_message_id,message_status
											FROM inbox_messages
											WHERE user_id='$userId' AND is_deleted=0 AND message_status<>'trash' ORDER BY datestamp DESC";
							$result		= $this->common->listMessage($selQuery,'inbox');
						}
					elseif($splitId[0]=='sent')
						{
							$query		= 	"UPDATE messages SET message_status='trash' WHERE message_id=".$splitId[1];
							mysql_query($query);
							$selQuery	= 	"SELECT subject,to_id ,datestamp,message_id,message, message_status,to_name,to_type
											FROM messages
											WHERE user_id='$userId' AND message_status<>'trash' AND is_deleted=0 ORDER BY datestamp DESC";
							$result		= $this->common->listMessage($selQuery,'sent');
						}
					echo $result;
				}
			elseif($_POST['action']=='msgTrashSingleDel')
				{
					$splitId	= explode("_",$_POST['delId']);
					if($splitId[0]=='inbox')
						$query	= "UPDATE inbox_messages SET is_deleted='1' WHERE inbox_message_id=".$splitId[1];
					elseif($splitId[0]=='sent')
						$query	= "UPDATE messages SET is_deleted='1' WHERE message_id=".$splitId[1];
					mysql_query($query);

					//call trash
					$query		= array();
					//echo "SELECT subject,to_id  AS userId,datestamp,message_id AS msgId FROM messages WHERE user_id='$userId' AND message_status='trash' AND is_deleted=0";
					$query[]	= "SELECT subject,to_id  AS userId,datestamp,message_id AS msgId FROM messages WHERE user_id='$userId' AND message_status='trash' AND is_deleted=0";
					//echo "SELECT subject,from_id AS userId,datestamp,inbox_message_id AS msgId FROM inbox_messages WHERE user_id='$userId' AND message_status='trash' AND is_deleted=0";;
					$query[]	= "SELECT subject,from_id AS userId,datestamp,inbox_message_id AS msgId FROM inbox_messages WHERE user_id='$userId' AND message_status='trash' AND is_deleted=0";
					$result	= $this->listTrash($query);
					echo $result;
				}
			elseif($_POST['action']=='groupCategory')
				{

					$category	= $_POST['category'];
					if($category !='0')
						{
							//create group sub category select box
							$res				= mysql_query("SELECT group_sub_category_id,group_sub_category_name FROM groups_sub_category WHERE group_sub_category_status='Yes' and group_category_id='$category'");
							$groupSubCategory	= array();
							$groupSubCategory[0]= "Select Type:";
							while($rs=mysql_fetch_object($res))
								$groupSubCategory[$rs->group_sub_category_id]	= $rs->group_sub_category_name;
							echo "var groupSubCategory = " . php2js($groupSubCategory);
						}
					//return "hi";
				}//end group category
			//show invitation form
			elseif($_POST['action']=='showInviteForm')
				{
					$friendId	= $_POST['fid'];												//get friend id from ajax.js
					$groupId	= $_POST['checkForId'];											//get group id from ajax.js
					$currentPage= base_url();

					$usrRs		= getRow('users',"username","user_id='$friendId'");

					$content	= '<div id="invitation_wrapper">
									<table border="1" rules="none">
									<tr>
										<td>Add people using the options on the right, then send them invitations. </td>
									</tr>
									<tr>
										<td><a href="#">'.$usrRs['username'].'</a></td>
										<td><a href="javascript: void(0)" onclick="removeInvitation(\''.$groupId.'\',\''.$currentPage.'\',\'group\')">Remove</a></td>
									</tr>
									<tr>
										<td>
										<h3>Message to Members (optional):</h3>
										<form method="post" name="frm_invite_pending" id="frm_invite_pending">
											<textarea id="personal" name="personal" class="textarea" rows="4"></textarea><br>
											<span id="personal_msg" style="display: none;"><small>Keep it under 255 characters.</small></span>
											<div id="invite_button">
											<input class="inputsubmit" value="Send Invitations" type="button" onclick="sendInvitation(\''.$friendId.'\',\''.$groupId.'\',\''.$currentPage.'\',\'group\');">
											</div>
										</form>
										</td>
									</tr>
									</table>
								</div>';
					echo $content;
				}//end showInviteForm
			//send invitation for the given friend id, for groups/events
			elseif($_POST['action']=='sendInvitation')
				{
					$friendId	= $_POST['fid'];												//get friend id from ajax.js
					$ownMessage	= $_POST['msg'];
					$sendFor	= $_POST['sendFor'];
					if($_POST['sendFor']=='group')												//get email content from ajax.js
						{
							$sendForId	= $_POST['sendForId'];									//get group id from ajax.js
							$rs			= getRow('groups',"group_name","group_id='$groupId'");	//select group name
							$sendForName= $rs['group_name'];									//get group name
							$viewUrl	= '<a href="'.base_url().'index.php/groups/view/'.$sendForId.'">'.base_url().'index.php/groups/view/'.$sendForId.'</a>';
						}
					elseif($_POST['sendFor']=='event')
						{
							$sendForId	= $_POST['sendForId'];									//get event id from ajax.js
							$rs			= getRow('events',"event_name","event_id='$eventId'");	//select event name
							$sendForName= $rs['event_name'];									//get event name
							$viewUrl	= '<a href="'.base_url().'index.php/events/view/'.$sendForId.'">'.base_url().'index.php/events/view/'.$sendForId.'</a>';
						}
					$frndRs		= getRow('users',"username,email","user_id='$friendId'");		//select receiver(Friend) name
					$usrRs		= getRow('users',"username,email","user_id='$userId'");			//select sender(current user) name
					$friendName	= $frndRs['username'];											//get friend name
					$userName	= $usrRs['username'];											//get current user name
					$friendEmail= $frndRs['email'];												//get friend name
					$userEmail	= $usrRs['email'];												//get current user name

					$this->load->library('email');												//load email library
					$config['mailtype'] = 'html';
					$this->email->initialize($config);
					$this->email->from($userEmail, $userName);
					$this->email->to($friendEmail);
					//$this->email->cc('another@another-site.com');
					//$this->email->bcc('them@their-site.com');
					if($ownMessage !='')
						$message	= $userName.'\'s private message<br>'.$ownMessage;
					if($_POST['sendFor']=='group')												//get email content from ajax.js
						{
							$tempRs			= getRow("mail_templates","template_subject,template_content","template_id=3");//for Invites friends to join a group(template_id=3)
							$spArray		= array("~~groupName~~"=>$sendForName,"~~privateMessage~~"=>$message,"~~senderName~~"=>$userName,"~~siteName~~"=>$siteName,"~~attachUrl~~"=>$viewUrl,"~~adminName~~"=>$adminName);
						}
					elseif($_POST['sendFor']=='event')
						{
							$tempRs			= getRow("mail_templates","template_subject,template_content","template_id=4");//for Invites friends to join a event(template_id=4)
							$spArray		= array("~~eventName~~"=>$sendForName,"~~privateMessage~~"=>$message,"~~senderName~~"=>$userName,"~~siteName~~"=>$siteName,"~~attachUrl~~"=>$viewUrl,"~~adminName~~"=>$adminName);
						}

					$subject	= nl2br(replaceSpVariables($spArray,$tempRs['template_subject']));//mail subject
					$content	= nl2br(replaceSpVariables($spArray,$tempRs['template_content']));//mail subject

					$this->email->subject($subject);											//set subject to send mail
					$this->email->message($content);											//set message to send mail
					if($this->email->send())
						{
							if($_POST['sendFor']=='group')
								insertRecord("groups_invitation","sender_id='$userId',receiver_id='$friendId',group_id='$sendForId',invitation_message='$ownMessage'");
							elseif($_POST['sendFor']=='event')
								insertRecord("events_invitation","sender_id='$userId',receiver_id='$friendId',event_id='$sendForId',invitation_message='$ownMessage'");
							echo "Invitation sent to :".$friendEmail;
						}
					else
						echo "Error in sending invitation.";
					//echo $this->email->print_debugger();
				}//end send invitation
			//send invitation to people who are belongs to this group,
			//to join this event
			elseif($_POST['action']=='inviteGroup')
				{
					$eventId	= $_POST['eid'];
					$groupId	= $_POST['gid'];												//get group id
					$rs			= getRow('events',"event_name","event_id='$eventId'");	//select event name
					$eventName	= $rs['event_name'];									//get event name
					$viewUrl	= base_url().'index.php/events/'.$eventId;

					$i			= 0;
					$j			= 0;
					$invited	= array();
					$notInvited	= array();
					//get the owner of the group
					$rs			= getRow('groups',"user_id","group_id='$groupId'");
					//check the group owner got invitation or not
					$isInvited	= getTotRec("event_invitation_id","events_invitation","receiver_id=".$rs[user_id]);
					$usrRs		= getRow("users","username,email,user_id","user_id=".$rs[user_id]);
					if($isInvited)//check the group member already invited or not
						{
							$invited[$j]['name']	= $usrRs['username'];
							$j++;
						}
					else
						{
							$notInvited[$i]['name']		= $usrRs['username'];
							$notInvited[$i]['email']	= $usrRs['email'];
							$notInvited[$i]['user_id']	= $usrRs['user_id'];
							$i++;
						}
					//end owner

					//prepares query for to list other memebers in this group
					$query		= "SELECT receiver_id FROM groups_invitation
									WHERE group_id='$groupId' AND invitation_status='accepted'";
					$res		= mysql_query($query);
					if($res)
						while($rs=mysql_fetch_object($res))
							{
								$isInvited	= getTotRec("event_invitation_id","events_invitation","receiver_id='$rs->receiver_id'");
								$usrRs		= getRow("users","username,email,user_id","user_id='$rs->receiver_id'");
								if($isInvited)//check the group member already invited or not
									{
										$invited[$j]['name']	= $usrRs['username'];
										$j++;
									}
								else
									{
										$notInvited[$i]['name']		= $usrRs['username'];
										$notInvited[$i]['email']	= $usrRs['email'];
										$notInvited[$i]['user_id']	= $usrRs['user_id'];
										$i++;
									}
							}//end while
					//end not already invited people preparation

					if(count($notInvited)>0)
						{	//send invitation, if the group having members who are not already got
							//invitation from this event
							$usrRs		= getRow('users',"username,email","user_id='$userId'");			//select sender(current user) name
							$userName	= $usrRs['username'];											//get current user name
							$userEmail	= $usrRs['email'];												//get current user name
							//prepares invitelist and receiver email list
							$inviteList	= '';
							$tolist		= '';
							for($loop=0;$loop<count($notInvited);$loop++)
								{
									$inviteList	.= ucwords($notInvited[$loop][name])."<br>";
									$tolist		.= $notInvited[$loop][email].",";
								}
							$this->load->library('email');												//load email library
							$config['mailtype'] = 'html';
							$this->email->initialize($config);
							$this->email->from($userEmail, $userName);
							$this->email->to($tolist);

							$subject	= $userName.' has invited you to join the event "'.$eventName.'"...';
							$content	= $userName.' has invited you to join the '.$siteTitle.' event "'.$eventName.'".<br><br>
											To see more details and confirm this event invitation, follow the link below:<br>'.
											$viewUrl.'<br><br>
											Thanks,<br>
											The '.$adminName;
							$this->email->subject($subject);											//set subject to send mail
							$this->email->message($content);											//set message to send mail

							if($this->email->send())
								{
									for($loop=0;$loop<count($notInvited);$loop++)
										insertRecord("events_invitation","sender_id='$userId',receiver_id=".$notInvited[$loop][user_id].",event_id='$eventId'");
									$notInviteResult	= "Invites were sent to the following peoples:<br>".$inviteList;
								}
							else
								$notInviteResult	=  "Error in sending invitation to the following peoples:<br>".$inviteList;
						}
					//the people who are already already invited for this event
					if(count($invited)>0)
						{
							$inviteList	= '';
							for($loop=0;$loop<count($invited);$loop++)
								$inviteList	.= ucwords($invited[$loop][name])."<br>";
							$inviteResult	= "The following people have already been invited:<br>".$inviteList;
						}
					echo $notInviteResult."<br>".$inviteResult;
				}//end inviteGroup

			//hide send invitation form, and prepare members list
			elseif($_POST['action']=='removeInvitation')
				{
					$listId		= $_POST['remForId'];
					$friends	= $this->friendslist($_POST['remForId'],$_POST['remFor']);
					echo $friends;
				}
			//send multiple invitation, for groups and events
			elseif($_POST['action']=='sendMultiple')
				{
					$tolist		= $_POST['tolist'];												//get email content from ajax.js
					$sendFor	= $_POST['sendFor'];
					$mailFlag	= true;
					$maillist	= explode(",",$tolist);
					for($i=0;$i<count($maillist);$i++)
						{
							if(!isValidEmail($maillist[$i]))
								$mailFlag=false;
						}

					if($_POST['sendFor']=='group')												//get email content from ajax.js
						{
							$sendForId	= $_POST['sendForId'];									//get group id from ajax.js
							$rs			= getRow('groups',"group_name","group_id='$groupId'");	//select group name
							$sendForName= $rs['group_name'];									//get group name
							$viewUrl	= base_url().'index.php/groups/'.$sendForId;			//url to see this group
									$stat="true";

						}
					elseif($_POST['sendFor']=='event')
						{
							$sendForId	= $_POST['sendForId'];									//get event id from ajax.js
							$rs			= getRow('events',"event_name","event_id='$eventId'");	//select event name
							$sendForName= $rs['event_name'];									//get event name
							$viewUrl	= base_url().'index.php/events/'.$sendForId;			//url to see this event
									$stat="false";

						}

					if($mailFlag)
						{
							if($_POST['sendFor']=='group')												//get email content from ajax.js
								{
									$sendForId	= $_POST['sendForId'];									//get group id from ajax.js
									$rs			= getRow('groups',"group_name","group_id='$sendForId'");//select group name
									$sendForName= $rs['group_name'];									//get group name
									$viewUrl	= '<a href="'.base_url().'index.php/groups/'.$sendForId.'">'.base_url().'index.php/groups/'.$sendForId.'</a>';
									$stat="true";
								}
							elseif($_POST['sendFor']=='event')
								{
									$sendForId	= $_POST['sendForId'];									//get event id from ajax.js
									$rs			= getRow('events',"event_name","event_id='$sendForId'");//select event name
									$sendForName= $rs['event_name'];									//get event name
									$viewUrl	= '<a href="'.base_url().'index.php/events/'.$sendForId.'">'.base_url().'index.php/events/'.$sendForId.'</a>';
									$stat="false";
								}
							$usrRs		= getRow('users',"username,email","user_id='$userId'");			//select sender(current user) name
							$userName	= $usrRs['username'];											//get current user name
							$userEmail	= $usrRs['email'];												//get current user name

							$this->load->library('email');												//load email library
							$config['mailtype'] = 'html';
							$this->email->initialize($config);
							$this->email->from($userEmail, $userName);
							$this->email->to($tolist);
							if($_POST['sendFor']=='group')												//get email content from ajax.js
								{
									$tempRs			= getRow("mail_templates","template_subject,template_content","template_id=3");//for Invites friends to join a group(template_id=3)
									$spArray		= array("~~groupName~~"=>$sendForName,"~~privateMessage~~"=>$message,"~~senderName~~"=>$userName,"~~siteName~~"=>$siteName,"~~attachUrl~~"=>$viewUrl,"~~adminName~~"=>$adminName);
								}
							elseif($_POST['sendFor']=='event')
								{
									$tempRs			= getRow("mail_templates","template_subject,template_content","template_id=4");//for Invites friends to join a event(template_id=4)
									$spArray		= array("~~eventName~~"=>$sendForName,"~~privateMessage~~"=>$message,"~~senderName~~"=>$userName,"~~siteName~~"=>$siteName,"~~attachUrl~~"=>$viewUrl,"~~adminName~~"=>$adminName);
								}
							$subject	= nl2br(replaceSpVariables($spArray,$tempRs['template_subject']));//mail subject
							$content	= nl2br(replaceSpVariables($spArray,$tempRs['template_content']));//mail subject
							$this->email->subject($subject);											//set subject to send mail
							$this->email->message($content);											//set message to send mail

								$notifiyRs	= getRow("users_status","notification_status","notification_id=2 AND user_id=".$userId);
								if($notifiyRs[notification_status]=='1' or $notifiyRs[notification_status]=='')
									$this->email->send();
								else
									continue;
							//check for notification
							if($stat=="true")
								$notifiyRs	= getRow("users_status","notification_status","notification_id=3 AND user_id=".$userId);
							else
								$notifiyRs	= getRow("users_status","notification_status","notification_id=4 AND user_id=".$userId);
							if($notifiyRs[notification_status]=='1' or $notifiyRs[notification_status]=='')
								$this->email->send();
							else
								continue;
							echo "Successfully mail sent.";

							$emailSplit	= explode(",",$tolist);
							//store all email friends in database
							for($i=0;$i<count($emailSplit);$i++)
								{
									//echo $emailSplit[$i];
									//checks whether the receiver email is already member of this site
									//if so store his user id, else store 0 as his user id and also store his email id
									$frndRs		= getRow("users","user_id","email='".$emailSplit[$i]."'");
									if($frndRs['user_id']!='')
										$friendId	= $frndRs['user_id'];
									else
										$friendId	= 0;
									insertRecord("friends_list","user_id='$userId',friend_mail='".$emailSplit[$i]."', friend_id='$friendId', datestamp='$now'");
								}
						}
					else
						echo "Please give the valid Email(s).";
				}//end sendmultiple
			elseif($_POST['action']=='showMembers' or $_POST['action']=='showNotreplied' or  $_POST['action']=='showBlocked')
				{
					$groupId	= $_POST['gid'];				//get group id from ajax.js
					$members	= '';
					$first		= false;
					$second		= false;
					$third		= false;

					if($_POST['action']=='showMembers')			//query for group members only
						{
							$first	= true;
							$query	= "SELECT receiver_id FROM groups_invitation WHERE group_id='$groupId' AND invitation_status='accepted'";
						}
					elseif($_POST['action']=='showNotreplied')	//query for non members
						{
							$second	= true;
							$query	= "SELECT receiver_id FROM groups_invitation WHERE group_id='$groupId' AND invitation_status='sent'";
						}
					elseif($_POST['action']=='showBlocked')		//query for blocked members
						{
							$third	= true;
							$query	= "SELECT receiver_id FROM groups_invitation WHERE group_id='$groupId' AND invitation_status='blocked'";
						}

					$res		= mysql_query($query);
					$totMembers	= 0;
					if($res)
						{
							while($rs=mysql_fetch_object($res))
								{
									$totMembers++;
									$usrRs		= getRow('users',"username","user_id='$rs->receiver_id'");
									$members 	.= '<span class="name_row" id="member_'.$rs->receiver_id.'">
													<a href="#">'.$usrRs['username'].'</a>
													</span>';
								}//END WHILE
						}//END IF

					//get group owner name, if the action is for members
					if($_POST['action']=='showMembers')
						{
							$totMembers++;
							$rs		= getRow('groups',"user_id","group_id='$groupId'");
							$usrRs	= getRow('users',"username","user_id='$rs[user_id]'");
							$members 	.= '<span class="name_row" id="member_'.$rs['user_id'].'">
											<a href="#">'.$usrRs['username'].'</a>
											</span>';
						}

					if($members =='')
						echo "<h4>Your search returned no results.</h4>";
					else
						{
							if($first)
								$members	= '<span><h4>Showing '.$totMembers.' member(s)</h4></span>'.$members;
							elseif($second)
								$members	= '<span><h4>Showing '.$totMembers.'  person(s) invited to this group.</h4></span>'.$members;
							elseif($third)
								$members	= '<span><h4>Showing '.$totMembers.'  person(s) who are blocked.</h4></span>'.$members;
							echo $members;
						}
				}//end showmemebers
			elseif($_POST['action']=='rateThisVideo')
				{
					$vblogId	= $_REQUEST['blogid'];//get cid from ajax.js
					$rateValue	= $_REQUEST['rv'];

					//creat condition for record exit query
					$condition = "user_id = '$userId' AND vblog_id='$vblogId' ";
					//filed name to be given while processing record exit function
					$fieldName = "vblog_id";

					//check the username and password exist
					if(IsRecordExist4Add('rate_video_blog',$fieldName,$condition))
						$msg	= "<font color='red'><b>Already rated !</b> </font>";
					else
						{
							$now		= date('Y-m-d h:i:s');
							$fieldList	= "user_id='$userId', vblog_id='$vblogId', rate_value='$rateValue', datestamp='$now'";
							if(!insertRecord('rate_video_blog',$fieldList))
								$msg	= "Sorry ! Technical problem !";
							else
								{
									$blogRs		= getRow("video_blog","rate_value,rate_count","vblog_id='$vblogId'");
									$singleRate	= $blogRs['rate_value'];
									$rateCount	= $blogRs['rate_count'];
									if($singleRate =='' or $singleRate==0)
										{
											$singleRate	= $rateValue;//assing rate value, if it rated first time
											$rateCount	= 1;
										}
									elseif($singleRate>0)
										{
											$singleRate	+= $rateValue;//add ratevalue , if it already rated
											$rateCount++;
										}
									$rateAvg	= round($singleRate/$rateCount,2);
									//execute query for updating rate_value field
									mysql_query("UPDATE video_blog SET rate_value='$singleRate', rate_count='$rateCount',rate_avg='$rateAvg' WHERE vblog_id='$vblogId'");
									$msg	= "<font color='green'><b>Thanks for the rating</b> </font>";
								}
						}

					//get rate value for the given cntent id
					$rateRes	= mysql_query("SELECT COUNT(vblog_id) AS rate_count, SUM(rate_value) AS rate_sum FROM rate_video_blog WHERE vblog_id='$vblogId'");
					$rateRs		= mysql_fetch_object($rateRes);
					$rateSum	= $rateRs->rate_sum;
					$rateImg	= base_url().'application/'.ratingImg($rateRs->rate_count,$rateRs->rate_sum);

					echo '<img src="'.$rateImg.'">('.$rateRs->rate_sum.')<br>'.$msg;
				}//end rate video blog
			//view posted comments
			elseif($_POST['action']=='view_comment')
				{
					$vblogId	= $_POST['blog_id'];
					$query		= 	"SELECT comment_id,comment_thumb_path, comment_video_path,created_on
									FROM vblog_comments
									WHERE vblog_id='$vblogId'";
					//echo $query;
					$res	= mysql_query($query);
					while($rs=mysql_fetch_object($res))
						{
							$playUrl	= base_url()."index.php/vblog/play/comment/".$rs->comment_id;
							$content	.=	'<tr><td>
												<table>
													<tr>
														<td width="200" align="center">
														<a href="'.$playUrl.'" style="text-decoration:none;"><img src="'.$rs->comment_thumb_path.'" border="0"></a>
														</td>
														<td align="right">
															'.$rs->created_on.'
														</td>
													</tr>
												</table>
											</td></tr>';
						}//end while
					if($content=='')
						$content	=	'<table><tr><td>Sorry, No comments posted yet.</td></tr></table>';
					else
						$content	=	'<table>
										<tr><td><table><tr><td width="200" align="center">Comments</td><td align="right">Posted On</td></tr></table></td></tr>
										'.$content.'</table>';
					echo $content;
				}//end view comments
			//post video comment on video blog
			elseif($_POST['action']=='post_comment')
				{
					$vblogId			= $_POST['blog_id'];									//get blog id
					$uploadPath			= base_url()."index.php/vblog/post_comment/".$vblogId;	//return path to store posts
					$filename			= rand(00001,32768);									//generate random number to create file name

					$_SESSION['post_id']	= $filename;//store the random no in session variable

					$recorder	='<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="550" height="400" id="QuickRecorder" align="middle">
								<param name="allowScriptAccess" value="sameDomain" />
								<param name="movie" value="'.$recorderPath.'QuickRecorder.swf?filename='.$filename.'&settingspath='.$red5SettingsPath.'settings.xml&skinpath='.$red5SettingsPath.'skin.xml&uploadpath='.$uploadPath.'" />
								<param name="quality" value="high" />
								<param name="bgcolor" value="#ffffff" />
								<embed src="'.$recorderPath.'QuickRecorder.swf?filename='.$filename.'&settingspath='.$red5SettingsPath.'settings.xml&skinpath='.$red5SettingsPath.'skin.xml&uploadpath='.$uploadPath.'"  width="400" height="340" loop="false" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" scale="exactfit" pluginspage="http://www.macromedia.com/go/getflashplayer" > </embed>
								</object>';
					echo "Post your valuable comments by using capturing video.<br><br>".$recorder;
				}//end post comment
			//remove school indormation on education profile page
			elseif($_POST['action']=='remove_school')
				{
					if($_POST['education']=='education_1')
						$schoolType	= 'education1';
					elseif($_POST['education']=='education_2')
						$schoolType	= 'education2';
					elseif($_POST['education']=='education_3')
						$schoolType	= 'education3';
					elseif($_POST['education']=='education_4')
						$schoolType	= 'education4';
					if(mysql_query("DELETE FROM school_education WHERE user_id='$userId' AND school_type='$schoolType'"))
						{
							$totSchools	= getTotRec("school_edu_id","school_education","user_id='$userId'");
							if($totSchools<1)
								mysql_query("DELETE FROM education_profile WHERE user_id='$userId'");
							echo "true";
						}
					else
						echo "false";
				}//end remove school
			//remove job details from the databse fro the work profile
			elseif($_POST['action']=='remove_job')
				{
					if($_POST['job']=='work_history_1')
						$jobType	= 'job1';
					elseif($_POST['job']=='work_history_2')
						$jobType	= 'job2';
					elseif($_POST['job']=='work_history_3')
						$jobType	= 'job3';
					elseif($_POST['job']=='work_history_4')
						$jobType	= 'job4';
					if(mysql_query("DELETE FROM work_profile WHERE user_id='$userId' AND job_type='$jobType'"))
						echo "true";
					else
						echo "false";

				}
			//leave from the network,for the given user_network id
			elseif($_POST['action']=='leave_network')
				{
					$networkUserId	= $_POST['nw_id'];
					$netRs			= getRow("network_users","network_id","network_user_id='$networkUserId'");
					if(updateRecords("network_users","is_deleted='1'","network_user_id='$networkUserId'"))
						{
							$msg	= '<div><font color="green">You have left the network.</font></div>';
							$logRs	= getRow("mini_feed_settings","mini_feed_status","mini_feed_id=11 AND user_id='$userId'");//log on 'discussion board'
							if($logRs[mini_feed_status]=='1' or $logRs[mini_feed_status]=='')
								{
									$networkName= $this->common->getNetworkName($netRs[network_id]);
									$logContent	= ' left the '.$networkName.' network';
									$logTime	= time();
									$logType	= 'network';
									$fieldList	= "log_content='$logContent', log_type='$logType', datestamp='$logTime',user_id='$userId'";
									//echo $query		= "INSERT INTO mini_feed_log SET ".$fieldList;
									//mysql_query($query);
									insertRecord("mini_feed_log",$fieldList);
								}
						}
					else
						$msg	= '<div><font color="red">Sorry ! Process failed.</font></div>';
					$result	= $msg.$this->common->listNetworks($userId);
					echo $result;

				}//end leave network
			//cancel the network,for the given user_network id
			elseif($_POST['action']=='cancel_network')
				{
					$networkUserId	= $_POST['nw_id'];
					if(updateRecords("network_users","is_deleted='1'","network_user_id='$networkUserId'"))
						$msg	= '<div><font color="green">You request has been successfully cancelled.</font></div>';
					else
						$msg	= '<div><font color="red">Sorry ! Process failed.</font></div>';
					$result	= $msg.$this->common->listNetworks($userId);
					echo $result;

				}//end leave network
			//resend network confirmation
			elseif($_POST['action']=='resend_network_confirmation')
				{
					$networkUserId	= $_POST['nw_id'];
					$confirmKey		= rand(1,999999);
					$netRs			= getRow("network_users","user_email","network_user_id='$networkUserId'");
					$joinEmail		= $netRs['user_email'];

					//send confirmation mail to user
					$networkConfirmUrl	= '<a href="'.base_url().'index.php/networks/confirm/'.$confirmKey.'/'.$userId.'">'.base_url().'index.php/networks/confirm/'.$confirmKey.'/'.$userId.'</a>';
					$this->load->library('email');	//loads email library
					$config['mailtype'] = 'html';
					$this->email->initialize($config);
					$this->email->from($adminEmail, $adminName);
					$this->email->to($joinEmail);

					$tempRs			= getRow("mail_templates","template_subject,template_content","template_id=6");//for nework confirmation(template_id=6)
					$spArray		= array("~~networkName~~"=>$networkName,"~~attachUrl~~"=>$networkConfirmUrl,"~~adminName~~"=>$adminName);
					$mailSubject	= nl2br(replaceSpVariables($spArray,$tempRs['template_subject']));//mail subject
					$mailContent	= nl2br(replaceSpVariables($spArray,$tempRs['template_content']));//mail subject

					$this->email->subject($mailSubject);											//set subject to send mail
					$this->email->message($mailContent);											//set message to send mail

					//send mail
					if($this->email->send())
						{
							if(updateRecords("network_users","confirm_key='$confirmKey'","network_user_id='$networkUserId'"))
								$msg	= '<div><font color="green">We have re-sent a confirmation mail to '.$joinEmail.'</font></div>';
							else
								$msg	= '<div><font color="red">Technical problem.</font></div>';
						}
					else//if error in sending confirmation
						$msg	= '<div><font color="red">Error in sending confirmation mail.</font></div>';
					echo $msg.$this->common->listNetworks($userId);
				}//end resend network confirm
			elseif($_POST['action']=='join_network')
				{
					$networkId	= $_POST['nw_id'];	//get network id
					$joinEmail	= $_POST['email'];	//get network joiner email
					$allowed	= false;
					$query		= "SELECT network_type, network_city, network_name, network_email, network_id FROM networks
											  WHERE network_id='$networkId'";
					$res	= mysql_query($query);
					$rs		= mysql_fetch_object($res);
					$networkType	= $rs->network_type;
					//checks whether the given email match with registered network mail
					$networkEmail	= $rs->network_email;
					$netSplit		= explode("@",$networkEmail);
					$joinSplit		= explode("@",$joinEmail);
					//echo "join:".$joinSplit[1]."net:".$netSplit[1];
					if($joinSplit[1]==$netSplit[1])
						{
							$allowed	= true;
							if($rs->network_type=='region')
								$networkName	= $rs->network_city;
							else
								$networkName	= $rs->network_name;
						}

					if($allowed)
						{
							if($networkType=='region')
								{
									$condition	= "user_id='$userId' AND network_type='region' AND user_status='approved' AND is_deleted='0'";
									$totReg 	= getTotRec("network_user_id","network_users",$condition);
								}
							$confirmKey	= rand(1,999999);
							$fields		= "network_id='$networkId',user_id='$userId',user_email='$joinEmail',network_type='$networkType',datestamp='$now',confirm_key='$confirmKey'";
							if(IsRecordExist4Add('network_users','network_user_id',"user_id='$userId' AND network_id='$networkId'"))
								echo '|<font color="red">You have already joined '.$networkName.' network.</font>';
							else
								{	//send confirmation mail to user
									$networkConfirmUrl	= '<a href="'.base_url().'index.php/networks/confirm/'.$confirmKey.'/'.$userId.'">'.base_url().'index.php/networks/confirm/'.$confirmKey.'/'.$userId.'</a>';
									$this->load->library('email');	//loads email library
									$config['mailtype'] = 'html';
									$this->email->initialize($config);
									$this->email->from($adminEmail, $adminName);
									$this->email->to($joinEmail);
									$tempRs			= getRow("mail_templates","template_subject,template_content","template_id=6");//for nework confirmation(template_id=6)
									$spArray		= array("~~networkName~~"=>$networkName,"~~attachUrl~~"=>$networkConfirmUrl,"~~adminName~~"=>$adminName);
									$mailSubject	= nl2br(replaceSpVariables($spArray,$tempRs['template_subject']));//mail subject
									$mailContent	= nl2br(replaceSpVariables($spArray,$tempRs['template_content']));//mail subject
									$this->email->subject($mailSubject);											//set subject to send mail
									$this->email->message($mailContent);											//set message to send mail
									//send mail
									if($this->email->send())
										{
											if($networkType=='region' and $totReg>0)
												{
													if(updateRecords('network_users',$fields,$condition))
														echo	'1|<font color="green">We have sent a confirmation mail to '.$joinEmail.'</font>';
													else
														echo '|<font color="red">Sorry! Technical problem.</font>';
												}
											else
												{
													if(insertRecord('network_users',$fields))
														echo	'1|<font color="green">We have sent a confirmation mail to '.$joinEmail.'</font>';
													else
														echo '|<font color="red">Sorry! Technical problem.</font>';
												}
										}
									else//if error in sending confirmation
										echo '<font color="red">Error in sending confirmation mail.</font>';
								}
						}//end allowed
					else
						echo '|<font color="red">Sorry!Your email doesnt match with this network.</font>';
				}//end join network
			elseif($_POST['action']=='invite_friend')
				{
					$toAddress	= $_POST['to_mail'];
					$message	= $_POST['msg'];
					if(trim($message)!='')
						$message	= "The sender\'s private message<br>".$message;

					if($toAddress=='')
						echo '';
					else
					{
						$emailList	= explode(",",$toAddress);
						$result		= $this->getInviteResult($emailList);
						$sendList	= $this->getInviteList($emailList);
						if($sendList!='')
							{
								$friendsUrl	= '<a href="'.base_url().'index.php/friends/request">'.base_url().'index.php/friends/request</a>';
								$this->load->library('email');	//loads email library
								$config['mailtype'] = 'html';
								$this->email->initialize($config);

								$usrRs		= getRow("users","username,email","user_id='$userId'");
								$senderName	= $usrRs['username'];
								$senderEmail= $usrRs['email'];

								$this->email->from($adminEmail,$adminName);
								$this->email->to($sendList);

								$tempRs			= getRow("mail_templates","template_subject,template_content","template_id=2");//for account confirmation(template_id=1)
								$spArray		= array("~~privateMessage~~"=>$message,"~~senderName~~"=>$senderName,"~~siteName~~"=>$siteName,"~~attachUrl~~"=>$friendsUrl,"~~adminName~~"=>$adminName);
								$mailSubject	= nl2br(replaceSpVariables($spArray,$tempRs['template_subject']));//mail subject
								$mailContent	= nl2br(replaceSpVariables($spArray,$tempRs['template_content']));//mail subject

								$this->email->subject($mailSubject);											//set subject to send mail
								$this->email->message($mailContent);											//set message to send mail
								$notifiyRs	= getRow("users_status","notification_status","notification_id=2 AND user_id=".$userId);
								if($notifiyRs[notification_status]=='1' or $notifiyRs[notification_status]=='')
									$this->email->send();
								else
									continue;

								$emailSplit	= explode(",",$sendList);
								//store all email friends in database
								for($i=0;$i<count($emailSplit);$i++)
									{
										//echo $emailSplit[$i];
										//checks whether the receiver email is already member of this site
										//if so store his user id, else store 0 as his user id and also store his email id
										$frndRs		= getRow("users","user_id","email='".$emailSplit[$i]."'");
										if($frndRs['user_id']!='')
											$friendId	= $frndRs['user_id'];
										else
											$friendId	= 0;
										insertRecord("friends_list","user_id='$userId',friend_mail='".$emailSplit[$i]."', friend_id='$friendId', datestamp='$now', invite_message='$message'");
									}
									//}//end mail send
							}//end sendList
						echo $result;
					}//end else
				}//end invite friends
			elseif($_POST['action']=='add_friend')
				{
					$captchaResponse		= $_POST['captchaResponse'];
					$captchaChallengeCode	= $_POST['captchaChallengeCode'];
					$friendId				= $_POST['friendId'];
					$message				= $_POST['msg'];
					if(trim($message)!='')
						$message	= "The sender\'s private message<br>".$message;

					$alreadyExist	= getTotRec("friends_list_id", "friends_list", "user_id='$userId' AND friend_id='$friendId'");
					if($captchaResponse!=$captchaChallengeCode)
						echo 'security';
					elseif($alreadyExist>0)
						echo '<table width="60%" align="center" style="border:10px solid #3399CC;">
								<tr><td><font color="red">You already added this friend as your friend.</font></td></tr>
								<tr><td align="right">
									<input type="button" class="inputbutton" onclick="javascript:document.location=\''.base_url().'index.php/home\';" id="home" name="home" value="Home" />
									<input type="button" class="inputbutton" onclick="history.go(-1);return true;" id="back" name="back" value="Back" />
									</td>
								</tr>
							</table>';
					else
						{
							$friendRs	= getRow("users","username,email","user_id='$friendId'");

							$friendsUrl	= '<a href="'.base_url().'index.php/friends">'.base_url().'index.php/friends</a>';
							$this->load->library('email');	//loads email library
							$config['mailtype'] = 'html';
							$this->email->initialize($config);

							$usrRs		= getRow("users","username,email","user_id='$userId'");
							$senderName	= $usrRs['username'];
							$senderEmail= $usrRs['email'];

							$this->email->from($adminEmail, $adminName);
							$this->email->to($friendRs['email']);

							$tempRs			= getRow("mail_templates","template_subject,template_content","template_id=5");//for Adds me as a friend(template_id=5)
							$spArray		= array("~~senderName~~"=>$senderName,"~~privateMessage~~"=>$message,"~~siteName~~"=>$siteName,"~~attachUrl~~"=>$friendsUrl,"~~adminName~~"=>$adminName);
							$mailSubject	= nl2br(replaceSpVariables($spArray,$tempRs['template_subject']));//mail subject
							$mailContent	= nl2br(replaceSpVariables($spArray,$tempRs['template_content']));//mail subject

							$this->email->subject($mailSubject);											//set subject to send mail
							$this->email->message($mailContent);											//set message to send mail
							//send mail
							if($this->email->send())
								{
									//store email friends in database
									if(insertRecord("friends_list","user_id='$userId', friend_mail='".$friendRs['email']."', friend_id='$friendId', datestamp='$now'"))
										echo "success";
									else
										echo '<table width="60%" align="center" style="border:10px solid #3399CC;">
												<tr><td><font color="red">Error:Sorry error in add friend details to database.</font></td></tr>
											</table>';
								}//end email send
							else
								echo '<table width="60%" align="center" style="border:10px solid #3399CC;">
										<tr><td><font color="red">Error:Sorry error in sending mail.</font></td></tr>
									</table>';
						}//end validation
				}//end add friend
			elseif($_POST['action']=='eventCategory')
				{

					$category	= $_POST['category'];
					if($category !='0')
						{
							//create group sub category select box
							$res				= mysql_query("SELECT event_sub_category_id,event_sub_category_name FROM events_sub_category WHERE event_sub_category_status='Yes' and event_category_id='$category'");
							$eventSubCategory	= array();
							$eventSubCategory[0]= "Select Type:";
							if($res)
							while($rs=mysql_fetch_object($res))
								$eventSubCategory[$rs->event_sub_category_id]	= $rs->event_sub_category_name;
							$selectSubCategory	= selectBox('event_sub_category', $eventSubCategory);
							echo $selectSubCategory;
						}
					//return "hi";
				}//end eventCategory
			elseif($_POST['action']=='eventHostChange')
				{
					//create event host group select box
					$res				= mysql_query("SELECT group_name FROM groups WHERE user_id='$userId' AND group_status='ok'");
					$eventHostGroup		= array();
					$eventHostGroup[0]	= "One of my groups:";
					if($res)
					while($rs=mysql_fetch_object($res))
						$eventHostGroup[$rs->group_name]	= $rs->group_name;
					echo selectBox('event_host_group', $eventHostGroup, '', 'onChange="eventHostGroupSelect(\'event_host\');"');
					//return "hi";
				}//end eventHostChange
			//show invitation form for events
			elseif($_POST['action']=='showInviteFormEvent')
				{
					$friendId	= $_POST['fid'];					//get friend id from ajax.js
					$eventId	= $_POST['checkForId'];					//get event id from ajax.js
					$currentPage= base_url();

					$usrRs		= getRow('users',"username","user_id='$friendId'");

					$content	= '<div id="invitation_wrapper">
									<table border="1" rules="none">
									<tr>
										<td>Add people using the options on the right, then send them invitations. </td>
									</tr>
									<tr>
										<td><a href="#">'.$usrRs['username'].'</a></td>
										<td><a href="javascript: void(0)" onclick="removeInvitation(\''.$eventId.'\',\''.$currentPage.'\',\'event\')">Remove</a></td>
									</tr>
									<tr>
										<td>
										<h3>Message to Guests (optional):</h3>
										<form method="post" name="frm_invite_pending" id="frm_invite_pending">
											<textarea id="personal" name="personal" class="textarea" rows="4"></textarea><br>
											<span id="personal_msg" style="display: none;"><small>Keep it under 255 characters.</small></span>
											<div id="invite_button">
											<input class="inputsubmit" value="Send Invitations" type="button" onclick="sendInvitation(\''.$friendId.'\',\''.$eventId.'\',\''.$currentPage.'\',\'event\');">
											</div>
										</form>
										</td>
									</tr>
									</table>
								</div>';
					echo $content;
				}//end showInviteForm
			//show event creation third form,
			//to list all guests and attending, not attending guests
			elseif($_POST['action']=='listGuest')
				{
					$eventId	= $_POST['eid'];		//get event id from ajax.js
					$listType	= $_POST['listType'];	//get list type,(all,attending,maybeattending,notattending,blocked,notreplied)
					$guest		= '';
					if($_POST['listType']=='all')	//query for show all guests only
						$query	= "SELECT receiver_id FROM events_invitation
											WHERE event_id='$eventId' AND invitation_status<>'sent' AND invitation_status<>'blocked'";
					elseif($_POST['listType']=='notreplied')	//query for not replied guest
						$query	= "SELECT receiver_id FROM events_invitation WHERE event_id='$eventId' AND invitation_status='sent'";
					else
						$query	= "SELECT receiver_id FROM events_invitation WHERE event_id='$eventId' AND invitation_status='$listType'";
					$res		= mysql_query($query);
					$totGuest	= 0;
					if($res)
						while($rs=mysql_fetch_object($res))
							{
								$totGuest++;
								$usrRs	= getRow('users',"username","user_id='$rs->receiver_id'");
								$guest	.= '<div class="name_row" id="guest_'.$rs->receiver_id.'">
												<a href="#">'.$usrRs['username'].'</a>
												</div>';
							}//END WHILE

					//get event owner name, if the action is for all guests
					if($_POST['listType']=='all')
						{
							$totGuest++;
							$rs		= getRow('events',"user_id","event_id='$eventId'");
							$usrRs	= getRow('users',"username","user_id='$rs[user_id]'");
							$guest 	.= '<div class="name_row" id="guest_'.$rs['user_id'].'">
											<a href="#">'.$usrRs['username'].'</a>
											</div>';
						}

					if($guest =='')
						echo "<h4>Your search returned no results.</h4>";
					else
						{
							if($_POST['listType']=='all')
								$guest	= '<span><h4>Showing '.$totGuest.' guest(s) for this event.</h4></span>'.$guest;
							elseif($_POST['listType']=='attending')
								$guest	= '<span><h4>Showing '.$totGuest.'  person(s) who is attending.</h4></span>'.$guest;
							elseif($_POST['listType']=='notattending')
								$guest	= '<span><h4>Showing '.$totGuest.'  person(s) who is not attending.</h4></span>'.$guest;
							elseif($_POST['listType']=='maybeattending')
								$guest	= '<span><h4>Showing '.$totGuest.'  person(s) who is maybe attending.</h4></span>'.$guest;
							elseif($_POST['listType']=='notreplied')
								$guest	= '<span><h4>Showing '.$totGuest.'  person(s) who is not yet replied.</h4></span>'.$guest;
							elseif($_POST['listType']=='blocked')
								$guest	= '<span><h4>Showing '.$totGuest.'  person(s) who is blocked.</h4></span>'.$guest;
							echo $guest;
						}
				}//end listGuest
			//to show the interface to confirm the remove event
			elseif($_POST['action']=='removeEvent')
				{
					$eventId	= $_POST['eid'];
					$eventType	= $_POST['eventType'];
					$eventRs	= getRow("events","event_name","event_id='$eventId'");
					$closeId	= 'ajax_content_'.$eventId;
					//$submitUrl	= base_url().'index.php/events/remove/'.$eventId;
					$content	= '	<table>
										<tr>
											<td>
												<form name="confirm_dialog" id="confirm_dialog" method="post" >
												<input type="hidden" name="event_id" id="event_id" value="{$eventId}">
												<strong>Confirm Delete</strong><br>
												Are you sure you want to remove your membership from '.ucwords($eventRs[event_name]).'?<br><br>
												<input type="button" name="remove_event" id="remove_event" value="Remove" onclick="doRemoveEvent(\''.$eventId.'\',\''.base_url().'\',\''.$eventType.'\');">
												<input type="button" onClick="close_div(\''.$closeId.'\');" name="cancel_remove_event" id="cancel_remove_event" value="Cancel">
												</form>
											</td>
										</tr>
									</table>';
					echo $content;

				}//end removeEvent
			//to show the interface to confirm the remove event
			elseif($_POST['action']=='cancelEvent')
				{
					$eventId	= $_POST['eid'];
					$eventType	= $_POST['eventType'];
					$eventRs	= getRow("events","event_name","event_id='$eventId'");
					$closeId	= 'ajax_content_'.$eventId;
					$cancelCommentId	= 'cancel_comment_'.$eventId;
					//$submitUrl	= base_url().'index.php/events/remove/'.$eventId;
					$content	= '	<table>
										<tr>
											<td>
												<form name="confirm_dialog" id="confirm_dialog" method="post" >
												<input type="hidden" name="event_id" id="event_id" value="{$eventId}">
												<strong>Cancel Event?</strong><br>
												We will email everyone who was invited that this event has been cancelled. You may add a note below if you want:<br><br>
												<textarea cols="30" rows="3" id="'.$cancelCommentId.'"></textarea><br><br>
												Are you sure you want to cancel this event? This cannot be undone.<br><br>
												<input type="button" name="remove_event" id="remove_event" value="Cancel Event" onclick="doCancelEvent(\''.$eventId.'\',\''.base_url().'\',\''.$eventType.'\',\''.$cancelCommentId.'\');">
												<input type="button" onClick="close_div(\''.$closeId.'\');" name="cancel_remove_event" id="cancel_remove_event" value="Nevermind">
												</form>
											</td>
										</tr>
									</table>';
					echo $content;

				}//end removeEvent
			//to show the rsvp status to change as guest likes
			elseif($_POST['action']=='changeRsvp')
				{
					$who		= $_POST['who'];
					$eventId	= $_POST['eid'];
					$attCheck		= '';
					$notAttCheck	= '';
					$mayAttCheck	= '';
					if($who=='admin')
						{
							$rs			= getRow("events","admin_rsvp_status","event_id='$eventId'");
							$rsvpStatus	= $rs['admin_rsvp_status'];
							$rsvpId		= 'admin_rsvp_status';
						}
					else
						{
							$rs			= getRow("events_invitation","event_invitation_id,invitation_status","receiver_id='$userId' AND event_id='$eventId'");
							$rsvpStatus	= $rs['invitation_status'];
							$rsvpId		= 'user_rsvp_status';
						}
					if($rsvpStatus=='attending')
						$attCheck	= 'checked';
					elseif($rsvpStatus=='notattending')
						$notAttCheck	= 'checked';
					elseif($rsvpStatus=='maybeattending')
						$mayAttCheck	= 'checked';
					$ajaxDivId		= 'ajax_content_'.$eventId;
					$rsvpContent	=	'<form name="rsvp_form" id="rsvp_form"><table>
											<tr><td colspan="3">Select the RSVP Status</td></tr>
											<tr>
												<td><input type="radio" name="'.$rsvpId.'" id="'.$rsvpId.'" value="attending" '.$attCheck.'>Attending</td>
												<td><input type="radio" name="'.$rsvpId.'" id="'.$rsvpId.'" value="notattending" '.$notAttCheck.'>Not Attending</td>
												<td><input type="radio" name="'.$rsvpId.'" id="'.$rsvpId.'" value="maybeattending" '.$mayAttCheck.'>May be Attending</td>
											</tr>
											<tr><td colspan="3" align="right">
												<input type="button" value="Ok" onclick="doRSVP(\''.$eventId.'\',\''.$rsvpId.'\',\''.base_url().'\');">
												<input type="button" value="Cancel" onclick="close_div(\''.$ajaxDivId.'\');">
												</td>
											</tr>
										</table></form>';

					echo $rsvpContent;
				}//end changeRsvp
			//to show the rsvp status to change as guest likes
			elseif($_POST['action']=='doRsvp')
				{
					$eventId	= $_POST['eid'];
					$rsvpStatus	= $_POST['rsvpStatus'];
					$rsvpId		= $_POST['rsvpId'];
					if($rsvpId=='admin_rsvp_status')
						updateRecords("events","admin_rsvp_status='$rsvpStatus'","user_id='$userId' AND event_id='$eventId'");
					else
						updateRecords("events_invitation","invitation_status='$rsvpStatus'","receiver_id='$userId' AND event_id='$eventId'");
					if($rsvpStatus		=='attending')
						echo 'Attending';
					elseif($rsvpStatus	=='notattending')
						echo 'Not Attending';
					elseif($rsvpStatus	=='maybeattending')
						echo 'Maybe Attending';
				}//end doRsvp
			//to show the rsvp status to change as guest likes
			elseif($_POST['action']=='doRemoveEvent')
				{
					$eventId	= $_POST['eid'];
					$eventType	= $_POST['eventType'];
					updateRecords("events_invitation","invitation_status='blocked'","receiver_id='$userId' AND event_id='$eventId'");
					$eventsArray	=  $this->common->getEventList($eventType,$userId);
					$eventList		=  $this->common->listEvents($eventsArray,$eventType);
					echo $eventList;
				}//end doRemoveEvent
			elseif($_POST['action']=='doCancelEvent')
				{
					$eventId		= $_POST['eid'];
					$eventType		= $_POST['eventType'];
					$cancelComment	= $_POST['cancelComment'];
					$msg			= 'Event has been successfully cancelled.';	//default result message

					if($cancelComment!='')
						$userMsg	= '<br>The owner sent the following message for you:</br><br>'.$cancelComment;
					//send confirmation mail of cancel event to user
					$eventRs	= getRow("events","event_name, event_start_time, event_end_time,event_location","event_id='$eventId'");
					$startTime	= $this->common->getEventTime($eventRs[event_start_time]);
					$endTime	= $this->common->getEventTime($eventRs[event_end_time]);
					$location	= ucwords($eventRs[event_location]);
					if(date("Y",$startTime)!=date("Y"))	//if event year not equal to current year, show the year
						{
							$eventStartTime	= date("l, Y F d h:i a",$startTime);
							$eventEndTime	= date("l, Y F d h:i a",$endTime);
						}
					elseif(date("Y",$endTime)==date("Y"))//if event year equal to current year, no need to show the year
						{
							$eventStartTime	= date("l, F h:i a",$startTime);
							$eventEndTime	= date("l, F h:i a",$endTime);
						}

					$this->load->library('email');	//loads email library
					$config['mailtype'] = 'html';
					$this->email->initialize($config);
					$this->email->from($adminEmail, $adminName);
					if(mysql_query("DELETE FROM events WHERE event_id='$eventId' AND user_id='$userId'"))
						{
							$res	= mysql_query("SELECT receiver_id FROM events_invitation WHERE sender_id='$userId' AND event_id='$eventId'");
							if($res)
								{
									while($rs=mysql_fetch_object($res))
										{
											$usrRs	= getRow("users","username,email","user_id=".$rs->receiver_id);
											$this->email->to($usrRs[email]);


											$tempRs			= getRow("mail_templates","template_subject,template_content","template_id=7");//for event cancellation(template_id=7)
											$spArray		= array("~~siteName~~"=>$siteTitle,"~~receiverName~~"=>ucwords($usrRs[username]),"~~eventName~~"=>ucwords($eventRs['event_name']),"~~eventStartTime~~"=>$eventStartTime,"~~eventEndTime~~"=>$eventEndTime,"~~eventLocation~~"=>$location,"~~privateMessage~~"=>$userMsg,"~~adminName~~"=>$adminName);
											$mailSubject	= nl2br(replaceSpVariables($spArray,$tempRs['template_subject']));//mail subject
											$mailContent	= nl2br(replaceSpVariables($spArray,$tempRs['template_content']));//mail subject

											$this->email->subject($mailSubject);											//set subject to send mail
											$this->email->message($mailContent);											//set message to send mail
											//send mail
											$this->email->send();
										}//end while
									if(!mysql_query("DELETE FROM events_invitation WHERE sender_id='$userId' AND event_id='$eventId'"))
										$msg	= mysql_error();
								}
							else
								$msg	= 'No guests available under '.ucwords($eventRs['event_name']).' event.';
						}
					else
						$msg	= 'Technical problem: '.mysql_error();

					$eventsArray	=  $this->common->getEventList($eventType,$userId);
					$eventList		=  $this->common->listEvents($eventsArray,$eventType);
					$result			= '<table width="100%" class="splborder">
										<tr>
											<td height="41" bordercolor="#ec8a00" bgcolor="#feffcf"><span class="blktitle">'.$msg.'</span><br />
											 </td>
										</tr>
										</table>'.$eventList;
					echo $result;
				}//end doRemoveEvent
			//to show the rsvp status to change as guest likes
			elseif($_POST['action']=='doRsvpMain')
				{
					$eventId	= $_POST['eid'];
					$rsvpStatus	= $_POST['rsvpStatus'];
					$rsvpId		= $_POST['rsvpId'];
					if($rsvpId=='admin_rsvp_status')
						{
							updateRecords("events","admin_rsvp_status='$rsvpStatus'","user_id='$userId' AND event_id='$eventId'");
							$isAdmin	= true;
						}
					else
						{
							updateRecords("events_invitation","invitation_status='$rsvpStatus'","receiver_id='$userId' AND event_id='$eventId'");
							$isAdmin	= false;
						}
					$content	= $this->common->rsvpContent($eventId,$userId,$isAdmin);
					echo $content;
				}//end doRsvpMain
			//send message to friends or groups or guests of event
			elseif($_POST['action']=='sendMessage')
				{
					$sendType	= $_POST['actionType'];		//get send type, such as to event, or to friend
					$toname		= $_POST['toname'];			//reciever name(eventname,friend name)
					$subject	= $_POST['subject'];		//message subject
					$message	= $_POST['content'];		//message original content
					if($toname=='')//do validation
						$msg	= "Please give to(receiver) name !|no";
					elseif($subject=='')
						$msg	= "Please give the subject !|no";
					elseif($message=='')
						$msg	= "Please give the message !|no";
					else
						if($sendType=='event')//check whether message for event
							{
								$eventId		= $_POST['actionId'];		//get event id from ajax.js
								$eventStatus	= $_POST['actionStatus'];	//get event attendance status from ajax.js
								$msg			= $this->messageEvent($subject,$message,$eventId,$eventStatus,$toname);
							}
						elseif($sendType=='group')//check whether message for group
							{
								$groupId		= $_POST['actionId'];		//get event id from ajax.js
								$msg			= $this->messageGroup($subject,$message,$groupId,$toname);
							}
						elseif($sendType=='single_user')//check whether message for group
							{
								$userID		= $_POST['actionId'];		//get event id from ajax.js
								if($this->store2Inbox($userID,$subject,$message))//store this user details to inbox table
									{
										$msg	= "Successfully sent|yes";
										$this->store2Sent($userID,$subject,$message,'friend');//store user details to sent(message) table
									}
								else
									$msg		= "Error in sending message!|no";
							}
						else//check whether message for friend
							{
								$usrRs		= getRow('users',"user_id,email","username='$toname'");
								$cur_usrRs	= getRow("users","username,email","user_id='$userId'");
								$userName	= $cur_usrRs['username'];											//get current user name
								$userEmail	= $cur_usrRs['email'];												//get current user name

								$this->load->library('email');												//load email library
								$config['mailtype'] = 'html';
								$this->email->initialize($config);
								$this->email->to($usrRs[email],$toname);
								$this->email->from($adminEmail, $adminName);
								$mailSubject	= $userName.' sent you a message on '.$siteTitle;
								base_url().'index.php/messages/inbox';
								$this->email->subject($mailSubject);											//set subject to send mail
								if($usrRs['email'] =='')//if no user for this username
									$msg	= "User not found !|no";
								else
									if($this->store2Inbox($usrRs[user_id],$subject,$message))//store this user details to inbox table
										{
											$notifiyRs	= getRow("users_status","notification_status","notification_id=1 AND user_id=".$usrRs[user_id]);
											if($notifiyRs[notification_status]=='1' or $notifiyRs[notification_status]=='')
												{
													$getMsgId	= getRow('inbox_messages',"inbox_message_id","from_id='".$userId."' and  user_id=".$usrRs[user_id]." and  subject='".$subject."' and  message='".$message."'");
													$mailMessage	= $cur_usrRs[username].' sent you a message.<br><br>To read this message, follow the link below<br><br>'.base_url().'index.php/messages/readMessage/i_'.$getMsgId[inbox_message_id];
													$this->email->message($mailMessage);											//set message to send mail
													//send mail
													$this->email->send();
													$msg	= "Successfully sent|yes";
													$this->store2Sent($usrRs[user_id],$subject,$message,'friend');//store user details to sent(message) table
												}
											else
												{
													$this->store2Sent($usrRs[user_id],$subject,$message,'friend');//store user details to sent(message) table
													$msg		= "Successfully sent|yes";
												}//if
										}//if
							}//else
					echo $msg;
				}//end sendMessage()

			//remove membership from event
			elseif($_POST['action']=='removeEventHome')
				{
					$eventId	= $_POST['eid'];
					$isAdmin	= getTotRec("event_id","events","event_id='$eventId' AND user_id='$userId'");
					if($isAdmin)
						{
							updateRecords("events","event_status='blocked'","user_id='$userId' AND event_id='$eventId'");
							updateRecords("events_invitation","invitation_status='blocked'","event_id='$eventId'");
						}
					else
						updateRecords("events_invitation","invitation_status='blocked'","receiver_id='$userId' AND event_id='$eventId'");
					echo true;
				}
			elseif($_POST['action']=='changeUserStatus')
				{
					$uid		= $_POST['uid'];
					$dowhat		= $_POST['dowhat'];
					$userName	= $this->common->getUsername($uid);
					if($dowhat=='store')
						{
							$statusId	= $_POST['status'];
							if($statusId=='1')
								{
									updateRecords("users","screen_status_id='', screen_status_changed_date=''","user_id=".$uid);
									$status	= '<a href="javascript: void(0);" class="BlueLink" onclick="show_div(\'screen_status_change_div\');close_div(\'screen_status_div\');">Update your status...</a>';

								}
							else
								{
									updateRecords("users","screen_status_id='$statusId', screen_status_changed_date='$now'","user_id=".$uid);
									$rs			= getRow("screen_status","status","screen_status_id=".$statusId);
									$status		= $userName .' is '.$rs['status'].' <a href="javascript: void(0);" class="BlueLink"  onclick="show_div(\'screen_status_change_div\');close_div(\'screen_status_div\');"><small>edit</small></a>';

									$wallLog	= getRow("mini_feed_settings","mini_feed_status","mini_feed_id=13 AND user_id='$uid'");
									if($wallLog[mini_feed_status]=='1' or $wallLog[mini_feed_status]=='')
										{
											$statusId		= getRow("users"," screen_status_id","user_id='$uid'");
											$statusType		= getRow("screen_status","status","screen_status_id=".$statusId[screen_status_id]);
											$logContent	= "is ".$statusType[status];
											$logTime	= time();
											$logType	= 'profile';
											$fieldList	= "log_content='$logContent', log_type='$logType', datestamp='$logTime',user_id='$uid'";
											insertRecord("mini_feed_log",$fieldList);
										}
								}
						}
					else
						{
							updateRecords("users","screen_status_id='', screen_status_changed_date=''","user_id=".$uid);
							$status	= '<a href="javascript: void(0);" class="BlueLink" onclick="show_div(\'screen_status_change_div\');close_div(\'screen_status_div\');">Update your status...</a>';
						}
					echo $status;
				}//end changeUserStatus
			//store posted content for the given user on profile page
			elseif($_POST['action']=='postToProfile')
				{
					$postedBy	= $_POST['by'];
					$postedTo	= $_POST['to'];
					$postedValue= $_POST['content'];
					$type		= $_POST['type'];//post for see all or for profile
					if(trim($postedValue)=='')
						echo "Please give the post content...";
					else
						{
							$dateStamp	= time();
							$fieldList	= "posted_by='$postedBy', posted_to='$postedTo',wall_post='$postedValue',datestamp='$dateStamp'";
							if(insertRecord("profile_wall",$fieldList))
								{
									$siteVars		= $this->common->getSiteVars();
									$postsOnProfile	= $siteVars['albumsOnProfile'];//toptal topics should appear on events home page
									if($type=='all')
										$query	= "SELECT wall_post,posted_by,datestamp FROM profile_wall WHERE posted_to='$postedTo' ORDER BY datestamp DESC ";
									else
										$query	= "SELECT wall_post,posted_by,datestamp FROM profile_wall WHERE posted_to='$postedTo' ORDER BY datestamp DESC LIMIT 0,".$postsOnProfile;
									$res	= mysql_query($query);
									if($res)
										{
											$totRec	= mysql_num_rows($res);
											while($rs=mysql_fetch_object($res))
												{
													$avatar		= $this->common->getAvatar($rs->posted_by);
													$postTime	= date("h:i a",$rs->datestamp);
													$postedUser	= $this->common->getUsername($rs->posted_by);
													$profileUrl	= base_url().'index.php/profile/user/'.$rs->posted_by;
													$content	.= '<tr><td>
																		<table><tr>
																				<td valign="top"><a href="'.$profileUrl.'" style="text-decoration:none;"><img src="'.$avatar.'" border="0" width="50"></td>
																				<td valign="top">'.$postedUser.' wrote<br>at'.$postTime.'<br>'.$rs->wall_post.'</td>
																				</tr>
																		</table></td></tr>';
												}//end while
										}
									$interface	= $this->postWall2Profile($postedBy,$postedTo,'successfully posted',$type);
									if($type=='all')
										$seeAllUrl	= '';
									else
										$seeAllUrl	= '<a href="'.base_url().'index.php/profile/allpost/'.$postedBy.'" class="BlueLink">See All</a>';
									echo $content	= '<table width="100%">
														<tr><td><span class="grytxt" id="total_posts_on_profile">Displaying '.$totRec. ' posts</span></td>
															<td align="right">'.$seeAllUrl.'</tr>
														<tr><td>'.$interface.'<table width="100%">'.$content.'</table>
														</td>
														</tr>
														</table>';
									//echo "Successfully posted...";
									$wallLog	= getRow("mini_feed_settings","mini_feed_status","mini_feed_id=2 AND user_id='$postedBy'");
									if($wallLog[mini_feed_status]=='1' or $wallLog[mini_feed_status]=='')
										{
											$logContent	= "posted a wall";
											$logTime	= time();
											$logType	= 'profile';
											$fieldList	= "log_content='$logContent', log_type='$logType', datestamp='$logTime',user_id='$postedBy'";
											//echo $query		= "INSERT INTO mini_feed_log SET ".$fieldList;
											//mysql_query($query);
											insertRecord("mini_feed_log",$fieldList);
										}
								}
							else
								echo "Sorry! Your message has not been posted.";
						}
				}//end postToProfile

			elseif($_POST['action']=='postToNetwork')
				{
					$postedBy	= $_POST['by'];
					$postedTo	= $_POST['to'];
					$postedValue= $_POST['content'];
					if(trim($postedValue)=='')
						echo "Please give the post content...";
					else
						{
							$dateStamp	= time();
							$fieldList	= "posted_by='$postedBy', posted_to='$postedTo',wall_post='$postedValue',datestamp='$dateStamp'";
							if(insertRecord("network_wall",$fieldList))
								{
									$siteVars		= $this->common->getSiteVars();
									//$postsOnNetwork	= $siteVars['albumsOnProfile'];//toptal topics should appear on events home page
									$query	= "SELECT wall_post,posted_by,datestamp,network_wall_id FROM network_wall WHERE posted_to='$postedTo' ORDER BY datestamp ";
									$res	= mysql_query($query);
									if($res)
										{
											$totRec	= mysql_num_rows($res);
											while($rs=mysql_fetch_object($res))
												{
													$avatar		= $this->common->getAvatar($rs->posted_by);
													$postTime	= date("h:i a",$rs->datestamp);
													$postedUser	= $this->common->getUsername($rs->posted_by);
													$profileUrl	= base_url().'index.php/profile/user/'.$rs->posted_by;
													$reportUrl	= base_url().'index.php/networks/reportNetworkUser/'.$rs->posted_by;
													$sendUrl	= base_url()."index.php/messages/compose/".$rs->posted_by;
													$deleteDivId= 'delete_wall_'.$rs->network_wall_id;

													if($rs->posted_by==$userId)
															$link	= '<br><a href="'.base_url().'index.php/networks/myNetworkPost/'.$postedTo.'" class="BlueLink">Write on my wall</a> - <a href="#" onclick="show_div(\''.$deleteDivId.'\');" class="BlueLink">Delete</a>';
													else
															$link	= '<br><a href="'.$sendUrl.'" class="Bluelink">Message</a> - <a href="'.$reportUrl.'" class="Bluelink">Report</a>';
													$content	.= '<tr><td><div style="display:none; z-index:0; width:300px; position:absolute; border:1px dashed #669966; background-color:#ccffcc; margin:0px 50px 0px 50px; text-align:left;" id="'.$deleteDivId.'">
											<form name="deleteWall" id="deleteWall" method="post" action="'.base_url().'index.php/networks/deleteWall">
											<input type="hidden" name="confirm_delete" id="confirm_delete" value="1">
											<input type="hidden" name="network_wall_id" id="network_wall_id" value="'.$rs->network_wall_id.'">
											<input type="hidden" name="networkId" id="networkId" value="'.$postedTo.'">
											<table width="100%"><tr><td><strong>Delete Post?</strong></td></tr>
											<tr><td>Are you sure you want to delete it?</td></tr>
											<tr><td align="right"><input type="submit" value="Delete"><input type="button" value="Cancel" onclick="close_div(\''.$deleteDivId.'\')"></td></tr>
											</table>
											</form>
										</div>
																		<table><tr>
																				<td valign="top"><a href="'.$profileUrl.'" style="text-decoration:none;"><img src="'.$avatar.'" border="0" width="50"></td>
																				<td valign="top">'.$postedUser.' wrote<br>at'.$postTime.'<br>'.$rs->wall_post.$link.'</td>
																				</tr>
																		</table></td></tr>';
												}//end while
										}
									$interface	= $this->postWall2Network($postedBy,$postedTo,'successfully posted');
									$seeAllUrl	= base_url().'index.php/networks/allNetworkPost/'.$postedBy;
									echo $content	= '<table width="100%">
														<tr><td><span class="grytxt" id="total_posts_on_profile">Displaying '.$totRec. ' posts</span></td>
															<td align="right"><a href="'.$seeAllUrl.'" class="BlueLink">See All</a></tr>
														<tr><td>'.$interface.'<table width="100%">'.$content.'</table>
														</td>
														</tr>
														</table>';
									//echo "Successfully posted...";
									$wallLog	= getRow("mini_feed_settings","mini_feed_status","mini_feed_id=2 AND user_id='$postedBy'");
									if($wallLog[mini_feed_status]=='1' or $wallLog[mini_feed_status]=='')
										{
											$logContent	= "posted a wall";
											$logTime	= time();
											$logType	= 'network';
											$fieldList	= "log_content='$logContent', log_type='$logType', datestamp='$logTime',user_id='$postedBy'";
											//echo $query		= "INSERT INTO mini_feed_log SET ".$fieldList;
											//mysql_query($query);
											insertRecord("mini_feed_log",$fieldList);
										}
								}
							else
								echo "Sorry! Your message has not been posted.";
						}
				}//end postToProfile

			elseif($_POST['action']=='discussionFilter')
				{
					$userId			= $_POST['uid'];
					$networkId		= $_POST['networkId'];
					$selectedFilter	= $_POST['selectedFilter'];
					$topic_content	= $this->listtopics($networkId,$selectedFilter);
					print $content 	= '<table><tr><td>'.$topic_content.'</td></tr></table>';
				}//end discussionFilter

			elseif($_POST['action']=='discussionSort')
				{
					$userId			= $_POST['uid'];
					$networkId		= $_POST['networkId'];
					$selectedFilter	= $_POST['selectedFilter'];
					$selectedSorter	= $_POST['selectedSorter'];
					$topic_content	= $this->listtopics($networkId,$selectedFilter,$selectedSorter);
					print $content 	= '<table><tr><td>'.$topic_content.'</td></tr></table>';
				}//end discussionFilter
			elseif($_POST['action']=='discussionSearch')
				{
					$userId			= $_POST['uid'];
					$networkId		= $_POST['networkId'];
					$searchTxt      = $_POST['searchTxt'];
					$topic_content	= $this->listtopics($networkId);
					print $content  = '<table><tr><td>'.$topic_content.'</td></tr></table>';

				}//end discussionFilter
			elseif($_POST['action']=='networkSearch')
				{
					$userId			= $_POST['uid'];
					$networkId		= $_POST['networkId'];
					$searchTxt      = $_POST['searchTxt'];
					$selectedValue	= $_POST['selectedValue'];
					if($selectedValue==1)
					{
					 $query		= "SELECT u.user_id FROM users as u,network_users as nu WHERE u.username LIKE '%$searchTxt%' and u.user_id=nu.user_id";
					}
					elseif($selectedValue==2)
					{
					$query		= "SELECT u.user_id FROM users as u,network_users as nu WHERE u.username LIKE '%$searchTxt%' and u.user_id=nu.user_id and nu.network_id=$networkId";
					}
					elseif($selectedValue==3)
					{
					$query		= "SELECT u.user_id FROM users as u,network_users as nu WHERE u.username LIKE '%$searchTxt%' and u.user_id=nu.user_id and nu.network_id!=$networkId";
					}
					$res			= mysql_query($query);
					$topic_content	= $this->common->listUsers($res,$userId);
					print $content  = '<table><tr><td>'.$topic_content.'</td></tr></table>';

				}//end networkSearch
			elseif($_POST['action']=='reportNetUser')
				{
					$userId			= $_POST['uid'];
					$reply_for_id	= $_POST['reply_for_id'];
					$type     		= $_POST['type'];
					$off_code     	= $_POST['off_code'];
					$comment      	= $_POST['comment'];
					$isReported	= getTotRec("report_id","report","user_id='".$userId."' AND report_for='".$type."' AND report_for_id=".$reply_for_id);
					if(!$isReported)
					{
					$sql=mysql_query("insert into report set user_id='".$userId."',report_for='".$type."',report_for_id=".$reply_for_id.",report_type_id=".$off_code.",report_comment='".$comment."',datestamp=now()");
					$report_content='<TABLE class="splborder" ><TR><TD bordercolor="#ec8a00" bgcolor="#FFD2D2">  <h2>Thank you for your report.</h2>
  <p>An administrator will review your request and take appropriate action.<br>
    For more information about our abuse policies, <a href="#" class="Bluelink">click here</a>.</p>
.</td></tr><tr><td align="center"><input onclick="window.location=\''.base_url().'index.php/home\'" id="home" name="Home" value="Home" type="button"></td></tr></table>';
					}
					//$topic_content	= $this->listtopics($networkId);
					print $content  = '<table><tr><td>'.$report_content.'</td></tr></table>';

				}//end discussionFilter


			elseif($_POST['action']=='networkWall')
				{
					$userId			= $_POST['uid'];
					$postedTo		= $_POST['postedTo'];
					$comment      	= $_POST['comment'];
					if($comment=="")
					{
					$report_content='<TABLE class="splborder" ><TR><TD bordercolor="#ec8a00" bgcolor="#FFD2D2">Please provide an comment.</td></tr></table>';
					}
					else
					{
					$sql=mysql_query("insert into network_wall set posted_by='".$userId."',posted_to='".$postedTo."',wall_post='".$comment."',datestamp=now()");
					$report_content='<TABLE class="splborder" ><TR><TD bordercolor="#ec8a00" bgcolor="#FFD2D2">  <h2>Thank you for your report.</h2>
  <p>An administrator will review your request and take appropriate action.<br>
    For more information about our abuse policies, <a href="#" class="Bluelink">click here</a>.</p>
.</td></tr><tr><td align="center"><input onclick="window.location=\''.base_url().'index.php/home\'" id="home" name="Home" value="Home" type="button"></td></tr></table>';
					}
					//$topic_content	= $this->listtopics($networkId);
					print $content  = '<table><tr><td>'.$report_content.'</td></tr></table>';

				}//end discussionFilter

			//store posted comment for the given user on profile page
			elseif($_POST['action']=='postComment')
				{
					$postedBy	= $_POST['by'];
					$postedTo	= $_POST['to'];
					$postedValue= $_POST['content'];
					if(trim($postedValue)=='')
						echo "Please give the post content...";
					else
						{
							$dateStamp	= time();
							$fieldList	= "user_id='$postedBy', photo_id='$postedTo',photo_comment_description='$postedValue',date_added='$dateStamp',status='active'";
							if(insertRecord("photo_comments",$fieldList))
								{
									$siteVars		= $this->common->getSiteVars();
									$postsOnProfile	= $siteVars['albumsOnProfile'];//toptal topics should appear on events home page
									$query	= "SELECT * FROM photo_comments as pc,users as u,picture_profile as pp WHERE pc.photo_id =$postedTo and  pc.user_id=u.user_id and pc.user_id=pp.user_id order by pc.date_added desc";
									$res	= mysql_query($query);
									if($res)
										{
											$totRec	= mysql_num_rows($res);
											while($rs=mysql_fetch_object($res))
												{
												$deleteCommentUrl=	 base_url().'index.php/photos/delete/'.$rs->comment_id.'/'.$rs->photo_id.'/'.$albumId;
												$content .='<tr><td colspan="2"><table><tr><td ><img src='.$rs->picture_path.' width=40 align="top"></td><td>'.$rs->username.' wrote<br>'.$rs->photo_comment_description.'<br><a href ="'.$deleteCommentUrl.'" class="BlueLink">delete</a></td></tr></table></td></tr>';
												}//end while
										}
									$currentPage	= base_url();
									$ajaxLoader		= base_url().'application/images/indicator_arrows_black.gif';
									$interface	= '<table width="100%">
							<tr><td align="center"><div id="post_comment_ajax_loader" style="display:none;"><img src="'.$ajaxLoader.'"></div></td></tr>
							<tr><td><div id="post_comment_result">Successfully posted...</div></td></tr>
							<tr><td><textarea cols="35" rows="3" name="comment" id="comment" onblur="if(document.getElementById(\'comment\').value==\'\') document.getElementById(\'comment\').value=\'Write something...\';" onclick="document.getElementById(\'comment\').value=\'\';">Write something...</textarea></td></tr>
							<tr><td><input type="button" name="submit" id="submit" value="Add your comments" onclick="postComment(\''.$postedBy.'\',\''.$postedTo.'\',\''.$currentPage.'\');"></td></tr></table>';
									echo '<span id="comment_div">'.$content.$interface.'</span>';
									//echo "Successfully posted...";
									$wallLog	= getRow("mini_feed_settings","mini_feed_status","mini_feed_id=2 AND user_id='$postedBy'");
									if($wallLog[mini_feed_status]=='1' or $wallLog[mini_feed_status]=='')
										{
											$logContent	= 'posted a comment for the photo';
											$logTime	= time();
											$logType	= 'profile';
											$fieldList	= "log_content='$logContent', log_type='$logType', datestamp='$logTime',user_id='$postedBy'";
											insertRecord("mini_feed_log",$fieldList);
										}
								}
							else
								echo "Sorry! Your Comment has not been posted.";
						}
				}//end postcomment




			elseif($_POST['action']=='showCollegeNetworks')
				{
					$region	= $_POST['region'];
					if($region=='0')
						$content	= 'Please select the region to list college networks';
					else
						{
							$query	=	"SELECT network_name, network_id FROM networks
										WHERE network_type='college' AND network_status='enabled' AND
										network_country='$region' ";

							$res		= mysql_query($query);
							$totRec		= mysql_num_rows($res);
							if($totRec>0)
								{
									$i=1;
									$content	= '<tr><td><strong>Available Colleges</strong><tr>';
									while($rs=mysql_fetch_object($res))
										{
											$viewUrl	= base_url().'index.php/networks/view/'.$rs->network_id;
											$content	.= '<td><a href="'.$viewUrl.'" style="text-decoration:none;">'.$rs->network_name.'</a></td>';
											if($i==3)
												{
													$content	.= '</tr><tr>';
													$i=1;
												}
											else
												$i++;
										}//end while
									$content	.= '</tr>';
								}//end res
							else
								$content	= '<tr><td>No college networks available under '.$region.' region</td></tr>';
							$content	= '<table>'.$content.'</table>';
						}//end else
					echo $content;
				}//end showCollegeNetworks
			//show all available school networks for the given region
			elseif($_POST['action']=='showSchoolNetworks')
				{
					$region	= $_POST['region'];
					if($region=='0')
						$content	= 'Please select the region to list school networks';
					else
						{
							$query	=	"SELECT network_name, network_id FROM networks
										WHERE network_type='school' AND network_status='enabled' AND
										network_country='$region' ";

							$res		= mysql_query($query);
							$totRec		= mysql_num_rows($res);
							if($totRec>0)
								{
									$i=1;
									$content	= '<tr><td><strong>Available Schools</strong><tr>';
									while($rs=mysql_fetch_object($res))
										{
											$viewUrl	= base_url().'index.php/networks/view/'.$rs->network_id;
											$content	.= '<td><a href="'.$viewUrl.'" style="text-decoration:none;">'.$rs->network_name.'</a></td>';
											if($i==3)
												{
													$content	.= '</tr><tr>';
													$i=1;
												}
											else
												$i++;
										}//end while
									$content	.= '</tr>';
								}//end res
							else
								$content	= '<tr><td>No school networks available under '.$region.' region</td></tr>';
							$content	= '<table>'.$content.'</table>';
						}//end else
					echo $content;
				}//end showCollegeNetworks
			if($_POST['action']=='generateScurityImage')
				{
					$randWord	= RandomName(rand(4,5));
					$vals = array(
							'word'		 => $randWord,
							'img_path'	 => BASEPATH . '../application/images/captcha/',
							'img_url'	 => base_url() . 'application/images/captcha/',
							'expiration' => 7200,
							'font_path'	 => BASEPATH . '../application/images/fonts/timesbd.ttf'
						);
					$cap 	= create_captcha($vals);
					$captchText	= $cap['image']."|".$randWord;
					echo $captchText;
				}
		}
	//send message to all members of this event
	function messageEvent($subject,$message,$eventId,$eventStatus,$toname)
		{
			global $userId,$now;
			$eventFlag		= true;
			$eventId		= $_POST['actionId'];		//get event id from ajax.js
			$eventStatus	= $_POST['actionStatus'];	//get event attendance status from ajax.js
			if($eventStatus=='notreplied')				//if query for not yet replied guests
				$query		= 	"SELECT receiver_id FROM events_invitation
								WHERE event_id='$eventId' AND invitation_status='sent' AND sender_id='$userId'";
			else//else query for maybeattending or attending guests
				$query		= 	"SELECT receiver_id FROM events_invitation
								WHERE event_id='$eventId' AND invitation_status='$eventStatus' AND sender_id='$userId'";
			$res		= mysql_query($query);
			if($res)
				{
					$totRec	= mysql_num_rows($res);
					if($totRec>0)
						{
							while($rs=mysql_fetch_object($res))
								{
									if(!$this->store2Inbox($rs->receiver_id,$subject,$message))//store this guest details to inbox table
										$eventFlag	= false;
									$this->store2Sent($rs->receiver_id,$subject,$message,'event',$toname);//store event details to sent(message) table
								}
							if($eventFlag)
								$msg	= "successfully sent!|yes";
							else
								$msg	= "Error in sending message to this event guests|no";
						}
					else
						$msg	= "No guests available for this event|no";
				}
			return $msg;
		}
	//send message to all members of this group
	function messageGroup($subject,$message,$groupId,$toname)
		{
			global $userId,$now;
			$eventFlag	= true;
			$eventId	= $_POST['actionId'];		//get event id from ajax.js
			$query		= 	"SELECT receiver_id FROM groups_invitation
							WHERE group_id='$groupId' AND invitation_status='accepted'";
			$res		= mysql_query($query);
			if($res)
				{
					$totRec	= mysql_num_rows($res);
					if($totRec>0)
						{
							while($rs=mysql_fetch_object($res))
								{
									if(!$this->store2Inbox($rs->receiver_id,$subject,$message))//store this guest details to inbox table
										$eventFlag	= false;
									$this->store2Sent($rs->receiver_id,$subject,$message,'group',$toname);//store event details to sent(message) table
								}
							if($eventFlag)
								$msg	= "successfully sent!|yes";
							else
								$msg	= "Error in sending message to this group members|no";
						}
					else
						$msg	= "No members available for this group|no";
				}
			return $msg;
		}

	//store user details to inbox_message table
	function store2Inbox($toid,$subject,$message)
		{
			global $userId,$now;
			//store mail info into inbox_messages table
			$inboxFieldList	= "from_id='$userId',user_id='$toid', subject='$subject', message='$message',datestamp='$now'";
			if(insertRecord('inbox_messages',$inboxFieldList))
				return true;
			else
				return false;
		}
	//store user details to messages table
	function store2Sent($toid,$subject,$message,$type,$typeValue='')
		{
			global $userId,$now;
			//store info at messages table as sent item
			$fieldList	= "user_id='$userId', to_id='$toid', subject='$subject', message='$message',datestamp='$now',to_name='$typeValue',to_type='$type'";
			if(insertRecord('messages',$fieldList))
				return true;
			else
				 return false;
		}
	//function to prepare friends list,
	//to show on groups member page
	function friendslist($listId,$listFor)
		{
			global $userId;
			//echo "SELECT friend_id FROM friends_list WHERE user_id='$userId' AND friend_id<>'$userId'";
			$currentPage	= base_url();
			$res	= mysql_query("SELECT friend_id AS friend_id FROM `friends_list` WHERE user_id='$userId' and approved_status='yes'
									UNION
									SELECT user_id as friend_id FROM `friends_list` WHERE friend_id='$userId' and approved_status='yes'");
			if($res)
			if($listFor=='group')
				{
					while($rs=mysql_fetch_object($res))
						{
							$recCount	= getTotRec('group_invitation_id',"groups_invitation","receiver_id='$rs->friend_id' AND group_id='$listId'");
							if($recCount<1)
								{
									$usrRs		= getRow('users',"username","user_id='$rs->friend_id'");
									$friends 	.= '<div id="check_friend_'.$rs->friend_id.'">
													<input name="check_'.$rs->friend_id.'" id="check_'.$rs->friend_id.'" type="checkbox" onclick="checkInviteFriends(\''.$rs->friend_id.'\',\''.$listId.'\',\''.$currentPage.'\',\'group\')">
													<label>'.$usrRs['username'].'</label>
													</div>';
								}
						}
				}
			elseif($listFor=='event')
				{
					while($rs=mysql_fetch_object($res))
						{
							$recCount	= getTotRec('event_invitation_id',"events_invitation","receiver_id='$rs->friend_id' AND event_id='$listId'");
							if($recCount<1)
								{
									$usrRs		= getRow('users',"username","user_id='$rs->friend_id'");
									$friends 	.= '<div id="check_friend_'.$rs->friend_id.'">
													<input name="check_'.$rs->friend_id.'" id="check_'.$rs->friend_id.'" type="checkbox" onclick="checkInviteFriends(\''.$rs->friend_id.'\',\''.$listId.'\',\''.$currentPage.'\',\'event\')">
													<label>'.$usrRs['username'].'</label>
													</div>';
								}
						}
				}
			return $friends;
		}//end method friendslist
	function listTrash($query)
		{
			$currentPage= base_url();
			$i		= 1;
			for($qryLoop=0;$qryLoop<count($query); $qryLoop++)
				{
					$res	= mysql_query($query[$qryLoop]);
					while($rs=mysql_fetch_object($res))
						{
							if($qryLoop == 0)
								{
									$folder	= 'sent';
									$mailAs	= 'To';
									$chkBoxName	= 'mess_s_'.$i;
									$singleChk	= "sent_".$rs->msgId;
									$readUrl	= base_url().'index.php/messages/readMessage/s_'.$rs->msgId.'_t';
								}
							else
								{
									$folder	= 'inbox';
									$mailAs	= 'From';
									$chkBoxName	= 'mess_i_'.$i;
									$singleChk	= "inbox_".$rs->msgId;
									$readUrl	= base_url().'index.php/messages/readMessage/i_'.$rs->msgId.'_t';
								}

							$msgId		= $rs->msgId;
							$userName	= $this->common->getUsername($rs->userId);
							$userImg	= $this->common->getAvatar($rs->userId);
							$actualDate	= strtotime($rs->datestamp);
							$sentDate	= date('M d, Y',$actualDate);
							$sentTime	= date('h:i a',$actualDate);
							$msgList	.= '<tr class="tr">
												<td><input type=checkbox name="'.$chkBoxName.'" id="'.$chkBoxName.'" value="'.$msgId.'"></td>
												<td valign="top">
													<table>
														<tr>
															<td>
																<img src="'.$userImg.'" border="0" width="50"></img>
															</td>
															<td valign="top">'.$mailAs.':
																'.$userName.'<br>
																<small>'.$sentDate.' at '.$sentTime.'</small>
															</td>
														</tr>
													</table>
												</td>
												<td>
													<strong><a href="'.$readUrl.'" class="BlueLink">'.$rs->subject.'</a></strong>
												</td>
												<td align="right"><a href="javascript:void(0);" style="text-decoration:none;" onclick="msgSingleDelete(\''.$singleChk.'\',\''.$currentPage.'\',\'trash\');">x</a></td>
											</tr>';
							$i++;
						}//end while
				}//end for

			$checkLength	= $i-1;

			//give option for delete trash messages
			$option		= '<table cellpadding=4 cellspacing=0 width=100% style="margin-bottom:10px">
							<tr>
								<td>
									select:<a href="javascript:void(0);" onclick="sel_message(\'all\',\''.$checkLength.'\',\'trash\')">all</a>-<a href="javascript:void(0);" onclick="sel_message(\'none\',\''.$checkLength.'\',\'trash\')">none</a>
								</td>
								<td>
									<select name="message_action" id="message_action" onchange="javascript:trashDelete(\''.$currentPage.'\')" >
										<option value="select_action">Select Action</option>
										<option value="move_to_trash">Delete</option>
									</select>
								</td>
							</tr>
							</table>';

			if($msgList !='')
				{
					$content	= '<form name="msgForm" method="post" id="msgForm">
									<table cellpadding=4 cellspacing=0 width=100% class=b_a>
										<tr>
											<td>
												'.$option.'
												<table cellpadding=4 cellspacing=0 width=100%>
													<tr>
														<td width=5%></td>
														<td width=44%></td>
														<td width=28%></td>
														<td width=23%></td>
													</tr>'.$msgList.'
												</table>
											</td>
										</tr>
										</table>
										<input type="hidden" id="msgCnt" value="'.$checkLength.'">
										</form>';
				}
			else
				{
					$content	= '<table cellpadding=4 cellspacing=0 width=100% class=b_a>
									<tr height="50"><td colspan="4" align="center">No messages</td></tr>
								</table>';
				}
			return $content;
		}//end listTrash()

	###################################################################################################
	#Method			: getInviteResult()
	#Type			: sub
	#Description	: to list all possible results while inviting friends, for the given bunk of emails
	##################################################################################################
	function getInviteResult($emailList)
		{
			$this->loadSettings();
			global $userId,$now,$mplayer,$mencoder,$rootPath,$adminName,$adminEmail,$siteTitle;
			$newFriends=false;$friends=false;$linkedFriends=false;$registeredFriends=false;$alreadyInvitedFlag=false;$invalidFlag=false;
			$isRegistered=0;$isFriend=0;$isLinked=0;
			$result	= '';$invalidEmails='';$newFriends='';$friends='';$linkedFriends='';$invitedFriends='';$registeredFriends='';
			for($i=0;$i<count($emailList);$i++)
				{
					$usrRs			= getRow("users","user_id,username","email='".$emailList[$i]."'");
					$emailName		= $emailList[$i];
					$isRegistered	= getTotRec("user_id","users","email='".$emailList[$i]."'");//check whether this email registered on this site
					if($isRegistered)//if registered user, check whether he is already his friend or got friend request
						{
							$isFriend	= getTotRec("friends_list_id","friends_list","user_id='$userId' AND approved_status='yes' AND friend_id=".$usrRs[user_id]);
							$isLinked	= getTotRec("friends_list_id","friends_list","user_id='$userId' AND approved_status='no' AND friend_id=".$usrRs[user_id]);
							$emailName	= $usrRs[username]."&lt;".$emailList[$i]."&gt;";
						}
					if(!isValidEmail($emailList[$i]))
						{
							$invalidFlag	= true;
							$invalidEmails	.= $emailName."<br>";
						}
					else
						{
							if(!$isRegistered)//prepares emails, which are not registered yet from the given email list
								{
									$newFriendFlag		= true;
									$newFriends			.= $emailName."<br>";
								}
							if($isRegistered and $isFriend)//prepares already friends emails, from the given email lsit
								{
									$friendFlag			= true;
									$friends			.= $emailName."<br>";
								}
							if($usrRs[user_id]==$userId)//check whether the user tried himself to send mail
								{
									$linkedFriendFlag	= true;
									$linkedFriends		.= $emailName."<br>";
								}
							if($isRegistered and $isLinked)//prepare already invited but not yet confirmed friends from the given email lsit
								{
									$alreadyInvitedFlag	= true;
									$invitedFriends		.= $emailName."<br>";
								}
							if($isRegistered and !$isLinked and !$isFriend and $usrRs[user_id]!=$userId)//prepare emails which are registered but not yet invited
								{
									$registeredFlag		= true;
									$registeredFriends	.= $emailName."<br>";
								}
						}
				}//end for
			if($newFriendFlag)
				$result	.= '<tr><td><strong>The following emails have been sent invites:</strong></td></tr>
							<tr><td>'.$newFriends.'</td></tr>';
			if($registeredFlag)
				$result	.= '<tr><td><strong>The following person is already registered on '.$siteTitle.'. <br>We have sent them a friend request for you:</strong></td></tr>
							<tr><td>'.$registeredFriends.'</td></tr>';
			if($friendFlag)
				$result	.= '<tr><td><strong>The following people already your friend:</strong></td></tr>
							<tr><td>'.$friends.'</td></tr>';
			if($linkedFriendFlag)
				$result	.= '<tr><td><strong>The following email is already linked to your account:</strong></td></tr>
							<tr><td>'.$linkedFriends.'</td></tr>';
			if($alreadyInvitedFlag)
				$result	.= '<tr><td><strong>You have already invited the following people:</strong></td></tr>
							<tr><td>'.$invitedFriends.'</td></tr>';
			if($invalidFlag)
				$result	.= '<tr><td><strong>The following email is invalid:</strong></td></tr>
							<tr><td>'.$invalidEmails.'</td></tr>';
			$inviteUrl	= base_url()."index.php/friends/invite";
			$homeUrl	= base_url()."index.php/home";
			$result	= '<table align="center">'.$result.'
						<tr><td align="right">
							<input type="button" value="Invite More People" onclick="window.location=\''.$inviteUrl.'\'">
							<input type="button" value="Go Home" onclick="window.location=\''.$homeUrl.'\'">
						</table>';
			return $result;
		}//end getInviteResult()

	###################################################################################################
	#Method			: getInviteList()
	#Type			: sub
	#Description	: to prepare valid email list to invite friends, from the bunk of mail ids.
	##################################################################################################
	function getInviteList($emailList)
		{
			$this->loadSettings();
			global $userId,$now,$mplayer,$mencoder,$rootPath,$adminName,$adminEmail;
			$toList	= '';
			$isLinked=false; $isFriend=0; $isRegistered=0;
			for($i=0;$i<count($emailList);$i++)
				{
					$isRegistered	= getTotRec("user_id","users","email='".$emailList[$i]."'");//check whether this email registered on this site
					if($isRegistered)//if registered user, check whether he is already his friend or got friend request
						{
							$usrRs		= getRow("users","user_id","email='".$emailList[$i]."'");
							$isFriend	= getTotRec("friends_list_id","friends_list","user_id='$userId' AND friend_id=".$usrRs[user_id]);
							if($usrRs[user_id]	== $userId)
								$isLinked	= true;
						}
					if((!$isRegistered and isValidEmail($emailList[$i])) or ($isRegistered and !$isFriend and !$isLinked and isValidEmail($emailList[$i])))
						$toList	.= $emailList[$i].",";
				}
			$toList	= trim($toList,",");
			return $toList;
		}//end getInviteList()

 	function photoComment($userId,$photoId,$result)
	{
						$currentPage	= base_url();
						$ajaxLoader		= base_url().'application/images/indicator_arrows_black.gif';
						$comment_content	= '<table width="100%">
							<tr><td align="center"><div id="post_comment_ajax_loader" style="display:none;"><img src="'.$ajaxLoader.'"></div</td></tr>
							<tr><td><div id="post_comment_result">'.$result.'</div></td></tr>
							<tr><td><textarea cols="35" rows="3" name="comment" id="comment" onblur="if(document.getElementById(\'comment\').value==\'\') document.getElementById(\'comment\').value=\'Write something...\';" onclick="document.getElementById(\'comment\').value=\'\';">Write something...</textarea></td></tr>
							<tr><td><input type="button" name="submit" id="submit" value="Add your comments" onclick="postComment(\''.$userId.'\',\''.$photoId.'\',\''.$currentPage.'\');"></td></tr></table>';
							return $comment_content;

	}




	###################################################################################################
	#Method			: listtopics()
	#Type			: sub
	#Description	: to list the topic posted by network user.
	##################################################################################################
	function listtopics($networkId,$selectedFilter="",$selectedSorter="")
		{
			$this->loadSettings();
			global $userId,$now;
			 $searchTxt = $_POST[searchTxt];
			 $totTopic	= getTotRec("network_discussion_topic_id","networks_discussion_topic","network_id='$networkId'");

			if ($searchTxt!="")   //query for search
			{
				$query			= "SELECT network_discussion_topic_id, network_discussion_topic,created_by,date_added
						  FROM networks_discussion_topic
						  WHERE network_discussion_topic='$searchTxt'";
				$displayInfo	='Displaying the only topic with '.$searchTxt.' in the topic. <a href="'.base_url().'index.php/networks/networkBoard/'.$networkId.'" class="Bluelink">See all Topics</a>';
				$content = $this->listContent($query);
				return $content;
				exit;
			}
			else
			{
				if($selectedFilter==1)
					{
					$condition 	= "nkTopic.network_id='$networkId'";
						if($selectedSorter==2)
							{
							$query	= "select * from networks_discussion_topic as nkTopic where ".$condition."  order by nkTopic.date_added desc";//condition for newest topic in sorter
							}
						elseif($selectedSorter==3)
							{
							$query	= "select * from networks_discussion_topic as nkTopic where ".$condition." group by nkTopic.network_discussion_topic_id order by max(nkTopic.created_by) asc ";//condition for most people in sorter
							}
						elseif ($selectedSorter==4)
							{
							$query	= "select * from networks_discussion_post as nkPost, networks_discussion_topic as nkTopic where ".$condition."  and nkPost.network_discussion_topic_id=nkTopic.network_discussion_topic_id group by nkPost.network_discussion_topic_id order by max(nkPost.network_discussion_topic_id) asc ";//condition for most post in sorter
							}
						else
							{
							$query="select * from networks_discussion_post as nkPost, networks_discussion_topic as nkTopic where ".$condition. " and nkPost.network_discussion_topic_id=nkTopic.network_discussion_topic_id and nkPost.replied_to='post' group by nkPost.network_discussion_topic_id order by max(nkPost.posted_date) asc ";
							}
							$content = $this->listContent($query);
							return $content;
							exit;
					}
				elseif($selectedFilter==2)
					{
					$condition	= "nkTopic.network_id='$networkId' and nkTopic.created_by=$userId";
						if($selectedSorter==2)
							{
							$query	= "select * from networks_discussion_topic as nkTopic where ".$condition."  order by nkTopic.date_added desc";//condition for newest topic in sorter
							}
						elseif($selectedSorter==3)
							{
							$query	= "select * from networks_discussion_topic as nkTopic where ".$condition." group by nkTopic.network_discussion_topic_id order by max(nkTopic.created_by) asc ";//condition for most people in sorter
							}
						elseif ($selectedSorter==4)
							{
							$query	= "select * from networks_discussion_post as nkPost, networks_discussion_topic as nkTopic where ".$condition." and nkPost.posted_by='$userId'  and nkPost.network_discussion_topic_id=nkTopic.network_discussion_topic_id group by nkPost.network_discussion_topic_id order by max(nkPost.network_discussion_topic_id) asc ";//condition for most post in sorter
							}
						else
							{
							$query="select * from networks_discussion_post as nkPost, networks_discussion_topic as nkTopic where ".$condition. " and nkPost.posted_by='$userId' and nkPost.network_discussion_topic_id=nkTopic.network_discussion_topic_id and nkPost.replied_to='post' group by nkPost.network_discussion_topic_id order by max(nkPost.posted_date) asc ";
							}
							$content = $this->listContent($query);
							return $content;
							exit;

					}
				elseif ($selectedFilter==3)
					{
					$friendList = $this->common->getFriends($userId);
					$i=0;
					for ($i=0;$i<count($friendList);$i++)
						{
						$query		= "select * from networks_discussion_post as nkPost, networks_discussion_topic as nkTopic where nkTopic.network_id='$networkId' and nkTopic.created_by=".$friendList[$i]." and nkPost.posted_by='".$friendList[$i]."'";
						$content = $this->listContent($query);
						return $content;
						exit;
						}
					}

			}
			//return $content;
		}//end listtopics()


	function listContent($query)
	{
			$loop	= 1;
			$res	= mysql_query($query);
			$rec	= mysql_num_rows($res);
			if($selectedFilter==1)
			$displayInfo= "Displaying all ".$rec." topics";
			if($selectedFilter==2)
			$displayInfo= "Displaying all ".$rec." topics that you've participated in.";
			if($res)
			if($rec>0)
			{
			while($rs=mysql_fetch_object($res))
				{
					$topic		= ucwords($rs->network_discussion_topic);		//get topic name
					$topicId	= $rs->network_discussion_topic_id;			//get topic id

					//get total posts available for thsis topic
					$totPosts	= getTotRec("network_discussion_post_id","networks_discussion_post","network_discussion_topic_id='$topicId'");
					//get total posted people for thsis topic
					$totPeople	= getTotRec("DISTINCT posted_by","networks_discussion_post","network_discussion_topic_id='$topicId'");

					//Prepare last updated date for this topic
					$dateQry	= "SELECT posted_date FROM networks_discussion_post
														  WHERE network_discussion_topic_id='$topicId'
														  ORDER BY posted_date DESC
														  LIMIT 0,1";
					$postDateRes	= mysql_query( $dateQry);
					$postDateRs		= mysql_fetch_object($postDateRes);
					$lastUpdate		= strtotime($postDateRs->posted_date);
					$formatDate		= date("M j, Y",$lastUpdate);
					$formatTime		= date("h:i A",$lastUpdate);
					$updatedDate	= $formatDate." at ".$formatTime;
					//End date preparation

					//prepare topic added date
					$topicDate		= strtotime($rs->date_added);
					$topicFormatDate= date("M j, Y",$topicDate);
					$topicFormatTime= date("h:i A",$topicDate);
					$topicDate		= $topicFormatDate." at ".$topicFormatTime;
					//end topic added date

					//latest posted person for this topic
					$postQry		= "SELECT posted_by FROM networks_discussion_post
														  WHERE network_discussion_topic_id='$topicId'
														  ORDER BY posted_date DESC
														  LIMIT 0,1";
					$postRes		= mysql_query( $postQry);
					$postRs			= mysql_fetch_object($postRes);
					$usrRs			= getRow('users',"username,user_id","user_id='$postRs->posted_by'");
					$postedBy		= ucwords($usrRs['username']);
					//end latest posted person
					$profileUrl		= base_url().'index.php/profile/user/'.$usrRs['user_id'];
					$postUrl		= base_url().'index.php/networks/allDiscussionPosts/'.$topicId;
					$content 		.='
								<tr><td colspan="2">'.$displayInfo.'</td></tr><tr>
									<td width="60%">
										<table>
											<tr><td><a href="'.$postUrl.'" class="BlueLink"><strong><font color="#3399FF">'.$topic.'</font></strong></a></td></tr>
											<tr><td><small>'.$totPosts.' post(s) by '.$totPeople.' people(s). <br>Created on '.$topicDate.'</small></td></tr></table>
									</td>
									<td width="40%">
										<table>
											<tr><td><a href="'.$postUrl.'" class="BlueLink"><font color="#3399FF">Latest post</a> </font>by <a href="'.$profileUrl.'" class="BlueLink"><font color="#3399FF">'.$postedBy.'</font></a></td></tr>
											<tr><td><small>Posted on '.$updatedDate.'</small></td></tr></table>
									</td>
								<tr>';
				}//end while
			}
			else
			{
			$content .='<tr><td align="center"><span class="grytxt">There are no discussions in the last 60 days that match '.$searchTxt.'</span><a href="'.base_url().'index.php/networks/networkBoard/'.$networkId.'" class="Bluelink"> See all topic</a></td></tr>';
			}
			$content	= '<table align="center">'.$content.'</table>';
			return $content;
	}


	###################################################################################################
	#Method			: postWall2Profile()
	#Type			: sub
	#Description	: create an interface to post a wall for the given user
	##################################################################################################
	function postWall2Profile($postedBy,$postedTo,$result,$type='')
		{
			$currentPage	= base_url();
			$ajaxLoader		= base_url().'application/images/indicator_arrows_black.gif';
			$content	= '<table width="100%">
							<tr><td align="center"><div id="wall_post_ajax_loader" style="display:none;"><img src="'.$ajaxLoader.'"></div</td></tr>
							<tr><td><div id="wall_post_result">'.$result.'</div></td></tr>
							<tr><td><textarea cols="35" rows="3" name="post_content" id="post_content" onblur="if(document.getElementById(\'post_content\').value==\'\') document.getElementById(\'post_content\').value=\'Write something...\';" onclick="document.getElementById(\'post_content\').value=\'\';">Write something...</textarea></td></tr>
							<tr><td><input type="button" name="post_wall" id="post_wall" value="Post" onclick="post2Profile(\''.$postedBy.'\',\''.$postedTo.'\',\''.$currentPage.'\',\''.$type.'\');"></td></tr></table>';
			return $content;
		}//postWall2Profile()
	###################################################################################################
	#Method			: deleteWall()
	#Type			: sub
	#Description	: delte the given wall post
	##################################################################################################
	function deleteWall()
		{
			$this->loadSettings();
			$datas	= $this->common->authenticate();
			global $userId,$now,$rootPath;				//declare $userId and $now as global variable
			$data	= array();

			if(count($datas)<1)
				{
					$datas					= $this->common->basicvars();
					$data					= $datas;
					$data['siteUrl']		= site_url();
					$data['center_login']	= true;
					$this->smartyextended->view('login',$data);
				}
			else
				{
					$networkWallId		= $_POST[network_wall_id];	//get network wall id
					$data				= $datas;					//store default values
							if(isset($_POST['confirm_delete']))
								{
								$nkRs		= getRow("network_wall","posted_to","network_wall_id=$networkWallId");
								mysql_query("DELETE FROM network_wall WHERE network_wall_id='$networkWallId'");
								header("Location: ".base_url()."index.php/networks/view/".$_POST[networkId]);
								}
				}
		}//end deleteWall()

	###################################################################################################
	#Method			: postWall2Network()
	#Type			: sub
	#Description	: create an interface to post a wall for the given user
	##################################################################################################
	function postWall2Network($postedBy,$postedTo)
		{
			$currentPage	= base_url();
			$ajaxLoader		= base_url().'application/images/indicator_arrows_black.gif';
			$action			= 'postToNetwork';
			$content		= '<table width="100%">
							<tr><td align="center"><div id="wall_post_ajax_loader" style="display:none;"><img src="'.$ajaxLoader.'"></div</td></tr>
							<tr><td><div id="wall_post_result"></div></td></tr>
							<tr><td><textarea cols="35" rows="3" name="post_content" id="post_content" onblur="if(document.getElementById(\'post_content\').value==\'\') document.getElementById(\'post_content\').value=\'Write something...\';" onclick="document.getElementById(\'post_content\').value=\'\';">Write something...</textarea></td></tr>
							<tr><td><input type="button" name="post_wall" id="post_wall" value="Post" onclick="post2NetworkProfile(\''.$postedBy.'\',\''.$postedTo.'\',\''.$currentPage.'\',\''.$action.'\');"></td></tr></table>';
			return $content;
		}//postWall2Network()

	###################################################################################################
	#Method			: load_settings()
	#Type			: sub
	#Description	: load all common variables from config and assign to the global variables
	##################################################################################################
	function loadSettings()
		{
			global $userId,$now,$mplayer,$mencoder,$rootPath,$adminName,$adminEmail,$siteName,$siteTitle,$recorderPath,$red5SettingsPath;
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
			$recorderPath		= $siteVars['recorderPath'];
			$red5SettingsPath	= $siteVars['red5SettingsPath'];

		}
}
?>
