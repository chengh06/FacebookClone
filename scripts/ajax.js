// JavaScript Document
//registeration
function register()
{
		var user		= $('name').value;
		var	lifestage	= $('lifestage').value;
		$('ajax_loader').style.display	= 'block';
		$('registerResult').innerHTML	= '';
		if(lifestage=='1')
			{
				var schoolStatus= $('schoolStatus').value;
				if(schoolStatus=='1' || schoolStatus=='2' || schoolStatus=='3')
					var collegeYear	= $('college_year').value;
			}
		else if(lifestage=='3')
			{
				var highSchool= $('high_school').value;
				var schoolYear= $('high_school_year').value;
			}
		var email	=$('register_email').value;
		var password=$('register_password').value;
		var bd_day	= $('birthday_day').value;
		var bd_month= $('birthday_month').value;
		var bd_year	= $('birthday_year').value;
		var captcha	= $('captcha_response').value;
		//var randWord= $('randWord').value;
		var randWord = document.getElementById("randWord").value;
		
		if($('terms').checked)
			var terms	= 1;
		else
			var terms	= 0;
		
		var url		= 'ajax';
		var pars	= 'action=register&uname='+user+'&lifestage='+lifestage+'&schoolStatus='+schoolStatus+'&collegeYear='+collegeYear+'&highSchool='+highSchool+'&schoolYear='+schoolYear+'&email='+email+'&pass='+password+'&bd='+bd_day+'&bm='+bd_month+'&by='+bd_year+'&terms='+terms+'&captcha='+captcha+'&randWord='+randWord;
		//alert(pars);
		var myAjax	= new Ajax.Request(
			url, 
			{
				parameters: pars, 
				onSuccess: updateRegister
			},email);
}

function updateRegister(originalRequest,json,email)
{
	var responseText				= originalRequest.responseText;
	if(responseText == 'success')
		{
			$('registration').style.display		= 'none';
			$('registerSuccess').style.display	= 'block';
			$('registerEmail').innerHTML		= email;

		}
	else
		$('registerResult').innerHTML	= '<table width="100%" class="splborder"><tr><td height="41" bordercolor="#ec8a00" bgcolor="#feffcf"><span class="blktitle">'+responseText+'</span><br /></td></tr></table>';
	$('ajax_loader').style.display	= 'none';
}
//---------------------------------------------------------------------------------------------------------------------
//edit basic profile
function basicProfile(currentPath,userId)
{
		$('basic_ajax_loader').style.display	= 'block';
		var sex			= $('sex').value;
		var meeting		= '';
		if($('meeting_men').checked)
			meeting	= $('meeting_men').value+',';
		if($('meeting_women').checked)
			meeting	+= $('meeting_women').value;
		//alert(meeting);
		var relation	= $('relationship').value;
		var lookingFor	= '';
		for(var i=1;i<=$('totMeets').value;i++)
			if($('meeting_for'+i).checked)
				lookingFor +=$('meeting_for'+i).value+',';
		//alert(lookingFor);
		var bd_day		= $('birthday_day').value;
		var bd_month	= $('birthday_month').value;
		var bd_year		= $('birthday_year').value;
		var bd_visible	= $('birthday_visibility').value;
		var hometown	= $('hometown').value;
		var country		= $('basic_profile_country_select').value;
		if(country=='US')
			var state	= $('basic_profile_state_us_select').value;
		else if(country=='CA')
			var state	= $('basic_profile_state_ca_select').value;
		else
			var state	= '';
		var politics	= $('political_view').value;
		var religion	= $('religion_name').value;
		
		var url		= currentPath+'index.php/ajax';
		var pars	= 'action=basicProfile&userId='+userId+'&sex='+sex+'&meeting='+meeting+'&relation='+relation+'&lookingFor='+lookingFor+'&bd='+bd_day+'&bm='+bd_month+'&by='+bd_year+'&bd_visible='+bd_visible+'&hometown='+hometown+'&country='+country+'&politics='+politics+'&religion='+religion+'&state='+state;
		//alert(pars);
		
		var myAjax	= new Ajax.Request(
			url, 
			{
				parameters: pars, 
				onSuccess: updateBasicProfile
			});
}

function updateBasicProfile(originalRequest)
{
	var responseText				= originalRequest.responseText;
	//alert(responseText);
	$('profileResult').innerHTML	= '<table width="100%" class="splborder"><tr><td height="41" bordercolor="#ec8a00" bgcolor="#feffcf"><span class="blktitle">'+responseText+'</span><br /></td></tr></table>';;
	$('basic_ajax_loader').style.display	= 'none';
}


