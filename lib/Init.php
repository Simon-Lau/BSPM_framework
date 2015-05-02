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
