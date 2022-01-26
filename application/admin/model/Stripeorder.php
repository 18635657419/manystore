<?php

namespace app\admin\model;

use think\Model;


class Stripeorder extends Model
{

    

    

    // 表名
    protected $table = 'stripeorder';
    
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


    public function manystore(){
        return $this->belongsTo('Manystore', 'shop_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function stripe()
    {
        return $this->belongsTo('Stripe', 'stripe_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