//Message functions
//tab process in message page---------------------------------------------------------------------------
function messageProcess(folder,currentPath)
{
	var flag	= false;//defualt flag for check box
	var msgIds	= '';//declare msgIds to store selected message ids
	var action	= $('message_action').value;
	var allNodes	= $A($('msgForm'));
	//alert(action);
	//alert(folder);
	var url		= currentPath+'index.php/ajax';
	var pars	= 'action=message_'+action;
	if(action=='mark_unread' || action=='mark_read' || action=='move_to_trash' || action=='select_action')
	{
		if(action=='mark_unread' && folder=='inbox')
			var msgName	= 'mess_i_r_';
		else if(action=='mark_unread' && folder=='sent')
			var msgName = 'mess_s_r_';
		else if(action=='mark_read' && folder=='inbox')
			var msgName	= 'mess_i_u_';
		else if(action=='mark_read' && folder=='sent')
			var msgName = 'mess_s_u_';
		else if(action=='select_action' && folder=='inbox')
			var msgName	= 'mess_i_r_';
		else if(action=='select_action' && folder=='sent')
			var msgName	= 'mess_s_r_';
		if(action=='move_to_trash')	
		{
			var msgId		= selectAllMsg();
			//alert(msgId);
			var splitIds	= msgId.split('|');
			msgIds		= splitIds[0];
			if(splitIds[1]=='1')
				flag=true;
			else
				flag=false;
		}
		else
		{
			var len = $('msgCnt').value;
			for(i = 0; i < allNodes.length; i++) 
			{
				//do something to each form field
				//alert(allNodes[i].name);
				for (j = 1; j <= len; j++)
				{
					var msg1='mess_i_r_' + j;
					var msg2='mess_i_u_' + j;
					var msg3='mess_s_r_' + j;
					var msg4='mess_s_u_' + j;
					if(allNodes[i].name	==msg1)
						msgName=msg1;
					else if(allNodes[i].name==msg2)
						msgName=msg2;
					else if(allNodes[i].name==msg3)
						msgName=msg3;
					else if(allNodes[i].name==msg4)
						msgName=msg4;
					else
						msgName='';
					if(msgName!='' && $(msgName).checked)
						{
							flag = true;
							msgIds += $(msgName).value+',';
						}
				}
			}
		}
		pars  +='&folder='+folder+'&msgIds='+msgIds;
	}
	//alert(flag);
	if((action=='mark_unread' || action=='mark_read') && !flag)
		alert("Please select atleast one message to process !");
	else
	{
			var myAjax	= new Ajax.Request(
				url, 
				{
					parameters: pars, 
					onSuccess: updateMessageProcess
				});
	}
}

function trashDelete(currentPath)
{
	var url		= currentPath+'index.php/ajax';
	var pars	= 'action=trashDel&delId='+delId;
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateMessageProcess
		});
}
function msgSingleDelete(delId,currentPath,action)
{
	//alert(delId);
	var ans	= confirm("Do you really want to remove this ?");
	//alert(ans);
	if(ans)
	{
		var url		= currentPath+'index.php/ajax';
		if(action=='trash')
			var pars	= 'action=msgTrashSingleDel&delId='+delId;
		else
			var pars	= 'action=msgSingleDel&delId='+delId;
		//alert(pars)	
		var myAjax	= new Ajax.Request(
			url, 
			{
				parameters: pars, 
				onSuccess: updateMessageProcess
			});
	}
}
function updateMessageProcess(originalRequest)
{
	var responseText			= originalRequest.responseText;
		//alert(responseText);
	$('message_content_div').innerHTML='';
	//alert(responseText);
	$('message_content_div').innerHTML= responseText;
}
function selectAllMsg()
{
	allNodes	= $A($('msgForm'));
	msgIds	 	= '';
	var len = $('msgCnt').value;
	for(i = 0; i < allNodes.length; i++) 
	{
		//do something to each form field
		//alert(allNodes[i].name);
		for (j = 1; j <= len; j++)
		{
			var msg1='mess_i_r_' + j;
			var msg2='mess_i_u_' + j;
			var msg3='mess_s_r_' + j;
			var msg4='mess_s_u_' + j;
			if(allNodes[i].name	==msg1)
				msgName=msg1;
			else if(allNodes[i].name	==msg2)
				msgName=msg2;
			else if(allNodes[i].name	==msg3)
				msgName=msg3;
			else if(allNodes[i].name	==msg4)
				msgName=msg4;
			else
				msgName='';
			if(msgName!='' && $(msgName).checked)
				{
					flag = true;
					msgIds += $(msgName).value+',';
				}
		}
	}
	if(flag)
		msgIds +='|1';
	else
		msgIds +='|0';
	return msgIds;
}
function trashDelete(currentPath)
{
	var url		= currentPath+'index.php/ajax';
	var pars	= 'action=trash_delete';
	allNodes 	= $A($('msgForm'));
	inboxMsgIds	= '';
	sentMsgIds	= '';
	var len = $('msgCnt').value;
	
	for(i = 0; i < allNodes.length; i++) 
	{
		//do something to each form field
		for (j = 1; j <= len; j++)
		{
			var msg1='mess_i_' + j;
			var msg2='mess_s_' + j;
			if(allNodes[i].name	== msg1)
			{
				if($(msg1).checked)
				{
					flag = true;
					inboxMsgIds += $(msg1).value+',';
				}
			}
			if(allNodes[i].name	== msg2)
			{
				if($(msg2).checked)
				{
					flag = true;
					sentMsgIds += $(msg2).value+',';
				}
			}
		}//end second for
	}//end first for
	
	pars +='&inboxMsgIds='+inboxMsgIds+'&sentMsgIds='+sentMsgIds;
	if(!flag)
		alert("Please select atleast one message to process !");
	else
	{
		var myAjax	= new Ajax.Request(
			url, 
			{
				parameters: pars, 
				onSuccess: updateMessageProcess
			});
	}
}//end function
//---------------------------------------------------------------------------------------------------

//to select group sub category while changing group category
function groupCategorySelect(currentPath)
{
		var url			= currentPath+'index.php/ajax';
		var category	= $('group_category').value;
		var pars		= 'action=groupCategory&category='+category;
		$('cat_ajax_loader').style.display	= 'block';
		//alert(pars);
		var myAjax	= new Ajax.Request(
			url, 
			{
				parameters: pars, 
				onSuccess: updateGroupCategorySelect
			});
}

function updateGroupCategorySelect(originalRequest)
{
	var responseText			= originalRequest.responseText;
	eval(responseText);
	$('group_sub_category').options.length = 0;
	var i=0;
	for (var key in groupSubCategory)
		{
			$('group_sub_category').options[i] = new Option(groupSubCategory[key], key);
			i = i + 1;
		}
	$('cat_ajax_loader').style.display	= 'none';
}
//-----------------------------------------------------------------------------------------------------

