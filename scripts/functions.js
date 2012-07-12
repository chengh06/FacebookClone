// JavaScript Document
function lifestage_choosen()
{
	var lsv	= $('lifestage').value;
	if(lsv == '1')
		{
			$('college_status_col_1').style.display	= 'block';
			$('college_status_col_2').style.display	= 'block';
			$('college_year_col_1').style.display	= 'block';
			$('college_year_col_2').style.display	= 'block';
			
			$('high_school_network_col_1').style.display= 'none';
			$('high_school_network_col_2').style.display= 'none';
			$('high_school_year_col_1').style.display	= 'none';
			$('high_school_year_col_2').style.display	= 'none';
		}
	else if(lsv == '3')
		{
			$('high_school_network_col_1').style.display= 'block';
			$('high_school_network_col_2').style.display= 'block';
			$('high_school_year_col_1').style.display	= 'block';
			$('high_school_year_col_2').style.display	= 'block';
			
			$('college_status_col_1').style.display	= 'none';
			$('college_status_col_2').style.display	= 'none';
			$('college_year_col_1').style.display	= 'none';
			$('college_year_col_2').style.display	= 'none';
		}
	else
		{
			$('high_school_network_col_1').style.display= 'none';
			$('high_school_network_col_2').style.display= 'none';
			$('high_school_year_col_1').style.display	= 'none';
			$('high_school_year_col_2').style.display	= 'none';
			$('college_status_col_1').style.display	= 'none';
			$('college_status_col_2').style.display	= 'none';
			$('college_year_col_1').style.display	= 'none';
			$('college_year_col_2').style.display	= 'none';
		}
}
function schoolstatus_choosen()
{
	var ssv	= $('schoolStatus').value;
	if(ssv=='4' || ssv=='5')
		$('college_year').disabled=true;
	else
		$('college_year').disabled=false;
}

//function to select,deslect message
function sel_message(msgName,len,folder)
{
	//window.alert(document.messages.mess_0.checked);
	//formEle	= $F($A('msgForm'));
	
	//var allNodes	= Array();
	//alert(folder);
	//alert(msgName);
	allNodes = $A($('msgForm'));
	if(msgName=='all' || msgName=='none')
	{
		if(folder=='trash')
			selectAllNoneTrash(msgName,len);
		else
			selectAllNone(msgName,len);
		$('message_action_option').innerHTML	= '<select name="message_action" id="message_action" onchange="javascript:messageProcess(\''+folder+'\',\''+baseUrl+'\')" ><option value="select_action">Select Action</option><option value="mark_unread">MarkAsUnread</option><option value="mark_read">MarkAsRead</option><option value="move_to_trash">Delete</option></select>';
	}
	else
	{
		if(msgName=='read' && folder=='inbox')
			{
				msgName	= 'mess_i_r_';
				unCheck	= 'mess_i_u_';
			}
		else if(msgName=='read' && folder=='sent')
			{
				msgName	= 'mess_s_r_';
				unCheck	= 'mess_s_u_';
			}
		else if(msgName=='unread' && folder=='inbox')
			{
				msgName	= 'mess_i_u_';
				unCheck	= 'mess_i_r_';
			}
		else if(msgName=='unread' && folder=='sent')
			{
				msgName	= 'mess_s_u_';
				unCheck	= 'mess_s_r_';
			}
		
		selected=false;
		for(i = 0; i < allNodes.length; i++) 
		{
			//do something to each form field
			for (j = 1; j <= len; j++)
				{
					if(allNodes[i].name	== unCheck+j)
						$(unCheck+j).checked = false;
					else if(allNodes[i].name	== msgName+j)
					{
						if($(msgName+j).checked)
							$(msgName+j).checked = false;
						else
							$(msgName+j).checked = true;
						selected=true;
					}
				}
		}
		if(!selected)
		{
			selectAllNone('none',len);
			$('message_menu').style.display	= 'none';
		}
		if(msgName=='mess_i_u_' || msgName=='mess_s_u_')
			$('message_action_option').innerHTML	= '<select name="message_action" id="message_action" onchange="javascript:messageProcess(\''+folder+'\',\''+baseUrl+'\')" ><option value="select_action">Select Action</option><option value="mark_read">MarkAsRead</option><option value="move_to_trash">Delete</option></select>';
		else
			$('message_action_option').innerHTML	= '<select name="message_action" id="message_action" onchange="javascript:messageProcess(\''+folder+'\',\''+baseUrl+'\')" ><option value="select_action">Select Action</option><option value="mark_unread">MarkAsUnread</option><option value="move_to_trash">Delete</option></select>';
	}

}

