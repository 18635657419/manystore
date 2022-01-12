<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 订单管理
 *
 * @icon fa fa-circle-o
 */
class Ppexpress extends Backend
{
    
    /**
     * Pporder模型对象
     * @var \app\admin\model\Pporder
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Ppexpress;
    }

    public function import()
    {
        parent::import();
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = $this->model
                    ->with('pporder')
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                
                
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }
    
    public function checkdata(){
        $value = input('value');
        $alldata = explode(PHP_EOL,$value);
     
        $allorder = [];
        foreach($alldata as $onedata){
            $data = explode(",",$onedata);
            foreach($data as $k=>$v){
                if($k == 0 ){
                    // 导入验证TODO    
                    $allorder[] = $v;                
                }
            }
        }
        $Pporder = new \app\admin\model\Pporder;
        $order_count = $Pporder->where('order_id','in',$allorder)->count();
        //初步判断是否所有订单数量是否一致
        if($order_count !== count($allorder)){
            //循环查询排查出不存在的订单号给予提示
            foreach($allorder as $order_id){
               $count =  $Pporder->where('order_id',$order_id)->count();
               if($count == 0){
                   $this->error("订单id:".$order_id.'不存在');
               }
            }
        }
        $Ppaccount = new \app\admin\model\Ppaccount;
        $list = $Ppaccount->where('status','on')->select();
       
        // 获取域名id列表
        $this->success('验证通过',"",$list);
    }


    public function importAccount(){
        $value = input('value');

       
        $remark = input('remark');
       
        if(!isset($value) || !$value){
            $this->error("参数错误");
        }
        $alldata = explode(PHP_EOL,$value);
      
        //自动生成导入批号
        $block_number = $this->getNextBlock();
      
        $add_data = [];
        foreach($alldata as $key => $onedata){
               //一行数据处理
            $new_one_data = [];
            $data = explode(",",$onedata);
            // $this->checkdata($data);
            // 0网站运单 1运单号2快递公司
            $new_one_data = [
                'order_id'     => $data[0]? $data[0]:'',
                'express_number'     => $data[1]? $data[1]:'',
                'exress_name'      => isset($data[2]) ? $data[2]:'',
                'status'          => 'unexecuted',//默认未执行
                'remark'          => $remark,
                'block_number'              =>$block_number,
                'createdate'            => date('Y-m-d H:i:s'),
            ];
            $add_data[] = $new_one_data;
        }
        //生成批号数据
        $blockdata = [
            'block_number' => $block_number,
            'qty' => count($add_data),
            'remark' => $remark,
            'order_id' =>$data[0]? $data[0]:'',
            'createdate'            => date('Y-m-d H:i:s'),
        ];
        $Ppblock  = new \app\admin\model\ppexpressBlock();
        //数据转化数组
        $res = $this->model->insertAll($add_data);
        if($res){
            $Ppblock->insert($blockdata);
            $this->success("添加任务成功");
        }else{
            $this->success("添加任务失败");
        }
    }

    public function getNextBlock(){
        $time = strtotime(date("Y-m-d 00:00:00"));
        $start=date("Y-m-d",time())." 0:0:0";
        $end=date("Y-m-d",time())." 24:00:00";
        $block_count = $this->model->whereTime('createdate', 'between', [$start, $end])->count();
        $next_count = $block_count + 1;
        $next_count = $next_count < 9 ? '0' . $next_count : $next_count;
        return date("Ymd", $time) . '.' . $next_count;
    }

}
