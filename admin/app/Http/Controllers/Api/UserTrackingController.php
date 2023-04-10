<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Http\Helpers\Api\AdminCurlLog;
use Illuminate\Support\Facades\DB;
use App\Model\UserTracking;
use App\Model\WxSmallVisitPage;

class UserTrackingController extends Controller
{
    private $tracking_date;

    public function __construct()
    {
        $this->tracking_date = date("Ymd", strtotime("-1 day"));
    }

    public function DailyUserTracking()
    {
        $ref_date = $this->tracking_date;
        $Statistics_Redis   = Redis::connection('statistics');
        $homePageUV         = $Statistics_Redis->ZCARD("Report_intoHome" . $ref_date);
        $checkoutUV        =  $Statistics_Redis->ZCARD("Report_intoCheckout" . $ref_date);
        $shoppingCartUV     =  $Statistics_Redis->ZCARD("Report_intoShoppingCar" . $ref_date);
        $submitCheckoutUV   = $Statistics_Redis->ZCARD("Report_intoSubmitCheckout" . $ref_date);
        $pdtDetailUV        = $Statistics_Redis->ZCARD("Report_intoPdtDetail" . $ref_date);

        $where = [
            'ref_date' => $ref_date,
        ];

        $data  = [
            'homePageUV'      => $homePageUV,
            'checkoutUV'     => $checkoutUV,
            'shoppingCartUV'  => $shoppingCartUV,
            'submitCheckoutUV' => $submitCheckoutUV,
            'pdtDetailUV'    => $pdtDetailUV
        ];

        $result = UserTracking::updateOrCreate($where, $data);

        //更新到DB成功的话，则删掉7天前的Redis记录
        if($result){
            $del_date = date("Ymd", strtotime("-7 day"));
            $Statistics_Redis->DEL("Report_intoSubmitCheckout" . $del_date);
            $Statistics_Redis->DEL("Report_intoHome" . $del_date);
            $Statistics_Redis->DEL("Report_intoCheckout" . $del_date);
            $Statistics_Redis->DEL("Report_intoShoppingCar" . $del_date);
            $Statistics_Redis->DEL("Report_intoPdtDetail" . $del_date);
        }
    }


    public function makeBounceRate()
    {
            $searchDate = date("Y-m-d" ,strtotime("-1 day"));
            $result= WxSmallVisitPage::where('ref_date', $searchDate)
                ->orderBy('ref_date', 'desc')
                ->get();
            if (!$result->isEmpty()){
                $page_info = $result->toArray();
                foreach ($page_info as $items){
                    //逐个计算跳出率
                    $bounce_rate = number_format($items['exitpage_pv'] / $items['page_visit_pv'] ,4) * 100 . "%";
                    $id = $items['id'];
                    $where = [
                        'id' => $id,
                    ];
                    $data  = [
                        'bounce_rate' => $bounce_rate,
                    ];

                    WxSmallVisitPage::updateOrCreate($where, $data);
                }
            }
        }
}