function selectAllNone(msgName,len)
{
	//alert(msgName);
	allNodes = $A($('msgForm'));
	if(msgName=='all' || msgName=='none')
	{
		
		if(msgName=='all')
			{
				status	= true;
			}
		else
			{
				status	= false;
			}
		for(i = 0; i < allNodes.length; i++) 
		{
			//do something to each form field
			//alert(allNodes[i].name);
			for (j = 1; j <= len; j++)
			{
				var msg1='mess_i_r_' + j;
				if(allNodes[i].name	==msg1)
					$(msg1).checked	= status;
				var msg2='mess_i_u_' + j;
				if(allNodes[i].name	==msg2)
					$(msg2).checked	= status;
				var msg3='mess_s_r_' + j;
				if(allNodes[i].name	==msg3)
					$(msg3).checked	= status;
				var msg4='mess_s_u_' + j;
				if(allNodes[i].name	==msg4)
					$(msg4).checked	= status;
			}
		}
	}
}
function selectAllNoneTrash(msgName,len)
{
	allNodes = $A($('msgForm'));
	if(msgName=='all' || msgName=='none')
	{
		
		if(msgName=='all')
			{
				status	= true;
			}
		else
			{
				status	= false;
			}
		
		for(i = 0; i < allNodes.length; i++) 
		{
			//do something to each form field
			//alert(allNodes[i].name);
			for (j = 1; j <= len; j++)
			{
				var msg1='mess_i_' + j;
				if(allNodes[i].name	==msg1)
					$(msg1).checked	= status;
				var msg2='mess_s_' + j;
				if(allNodes[i].name	==msg2)
					$(msg2).checked	= status;
			}
		}
	}
}


//function to show access related message on groups(step1)
function toggle_access_str(str)
{
	if(str=='photos')
		{
			if($('show_photos').checked)
				$('show_photos_suboptions').style.display	= 'block'
			else
				$('show_photos_suboptions').style.display	= 'none'
		}
	
	if($('show_message_board').checked && $('show_wall').checked && $('show_photos').checked)
		{
			var	public_access	= 'Anyone can join and invite others to join. Anyone can see the group information, the discussion board, the wall, and photos.';
			var closed_access	= 'Administrative approval is required for new members to join. Anyone can see the group information, but only members can see the discussion board, the wall, and photos.';
			var private_access	= 'The group will not appear in search results or in the profiles of its members. Membership is by invitation only, and only members can see the group information, the discussion board, the wall, and photos.';
		}
	else if(!$('show_message_board').checked && !$('show_wall').checked && !$('show_photos').checked)
		{
			var	public_access	= 'Anyone can join and invite others to join. Anyone can see the group information';
			var closed_access	= 'Administrative approval is required for new members to join. Anyone can see the group information.';
			var private_access	= 'The group will not appear in search results or in the profiles of its members. Membership is by invitation only, and only members can see the group information.';
		}
	else if(!$('show_message_board').checked && $('show_wall').checked && $('show_photos').checked)
		{
			var	public_access	= 'Anyone can join and invite others to join. Anyone can see the group information, the wall, and photos.';
			var closed_access	= 'Administrative approval is required for new members to join. Anyone can see the group information, but only members can see the wall, and photos.';
			var private_access	= 'The group will not appear in search results or in the profiles of its members. Membership is by invitation only, and only members can see the group information, the wall, and photos.';
		}
	else if(!$('show_message_board').checked && !$('show_wall').checked && $('show_photos').checked)
		{
			var	public_access	= 'Anyone can join and invite others to join. Anyone can see the group information and photos.';
			var closed_access	= 'Administrative approval is required for new members to join. Anyone can see the group information, but only members can see the photos.';
			var private_access	= 'The group will not appear in search results or in the profiles of its members. Membership is by invitation only, and only members can see the group information, and photos.';
		}
	else if($('show_message_board').checked && !$('show_wall').checked && !$('show_photos').checked)
		{
			var	public_access	= 'Anyone can join and invite others to join. Anyone can see the group information, and the discussion board.';
			var closed_access	= 'Administrative approval is required for new members to join. Anyone can see the group information, but only members can see the discussion board.';
			var private_access	= 'The group will not appear in search results or in the profiles of its members. Membership is by invitation only, and only members can see the group information, and the discussion board.';
		}
	else if(!$('show_message_board').checked && !$('show_wall').checked && $('show_photos').checked)
		{
			var	public_access	= 'Anyone can join and invite others to join. Anyone can see the group information, and photos.';
			var closed_access	= 'Administrative approval is required for new members to join. Anyone can see the group information, but only members can see the photos.';
			var private_access	= 'The group will not appear in search results or in the profiles of its members. Membership is by invitation only, and only members can see the group information, and photos.';
		}
	else if(!$('show_message_board').checked && $('show_wall').checked && !$('show_photos').checked)
		{
			var	public_access	= 'Anyone can join and invite others to join. Anyone can see the group information, and the wall.';
			var closed_access	= 'Administrative approval is required for new members to join. Anyone can see the group information, but only members can see the wall.';
			var private_access	= 'The group will not appear in search results or in the profiles of its members. Membership is by invitation only, and only members can see the group information, and the wall.';
		}
	
	$('public_access').innerHTML	= public_access;
	$('closed_access').innerHTML	= closed_access;
	$('private_access').innerHTML	= private_access;
}

