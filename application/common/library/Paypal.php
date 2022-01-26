<?php
namespace app\common\library;

use app\admin\model\AuthRule;
use fast\Tree;
use think\addons\Service;
use think\Db;
use think\Exception;
use think\exception\PDOException;

class Paypal
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

    /**
     * Build PayPal API request
     * 
     * @param string $username
     * @param string $password
     * @param string $signature
     * @param string $environment
     */
    public function __construct($username, $password, $signature, $environment = 'live')
    {
        if (!in_array($environment, $this->allowedEnvs)) {
            throw new Exception('Specified environment is not allowed.');
        }
        $this->config = array(
            'username'    => $username,
            'password'    => $password,
            'signature'   => $signature,
            'environment' => $environment
        );
    }

    /**
     * Make a request to the PayPal API
     * 
     * @param  string $method API method (e.g. GetBalance)
     * @param  array  $params Additional fields to send in the request (e.g. array('RETURNALLCURRENCIES' => 1))
     * @return array
     */
    public function call($method, array $params = array())
    {
        $fields = $this->encodeFields(array_merge(
            array(
                'METHOD'    => $method,
                'VERSION'   => self::VERSION,
                'USER'      => $this->config['username'],
                'PWD'       => $this->config['password'],
                'SIGNATURE' => $this->config['signature']
            ),
            $params
        ));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getUrl());
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//lh 本地跳过证书 测试使用 线上关闭
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
       
        if (!$response) {
            throw new Exception('Failed to contact PayPal API: ' . curl_error($ch) . ' (Error No. ' . curl_errno($ch) . ')');
        }
        curl_close($ch);
        //parse_str($response, $result);

        $result = explode("&", $response);

        # Loop through the new array and further bust up each element by the equal sign (=)
        # and then create a new array with the left side of the equal sign as the key and the right side of the equal sign as the value
        $temp = [];
        foreach($result as $value){
            $value = explode("=", $value);
            $temp[$value[0]] = $value[1];
        }
        return $this->decodeFields($temp);
    }

    /**
     * Prepare fields for API
     * 
     * @param  array  $fields
     * @return array
     */
    private function encodeFields(array $fields)
    {
        return array_map('urlencode', $fields);
    }

    /**
     * Make response readable
     * 
     * @param  array  $fields
     * @return array
     */
    private function decodeFields(array $fields)
    {
        return array_map('urldecode', $fields);
    }

    /**
     * Get API url based on environment
     * 
     * @return string
     */
    private function getUrl()
    {
        if (is_null($this->url)) {
            switch ($this->config['environment']) {
                case 'sandbox':
                case 'beta-sandbox':
                    $this->url = "https://api-3t.$environment.paypal.com/nvp";
                    break;
                default:
                    $this->url = 'https://api-3t.paypal.com/nvp';
            }
        }
        return $this->url;
    }
}

