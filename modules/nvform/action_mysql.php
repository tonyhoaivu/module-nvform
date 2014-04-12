<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-10-2010 20:59
 */

if( ! defined( 'NV_IS_FILE_MODULES' ) ) die( 'Stop!!!' );

$sql_drop_module = array();

$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . ";";

$sql_create_module = $sql_drop_module;

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . " (
	id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	title varchar(255) NOT NULL,
 	alias varchar(255) NOT NULL,
 	image varchar(255) DEFAULT '',
 	description text,
 	weight smallint(4) NOT NULL DEFAULT '0',
 	add_time int(11) NOT NULL DEFAULT '0',
	status tinyint(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (id),
	UNIQUE KEY alias (alias)
) ENGINE=MyISAM";