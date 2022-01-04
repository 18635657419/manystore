<?php

namespace app\admin\model;

use think\Model;


class Pporder extends Model
{

    

    

    // 表名
    protected $table = 'pporder';
    
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
        return ['plated' => __('Status plated'), 'cancal' => __('Status cancal'), 'ing' => __('Status ing')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function ppaccount()
    {
        return $this->belongsTo('Ppaccount', 'pp_id', 'pp_id', [], 'LEFT')->setEagerlyType(0);
    }


    public function domainmanage()
    {
        return $this->belongsTo('Domainmanage', 'domain_id', 'domain_id', [], 'LEFT')->setEagerlyType(0);
    }
}
