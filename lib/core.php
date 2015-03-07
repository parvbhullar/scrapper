<?php
/**
 * Created by PhpStorm.
 * User: Office
 * Date: 3/7/15
 * Time: 3:53 PM
 */
require dirname(__DIR__)."/vendor/autoload.php";

class core {
    public function init(){
        \Purekid\Mongodm\MongoDB::setConfigBlock('default', array(
            'connection' => array(
                'hostnames' => 'localhost',
                'database'  => 'scrapper',
//                            'username'  => 'root',
//                            'password'  => '',
                'options'  => array()
            )
        ));
    }
}