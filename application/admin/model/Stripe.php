<?php

namespace app\admin\model;

use think\Model;


class Stripe extends Model
{

    

    

    // 表名
    protected $table = 'stripe';
    
    // 自动写入时间戳字段

     // 自动写入时间戳字段
     protected $autoWriteTimestamp = true;
     
     protected $type = [
         'createdate' => 'datetime:Y-m-d H:i:s',
         'updatedate' => 'datetime:Y-m-d H:i:s',
     ];

     // 定义时间戳字段名
     protected $createTime = 'createdate';
     protected $updateTime = 'updatedate';
     protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text',
        'account_type_text'
    ];
    

    
    public function getStatusList()
    {
        return ['on' => __('Status on'), 'pause' => __('Status pause'), 'off' => __('Status off')];
    }

    public function getAccountTypeList()
    {
        return ['T7' => __('Account_type t7'), 'T2' => __('Account_type t2')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getAccountTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['account_type']) ? $data['account_type'] : '');
        $list = $this->getAccountTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function manystore(){
        return $this->belongsTo('Manystore', 'shop_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


}
