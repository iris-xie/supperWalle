<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class MY_Model
 */
class MY_Model extends CI_Model {
    protected $table_name = NULL;

	public function __construct() {
        $this->load->database();
        log_message('debug', "Model Class Initialized");
	}

    /**
     * @param $data
     * @return bool
     */
    public function add($data) {
        $rs = $this->db->insert($this->table_name, $data);
        if ($rs)
            return $this->db->insert_id();
        else
            return FALSE;
    }

    /**
     * @param array $where
     * @return bool
     */
    public function delete($where = array()) {
        if (!empty($where)) {
            if (is_array($where))
                $this->db->where($where);
            else
                $this->db->where($where, NULL, FALSE);
        }
        $this->db->delete($this->table_name);
        return $this->db->affected_rows() ? TRUE : FALSE;
    }

    /**
     * @param $where
     * @param $update_data
     * @param array $escape_fields
     * @return bool
     */
    public function update($where, $update_data, $escape_fields = array()) {
        $this->db->where($where);
        foreach ($update_data as $field=>$val) {
            if (!empty($escape_fields) && in_array($field, $escape_fields)) {
                $this->db->set($field, $val, FALSE);
            } else {
                $this->db->set($field, $val);
            }
        }
        $this->db->update($this->table_name);
        return $this->db->affected_rows() ? TRUE : FALSE;
    }

    /**
     * @param $field
     * @param null $value
     * @return mixed
     */
    public function info($value, $field = NULL) {
        $this->db->from($this->table_name);
        if (is_array($value)) {
            foreach ($value as $key=>$val) {
                $this->db->where($key, $val);
            }
        } else {
            $this->db->where($field, $value);
        }

        $rs = $this->db->get()->row_array();
        return $rs;
    }

    /**
     * @param array $where
     * @return mixed
     */
    public function count($where = array()) {
        if (!empty($where)) {
            if (is_array($where))
                $this->db->where($where);
            else
                $this->db->where($where, NULL, FALSE);
        }
        return $this->db->count_all_results($this->table_name);
    }

    /**
     * @param array $where
     * @param string $order_by
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function page_lists($where = array(), $order_by = 'id DESC', $limit = 10, $offset = 0) {
        $this->db->from($this->table_name);
        if (!empty($where)) {
            if (is_array($where))
                $this->db->where($where);
            else
                $this->db->where($where, NULL, FALSE);
        }
        $count = $this->db->count_all_results('', FALSE);
        $this->db->order_by($order_by);
        $this->db->limit($limit);
        $this->db->offset($offset);
        $list = $this->db->get()->result_array();
        return array(
            'count' => $count,
            'list' => $list,
        );
    }

    /**
     * @param array $where
     * @param string $order_by
     * @param int $limit
     */
    public function lists($where = array(), $order_by = '', $limit = 0, $key_field = '') {
        $this->db->from($this->table_name);
        if (!empty($where)) {
            if (is_array($where))
                $this->db->where($where);
            else
                $this->db->where($where, NULL, FALSE);
        }
        if (!empty($order_by))
            $this->db->order_by($order_by);
        if (!empty($limit))
            $this->db->limit($limit);
        $list = $this->db->get()->result_array();
        if ($key_field && is_string($key_field)) {
            $new_list = array();
            foreach ($list as $val) {
                $new_list[$val[$key_field]] = $val;
            }
            return $new_list;
        }
        return $list;
    }
}