<?php
# index.php
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

session_start();


if($_SESSION['logged'] != 1) {
	header('location:login.php');
}
else {


switch(@$_GET['action']) {

	case "callq":
		$include = "callq.php" ;
		$page_title = "Calls queue" ;
	break;

	case "hno":
		$include = "hno.php" ;
		$page_title = "Non-business days" ;
	break;
	
	case "logs":
		$include = "calllogs.php" ;
		$page_title = "Calls Logs" ;
	break;

	case "web_profile":
		$include = "web_profile.php";
		$page_title = "Web Interface Management";
	break;

	default:
		$include = "profile.php";
		$page_title = "Profile management";
}

require_once('config.php');
require_once('mysqlconnect.php');
require_once('common.php');

include_once('html_header.php');
?>
<div id="header">
	<img height="100px" src="images/logo.png" />
</div>

<div id="left">
	<table id="menu">
<?php
	if($_SESSION['web_access'] == "admin") {
?>
	<tr>
		<td><a href="?action=profile">Users Management</a></td>
	</tr>
        <tr>
                <td><a href="?action=web_profile">Web Interface Management</a></td>
        </tr>
	<tr>
		<td><a href="?action=callq">Calls Queue</a></td>
	</tr>
	<tr>
		<td><a href="?action=hno">Non-business days</a></td>
	</tr>
	<tr>
		<td><a href="?action=logs">Calls Logs</a></td>
	</tr>
<?php
	} else {
?>
	<tr>
		<td><a href="?action=profile">Show profile</a></td>
	</tr>
	<tr>
		<td><a href="?action=callq">My Calls Queue</a></td>
	</tr>
	<tr>
		<td><a href="?action=logs">My Calls logs</a></td>
	</tr>
<?php
}
?>
	<tr>
		<td><a href="login.php">LogOff</a></td>
	</tr>
	</table>
</div>

<div id="content">
	<?php include_once('./screens/'.$include); ?>

</div>

<div id="footer">
Hub'erte - Copyright 2014 - <a href="http://www.my-linux.fr">http://www.my-linux.fr</a>
</div>
<?php
include_once('html_footer.php');

}
?>
