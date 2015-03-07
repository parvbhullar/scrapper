<?php
namespace Model;
use Purekid\Mongodm\Model;

/**
 *
 * @package
 *
 * @final
 */
final class SHMetadata extends Model
{
    public $excluded = array('_id', 'created_by', 'created_at');
    protected static $useType = false;
    public function Initialize($user = "Unknown"){
        $this->status = 1; // 0 Inactive, 1 Active, 2 Deleted
        $this->created_at = time();
        $this->created_by = $user;
    }
    static $collection = "hq_sh_metadata";

    /** specific definition for attributes, not necessary! **/
    protected static $attrs = array(
        'profile' => array('default'=> null, 'model'=> 'Model\\Profiles', 'type'=>'reference'), //If single reference den use embed

        'hash' => array('default'=>'','type'=>'string'),
        'zipResumesS3path' => array('default'=>'','type'=>'string'),
        'zipMetadataS3path' => array('default'=>'','type'=>'string'),
        'downloadedResumesPath' => array('default'=>'','type'=>'string'),
        'downloadedMetadataPath' => array('default'=>'','type'=>'string'),
        'currentJob' => array('default'=>'','type'=>'string'),
        'previousJobs' => array('default'=>'','type'=>'string'),

        'updated_at' => array('type'=>'date','default'=> null),
        'created_at' => array('type'=>'date', 'default'=> null)
    );

    public function Add(){
        try
        {
            $res = $this->IsExists();
            if($res != false)
            {
                $obj = SHMetadata::id($res);
                //print_r($obj); exit;
                $obj->update($this->toArray($this->excluded));
                $obj->save();
                return $res;
            }
            $this->save();
            return $this->getId();
        }
        catch(\Exception $ex){
           // \G::$logger->Log($ex, "Error");
            throw $ex;
        }
    }


    public function IsExists()
    {
        try{
            $data = SHMetadata::id($this->getId());
            if (empty($data))
            {  return false;}
            else
                return $data->getId();
        }
        catch(\Exception $ex){
          //  \G::$logger->Log($ex, "Error");
            throw $ex;
        }
    }

}

?>