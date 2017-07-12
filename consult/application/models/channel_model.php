<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Channel_model
 */
class Channel_model extends MY_Model {
    protected $table_name = 'channel';

    public function __construct() {
        parent::__construct();
    }
}