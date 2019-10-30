<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

ERROR - 2019-04-21 11:24:03 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near '' at line 4 - Invalid query: SELECT ad.*,u.* 
                   FROM dgt_users u  
				   left join dgt_account_details as ad on ad.user_id = u.id
				   WHERE u.activated = 1 and u.id != 
ERROR - 2019-04-21 11:24:03 --> Severity: error --> Exception: Call to a member function result() on boolean /home/dz95cocnc9py/public_html/livetest/application/modules/sidebar/views/top_header.php 110
