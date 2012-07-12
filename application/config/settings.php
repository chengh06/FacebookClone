<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
$config['mplayer']		= "/usr/local/bin/mplayer";							//mplayer path
$config['mencoder']		= "/usr/local/bin/mencoder";						//mencoder path
$config['rootPath']		= "/home/development/public_html/treedrop/";		//dev5 server
$config['userId']		= isset($_SESSION['user_id']);								//get user id
$config['now']			= date('Y-m-d h:i:s');