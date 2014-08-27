<?php
# Acknowledge.php
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


function Acknowledge($User, $AckCode, $Delay, $msg) {

        global $Config;

        $message = 'From: '.$User.' \n' ;
        $message .= 'Subject: Hobbit ['.$AckCode.'] delay='.$Delay.'\n' ;
        $message .= '\n' ;
        $message .= $msg . '\n' ;
        $message .= 'EOF' ;

        $message = str_replace("'","&#39;",$message);
        $message = str_replace('"',"&#34;",$message);
        $huberte_user = $Config['Huberte_user'];
        $xymon_ip = $Config['Xymon_IP'];
        $xymon_path = $Config['Xymon_Prefix'];

        system('echo "echo -e \"'.$message.'\" | '.$xymon_path.'/server/bin/hobbit-mailack" | sudo su - '.$huberte_user.' -c "ssh '.$xymon_ip.'"');

}


?>
