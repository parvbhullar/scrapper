<?php
namespace Services;
use Model\OutBoundProfileLinks;
use Model\Education;
use Model\Experience;
use Model\Profiles;
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
                    $d = dirname($htmlDir)."/file.json";
//                   file_put_contents($d, json_encode($json));exit;
                    $jsonList[$l['url']] = $json;
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
        foreach($jsonList as $url=>$json){

            $this->parseProfile($json);
            //create profiles object
            //education object
            //experience object
            //outbound object
        }
    }

    public function parseProfile($profile)
    {
//        $profile = $this->getProfile();
//        $this->currentProfile = $profile;
        $name = $this->getDataByKey('name', true);

        if($name){
          //  $profile = new Profiles();
            $profile->name =$name;
            $profile->title = $this->getDataByKey('title', true);
            $profile->gender = $this->getGender($name);
            $profile->profileStore = $this->getDataByKey($profile,'profileStore');

            $profile->source =  $this->getDataByKey('source', true);
            $profile->industry = $this->getDataByKey('industry', true);
            $profile->locality = $this->getDataByKey('locality', true);

            $profile->outBoundProfilesLinks =  $this->parseOutgoingLinks($profile);
            $profile->experience =  $this->parseExperience($profile);
            $profile->education =  $this->parseEducation($profile);

            $profile->sourceService =  $this->getDataByKey('sourceService', true);
            $profile->resumeLastUpdated =  $this->getValidDate($this->getText($profile['resumeLastUpdated']));
            $profile->updatedInES =  $this->getDataByKey('updatedInES', true);
            $profile->shMetadata =  1;

            $s = 0;
            $profile->status = $s;

//            $this->setProfile($profile);
        }
        return $profile;
    }

    public function parseEducation($profile)
    {
        $list = $this->getDataByKey('education',true);
        $p = new Profiles();
        if($list){
            $i = 1;
//            $profile = $this->deleteEducation($profile, $profile->getEducation());
            foreach($list as $edu){
                $e = new Education();
                $e->school=$this->getText($edu["school"]);
                $e->degree=$this->getText($edu["degree"]);
                $e->program=$this->getText($edu["major"]);
                $e->gpa=$this->getText($edu["grade"]);
                $e->fromDate=$this->getValidDate($this->getText($edu['start_date']));
                $endDate = $this->getText($edu['end_date']);
                $e->toDate=$this->getValidDate($endDate);
                $e->year=$this->dateStringClean($this->getText($edu['end_date']));
                $e->createdAt = new \DateTime();
                $e->updatedAt = new \DateTime();
                $e->seq = $i;
                $e->profile = $profile;
                $s = 0;
                $e->status = $s;
               // $profile->addEducation($e);
//                $this->addEducation($e);
                $i++;
            }
          //  $this->_print(count($list)." educations added");
        }
        return $e;
    }

    public function parseExperience($profile)
    {
        $list = $this->getDataByKey('experience',true);
        if($list){
//            print_r($list);
//            $list = $this->sortDate($list);
//            print_r($list);
//            $profile = $this->deleteExperience($profile, $profile->getExperience());
            $i = 1;
            foreach($list as $expArr){
                $ex = new Experience();
                $ex->companyName = $this->getText($expArr['company']);
                $ex->role=$this->getText($expArr['title']);
                $ex->industry=$profile->getIndustry();
                $ex->location=$this->getText($expArr['locality']);
                $ex->description = "";
                $ex->fromDate=$this->getValidDate($this->getText($expArr['start_date']));
                $endDate = $this->getText($expArr['end_date']);
                $ex->toDate=$this->getValidDate($endDate);
                $duration = $this->dateDuration($this->getText($expArr['start_date']), $this->getText($expArr['end_date']));
                $current = $endDate ? false : true;
                $ex->current=$current;
                $ex->createdAt=new \DateTime();
                $ex->updatedAt=new \DateTime();
                $ex->seq = $i;
                $ex->duration = $duration;

                $obl = false;//$this->getRepo('CrawlBundle:Experience')->checkExperience($profile, $ex);
                if(!$obl){
                   // $profile->addExperience($ex);
                    $ex->profile = $profile;
                }
                $i++;
                $s = 0;
                $ex->status = $s;
            }
            //Reset Seq
            //$profile = $this->getProfile();
          //  $this->_print(count($list)." experiences added");

        }
        return $ex;
    }

    public function getCurrent($exp){

    }

    public function parseOutgoingLinks($data,$profile)
    {
        $list = $this->getDataByKey($data, 'side_profiles');
        if($list){
//            $profile = $this->getProfile();
//            $this->deleteOutboundLinks($profile->getOutBoundProfilesLinks());
            foreach($list as $sProfile){
                $url = $this->getHref($sProfile['name']);
                $obl = false;//$this->getRepo('CrawlBundle:Profiles')->findOutboundLink($url);
//                $this->_print($url);
                if(!$obl){
                    $obl = new OutBoundProfileLinks();
                    $obl->url = $url;
                    $obl->name = $this->getText($sProfile['name']);
                    $obl->summary = $this->getText($sProfile['description']);
                    $s = 0;
                    $obl->status= $s;
                    $obl->profile = array($profile);
                    $profile->addOutBoundProfilesLink($obl);
                }
            }
//            $this->_print(count($list)." side profiles added");
        }
        return $profile;
    }

    public function dateStringClean($dateStr){
        if(is_string($dateStr)){
            $dateStr = str_replace("-","", $dateStr);
            return str_replace("â","", $dateStr);
        }
        return $dateStr;
    }

    public function getValidDate($dateStr){
        if($dateStr){
//            echo("Date - str $dateStr\n");
            $dateStr = $this->dateStringClean($dateStr);
            $dv = false;
            if(is_string($dateStr)){
                $dv = intval($dateStr);
            }

            if($dv){
//                echo("Date - dv true str $dateStr\n");
                $date = $this->validateDate("Jan ".$dateStr);
            } else
                $date = $this->validateDate($dateStr);

            if(!$date){
                return null;
            }
//            echo("Returning Date - dv true str ".$date->format("M Y")."\n");
            return $date;
        }
        return null;

    }

    private function validateDate($dateStr){
        if(is_string($dateStr) === false){
            return $dateStr;
        }
        if (($timestamp = strtotime($dateStr)) === false) {
            return false;
        } else {
            $cD = new \DateTime('now');
            $cD->setTimestamp($timestamp);
            return $cD;
        }
    }

    public function getDataByKey($data, $key, $singleValue = false){

//        $data = $this->getData();
//        $data = $data[$this->getUrl()];

        if(!isset($data[$key]) || count($data[$key]) == 0){
           echo("Scrapped data does not contain $key. Kindly recheck scrap mapping or key\n");
            return false;
        }

        if($singleValue){
            foreach($data[$key] as $v){
//                $this->_print(json_encode($v), true);
                return $this->getText($v);
            }
            return false;
        }
        return $data[$key];
    }

    public function getText($value){
        return isset($value['text']) ? $this->cleanText($value['text']) : "";
    }

    public function getHref($value){
        return isset($value['href']) ? $value['href'] : "";
    }
    public function getArrayValue($array, $key){
        return isset($array[$key]) ? $this->cleanText($array[$key]) : "";
    }

    public function strContains($val, $needle){
        $val = preg_replace('/(\r\n\r\n)$/', '', $val);
        $needle = preg_replace('/(\r\n\r\n)$/', '', $needle);
        if($needle && strpos(trim(strtolower($val)), trim(strtolower($needle))) !== false){
            return true;
        }
        return false;
    }

    public function cleanText($val){
        if($val && is_string($val)){
            //Check for xml chars
            $val = mb_convert_encoding($val, "UTF-8", "HTML-ENTITIES");// preg_replace_callback("/(&[#0-9a-z]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $val);
//            $val = str_replace("-","", $val);
//            $val = str_replace(",","", $val);
            return $val;
        }
        return $val;
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
            $bytes = @file_get_contents($path);
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
    public function getGender($name) {
        $namePart = explode(" ", $name);
        $firstName = $namePart[0];
      //  $this->_print("Gender update from APIS : " . $firstName);
        $purl = 'http://api.genderize.io?name='.$firstName;

        $this->curlURL($purl);
        $json = $this->getResponse();
        $gender = json_decode($json, true);
     //   $this->_print("Name :".$name .": Gender :" . (isset($gender["gender"]) ? $gender["gender"] : "NA"));

        if($gender && isset($gender["gender"])){
            if($gender["gender"] == 'male') {
                $g = 'Men';
            }
            else if($gender["gender"] == 'female') {
                $g = 'Women';
            } else {
                $g = '';
            }
        } else {
            $g = '';
        }

        return $g;
    }
    public  function curlURL($url){
//        $response = $this->post($url, array(
//            'CURLOPT_HEADER' => false,
//            'CURLOPT_RETURNTRANSFER' => true,
//            'CURLOPT_SSL_VERIFYPEER' => 0
//        ));

        $pUrlHandle = curl_init();
        curl_setopt($pUrlHandle, CURLOPT_HEADER, false);
        curl_setopt($pUrlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($pUrlHandle, CURLOPT_URL, $url);
        curl_setopt($pUrlHandle, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($pUrlHandle, CURLOPT_USERAGENT, rand(10000, 1000000000));
        $jsonstring = curl_exec($pUrlHandle);
        $httpCode = curl_getinfo($pUrlHandle);

        //$this->_print(curl_error($pUrlHandle));
        //$this->_print("httpcode: ". $httpCode['http_code'] .":url : $url " );


        return  $jsonstring;
    }
}