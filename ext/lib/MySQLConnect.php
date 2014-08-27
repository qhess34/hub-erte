<?php
# MySQLConnect.php
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

require_once('./Config.php');
require_once('./lib/Common.php');

function DBconnect() {
	global $Config ;

	$mysql_errors = "" ;

	mysql_connect($Config['MySQL_Host'],$Config['MySQL_User'],$Config['MySQL_Password']);

	$mysql_errors .= mysql_error() ;

	mysql_select_db($Config['MySQL_Database']) ;

	$mysql_errors .= mysql_error() ;

	if(!empty($mysql_errors)) {

		ShowLogs('CRITICAL',$mysql_errors);

	}
}

function DBclose() {
	mysql_close() ;
}


?>