//group, members, show invite friend form
function checkInviteFriends(friendId,checkForId,currentPath,checkFor)
{
	$('check_'+friendId).checked	= false;
	$('check_friend_'+friendId).style.display	= 'none';
	
	var url		= currentPath+'index.php/ajax';
	//alert(category);
	if(checkFor=='group')
		var pars	= 'action=showInviteForm&fid='+friendId+'&checkForId='+checkForId+'&checkFor='+checkFor;
	else if(checkFor=='event')
		var pars	= 'action=showInviteFormEvent&fid='+friendId+'&checkForId='+checkForId;
	//alert(pars);
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateCheckInviteFriends
		});
}
//send invitation
function sendInvitation(friendId,sendForId,currentPath,sendFor)
{
	var url		= currentPath+'index.php/ajax';
	//alert(friendId);
	//alert(groupId);
	var msg		= $('personal').value;
	var pars	= 'action=sendInvitation&fid='+friendId+'&sendForId='+sendForId+'&msg='+msg+'&sendFor='+sendFor;
	//alert(pars);
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateSendInvitation
		});
}

function updateCheckInviteFriends(originalRequest)
{
	var responseText						= originalRequest.responseText;
	//alert(responseText);
	$('friend_invite_form').style.display	= 'block';
	$('friend_invite_form').innerHTML		= responseText;
}
function updateSendInvitation(originalRequest)
{
	var responseText					= originalRequest.responseText;
	//alert(responseText);
	$('friend_invite_form').style.display	= 'none';
	$('invite_result_main').style.display	= 'block';
	$('invite_result_sub').innerHTML		= responseText;
	$('friend_invite_form').innerHTML		= '';
}


//fucntion to hide the send invitation form
function removeInvitation(remForId,currentPath,remFor)
{
	var url		= currentPath+'index.php/ajax';
	//alert(friendId);
	//alert(groupId);
	var pars	= 'action=removeInvitation&remForId='+remForId+'&remFor='+remFor;
	//alert(pars);
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateRemoveInvitation
		});
}
function updateRemoveInvitation(originalRequest)
{
	var responseText						= originalRequest.responseText;
	//alert(responseText);
	$('friend_invite_form').style.display	= 'none';
	$('userlist').innerHTML					= responseText;
}


//send multiple ivitation
function sendMultipleInvitation(sendForId,sendFor,currentPath)
{
	var url		= currentPath+'index.php/ajax';
	var tolist	= $('email_addresses').value;
	var pars	= 'action=sendMultiple&sendForId='+sendForId+'&tolist='+tolist+'&sendFor='+sendFor;
	//alert(pars);
	$('ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateSendMultipleInvitation
		});
}
function updateSendMultipleInvitation(originalRequest)
{
	var responseText					= originalRequest.responseText;
	//alert(responseText);
	$('friend_invite_form').style.display	= 'none';
	$('invite_result_main').style.display	= 'block';
	$('invite_result_sub').innerHTML		= responseText;
	$('friend_invite_form').innerHTML		= '';
	$('ajax_loader').style.display			= 'none';
}
//function to show the group members
function showMembers(groupId,currentPath)
{
	var url		= currentPath+'index.php/ajax';
	//alert(groupId);
	if($('list_type').value=='0')
		var action = 'showMembers';
	else if($('list_type').value=='1')
		var action = 'showNotreplied';
	else if($('list_type').value=='2')
		var action = 'showBlocked';
	var pars	= 'action='+action+'&gid='+groupId;
	//alert(pars);
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateShowMembers
		});
}
function updateShowMembers(originalRequest)
{
	var responseText					= originalRequest.responseText;
	//alert(responseText);
	$('members_list').innerHTML			= responseText;
}

