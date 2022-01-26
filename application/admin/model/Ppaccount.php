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
        'status_text',
        'second_status_text'
    ];
    
    public function getSecondStatusList(){
        return ['on_normal_on' => __('正常（审核完成）'), 'on_normal_not' => __('正常（未出现审核）'),
         'on_power_ing' => __('有收款权限审核中'),"on_power_off"=>__('有收款权限待提审'),
         'off_normal' =>'待使用','autooff_first_off_power' =>'首单无收款权限','autooff_ing_off_power' =>'使用中无收款受限','autooff_off_off_power' =>'未进单无收款权限','autooff_continuity_off' =>'连续失败(待人工排查)','limited180_money_time' =>'不可用',
         'finish_check_pending' =>'待提审', 'finish_cash_pending' =>'待提现', 'finish_on' =>'已完成',
        ];
    }
    
    public function getFinishStatusList(){
        return ['finish_check_pending' =>'待提审', 'finish_cash_pending' =>'待提现', 'finish_on' =>'已完成',];
    }

    public function getAutooffStatusList(){
        return ['autooff_first_off_power' => __('首单无收款权限'), 'autooff_ing_off_power' => __('使用中无收款受限'), 'autooff_off_off_power' => __('未进单无收款权限'),"autooff_continuity_off"=>__('连续失败(待人工排查)')];
    }

    public function getLimited180StatusList(){
        return ['limited180_money_time' =>'不可用'];
    }

    public function getOffStatusList(){
        return ['off_normal' => __('待使用')];
    }

    public function getOnStatusList(){
        return ['on_normal_on' => __('正常（审核完成）'), 'on_normal_not' => __('正常（未出现审核）'), 'on_power_ing' => __('有收款权限审核中'),"on_power_off"=>__('有收款权限待提审')];
    }
    
    public function getStatusList()
    {
        return ['on' => __('正在使用'), 'off' => __('待使用'), 'limited180' => __('不可用'),"autooff"=>__('提前下线'),'finish' =>'已完成'];
    }


    public function getPriorityList()
    {
        return ['level1' => __('最高'), 'level2' => __('中等'), 'level3' => __('一般')];
    }

    public function getSecondStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['second_status']) ? $data['second_status'] : '');
        $list = $this->getSecondStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function manystore(){
        return $this->belongsTo('Manystore', 'shop_id', 'shop_id', [], 'LEFT')->setEagerlyType(0);
    }

    public function domainmanage()
    {
        return $this->belongsTo('Domainmanage', 'domain_id', 'domain_id', [], 'LEFT')->setEagerlyType(0);
    }

}
