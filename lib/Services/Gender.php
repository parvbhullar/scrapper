<?php
/**
 * Created by PhpStorm.
 * User: Office
 * Date: 3/9/15
 * Time: 3:05 PM
 */

namespace Services;


use Clover\Text\LTSV;

class Gender {
     private  $list;
    public function __construct($filePath = false){
        //
        $filePath = $filePath ? $filePath : dirname(__DIR__).'/data/name_only.txt';
        $this->list = $this->readJson($filePath);
    }

    public function readJson($filePath){
        $file = new \SplFileObject($filePath);
        $i=0;
        $list = [];
        while (!$file->eof()) {
            $jsonRow = $file->fgets();
            $jsonData = preg_split('/\s+/', $jsonRow);
//            print_r($jsonData);exit;
            if(isset($jsonData[1]) && !isset($list[$jsonData[1]]))
                $list[trim($jsonData[1])] = trim(str_replace('?', '', $jsonData[0])) == 'F' ? 'Women' : 'Men' ;
            $i++;
        }
//        print_r($list);exit;
        return $list;
    }

    public function getGender($name){
        return isset($this->list[$name]) ? $this->list[$name] : '';
    }
} 