//Rate this video----------------------------------------------------------------------------------------
function rateThisVideo(currentPath,vblogId,login,rateValue) {
	//alert(login);
	//if(!login)
		//$("loginResultForRate").style.display ='block';
	//else
	//{
		var strURL	= currentPath+'index.php/ajax';
		var pars	= 'blogid='+vblogId+'&action=rateThisVideo&rv='+rateValue;
	
		var myAjax	= new Ajax.Request(
			strURL, 
			{
				parameters: pars, 
				onSuccess: updateRateVideo	
			});
	//}
}
function updateRateVideo(originalRequest)
{
	var responseText	= originalRequest.responseText;
	$("ratingDiv").style.display 	='none';
	$("ratingDivWrapper").innerHTML = responseText;
}
//--------------------------------------------------------------------------------------------------
//Rate this video----------------------------------------------------------------------------------------
function processComments(currentPath,vblogId,action) 
{
		//alert(vblogId);
		var strURL	= currentPath+'index.php/ajax';
		var pars	= 'blog_id='+vblogId+'&action='+action;
	
		var myAjax	= new Ajax.Request(
			strURL, 
			{
				parameters: pars, 
				onSuccess: updateProcessComments
			},action);
}
function updateProcessComments(originalRequest,json,action)
{
	var responseText	= originalRequest.responseText;
	$("comment_tab").innerHTML = responseText;
	if(action=='post_comment')
		{
			$('view_comments').style.backgroundColor ="#CCCCCC";
			$('post_comments').style.backgroundColor="#003366";
		}
	else
		{
			$('view_comments').style.backgroundColor="#003366";
			$('post_comments').style.backgroundColor="#CCCCCC";
		}
}
//--------------------------------------------------------------------------------------------------
//Remove school in education profile----------------------------------------------------------------
function remove_school(school_name,currentPath) 
{
		var ans	= confirm("Do you want to remove this school from database?");
		if(ans)
		{
			var strURL	= currentPath+'index.php/ajax';
			
			/*var cons	= Array();
			//retrieve school name
			var	school	= $(school_name+'_school_name').value;
			//retrieve school year
			if(parseInt($(school_name+'_year').value)>0)
				var school_year	= $(school_name+'_year').value
			else
				var school_year	= '';
			//retrieve constration value
			for(var i=1;i<=parseInt($(school_name+'_concentration_value_count').value);i++)
				{
					cons[i-1]	= $(school_name+'_concentration'+i+'_name').value;
				}
			//retrieve attended for
			var attended_for	= $(school_name+'_school_type').value;
			//retrieve degree
			if(attended_for=='gradschool')
				var degree	= $(school_name+'_degree_name').value;
			
			var pars	= 'action=remove_school&cons='+cons+'&att_for='+attended_for+'&degree='+degree+'&school='+school+'&school_year='+school_year+'&education='+school_name;*/
			//alert(pars);
			var pars	= 'action=remove_school&education='+school_name;
			var arg		= school_name+'|'+currentPath;
			//alert(pars);
			var myAjax	= new Ajax.Request(
				strURL, 
				{
					parameters: pars, 
					onSuccess: updateRemove_school
				},arg);
		}
}
function updateRemove_school(originalRequest,json,arg)
{
	var responseText	= originalRequest.responseText;
	var argsplit	= arg.split("|");
	
	$('education_result').style.display	= 'block';
	if(argsplit[0]!='education_1' && responseText=='true')
		{
			$(argsplit[0]).style.display	= 'none';
			result	= 'Changes saved.';
		}
	else if(responseText=='true')
		result	= 'Changes saved.';
	else
		result	= 'Changes has not been saved.';
	$('education_result').innerHTML	=	'<table width="100%" class="splborder"><tr><td height="41" bordercolor="#ec8a00" bgcolor="#feffcf"><span class="blktitle">'+result+'</span><br /></td></tr></table>';						
	$('ecuacation_main_div').style.display	= 'none';
	var t= setTimeout("showRemoveSchoolResult(\""+argsplit[1]+"\")",2000);
}
	

function showRemoveSchoolResult(curPath)
{
	//alert(curPath);
	window.location	= curPath+"index.php/editprofile/education";
}
//--------------------------------------------------------------------------------------------------
//Remove school in education profile----------------------------------------------------------------
function remove_job(job_name,currentPath) 
{
		var ans	= confirm("Do you want to remove this job from database?");
		if(ans)
		{
			var strURL	= currentPath+'index.php/ajax';
			
			var pars	= 'action=remove_job&job='+job_name;
			//alert(pars);
			var arg		= job_name+'|'+currentPath;
			var myAjax	= new Ajax.Request(
				strURL, 
				{
					parameters: pars, 
					onSuccess: updateRemove_job
				},arg);
		}
}
function updateRemove_job(originalRequest,json,arg)
{
	var responseText	= originalRequest.responseText;
	var argsplit		= arg.split("|");
	
	if(argsplit[0]!='work_history_1' && responseText=='true')
		{
			$(argsplit[0]).style.display	= 'none';
			result	= 'Changes saved.';
		}
	else if(responseText=='true')
		result	= 'Changes saved.';
	else
		result	= 'Changes has not been saved.';
	$('work_history_result').innerHTML	=	'<table width="100%" class="splborder"><tr><td height="41" bordercolor="#ec8a00" bgcolor="#feffcf"><span class="blktitle">'+result+'</span><br /></td></tr></table>';						
	$('work_main_div').style.display	= 'none';
	var t= setTimeout("showRemoveWorkResult(\""+argsplit[1]+"\")",2000);
}
//--------------------------------------------------------------------------------------------------
function showRemoveWorkResult(curPath)
{
	//alert(curPath);
	window.location	= curPath+"index.php/editprofile/work";
}
//to delete the user from given network
function leave_network(currentPath)
{
	var strURL		= currentPath+'index.php/ajax';
	var network_id	= $('leave_network_id').value;
	var pars		= 'action=leave_network&nw_id='+network_id;
	//alert(pars);
	var myAjax	= new Ajax.Request(
		strURL, 
		{
			parameters: pars, 
			onSuccess: updateLeave_network
		});
}
//to cancel the request from given network
function cancel_network(currentPath)
{
	var strURL		= currentPath+'index.php/ajax';
	var network_id	= $('cancel_network_id').value;
	var pars		= 'action=cancel_network&nw_id='+network_id;
	//alert(pars);
	var myAjax	= new Ajax.Request(
		strURL, 
		{
			parameters: pars, 
			onSuccess: updateLeave_network
		});
}
//to resend network confirmation
function resend_network_confirm(network_id,currentPath)
{
	var strURL		= currentPath+'index.php/ajax';
	var pars		= 'action=resend_network_confirmation&nw_id='+network_id;
	//alert(pars);
	var myAjax	= new Ajax.Request(
		strURL, 
		{
			parameters: pars, 
			onSuccess: updateLeave_network
		});
}

function updateLeave_network(originalRequest)
{
	var responseText	= originalRequest.responseText;
	//alert(responseText);
	$('network_left_column').innerHTML	= responseText;
	$('confirm_dialog').style.display	= 'none';
	$('cancel_dialog').style.display	= 'none';
}

