
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

        switch($command)
        {
            case "scrap":
                $jsonFile = isset($argv[2])? $argv[2] : "";
                $dir = isset($argv[3])? $argv[3] : "";
                $batch = isset($argv[4])? $argv[4] : 50;
                if($jsonFile && $dir){
                    $this->init();
                    $scrpService = new \Services\ScrapperService();
                    $scrpService->metaToJson($jsonFile, $dir, $batch);
                } else {
                    echo "Command is\n";
                    echo "php lib/console.php scrap '<jsonFile>' '<htmlDir>' '<batchLimit>' \n";
                }
                break;
            default:

                echo "Available Commands\n";
                echo "scrap\n";
                break;
        }
    }

    public function init(){
        \Purekid\Mongodm\MongoDB::setConfigBlock('default', array(
            'connection' => array(
                'hostnames' => 'localhost',
                'database'  => 'scrapper',
//                            'username'  => 'root',
//                            'password'  => '',
                'options'  => array()
            )
        ));
    }

}
//php lib/console.php scrap "H:\IINCORE\COLLEGEFEED\scrap-hero-data\li.json" "H:\IINCORE\COLLEGEFEED\scrap-hero-data\htmls" 50
$app = new Console($argv);