//rate video
function showStars(starId,currentPath)
{
	$(starId).src= currentPath+"application/images/star_y.gif";
	if(starId=='star_1')
		rateTip='Poor';
	else if(starId=='star_2')	
		rateTip='Nothing special';
	else if(starId=='star_3')
		rateTip='Worth watching';
	else if(starId=='star_4')
		rateTip='Pretty cool';
	else if(starId=='star_5')
		rateTip='Awesome!';
	$('ratingMessage').innerHTML = rateTip;	
}
function clearStars(starId,currentPath)
{
	$(starId).src					= currentPath+"application/images/star.gif";
	$('ratingMessage').innerHTML	= 'Rate this video';	
}


//function to chech the text limit of the given field and enable error messaga
//arg1	: field id
//arg2	: text limit
//arg3	: div id or id in which the error msg defined
function textLimit(field_id,limit,div_id)
{
	var field_content	= $(field_id).value;
	if(field_content.length>limit)
		{
			$(field_id).value		= field_content.substring(0,limit);
			$(div_id).style.display	= 'block';
		}
	else if(field_content.length<=limit)
		$(div_id).style.display	= 'none';
}

//function to add concentration, while creating education profile
function addConcentration(cons_name)
{
	var cons_value	= $(cons_name+'_concentration_value_count').value;
	cons_value	= parseInt(cons_value)+1;
	if(cons_value<4)
		{
			$(cons_name+'_concentration_value_count').value= cons_value;
			$(cons_name+'_concentration'+cons_value).style.display='block';
			if(cons_value==3)
				$(cons_name+'_concentration_adder').style.display='none';
		}
}

