<?php
# login.php
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

session_start();

if(isset($_SESSION['logged'])) {
	session_destroy();
}

require_once('config.php');
require_once('mysqlconnect.php');

$page_title = "Login" ;
$login_return = NULL;

if(isset($_POST['mail'])) {
	DBconnect();

	$mail = mysql_real_escape_string($_POST['mail']);
	$password_md5 = md5(mysql_real_escape_string($_POST['password']));
	
	$QueryUser = mysql_query("SELECT id, mail, web_password, web_access FROM hub_users WHERE 
									mail='".$mail."' AND 
									web_password='".$password_md5."' AND
									web_access<>'disable'");
	
	if(mysql_num_rows($QueryUser) != 1) {
		$login_return = '<b><font color="red">Login failed, try again !</font></b><br />' ;
	}
	else {
		$DataUser = mysql_fetch_array($QueryUser);
		$_SESSION['logged'] = 1;
		$_SESSION['web_access'] = $DataUser['web_access'];
		$_SESSION['userid'] = $DataUser['id'];

		header("location:index.php");
	}


	DBclose();
}

include_once('html_header.php');
?>
<div align="center" id="login-logo">
	<img src="images/logo.png" />
</div>

<div id="login-form">
	<form method="POST">
		<h4>Please login : </h4>
		<?=$login_return ?>
		<label>Mail Address : <input type="text" class="inputtext"  name="mail" /></label><br />
		<label>Password : <input type="password" class="inputtext" name="password" /></label><br />
		<input type="submit" class="inputsubmit" value="Validate" />
	</form>
</div>
<?php
include_once('html_footer.php');
?>
