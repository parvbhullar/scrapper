<?php
/**
 * Created by PhpStorm.
 * User: Office
 * Date: 3/6/15
 * Time: 4:27 PM
 */

namespace Multithreading;

use Model\Profiles;
use Services\Gender;
use Services\ScrapperService;

class MysqlThreadTrigger {
    private $threads, $workers = [];
    private $jsonFile, $batchLimit, $rootPath, $total = 0, $start =0, $totalLimit = 0;
    private $startTime = 0;
    private $gender;
    public function __construct($jsonFile, $start= 0, $totalLimit = 500, $threads = 5, $batchLimit = 100, $rootPath = false){
        $this->threads = $threads;
        $this->jsonFile = $jsonFile;
        $this->batchLimit = $batchLimit;
        $this->rootPath = $rootPath;
        $this->totalLimit = $totalLimit;
        $this->start = $start;
        $this->gender = new Gender();
    }

    public function start($profiles, $last = 0){
        $this->total += $this->threads * $this->batchLimit;
//        print_r($this); exit;
        echo "start from $last - totalLimit {$this->totalLimit} - batchLimit = {$this->batchLimit} - threads = {$this->threads}"."\n";
        foreach (range(0, $this->threads-1) as $i) {
            $sProfiles = array_slice($profiles, $last, $this->batchLimit);
            $this->workers[$i] = new MysqlParseWorker($i, null, $sProfiles, $this->jsonFile, $this->rootPath, $this->gender);
            $this->workers[$i]->start(PTHREADS_INHERIT_NONE); //parseProfiles($this->rootPath);
//            $this->workers[$i]->run(); //parseProfiles($this->rootPath);
//            $last = $this->workers[$i]->getLast();
            $last += $this->batchLimit;
            echo count($sProfiles)." profiles passed total profiles - ".count($profiles)."to worker thread $i".PHP_EOL;
//            $profiles = array_slice($profiles, $this->batchLimit, count($profiles));
        }
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
//                    var_dump($arg);
                    echo("Thread {$key} stopped\n");
                }
            }
        }
        if($this->totalLimit > $this->total){
            echo "Inside Total limit - {$this->totalLimit} - {$this->total}\n";
            $this->start($profiles, $last);
        }
    }

    public function startPool($profiles, $last = 0){
        echo "start from $last - totalLimit {$this->totalLimit} - batchLimit = {$this->batchLimit} - threads = {$this->threads}"."\n";
        $iMaxThread = (int)(count($profiles) / $this->batchLimit);
        $pool = new \Pool($this->threads, 'Multithreading\PoolWorker', [PTHREADS_INHERIT_NONE]);
        $this->total += $iMaxThread * $this->batchLimit;
        foreach (range(0, $iMaxThread) as $i) {
            $sProfiles = array_slice($profiles, $last, $this->batchLimit);
            $t = new MysqlParseWorker($i, null, $sProfiles, $this->jsonFile, $this->rootPath, $this->gender);
//            $t->start();
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
                if(!$arg){
//                    var_dump($arg);
                    echo("Thread {$key} stopped\n");
                }
            }
        }
        if($this->totalLimit > $this->total){
            echo "Inside Total limit - {$this->totalLimit} - {$this->total}\n";
//            $this->startPool($profiles, $last);
        }
    }

    public function run(){
        $start_time = $this->startTime = time();
        $profiles = $this->getProfiles(array(), $this->start, $this->totalLimit);
        if($this->totalLimit)
            $profiles = array_slice($profiles, $this->start, $this->totalLimit);
        if(count($profiles) > 0){
            $this->startPool($profiles, 0);
        } else {
            echo "Profiles count is less from start\n";
        }
//        $this->start($profiles, $this->start);
        //start queing jobs;
        $end_time = time();
        $duration = $end_time - $start_time;
        $dv = new \DateInterval('PT' . $duration . 'S');
        echo("Profile process took" . " - " . $dv->m . " minutes " . $dv->s . " seconds \n");
    }

    public function getProfiles($params = array(), $start = 0, $limit = 1000){
        $q = "select * from cf_scrapped_data LIMIT $start,$limit";
        $rows = \R::getAll($q);// limit ". $start."," .$$limit);
        return $rows;
    }
}