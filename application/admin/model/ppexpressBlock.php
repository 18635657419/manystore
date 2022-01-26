<?php

namespace app\admin\model;

use think\Model;


class ppexpressBlock extends Model
{

    

    

    // 表名
    protected $table = 'ppexpress_block';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    
    public function pporder()
    {
        return $this->belongsTo('Pporder', 'order_id', 'order_id', [], 'LEFT')->setEagerlyType(0);
    }

    public function manystore(){
        return $this->belongsTo('Manystore', 'shop_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
