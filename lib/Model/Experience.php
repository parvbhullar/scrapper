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
    public function Initialize($user = "Unknown"){
        $this->status = 1; // 0 Inactive, 1 Active, 2 Deleted
        $this->created_at = time();
        $this->created_by = $user;
    }
    static $collection = "Experience";

    /** specific definition for attributes, not necessary! **/
    protected static $attrs = array(
        'profile' => array('model'=> 'Model\\Profiles', 'type'=>'reference'), //If single reference den use embed

        'role' => array('default'=>'','type'=>'string'),
        'companyName' => array('default'=>'','type'=>'string'),
        'industry' => array('default'=>'','type'=>'string'),
        'fromdate' => array('type'=>'timestamp'),
        'todate' => array('type'=>'timestamp'),
        'duration' => array('default'=>'','type'=>'string'),
        'current' => array('type'=>'boolean'),
        'location' => array('default'=>'','type'=>'string'),
        'description' => array('default'=>'','type'=>'string'),
        'seq' => array('default'=>0,'type'=>'integer'),

        'status' => array('default'=>1, 'type'=>'integer'), #0 deactive, 1 active, 2 deleted
        'created_by' => array('default'=>'Unknown','type'=>'string'),
        'created_at' => array('type'=>'timestamp')
    );

}

?>