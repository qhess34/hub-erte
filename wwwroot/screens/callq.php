<?php
# callq.php
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

	$UpdateReturn = NULL ;

	if($_SESSION['web_access'] == "admin") {
		$alertfilter = "";
		$delalertfilter = "";
	}
	else {
		$alertfilter = "WHERE alerts.id_owner='".$_SESSION['userid']."'";
		$delalertfilter = "AND id_owner='".$_SESSION['userid']."'";
	}


	if(isset($_POST['idcall'])) {

		mysql_query("DELETE FROM hub_alerts WHERE id='".$_POST['idcall']."' ".$delalertfilter);

		$UpdateReturn = '<center><b><font color="red">Alert deleted</font></b></center><br />';

	}

	$QueryCalls = mysql_query("SELECT *, alerts.id as id_alert FROM hub_alerts alerts 
									LEFT JOIN hub_users users
				                                        ON alerts.id_owner = users.id
									".$alertfilter);

	DBclose();

?>

	<h2>Calls Queue</h2>
	<?=$UpdateReturn ?>
	<div id="callslist">
	<table>
		<tr class="callslegend">
			<td>Status</td>
			<td width="120px">Alert Open</td>
			<td width="120px">Alert Last Update</td>

		<?php if($_SESSION['web_access'] == "admin") { ?>
			<td width="120px">User</td>
		<?php } ?>

			<td width="100px">Plateform</td>
			<td width="80px">Host</td>
			<td width="80px">Service</td>
			<td width="80px">Escalation</td>
			<td>Notification</td>
			<td>Delete</td>
		</tr>


<?php	 while($DataCalls = mysql_fetch_array($QueryCalls)) { 
		
	$DataCalls = arraystripslashes($DataCalls); ?>
			
                <tr class="callscontent">

		<?php if($DataCalls['recovered'] == 1) {
			$DataCalls['state'] = "green";
		} ?>
                        <td><img src="images/<?=$DataCalls['state'] ?>.gif" /></td>

                        <td><?=$DataCalls['create_time'] ?></td>
                        <td><?=$DataCalls['last_time'] ?></td>

		<?php if($_SESSION['web_access'] == "admin") {	?>
                        <td><?=$DataCalls['name'] ?><br />(<?=$DataCalls['phone'] ?>)</td>
		<?php } ?>

                        <td><?=$DataCalls['plateform'] ?></td>
                        <td><?=$DataCalls['host'] ?></td>
                        <td><?=$DataCalls['service'] ?></td>
                        <td><?=$DataCalls['escalation'] ?></td>
                        <td><?=$DataCalls['autodel'] ?></td>
                
	        <form name="del<?php echo $DataCalls['id_alert'] ?>" method="POST">
	                <td>
        	                <input type="image" name="delcall" src="images/delfile.png" />
                	        <input type="hidden" name="idcall" value="<?php echo $DataCalls['id_alert'] ?>" />
	                </td>
        	</form>
	
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
