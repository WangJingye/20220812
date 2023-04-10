<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/1/13
 * Time: 10:43
 */

namespace App\Http\Controllers\Api\Search;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use PHPMailer\PHPMailer\Exception;

class ReportController extends Controller
{
    public function reportAnalytics(Request $request)
    {
        $Analytics_date = date("Ymd");
        $Statistics_Redis       = Redis::connection('statistics');
        $openid = $request->get("openid");
        $event = $request->get("event");
        $curPage = $request->get("curPage");
        $data = $request->get("data");
        try{
            $Statistics_Redis->ZINCRBY("Report_".$event . $Analytics_date, 1 ,$openid);
            return $this->success("event ok");

        }
        catch (\Exception $exception){
            return $this->error(0,"faile","event fail");
        }
    }
}