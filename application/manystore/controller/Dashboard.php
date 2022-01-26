<?php

namespace app\manystore\controller;

use app\common\controller\ManystoreBase;
use think\Config;
use think\Session;

use app\admin\model\Admin;
use app\admin\model\User;
use app\common\model\Attachment;
use fast\Date;
use think\Db;

/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends ManystoreBase
{

  

    // /**
    //  * 查看
    //  */
    // public function index()
    // {
    //     $seventtime = \fast\Date::unixtime('day', -7);
    //     $paylist = $createlist = [];
    //     for ($i = 0; $i < 7; $i++)
    //     {
    //         $day = date("Y-m-d", $seventtime + ($i * 86400));
    //         $createlist[$day] = mt_rand(20, 200);
    //         $paylist[$day] = mt_rand(1, mt_rand(1, $createlist[$day]));
    //     }
    //     $hooks = config('addons.hooks');
    //     $uploadmode = isset($hooks['upload_config_init']) && $hooks['upload_config_init'] ? implode(',', $hooks['upload_config_init']) : 'local';
    //     $addonComposerCfg = ROOT_PATH . '/vendor/karsonzhang/fastadmin-addons/composer.json';
    //     Config::parse($addonComposerCfg, "json", "composer");
    //     $config = Config::get("composer");
    //     $addonVersion = isset($config['version']) ? $config['version'] : __('Unknown');
    //     $this->view->assign([
    //         'totaluser'        => 35200,
    //         'totalviews'       => 219390,
    //         'totalorder'       => 32143,
    //         'totalorderamount' => 174800,
    //         'todayuserlogin'   => 321,
    //         'todayusersignup'  => 430,
    //         'todayorder'       => 2324,
    //         'unsettleorder'    => 132,
    //         'sevendnu'         => '80%',
    //         'sevendau'         => '32%',
    //         'paylist'          => $paylist,
    //         'createlist'       => $createlist,
    //         'addonversion'       => $addonVersion,
    //         'uploadmode'       => $uploadmode
    //     ]);

    //     return $this->view->fetch();
    // }
    public function index()
    {
        try {
            \think\Db::execute("SET @@sql_mode='';");
        } catch (\Exception $e) {

        }
        $column = [];
        $starttime = Date::unixtime('day', -6);
        $endtime = Date::unixtime('day', 0, 'end');
        $joinlist = \app\admin\model\Pporder::with('ppaccount')->where('pporder.createdate', 'between time', [$starttime, $endtime])
            ->where('ppaccount.shop_id',SHOP_ID)
            ->where('pporder.status','in',['plated','pendding'])
            ->field('pporder.order_id, pporder.status, COUNT(*) AS nums, DATE_FORMAT(FROM_UNIXTIME(pporder.createdate), "%Y-%m-%d") AS join_date')
            ->group('join_date')
            ->select();
      
        for ($time = $starttime; $time <= $endtime;) {
            $column[] = date("Y-m-d", $time);
            $time += 86400;
        }
        $userlist = array_fill_keys($column, 0);
        foreach ($joinlist as $k => $v) {
            $userlist[$v['join_date']] = $v['nums'];
        }

     


        $joinlist =  \app\admin\model\Pporder::with('ppaccount')->where('pporder.createdate', 'between time', [$starttime, $endtime])
        ->where('ppaccount.shop_id',SHOP_ID)
        ->where('pporder.status','in',['plated','pendding'])
        ->field('pporder.order_id, pporder.status, sum(pporder.amount) AS nums, DATE_FORMAT(FROM_UNIXTIME(pporder.createdate), "%Y-%m-%d") AS join_date')
        ->group('join_date')
        ->select();
       
  
        for ($time = $starttime; $time <= $endtime;) {
            $column[] = date("Y-m-d", $time);
            $time += 86400;
        }
        $incomelist = array_fill_keys($column, 0);
        foreach ($joinlist as $k => $v) {
            $incomelist[$v['join_date']] = round($v['nums'],2);
        }


        $joinlist = \app\admin\model\Stripeorder::with('stripe')->where('stripeorder.createdate', 'between time', [$starttime, $endtime])
        ->where('stripe.shop_id',SHOP_ID)
        ->where('stripeorder.status','in',['plated','pendding'])
        ->field('stripeorder.order_id, stripeorder.status, COUNT(*) AS nums, DATE_FORMAT(FROM_UNIXTIME(stripeorder.createdate), "%Y-%m-%d") AS join_date')
        ->group('join_date')
        ->select();
  
        for ($time = $starttime; $time <= $endtime;) {
            $column[] = date("Y-m-d", $time);
            $time += 86400;
        }
        $stripeorder = array_fill_keys($column, 0);
        foreach ($joinlist as $k => $v) {
            $stripeorder[$v['join_date']] = $v['nums'];
        }

        $joinlist = \app\admin\model\Stripeorder::with('stripe')->where('stripeorder.createdate', 'between time', [$starttime, $endtime])
        ->where('stripe.shop_id',SHOP_ID)
        ->where('stripeorder.status','in',['plated','pendding'])
        ->field('stripeorder.order_id, stripeorder.status, sum(amount) AS nums, DATE_FORMAT(FROM_UNIXTIME(stripeorder.createdate), "%Y-%m-%d") AS join_date')
        ->group('join_date')
        ->select();
       
  
        for ($time = $starttime; $time <= $endtime;) {
            $column[] = date("Y-m-d", $time);
            $time += 86400;
        }
        $stripeincomelist = array_fill_keys($column, 0);
        foreach ($joinlist as $k => $v) {
            $stripeincomelist[$v['join_date']] = round($v['nums'],2);
        }

       

        // 统计查询显示

        $start=date("Y-m-d",time())." 0:0:0";
        $end=date("Y-m-d",time())." 24:00:00";

        $sdefaultDate = date("Y-m-d"); 
        //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期 
        $first=1; 
        //获取当前周的第几天 周日是 0 周一到周六是 1 - 6 
        $w=date('w',strtotime($sdefaultDate)); 
        //获取本周开始日期，如果$w是0，则表示周日，减去 6 天 
        $week_start = date('Y-m-d',strtotime("$sdefaultDate -".($w ? $w - $first : 6).' days')); 
        //本周结束日期 
        $week_end = date('Y-m-d',strtotime("$week_start +6 days"));

        $beginThismonth = date("Y-m-d",mktime(0,0,0,date('m'),1,date('Y')))." 0:0:0"; 
        $endThismonth=  date("Y-m-d",mktime(23,59,59,date('m'),date('t'),date('Y')))." 24:00:00";

        

       
        $dbTableList = Db::query("SHOW TABLE STATUS");
        $this->view->assign([
            'pporder'         => \app\admin\model\Pporder::with('ppaccount')->where('ppaccount.shop_id',SHOP_ID)->where('pporder.status','in',['plated','pendding'])->count(),
            'pporderTotal'    =>  round(\app\admin\model\Pporder::with('ppaccount')->where('ppaccount.shop_id',SHOP_ID)->where('pporder.status','in',['plated','pendding'])->sum('amount'),2),
            'pporderingqty'   =>  \app\admin\model\Pporder::with('ppaccount')->where('ppaccount.shop_id',SHOP_ID)->where('pporder.status','ing')->count(),
            'pporderingTotal' =>   round( \app\admin\model\Pporder::with('ppaccount')->where('ppaccount.shop_id',SHOP_ID)->where('pporder.status','ing')->sum('amount'),2),
            'pptodayqty'      =>  \app\admin\model\Pporder::with('ppaccount')->where('ppaccount.shop_id',SHOP_ID)->whereTime('pporder.createdate', 'between', [$start, $end])->where('pporder.status','in',['plated','pendding'])->count(),
            'pptodaytotal'    =>  round(\app\admin\model\Pporder::with('ppaccount')->where('ppaccount.shop_id',SHOP_ID)->whereTime('pporder.createdate', 'between', [$start, $end])->where('pporder.status','in',['plated','pendding'])->sum('amount'),2),
            'ppweekqty'      =>  \app\admin\model\Pporder::with('ppaccount')->where('ppaccount.shop_id',SHOP_ID)->whereTime('pporder.createdate', 'between', [$week_start, $week_end])->where('pporder.status','in',['plated','pendding'])->count(),
            'ppweektotal'    =>  round( \app\admin\model\Pporder::with('ppaccount')->where('ppaccount.shop_id',SHOP_ID)->where('pporder.status','in',['plated','pendding'])->whereTime('pporder.createdate', 'between', [$week_start, $week_end])->sum('amount'),2),
            'ppmonthqty'      =>  \app\admin\model\Pporder::with('ppaccount')->where('ppaccount.shop_id',SHOP_ID)->whereTime('pporder.createdate', 'between', [$beginThismonth, $endThismonth])->where('pporder.status','in',['plated','pendding'])->count(),
            'ppmonthtotal'    =>  round( \app\admin\model\Pporder::with('ppaccount')->where('ppaccount.shop_id',SHOP_ID)->where('pporder.status','in',['plated','pendding'])->whereTime('pporder.createdate', 'between', [$beginThismonth, $endThismonth])->sum('amount'),2),
            'totaluser'       => User::count(),
            'totaladdon'      => count(get_addon_list()),
            'totaladmin'      => Admin::count(),
            'totalcategory'   => \app\common\model\Category::count(),
            'todayusersignup' => User::whereTime('jointime', 'today')->count(),
            'todayuserlogin'  => User::whereTime('logintime', 'today')->count(),
            'sevendau'        => User::whereTime('jointime|logintime|prevtime', '-7 days')->count(),
            'thirtydau'       => User::whereTime('jointime|logintime|prevtime', '-30 days')->count(),
            'threednu'        => User::whereTime('jointime', '-3 days')->count(),
            'sevendnu'        => User::whereTime('jointime', '-7 days')->count(),
            'dbtablenums'     => count($dbTableList),
            'dbsize'          => array_sum(array_map(function ($item) {
                return $item['Data_length'] + $item['Index_length'];
            }, $dbTableList)),
            'attachmentnums'  => Attachment::count(),
            'attachmentsize'  => Attachment::sum('filesize'),
            'picturenums'     => Attachment::where('mimetype', 'like', 'image/%')->count(),
            'picturesize'     => Attachment::where('mimetype', 'like', 'image/%')->sum('filesize'),
            // stripe订单统计
            'stripeorder'         => \app\admin\model\Stripeorder::where('status','plated')->count(),
            'stripeorderTotal'    =>  round(\app\admin\model\Stripeorder::where('status','plated')->sum('amount'),2),
            'stripeorderingqty'   =>   \app\admin\model\Stripeorder::where('status','ing')->count(),
            'stripeorderingTotal' =>   round(\app\admin\model\Stripeorder::where('status','ing')->sum('amount'),2),
            'stripetodayqty'      =>  \app\admin\model\Stripeorder::whereTime('createdate', 'between', [$start, $end])->where('status','plated')->count(),
            'stripetodaytotal'    =>  round( \app\admin\model\Stripeorder::where('status','plated')->where('status','plated')->whereTime('createdate', 'between', [$start, $end])->sum('amount'),2),
            'stripeweekqty'      =>  \app\admin\model\Stripeorder::whereTime('createdate', 'between', [$week_start, $week_end])->where('status','plated')->count(),
            'stripeweektotal'    =>  round( \app\admin\model\Stripeorder::where('status','plated')->where('status','plated')->whereTime('createdate', 'between', [$week_start, $week_end])->sum('amount'),2),
            'stripemonthqty'      =>  \app\admin\model\Stripeorder::whereTime('createdate', 'between', [$beginThismonth, $endThismonth])->where('status','plated')->count(),
            'stripemonthtotal'    =>  round( \app\admin\model\Stripeorder::where('status','plated')->whereTime('createdate', 'between', [$beginThismonth, $endThismonth])->sum('amount'),2),



        ]);
        $this->assignconfig('column', array_keys($userlist));
        $this->assignconfig('userdata', array_values($userlist));
        $this->assignconfig('incomelist', array_values($incomelist));

        $this->assignconfig('stripeorder', array_values($stripeorder));
        $this->assignconfig('stripeincomelist', array_values($stripeincomelist));

        return $this->view->fetch();
    }


}
