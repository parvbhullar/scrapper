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
        'fromdate' => array('type'=>'timestamp'),
        'todate' => array('type'=>'timestamp'),
        'year' => array('default'=>'','type'=>'string'),
        'gpa' => array('default'=>'','type'=>'string'),
        'seq' => array('default'=>'','type'=>'integer'),
        'time' => array('type'=>'timestamp'),
        'status' => array('default'=>1, 'type'=>'integer'), #0 deactive, 1 active, 2 deleted
        'created_by' => array('default'=>'Unknown','type'=>'string'),
        'created_at' => array('type'=>'timestamp')
    );

}

?>