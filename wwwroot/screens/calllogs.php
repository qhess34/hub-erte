<?php
# calllogs.php
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

if(@$_SESSION['logged'] != 1) {
	die("Hacking Attempt !");
}
elseif($_SESSION['web_access'] == "admin" || $_SESSION['web_access'] == "user") {
	
	DBconnect();

	if(isset($_POST['id'])) {


	}
		
	if($_SESSION['web_access'] == "admin") {
		$logsfilter = "";
	}
	else {
		$logsfilter = "WHERE id_owner='".$_SESSION['userid']."'";
	}

	if(!isset($_GET['nb'])) {
		$nb = "LIMIT 20";
	}
	elseif($_GET['nb'] == "all") {
		$nb = "";
	}
	else {
		$nb = "LIMIT " . mysql_escape_string($_GET['nb']);
	}

	$QueryLogs = mysql_query("SELECT * FROM hub_alerts_logs ".$logsfilter." ORDER BY create_time DESC ".$nb."");

	DBclose();

?>

	<h2>Calls Logs</h2>
	<div id="callslist">
	<a href="?action=logs&nb=20">Show 20 results</a> - <a href="?action=logs&nb=50">Show 50 results</a> - <a href="?action=logs&nb=100">Show 100 results</a> - <a href="?action=logs&nb=all">Show All results</a><br /><br />
	<table>
		<tr class="callslegend">
			<td>Status</td>
			<td width="160px">Call date</td>
			<td width="60px">Number called</td>
			<td width="120px">Name called</td>

		<?php if($_SESSION['web_access'] == "admin") { ?>
			<td width="60px">User ID</td>
		<?php } ?>

			<td width="100px">Call Status</td>
			<td width="100px">Plateform</td>
			<td width="80px">Host</td>
			<td width="70px">Service</td>
			<td width="50px">Escalation</td>
		
		<?php if($_SESSION['web_access'] == "admin") { ?>
			<td>CallVars</td>
		<?php } ?>

		</tr>


<?php	 while($DataLogs = mysql_fetch_array($QueryLogs)) { 
		$DataLogs = arraystripslashes($DataLogs); ?>
			
                <tr class="callscontent">

		<?php if($DataLogs['recovered'] == 1) {
			$DataLogs['status'] = "green";
		} ?>
                        <td><img src="images/<?=$DataLogs['status'] ?>.gif" /></td>

                        <td><?=$DataLogs['create_time'] ?></td>
                        <td><?=$DataLogs['number'] ?></td>
                        <td><?=$DataLogs['name'] ?></td>

		<?php if($_SESSION['web_access'] == "admin") {	?>
                        <td><?=$DataLogs['id_owner'] ?></td>
		<?php } ?>

			<td><?=$DataLogs['callstatus'] ?></td>
                        <td><?=$DataLogs['plateform'] ?></td>
                        <td><?=$DataLogs['host'] ?></td>
                        <td><?=$DataLogs['service'] ?></td>
                        <td><?=$DataLogs['escalation'] ?></td>

		<?php if($_SESSION['web_access'] == "admin") { ?>
                        <td><input type="text" class="inputtext" size="10" value="<?=$DataLogs['callsvar'] ?>" /></td>
		<?php } ?>
                
		</tr>

<?php	} ?>

	</table>
	</div>

<?php
}
else {

        echo "Access Forbidden !!" ;

}
	
?>
