<?php
namespace app\admin\command;

use think\addons\AddonException;
use think\addons\Service;
use think\Config;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\Db;
use think\Exception;
use think\exception\PDOException;
use app\common\library\Paypal;



use VARIANT;

class PPGetBalance extends Command
{


  /**
     * API Version
     */
    const VERSION = 51.0;

    /**
     * List of valid API environments
     * @var array
     */
    private $allowedEnvs = array(
        'beta-sandbox',
        'live',
        'sandbox'
    );

    /**
     * Config storage from constructor
     * @var array
     */
    private $config = array();

    /**
     * URL storage based on environment
     * @var string
     */
    private $url;

    protected function configure()
    {
      
        $paypal = new Paypal('guotaishangmao_api1.hotmail.com', 'GMZQ7JTKA8XZBECU', 'A3ZvRt6h92yDknX3q4opF9Gbcu.GAqleteObT1zZaOOt3FxrV-s2LDHF');

        // $response = $paypal->call('GetBalance', ['RETURNALLCURRENCIES' => 1]);
        $result= $paypal->call('TransactionSearch', [
                'TRANSACTIONCLASS' => 'RECEIVED',
                'STARTDATE' => '2021-11-01T05:38:48',
                'ENDDATE' => '2022-01-30T05:38:48Z',
                'VERSION' => '49',
        
        
        ]);
        // var_dump($response);die;
      
        // var_dump(count($result)/11);die;
        for($i=0; $i < count($result)/11; $i++){
            if($i >= 40){
                continue;
            }
            $returned_array[$i] = array(
                "timestamp"         =>    urldecode($result["L_TIMESTAMP".$i]),
                "timezone"          =>    urldecode($result["L_TIMEZONE".$i]),
                "type"              =>    urldecode($result["L_TYPE".$i]),
                "email"             =>    urldecode($result["L_EMAIL".$i]),
                "name"              =>    urldecode($result["L_NAME".$i]),
                "transaction_id"    =>    urldecode($result["L_TRANSACTIONID".$i]),
                "status"            =>    urldecode($result["L_STATUS".$i]),
                "amt"               =>    urldecode($result["L_AMT".$i]),
                "currency_code"     =>    urldecode($result["L_CURRENCYCODE".$i]),
                "fee_amount"        =>    urldecode($result["L_FEEAMT".$i]),
                "net_amount"        =>    urldecode($result["L_NETAMT".$i]));
        }
        // var_dump($returned_array);die;

        
    }
    
    


   

}
