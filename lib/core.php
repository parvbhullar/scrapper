<?php
/**
 * Created by PhpStorm.
 * User: Office
 * Date: 3/7/15
 * Time: 3:53 PM
 */
require dirname(__DIR__)."/vendor/autoload.php";
//define("ROOT", dirname(__DIR__));
if(!class_exists('\R'))
    require dirname(__DIR__)."/vendor/gabordemooij/redbean/RedBean/redbean.inc.php";
class core {
    public function init(){

        date_default_timezone_set('America/Los_Angeles');

        \Purekid\Mongodm\MongoDB::setConfigBlock('default', array(
            'connection' => array(
                'hostnames' => 'localhost', #104.155.217.153
                'database'  => 'new-bi',
//                            'username'  => 'root',
//                            'password'  => '',
                'options'  => array()
            )
        ));

        $this->mysql('localhost','cf_feeds','root','');
    }

    public function mysql($host, $db, $user, $password){
        \R::setup('mysql:host='.$host.';dbname='.$db, $user, $password);

    }
}