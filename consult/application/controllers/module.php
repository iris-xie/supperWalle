<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Log
 */
class Module extends MY_Controller{

    public function __construct() {
        parent::__construct();
        $this->load->model('module_model', 'module_model');
    }

    function index()
    {

        $module_config=$this->module_model->get_module_lists();

        $this->data->set(array(
            'module_config'    => $module_config,
        ));
        $this->data['add_pri'] = $this->has_privileges('module', 'add_module');
        $this->data['edit_pri'] = $this->has_privileges('module', 'edit_module');
        $this->data['del_pri'] = $this->has_privileges('module', 'del_module');
        $this->load->view('module/module_list', $this->data->all());

    }

    function module_list() {
        $_map  = array('id','name','module','action','remark');
        $limit = $this->input->get('length');
        $start = $this->input->get('start');
        $orders = $this->input->get('order');
        $columns = $this->input->get('columns');

        $search = $this->input->get('search');

        $search_where = "";

        $filter = "";
        if(!empty($orders))
        {
            $filter = " ORDER BY ";
            foreach($orders as $order)
            {
                $filter .= $_map[$order['column']]." ".$order['dir'].",";
            }

        }

        if(!empty($filter))
        {
            $filter=rtrim($filter,',');
        }

        if(!empty($columns))
        {
            foreach($columns as $con)
            {

                if($con['searchable'] === 'true' && !empty($search['value']))
                {
                    $search_where .= ' '.$con['data']. ' = \''.$search['value'].'\' OR';
                }
            }

        }

        if(!empty($search_where))
        {
            $new_search_where = rtrim($search_where,'OR');
        }

        if(!empty($limit))
        {
            $start = empty($start)?0:$start;
            $limmit_clause = ' LIMIT '.$start.','.$limit;

        }else{
            $limmit_clause = '';
        }
        $where_clause = empty($new_search_where)?1:$new_search_where;
        $where = ' '.$where_clause.$filter.$limmit_clause;

        $data=$this->module_model->get_module_lists($where);

        if(!empty($limit))
        {
            $where = ' '.$where_clause.$filter;
            $count_base = $this->module_model->get_module_lists($where);
            $count = count($count_base);
        }

        $total = $this->module_model->total_count();

        echo json_encode(array('data'=>$data,"recordsTotal"=>$total,"recordsFiltered"=>empty($count)?count($data):$count,"draw"=>$_GET["draw"]));

    }

    function add_module()
    {
        $module=$this->input->get('module');
        $action=$this->input->get('action');
        $name  =$this->input->get('name');
        $remark=$this->input->get('remark');

        if(empty($module) || empty($name))
            self::json('参数错误',self::DEFAULT_ERROR_STATUS);

        $res =$this->module_model->has_module($module,'module');
        if($res)
        {
            if(empty($action))
                self::json('参数错误',self::DEFAULT_ERROR_STATUS);

            $arr = array(
                'module'=>$module,
                'action'=>$action,
                'name'=>$name,
                'parent_id'=>$res->id,
                'remark'=>$remark
            );
        }else{
            $arr = array(
                'module'=>$module,
                'action'=>'',
                'name'=>$name,
                'parent_id'=>0,
                'remark'=>$remark
            );
        }
        $module_id = $this->module_model->add($arr);

        self::record_log('module', '模块添加', '添加模块:'.$name.',module:'.$module.',action:'.$action.',remark:'.$remark,$module_id);
        self::json('添加成功',self::SUCCESS_STATUS);
    }

    function del_module()
    {
        $id = $this->input->get('id');
        $module = $this->input->get('module');

        if (empty($id)||empty($module))
        {
            self::json('参数错误', self::DEFAULT_ERROR_STATUS);
        }

        $res = $this->module_model->has_module($module, 'module');

        if (!empty($res) && $res->id != $id)
        {
            $info = $this->module_model->info($id,'id');
            $res  = $this->module_model->delete(array('id'=>$id));
            if($res)
            {
                self::record_log('module', '模块删除', '模块删除:'.',module:'.$info['module'].',action:'.$info['action'].',remark:'.$info['remark'],$id);
                self::json('操作成功!', self::SUCCESS_STATUS);
            }
        } elseif (!empty($res) && $res->id == $id)
        {
            $has_action = $this->module_model->has_action($res->id);
            if (empty($has_action))
            {
                $info = $this->module_model->info($id,'id');
                $res  = $this->module_model->delete(array('id'=>$id));
                if($res)
                {
                    self::record_log('module', '模块删除', '模块删除:'.',module:'.$info['module'].',action:'.$info['action'].',remark:'.$info['remark'],$id);
                    self::json('操作成功!', self::SUCCESS_STATUS);
                }
            } else
            {
                self::json('模块不为空，违法操作', self::DEFAULT_ERROR_STATUS);
            }
        } elseif (empty($res))
        {
            $info = $this->module_model->info($id,'id');
            $res  = $this->module_model->delete(array('id'=>$id));
            if($res)
            {
                self::record_log('module', '模块删除', '模块删除:'.',module:'.$info['module'].',action:'.$info['action'].',remark:'.$info['remark'],$id);
                self::json('非法子动作，删除成功!', self::SUCCESS_STATUS);
            }
        }

    }

    function edit_module()
    {
        $module=$this->input->get('module');
        $action=$this->input->get('action');
        $name  =$this->input->get('name');
        $id    =$this->input->get('id');
        $remark=$this->input->get('remark');

        if(empty($name)||empty($id))
            self::json('参数错误',self::DEFAULT_ERROR_STATUS);

        $res =$this->module_model->has_module($module,'module');

        if(!empty($res))
        {
            $arr = array(
                'remark'=>$remark,
                'module'=>$module,
                'action'=>$action,
                'name'=>$name,
            );
            $res = $this->module_model->update(array('id'=>$id),$arr);
            if($res)
            {
                self::record_log('module', '模块编辑', '编辑模块:'.',module:'.$arr['module'].',action:'.$arr['action'].',remark:'.$arr['remark'],$id);
                self::json('编辑成功',self::SUCCESS_STATUS);
            }
        }else{
            self::json('模块不存在',self::DEFAULT_ERROR_STATUS);
        }
    }
}