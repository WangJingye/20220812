<?php


namespace App\Service;

use App\Model\UserBalance;
use App\Model\UserBalanceLog;
use App\Service\Dlc\UsersService;

class BalanceService
{
    public function refundBalanceCard($params)
    {
        $userBalance = UserBalance::query()->where('id', $params['id'])->first();
        $userBalance['status'] = 3;
        $userBalance['refund_time'] = date('Y-m-d H:i:s');
        //申请开票的购物卡取消申请开票状态
        if ($userBalance['is_invoice'] == 1) {
            $userBalance['is_invoice'] = 0;
        }
        $userBalance->save();
        $userInfo = UsersService::getUserInfo($userBalance['user_id']);
        $balance = $userBalance['amount'] - $userBalance['used_amount'];
        $userInfo['balance'] -= $balance;
        if ($userInfo['balance'] < 0) {
            $userInfo['balance'] = 0;
        }
        $userInfo->save();

        $data = [
            'user_id' => $userInfo['id'],
            'balance' => $balance,
            'type' => 4,
            'order_sn' => $params['order_sn'],
            'order_title' => $params['order_title'],
            'remain_balance' => $userInfo['balance'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        UserBalanceLog::insert($data);
    }
}