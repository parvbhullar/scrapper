<?php
namespace Services;
use Model\OutBoundProfileLinks;
use Model\Education;
use Model\Experience;
use Model\Profiles;
use Model\SHMetadata;
use Purekid\Mongodm\Collection;

/**
 *
 * @package
 *
 * @final
 */
final class ScrapperService
{
    private $index, $type, $esClient;
    private $jsonFilePath, $htmlDir;
    const SOURCE_SERVICE_INDEED = "INDEED";
    const SOURCE_SERVICE_LINKED_IN = "LINKED_IN";
    const SOURCE_SERVICE_ANGEL_LIST = "ANGEL_LIST";
    const SOURCE_SERVICE_ZIP_RECRUITER = "ZIP_RECRUITER";
    const SOURCE_SERVICE_GOOGLE_PLUS = "GOOGLE_PLUS";

    // Crawl Status
    const CS_LINK_STORED = 0;
    const CS_CRAWLED = 1;
    const CS_PARSED = 2;
    const CS_ES_UPDATED = 3;
    public function __construct($jsonPath = "", $htmlDir = "")
    {
        $this->jsonFilePath = $jsonPath;
        $this->htmlDir = $htmlDir;
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

    public function process($list){
        foreach($list as $l){
            if(isset($l['hash']) && isset($l['url'])){
                $filePath = $this->htmlDir.DIRECTORY_SEPARATOR.$l['hash'].".html";
//                echo "Scraping url - $t";//.$l['url']. " file path - $filePath\n";
                $json = $this->scrapAll($l['url'], $filePath);
                if($json){
                    $d = dirname(dirname(__DIR__))."/file.json";
//                   file_put_contents($d, json_encode($json));
                    //create profile
                    $this->createProfiles($l['url'], $l, $json);
                    $jsonList = [];
                }
            }
        }
    }
    public function sqlToMongo($sqldata)
    {
        {
            $profile = new Profiles();
            $profile->id(new \MongoId());
            $profile->name = $sqldata['name'];
          //  $profile->title = $sqldata['title'];
            $profile->gender = $sqldata['Gender'];
            $profile->profileStore = null;// $this->getDataByKey($data, $profile,'profileStore');


            $profile->industry = $sqldata['Industry'];
            $profile->locality = $sqldata['Location'];
            $profile->sourceService = $sqldata['source'];
            $s =  self::CS_PARSED; //Set status Parsed
            $profile->status = $s;

          // $profile =  $this->parseDBEducation($this->textToObj($sqldata['Education']), $profile);
            $data = $this->textToObj($sqldata['Experience']);
            $profile =  $this->parseDBExperience($data, $profile);
             $date =  new \DateTime();
            $date->setISODate(1900, 1, 1);
            $profile->resumeLastUpdated =$date;// $this->getValidDate($this->getText($profile['resumeLastUpdated']));
            $profile->updatedInES = false;// $this->getDataByKey($data, 'updatedInES', true);

            $s =  self::CS_PARSED; //Set status Parsed
            $profile->status = $s;
            $id = $profile->Add();

        }

        return $profile;
    }
    public function textToObj($text)
    {
        $list = [];
        $list[] = json_decode($text,true);
        $data = array();
        foreach ($list as $l) {
            foreach ($l as $k => $v) {
                $data[$k] = $v;
            }
        }
        return $data;
    }
    public function parseDBExperience($list, Profiles $profile)
    {
        if($list){
            $i = 1;
            $oList = [];
            foreach($list as $expArr){
                $ex = new Experience();
               $ex->id(new \MongoId());
                $ex->companyName = $expArr['Company'];
               $ex->role=$expArr['Title'];
                $ex->industry=$profile->getIndustry();
                $ex->location=$expArr['Location'];
                $ex->description = $expArr['Description'];
                $ex->fromDate=$this->getValidDate($expArr['Start Date']);
                $endDate = $expArr['End Date'];
                $ex->toDate=$this->getValidDate($endDate);
                $duration = $this->dateDuration($expArr['Start Date'], $expArr['End Date']);
                $current = $endDate ? false : true;
                $ex->current=$current;
                $ex->createdAt=new \DateTime();
                $ex->updatedAt=new \DateTime();
                $ex->seq = $i;
                $ex->duration = $duration;

                $obl = false;//$this->getRepo('CrawlBundle:Experience')->checkExperience($profile, $ex);
                $ex->profile = $profile;
                $ex->Add();
               $oList[] = $ex;
                $i++;
                $s = 0;
                $ex->status = $s;
            }

            $profile->experience = Collection::make($oList);;
            //Reset Seq
            //$profile = $this->getProfile();
            //  $this->_print(count($list)." experiences added");

        }
        return $profile;
    }
    public function parseDBEducation($list, $profile)
    {
        if($list){
            $i = 1;
//            $profile = $this->deleteEducation($profile, $profile->getEducation());
            $oList = [];
            foreach($list as $edu){
                $e = new Education();
                $e->id(new \MongoId());
                $e->school=$edu["school"];
                $e->degree=$edu["degree"];
                $e->program=$edu["major"];
                $e->gpa=$edu["grade"];
                $e->fromDate=$this->getValidDate($edu['start_date']);
                $endDate = $edu['end_date'];
                $e->toDate=$this->getValidDate($endDate);
                $e->year=$this->dateStringClean($edu['end_date']);
                $e->createdAt = new \DateTime();
                $e->updatedAt = new \DateTime();
                $e->seq = $i;
                $e->profile = $profile;
                $s = 0;
                $e->status = $s;
                // $profile->addEducation($e);
//                $this->addEducation($e);
                $e->Add();
                $oList[] = $e;
                $i++;
            }
            $profile->education = Collection::make($oList);;
            //  $this->_print(count($list)." educations added");
        }
        return $profile;
    }
    public function metaToJson($jsonPath, $htmlDir, $batch = 10, $limit = 20){
        $this->jsonFilePath = $jsonPath;
        $this->htmlDir = $htmlDir;

        $list = $this->readJson($jsonPath);
        $jsonList = [];
        $i = $t = 1;
        $start_time = time();
        foreach($list as $l){
            if(isset($l['hash']) && isset($l['url'])){
                $filePath = $htmlDir.DIRECTORY_SEPARATOR.$l['hash'].".html";
                echo "Scraping url - $t";//.$l['url']. " file path - $filePath\n";
                $json = $this->scrapAll($l['url'], $filePath);
                if($json){
                    $d = dirname(dirname(__DIR__))."/file.json";
//                   file_put_contents($d, json_encode($json));
                    //create profile
                    $this->createProfiles($l['url'], $l, $json);
                    $jsonList = [];
                    $i++;
                }
                $t++;
            }
            if($t >= $limit){
                break;
            }
        }
        $end_time = time();
        $duration = $end_time - $start_time;
        $dv = new \DateInterval('PT'.$duration.'S');
        echo("Whole Profile process took"." - ". $dv->m . " minutes ". $dv->s." seconds \n");

    }

    public function createProfiles($url, $sh, $scrappedJson){
        $profile = $this->parseProfile($scrappedJson[$url], $sh);
        //print_r($scrappedJson[$url]);exit;
        $d = dirname(dirname(__DIR__))."/profile.json";

    }

    public function parseProfile($data, $shJson)
    {
        $url = $this->getArrayValue($shJson, 'url');
        $profile = Profiles::one(array("source" => trim($url)));
        if(!$profile){
            $profile = new Profiles();
            $profile->id(new \MongoId());
            $id = $profile->Add();
            $profile = Profiles::id($id);
            $profile->source = $url;
        } else {
            if($profile->status == self::CS_PARSED){
                echo "Profile already parsed - $url\n";
                return false;
            }
        }
//        $this->currentProfile = $profile;
        $name = $this->getArrayValue($shJson, 'name');// $this->getDataByKey($data, 'name', true);

        if($name){
            $profile->name =$name;
            $profile->title = $this->getDataByKey($data, 'title', true);
            $profile->gender = $this->getGender($name);
            $profile->profileStore = null;// $this->getDataByKey($data, $profile,'profileStore');


            $profile->industry = $this->getDataByKey($data, 'industry', true);
            $profile->locality = $this->getDataByKey($data, 'locality', true);
            $profile->sourceService = self::SOURCE_SERVICE_LINKED_IN;// $this->getDataByKey($data, 'sourceService', true);
            $s =  self::CS_PARSED; //Set status Parsed
            $profile->status = $s;
//            $profile->Add();
//            echo "Profile saved\n";
            $profile =  $this->parseOutgoingLinks($data, $profile);
//            print_r($profile->outBoundProfilesLinks);
            $profile =  $this->parseExperience($data, $profile);
//            print_r($profile->experience);
            $profile =  $this->parseEducation($data, $profile);
            $profile = $this->parseSHMetadata($shJson, $profile);

            $date =  new \DateTime();
            $date->setISODate(1900, 1, 1);
            $profile->resumeLastUpdated =$date;// $this->getValidDate($this->getText($profile['resumeLastUpdated']));
            $profile->updatedInES = false;// $this->getDataByKey($data, 'updatedInES', true);

            $s =  self::CS_PARSED; //Set status Parsed
            $profile->status = $s;
            $profile->Add();
//            exit;
//            $profile->id();
//            $this->setProfile($profile);
//            $profile->Initialize();
//            print_r($profile);exit;
        }

        return $profile;
    }



    public function parseEducation($data, $profile)
    {
        $list = $this->getDataByKey($data, 'education');
        if($list){
            $i = 1;
//            $profile = $this->deleteEducation($profile, $profile->getEducation());
            $oList = [];
            foreach($list as $edu){
                $e = new Education();
                $e->id(new \MongoId());
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
                $e->Add();
                $oList[] = $e;
                $i++;
            }
            $profile->education = Collection::make($oList);;
            //  $this->_print(count($list)." educations added");
        }
        return $profile;
    }

    public function parseExperience($data, Profiles $profile)
    {
        $list = $this->getDataByKey($data, 'experience');
        if($list){
//            print_r($list);
//            $list = $this->sortDate($list);
//            print_r($list);
//            $profile = $this->deleteExperience($profile, $profile->getExperience());
            $i = 1;
            $oList = [];
            foreach($list as $expArr){
                $ex = new Experience();
                $ex->id(new \MongoId());
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
                $ex->profile = $profile;
                $ex->Add();
                $oList[] = $ex;
                $i++;
                $s = 0;
                $ex->status = $s;
            }

            $profile->experience = Collection::make($oList);

            //Reset Seq
            //$profile = $this->getProfile();
            //  $this->_print(count($list)." experiences added");

        }
        return $profile;
    }

    public function parseSHMetadata($shmdata, $profile)
    {
        $shmd = new SHMetadata();
        $shmd->id(new \MongoId());
        $shmd->hash=$this->getArrayValue($shmdata, "hash");
        $shmd->zipResumesS3path = $this->getArrayValue("zipResumesS3path", "");
        $shmd->zipMetadataS3path = $this->getArrayValue($shmdata, "zipMetadataS3path");
        $shmd->downloadedResumesPath = $this->htmlDir;
        $shmd->downloadedMetadataPath = $this->jsonFilePath;
        $shmd->currentJob = $this->getArrayValue($shmdata, "currentJob");
        $shmd->previousJobs =$this->getArrayValue($shmdata, "previousJobs");

        $shmd->profile = $profile;

        $s = 0;
        $shmd->status = $s;
        $shmd->Add();

        $profile->SHMetadata = $shmd;
        //  $this->_print(count($list)." educations added");

        return $profile;
    }

    public function dateDuration($start, $to)
    {
        if(isset($start) && isset($end)) {
            $start = $this->formatDate($start);
            $end = $this->formatDate($to);
            return $start->diff($end);
        }
        return null;
    }
    public function formatDate($date) {
        if(isset($date)) {
            $date = new \DateTime($date);
            return $date;
        }
        else
            return null;
    }

    public function getCurrent($exp){

    }

    public function parseOutgoingLinks($data, Profiles $profile)
    {
        $list = $this->getDataByKey($data, 'side_profiles');
//        $profile->outBoundProfilesLinks = null;
        if($list){
//            $profile = $this->getProfile();
//            $this->deleteOutboundLinks($profile->getOutBoundProfilesLinks());
            $oList = [];
//            print_r($profile);
            foreach($list as $sProfile){
                $url = $this->getHref($sProfile['name']);
                $obl = false;//$this->getRepo('CrawlBundle:Profiles')->findOutboundLink($url);
//                $this->_print($url);
                if(!$obl){
                    $obl = new OutBoundProfileLinks();
                    $obl->id(new \MongoId(null));
                    $obl->source = $url;
                    $obl->name = $this->getText($sProfile['name']);
                    $obl->summary = $this->getText($sProfile['description']);
                    $s = self::CS_LINK_STORED;
                    $obl->status= $s;
                    $obl->profile = $profile;
//                    echo "saving first $url\n";
                    $obl->Add();
                    $oList[] = $obl;
                }
            }
            $profile->outBoundProfilesLinks = $oList;
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
//            $val = mb_convert_encoding($val, "UTF-8", "HTML-ENTITIES");// preg_replace_callback("/(&[#0-9a-z]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $val);
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
        } else {
            echo("Content not found\n");
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

    function getGenderNew($name) {
        $namePart = explode(" ", $name);
        $firstName = $namePart[0];
        $gender = new Gender;
        $country = Gender::US;

        $result = $gender->get($firstName, $country);

        switch($result) {
            case Gender::IS_FEMALE:
            case Gender::IS_MOSTLY_FEMALE:
                $g = 'Women'; break;
            case Gender::IS_MALE:
            case Gender::IS_MOSTLY_MALE:
                $g = 'Men';break;
            default:
                $g = '';
                break;
        }
        return $g;
    }
//echo getGender('markus'); //Output: male
    public function getGender($name) {
        return null;
        $namePart = explode(" ", $name);
        $firstName = $namePart[0];
        //  $this->_print("Gender update from APIS : " . $firstName);
//        $purl = 'http://api.genderize.io?name='.$firstName;
        //        $json = $this->curlURL($purl);
        $json = file_get_contents('https://gender-api.com/get?name='.urlencode($firstName));

//        $json = $this->getResponse();
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