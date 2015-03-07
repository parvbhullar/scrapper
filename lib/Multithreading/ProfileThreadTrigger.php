<?php
/**
 * Created by PhpStorm.
 * User: Office
 * Date: 3/6/15
 * Time: 4:27 PM
 */

namespace Multithreading;

use Model\Profiles;
use Services\ScrapperService;

class ProfileThreadTrigger {
    private $threads, $workers = [];
    private $jsonFile, $batchLimit, $rootPath;
    public function __construct($jsonFile, $threads = 5, $batchLimit = 100, $rootPath = false){
        $this->threads = $threads;
        $this->jsonFile = $jsonFile;
        $this->batchLimit = $batchLimit;
        $this->rootPath = $rootPath;
    }

    public function run(){
        $total = $this->threads * $this->batchLimit;
        $profiles = $this->readJson($this->jsonFile);;
        $last = 0;
//        echo "Rootpath". $this->rootPath."\n";
        foreach (range(0, $this->threads) as $i) {
            $sProfiles = array_slice($profiles, $last, $this->batchLimit);
            $this->workers[$i] = new ProfileParseWorker($i, null, $sProfiles, $this->rootPath);
            $this->workers[$i]->start(PTHREADS_INHERIT_NONE); //parseProfiles($this->rootPath);
//            $this->workers[$i]->run(); //parseProfiles($this->rootPath);
//            $last = $this->workers[$i]->getLast();
            $last += $this->batchLimit;
            echo count($sProfiles)." profiles passed to worker thread $i".PHP_EOL;
        }
    }

    public function readJson($jsonFilePath){
        $file = new \SplFileObject($jsonFilePath);
        $i=0;
        $list = [];
        while (!$file->eof()) {
            $jsonRow = $file->fgets();
            $jsonData = json_decode($jsonRow, true);
            $list[] = $jsonData;
            $i++;
        }
        return $list;
    }

    public function runFromDb(){
// Initialize and start the threads
        $total = $this->threads * $this->batchLimit;
        $profiles = Profiles::find(array('status' => ScrapperService::CS_CRAWLED), array(), array(), $total, 0);
        $last = 0;
        echo "Rootpath". $this->rootPath."\n";
        foreach (range(0, $this->threads) as $i) {
            $sProfiles = array_slice($profiles, $last, $this->batchLimit);
            $this->workers[$i] = new ProfileParseWorker($i, null, $sProfiles, $this->rootPath);
            $this->workers[$i]->start(PTHREADS_INHERIT_NONE); //parseProfiles($this->rootPath);
//            $this->workers[$i]->run(); //parseProfiles($this->rootPath);
//            $last = $this->workers[$i]->getLast();
            $last += $this->batchLimit;
            echo count($sProfiles)." profiles passed to worker thread $i".PHP_EOL;
        }

// Let the threads come back
//        foreach (range(0, 5) as $i) {
//            $this->workers[$i]->join();
//        }
    }
} 