<?php


namespace App\Service;

use App\Model\UserBalance;
use App\Model\UserBalanceLog;
use App\Service\Dlc\UsersService;
use Illuminate\Support\Facades\DB;

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
            'created_at' => date('Y-m-d H:i:s'),
            'recharge_amount' => $params['refund_amount'],
        ];
        UserBalanceLog::insert($data);
    }

    public function exportBalanceLog($params)
    {
        $data = [];
        $data[] = [
            '手机号码', '用户姓名', '订单编号', '产品名称', '下单时间', '支付时间',
            '退款时间', '订单状态', '订单金额', '支付类型', '微信支付金额'
        ];
        $query = DB::table('tb_user_balance_log as a')
            ->leftJoin('tb_users AS b', 'b.id', '=', 'a.user_id')
            ->whereRaw('(a.type = 1 or a.type = 4)');
        if (!empty($params['mobile'])) {
            $query->where('b.phone', '=', $params['mobile']);
        }
        if (!empty($params['start_time'])) {
            $query->where('a.created_at', '>=', $params['start_time']);
        }
        if (!empty($params['order_payment_type'])) {
            if ($params['order_payment_type'] != 2) {
                return $data;
            }
        }
        if (!empty($params['end_time'])) {
            $query->where('a.created_at', '<=', $params['end_time']);
        }
        if (isset($params['order_sn']) && $params['order_sn'] !== '') {
            $query->where('a.order_sn', $params['order_sn']);
        }
        if (isset($params['goods_name']) && $params['goods_name'] !== '') {
            $query = $query->whereRaw('a.order_title like \'%' . $params['goods_name'] . '%\'');
        }
        $list = $query->select(['a.*', 'b.phone', 'b.first_name', 'b.last_name'])
            ->orderBy('a.id', 'desc')
            ->groupBy(['a.id'])
            ->get()->toArray();
        foreach ($list as $v) {
            $v = json_decode(json_encode($v), true);
            $data[] = [
                "\t" . $v['phone'],
                $v['first_name'] . $v['last_name'],
                "\t" . $v['order_sn'],
                $v['order_title'],
                $v['created_at'],
                $v['created_at'],
                $v['type'] == 1 ? '' : $v['created_at'],
                $v['type'] == 1 ? '支付' : '退款',
                $v['balance'],
                '微信',
                $v['type'] == 4 ? -$v['recharge_amount'] : $v['recharge_amount'],
            ];
        }
        return $data;
    }
}