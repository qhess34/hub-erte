<?php
# profile.php
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
elseif($_SESSION['web_access'] == "admin") {
	
	DBconnect();

	$UpdateReturn = NULL ;

	if(isset($_POST['id'])) {

		$checkbox = array('acl_recovered','acl_ack_alert','acl_ack_srv','acl_ack_ptf','acl_ack_all','acl_escalation','enable');

		foreach($checkbox as $CheckBoxName) {
			if(isset($_POST[$CheckBoxName])) {
				$_POST[$CheckBoxName] = 'Y' ;
			}
			else {
				$_POST[$CheckBoxName] = 'N' ;
			}
		}
		
		$toUpdate = "";

		foreach($_POST as $param => $value) {
			if($param != 'valid_x' && $param != 'valid_y' && $param != 'id') {
			
				$value = mysql_escape_string(htmlentities($value));
				$toUpdate .= $param."='".$value."',";

			}
		}
		$toUpdate = substr($toUpdate, 0, -1); 
		
	
		mysql_query("UPDATE hub_users SET ".$toUpdate." WHERE id='".$_POST['id']."'");
		$UpdateReturn = '<center><b><font color="green">User updated</font></b></center><br />';

	}

	if(isset($_POST['deluser_x']))
	{
        	$iduser = $_POST['iduser'] ;
	        mysql_query("delete from hub_users where id='$iduser'") ;
        	$UpdateReturn = '<center><b><font color="red">Customer deleted !</font></b></center><br />' ;
	}

        if(isset($_POST['adduser']))
        {
                mysql_query("insert into hub_users () values ()") ;
                $UpdateReturn = '<center><b><font color="green">Customer added !</font></b></center><br />' ;
        }

	$QueryUsers = mysql_query("SELECT * FROM hub_users");

	DBclose();

?>

	<h2>Users profile management</h2>
	<?=$UpdateReturn ?>
	<div id="userslist">
	<table>
		<tr class="userslegend">
			<td width="50px">ID</td>
			<td width="150px">Name</td>
			<td width="150px">Phone Number</td>
			<td width="160px">Mail Address</td>
			<td width="10px">Lang.</td>
			<td width="80px">Plateform</td>
			<td width="80px">Upper Level ID</td>
			<td width="80px">Recovered</td>
			<td>Ack. Alert</td>
			<td>Ack. Server</td>
			<td>Ack. PlateForm Alerts</td>
			<td>Ack. All Alerts</td>
			<td>Can Escalate</td>
			<td>Enable / Disable</td>
			<td>Edit</td>
			<td>Delete</td>
		</tr>
		<tr class="userslegend2">
			<td colspan="8">Informations</td>
			<td colspan="6">ACL</td>
			<td colspan="2"> </td>
		</tr>


<?php	 while($DataUsers = mysql_fetch_array($QueryUsers)) { 
		$DataUsers = arraystripslashes($DataUsers); ?>
		
                <tr class="userscontent">
		<form method="POST" name="edituser<?=$DataUsers['id'] ?>">
                        <td>
				<?=$DataUsers['id'] ?>
				<input type="hidden" name="id" value="<?=$DataUsers['id'] ?>" />
			</td>
                        <td><input type="text" class="inputtext" size="15" name="name" value="<?=$DataUsers['name'] ?>" /></td>
                        <td><input type="text" class="inputtext" size="10" name="phone" value="<?=$DataUsers['phone'] ?>" /></td>
                        <td><input type="text" class="inputtext" size="20" name="mail" value="<?=$DataUsers['mail'] ?>" /></td>
                        <td><input type="text" class="inputtext" size="2" name="language" value="<?=$DataUsers['language'] ?>" /></td>
                        <td><input type="text" class="inputtext" size="10" name="usr_plateform" value="<?=$DataUsers['usr_plateform'] ?>" /></td>
                        <td><input type="text" class="inputtext" size="2" name="id_upper" value="<?=$DataUsers['id_upper'] ?>" /></td>
                        <td>
			   <input type="checkbox" name="acl_recovered" <?=status2check($DataUsers['acl_recovered']) ?> />
			</td>
                        <td>
			   <input type="checkbox" name="acl_ack_alert" <?=status2check($DataUsers['acl_ack_alert']) ?> />
			</td>
                        <td>
			   <input type="checkbox" name="acl_ack_srv" <?=status2check($DataUsers['acl_ack_srv']) ?> />
			</td>
                        <td>
			   <input type="checkbox" name="acl_ack_ptf" <?=status2check($DataUsers['acl_ack_ptf']) ?> />
			</td>
                        <td>
			   <input type="checkbox" name="acl_ack_all" <?=status2check($DataUsers['acl_ack_all']) ?> />
			</td>
                        <td>
			   <input type="checkbox" name="acl_escalation" <?=status2check($DataUsers['acl_escalation']) ?> />
			</td>
                        <td>
			   <input type="checkbox" name="enable" <?=status2check($DataUsers['enable']) ?> />
			</td>
                        <td><input type="image" name="valid"  onclick="this.form.submit();" src="images/edit.png" /></td>
		</form>
                
	        <form name="del<?php echo $DataUsers['id'] ?>" method="POST">
	                <td>
        	                <input type="image" name="deluser" src="images/deluser.png" />
                	        <input type="hidden" name="iduser" value="<?php echo $DataUsers['id'] ?>" />
	                </td>
        	</form>
	
		</tr>

<?php	} ?>

	</table>

	<form name="add" method="post">
	        <p align="right">
	                <input type="image" src="images/adduser.png" />
	                <input type="hidden" name="adduser" value="1"  />
	        </p>
	</form>

	</div>

<?php	
}
elseif($_SESSION['web_access'] == "user") {

	DBconnect();

	$UpdateReturn = NULL ;
	if(isset($_POST['phone'])) {

		$toUpdate = "" ;

                $checkbox = array('acl_recovered','enable');

                foreach($checkbox as $CheckBoxName) {
                        if(isset($_POST[$CheckBoxName])) {
                                $_POST[$CheckBoxName] = 'Y' ;
                        }
                        else {
                                $_POST[$CheckBoxName] = 'N' ;
                        }
			$toUpdate .= $CheckBoxName . "='". $_POST[$CheckBoxName] . "'," ;
                }

		if(!empty($_POST['web_password'])) {

			$toUpdate .= "web_password='" . md5(mysql_escape_string($_POST['web_password'])) ."'," ;

		}

		$toUpdate .= "phone='" . mysql_escape_string(htmlentities($_POST['phone'])) . "'," ;
		$toUpdate .= "mail='" . mysql_escape_string(htmlentities($_POST['mail'])) . "'," ;
		$toUpdate .= "language='" . mysql_escape_string(htmlentities($_POST['language'])) . "'" ; 
		
		mysql_query("UPDATE hub_users SET ".$toUpdate." WHERE id='".$_SESSION['userid']."'");
		$UpdateReturn = '<center><b><font color="green">Profile Updated</font></b></center><br />';
	}

	$QueryUser = mysql_query("SELECT * FROM hub_users WHERE id='".$_SESSION['userid']."' LIMIT 1");
	$DataUser = mysql_fetch_array($QueryUser);
	$DataUser = arraystripslashes($DataUser);

	$QueryUpper = mysql_query("SELECT name FROM hub_users WHERE id='".$DataUser['id_upper']."' LIMIT 1");
	$DataUpper = mysql_fetch_array($QueryUpper) ;

        if($DataUpper) {

                $DataUpper = arraystripslashes($DataUpper);

        }


	$Upper = $DataUpper['name'] ;

	$QueryUnder = mysql_query("SELECT name FROM hub_users WHERE id_upper='".$DataUser['id']."'");
	$Under = "";
	while($DataUnder = mysql_fetch_array($QueryUnder)) {
		$DataUnder = arraystripslashes($DataUnder);

		$Under .= $DataUnder['name'] . '<br />';
	}

	DBclose();

?>
	<h2>My Profile</h2>

	<div id="userprofile">
	<?=$UpdateReturn ?>
		<form method="POST">
		<table>
			<tr>
				<td width="260px" class="userlegend"><i>ID</i></td>
				<td width="150px" class="uservalue"><?=$DataUser['id'] ?></td>
			</tr>
                        <tr>
                                <td class="userlegend"><i>Name</i></td>
                                <td class="uservalue"><?=$DataUser['name'] ?></td>
                        </tr>
                        <tr>
                                <td class="userlegend">Phone Number<br /><span class="description">(Add one "0" next number, if external number)</span>
				</td>
                                <td class="uservalue">
					<input type="text" class="inputtext" name="phone" value="<?=$DataUser['phone'] ?>" />
				</td>
                        </tr>
                        <tr>
                                <td class="userlegend">Mail address<br /><span class="description">Caution : Your mail address is used to login</span>
			</td>
                                <td class="uservalue">
					<input type="text" class="inputtext" name="mail" value="<?=$DataUser['mail'] ?>" />
			        </td>
                        </tr>
                        <tr>
                                <td class="userlegend">Password</td>
                                <td class="uservalue"><input type="password" class="inputtext" name="web_password" value="" /></td>
                        </tr>
                        <tr>
                                <td class="userlegend">Language</td>
                                <td class="uservalue">
					<input type="text" class="inputtext" size="2" name="language" value="<?=$DataUser['language'] ?>" />
			</td>
                        </tr>
                        <tr>
                                <td class="userlegend"><i>Plateform</i></td>
                                <td class="uservalue"><?=$DataUser['usr_plateform'] ?></td>
                        </tr>
                        <tr>
                                <td class="userlegend">Receive recovered<br /><span class="description">Enable calls when an alert is recovered.</span>
				</td>
                                <td class="uservalue">
					<input type="checkbox" name="acl_recovered" <?=status2check($DataUser['acl_recovered']) ?> />
				</td>
                        </tr>
                        <tr>
                                <td class="userlegend">Enable Call Notifications<br /><span class="description">This option does not disable escalation</span></td>
                                <td class="uservalue">
				<input type="checkbox" name="enable" <?=status2check($DataUser['enable']) ?> /></td>
                        </tr>
                        <tr>
                                <td class="userlegend"><i>Under Level</i></td>
                                <td class="uservalue"><?=$Under ?></td>
                        </tr>
                        <tr>
                                <td class="userlegend"><i>Upper Level</i></td>
                                <td class="uservalue"><?=$Upper ?></td>
                        </tr>
		</table>
		<p align="right">
			<input type="image" name="valid"  onclick="this.form.submit();" src="images/edit.png" />
		</p>
		</form>

	</div>
<?php
}
?>
