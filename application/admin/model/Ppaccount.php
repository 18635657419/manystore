<?php

namespace app\admin\model;

use think\Model;


class Ppaccount extends Model
{

    

    

    // 表名
    protected $table = 'ppaccount';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text'
    ];
    

    
    public function getStatusList()
    {
        return ['on' => __('Status on'), 'off' => __('Status off'), 'limited' => __('Status limited'), 'limited180' => __('Status limited180'),"offline"=>__('Status Offline')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function domainmanage()
    {
        return $this->belongsTo('Domainmanage', 'domain_id', 'domain_id', [], 'LEFT')->setEagerlyType(0);
    }
}
