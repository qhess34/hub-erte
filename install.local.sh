#!/bin/sh
# install.local.sh
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
cp ext/Config.php.orig ext/Config.php
cp wwwroot/config.php.orig wwwroot/config.php

if [ -e install.hobbit.sh ]; then
	rm install.hobbit.sh 
fi

echo "Welcome on Hub'erte Installation"
echo
echo "===> User creation <==="
echo
echo "Where do you want to install Hub'erte [/usr/local/huberte] ?"
read prefix
echo "What userid will be run Hub'erte [huberte] ?"
read userid
echo "What groupid will be run Hub'erte [huberte] ?"
read groupid

if [ "$prefix" == "" ]; then
        prefix="/usr/local/huberte"
fi

if [ "$userid" == "" ]; then
        userid="huberte"
fi

if [ "$groupid" == "" ]; then
        groupid="huberte"
fi

echo
echo "User environnement creation ..."
echo 

mkdir -p $prefix
groupadd $groupid
useradd -d $prefix -g $groupid $userid
chown -R $userid:$groupid $prefix

sed -i s'|PREFIX|'$prefix'|' ext/Config.php
sed -i s'/HUBERTE_USER/'$userid'/' ext/Config.php

echo
echo "===> Hobbit/Xymon & Asterisk <==="
echo
echo "Asterisk and Xymon / Hobbit are on same server [yes] ?"
read oneserver

echo "What is Xymon/Hobbit username ?"
read hobbituser

echo "What is Xymon/Hobbit directory ?"
read xymonprefix

echo "What is Asterisk username [asterisk] ?"
read asteriskuserid

if [ "$hobbituser" == "" ] || [ "$xymonprefix" == "" ]; then
        echo "Error, one field is blank"
        exit 1
fi

if [ "$asteriskuserid" == "" ]; then
        asteriskuserid="asterisk"
fi

if [ "$oneserver" == "N" ] || [ "$oneserver" == "n" ] || [ "$oneserver" == "no" ] || [ "$oneserver" == "NO" ]; then

	oneserver=FALSE

	su - $userid -c "mkdir -p $prefix/.ssh"
	su - $userid -c "ssh-keygen -t rsa -b 2048 -f $prefix/.ssh/id_rsa -P \"\""

	echo "#!/bin/sh
	# install.hobbit.sh
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
	#" > install.hobbit.sh

	echo "mkdir -p $prefix" >> install.hobbit.sh
	echo "groupadd $groupid" >> install.hobbit.sh
	echo "useradd -d $prefix -g $groupid $userid" >> install.hobbit.sh
	echo "chown -R $userid:$groupid $prefix" >> install.hobbit.sh
	echo "su - $userid -c \"mkdir -p $prefix/.ssh\""  >> install.hobbit.sh
	echo "su - $userid -c \"ssh-keygen -t rsa -b 2048 -f $prefix/.ssh/id_rsa -P \\\"\\\"\"" >> install.hobbit.sh
	echo "echo \"`cat $prefix/.ssh/id_rsa.pub`\" >> $prefix/.ssh/authorized_keys" >> install.hobbit.sh
	echo "echo"  >> install.hobbit.sh
	echo "echo \"Copy following line on $prefix/.ssh/authorized_keys, on Hub'erte Server :\"" >> install.hobbit.sh
	echo "cat $prefix/.ssh/id_rsa.pub" >> install.hobbit.sh
	echo "echo \"$hobbituser   ALL=(ALL) NOPASSWD:ALL\" >> /etc/sudoers" >> install.hobbit.sh

	echo "What is IP of Xymon server for ssh from here ?"
	read xymonip
	echo "What is IP of local server for ssh from Xymon/Hobbit server ?"
	read localip

	cp ext/HobbitAlert.sh.ssh ext/HobbitAlert.sh

	sed -i s'/HUBERTE_IP/'$localip'/' ext/HobbitAlert.sh
	sed -i s'/XYMON_IP/'$xymonip'/' ext/Config.php

else
	oneserver=TRUE
	cp ext/HobbitAlert.sh.local ext/HobbitAlert.sh
	echo "$hobbituser   ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers
fi

sed -i s'|PREFIX|'$prefix'|' ext/HobbitAlert.sh
sed -i s'|XYMON_PATH|'$xymonprefix'|' ext/Config.php

