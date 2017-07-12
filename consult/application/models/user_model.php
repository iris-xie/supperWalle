<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class User_model
 */
class User_model extends MY_Model {
    protected $table_name = 'user';

	public function __construct() {
        parent::__construct();
    }
}