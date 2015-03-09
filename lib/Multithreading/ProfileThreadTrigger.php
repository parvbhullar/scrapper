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
    private $jsonFile, $batchLimit, $rootPath, $total = 0, $totalLimit = 0;
    private $startTime = 0;
    public function __construct($jsonFile, $totalLimit = 500, $threads = 5, $batchLimit = 100, $rootPath = false){
        $this->threads = $threads;
        $this->jsonFile = $jsonFile;
        $this->batchLimit = $batchLimit;
        $this->rootPath = $rootPath;
        $this->totalLimit = $totalLimit;
    }

    public function start($profiles){
        $this->total += $this->threads * $this->batchLimit;
        $last = 0;
//        echo "Rootpath". $this->rootPath."\n";
        foreach (range(0, $this->threads) as $i) {
            $sProfiles = array_slice($profiles, $last, $this->batchLimit);
            $this->workers[$i] = new ProfileParseWorker($i, null, $sProfiles, $this->rootPath);
            $this->workers[$i]->start(PTHREADS_INHERIT_NONE); //parseProfiles($this->rootPath);
//            $this->workers[$i]->run(); //parseProfiles($this->rootPath);
//            $last = $this->workers[$i]->getLast();
            $last += $this->batchLimit;
            echo count($sProfiles)." profiles passed total profiles - ".count($profiles)."to worker thread $i".PHP_EOL;
            $profiles = array_slice($profiles, $this->batchLimit, count($profiles));
        }


//        $pool = new \Pool(1, PoolWorker::class);
//        $pool->submit(new MyWork("A"));
//        $pool->submit(new MyWork("B"));
//        $pool->submit(new MyWork("C"));
        //remove processed profiles from array - call again until limit breaks
        echo "Total limit - {$this->totalLimit} - {$this->total}\n";
        if($this->total > $this->totalLimit){
            echo "Inside Total limit - {$this->totalLimit} - {$this->total}\n";
            $this->start($profiles);
        }
    }

    public function startPool($profiles){
        $iMaxThread = (int)(count($profiles) / $this->batchLimit);
        $pool = new \Pool($this->threads, 'Multithreading\PoolWorker', [PTHREADS_INHERIT_NONE]);
        $total = $this->threads * $this->batchLimit;
        $last = 0;
        foreach (range(0, $iMaxThread) as $i) {
            $sProfiles = array_slice($profiles, $last, $this->batchLimit);
            $t = new ProfileParseWorker($i, null, $sProfiles, $this->rootPath);
            $t->start();
            $pool->submit($t);
            $this->workers[$i] = $t;
//            $this->workers[$i]->start(PTHREADS_INHERIT_NONE); //parseProfiles($this->rootPath);
//            $this->workers[$i]->run(); //parseProfiles($this->rootPath);
//            $last = $this->workers[$i]->getLast();
            $last += $this->batchLimit;
            echo count($sProfiles)." profiles passed total profiles - ".count($profiles)."to worker thread $i".PHP_EOL;
//            $profiles = array_slice($profiles, $this->batchLimit, count($profiles));
        }

        $pool->shutdown();

        $arg=true;
        while($arg){
            $arg=false;
            foreach($this->workers as $key => $object){
//    for ($i = 1; $i <= 5; $i++) {
                $arg2=$object->isRunning();
                if($arg2){
                    $arg=$arg2;
                }else{
                    //var_dump($key);
                    unset ($this->workers[$key]);
                }
//var_dump($key);
                if(!$arg){
                    var_dump($arg);
                }
            }
        }
    }

    public function run(){
        $total = $this->threads * $this->batchLimit;
        $start_time = $this->startTime = time();
        $profiles = $this->readJson($this->jsonFile);;
        $profiles = array_slice($profiles, 0, $this->totalLimit);
        $this->startPool($profiles);
        //start queing jobs;
        $end_time = time();
        $duration = $end_time - $start_time;
        $dv = new \DateInterval('PT' . $duration . 'S');
        echo("Profile process took" . " - " . $dv->m . " minutes " . $dv->s . " seconds \n");
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