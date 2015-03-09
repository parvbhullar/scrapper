<?php
/**
 * Created by PhpStorm.
 * User: Office
 * Date: 3/6/15
 * Time: 4:04 PM
 */

namespace Multithreading;


use Hq\CrawlBundle\CustomClasses\ESParser;
use Hq\CrawlBundle\CustomClasses\ESProfile;
use Hq\CrawlBundle\CustomClasses\HQCrawlConstants;
use Hq\CrawlBundle\Services\LinkedInParserService;
use Services\ScrapperService;

class ProfileParseWorker extends \Thread {
    private $workerId;
    private $profiles;
    private $batchLimit;
    private $container, $dm, $rootPath;
    private $threadTook = "NA";
    public function __construct($id, $container, $profiles, $rootPath = false)
    {
        $this->workerId = $id;
        $this->profiles = $profiles;
        $this->container = $container;
//        $this->dm =  $this->container->get('doctrine_mongodb')->getManager();
        $this->rootPath = $rootPath;
    }

    public function start($options = PTHREADS_INHERIT_NONE){
        parent::start($options);
    }

    public function _print($msg, $print = true){
        if($print)
            echo $msg . PHP_EOL;
    }

    public function getLast(){
        return $this->last;
    }

    public function parseProfiles($rootPath = false) {
        $start_time = time();
//        echo $this->batchLimit." batch limit, last {$this->last}\n";
        $sS = new ScrapperService("", $rootPath);
        $sS->process($this->profiles);

        $end_time = time();
        $duration = $end_time - $start_time;
        $dv = new \DateInterval('PT'.$duration.'S');
        $this->threadTook = ("Thread -  {$this->workerId} - process took"." - ". $dv->m . " minutes ". $dv->s." seconds \n");
    }

    public function  getDBStoreProfileLink($p, $rootPath = false) {
        $path=null;
        $sh = $p->getShMetadata();
        print_r($sh);
        if(isset($sh)) {
            $rootPath = $rootPath ? $rootPath : $sh->getDownloadedResumesPath();
        }
        $path = $rootPath . '/'.  $p->getShMetadata()->getHash() . '.html';
        return $path;
    }

    public function initiate(){
//        if(!class_exists('\core')){
            require dirname(__DIR__)."/core.php";
//        }
        $c = new \core();
        $c->init();
    }

    public function copyToArray($objects) {
        $obj_arr = array();
        foreach($objects as $obj) {
            array_push($obj_arr, $obj);
//            print_r($obj->getId() . "\n");

        }
        return $obj_arr;
    }

    public function getFileContents($path) {
        if(isset($path) && $path != '/.html') {
            $bytes = file_get_contents($path);
            return $bytes;
        }
        return null;

    }

    public function run()
    {
        $this->_print("ProfileParseWorker {$this->workerId} start running");
        $this->initiate();
        $this->parseProfiles($this->rootPath);
    }
} 