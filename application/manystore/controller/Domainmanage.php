<?php

namespace app\manystore\controller;

use app\common\controller\ManystoreBase;
use \think\Request;

/**
 * 域名管理
 *
 * @icon fa fa-circle-o
 */
class Domainmanage extends ManystoreBase
{
    
    /**
     * Domainmanage模型对象
     * @var \app\admin\model\Domainmanage
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Domainmanage;
        $this->view->assign("statusList", $this->model->getStatusList());
    }

	public function index1(){
		 $request = Request::instance();
		 $params = $request->param();
		 $kw = $params['name'];
		 $domainlist = $this->model->where("name", "like", "%{$kw}%")->find();
		 $res = [
		 	'list' => $domainlist,
		 ];

		 exit(json_encode($res));
		
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
    

}
