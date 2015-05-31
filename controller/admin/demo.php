<?php
class demo_Controller extends Controller {

    function init() {
        $this->_Demo = Load::model('test', 'admin');
    }

    function testAction() {
        $data = $this->_Demo->read_demo();
        $name = $data[0]["user_name"];
        $tpl = "admin_demo.tpl";
        $this->assign("user_name", $name);
        $this->display($tpl);
    }
    
} 