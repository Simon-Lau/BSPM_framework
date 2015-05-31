<?php

class admin_test_Model extends Model{
    function init(){
       $this->_userDemoDB  = Load::table("test", "db_cfg_user");
    }
    
    function read_demo(){
        $sql = "select * from user";
        $data = $this->_userDemoDB->queryAll($sql);
        return $data;
    }
}