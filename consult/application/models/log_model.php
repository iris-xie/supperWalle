<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Log_model
 */
class Log_model extends MY_Model {
    protected $table_name = 'log';

    public function __construct() {
        parent::__construct();
    }
}