//function to change school status, while creating education profile
function schoolChangeStatus(edu_name)
{
	/*if($(edu_name+'_school_type').value=='college')
		{
			$(edu_name+'_school').style.display='none';
			$(edu_name+'_college').style.display='block';
		}
	else
		{
			$(edu_name+'_school').style.display='block';
			$(edu_name+'_college').style.display='none';
		}*/
	//alert($('education_'+edu_name+'_school_type').value);
	if($(edu_name+'_school_type').value=='gradschool')
		$(edu_name+'_degree').style.display='block';
	else
		$(edu_name+'_degree').style.display='none';
	
}
//function add another school
function add_school()
{
	var section_value	= $('school_section_count').value;
	section_value	= parseInt(section_value)+1;
	$('school_section_count').value	= section_value;
	if(section_value<5)
		{
			$('education_'+section_value).style.display='block';
			if(section_value==4)
				$('add_link').style.display='none';
		}
}
///function to change state regarding the selected countries
function selectState(work_name)
{
	//alert(work_name);
	var country	= $(work_name+'_country_select').value;
	//alert(country);
	if(country=='US')
		{
			$(work_name+'_state_label').style.display	= 'block';
			$(work_name+'_province_label').style.display= 'none';
			$(work_name+'_country_label').style.display	= 'none';
			$(work_name+'_state_ca').style.display		= 'none';
			$(work_name+'_state_us').style.display		= 'block';
			$(work_name+'_country').style.display		= 'block';
		}
	else if(country=='CA')
		{
			$(work_name+'_state_label').style.display	= 'none';
			$(work_name+'_province_label').style.display= 'block';
			$(work_name+'_country_label').style.display	= 'none';
			$(work_name+'_state_ca').style.display		= 'block';
			$(work_name+'_state_us').style.display		= 'none';
			$(work_name+'_country').style.display		= 'block';
		}
	else
		{
			$(work_name+'_country').style.display		= 'block';
			$(work_name+'_country_label').style.display	= 'block';
			
			$(work_name+'_state_label').style.display	= 'none';
			$(work_name+'_province_label').style.display= 'none';
			$(work_name+'_state_ca').style.display		= 'none';
			$(work_name+'_state_us').style.display		= 'none';
			
		}
}

function searchState(work_name)
{
	//alert(work_name);
	var country	= $('country_select').value;
	//alert(country);
	if(country=='US')
		{
			$('state_label').style.display	= 'block';
			$('province_label').style.display= 'none';
			$('country_label').style.display	= 'none';
			$('state_ca').style.display		= 'none';
			$('state_us').style.display		= 'block';
			$('country').style.display		= 'block';
		}
	else if(country=='CA')
		{
			$('state_label').style.display	= 'none';
			$('province_label').style.display= 'block';
			$('country_label').style.display	= 'none';
			$('state_ca').style.display		= 'block';
			$('state_us').style.display		= 'none';
			$('country').style.display		= 'block';
		}
	else
		{
			$('country').style.display		= 'block';
			$('country_label').style.display	= 'block';
			
			$('state_label').style.display	= 'none';
			$('province_label').style.display= 'none';
			$('state_ca').style.display		= 'none';
			$('state_us').style.display		= 'none';
			
		}
}



//function to enable/disable end work span
function toggleEndWorkSpan(span_name)
{
	res	= $(span_name+'current').checked;
	if(res)
		{
			$(span_name+'endspan').style.display	= 'none';
			$(span_name+'present').style.display	= 'block';
		}
	else
		{
			$(span_name+'endspan').style.display	= 'block';
			$(span_name+'present').style.display	= 'none';
		}
		
}

//function add another job
function add_job()
{
	var section_value	= $('work_section_count').value;
	section_value	= parseInt(section_value)+1;
	$('work_section_count').value	= section_value;
	if(section_value<5)
		{
			$('work_history_'+section_value).style.display='block';
			if(section_value==4)
				$('add_link').style.display='none';
		}
}

//function add another screen in contact profile page
function add_screen()
{
	var section_value	= $('contact_screen_count').value;
	section_value		= parseInt(section_value)+1;
	
	$('contact_screen_count').value	= section_value;
	if(section_value<6)
		{
			$('screen_name_'+section_value).style.display='block';
			if(section_value==5)
				$('add_im_link').style.display='none';
		}
}

