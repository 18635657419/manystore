<?php

namespace app\api\controller;

use app\common\controller\Api;
use \think\Request;
use \think\Db;


/**
 * 首页接口
 */
class Index extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    protected $b_domain_url = "https://{b_domain}/payment.php";
    protected $pp_pay_max_fail_count = 4;

    /**
     * 首页
     *
     */
    public function index()
    {
        $this->success('请求成功');
    }

    public function getPp()
    {
        $request = Request::instance();
        $params = $request->param();
        $token = isset($params['token']) ? $params['token'] : '';
        $pay_balance = isset($params['balance']) ? $params['balance'] : 0;
        $proxy_order_id = isset($params['orderid']) ? $params['orderid'] : $params['invoice'];

        if (!$token) {
            $this->errorlog("未授权#1");
            $this->error("未授权#1");
        }
        $domaininfo = Db::table("domainmanage")->where('token', $token)->whereIn("status", ['on', 'limited'])->find();
        if (!$domaininfo) {
            $this->errorlog("未授权#2");
            $this->error("未授权#2");
        }
        if (!$proxy_order_id) {

            $this->errorlog("参数错误");
            $this->error("参数错误");
        }

        $product_info = $this->parseOrderinfo($params);
        $payment_orderid = "";
        $existsOrder = Db::table("pporder")->where('proxy_order_id', $proxy_order_id)->where("order_uuid", $product_info['order_uuid'])->find();
        $b_domain_url = "";
        $account = [];
        if ($existsOrder) {
            //添加可用判断为防止管理员手动关闭帐号后，还能进款
            $payment_order_id = $existsOrder['order_id'];
            $account = Db::table("ppaccount")->where('status', 'on')->where("pp_id", $existsOrder['pp_id'])->find();;
        }
        if (!$account) {
            $account_list = Db::table("ppaccount")->where('status', 'on')->where("domain_id", $domaininfo['domain_id'])->select();

            $allow_account_list = [];
            foreach ($account_list as $accountinfo) {
                //付款失败次数过多，则禁用付款
                if ($accountinfo['fail_count'] >= $this->pp_pay_max_fail_count) {
                    Db::table("ppaccount")->where('pp_id', $accountinfo['pp_id'])->update(['status' => 'autooff', 'updatedate' => date('Y-m-d H:i:s')]);
                    continue;
                }


                $order_list = Db::table("pporder")->where('status', 'plated')->where("pp_id", $accountinfo['pp_id'])->select();


                $total_amount = $pay_balance;
                $total_order = 0;
                $day_amount = $pay_balance;
                $day_order = 0;
                $today = strtotime(date("Y-m-d 00:00:00"));
                foreach ($order_list as $orderinfo) {
                    $total_amount += $orderinfo['amount'];
                    $total_order += 1;
                    $order_time = strtotime($orderinfo['createdate']);
                    if ($order_time >= $today) {
                        $day_amount += $orderinfo['amount'];
                        $day_order += 1;
                    }
                }
                if ($total_amount < $accountinfo['totalamount'] &&
                    $total_order < $accountinfo['totalorder'] &&
                    $day_order < $accountinfo['orderbyday'] &&
                    $day_amount < $accountinfo['amountbyday']
                ) {
                    $allow_account_list[] = $accountinfo;
                }

            }
            if (!$allow_account_list) {
                $this->errorlog("无帐号可用", $domaininfo['domain_id']);
                $this->error("无帐号可以用");
            }
            $account_key = array_rand($allow_account_list);
            $account = $allow_account_list[$account_key];
            $date = date('Y-m-d H:i:s');
            //添加统计
            //
            $pporder_data = [
                'pp_id' => $account['pp_id'],
                'domain_id' => $domaininfo['domain_id'],
                'proxy_order_id' => $proxy_order_id,
                'createdate' => $date,
                'status' => 'ing',
            ];
            $order_data = $pporder_data;
            $order_data['proxy_order_id'] = $proxy_order_id;
            $order_data['order_uuid'] = $product_info['order_uuid'];
            $order_data['ordername'] = $product_info['productname'];
            $order_data['amount'] = $product_info['total_amount'];
            $order_data['currency_code'] = $params['currency_code'];
            $order_data['storename'] = $product_info['storename'];
            $order_data['proxyemail'] = $params['email'];
            $order_data['zip'] = $params['zip'];
            $order_data['country'] = $params['country'];
            $order_data['city'] = $params['city'];
            $order_data['address'] = $params['address1'] . " " . $params['address2'];
            $order_data['username'] = $params['first_name'] . " " . $params['last_name'];
            $order_data['phone'] = $product_info['phone'];
            $order_data['create_raw_data'] = json_encode($params);
            $order_data['updatedate'] = date("Y-m-d H:i:s");

            Db::table("pporder")->insert($order_data);
            //添加统计代码


            Db::table("ppaccount")->where('pp_id', $account['pp_id'])->inc('fail_count', 1)->update(['updatedate' => date('Y-m-d H:i:s')]);
            $pp_static = Db::table("ppstatistics")->where('account_id', $accountinfo['pp_id'])->find();
            if($pp_static){

                Db::table("ppstatistics")->where('account_id', $account['pp_id'])->inc('order_qty', 1)->inc("order_total", $product_info['total_amount'])->update();
            }else{
                Db::table("ppstatistics")->insert([
                    'account_id' => $account['pp_id'],
                    'order_qty' => 1,
                    'order_total' => $product_info['total_amount'],
                ]);
            }

        }
        if ($account['b_domain']) {
            $b_domain_url = str_replace("{b_domain}", $account['b_domain'], $this->b_domain_url);

        }
        $this->success("ok", [
            "email" => $account['ppaccount'],
            'b_domain_url' => $b_domain_url,
            'payment_order_id' => $product_info['order_uuid'],
        ]);

    }

    public function checkv2()
    {
        $request = Request::instance();
        $params = $request->param();
        $payment_order_id = isset($params['payment_order_id']) ? $params['payment_order_id'] : "";

        $exists_order = Db::table("pporder")->where('status', 'ing')->where("order_uuid", $payment_order_id)->find();
        $host = $params['host'];
        $host = str_replace("www.", "", $host);

        if ($exists_order) {
            Db::table("pporder")->where('order_id', $exists_order['order_id'])->update(['step' => 'jumptob']);
            $exists_paypal = Db::table("ppaccount")->where("pp_id", $exists_order['pp_id'])->find();
            $params = json_decode($exists_order['create_raw_data'], true);
            $params['return'] = str_replace($exists_order['storename'], $host, $params['return']);
            $params['cancel_return'] = str_replace($exists_order['storename'], $host, $params['cancel_return']);
            $params['notify_url'] = str_replace($exists_order['storename'], $host, $params['notify_url']);
            $params['business'] = $exists_paypal['ppaccount'];
            unset($params['token']);
            $this->success('ok', $params);

        }
        $this->errorlog("B站跳转失败#2", "", "", json_encode($params));
        $this->error("error");


    }

    public function check()
    {
        $request = Request::instance();
        $params = $request->param();
        $proxy_order_id = isset($params['orderid']) ? $params['orderid'] : $params['invoice'];
        $orderinfo = $this->parseOrderinfo($params);
        $host = $params['host'];
        $host = str_replace("www.", "", $host);
        $exists_order = Db::table("pporder")->where('status', 'ing')->where("proxy_order_id", $proxy_order_id)->where("order_uuid", $orderinfo['order_uuid'])->find();
        $exists_paypal = Db::table("ppaccount")->where("b_domain", $host)->find();
        if ($exists_order && $exists_paypal) {
            unset($params['sandbox']);
            unset($params['method']);
            unset($params['host']);
            Db::table("pporder")->where('order_id', $exists_order['order_id'])->update(['step' => 'jumptob']);
            $params['return'] = str_replace($orderinfo['storename'], $host, $params['return']);
            $params['cancel_return'] = str_replace($orderinfo['storename'], $host, $params['cancel_return']);
            $params['notify_url'] = str_replace($orderinfo['storename'], $host, $params['notify_url']);
            $this->success('ok', $params);

        }
        $this->error("error");


    }

    public function getPpbyb()
    {
        $request = Request::instance();
        $params = $request->param();
        $host = isset($params['domain']) ? $params['domain'] : "";
        $exists_paypal = Db::table("ppaccount")->where("b_domain", 'like', "%{$host}%")->find();
        $this->success('ok', ['email' => $exists_paypal['ppaccount']]);


    }

    public function stepToA()
    {
        $request = Request::instance();
        $params = $request->param();
        $proxy_order_id = isset($params['orderid']) ? $params['orderid'] : $params['invoice'];
        $proxy_order_id = "WC-" . $proxy_order_id;
        $order_uuid = isset($params['order_uuid']) ? $params['order_uuid'] : "";
        $exists_order = Db::table("pporder")->where("proxy_order_id", $proxy_order_id)->where("order_uuid", $order_uuid)->find();
        if ($exists_order) {
            $savedata = [];
            $savedata['step'] = 'returntob';
            $update_raw_data = isset($params['paypal_ipn']) ? $params['paypal_ipn'] : "";
            if ($update_raw_data) {
                $savedata['update_raw_data'] = $update_raw_data;
            }
            Db::table("pporder")->where('order_id', $exists_order['order_id'])->update($savedata);
            $this->success('ok', json_decode($exists_order['create_raw_data'], true));

        }
        $this->errorlog("error  #11", "", "", json_encode($params));
        $this->error("error #11");

    }

    public function saveOrder()
    {
        $request = Request::instance();
        $params = $request->param();
        $token = isset($params['token']) ? $params['token'] : "";
        $orderid = isset($params['order_id']) ? $params['order_id'] : "";
        $order_uuid = isset($params['order_uuid']) ? $params['order_uuid'] : "";
        $order_status = isset($params['order_status']) ? $params['order_status'] : "";
        if (!$token) {
            $this->errorlog("支付回调验证失败");
            $this->error("支付回调验证失败");
        }
        if (!$orderid) {
            $this->errorlog("支付回调参数错误");
            $this->error("支付回调参数错误");
        }
        //$domaininfo =  Db::table("domainmanage")->where('token', $token)->where("status" , "on")->find();
        //if(! $domaininfo){
        //	  $this->errorlog("支付回调验证失败#2", "", "", json_encode($params));
        //	  $this->error("支付回调验证失败#2");
        //	}

        $order_data = [];
        $order_data['status'] = $order_status;
        $order_data['updatedate'] = date('Y-m-d H:i:s');

        Db::table("pporder")->where("order_uuid", $order_uuid)->where("proxy_order_id", $orderid)->update($order_data);
        //收款成功，失败订单重置 为0
        $pp_account_for_order = Db::table("pporder")->where("order_uuid", $order_uuid)->where("proxy_order_id", $orderid)->find();
        $pp_account_id = $pp_account_for_order['pp_id'];



        if($pp_account_id){
            $pp_account_order_list = Db::table("pporder")->where('pp_id', $pp_account_id)->select();
            $save_data = [];

            $date = date('Y-m-d H:i:s');


            //收款统计
            $success_order_qty = 0;
            $success_total = 0;
            $unpaid_total = 0;
            $unpaid_order_qty = 0;
            $end_order_date = $date;
            foreach ($pp_account_order_list as $order){
                if(in_array($order['status'], ['plated', 'pendding'])){
                    $success_order_qty += 1;
                    $success_total += $order['amount'];
                    //第一笔订单

                }else{
                    $unpaid_total += $order['amount'];
                    $unpaid_order_qty += 1;
                }

            }
            $save_data = [
                'success_order_qty' => $success_order_qty,
                'success_total' => $success_total,
                'unpaid_total' => $unpaid_total,
                'unpaid_order_qty' => $unpaid_order_qty,
                'end_order_date' => $end_order_date,
            ];

            if(count($pp_account_order_list) == 1 && in_array($order_status, ['plated', 'pendding'] )){

                $save_data['first_order_date'] = $date;
            }

            Db::table("ppstatistics")->where('account_id', $pp_account_id)->update($save_data);

        }








        //订单回调成功


        //回调fb订单
        //$this->callbackFb($params);


        $this->success("ok", "");


    }

    private function errorlog($error, $domain_id = "", $pp_id = "", $desc = "")
    {
        $date = date('Y-m-d H:i:s');
        $request = Request::instance();
        $params = $request->param();
        $save = [
            'createdate' => $date,
            'error' => $error,
            'remark' => $desc,
            'ip' => $request->ip(),
        ];
        $domain_id && $save['domain_id'] = $domain_id;
        Db::table("errorlog")->insert($save);

    }

    private function parseOrderinfo($data)
    {
        $product_name = "";
        $amount = $data['tax_cart'];
        $item_key = 1;
        foreach ($data as $key => $info) {
            if (strpos($key, "item_name") !== false) {
                $amount_key = "amount_" . $item_key;
                $quantity_key = "quantity_" . $item_key;
                $shipping_key = 'shipping_' . $item_key;
                $product_name .= $data[$quantity_key] . " x " . $info . " ";
                $amount += $data[$quantity_key] * $data[$amount_key];
                if (isset($data[$shipping_key])) {
                    $amount += $data[$shipping_key];
                }
                $item_key++;

            }

        }
        $night_phone_c = isset($data['night_phone_c']) ? $data['night_phone_c'] : "";
        $night_phone_b = isset($data['night_phone_b']) ? $data['night_phone_b'] : "";
        $store_url_info = parse_url($data['return']);
        $custom = str_replace("&quot;", '"', $data['custom']);

        $order_uuid = $custom;
//        $order_uuid = json_decode($custom, true);

        return [
            'productname' => $product_name,
            'total_amount' => $amount,
            'storename' => $store_url_info['host'],
            'phone' => $data['night_phone_a'] . $night_phone_b . $night_phone_c,
            'order_uuid' => $order_uuid['order_key'],
        ];

    }
}
