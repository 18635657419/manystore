<?php

namespace app\manystore\model;

use think\Cache;
use think\Model;

class ManystoreAuthRule extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    protected static function init()
    {
        self::afterWrite(function ($row) {
            Cache::rm('__manystore_menu__');
        });
    }

    public function getTitleAttr($value, $data)
    {
        return __($value);
    }

    public static function getTreeList($selected = [])
    {
        $ruleList = collection(self::where('status', 'normal')->order('weigh desc,id desc')->select())->toArray();
        $nodeList = [];
        Tree::instance()->init($ruleList);
        $ruleList = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'name');
        $hasChildrens = [];
        foreach ($ruleList as $k => $v)
        {
            if ($v['haschild'])
                $hasChildrens[] = $v['id'];
        }
        foreach ($ruleList as $k => $v) {
            $state = array('selected' => in_array($v['id'], $selected) && !in_array($v['id'], $hasChildrens));
            $nodeList[] = array('id' => $v['id'], 'parent' => $v['pid'] ? $v['pid'] : '#', 'text' => __($v['title']), 'type' => 'menu', 'state' => $state);
        }
        return $nodeList;
    }


}
