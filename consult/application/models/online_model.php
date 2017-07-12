<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Online_model
 */
class Online_model extends MY_Model {
    protected $table_name = 'online';

    public function __construct() {
        parent::__construct();
    }
}