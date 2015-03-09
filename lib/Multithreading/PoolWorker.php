<?php
/**
 * Created by PhpStorm.
 * User: Office
 * Date: 3/9/15
 * Time: 10:24 AM
 */

namespace Multithreading;


class PoolWorker  extends \Worker {
    public function start($options = PTHREADS_INHERIT_NONE){
        parent::start($options);
    }
    public function run() {}
}