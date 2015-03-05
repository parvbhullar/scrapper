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

    public function readJsonNScrap($jsonDir, $htmlDir){

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

        $this->time = $duration;
        $this->total = count($data);
        $this->data = $data;

        return array('time' => $duration, 'total' => count($data), 'data' => $data);
    }

}