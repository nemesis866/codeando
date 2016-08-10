/************************************************
Base de datos sistema de estadisticas

Proyecto: Codeando.org
Author: Paulo Andrade
Email: source.compu@gmail.com
Web: http://www.pauloandrade1.com
************************************************/

CREATE TABLE IF NOT EXISTS `stats_Day` (
  	`id` int(11) NOT NULL auto_increment,
	`day` varchar(10) NOT NULL default '',
	`user` int(10) NOT NULL default '0',
	`view` int(10) NOT NULL default '0',
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci AUTO_INCREMENT=0 ;
    
CREATE TABLE IF NOT EXISTS `stats_IPs` (
	`id` int(11) NOT NULL auto_increment,
	`ip` varchar(15) NOT NULL default '',
	`time` int(20) NOT NULL default '0',
	`online` int(20) NOT NULL default '0',
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci AUTO_INCREMENT=0 ;
    
CREATE TABLE IF NOT EXISTS `stats_Page` (
	`id` int(11) NOT NULL auto_increment,
	`day` varchar(10) NOT NULL default '',
	`page` varchar(255) NOT NULL default '',
	`view` int(10) NOT NULL default '0',
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci AUTO_INCREMENT=0 ;
    
CREATE TABLE IF NOT EXISTS `stats_Referer` (
	`id` int(11) NOT NULL auto_increment,
	`day` varchar(10) NOT NULL default '',
	`referer` varchar(255) NOT NULL default '',
	`view` int(10) NOT NULL default '0',
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci AUTO_INCREMENT=0 ;
    
CREATE TABLE IF NOT EXISTS `stats_Keyword` (
	`id` int(11) NOT NULL auto_increment,
	`day` varchar(10) NOT NULL default '',
	`keyword` varchar(255) NOT NULL default '',
	`view` int(10) NOT NULL default '0',
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci AUTO_INCREMENT=0 ;
    
CREATE TABLE IF NOT EXISTS `stats_Language` (
	`id` int(11) NOT NULL auto_increment,
	`day` varchar(10) NOT NULL default '',
	`language` varchar(2) NOT NULL default '',
	`view` int(10) NOT NULL default '0',
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci AUTO_INCREMENT=0 ;