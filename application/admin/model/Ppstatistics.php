<?php

namespace app\admin\model;

use think\Model;


class Ppstatistics extends Model
{

    

    

    // 表名
    protected $table = 'ppstatistics';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    
    public function ppaccount()
    {
        return $this->belongsTo('Ppaccount', 'account_id', 'pp_id', [], 'LEFT')->setEagerlyType(0);
    }
    public function manystore(){
        return $this->belongsTo('Manystore', 'shop_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
  
}