//to join the network
function join_networks(network_id,currentPath)
{
	var strURL		= currentPath+'index.php/ajax';
	var email		= $('join_network_email').value;
	//alert(email);
	if(email=='' || (email!='' && !isEmail(email)))
		{
			$('join_network_result').style.display	= 'block';
			$('join_network_result').innerHTML		= "<font color='red'>Please give the valid email!</font>";
		}
	else
		{
			$('join_network_result').style.display	= 'none';
			$('join_network_result').innerHTML		= '';
			
			var pars		= 'action=join_network&nw_id='+network_id+'&email='+email;
			//alert(pars);
			var myAjax	= new Ajax.Request(
				strURL, 
				{
					parameters: pars, 
					onSuccess: updateJoin_networks
				},email);
		}
}

function updateJoin_networks(originalRequest,json,email)
{
	var responseText	= originalRequest.responseText;
	//alert(responseText);
	var splitResult		= responseText.split('|');
	//alert(splitResult[1]);
	if(splitResult[0]=='1')
		{
			$('join_network_result').style.display	= 'block';
			$('join_network').style.display			= 'none';
			$('join_network_result').innerHTML		= splitResult[1];
		}
	else
		{
			$('join_network_result').style.display	= 'block';
			$('join_network').style.display			= 'block';
			$('join_network_result').innerHTML		= splitResult[1];
			$('join_network_email').value			= email;
		}
}

//invite friends
function invite_friends(currentPath)
{
	var strURL		= currentPath+'index.php/ajax';
	var to_mail		= $('invite_to_address').value
	var msg			= $('invite_msg').value;
	var pars		= 'action=invite_friend&to_mail='+to_mail+'&msg='+msg;
	//alert(pars);
	$('ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		strURL, 
		{
			parameters: pars, 
			onSuccess: updateInvite_friends
		});
}

function updateInvite_friends(originalRequest)
{
	var responseText	= originalRequest.responseText;
	//alert(responseText);
	if(responseText!='')
		{
			$('invite_result').style.display		= 'block';
			$('invite_result').innerHTML			= responseText;
			$('ajax_loader').style.display			= 'none';
			$('invite_friends_div').style.display	= 'none';
		}
	else
		{
			$('invite_result').innerHTML			= 'Plese give email ids to invite friends';
			$('ajax_loader').style.display			= 'none';
		}
}

//add friend to current users friends list
function addfriend(currentPath,friendId)
{
	var strURL				= currentPath+'index.php/ajax';
	var captchaResponse		= $('captcha_response').value
	var captchaChallengeCode= $('captcha_challenge_code').value;
	if($('addMsgBox').style.display=='block')
		var msg	= $('message').value;
	else
		var msg	= '';
	var pars		= 'action=add_friend&captchaResponse='+captchaResponse+'&captchaChallengeCode='+captchaChallengeCode+'&friendId='+friendId+'&msg='+msg;
	//alert(pars);
	$('ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		strURL, 
		{
			parameters: pars, 
			onSuccess: updateAddfriend
		});
	$('add_friend_security').style.display	= 'none';
	('add_friend_error').style.display		= 'none';
}

function updateAddfriend(originalRequest)
{
	var responseText	= originalRequest.responseText;
	//alert(responseText);
	if(responseText=='success')
		{
			$('add_friend_success').style.display	= 'block';
			$('add_friend_section').style.display			= 'none';
		}
	else if(responseText=='security')
		{
			$('add_friend_section').style.display	= 'block';
			$('add_friend_security').style.display	= 'block';
			$('add_friend_success').style.display	= 'none';
			$('add_friend_error').style.display		= 'none';
		}
	else
		{
			$('add_friend_success').style.display	= 'none';
			$('add_friend_section').style.display	= 'block';
			$('add_friend_error').style.display		= 'block';
			$('add_friend_error').innerHTML			= responseText;
		}
	$('ajax_loader').style.display	= 'none';
}
//to select event sub category while changing event category
function eventCategorySelect(currentPath)
{

		//alert(currentPath);
		var url		= currentPath+'index.php/ajax';
		var category	= $('event_category').value;
		//alert(category);

		$('event_type_ajax_loader').style.display	= 'block';
		var pars	= 'action=eventCategory&category='+category;
		//alert(pars);
		var myAjax	= new Ajax.Request(
			url, 
			{
				parameters: pars, 
				onSuccess: updateEventCategorySelect
			});
}

function updateEventCategorySelect(originalRequest)
{
	var responseText			= originalRequest.responseText;
	//alert(responseText);
	$('div_event_sub_category').innerHTML= responseText;
	$('event_type_ajax_loader').style.display	= 'none';
}
//reset the hostgroup select box
//while changing host text box on event creation first step
function eventHostChange(currentPath)
{
		//alert(currentPath);
		//$('event_host_ajax_loader').style.display	= 'block';
		var url		= currentPath+'index.php/ajax';
		var pars	= 'action=eventHostChange';
		//alert(pars);
		var myAjax	= new Ajax.Request(
			url, 
			{
				parameters: pars, 
				onSuccess: updateEventHostChange
			});
}
function updateEventHostChange(originalRequest)
{
	var responseText			= originalRequest.responseText;
	//alert(responseText);
	$('div_event_host_group').innerHTML= responseText;
	//$('event_host_ajax_loader').style.display	= 'none';
}
//function to show the event guest
function showGuest(eventId,currentPath)
{
	var url			= currentPath+'index.php/ajax';
	var listType	= $('list_type').value;
	var pars		= 'action=listGuest&listType='+listType+'&eid='+eventId;
	//alert(pars);
	$('ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateShowGuest
		});
}
function updateShowGuest(originalRequest)
{
	var responseText			= originalRequest.responseText;
	//alert(responseText);
	$('guest_list').innerHTML	= responseText;
	$('ajax_loader').style.display	= 'none';
}
//send invitation to group members
//to join this event
function inviteGroup(eventId,groupId,currentPath)
{
	var url		= currentPath+'index.php/ajax';
	var tolist	= $('email_addresses').value;
	var pars	= 'action=inviteGroup&eid='+eventId+'&gid='+groupId;
	//alert(pars);
	$('ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateInviteGroup
		});
}
function updateInviteGroup(originalRequest)
{
	var responseText					= originalRequest.responseText;
	//alert(responseText);
	$('friend_invite_form').style.display	= 'none';
	$('invite_result_main').style.display	= 'block';
	$('invite_result_sub').innerHTML		= responseText;
	$('friend_invite_form').innerHTML		= '';
	$('ajax_loader').style.display			= 'none';
}

