<?php
# GenerateCall.php
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

require_once('./Config.php') ;

function GenerateCall($number, $user, $alert, $roots) {

	global $Config ;

	$CallsVar = '' ;

	$Channel = 'Local/' . $number . '@' . $Config['Call_context'];

	$Separator = ',' ;

	foreach($user as $ParamUser => $ValueUser) {

		$CallsVar .= $ParamUser . '=' . $ValueUser . $Separator;
		$user[$ParamUser] = mysql_real_escape_string($ValueUser);

	}

	foreach($alert as $ParamAlert => $ValueAlert) {
		
		$CallsVar .= $ParamAlert . '=' . $ValueAlert . $Separator;
		$alert[$ParamAlert] = mysql_escape_string($ValueAlert);

	}

	foreach($roots as $ParamRoots => $ValueRoots) {

		$CallsVar .= $ParamRoots . '=' . $ValueRoots . $Separator;
		$roots[$ParamRoots] = mysql_escape_string($ValueRoots);

	}

	$CallsVar = mysql_escape_string($CallsVar);

        mysql_query("INSERT INTO hub_alerts_logs (
					id_owner,
                                        create_time,
                                        number,
                                        name,
                                        host,
                                        service,
                                        plateform,
                                        status,
                                        escalation ,
                                        recovered ,
                                        callsvar )
                        VALUES (
				'".$user['user_id']."',
                                NOW(),
                                '".$number."',
                                '".$user['user_name']."',
                                '".$alert['alert_host']."',
                                '".$alert['alert_service']."',
                                '".$alert['alert_plateform']."',
                                '".$alert['alert_status']."',
                                '".$alert['alert_escalation']."',
                                '".$alert['alert_recovered']."',
                                '".$CallsVar."')") ;

	$IdLog = mysql_insert_id() ;
	$CallsVar .= 'idlog='.$IdLog ;

	// Connect to manager
	$socket = fsockopen($Config['Manager_Host'],$Config['Manager_Port']) ;

	fputs($socket, "Action: Login\r\n");
        fputs($socket, "UserName: ".$Config['Manager_User']."\r\n");
        fputs($socket, "Secret: ".$Config['Manager_Secret']."\r\n\r\n");

	// Generate Call
	fputs($socket, "Action: Originate\r\n");
	fputs($socket, "Channel: $Channel\r\n");
	fputs($socket, "CallerId: Hub'erte ". $alert['alert_status'] ." alert <".$alert['alert_host']."/".$alert['alert_service'].">\r\n") ;
	fputs($socket, "Variable: $CallsVar\r\n") ;
        fputs($socket, "Context: huberte-home\r\n");
        fputs($socket, "Exten: s\r\n") ;
        fputs($socket, "Priority: 1\r\n\r\n");
        $wrets = fgets($socket,128);

        fputs($socket, "Action: Logoff\r\n\r\n");
        fclose($socket);

	echo $wrets;
}


?>
