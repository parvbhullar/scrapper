<?php
namespace Services;

/**
 *
 * @package
 *
 * @final
 */
final class ScrapperService
{
    private $index, $type, $esClient;

    public function __construct()
    {

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

    public function metaToJson($jsonPath, $htmlDir, $batch = 10){
        $list = $this->readJson($jsonPath);
        $jsonList = [];
        $i = 1;
        foreach($list as $l){
            if(isset($l['hash']) && isset($l['url'])){
                $filePath = $htmlDir.DIRECTORY_SEPARATOR.$l['hash'].".html";
                echo "Scraping url ".$l['url']. " file path - $filePath\n";
                $json = $this->scrapAll($l['url'], $filePath);
                if($json){
                    $jsonList[] = $json;
                    if($batch == $i){
                        //create profile
                        $this->createProfiles($jsonList);
                        $jsonList = [];
                        $i = 1;
                    }
                    $i++;
                }
            }
        }

    }

    public function createProfiles($jsonList){
        foreach($jsonList as $json){
            //create profiles object
            //education object
            //experience object
            //outbound object
        }
    }

    public function scrapAll($url, $path){
        $paths = array(
            array('name' => 'name', 'path' => array('span.full-name')),
            array('name' => 'title', 'path' => array('p.title')),
            array('name' => 'locality', 'path' => array('span.locality')),
            array('name' => 'industry', 'path' => array('dd.industry')),
            array('name' => 'experience', 'path' => array('div.background-experience > div.section-item'),
                'children' => array(
                    array('name' => 'title', 'path' => array('header > h4')),
                    array('name' => 'company', 'path' => array('header > h5')),
                    array('name' => 'locality', 'path' => array('span.locality')),
                    array('name' => 'start_date', 'path' => array('time:nth-child(1)')),
                    array('name' => 'end_date', 'path' => array('time:nth-child(2)')),
                )),
            array('name' => 'education', 'path' => array('div.background-education > div.section-item'),
                'children' => array(
                    array('name' => 'school', 'path' => array('header > h4')),
                    array('name' => 'degree', 'path' => array('header > h5 > span.degree')),
                    array('name' => 'major', 'path' => array('header > h5 > span.major')),
                    array('name' => 'grade', 'path' => array('header > h5 > span.grade')),
                    array('name' => 'start_date', 'path' => array('.education-date > time:nth-child(1)')),
                    array('name' => 'end_date', 'path' => array('.education-date > time:nth-child(2)')),
                )),
            array('name' => 'side_profiles', 'path' => array('div.insights-browse-map > ul > li'),
                'children' => array(
                    array('name' => 'name', 'path' => array('h4 > a')),
                    array('name' => 'description', 'path' => array('p.browse-map-title'))
                )),
        );

        $content = $this->getFileContents($path);
        if($content){
            return $this->scrap($url, $paths, false, $content);
        }
        return false;
    }

    public function getFileContents($path) {
        if(isset($path) && $path != '/.html') {
            $bytes = file_get_contents($path);
            return $bytes;
        }
        return false;
    }


    public function scrap($url, $cssPaths, $objectRoot = false, $content = null, $depth = 0){
        $start_time = time();
        $simple_crawler = new SimpleScrapper($url, $cssPaths, $depth);
        $urls = array();//$this->getStopUrls();
        $simple_crawler->setStopUrls($urls);
//        $simple_crawler->setObjectsRoot($objectRoot);
        $simple_crawler->setExtractInfo();
        if($content){
            $simple_crawler->traverse($url, $content);
        } else {
            $simple_crawler->traverse();
        }
        $data = $simple_crawler->getLinksInfo();
        $end_time = time();
        $duration = $end_time - $start_time;

        return $data;// array('time' => $duration, 'total' => count($data), 'data' => $data);
    }

}