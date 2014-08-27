#!/usr/bin/php
<?php
# CheckAlerts.php
#
# Copyright 2009 Quentin Hess
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

require_once("./lib/MySQLConnect.php");
require_once("./lib/GenerateCall.php");
require_once("./lib/Common.php");

if(!file_exists($Config['Lockfile'])) {


ShowLogs('INFO','Creating lock file');

$fp = fopen($Config['Lockfile'], "w+");

$Waiting = 0;

DBconnect();

while( TRUE ) {

if($Waiting != 0) {

ShowLogs('INFO','Waiting 55 seconds');

sleep(55);

}

if($Config['Delivery_strategy'] == "per_alert") {

	ShowLogs('INFO','Strategy per_alert is applied');


	// Strategy to sort per alerts
	$QueryAlerts = mysql_query("SELECT *,UNIX_TIMESTAMP(last_time) as unixtime, alerts.id as id_alert 
						FROM hub_alerts alerts 
						LEFT JOIN hub_users users
						ON alerts.id_owner = users.id
						WHERE
							alerts.host=(SELECT host FROM hub_alerts LIMIT 1) AND 
							alerts.service=(SELECT service FROM hub_alerts LIMIT 1) AND 
							alerts.recovered=(SELECT recovered FROM hub_alerts LIMIT 1) 
						ORDER BY alerts.".$Config['Delivery_sort']);
} 
elseif($Config['Delivery_strategy'] == "per_owner") {

	ShowLogs('INFO','Strategy per_owner is applied');


	// Strategy to sort alerts by owner
	$QueryAlerts = mysql_query("SELECT *,UNIX_TIMESTAMP(last_time) as unixtime, alerts.id as id_alert 
						FROM hub_alerts alerts 
						LEFT JOIN hub_users users
						ON alerts.id_owner = users.id
						GROUP BY id_owner
						ORDER BY alerts.".$Config['Delivery_sort']);

}
else { 
	ShowLogs('CRITICAL','No strategy is defined');
	exit(1);
}


if(mysql_num_rows($QueryAlerts) == 0) {
	ShowLogs('INFO','No alerts : exit'); 
	break;
}

$SKIP = 0;

$OneCallPerSession = array();

while($DataAlerts = mysql_fetch_array($QueryAlerts)) {

	ShowLogs('INFO','Wait 1 second');
	sleep(1);


	if($DataAlerts['id_upper'] == '0' || $DataAlerts['autodel'] == '1') {

		ShowLogs('INFO','No Upper Level is set : disable ACL Escalation'); 
		$DataAlerts['acl_escalation'] = 'N'; // Force No for escalation

	}
	else {

		$diff_alert = time() - $DataAlerts['unixtime'];
		ShowLogs('INFO','Last action on this alert : ' . $diff_alert . 'seconds');

		if($diff_alert > $Config['Escalation_delay']) {

			ShowLogs('INFO','Last action, there are more than '.$Config['Escalation_delay'].' seconds');
			ShowLogs('INFO','Escalation to '.$DataAlerts['id_upper']);

			mysql_query("DELETE FROM hub_alerts WHERE
							host='".$DataAlerts['host']."',
							service='".$DataAlerts['service']."',
							autodel='0',
							id<>'".$DataAlerts['id_alert']);

			mysql_query("UPDATE hub_alerts SET 
						id_owner='".$DataAlerts['id_upper']."',
						last_time=NOW(),
						escalation='1'
					WHERE id='".$DataAlerts['id_alert']."'"); 

			$SKIP = 1;		
		}
	}

	if($SKIP == 0 && !in_array($DataAlerts['phone'],$OneCallPerSession)) {

		ShowLogs('INFO','Create new call');


		// Overwrite ACL to No if alert is a notification
		if($DataAlerts['autodel'] == 1) {

			ShowLogs('INFO','Call is notification, disable all options');

			$DataAlerts['acl_ack_alert'] = 'N';
                        $DataAlerts['acl_ack_srv'] = 'N';
                        $DataAlerts['acl_ack_ptf'] = 'N';
                        $DataAlerts['acl_ack_all'] = 'N';
                        $DataAlerts['acl_escalation'] = 'N';

		}

		ShowLogs('INFO','Set Call Variables');

                if(!file_exists($Root['soundsroot'] . '/' . $DataAlerts['language'])) {

                        ShowLogs('ERROR','Language " ' . $DataAlerts['language'] . ' " doesn\'t exist, "fr" is selected');
			$DataAlerts['language'] = 'fr';

                }

		// Define alert properties
		$alert = array(
				'alert_id'		=> $DataAlerts['id_alert'],
				'alert_escalation'	=> $DataAlerts['escalation'],
				'alert_host'            => $DataAlerts['host'],
				'alert_service'         => $DataAlerts['service'],
				'alert_plateform'       => $DataAlerts['plateform'],
				'alert_status'          => $DataAlerts['state'],
				'alert_ackcode'         => $DataAlerts['ackcode'] ,
				'alert_recovered'       => $DataAlerts['recovered']
				);

		$user = array(
				'user_id'		=> $DataAlerts['id'],
				'user_name'		=> $DataAlerts['name'],
				'user_lang'		=> $DataAlerts['language'],
				'user_id_upper'		=> $DataAlerts['id_upper'],
                                'acl_ack_alert'         => $DataAlerts['acl_ack_alert'],
                                'acl_ack_srv'           => $DataAlerts['acl_ack_srv'],
                                'acl_ack_ptf'           => $DataAlerts['acl_ack_ptf'],
                                'acl_ack_all'           => $DataAlerts['acl_ack_all'],
                                'acl_escalation'        => $DataAlerts['acl_escalation']);

		$number = $DataAlerts['phone'];

			
		ShowLogs('INFO','Number is '.$number);
		ShowLogs('INFO','Generate call to '. $DataAlerts['name'] . ' ' . $DataAlerts['phone']);

		ShowLogs('DEBUG',var_dump($alert));
		ShowLogs('DEBUG',var_dump($user));
		ShowLogs('DEBUG',var_dump($Root));

		// Generate call
		GenerateCall($number, $user, $alert, $Root);
		$OneCallPerSession[] = $DataAlerts['phone'];
		$Waiting = 1;	

		if($DataAlerts['autodel'] == 1) {
			
			ShowLogs('INFO','Is notification : drop alert '. $DataAlerts['id_alert']);	

			mysql_query("DELETE FROM hub_alerts WHERE id='".$DataAlerts['id_alert']."'");

		}
	
	}

}	

}

DBclose();

ShowLogs('INFO','Remove lock file');
unlink($Config['Lockfile']);

}
else {
	ShowLogs('INFO','Script already started, remove '.$Config['Lockfile'].' for unlocked');
}


?>
