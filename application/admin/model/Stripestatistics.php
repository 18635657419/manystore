<?php

namespace app\admin\model;

use think\Model;


class Stripestatistics extends Model
{

    

    

    // 表名
    protected $table = 'stripestatistics';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    
    public function stripe()
    {
        return $this->belongsTo('stripe', 'stripe_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

  
}
