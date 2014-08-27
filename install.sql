SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE `hub_alerts` (
  `id` int(11) NOT NULL auto_increment,
  `id_owner` int(11) NOT NULL,
  `create_time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `last_time` timestamp NOT NULL default '0000-00-00 00:00:00',
  `plateform` varchar(255) NOT NULL,
  `host` varchar(255) NOT NULL,
  `service` varchar(255) NOT NULL,
  `state` varchar(20) NOT NULL,
  `recovered` tinyint(4) NOT NULL,
  `escalation` tinyint(4) NOT NULL,
  `autodel` tinyint(4) NOT NULL default '0',
  `ackcode` varchar(20) NOT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `hub_alerts_logs` (
  `id` int(11) NOT NULL auto_increment,
  `id_owner` int(11) NOT NULL,
  `create_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `number` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `host` varchar(255) NOT NULL,
  `service` varchar(255) NOT NULL,
  `plateform` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL,
  `escalation` tinyint(4) NOT NULL,
  `recovered` tinyint(4) NOT NULL,
  `callstatus` varchar(255) NOT NULL default 'NO ANSWERED',
  `callsvar` text NOT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `hub_hno` (
  `id` int(11) NOT NULL auto_increment,
  `date` date NOT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `hub_users` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default 'Undefined',
  `phone` varchar(20) NOT NULL,
  `mail` varchar(255) NOT NULL default 'Undefined',
  `language` varchar(2) NOT NULL default 'fr',
  `usr_plateform` varchar(255) NOT NULL default 'ALL',
  `acl_ack_alert` varchar(1) NOT NULL default 'N',
  `acl_ack_srv` varchar(1) NOT NULL default 'N',
  `acl_ack_ptf` varchar(1) NOT NULL default 'N',
  `acl_ack_all` varchar(1) NOT NULL default 'N',
  `acl_escalation` varchar(1) NOT NULL default 'N',
  `id_upper` int(11) NOT NULL,
  `acl_recovered` varchar(1) NOT NULL default 'Y',
  `web_access` varchar(10) NOT NULL default 'disable',
  `web_password` varchar(255) NOT NULL,
  `enable` varchar(1) NOT NULL default 'Y',
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

INSERT INTO `hub_users` (`id`, `name`, `phone`, `mail`, `language`, `usr_plateform`, `acl_ack_alert`, `acl_ack_srv`, `acl_ack_ptf`, `acl_ack_all`, `acl_escalation`, `id_upper`, `acl_recovered`, `web_access`, `web_password`, `enable`) VALUES 
(1, 'Administrator', '', 'admin', 'fr', 'ALL', 'N', 'N', 'N', 'N', 'N', 0, 'Y', 'admin', '5f4dcc3b5aa765d61d8327deb882cf99', 'Y');

