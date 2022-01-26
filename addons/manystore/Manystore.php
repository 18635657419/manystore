<?php

namespace addons\manystore;

use app\common\library\Menu;
use app\common\library\ManystoreMenu;
use think\Addons;
use think\Exception;

/**
 * 插件
 */
class Manystore extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name' => 'manystore',
                'title' => '商家管理',
                'icon' => 'fa fa-list',
                'remark' => 'Rule tips',
                'sublist' => [
                    [
                        'name' => 'manystore/index',
                        'title' => '商家列表',
                        'icon' => 'fa fa-list',
                        'sublist' => [
                            ['name' => 'manystore/index/index', 'title' => 'View'],
                            ['name' => 'manystore/index/add', 'title' => 'Add'],
                            ['name' => 'manystore/index/edit', 'title' => 'Edit'],
                            ['name' => 'manystore/index/del', 'title' => 'Delete'],
                        ]
                    ],
                    [
                        'name' => 'manystore/rule',
                        'title' => '商家菜单规则',
                        'icon' => 'fa fa-list',
                        'sublist' => [
                            ['name' => 'manystore/rule/index', 'title' => 'View'],
                            ['name' => 'manystore/rule/add', 'title' => 'Add'],
                            ['name' => 'manystore/rule/edit', 'title' => 'Edit'],
                            ['name' => 'manystore/rule/del', 'title' => 'Delete'],
                        ]
                    ],
                    [
                        'name' => 'manystore/config_group',
                        'title' => '商家系统配置分组',
                        'icon' => 'fa fa-list',
                        'sublist' => [
                            ['name' => 'manystore/config_group/index', 'title' => 'View'],
                            ['name' => 'manystore/config_group/add', 'title' => 'Add'],
                            ['name' => 'manystore/config_group/edit', 'title' => 'Edit'],
                            ['name' => 'manystore/config_group/del', 'title' => 'Delete'],
                        ]
                    ],
                    [
                        'name' => 'manystore/config',
                        'title' => '商家系统配置管理',
                        'icon' => 'fa fa-list',
                        'sublist' => [
                            ['name' => 'manystore/config/index', 'title' => 'View'],
                            ['name' => 'manystore/config/add', 'title' => 'Add'],
                            ['name' => 'manystore/config/edit', 'title' => 'Edit'],
                            ['name' => 'manystore/config/del', 'title' => 'Delete'],
                        ]
                    ],
                    [
                        'name' => 'manystore/command',
                        'title' => '商家在线命令管理',
                        'icon' => 'fa fa-list',
                        'sublist' => [
                            ['name' => 'manystore/command/index', 'title' => '查看'],
                            ['name' => 'manystore/command/add', 'title' => '添加'],
                            ['name' => 'manystore/command/detail', 'title' => '详情'],
                            ['name' => 'manystore/command/execute', 'title' => '运行'],
                            ['name' => 'manystore/command/del', 'title' => '删除'],
                        ]
                    ]
                ]
            ],
        ];

        Menu::create($menu);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {

        Menu::delete('manystore');
        return true;
    }

    /**
     * 插件启用方法
     */
    public function enable()
    {
        Menu::enable('manystore');

        $manystore_menu = [
            [
                'name' => 'dashboard',
                'title' => 'Dashboard',
                'icon' => 'fa fa-dashboard',
                'remark' => 'Dashboard tips',
                'sublist' => [
                    ['name' => 'dashboard/index', 'title' => 'View'],
                    ['name' => 'dashboard/add', 'title' => 'Add'],
                    ['name' => 'dashboard/edit', 'title' => 'Edit'],
                    ['name' => 'dashboard/del', 'title' => 'Delete'],
                    ['name' => 'dashboard/multi', 'title' => 'Multi'],
                ]
            ],
            [
                'name' => 'general',
                'title' => 'General',
                'icon' => 'fa fa-cogs',
                'remark' => '',
                'sublist' => [
                    [
                        'name' => 'general/config',
                        'title' => 'Config',
                        'icon' => 'fa fa-cog',
                        'sublist' => [
                            ['name' => 'general/config/index', 'title' => 'View'],
                            ['name' => 'general/config/edit', 'title' => 'Edit'],
                        ]
                    ],
                    [
                        'name' => 'general/attachment',
                        'title' => 'Attachment',
                        'icon' => 'fa fa-file-image-o',
                        'sublist' => [
                            ['name' => 'general/attachment/index', 'title' => 'View'],
                            ['name' => 'general/attachment/select', 'title' => 'Select attachment'],
                            ['name' => 'general/attachment/add', 'title' => 'Add'],
                            ['name' => 'general/attachment/edit', 'title' => 'Edit'],
                            ['name' => 'general/attachment/del', 'title' => 'Delete'],
                            ['name' => 'general/attachment/multi', 'title' => 'Multi'],
                        ]
                    ],
                    [
                        'name' => 'general/profile',
                        'title' => 'Profile',
                        'icon' => 'fa fa-user',
                        'sublist' => [
                            ['name' => 'general/profile/index', 'title' => 'View'],
                            ['name' => 'general/profile/update', 'title' => 'Update profile'],
                            ['name' => 'general/profile/shop_update', 'title' => 'Update shop'],
                            ['name' => 'general/profile/add', 'title' => 'Add'],
                            ['name' => 'general/profile/edit', 'title' => 'Edit'],
                            ['name' => 'general/profile/del', 'title' => 'Delete'],
                            ['name' => 'general/profile/multi', 'title' => 'Multi'],
                        ]
                    ],
                    [
                        'name' => 'general/log',
                        'title' => '操作日志',
                        'icon' => 'fa fa-file-text',
                        'sublist' => [
                            ['name' => 'general/log/index', 'title' => 'View'],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'auth',
                'title' => 'Auth',
                'icon' => 'fa fa-group',
                'remark' => '',
                'sublist' => [
                    [
                        'name' => 'auth/manystore',
                        'title' => 'Admin',
                        'icon' => 'fa fa-user',
                        'sublist' => [
                            ['name' => 'auth/manystore/index', 'title' => 'View'],
                            ['name' => 'auth/manystore/add', 'title' => 'Add'],
                            ['name' => 'auth/manystore/edit', 'title' => 'Edit'],
                            ['name' => 'auth/manystore/del', 'title' => 'Delete'],
                        ]
                    ],
                    [
                        'name' => 'auth/manystorelog',
                        'title' => 'Admin log',
                        'icon' => 'fa fa-list-alt',
                        'sublist' => [
                            ['name' => 'auth/manystorelog/index', 'title' => 'View'],
                            ['name' => 'auth/manystorelog/detail', 'title' => 'Detail'],
                            ['name' => 'auth/manystorelog/del', 'title' => 'Delete'],
                        ]
                    ],
                    [
                        'name' => 'auth/group',
                        'title' => 'Group',
                        'icon' => 'fa fa-group',
                        'sublist' => [
                            ['name' => 'auth/group/index', 'title' => 'View'],
                            ['name' => 'auth/group/add', 'title' => 'Add'],
                            ['name' => 'auth/group/edit', 'title' => 'Edit'],
                            ['name' => 'auth/group/del', 'title' => 'Delete'],
                        ]
                    ]
                ]
            ]
        ];
        $info = get_addon_info('manystore');
        if(!$info['install']){
            db()->startTrans();
            try{
                ManystoreMenu::create($manystore_menu);
                db()->commit();
                $info['install'] = 1;
                set_addon_info('manystore', $info);
            }catch (Exception $e) {
                db()->rollback();
                return false;
            }
        }
        return true;
    }

    /**
     * 插件禁用方法
     */
    public function disable()
    {
        Menu::disable('manystore');
    }


}
