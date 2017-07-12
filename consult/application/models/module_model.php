<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Module_model
 */
class Module_model extends MY_Model {
    protected $table_name = 'module';

    public function __construct() {
        parent::__construct();
        $this->table_name = 'module';
    }

    /**
     * @param array $where
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get_module_lists($where = array())
    {
        $this->db->from('module');

        if ($where)
        {
            $this->db->where($where, NULL, FALSE);
        }

        $list = $this->db->get()->result_array();

        return $list;
    }

    public function total_count()
    {
        return $this->db->count_all('module');
    }

    /**
     * @param array $where
     * @return array
     */
    public function get_module_map($where)
    {
        $this->db->from('module');

        $this->db->where_in('id',$where);

        $list = $this->db->get()->result_array();

        $return =  array();

        foreach($list as $item)
        {

            $return[]=$item['module'];
        }

        return $return;
    }

    public function add_module($arr)
    {
        $this->db->insert('t_module',$arr);
        return true;
    }

    public function del_module($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('module');
        return true;
    }

    function has_action($id)
    {
        $this->db->from('module');
        $this->db->where(array('parent_id'=>$id));
        $return= $this->db->get()->result_array();

        return $return;
    }

    public function edit_module($arr,$id)
    {
        $this->db->where('id', $id);
        $this->db->update('t_module',$arr);
        return true;
    }

    public function has_module($module,$type)
    {
        $this->db->from('module');
        $this->db->where(array($type=>$module,'parent_id'=>0));
        $return= $this->db->get()->row();

        return $return;
    }

    /**
     * @param array $where
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get_action_map($module)
    {
        if(empty($module)) return false;
        $this->db->from('module');

        $where = array('parent_id !='=>0,'module ='=>(string)$module);

        $this->db->where($where);

        $list = $this->db->get()->result_array();

        $return =  array();

        foreach($list as $item)
        {
            $return[$item['id']]=$item['action'];
        }

        return $return;
    }
}