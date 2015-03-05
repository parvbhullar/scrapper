<?php
namespace Services;

class BaseService
{
    protected  $container, $environment;
    protected $print = true;
    protected $stringUtilService, $fileService, $awsService, $dm, $dateUtilService;

    public function __construct($environment, $service)
    {
        $this->environment = $environment;
        $this->container = $service;
        $this->print = false;
        $this->setServices();
    }

    private function setServices() {
        $this->stringUtilService = $this->getContainer()->get("StringUtilService");
        $this->fileService = $this->getContainer()->get("FileUtilService");
        $this->dateUtilService = $this->getContainer()->get("DateUtilService");
        $this->awsService = $this->getContainer()->get("AwsService");
        $this->dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
    }

    public function _print($msg, $print = null){
        $print = $print === null ? $this->print : $print;
        if($print){
//            $date = new \DateTime();
//            $date = $date->format('Y-m-d H:i:s');
//            echo "[$date] $msg \n";
            echo "$msg \n";
        }
    }

    public function generateUrl($route, $parameters = array(), $absolute = false)
    {
        return $this->get('router')->generate($route, $parameters, $absolute);
    }

    public function _printTimeDuration($msg, $time = 0, $print = null){
        $dv = new \DateInterval('PT'.$time.'S');
        $this->_print($msg." - ". $dv->m . " minutes ". $dv->s." seconds ", $print);
    }

    public function log($msg){
//        TODO add log functionality
    }

    /*
     * Returns the service for the key passed.
     */
    public function get($key){
        return $this->container->get($key);
    }

    /*
     * Returns document repo
     * $key -> Document Reference.
     *
     * eg: $this->getRepo('CrawlBundle:Profiles')->findOrCreateProfile($url);
     *
     */
    public function getRepo($key){
        return $this->dm->getRepository($key);
    }

    public function persist($obj){
        try{
            $this->dm->persist($obj);
            $this->dm->flush();
        } catch (\Exception $ex){
            echo("Exception " . $ex->getMessage() . "\n");
            throw $ex;
        }
    }

    public function remove($obj){
        $this->dm->remove($obj);
    }
    public function flush(){
        $this->dm->flush();
    }
    #endregion

    #region String service

    public function setPrint($p = true){
        $this->print = $p;
    }

    public function strContains($val, $needle){
        return $this->stringUtilService->strContains($val, $needle);
    }

    public function checkStopwords($text){
        return $this->stringUtilService->checkStopwords($text);
    }

    public function sliceText($text, $limit = 20){
        return $this->stringUtilService->sliceText($text, $limit);
    }

    public function cleanText($text){
        return $this->stringUtilService->cleanText($text);
    }

    public function getArrayValue($array, $key){
        return $this->stringUtilService->getArrayValue($array, $key);
    }

    #endregion

    #region Date service
    public function formatDateMY($date){
        return $this->dateUtilService->formatDateMY($date);
    }

    public function format($date, $format = "Y-m-d H:i:s"){
        return $this->dateUtilService->format($date, $format);
    }

    public function getValidDate($dateStr){
        return $this->dateUtilService->getValidDate($dateStr);
    }

    public function dateStringClean($dateStr){
        return $this->dateUtilService->dateStringClean($dateStr);
    }

    public function dateDuration($start, $to) {
        return $this->dateUtilService->calculateDuration($start, $to);
    }
    public function sortDate($dateArray, $key = 'start_date', $ascending = false){
        return $this->dateUtilService->sort($dateArray, $key, $ascending);
    }
    #endregion

    #region aws service
    public function getAWSConfig(){
        return $this->awsService->getAWSConfig();
    }
    #endregion

    #region file service

    public function getFileContents($path) {
        return $this->fileService->getFileContents($path);
    }
    #endregion

    public function getContainer()
    {
        return $this->container;
    }

    public function getDM()
    {
        return $this->dm;
    }

    public function getEnvironment()
    {
        return $this->environment;
    }

    public function copyToArray($objects) {
        $obj_arr = array();
        foreach($objects as $obj) {
            array_push($obj_arr, $obj);
//            print_r($obj->getId() . "\n");

        }
        return $obj_arr;
    }

    public function setUnsetDM() {
        $this->dm->flush();
        $this->dm->clear();
        unset($this->dm);

        $this->dm =  $this->container->get('doctrine_mongodb')->getManager();

    }

}
