#!/usr/bin/php
<?php
# GenerateAlert.php
#
# Copyright 2014 Quentin Hess
#
#    This file is part of Hub'erte.
#
#    Hub'erte is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    Hub'erte is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with Hub'erte.  If not, see <http://www.gnu.org/licenses/>.
#
# 
#### FOR HOBBIT/XYMON MONITOR ###
# EDIT FILE : ~hobbit/server/etc/hobbit-alerts.cfg
# And add following line, like MAIL notification.
#
# SCRIPT HUB'ERTE_PATH/HobbitAlert.sh PlateForm Hobbit_Flags
#
########################

// Shorts arguments
$opts = "a:h:s:c:r:p:" ;
$options = getopt($opts);

// Helping
$help = 'GenerateAlert.php -a ackcode [-p PLATEFORM] -h hostname -s service -c color [-r]
		
		-a 	Code ACK generate by Hobbit

		-p 	Plateform which contain host (ALL by default)	

		-h 	Hostname of the server

		-s 	Service 

		-c 	Alert Color (red, yellow, blue, white, purple)

		-r 	If it\'s recovered

' ;

// Inlude function and config
require_once('./lib/MySQLConnect.php') ;
require_once('./lib/Common.php') ;

// Check arguments data
if(!isset($options['p'])) { $plateform = 'ALL'; } else { $plateform = $options['p']; }
if(!isset($options['r'])) { $isrecovered = '0'; } else { $isrecovered = $options['r']; }

if(!isset($options['h']) || !isset($options['s']) || !isset($options['c']) || !isset($options['a'])) {
	echo $help ;
	ShowLogs('CRITICAL','Syntax Error');
	exit(1) ;
}
else {
	DBconnect() ;

	// If ackcode is set, Update this.
	if($options['a'] != '-1') {
	
		ShowLogs('INFO','AckCode is ' . $options['a']);
		$ackupd = "ackcode='".$options['a']."'";
	}
	else {

		ShowLogs('INFO','No ACK code');
		$ackupd = '';

	}

	$QueryExist = mysql_query("SELECT count(*) as nbalerts FROM hub_alerts 
								WHERE 
						                        host='".$options['h']."' AND
	                                        			service='".$options['s']."' AND
				                                        recovered='".$isrecovered."'") ;
	$DataExist = mysql_fetch_array($QueryExist) ;

	$NumAlerts = $DataExist['nbalerts'] ;

	// Updating twin entries
	mysql_query("UPDATE hub_alerts SET
					state='".$options['c']."',
					last_time=NOW(),
					".$ackupd."
				WHERE
					host='".$options['h']."' AND
					service='".$options['s']."' AND
					recovered='".$isrecovered."'") ;


	ShowLogs('INFO',$NumAlerts . ' queries updated') ;


	// Search current date in HNO table
	$CheckHNOday = mysql_query("SELECT date FROM hub_hno WHERE date=CURDATE()") ;
	$CountHNOday = mysql_num_rows($CheckHNOday) ;
	
	// IF the current moment is in HNO OR houre between 20 and 8 o'clock OR week-end day (Sat and Sun)
	if($CountHNOday != 0 || (date('G') < $Config['HNO_end'] || date('G') >= $Config['HNO_start']) || (date('N') == 6 || date('N') == 7)) {

		ShowLogs('INFO','Currently not openned');

		// There aren't already alert
		if($NumAlerts == '0')  {
 
		// List Plateform Users
		$QueryUsers = mysql_query("SELECT * FROM hub_users WHERE 
								(usr_plateform='".$plateform."' OR usr_plateform='ALL') 
								AND enable='Y' AND phone<>''") ;			

		while($DataUsers = mysql_fetch_array($QueryUsers)) {

			// For notified users
			if($DataUsers['acl_ack_alert'] == 'N' &&
				$DataUsers['acl_ack_srv'] == 'N' &&
				$DataUsers['acl_ack_ptf'] == 'N' &&
				$DataUsers['acl_ack_all'] == 'N') { $Autodel = '1'; }
			else { $Autodel = '0'; }
	
			// For recovered
			if($isrecovered == 1) {
				$Autodel = '1';
			}
			
			ShowLogs('INFO','Auto delete is set to ' . $Autodel);
	
			if(($DataUsers['acl_recovered'] == 'Y' && $isrecovered == 1) || $isrecovered == 0) {

				ShowLogs('INFO','Create Alert for '. $DataUsers['id']);
	
				mysql_query("INSERT INTO hub_alerts (
								id_owner ,
								create_time ,
								last_time ,
								plateform ,
								host ,
								service ,
								state ,
								recovered ,
								autodel ,
								ackcode ) 
							VALUES (
								'".$DataUsers['id']."',
								NOW(),
								NOW(),
								'".$plateform."',
								'".$options['h']."',
								'".$options['s']."',
								'".$options['c']."',
								'".$isrecovered."',
								'".$Autodel."',
								'".$options['a']."')") ;
			}

		}		 
	
		}
		else {
	
			ShowLogs('INFO','Alert Already Exist');

		}
	}
	else {
		// Purge Alerts table on HO
		ShowLogs('INFO','Remove all alerts') ;

		mysql_query("DELETE * FROM hub_alerts") ;
	}

	DBclose() ;
}
?>