echo "$asteriskuserid        ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers


echo
echo "===> MySQL Database <==="
echo "Please create MySQL user which have grants to modify a database"
echo
echo "MySQL Host [127.0.0.1] ?"
read mysqlhost
echo "MySQL Username ?"
read mysqluser
echo "MySQL Password ?"
read mysqlpassword
echo "MySQL Database ?"
read mysqldb

if [ "$mysqlhost" == "" ]; then
        mysqlhost="127.0.0.1"
fi

if [ "$mysqluser" == "" ] || [ "$mysqlpassword" == "" ] || [ "$mysqldb" == "" ]; then
        echo "Error, one field is blank"
        exit 1
fi

echo 
echo "MySQL database installation ..."
echo

mysql -u$mysqluser -p$mysqlpassword -h$mysqlhost $mysqldb < ./install.sql

if [ ! $? -eq 0 ]; then
        echo "MySQL installation error"
        exit 1
fi

sed -i s'/MYSQL_HOST/'$mysqlhost'/' ext/Config.php
sed -i s'/MYSQL_HOST/'$mysqlhost'/' wwwroot/config.php

sed -i s'/MYSQL_USER/'$mysqluser'/' ext/Config.php
sed -i s'/MYSQL_USER/'$mysqluser'/' wwwroot/config.php

sed -i s'/MYSQL_PASSWORD/'$mysqlpassword'/' ext/Config.php
sed -i s'/MYSQL_PASSWORD/'$mysqlpassword'/' wwwroot/config.php

sed -i s'/MYSQL_DB/'$mysqldb'/' ext/Config.php
sed -i s'/MYSQL_DB/'$mysqldb'/' wwwroot/config.php


echo "===> Asterisk Manager <==="
echo
echo "Manager host [127.0.0.1] ?"
read managerhost
echo "Manager port [5038] ?"
read managerport
echo "Manager user ?"
read manageruser
echo "Manager password ?"
read managerpassword

if [ "$managerhost" == "" ]; then
        managerhost="127.0.0.1"
fi

if [ "$managerport" == "" ]; then
        managerport="5038"
fi

if [ "$manageruser" == "" ]; then
        echo "Error, one field is blank"
        exit 1
fi

sed -i s'/MANAGER_HOST/'$managerhost'/' ext/Config.php
sed -i s'/MANAGER_PORT/'$managerport'/' ext/Config.php
sed -i s'/MANAGER_USER/'$manageruser'/' ext/Config.php
sed -i s'/MANAGER_SECRET/'$managerpassword'/' ext/Config.php

echo "===> Others <==="
echo
echo "What is the context used to call numbers from huberte ?"
read context
echo "What is a delivery strategy to apply [per_alert] ?"
read delstrat
echo "What is escalation delay to set [1800] (seconds) ?"
read escaldelay
echo "Non-business hour start [20] ?"
read hnostart
echo "Non-business hour end [8] ?"
read hnoend

if [ "$delstrat" == "" ]; then
        delstrat="per_alert"
fi

if [ "$escaldelay" == "" ]; then
        escaldelay="1800"
fi

if [ "$hnostart" == "" ]; then
        hnostart="20"
fi

if [ "$hnoend" == "" ]; then
        hnoend="8"
fi

if [ "$context" == "" ]; then
        echo "Error, one field is blank"
        exit 1
fi

sed -i s'/CONTEXT_CALL/'$context'/' ext/Config.php
sed -i s'/DELIVERY_STRATEGY/'$delstrat'/' ext/Config.php
sed -i s'/ESCALATION_DELAY/'$escaldelay'/' ext/Config.php
sed -i s'/HNO_START/'$hnostart'/' ext/Config.php
sed -i s'/HNO_END/'$hnoend'/' ext/Config.php

cp -r sounds $prefix/
cp -r ext $prefix/
cp COPYING $prefix/
cp -r asterisk $prefix/
chown -R $userid:$groupid $prefix

echo
echo "===> Installation Complete <==="
echo
if [ "$oneserver" == "FALSE" ]; then
	echo "Copy install.hobbit.sh script on Hobbit/Xymon server and run it"
	echo
fi
echo "Add following line at the end of your Asterisk extensions.conf file :"
echo "#include $prefix/asterisk/extensions_huberte.conf"

