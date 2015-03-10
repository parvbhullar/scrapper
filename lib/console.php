
<?php
/**
 * Created by PhpStorm.
 * User: Office
 * Date: 3/5/15
 * Time: 12:56 PM
 */

require dirname(__DIR__)."/vendor/autoload.php";
class console {
    private $argv = array();
    public function __construct($argv){
        $command = isset($argv[1])? $argv[1] : "Undefined";
//        print_r($command); exit;
        $c = new core();
        $c->init();
        switch($command)
        {
            case "scrap":
//                $jsonFile = "G:\\Oranikle\\Bigdata\\li.json";
//                $dir = "G:\\Oranikle\\Scrapper\\files";;
//                $batch = 50;

                $jsonFile = isset($argv[2])? $argv[2] : "";
                $dir = isset($argv[3])? $argv[3] : "";
                $batch = isset($argv[4])? $argv[4] : 50;
                if($jsonFile && $dir){
//                    $this->init();
                    $scrpService = new \Services\ScrapperService();
                    $scrpService->metaToJson($jsonFile, $dir, 0, $batch);
                } else {
                    echo "Command is\n";
                    echo "php lib/console.php scrap '<jsonFile>' '<htmlDir>' '<batchLimit>' \n";
                }
                break;
            case "scrap_multithread":
                $jsonFile = isset($argv[2])? $argv[2] : "";
                $dir = isset($argv[3])? $argv[3] : "";
                $threads = isset($argv[4])? $argv[4] : 5;
                $batch = isset($argv[5])? $argv[5] : 100;
                $start = isset($argv[6])? $argv[6] : 0;
                $limit = isset($argv[7])? $argv[7] : 500;
                if($jsonFile && $dir){
//                    $this->init();
                    $scrpService = new \Multithreading\ProfileThreadTrigger($jsonFile, $start, $limit, $threads, $batch, $dir);
                    $scrpService->run();
                } else {
                    echo "Command is\n";
                    echo "php lib/console.php scrap '<jsonFile>' '<htmlDir>' '<batchLimit>' \n";
                }
                break;
            case "mysql":
                $threads = isset($argv[2])? $argv[2] : 5;
                $batch = isset($argv[3])? $argv[3] : 100;
                $start = isset($argv[4])? $argv[4] : 1;
                $limit = isset($argv[5])? $argv[5] : 500;
                $scrpService = new \Multithreading\MysqlThreadTrigger("", $start, $limit, $threads, $batch);
                $scrpService->run();
                break;
            case "mysqlone":
                $row = \R::getRow("select * from cf_scrapped_data");
                if($row){
//                    $this->init();
                    $scrpService = new \Services\ScrapperService();
                   // foreach ($rows as $row)
                        $scrpService->sqlToMongo($row);
                } else {
                    echo "Record not found.\n";
                }
                break;

            default:

                echo "Available Commands\n";
                echo "scrap\n";
                echo "scrap_multithread\n" ;
                echo "mysql\n";
                echo "mysqlone\n";

                break;
        }
    }

//    public function init(){
//        \Purekid\Mongodm\MongoDB::setConfigBlock('default', array(
//            'connection' => array(
//                'hostnames' => 'localhost',
//                'database'  => 'scrapper',
////                            'username'  => 'root',
////                            'password'  => '',
//                'options'  => array()
//            )
//        ));
//    }

}
//php lib/console.php scrap "H:\IINCORE\COLLEGEFEED\scrap-hero-data\li.json" "H:\IINCORE\COLLEGEFEED\scrap-hero-data\htmls" 50
//php lib/console.php scrap_multithread "H:\IINCORE\COLLEGEFEED\scrap-hero-data\li.json" "H:\IINCORE\COLLEGEFEED\scrap-hero-data\htmls" 5 50 0 250
//5 = threads, 50 = profiles per batch, 0 = start head on file, 250 = total profiles to be parsed

//php lib/console.php scrap "/root/phq/sh/json_feb_17_99k.json" "/root/phq/sh/deb17/" 20
//php lib/console.php scrap_multithread "/var/www/shdata/li_data.json" "/var/www/shdata/htmls/" 5 200 0 20

$app = new Console($argv);