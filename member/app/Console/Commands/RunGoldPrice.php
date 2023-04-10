<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\GoldPrice;
use Illuminate\Support\Facades\Redis;

class RunGoldPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:gold-price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Gold Price Pre Day';

    public $domain;

    public $aHeader;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->domain = env('CRM_CUSTOMER_DOMAIN');
        $this->aHeader[] = 'accept: application/json';
        $this->aHeader[] = 'Authorization: ' . $this->getToken();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $todayFormat = date('d/m/Y');
        $result = Redis::get('gold-price-'.$todayFormat);
        if($result) {
            $this->info($todayFormat.'-今日金价已同步！'); 
            return false;
        }
        $url = $this->domain . 'gold-prices/cn';
        $response = http_request($url, false, $this->aHeader, 'GET', '获取每日金价数据：');
        if($response['httpCode'] !== 200){
            $this->info($todayFormat.'-今日金价HttpCode异常！'); 
            return false;
        }
        $result = json_decode($response['data'], true);

        if(!array_key_exists('goldRates', $result)) {
            return false;
        }
        $goldData = $result['goldRates'];
        if(!$goldData){
            return false;
        }
        $data = [];

        $today = date("Y-m-d");
        foreach ($goldData as $key => $goldItem) {
            $regex="'\d{1,2}/\d{1,2}/\d{4}'is";
            preg_match_all($regex,$goldItem['entryDate'],$matches);
            
            if($matches[0][0] !== $todayFormat){
                $this->info($today.'-今日金价还没同步！');
                return false;
            }
            if(!array_key_exists($goldItem['type'], GoldPrice::ENG_TRANSFER_CHN)) {
                continue;
            }
            $insertData = [];
            $insertData['name'] = GoldPrice::ENG_TRANSFER_CHN[$goldItem['type']];
            $insertData['eng_name'] = $goldItem['type'];
            if(strpos($goldItem['type'], 'EXCH')){
                $insertData['type'] = 'EXCH';
            } else {
                $insertData['type'] = 'SELL';
            }

            $insertData['price'] = $goldItem['ptRate'] * 100;
            $insertData['entry_date'] = $goldItem['entryDate'];
            $insertData['created_at'] = $insertData['updated_at'] = date('Y-m-d H:i:s');
            $data[] = $insertData;
        }
       
        $keyData = collect($data)->filter(function ($item){
            return $item['type']  == 'SELL';
        })->map(function ($item) {
            $item['price'] = $item['price'] / 100;
            return $item;
        })->keyBy('eng_name')->toArray();

        $redisData = json_encode(['goldRates' => $keyData]);

        Redis::set('gold-price-today', $redisData);

        Redis::set('gold-price-'.$todayFormat, $redisData);

        $this->info($today.'每日金价数据成功');
        
    }

    // 获取接口token
    public function getToken()
    {
        $tokenData = Redis::get('crm-token');
        if(empty($tokenData)){
            $tokenData = $this->authCrmToken();
        }
        return $tokenData;
    }

    // CRM生成Token
    public function authCrmToken() 
    {
        $url = $this->domain . 'authentication/token';
        $response = http_request($url, false, $this->aHeader, 'GET', '获取crm Token：');
        $result = json_decode($response['data'], true);
        $expiration = strtotime($result['expiration']);
        $tokenExpired = $expiration - time() - 10;

        Redis::setex('crm-token', $tokenExpired, $result['token']);
        return $result['token'];
    }
}
