<?php

namespace app\admin\model;

use think\Model;


class Ppblock extends Model
{

    

    

    // 表名
    protected $table = 'ppblock';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

   
    

    
  



   


    public function domainmanage()
    {
        return $this->belongsTo('Domainmanage', 'domain_id', 'domain_id', [], 'LEFT')->setEagerlyType(0);
    }
}
