<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use Exception;

/**
 * pp帐号管理
 *
 * @icon fa fa-circle-o
 */
class Ppaccount extends Backend
{
    
    /**
     * Ppaccount模型对象
     * @var \app\admin\model\Ppaccount
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Ppaccount;
        $this->view->assign("statusList", $this->model->getStatusList());
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
                    ->with(['domainmanage'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);
            $Pporder = new \app\admin\model\Pporder();
            $start=date("Y-m-d",time())." 0:0:0";
            $end=date("Y-m-d",time())." 24:00:00";
        
            foreach ($list as $row) {
                $allamount = $Pporder->where('pp_id',$row['pp_id'])->sum('amount');
                $todayamount =  $Pporder->where('pp_id',$row['pp_id'])->whereTime('createdate', 'between', [$start, $end])->sum('amount');
                $row['allamount'] =  round($allamount,2);
                $row['todayamount'] = round($todayamount,2);;
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    public function importAccount(){
        $value = input('value');

        $status = input('status');
        $remark = input('remark');
        $offline_day_value = input('offline_day_value');
        $block_remarks = input('block_remarks');
        $amountbyday = input('amountbyday');
        $orderbyday = input('orderbyday');
        $totalamount = input('totalamount');
        $totalorder = input('totalorder');
        $domain_id = input('domain_id');
       
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
            $data = explode("-",$onedata);
            // $this->checkdata($data);
            // 0pp帐号 1B站域名2状态3备注4总订单5总金额6每天总订单7每天总金额
            $new_one_data = [
                'ppaccount'     => $data[0],
                'b_domain'      => $data[1],
                'public_key'    => $data[2],
                'private_key'    => $data[3],
                'status'          => $status,
                'remark'          => $remark,
                'offline_day_value'          => $offline_day_value,
                'block_remarks'          => $block_remarks,
                'block_number'              =>$block_number,
                'createdate'            => date('Y-m-d H:i:s'),
                'amountbyday' => $amountbyday,
                'orderbyday'  => $orderbyday,
                'totalamount' => $totalamount,
                'totalorder'  => $totalorder,
                'domain_id'     =>$domain_id,

            ];
            
            // if($new_one_data['b_domain']){
            //     // 查询domain_id
            //     $Domainmanage= new \app\admin\model\Domainmanage();
            //     $domainData = $Domainmanage->where('name',$new_one_data['b_domain'])->find();
            //     if($domainData){
            //         $new_one_data['domain_id'] = $domainData['domain_id'];
            //     }
            // }
           
            $add_data[] = $new_one_data;
           
        }
        //数据转化数组
        $res = $this->model->insertAll($add_data);
        if($res){
            $this->success("导入成功");
        }else{
            $this->success("导入失败");
        }
    }

    public function getNextBlock(){
        $time = strtotime(date("Y-m-d 00:00:00"));
        $block_count = $this->model->where("createdate", '>=', $time)
                    ->where("createdate", '<', $time + 86400)
                    ->count();
           

        $next_count = $block_count + 1;
        $next_count = $next_count < 9 ? '0' . $next_count : $next_count;
        return date("Ymd", $time) . '.' . $next_count;


    }
    // 0pp帐号 1B站域名2状态3备注4总订单5总金额6每天总订单7每天总金额
    public function checkdata(){
        $value = input('value');
        $alldata = explode(PHP_EOL,$value);
        foreach($alldata as $onedata){
            $data = explode("-",$onedata);
            if(count($data) !== 4){
                $this->error("格式不正确");
            }
            foreach($data as $k=>$v){
                if($k == 0 && $v == ''){
                    $this->error("pp帐号不能为空");
                }
                if($k == 1 && $v == ''){
                    $this->error("域名不能为空");
                }
            }
        }
        $Domainmanage = new \app\admin\model\Domainmanage;
        $list = $Domainmanage->where('status','on')->select();
       
        // 获取域名id列表
        $this->success('验证通过',"",$list);
    }

      /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);

                // 查询账号修改前的状态
                $lastdata = $this->model->where('pp_id',$ids)->find();
                if($lastdata['status'] == 'autooff' && $params['status'] == 'on'){
                    $params['fail_count'] = 0;
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }


}