//sets the interface on network suggestion form, regarding
//the selected network type
function setupNetworkSuggestForm()
{
	var network_type	= $('network_type').value;
	if(network_type=='school' || network_type=='college' || network_type=='work' || network_type=='region')
		{
			$('network_submit').style.display		= 'block';
			$('network_name').style.display			= 'block';
			$('network_email').style.display		= 'block';
			$('network_region').style.display		= 'block';
			$('network_contact_email').style.display= 'block';
			if(network_type=='work')
				$('network_website').style.display	= 'block';
			else
				$('network_website').style.display	= 'none';
			if(network_type=='region')
				$('network_email').style.display		= 'none';
		}
	else
		{
			$('network_submit').style.display		= 'none';
			$('network_name').style.display			= 'none';
			$('network_email').style.display		= 'none';
			$('network_region').style.display		= 'none';
			$('network_contact_email').style.display= 'none';
			$('network_website').style.display		= 'none';
		}
	if(network_type=='school')
		{
			$('namelabel').innerHTML	= 'High School Name';
			$('emaillabel').innerHTML	= 'High School Email(optional)';
		}
	else if(network_type=='college')
		{
			$('namelabel').innerHTML	= 'College Name';
			$('emaillabel').innerHTML	= 'College Email';
		}
	else if(network_type=='work')
		{
			$('namelabel').innerHTML	= 'Company Name';
			$('emaillabel').innerHTML	= 'Company Email';
		}
	else if(network_type=='region')
		$('namelabel').innerHTML	= 'Region Name';
}
//validate network form
function validate_network()
{
	country	= $('network_country_select').value;
	if(country=='US')
		state	= parseInt($('network_state_us_select').value);
	else if(country=='CA')
		state	= parseInt($('network_state_ca_select').value);
	networkEmail	= $('net_email').value;
	contactEmail	= $('net_contact_email').value;
	networkType		= $('network_type').value;
	networkName		= $('net_name').value;
	city			= $('net_city').value;
	//alert(country);
	//return false;
	if(country=='0' || networkName=='' || city=='')
		error	= 'Please give the required fields.';
	else if((country=='US' || country=='CA') && state<1)
		error	= 'Please select the State/Province.';
	else if(networkType!='region' && networkEmail!='' && !isEmail(networkEmail))
		error	= 'Please give the valid email.';
	else if(contactEmail!='' && !isEmail(contactEmail))
		error	= 'Please give the valid contact email.';
	else
		error	= '';
	if(error!='')
		{
			$('network_result').innerHTML	= '<font color="red">'+error+'</font>';
			return false;
		}
	else
		return true;
}
//cehck valid email
function isEmail(str)
{
	var r1 = new RegExp("(@.*@)|(\\.\\.)|(@\\.)|(^\\.)");
	var objRegExp = /(^[a-z]([a-z0-9_\.]*)@([^-][a-z0-9-_\.]*)([.][a-z]{3})$)|(^[a-z]([a-z0-9_\.]*)@([^-][a-z0-9-_\.]*)(\.[a-z]{2,3})(\.[a-z]{2})*$)/i;
	return (!r1.test(str) && objRegExp.test(str));
}

//to show the div content
function show_div(div_id)
{
	//alert(div_id);
	$(div_id).style.display	= 'block';
}
//to close div
function close_div(div_id)
{
	$(div_id).style.display	= 'none';
}

