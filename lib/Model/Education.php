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
        'user_id' => array('default'=>'','type'=>'mixed'),
        'profile' => array('model'=> 'Model\\Profiles', 'type'=>'reference'), //If single reference den use embed
        'url' => array('default'=>'','type'=>'string'),
        'search_source' => array('default'=>'','type'=>'integer'),
        'time' => array('type'=>'timestamp'),
        'status' => array('default'=>1, 'type'=>'integer'), #0 deactive, 1 active, 2 deleted
        'created_by' => array('default'=>'Unknown','type'=>'string'),
        'created_at' => array('type'=>'timestamp')
    );

}

?>