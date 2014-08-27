#!/usr/bin/php
<?php
# EscalateAlert.php
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


// Shorts arguments
$opts = "i:e:h:s:" ;
$options = getopt($opts);

// Helping
$help = 'EscalateAlert.php -i id -e upperlvl -h host -s service

		-i			Alert ID

		-e			Escalation

		-h			Host
		
		-s			Service

' ;


require_once('./lib/MySQLConnect.php') ;

// Check arguments data
if(!isset($options['i']) || !isset($options['e']) || !isset($options['h']) || !isset($options['s'])) {
        echo $help ;
        exit(1);
}
else {
	DBconnect() ;

	mysql_query("DELETE FROM hub_alerts WHERE 
						host='".$options['h']."' AND 
						service='".$options['s']."' AND
						autodel='0' AND
						id<>'".$options['i']."'");
		
	mysql_query("UPDATE hub_alerts SET 
					id_owner='".$options['e']."',
					last_time=NOW(),
					escalation='1' 
					WHERE id='".$options['i']."'");
		
	DBclose();
}

?>
