<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/9/4
 * Time: 14:54
 */

namespace App\Http\Controllers\Api\Pos;


use App\Http\Controllers\Api\ApiController;
use App\Model\QQMailer;
use Illuminate\Support\Facades\Redis;

class sendMailController extends ApiController
{
    public function newMail(){
        $redis = Redis::connection();
        $forward_order_id_name = 'deliveryOrder'.date("Ymd", strtotime("-1 day"));
        $after_order_id_name = 'afterOrderReviced'.date("Ymd", strtotime("-1 day"));;
        $forward_order_count = $redis->scard($forward_order_id_name);
        $after_order_count = $redis->scard($after_order_id_name);
        
        // 实例化 QQMailer
        $mailer = new QQMailer(true);
        // 添加附件
        foreach(glob("/opt/ecToPos/*.txt") as  $txt)
        {
            $file = basename($txt);
            $mailer->addFile( "/opt/ecToPos/" . $file);
        }
        $file_excel = "/opt/ecToPos/" . date("Ymd") .".xlsx";
        $mailer->addFile( $file_excel);
        // 邮件标题
        $title = date("Y年m月d日") . ' Sales文件同步';
        // 邮件内容
        $data = date("Y年m月d日");
        $content =
<<< EOF
        <p align="left">
        Dear All:
        </p>
        <p align="left">
        &nbsp;&nbsp;&nbsp;&nbsp;附件是{$data}生成的Sales及Vip文件，仅供参考~,
                昨天发货{$forward_order_count}单，
                昨天退货{$after_order_count}单，可供参考~
        </p>
EOF;
        // 发送QQ邮件
        try{
            $mailer->send('pinko.ge@connext.com.cn', $title, $content);
        }
        catch (\Exception $exception){
            echo $exception;
        }
    }

    public function WmsFileEmail(){
        $redis = Redis::connection();
        $redis_set_name = "wms_new_file_" . date("Ymd");
        $new_file_list = $redis->smembers($redis_set_name);
        $oss_file_name = '';
        foreach ($new_file_list as $new_file_name){
            $oss_file_name .= $new_file_name;
            $oss_file_name .= PHP_EOL;
        }
        $title = date("Y年m月d日") . ' WMS上传了新的Rec文件';
        // 邮件内容
        $data = date("Y年m月d日 H时i分s秒");
        $content =
            <<< EOF
        <p align="left">
        Dear Jiang Yan:
        </p>
        <p align="left">
        &nbsp;&nbsp;&nbsp;&nbsp;{$data}WMS上传了新的Rec文件到OSS啦，文件名是\n{$oss_file_name}请及时处理~
        </p>
EOF;
        $mailer = new QQMailer(true);
        // 发送QQ邮件
        try{
            $mailer->wmsEmailSend('Peien.Wang@connext.com.cn', $title, $content);
        }
        catch (\Exception $exception){
            echo $exception;
        }
    }
}