//show interface to cancel/remove the membershop from the event
function cancelEvent(eventId,currentPath,action,eventType)
{
	var url		= currentPath+'index.php/ajax';
	if(action=='cancel')
		action	= 'cancelEvent';
	else
		action	= 'removeEvent';
	var pars	= 'action='+action+'&eid='+eventId+'&eventType='+eventType;
	//alert(pars);
	$('ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateInviteGroup
		},eventId);
}
function updateInviteGroup(originalRequest,json,eventId)
{
	var responseText						= originalRequest.responseText;
	$('ajax_content_'+eventId).style.display= 'block';
	$('ajax_content_'+eventId).innerHTML	= responseText;
	$('ajax_loader').style.display			= 'none';
	
}
//show interface to change RSVP status of user for an event
function changeRSVP(eventId,currentPath,who)
{
	var url		= currentPath+'index.php/ajax';
	var pars	= 'action=changeRsvp&eid='+eventId+'&who='+who;
	//alert(pars);
	$('ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateChangeRSVP
		},eventId);
}
function updateChangeRSVP(originalRequest,json,eventId)
{
	var responseText						= originalRequest.responseText;
	$('ajax_content_'+eventId).style.display= 'block';
	$('ajax_content_'+eventId).innerHTML	= responseText;
	$('ajax_loader').style.display			= 'none';
	
}
//process the rsvp status changes
function doRSVP(eventId,rsvpId,currentPath)
{
	var rsvpValue = Form.getInputs('rsvp_form','radio',rsvpId).find(function(radio) { return radio.checked; }).value;
	//$('rsvp_status_'+ eventId).innerHTML		= typeValue;
	//$('ajax_content_'+ eventId).style.display	= 'none';
	
	var url		= currentPath+'index.php/ajax';
	var pars	= 'action=doRsvp&eid='+eventId+'&rsvpStatus='+rsvpValue+'&rsvpId='+rsvpId;
	//alert(pars);
	$('ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateDoRSVP
		},eventId);
}
function updateDoRSVP(originalRequest,json,eventId)
{
	var responseText							= originalRequest.responseText;
	$('rsvp_status_'+ eventId).innerHTML		= responseText;
	$('ajax_content_'+ eventId).style.display	= 'none';
	$('ajax_loader').style.display				= 'none';
}
//remove the event
function doRemoveEvent(eventId,currentPath,eventType)
{
	var url		= currentPath+'index.php/ajax';
	var pars	= 'action=doRemoveEvent&eid='+eventId+'&eventType='+eventType;
	//alert(pars);
	$('ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateDoRemoveEvent
		});
}
function updateDoRemoveEvent(originalRequest)
{
	var responseText					= originalRequest.responseText;
	$('main_events_list').innerHTML		= responseText;
	$('ajax_loader').style.display		= 'none';
}
//process the rsvp status changes for event main page
function doRsvpMain(eventId,rsvpId,currentPath)
{
	var rsvpValue = Form.getInputs('rsvp_form','radio',rsvpId).find(function(radio) { return radio.checked; }).value;
	//$('rsvp_status_'+ eventId).innerHTML		= typeValue;
	//$('ajax_content_'+ eventId).style.display	= 'none';
	
	var url		= currentPath+'index.php/ajax';
	var pars	= 'action=doRsvpMain&eid='+eventId+'&rsvpStatus='+rsvpValue+'&rsvpId='+rsvpId;
	//alert(pars);
	$('rsvp_ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateDoRsvpMain
		});
}
function updateDoRsvpMain(originalRequest)
{
	var responseText						= originalRequest.responseText;
	$('main_rsvp_content').innerHTML		= responseText;
	$('rsvp_ajax_loader').style.display		= 'none';
}
//send message to friend or guests of an event
function sendMessage(actionId,actionType,currentPath)
{
	var toname	= $('mailTo').value;
	var subject	= $('mailSubject').value;
	var content	= $('mailContent').value;
	
	var pars	='action=sendMessage&subject='+subject+'&content='+content+'&toname='+toname;
	if(actionType=='event')
		{
			var actionStatus= $('attendees').value;
			pars		+= '&actionId='+actionId+'&actionType='+actionType+'&actionStatus='+actionStatus;
		}
	else if(actionType=='group')
		pars		+= '&actionId='+actionId+'&actionType='+actionType;
	var url		= currentPath+'index.php/ajax';
	
	//alert(pars);
	$('ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateSendMessage
		});
}
function updateSendMessage(originalRequest)
{
	var responseText				= originalRequest.responseText;
	//alert(responseText);
	var splitRes					= responseText.split("|");
	if(splitRes[1]=='yes')
		{
			$('mailTo').value		= '';
			$('mailSubject').value	= '';
			$('mailContent').value	= '';
		}
	$('send_result').innerHTML		= '<table width="100%" class="splborder"><tr><td height="41" bordercolor="#ec8a00" bgcolor="#feffcf"><span class="blktitle">'+splitRes[0]+'</span><br /></td></tr></table>';
	$('ajax_loader').style.display	= 'none';
}
//remove membership from event on event homepage
function removeEventHome(eventId,currentPath)
{
	var url		= currentPath+'index.php/ajax';
	var pars	= 'action=removeEventHome&eid='+eventId;
	//alert(pars);
	$('ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateRemoveEventHome
		},currentPath);
}
function updateRemoveEventHome(originalRequest,json,currentPath)
{
	var responseText					= originalRequest.responseText;
	$('ajax_loader').style.display		= 'none';
	$('view_event_main_div').style.display	= 'none';
	if(responseText)
	window.location	= currentPath+'index.php/events/show/upcoming';
}
//process the rsvp status changes for event main page
function changeUserStatus(userId,currentPath,dowhat)
{
	var url		= currentPath+'index.php/ajax';
	if(dowhat=='store')
		var status	= $('screenStatusSelect').value;
	var pars	= 'action=changeUserStatus&uid='+userId+'&status='+status+'&dowhat='+dowhat;
	//alert(pars);
	$('status_ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateChangeUserStatus
		});
}
function updateChangeUserStatus(originalRequest)
{
	var responseText							= originalRequest.responseText;
	//alert(responseText);
	$('screen_status_div').style.display		= 'block';
	$('screen_status_div').innerHTML			= responseText;
	$('status_ajax_loader').style.display		= 'none';
	$('screen_status_change_div').style.display	= 'none';
}
//remove membership from event on event homepage
//post comment
function postComment(postedBy,postedTo,currentPath)
{
	var url		= currentPath+'index.php/ajax';
	var postedValue	= $('comment').value;
	var pars	= 'action=postComment&by='+postedBy+'&to='+postedTo+'&content='+postedValue;
	//alert(pars);
	$('post_comment_ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updatePostComment
		},currentPath);
}
function updatePostComment(originalRequest)
{
	var responseText						= originalRequest.responseText;
	$('post_comment_ajax_loader').style.display= 'none';
	$('comment_div').innerHTML			= responseText;
}
// post comment ends
function showPostResult()
{
	$('wall_post_result').style.display= 'none';
}
//prepare college netwoks
function showCollegeNetworks(currentPath)
{
	var url		= currentPath+'index.php/ajax';
	var region	= $('college_region_select').value;
	var pars	= 'action=showCollegeNetworks&region='+region;
	//alert(pars);
	$('ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateShowCollegeNetworks
		});
}
function updateShowCollegeNetworks(originalRequest)
{
	var responseText						= originalRequest.responseText;
	//alert(responseText);
	$('college_network_div').style.display	= 'block';
	$('college_network_div').innerHTML		= responseText;
	$('ajax_loader').style.display			= 'none';
}
//prepare school netwoks
function showSchoolNetworks(currentPath)
{
	var url		= currentPath+'index.php/ajax';
	var region	= $('school_region_select').value;
	var pars	= 'action=showSchoolNetworks&region='+region;
	//alert(pars);
	$('ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateShowSchoolNetworks
		});
}
function updateShowSchoolNetworks(originalRequest)
{
	var responseText						= originalRequest.responseText;
	//alert(responseText);
	$('school_network_div').style.display	= 'block';
	$('school_network_div').innerHTML		= responseText;
	$('ajax_loader').style.display			= 'none';
}

