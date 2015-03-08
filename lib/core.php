<?php
/**
 * Created by PhpStorm.
 * User: Office
 * Date: 3/7/15
 * Time: 3:53 PM
 */
require dirname(__DIR__)."/vendor/autoload.php";
require ROOT."/vendor/gabordemooij/redbean/RedBean/redbean.inc.php";
class core {
    public function init(){
        define("ROOT", dirname(__DIR__));
        date_default_timezone_set('America/Los_Angeles');

        \Purekid\Mongodm\MongoDB::setConfigBlock('default', array(
            'connection' => array(
                'hostnames' => 'localhost', #104.236.112.37
                'database'  => 'scrapper',
//                            'username'  => 'root',
//                            'password'  => '',
                'options'  => array()
            )
        ));

        $this->mysql('localhost','scrapper','root','');
    }

    public function mysql($host, $db, $user, $password){
        \R::setup('mysql:host='.$host.';dbname='.$db, $user, $password);

    }
}