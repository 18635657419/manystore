<?php

namespace app\admin\model;

use think\Model;


class Errorlog extends Model
{

    

    

    // 表名
    protected $table = 'errorlog';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    







    public function ppaccount()
    {
        return $this->belongsTo('Ppaccount', 'pp_id', 'pp_id', [], 'LEFT')->setEagerlyType(0);
    }


    public function domainmanage()
    {
        return $this->belongsTo('Domainmanage', 'domain_id', 'domain_id', [], 'LEFT')->setEagerlyType(0);
    }
}
