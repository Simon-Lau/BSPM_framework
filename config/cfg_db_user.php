<?php

/*
 *  example of create a database configue
 *  by simonLau 
 *  2015-04-30
 */

return array(
    'dbname' => 'mysql',
    'charset' => 'utf8',
    'write' => array(
        'default' => '127.0.0.1',
        //'dbhost' => 'www.hit.edu.cn',  //use zkname and need to decode it or you can use just a ip address
        'dbhost' => '127.0.0.1',
        'username' => 'webframe',
        'password' => 'webframe',
        'port' => '3303',
    ),
    'read' => array(
        'default' => '127.0.0.1',
        // 'dbhost' => 'www.hit.edu.cn',
        'dbhost' => '127.0.0.1',
        'username' => 'webframe_r',
        'password' => 'webframe_read',
        'port' => '3302',
    )
);