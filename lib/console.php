<?php
/**
 * Created by PhpStorm.
 * User: Office
 * Date: 3/5/15
 * Time: 12:56 PM
 */

require dirname(__DIR__)."/vendor/autoload.php";
class console {
    private $argv = array();
    public function __construct($argv){
        $command = isset($argv[1])? $argv[1] : "Undefined";
        switch($command)
        {
            case "scrap":
                $dir = isset($argv[2])? $argv[2] : "";


                break;
        }
    }

    public function scrap($dir){
        //files in files dir
        //Load 100 files each create object and create json from that OR push to db in one batch
        //
        //TODO call scrapper service to pass
    }
}