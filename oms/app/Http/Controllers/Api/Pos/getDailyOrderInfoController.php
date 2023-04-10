<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/5/30
 * Time: 23:54
 */
namespace App\Http\Controllers\Api\Pos;


use App\Http\Controllers\Api\Controller;
use League\Flysystem\Sftp\SftpAdapter;
use League\Flysystem\Filesystem;
use App\Services\Api\Pos\EcToPosFileServices;
use App\Http\Controllers\Api\ApiController;

class getDailyOrderInfoController extends ApiController
{
    private $ref_date;

    public function __construct()
    {
        $this->ref_date = date("Y-m-d",strtotime("-1 day"));
    }

    public function getSftp()
    {
        $adapter = new SftpAdapter([
            'host' => '118.89.40.41',
            'port' => 22,
            'username' => 'root',
            'password' => 'Connext@2019',
            //'privateKey' => 'path/to/or/contents/of/privatekey',
            'root' => '/root',
            'timeout' => 10,
            'directoryPerm' => 0755
        ]);

        $filesystem = new Filesystem($adapter);


        var_export($filesystem);
    }


    /**
     * 订单Sale导出Txt文件给POS
     * @return mixed
     * @throws \Exception
     */
    public function makeOrderInfoToText()
    {
        $ecToPos = new EcToPosFileServices();
        return $ecToPos->makeSalesData();
    }

    /**
     * 会员信息VIP内容导出Txt给POS
     * @return mixed
     * @throws \Exception
     */
    public function makeMemberInfoToText()
    {
        $ecToPos = new EcToPosFileServices();
        return $ecToPos->makeMemberInfoData();
    }

    public function makeReceivingInfoToText()
    {
        $ecToPos = new EcToPosFileServices();
        return $ecToPos->makeReceivingData();

    }

    public function makeReturnInfoToText()
    {
        $ecToPos = new EcToPosFileServices();
        return $ecToPos->makeReturnOrderData();
    }

    public function makeAdjustmentInfoToText()
    {
        $ecToPos = new EcToPosFileServices();
        return $ecToPos->makeAdjustmentData();
    }
}