//function to show leave network dialog box
function show_leave_network_dialog(network_id)
{
	$('confirm_dialog').style.display	= 'block';
	$('cancel_dialog').style.display	= 'none';
	$('leave_network_id').value			= network_id;
}
//function to show cancel request network dialog box
function show_cancel_network_dialog(network_id)
{
	$('confirm_dialog').style.display	= 'none';
	$('cancel_dialog').style.display	= 'block';
	$('cancel_network_id').value		= network_id;
}
//empty the host textbox while click on one of my groups
//select box on event creation first step page
function eventHostGroupSelect(hostTextId)
{
	//alert($('event_host_group').value);
	if($('event_host_group').value!=0)
		$(hostTextId).value	= '';
}
//function to show access related message on events(step1)
function toggle_access_event(str)
{
	if(str=='photos')
		{
			if($('show_photos').checked)
				$('show_photos_suboptions').style.display	= 'block'
			else
				$('show_photos_suboptions').style.display	= 'none'
		}
	
	if($('show_guest_list').checked && $('show_wall').checked && $('show_photos').checked)
		{
			var	public_access	= 'People can add themselves to the guest list and invite others to the event. Anyone can see the event information, the guest list, and photos of the event.';
			var closed_access	= 'Only people you invite will be on the guest list. People can request invitations. Anyone can see the event time and description, but only those invited can see the location, the guest list and photos of the event.';
			var private_access	= 'The event will not appear in search results. Only people you invite can see the event information, the guest list, and photos of the event.';
		}
	else if(!$('show_guest_list').checked && !$('show_wall').checked && !$('show_photos').checked)
		{
			var	public_access	= 'People can add themselves to the guest list and invite others to the event. Anyone can see the event information and the wall.';
			var closed_access	= 'Only people you invite will be on the guest list. People can request invitations. Anyone can see the event time and description, but only those invited can see the location, the wall.';
			var private_access	= 'The event will not appear in search results. Only people you invite can see the event information and the wall.';
		}
	else if(!$('show_guest_list').checked && $('show_wall').checked && $('show_photos').checked)
		{
			var	public_access	= 'People can add themselves to the guest list and invite others to the event. Anyone can see the event information and photos of the event.';
			var closed_access	= 'Only people you invite will be on the guest list. People can request invitations. Anyone can see the event time and description, but only those invited can see the location, photos of the event.';
			var private_access	= 'The event will not appear in search results. Only people you invite can see the event information and photos of the event.';
		}
	else if(!$('show_guest_list').checked && !$('show_wall').checked && $('show_photos').checked)
		{
			var	public_access	= 'People can add themselves to the guest list and invite others to the event. Anyone can see the event information, the wall, and photos of the event.';
			var closed_access	= 'Only people you invite will be on the guest list. People can request invitations. Anyone can see the event time and description, but only those invited can see the location, the wall and photos of the event.';
			var private_access	= 'The event will not appear in search results. Only people you invite can see the event information, the wall, and photos of the event.';
		}
	else if($('show_guest_list').checked && !$('show_wall').checked && !$('show_photos').checked)
		{
			var	public_access	= 'People can add themselves to the guest list and invite others to the event. Anyone can see the event information, the guest list, and the wall.';
			var closed_access	= 'Only people you invite will be on the guest list. People can request invitations. Anyone can see the event time and description, but only those invited can see the location, the guest list and the wall.';
			var private_access	= 'The event will not appear in search results. Only people you invite can see the event information, the guest list, and the wall.';
		}
	else if(!$('show_guest_list').checked && !$('show_wall').checked && $('show_photos').checked)
		{
			var	public_access	= 'People can add themselves to the guest list and invite others to the event. Anyone can see the event information, the wall, and photos of the event.';
			var closed_access	= 'Only people you invite will be on the guest list. People can request invitations. Anyone can see the event time and description, but only those invited can see the location, the wall and photos of the event.';
			var private_access	= 'The event will not appear in search results. Only people you invite can see the event information, the wall, and photos of the event.';
		}
	else if(!$('show_guest_list').checked && $('show_wall').checked && !$('show_photos').checked)
		{
			var	public_access	= 'People can add themselves to the guest list and invite others to the event. Anyone can see the event information.';
			var closed_access	= 'Only people you invite will be on the guest list. People can request invitations. Anyone can see the event time and description.';
			var private_access	= 'The event will not appear in search results. Only people you invite can see the event information.';
		}
	else if($('show_guest_list').checked && !$('show_wall').checked && $('show_photos').checked)
		{
			var	public_access	= 'People can add themselves to the guest list and invite others to the event. Anyone can see the event information, the guest list, the wall, and photos of the event.';
			var closed_access	= 'Only people you invite will be on the guest list. People can request invitations. Anyone can see the event time and description, but only those invited can see the location, the guest list, the wall, and photos of the event.';
			var private_access	= 'The event will not appear in search results. Only people you invite can see the event information, the guest list, the wall, and photos of the event.';
		}
	$('public_access').innerHTML	= public_access;
	$('closed_access').innerHTML	= closed_access;
	$('private_access').innerHTML	= private_access;
}
//function to show div block with content
function showDivContent(divId,divContent)
{
	$(divId).style.display		= 'block';
	$(divId).innnerHTML			= divContent;
}
function browseEvents()
{
	$('event_category').value	= $('event_category_select').value;
	$('event_date').value		= $('event_date_select').value;
	$('browse_event_form').submit();
}
//
function showSchoolCity()
{
	if($('school_country_select').value=='United States')
		{
			$('school_city_us_div').style.display		= 'block';
			$('school_city_ca_div').style.display		= 'none';
			$('school_city_default_div').style.display	= 'none';
			
		}
	else if($('school_country_select').value=='Canada')
		{
			$('school_city_us_div').style.display		= 'none';
			$('school_city_ca_div').style.display		= 'block';
			$('school_city_default_div').style.display	= 'none';
			
		}
	else
		{
			$('school_city_us_div').style.display		= 'none';
			$('school_city_ca_div').style.display		= 'none';
			$('school_city_default_div').style.display	= 'block';
		}
}
function IsNumeric(sText)

