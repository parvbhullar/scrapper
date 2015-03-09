<?php
namespace Model;
use Purekid\Mongodm\Model;

/**
 *
 * @package
 *
 * @final
 */
final class Experience extends Model
{
    public $excluded = array('_id', 'created_by', 'created_at');
    protected static $useType = false;
    public function Initialize($user = "Unknown"){
        $this->status = 1; // 0 Inactive, 1 Active, 2 Deleted
        $this->created_at = time();
        $this->created_by = $user;
    }
    static $collection = "hq_experience";

    /** specific definition for attributes, not necessary! **/
    protected static $attrs = array(
        'profile' => array('default'=> null, 'model'=> 'Model\\Profiles', 'type'=>'reference'), //If single reference den use embed
        'role' => array('default'=>'','type'=>'string'),
        'companyName' => array('default'=>'','type'=>'string'),
        'industry' => array('default'=>'','type'=>'string'),
        'fromDate' => array('type'=>'date'),
        'toDate' => array('type'=>'date'),
        'duration' => array('default'=>'','type'=>'string'),
        'current' => array('type'=>'boolean'),
        'location' => array('default'=>'','type'=>'string'),
        'description' => array('default'=>'','type'=>'string'),
        'seq' => array('default'=>0,'type'=>'integer'),

        'status' => array('default'=>1, 'type'=>'integer'), #0 deactive, 1 active, 2 deleted
        'created_by' => array('default'=>'Unknown','type'=>'string'),
        'created_at' => array('type'=>'date')
    );
    public function Add(){
        try
        {
            $res =  $this->IsExists();
            if($res != false)
            {
                $obj = Experience::id($res);
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
            $data = Experience::one(array("profile.id" => $this->profile->getId(), "role" => $this->role, "companyName" => $this->companyName
            , "fromDate" => $this->fromDate, "toDate" => $this->toDate));
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