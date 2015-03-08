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
        define("ROOT", dirname(__DIR__));
        require ROOT."/vendor/gabordemooij/redbean/RedBean/redbean.inc.php";

        \Purekid\Mongodm\MongoDB::setConfigBlock('default', array(
            'connection' => array(
                'hostnames' => 'localhost',
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