{
   var ValidChars = "0123456789.";
   var IsNumber=true;
   var Char;

 
   for (i = 0; i < sText.length && IsNumber == true; i++) 
      { 
      Char = sText.charAt(i); 
      if (ValidChars.indexOf(Char) == -1) 
         {
         IsNumber = false;
         }
      }
   return IsNumber;
   
   }
   
//function to validate work form
function validateWork()
{	
	var totalJobs = $('work_section_count').value;
	var d=new Date();
	var curmonth=d.getMonth();
	var company	= $('work_history_1_company').value;
	if(company=='')
		{	
			$('work_history_result').innerHTML	= '<table width="100%" class="splborder"><tr><td height="41" bordercolor="#ec8a00" bgcolor="#feffcf"><span class="blktitle">Employer field is mandatory.</span><br /></td></tr></table>';
			$('work_success').style.display	= 'none';
			$('work_error').style.display	= 'none';
		}
		else if(totalJobs!=0)
		{
		  for (i = 1; i <= totalJobs; i++) 
			{
					 if ($('work_history_'+i+'_workspan_current').checked==true)
						{
							if(!(($('work_history_'+i+'_start_month_select').value>0) && (curmonth >= $('work_history_'+i+'_start_month_select').value)))
							{
								$('work_history_result').innerHTML	= '<table width="100%" class="splborder"><tr><td height="41" bordercolor="#ec8a00" bgcolor="#feffcf"><span class="blktitle">Please Enter correct month</span><br /></td></tr></table>';
								$('work_success').style.display	= 'none';
								$('work_error').style.display	= 'none';
							}
							else
							{
							document.work_form.submit();
							}
						}
						else
						{
							
							if($('work_history_'+i+'_start_year_select').value==$('work_history_'+i+'_end_year_select').value)
							{
											if($('work_history_'+i+'_start_month_select').value>=$('work_history_'+i+'_end_month_select').value)
											{
												$('work_history_result').innerHTML	= '<table width="100%" class="splborder"><tr><td height="41" bordercolor="#ec8a00" bgcolor="#feffcf"><span class="blktitle">Please Enter correct month</span><br /></td></tr></table>';
												$('work_success').style.display	= 'none';
												$('work_error').style.display	= 'none';
											}
											else
											{
											document.work_form.submit();
											}
							}
							
								if((($('work_history_'+i+'_end_year_select').value>0) && ($('work_history_'+i+'_start_year_select').value>=$('work_history_'+i+'_end_year_select').value) ))
								{		
											document.work_form.submit();
								}
								else
								{
								$('work_history_result').innerHTML	= '<table width="100%" class="splborder"><tr><td height="41" bordercolor="#ec8a00" bgcolor="#feffcf"><span class="blktitle">Please Enter correct year</span><br /></td></tr></table>';
								$('work_success').style.display	= 'none';
								$('work_error').style.display	= 'none';
								}
						}
			}
		}
	else
	{
		document.work_form.submit();
	}
}
   
   
   

