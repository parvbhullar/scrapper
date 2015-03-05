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