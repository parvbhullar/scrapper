<?php
namespace Model;
use Purekid\Mongodm\Model;

/**
 *
 * @package
 *
 * @final
 */
final class OutBoundProfileLinks extends Model
{
    public $excluded = array('_id', 'created_by', 'created_at');
    public function Initialize($user = "Unknown"){
        $this->status = 1; // 0 Inactive, 1 Active, 2 Deleted
        $this->created_at = time();
        $this->created_by = $user;
    }
    static $collection = "OutBoundProfileLinks";

    /** specific definition for attributes, not necessary! **/
    protected static $attrs = array(
        'profile' => array('model'=> 'Model\\Profiles', 'type'=>'reference'), //If single reference den use embed
        'name' => array('default'=>'','type'=>'string'),
        'source' => array('default'=>'','type'=>'string'),
        'summary' => array('default'=>'','type'=>'string'),

        'status' => array('default'=>1, 'type'=>'integer'), #0 deactive, 1 active, 2 deleted
    );

}

?>