//function to validate contact form
function validateContact()
{
	var mobile	= $('mobile').value;
	var land	= $('other_phone').value;
	var url		= $('website').value;
	var zip		= $('zip').value;
	if(mobile!='' && !IsNumeric(mobile))
		{
			$('contact_result').innerHTML	= '<table width="100%" class="splborder"><tr><td height="41" bordercolor="#ec8a00" bgcolor="#feffcf"><span class="blktitle">Please give the valid mobile number.</span><br /></td></tr></table>';
			$('contact_success').style.display	= 'none';
			$('contact_error').style.display	= 'none';
		}
	else if(land!='' && !IsNumeric(land))
		{
			$('contact_result').innerHTML	= '<table width="100%" class="splborder"><tr><td height="41" bordercolor="#ec8a00" bgcolor="#feffcf"><span class="blktitle">Please give the valid landline number.</span><br /></td></tr></table>';
			$('contact_success').style.display	= 'none';
			$('contact_error').style.display	= 'none';
		}
	else if(zip!='' && !IsNumeric(zip))
		{
			$('contact_result').innerHTML	= '<table width="100%" class="splborder"><tr><td height="41" bordercolor="#ec8a00" bgcolor="#feffcf"><span class="blktitle">Please give the valid zip code.</span><br /></td></tr></table>';
			$('contact_success').style.display	= 'none';
			$('contact_error').style.display	= 'none';
		}
	else if(url!='' && !isValidUrl(url))
		{
			$('contact_result').innerHTML	= '<table width="100%" class="splborder"><tr><td height="41" bordercolor="#ec8a00" bgcolor="#feffcf"><span class="blktitle">Please give the valid url.</span><br /></td></tr></table>';
			$('contact_success').style.display	= 'none';
			$('contact_error').style.display	= 'none';
		}
	else
		document.contact_profile_form.submit();
}
function isValidUrl(url) 
{
    var v = new RegExp();
   // v.compile("^[A-Za-z]+://[A-Za-z0-9-_]+\\.[A-Za-z0-9-_%&\?\/.=]+$");
   v.compile("^[A-Za-z]+\\.[A-Za-z0-9-_%&\?]+\\.[A-Za-z]+$");
    if (!v.test(url)) 
        return false;
	else
		return true;
} 

//un select all check box
function deSelectMain(form,chkName)
{
	var dmform=document.forms[form];
	var len = dmform.elements.length;
	var flag=false;
	var i=0;
	var j=1;
	for( i=0 ; i<len ; i++) 
	{
		//alert(dmform.elements[i].name);
		if(dmform.elements[i].name==chkName+j) 
		{
			if(dmform.elements[i].checked==false)
			{ 
				flag=true;
			}
			j++;
		}
	}

	if(flag)
		dmform.checkAll.checked=0;
	else
		dmform.checkAll.checked=1;
}
//select all check box
function CheckAll(form)
{
	//alert (document.ViewProductForm.checkAll.checked);
	//alert("hi");
	var dmform=document.forms[form];
	//alert(dmform);
	var Checked=dmform.checkAll.checked;
	if(Checked)
		SetChecked(1,'check_',form);
	else
		SetChecked(0,'check_',form);
}

function SetChecked(val,chkName,form) 
{
	
	//dml=document.forms['ViewProductForm'];
	//alert("hi");
	var dmform=document.forms[form];
	len = dmform.elements.length;
	//alert(len);
	var i=0;
	var j=1;
	for( i=0 ; i<len ; i++) 
	{
		if (dmform.elements[i].name==chkName+j) 
		{
			dmform.elements[i].checked=val;
			j++;
		}
	}
}


//confirmation function dis-approve networks
//chkName	=> check box name
//form		=> form name
//hiddenVar	=> hidden variable to be set while submitting the page
//action	=> it should be displayed on result message
function doMutipleSelect(chkName,form,hiddenVar,action)
{
	//alert(hiddenVar);
	//var chkName='check';
	var dmform=document.forms[form];
	var len = dmform.elements.length;
	//alert(len);
	var flag=false;
	var i=0;
	var j=1;
	for( i=0 ; i<len ; i++) 
	{
		
		if (dmform.elements[i].name==chkName+j) 
		{
		//	alert(dmform.elements[i].name);
			if(dmform.elements[i].checked==true)
			{ 
				flag=true;
			}
			j++;
		}
	}
	//var Result = form.active_select.value;
	var answer=false;
	if(flag==false)
		alert("Please select atleast one record to "+action+" !");
	else
		answer=confirm("Do you want to "+action+" the selected records?");//alert("hello");
	if(answer)
	{
		dmform.elements[hiddenVar].value='1';
		//alert(dmform);
		dmform.submit();
	}
}

function reportUser() 
{
	var off_code	= $('off_code').value;
	if(off_code==0)
	{
		return document.reportNetworkUser.comment.disabled=true;
	}
	else
	{
		return document.reportNetworkUser.comment.disabled=false;

	}
}

function confirmDelete()
{
	//alert('hi');
	return confirm('Do you really want to delete the selected log(s)?');
}
selectEmployerFromSuggestor = function(cur_box, sel_list){
	//alert("Employer ID \n" + sel_list.id + "\n has been selected" );
	$('e').value = sel_list.id;
	//$('company').value = cur_box.value;
}

