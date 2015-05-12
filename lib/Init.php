<?php
/**
* Init.php
* used to init to set some root build path
* by simonLau
* April 30, 2015
**/
define("APP_NAME","BSPM_framework");
define("ROOT_DIR",dirname(dirname(__FILE))); // easy_framework
define("MODEL_DIR",ROOT_DIR."/model");
define("CTRL_DIR", ROOT_DIR."/controller");
define("LIB_DIR",ROOT_DIR."/lib");
define("CONF_DIR",ROOT_DIR."/config");
define("TPL_DIR",ROOT_DIR."/templates");

require LIB_DIR ."/Db_Table.php";
require LIB_DIR ."/Db.php";
require LIB_DIR ."/Model.php";
require LIB_DIR ."/Controller.php";
require LIB_DIR ."/Request.php";
require LIB_DIR ."/Response.php";
require LIB_DIR ."/Exception.php";
require LIB_DIR ."/Load.php";
require LIB_DIR ."/View.php";
require LIB_DIR ."/Front.php";