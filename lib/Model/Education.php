<?php
namespace Model;
use Purekid\Mongodm\Model;

/**
 *
 * @package
 *
 * @final
 */
final class Education extends Model
{
    public $excluded = array('_id', 'created_by', 'created_at');
    public function Initialize($user = "Unknown"){
        $this->status = 1; // 0 Inactive, 1 Active, 2 Deleted
        $this->created_at = time();
        $this->created_by = $user;
    }
    static $collection = "Education";

    /** specific definition for attributes, not necessary! **/
    protected static $attrs = array(

        'profile' => array('model'=> 'Model\\Profiles', 'type'=>'reference'), //If single reference den use embed
        'school' => array('default'=>'','type'=>'string'),
        'degree' => array('default'=>'','type'=>'string'),
        'program' => array('default'=>'','type'=>'string'),
        'fromDate' => array('type'=>'date'),
        'toDate' => array('type'=>'date'),
        'year' => array('default'=>'','type'=>'string'),
        'gpa' => array('default'=>'','type'=>'string'),
        'seq' => array('default'=>'','type'=>'integer'),
        'status' => array('default'=>1, 'type'=>'integer'), #0 deactive, 1 active, 2 deleted
        'created_by' => array('default'=>'Unknown','type'=>'string'),
        'created_at' => array('type'=>'date')
    );

    public function Add(){
        try
        {
            $res = $this->IsExists();
            if($res != false)
            {
                $obj = Education::id($res);
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
            $data = Education::id($this->getId());
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