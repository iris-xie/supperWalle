<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Group_model
 */
class Group_model extends MY_Model {
    protected $table_name = 'group';

    public function __construct() {
        parent::__construct();
    }
}