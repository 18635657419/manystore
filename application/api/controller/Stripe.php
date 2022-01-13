<?php

namespace app\api\controller;

use app\common\controller\Api;
use \think\Request;
use \think\Db;


/**
 * 首页接口
 */
class Stripe extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    protected $t7_max_order_total = 50;
    protected $t7_max_amount_total = 4000;
    protected $t7_max_order_day = 4;
    protected $t7_max_amount_day = 400;
    protected $stripe_pay_max_fail_count = 5;

    /**
     * 首页
     *
     */
    public function index()
    {
        $this->success('请求成功');
    }
    public function getStripe(){
        $request = Request::instance();
        $params = $request->param();
        $token = isset($params['token']) ? $params['token'] : '';
        $pay_balance = isset($params['balance']) ? $params['balance'] : 0;
        $proxy_order_id = isset($params['order_id']) ? $params['order_id'] : "";
        $domain =  isset($params['domain']) ? $params['domain'] : "";
        if(! $token ){
            $this->errorlog("stripe授权#1");
            $this->error("stripe未授权#1");
        }
        $token = str_replace("sk_test_", '', $token);
        $stripe_group=  Db::table("stripe_group")->where('token', $token)->whereIn("status" ,['on'])->find();
        if(! $stripe_group){
            $this->errorlog("stripe未授权#2");
            $this->error("stripe未授权#2");
        }
        $account = [];
        if(! $account){
            $account_list = Db::table("stripe")->where('status', 'on')->whereIn("id", $stripe_group['stripe_ids'])->select();;

            $allow_account_list = [];
            foreach($account_list as $accountinfo){
                if ($accountinfo['fail_count'] >= $this->stripe_pay_max_fail_count) {
                    Db::table("stripe")->where('id', $accountinfo['id'])->update(['status' => 'pause', 'updatedate' => date('Y-m-d H:i:s')]);
                    continue;
                }
                //T7 帐号轮询
                $max_amount_total = $this->t7_max_amount_total;
                $max_order_total = $this->t7_max_order_total;
                $max_order_day = $this->t7_max_order_day;
                $max_amount_day = $this->t7_max_amount_day;
                if($accountinfo['account_type'] == 'T2'){
                    $max_amount_total = $accountinfo['totalamount'];
                    $max_order_total = $accountinfo['totalorder'];;
                    $max_order_day = $accountinfo['orderbyday'];
                    $max_amount_day = $accountinfo['amountbyday'];

                }


                $order_list = Db::table("stripeorder")->where('status', 'plated')->where("stripe_id", $accountinfo['id'])->select();

                $total_amount = $pay_balance;
                $total_order = 0;
                $day_amount = $pay_balance;
                $day_order = 0;
                $today = strtotime(date("Y-m-d 00:00:00"));
                foreach($order_list as $orderinfo){
                    $total_amount += $orderinfo['amount'];
                    $total_order += 1;
                    $order_time = strtotime($orderinfo['createdate']);
                    if($order_time >= $today){
                        $day_amount += $orderinfo['amount'];
                        $day_order += 1;
                    }
                }

                if($max_amount_total >= $total_amount &&
                    $max_order_total >= $total_order &&
                    $max_order_day >= $day_order &&
                    $max_amount_day >= $day_amount
                ){

                    $allow_account_list[] = $accountinfo;
                }

            }
            if(! $allow_account_list){
                $this->errorlog("无帐号可用", $domain);
                $this->error("无帐号可以用");
            }
            $account_key = array_rand($allow_account_list);
            $account = $allow_account_list[$account_key];

//            $date = date('Y-m-d H:i:s');
//            $product_info = $this->parseOrderinfo($params);

//            $stripe_order_data = [
//                'stripe_id' => $account['id'],
//                'proxy_order_id' => $proxy_order_id,
//                'createdate' => $date,
//                'status' => 'ing',
//            ];
//            $order_data = $stripe_order_data;
//            $order_data['proxy_order_id'] = $proxy_order_id;
//            $order_data['order_uuid'] = $product_info['order_uuid'];
//            $order_data['ordername'] = $product_info['productname'];
//            $order_data['amount'] = $product_info['total_amount'];
//            $order_data['currency_code'] = $params['currency_code'];
//            $order_data['storename'] = $product_info['storename'];
//            $order_data['proxyemail'] = $params['email'];
//            $order_data['zip'] = $params['zip'];
//            $order_data['country'] = $params['country'];
//            $order_data['city'] = $params['city'];
//            $order_data['address'] = $params['address1'] . " " . $params['address2'];
//            $order_data['username'] = $params['first_name'] . " " . $params['last_name'];
//            $order_data['phone'] = $product_info['phone'];
//            $order_data['create_raw_data'] = json_encode($params);
//            $order_data['updatedate'] = date("Y-m-d H:i:s");

//            Db::table("stripeorder")->insert($stripe_order_data);
            //添加统计代码


//            Db::table("stripeaccount")->where('stripe_id', $account['id'])->inc('fail_count', 1)->update(['updatedate' => date('Y-m-d H:i:s')]);
//            $pp_static = Db::table("stripestatistics")->where('account_id', $account['id'])->find();
//            if($pp_static){
//
//                Db::table("stripeaccount")->where('account_id', $account['id'])->inc('order_qty', 1)->inc("order_total", $product_info['total_amount'])->update();
//            }else{
//
//                Db::table("stripeaccount")->insert([
//                    'account_id' => $account['id'],
//                    'order_qty' => 1,
//                    'order_total' => $product_info['total_amount'],
//                ]);
//            }
            $this->success("ok", [
                "list" => $account,
            ]);

        }


    }
    public function saveOrder(){
        $request = Request::instance();
        $params = $request->param();
        file_put_contents("aaa", var_export($params, true));
        $token = isset($params['token']) ? $params['token'] : "";

        $domain =  isset($params['domain']) ? $params['domain'] : "";
        $intent = isset($params['intent']) ? $params['intent'] : "";
        $intent = str_replace("&quot;", '"', $intent);
        $intent = json_decode($intent, true);
        $stripe_id = isset($params['stripe_id']) ? $params['stripe_id'] : "";

        if(! $token ){
            $this->errorlog("stripe支付回调验证失败");
            $this->error("stripe支付回调验证失败");
        }
        if(! $intent || ! $domain){
            $this->errorlog("stripe支付回调参数错误");
            $this->error("stripe支付回调参数错误");
        }
        $token = str_replace("sk_test_", '', $token);
        $group_stripe =  Db::table("stripe_group")->where('token', $token)->where("status" , "on")->find();
        if(! $group_stripe){
            $this->errorlog("stripe支付回调验证失败#2");
            $this->error("stripe支付回调验证失败#2");
        }



        $order_desc = $intent['charges']['data'][0];

        $address = $order_desc['billing_details']['address']['line1'] .  " " .  $order_desc['billing_details']['address']['line2'];
        $order_data = [];
        $order_data['stripe_order_id'] = $order_desc['id'];
        $order_data['proxy_order_id'] = $order_desc['metadata']['order_id'];
        $order_data['ordername'] = $order_desc['description'];
        $order_data['amount'] = $order_desc['amount'] / 100;
        $order_data['status'] = $intent['status'] == 'succeeded' ? 'plated' : 'cancal' ;
        $order_data['storename'] = $domain;
        $order_data['proxyemail'] = $order_desc['billing_details']['email'];
        $order_data['zip'] = $order_desc['billing_details']['address']['postal_code'];
        $order_data['country'] = $order_desc['billing_details']['address']['country'];
        $order_data['city'] = $order_desc['billing_details']['address']['city'];
        $order_data['address'] = $address;
        $order_data['username'] =  $order_desc['billing_details']['name'];
        $order_data['phone'] = $order_desc['billing_details']['phone'];
        $order_data['createdate'] = date("Y-m-d H:i:s");
        $order_data['stripe_id'] = $stripe_id;
        $res =  Db::table("stripeorder")->insert($order_data);
        if($res){
            if($intent['status'] == 'succeeded'){
                Db::table("stripe")->where('id', $stripe_id)->update(['status' => 'on', 'updatedate' => date('Y-m-d H:i:s')]);
            }else{
                Db::table("stripe")->where('id', $stripe_id)->inc("fail_count", 1)-> update( ['updatedate' => date('Y-m-d H:i:s')]);
            }
            $this->success("ok","");
        }
        $this->error("不存在该订单!");
        $this->errorlog("不存在该订单!");







    }
    private function errorlog($error, $domain_id="", $stripe_id= ""){
        $date = date('Y-m-d H:i:s');
        $request = Request::instance();
        $params = $request->param();
        $save = [
            'createdate' => $date,
            'error' => $error,
            'ip' => $request->ip(),
            'remark' => json_encode($params),
        ];
        $domain_id && $save['domain_id'] = $domain_id;
        Db::table("errorlog")->insert($save);

    }


}
