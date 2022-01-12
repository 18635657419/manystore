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
				if($accountinfo['account_type'] == 'T2'){
					$allow_account_list[] = $accountinfo;
					continue;
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
				if($this->t7_max_amount_total > $total_amount &&
						$this->t7_max_order_total > $total_order &&
						$this->t7_max_order_day > $day_order &&
						$this->t7_max_amount_day > $day_amount
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
			$date = date('Y-m-d H:i:s');
			$stripeorder_data = [
				'stripe_id' => $account['id'],
				'domain' => $domain,
				'proxy_order_id' => $proxy_order_id,
				'createdate' => $date,
				'status' => 'ing',
			];
			Db::table("stripeorder")->insert($stripeorder_data);

		}
		$this->success("ok", [
				"list" => $account,
				]);

	}
	public function saveOrder(){
		  $request = Request::instance();
		  $params = $request->param();
		  $token = isset($params['token']) ? $params['token'] : "";
		  $orderid = isset($params['order_id']) ? $params['order_id'] : "";
		  $domain =  isset($params['domain']) ? $params['domain'] : "";
		  if(! $token ){
			  $this->errorlog("stripe支付回调验证失败");
			  $this->error("stripe支付回调验证失败");
		  }
		  if(! $orderid || ! $domain){
			  $this->errorlog("stripe支付回调参数错误");
			  $this->error("stripe支付回调参数错误");
		  }
		  $group_stripe =  Db::table("stripe_group")->where('token', $token)->where("status" , "on")->find();
		  if(! $group_stripe){
			  $this->errorlog("stripe支付回调验证失败#2");
			  $this->error("stripe支付回调验证失败#2");
			}
		  $order_data = [];
		  $order_data['proxy_order_id'] = $orderid;
		  $order_data['ordername'] = $params['productname'];
		  $order_data['amount'] = $params['total'];
		  $order_data['status'] = "plated";
		  $order_data['storename'] = $params['store_name'];
		  $order_data['proxyemail'] = $params['email'];
		  $order_data['zip'] = $params['payment_postcode'];
		  $order_data['country'] = $params['payment_country'];
		  $order_data['city'] = $params['payment_city'];
		  $order_data['address'] = $params['payment_address_1'] . " " . $params['payment_address_2'];
		  $order_data['username'] = $params['firstname'] . " " . $params['lastname'] ;
		  $order_data['phone'] = $params['telephone'];
		  $order_data['updatedate'] = date("Y-m-d H:i:s");
		  $res = Db::table("stripeorder")->where("domain", $domain)->where("proxy_order_id", $orderid)->update($order_data);
		  if($res){
			  $this->success("ok","");
		  }
		  $this->error("不存在该订单!");
		  $this->errorlog("不存在该订单!");
			  
			  
			  

		
		
		
	}
	private function errorlog($error, $domain_id="", $pp_id= ""){
		$date = date('Y-m-d H:i:s');
		$request = Request::instance();
		$params = $request->param();
		$save = [
		 'createdate' => $date,
		 'error' => $error,
		 'ip' => $request->ip(),
		];
		$domain_id && $save['domain_id'] = $domain_id;
		Db::table("errorlog")->insert($save);
		
	}
}
