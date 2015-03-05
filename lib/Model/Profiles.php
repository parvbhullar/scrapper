<?php
namespace Model;
use Purekid\Mongodm\Model;

/**
 *
 * @package
 *
 * @final
 */
final class Profiles extends Model
{
    public $excluded = array('_id', 'created_by', 'created_at');
    public function Initialize($user = "Unknown"){
        $this->status = 1; // 0 Inactive, 1 Active, 2 Deleted
        $this->created_at = time();
        $this->created_by = $user;
    }
    static $collection = "Profiles";

    /** specific definition for attributes, not necessary! **/
    protected static $attrs = array(
        'name' => array('default'=>'','type'=>'string'),
        'title' => array('default'=>'','type'=>'string'),
        'gender' => array('default'=>'','type'=>'string'),
        'sourse' => array('default'=>'','type'=>'string'),
        'profileStore' => array('default'=>1,'type'=>'integer'), # 1 targetDocument="ProfileStore", 2 cascade="persist"
        'industry' => array('default'=>'','type'=>'string'),
        'locality' => array('default'=>'','type'=>'string'),
        //** array field */
        'outBoundProfilesLinks' => array('default'=>'','type'=>'string'), #targetDocument="OutBoundProfileLinks", cascade="persist"
        'experience' => array('default'=>'','type'=>'string'), #targetDocument="Experience", cascade="persist"
        'education' => array('default'=>'','type'=>'string'), #targetDocument="Education", cascade="persist"

        'sourceService' => array('default'=>'','type'=>'string'),
        'resumeLastUpdated' => array('type'=>'timestamp'),
        'updatedInES' => array('default'=>'','type'=>'string'),
        'shMetadata' => array('default'=>1,'type'=>'integer'), # 1 targetDocument="SHMetadata", 2 cascade="persist"

        'status' => array('default'=>1, 'type'=>'integer'), #0 deactive, 1 active, 2 deleted
        'created_by' => array('default'=>'Unknown','type'=>'string'),
        'created_at' => array('type'=>'timestamp')
    );

}

?>