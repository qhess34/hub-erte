#!/usr/bin/php
<?php
# AckAlert.php
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

// Arguments list
$opts = "u:a:p:h:d:m:t" ;
$options = getopt($opts);

// Helping
$help = 'AckAlert.php [-a ackcode] [-u username] [-m message] [-d delay(w|d)] [-p plateform] [-h host] [-t] 

		-a      Acknowledge code generate by Hobbit/Xymon

		-u	Acknowledgement username 

                -m      Acknowledgement message

                -d      Acknowledgement delay
		
		-p 	Plateform 

		-h 	Xymon server host

		-t	Acknowledge all alerts of alert spool

' ;

require_once('./lib/Acknowledge.php');
require_once('./lib/MySQLConnect.php') ;

// Setting defaults values
if(isset($options['u'])) { $username = $options['u']; } else { $username = "Hub'erte"; }
if(isset($options['m'])) { $message = $options['m']; } else { $message = "Acknowledge by phone"; }
if(isset($options['d'])) { $delay = $options['d']; } else { $delay = "30m"; }

// For Acknowledge all alerts 
if(isset($options['t'])) {

	$SQLParams = "autodel='0'" ;

}
// For Acknowledge by plateform
elseif(isset($options['p']) && $options['p'] != 'ALL') {

	$SQLParams = "plateform='".$options['p']."' AND autodel='0'" ;

}
// For Acknowledge by host
elseif(isset($options['h'])) {

	$SQLParams = "host='".$options['h']."' AND autodel='0'" ;
}
// For Acknowledge by Ack Code
elseif(isset($options['a'])) {

	$SQLParams = "ackcode='".$options['a']."' AND autodel='0'" ;

}
else {
	
	echo $help ;
	exit(1) ;

}

DBconnect() ;

$QueryAlerts = mysql_query("SELECT ackcode FROM hub_alerts WHERE ".$SQLParams) ;
		
while($DataAlerts = mysql_fetch_array($QueryAlerts)) {

	Acknowledge($username, $DataAlerts['ackcode'], $delay, $message);
			
}

mysql_query("DELETE FROM hub_alerts WHERE ".$SQLParams) ;

DBclose() ;

?>
