#!/usr/bin/php
<?php
# CallUpdateStatus.php
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


// Arguments list
$opts = "i:s:";

$options = getopt($opts);

// Helping
$help = 'CallUpdateStatus.php -i id -s status 

		-i			Id of current log

		-s			New status

' ;

require_once('./lib/MySQLConnect.php');

// Check arguments data
if(!isset($options['i']) || !isset($options['s'])) {
        echo $help;
        exit(1);
}
else {
	DBconnect() ;

		mysql_query("UPDATE hub_alerts_logs SET	callstatus='".$options['s']."' WHERE id='".$options['i']."'");

	DBclose() ;
}

?>
