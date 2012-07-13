<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$this->load->view('header');
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%">
			<table width="100%">
				<tr height="50">
					<td>
					<span id="view_all" class="tabactive">
						<a href="<?php echo $currentPage;?>index.php/editprofile/basic" class="TabWhiteLink">Basic</a>
					</span>
					<span id="create_blog" class="tab">
						<a href="<?php echo $currentPage;?>index.php/editprofile/contact" class="TabBlackLink">Contact</a>
					</span>
					<span id="create_blog" class="tab">
						<a href="<?php echo $currentPage;?>index.php/editprofile/personal" class="TabBlackLink">Personal</a>
					</span>
					<span id="search_blog" class="tab">
						<a href="<?php echo $currentPage;?>index.php/editprofile/education" class="TabBlackLink">Education</a>
					</span>
					<span id="top_videos" class="tab">
						<a href="<?php echo $currentPage;?>index.php/editprofile/work" class="TabBlackLink">Work</a>
					</span>
					<span id="view_all" class="tab">
						<a href="<?php echo $currentPage;?>index.php/editprofile/picture" class="TabBlackLink">Picture</a>
					</span>
					</td>
				</tr>
			</table>
			<div id="basic_ajax_loader" style="display:none;" align="center"> <img src="<?php echo $currentPage;?>images/indicator_mozilla_yellow.gif" border="0"></div>
			<table width="100%">
				<tbody>
					<tr><td><div id="profileResult"></div></td></tr>
					<tr><td>
						<form name="basic_profile" method="post">
						<table width="100%">
							<tr class="select sex">
								<td class="label">Sex:</td>
								<td>
									<select class="select" name="sex" id="sex">
									<option value="none" "<?php $sexNone;?>">Select Sex:</option>
									<option value="female" "<?php $female?>">Female</option>
									<option value="male" "<?php $male;?>">Male</option>
									</select>
								</td>
							</tr>
							<tr class="checkbox_array tallrow">
								<td class="label">Interested in:</td>
								<td>
									<table class="checkbox_array" border="0" cellspacing="0">
										<tbody>
											<tr>
												<td>
													<input name="meeting_men" id="meeting_men" type="checkbox" value="men" "<?php echo $menCheck;?>">
												</td>
												<td><label for="meeting_men">Men</label></td>
												<td><input name="meeting_women" id="meeting_women" type="checkbox" value="women" "<?php echo $womenCheck;?>"></td>
												<td><label for="meeting_women">Women</label></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr class="relationship tallrow">
								<td class="label">Relationship&nbsp;Status:</td>
								<td>
								<?php echo $relationshipStatus;?>
								<span id="relationship_new_partner_to" style="display: none;">to...<br>
								</span><span id="relationship_new_partner_with" style="display: none;">with...<br></span>
								<span id="relationship_new_partner" style="display: none;">
								<input class="inputtext" style="margin: 4px 0px;" name="new_partner" id="relationship_new_partner"><br>
								</span>
								</td>
							</tr>
							<tr class="checkbox_array tallrow">
								<td class="label">Looking for:</td>
								<td>
									<table class="checkbox_array" border="0" cellspacing="0">
										<tbody>
											<?php echo $lookingFor;?>
											<input type="hidden" name="totMeets" id="totMeets" value="<?php echo $totMeets;?>">
										</tbody>
									</table>
								</td>
							</tr>
							<tr class="birthday tallrow">
								<td class="label">Birthday:</td>
								<td>
									<?php echo $birthDay;?>
									<?php echo $birthMonth;?>
									<?php echo $birthYear;?>
								</td>
							</tr>
							<tr class="birthday_pref birthday tallrow">
								<td class="label"></td>
								<td><?php echo $birthdayVisibility;?>in my profile.</td>
							</tr>
							<tr class="text hometown">
								<td class="label">Hometown:</td>
								<td><input id="hometown" name="hometown" class="inputtext" value="<?php echo $hometown;?>" type="text"></td>
							</tr>
							
							<tr>
								<td class="label" valign="top">
									<?php if($basic_country=='US'):?>
										<span id="basic_profile_state_label" style="display:block;">State:</span>
										<span id="basic_profile_province_label" style="display:none;">Province:</span>
									<?php elseif($basic_country=='CA'):?>									
										<span id="basic_profile_state_label" style="display:none;">State:</span>
										<span id="basic_profile_province_label" style="display:block;">Province:</span>
									<?php else:?>									
										<span id="basic_profile_state_label" style="display:none;">State:</span>
										<span id="basic_profile_province_label" style="display:none;">Province:</span>
									<?php endif;?>									
									<span id="basic_profile_country_label">Country:</span>
								</td>
								<td>
									<?php if($basic_country=='US'):?>
										<span id="basic_profile_state_us" style="display:block;"><?php echo $basic_profile_state_us_select;?></span>
										<span id="basic_profile_state_ca" style="display:none;"><?php echo $basic_profile_state_ca_select;?></span>
									<?php elseif($basic_country=='CA'):?>
										<span id="basic_profile_state_ca" style="display:block;"><?php echo $basic_profile_state_ca_select;?></span>
										<span id="basic_profile_state_us" style="display:none;"><?php echo $basic_profile_state_us_select;?></span>
									<?php else:?>
										<span id="basic_profile_state_us" style="display:none;"><?php echo $basic_profile_state_us_select;?></span>
										<span id="basic_profile_state_ca" style="display:none;"><?php echo $basic_profile_state_ca_select;?></span>
									<?php endif;?>
									<span id="basic_profile_country" style="display:block;"><?php echo $basic_profile_country_select;?></span>
								</td>
							</tr>
							<tr class="select political">
								<td class="label">Political Views:</td>
								<td>
									<?php echo $politicalView;?>
									
								</td>
							</tr>
							<tr class="information religion" id="religion">
								<td class="label">Religious Views:</td>
								<td>
								<input name="religion_name" id="religion_name" class="inputtext" value="<?php echo $religion;?>" maxlength="100" size="25" autocomplete="off" >
								<input name="religion_id" id="religion_id" type="hidden">
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<div class="buttons">
									
									<input class="inputsubmit" id="save" name="save" value="Save Changes" type="button" onClick="basicProfile('<?php echo $currentPage;?>','<?php echo $userId?>');">
									<input class="inputbutton" id="" name="" value="Cancel" type="button" onclick="window.location='<?php echo $currentPage;?>index.php/home';">
									</div>
								</td>
							</tr>
						</table>
						</form>
					</td></tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>
<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$this->load->view('footer');
?>