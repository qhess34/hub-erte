<?php
# hno.php
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

if(@$_SESSION['logged'] != 1) {
	die("Hacking Attempt !");
}
elseif($_SESSION['web_access'] == "admin") {
	
	DBconnect();

	$UpdateReturn = NULL;
	if(isset($_POST['id'])) {
	
		$date = mysql_escape_string($_POST['date']);	
		
		mysql_query("UPDATE hub_hno SET date='".$date."' WHERE id='".$_POST['id']."'");

		$UpdateReturn = '<center><b><font color="green">Date updated</font></b></center><br />';
	}	

	if(isset($_POST['idhno'])) {
		
		mysql_query("DELETE FROM hub_hno WHERE id='".$_POST['idhno']."'");

		$UpdateReturn = '<center><b><font color="red">Date deleted</font></b></center><br />';
	}

	if(isset($_POST['addfile'])) {
		
		mysql_query("INSERT INTO hub_hno () VALUES ()");

		$UpdateReturn = '<center><b><font color="green">Date added</font></b></center><br />';
	}

	$QueryHNO = mysql_query("SELECT * FROM hub_hno WHERE date >= CURRENT_DATE() OR date = '0000-00-00' ORDER BY date");

?>

	<h2>Non-business days management</h2>
	<?=$UpdateReturn ?>
	<div id="hnolist">
	<table>
		<tr class="hnolegend">
			<td width="120px">Date</td>
			<td>Edit</td>
			<td>Delete</td>
		</tr>

<?php	 while($DataHNO = mysql_fetch_array($QueryHNO)) { ?>
		
                <tr class="hnocontent">
		<form method="POST" name="edithno<?=$DataHNO['id'] ?>">
			<td>
				<input type="hidden" name="id" value="<?=$DataHNO['id'] ?>" />
				<input type="text" class="inputtext" size="10"  name="date" value="<?=$DataHNO['date'] ?>" />
			</td>
			<td>	
				<input type="image" name="valid"  onclick="this.form.submit();" src="images/edit.png" />
			</td>
		</form>
                <form name="del<?php echo $DataHNO['id'] ?>" method="POST">
                        <td>
                                <input type="image" name="delhno" src="images/delfile.png" />
                                <input type="hidden" name="idhno" value="<?php echo $DataHNO['id'] ?>" />
                        </td>
                </form>
		</tr>

<?php	} ?>

	</table>
        <form name="add" method="post">
                <p align="right">
                        <input type="image" src="images/addfile.png" />
                        <input type="hidden" name="addfile" value="1"  />
                </p>
        </form>

	</div>

<?php	
}
else {
	
	echo "Access Forbidden !!" ;

}
?>
