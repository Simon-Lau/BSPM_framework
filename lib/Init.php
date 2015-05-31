<?php
define('APP_NAME', 'WEB');
define('ROOT_DIR', dirname(dirname(__FILE__))); //bank
define('CTRL_DIR', ROOT_DIR.'/controller');
define('CONF_DIR', ROOT_DIR.'/config');
define('LIB_DIR', ROOT_DIR.'/lib');
define('MOD_DIR', ROOT_DIR.'/model');
define('TMP_DIR', ROOT_DIR.'/cache');
define("LOG_DIR", ROOT_DIR.'/logs/');
define('TPL_DIR', ROOT_DIR.'/templates');
require LIB_DIR."/smarty/Smarty.class.php";
require LIB_DIR."/Model.php";
require LIB_DIR."/Db_Table.php";
require LIB_DIR."/Db.php";
require LIB_DIR."/Controller.php";
require LIB_DIR."/Load.php";
require LIB_DIR."/Request.php";
require LIB_DIR."/Response.php";
require LIB_DIR."/View.php";
require LIB_DIR."/Exception.php";
require LIB_DIR."/Front.php";