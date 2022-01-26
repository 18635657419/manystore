<?php

namespace app\manystore\controller;

use app\common\controller\ManystoreBase;

/**
 * pp帐号管理
 *
 * @icon fa fa-circle-o
 */
class Stripestatistics extends ManystoreBase
{
    
    /**
     * Ppaccount模型对象
     * @var \app\admin\model\Ppaccount
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Stripestatistics;
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
                    ->with(['stripe'])
                    ->where($where)
                    ->where('stripestatistics.shop_id',SHOP_ID)
                    ->order($sort, $order)
                    ->paginate($limit);
            foreach ($list as $row) {
                $row['success_rate'] =  round($row['success_order_qty']/$row['order_qty'],2) * 100 ."%"; //成功率
                $row['period']  = floor((strtotime($row['end_order_date'])-strtotime($row['first_order_date']))/86400) == 0 ? 1:floor((strtotime($row['end_order_date'])-strtotime($row['first_order_date']))/86400);// 收款周期
               
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

}