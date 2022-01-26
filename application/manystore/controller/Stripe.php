<?php

namespace app\manystore\controller;

use app\common\controller\ManystoreBase;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Cookie;
use think\Session;
use Exception;
use think\Db;


/**
 * stripe管理
 *
 * @icon fa fa-circle-o
 */
class Stripe extends ManystoreBase
{
    
    /**
     * Stripe模型对象
     * @var \app\admin\model\Stripe
     */
    protected $model = null;

    public function _initialize()
    {   
      
        parent::_initialize();
        $this->model = new \app\admin\model\Stripe;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("accountTypeList", $this->model->getAccountTypeList());
    }

    public function import()
    {
        parent::import();
    }

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
                    ->where($where)
                    ->where('shop_id',SHOP_ID)
                    ->order($sort, $order)
                    ->paginate($limit);
            $Stripeorder = new \app\admin\model\Stripeorder();
            $start=date("Y-m-d",time())." 0:0:0";
            $end=date("Y-m-d",time())." 24:00:00";
        
            foreach ($list as $row) {
                $allamount = $Stripeorder->where('stripe_id',$row['id']) ->where('shop_id',SHOP_ID)->where('status','in',['plated','pendding'])->sum('amount');
                $todayamount =  $Stripeorder->where('stripe_id',$row['id']) ->where('shop_id',SHOP_ID)->where('status','in',['plated','pendding'])->whereTime('createdate', 'between', [$start, $end])->sum('amount');
                $allqty = $Stripeorder->where('stripe_id',$row['id']) ->where('shop_id',SHOP_ID)->where('status','in',['plated','pendding'])->count();
                $todayqty =  $Stripeorder->where('stripe_id',$row['id']) ->where('shop_id',SHOP_ID)->where('status','in',['plated','pendding'])->whereTime('createdate', 'between', [$start, $end])->count();
                $row['allamount'] =  round($allamount,2);
                $row['todayamount'] = round($todayamount,2);
                $row['allqty'] = $allqty;
                $row['todayqty'] = $todayqty;
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

    
  

}