//generate security check image, and assign it to the given div
function generateSecurityImage(currentPath,divId)
{
	var url		= currentPath+'index.php/ajax';
	var pars	= 'action=generateScurityImage';
	$('security_ajax_loader').style.display	= 'block';
	var arg	= currentPath+'|'+divId;
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateGenerateScurityImage
		},arg);
}
function updateGenerateScurityImage(originalRequest,json,arg)
{
	var responseText		= originalRequest.responseText;
	var splitText			= responseText.split('|');
	var argSplit			= arg.split('|');
	var captchImage			= splitText[0];
	$(argSplit[1]).innerHTML		= captchImage;
	$('randWord').value		= splitText[1];
	$('security_ajax_loader').style.display	= 'none';
}

//cancel the event, and email to the event guests
function doCancelEvent(eventId,currentPath,eventType,cancelCommentId)
{
	var url				= currentPath+'index.php/ajax';
	var	cancelComment	= $(cancelCommentId).value;
	var pars			= 'action=doCancelEvent&eid='+eventId+'&eventType='+eventType+'&cancelComment='+cancelComment;
	$('ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateDoCancelEvent
		});
}
function updateDoCancelEvent(originalRequest)
{
	var responseText					= originalRequest.responseText;
	$('main_events_list').innerHTML		= responseText;
	$('ajax_loader').style.display		= 'none';
}
// filter the discussion board in network
function discussionFilter(userId,currentPath,networkId,filter)
{
	var selectedFilter	= $('filter').value;
	var url				= currentPath+'index.php/ajax';
	var pars			= 'action=discussionFilter&uid='+userId+'&networkId='+networkId+'&selectedFilter='+selectedFilter;
	$('ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateDiscussionFilter
		});
}

