<?php
# web_profile.php
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
	
		$toUpdate = "web_access='" . $_POST['web_access'] . "'" ;
 
		if(!empty($_POST['web_password'])) {
	
			$toUpdate .= ", web_password='" . md5(mysql_escape_string($_POST['web_password'])) ."'" ;

		}
			
		mysql_query("UPDATE hub_users SET ".$toUpdate." WHERE id='".$_POST['id']."'");
		
		$UpdateReturn = '<center><b><font color="green">User '. $_POST['id'] . ' updated</font></b></center><br />';
	}	

	$QueryUsers = mysql_query("SELECT id,name,mail,web_access,web_password FROM hub_users");

?>

	<h2>Web Interface management</h2>
	<?=$UpdateReturn ?>
	<div id="userslist">
	<table>
		<tr class="userslegend">
			<td width="50px">ID</td>
			<td width="150px">Name</td>
			<td width="160px">Mail Address</td>
			<td width="80px">Password</td>
			<td width="80px">Access</td>
			<td>Edit</td>
		</tr>


<?php	 while($DataUsers = mysql_fetch_array($QueryUsers)) { 
		$DataUsers = arraystripslashes($DataUsers); ?>

                <tr class="userscontent">
		<form method="POST" name="edituser<?=$DataUsers['id'] ?>">
                        <td>
				<?=$DataUsers['id'] ?>
				<input type="hidden" name="id" value="<?=$DataUsers['id'] ?>" />
			</td>
                        <td><?=$DataUsers['name'] ?></td>
                        <td><?=$DataUsers['mail'] ?></td>
                        <td><input type="password" class="inputtext" size="10" name="web_password" value="" /></td>
                        <td>
			<select name="web_access">
				<option selected><?=$DataUsers['web_access'] ?></option>
				<option>disable</option>
				<option>admin</option>
				<option>user</option>
			</select>
                        </td>
			<td><input type="image" name="valid"  onclick="this.form.submit();" src="images/edit.png" /></td>
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