function updateDiscussionFilter(originalRequest)
{
	var responseText					= originalRequest.responseText;
	$('ajax_loader').style.display		= 'none';
	$('filter_div').innerHTML			= responseText;
}
//sort the discussion board in networks
function discussionSort(userId,currentPath,networkId,filter,sorter)
{
	var selectedFilter	= $('filter').options[$('filter').options.selectedIndex].value;
	var selectedSorter	= $('sorter').value;
	var url				= currentPath+'index.php/ajax';
	var pars			= 'action=discussionSort&uid='+userId+'&networkId='+networkId+'&selectedFilter='+selectedFilter+'&selectedSorter='+selectedSorter;
	$('ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateDiscussionSort
		});
}
function updateDiscussionSort(originalRequest)
{
	var responseText					= originalRequest.responseText;
	$('ajax_loader').style.display		= 'none';
	$('filter_div').innerHTML			= responseText;
}
// search for discussion board in network
function discussionSearch(userId,currentPath,networkId)
{
	var url			= currentPath+'index.php/ajax';
	var searchText	= $('search').value;
	var pars		= 'action=discussionSearch&uid='+userId+'&networkId='+networkId+'&searchTxt='+searchText;
	$('ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateDiscussionSearch
		});
}
function updateDiscussionSearch(originalRequest)
{
	var responseText					= originalRequest.responseText;
	$('ajax_loader').style.display		= 'none';
	$('filter_loader').style.display	= 'none';
	$('sorter_loader').style.display	= 'none';
	$('search_loader').style.display	= 'block';
	$('filter_div').style.display		= 'block';
	$('filter_div').innerHTML			= responseText;
}


function showDiscussionSearch(userId,currentPath,networkId,basicSearch)
{
	var url				= currentPath+'index.php/ajax';
	var selectedFilter 	= $('basicSearch').value;
	var pars			= 'action=discussionFilter&uid='+userId+'&networkId='+networkId+'&selectedFilter='+selectedFilter;
	$('ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateShowDiscussionSearch
		});

}
function updateShowDiscussionSearch(originalRequest)
{
	var responseText					= originalRequest.responseText;
	$('ajax_loader').style.display		= 'none';
	$('filter_loader').style.display	= 'block';
	$('sorter_loader').style.display	= 'block';
	$('search_loader').style.display	= 'none';
	$('filter_div').style.display		= 'block';
	$('filter_div').innerHTML			= responseText;
}


//post wall for user in profile
function post2Profile(postedBy,postedTo,currentPath,type)
{
	var url		= currentPath+'index.php/ajax';
	var postedValue	= $('post_content').value;
	var pars	= 'action=postToProfile&by='+postedBy+'&to='+postedTo+'&content='+postedValue+'&type='+type;
	$('wall_post_ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updatePost2Profile
		},currentPath);
}
function updatePost2Profile(originalRequest)
{
	var responseText						= originalRequest.responseText;
	$('wall_post_ajax_loader').style.display= 'none';
	$('post_content_div').innerHTML			= responseText;
}
// post wall for region in network
function post2NetworkProfile(postedBy,postedTo,currentPath,action)
{
	var url			= currentPath+'index.php/ajax';
	var postedValue	= $('post_content').value;
	var pars		= 'action='+action+'&by='+postedBy+'&to='+postedTo+'&content='+postedValue;
	$('wall_post_ajax_loader').style.display	= 'block';
	var myAjax		= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updatePost2NetworkProfile
		},currentPath);
}
function updatePost2NetworkProfile(originalRequest)
{
	var responseText						= originalRequest.responseText;
	$('wall_post_ajax_loader').style.display= 'none';
	$('post_content_div').innerHTML			= responseText;
}
//type your report about a particular person in network
function reportNetUser(userId,reply_for_id,currentPath,type)
{
	var url			= currentPath+'index.php/ajax';
	var off_code	= $('off_code').value;
	var comment		= $('comment').value;
	if(comment=="")
	{
	$('result_div').style.display	= 'block';
	$('result_div').innerHTML		= '<TABLE class="splborder" align="center"><TR><TD bordercolor="#ec8a00" bgcolor="#FFD2D2">Please provide an explanation for this report of offensive content.</td></tr></table>';
	}
	else
	{
	var pars	= 'action=reportNetUser&uid='+userId+'&reply_for_id='+reply_for_id+'&type='+type+'&off_code='+off_code+'&comment='+comment;
	$('report_ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateReportNetworkUser
		});
	}
}
function updateReportNetworkUser(originalRequest)
{
	var responseText							= originalRequest.responseText;
	$('report_ajax_loader').style.display		= 'none';
	$('report_div').style.display				= 'none';
	$('result_div').style.display				= 'block';
	$('result_div').innerHTML					= responseText;
}

function networkWall(postedBy,postedTo,currentPath)
{
	var url			= currentPath+'index.php/ajax';
	var comment		= $('wall_text').value;
	var pars		= 'action=networkWall&uid='+postedBy+'&postedTo='+postedTo+'&comment='+comment;
	$('post_ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateNetworkWall
		});
}
function updateNetworkWall(originalRequest)
{
	var responseText						= originalRequest.responseText;
	$('post_ajax_loader').style.display		= 'none';
	$('post_wall_content').style.display	= 'none';
	$('wall_result_div').style.display		= 'block';
	$('wall_result_div').innerHTML			= responseText;
}

function networkSearch(userId,currentPath,networkId,search_dropdown)
{
	var url				= currentPath+'index.php/ajax';
	var selectedValue	= $('search_dropdown').options[$('search_dropdown').options.selectedIndex].value;
	var searchText		= $('search').value;
	var pars			= 'action=networkSearch&uid='+userId+'&networkId='+networkId+'&searchTxt='+searchText+'&selectedValue='+selectedValue;
	$('search_ajax_loader').style.display	= 'block';
	var myAjax	= new Ajax.Request(
		url, 
		{
			parameters: pars, 
			onSuccess: updateNetworkSearch
		});
}
function updateNetworkSearch(originalRequest)
{
	var responseText						= originalRequest.responseText;
	$('search_ajax_loader').style.display	= 'none';
	$('search_content').style.display		= 'none';
	$('search_div').innerHTML				= responseText;
}
