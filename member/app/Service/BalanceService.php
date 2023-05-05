<?php


namespace App\Service;

use App\Model\UserBalance;
use App\Model\UserBalanceLog;
use App\Model\Users;
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
                $v['phone'],
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

    public function recharge($params)
    {
        $userInfo = Users::query()->where('phone', $params['mobile'])->first();
        $gold = [
            'gold_name' => $params['gold_name'],
            'price' => $params['price'],
            'rate' => $params['price'] > 0 ? $params['face_value'] / $params['price'] : 1,
            'face_value' => $params['face_value'],
            'valid_time' => $params['valid_time'],
            'gold_type' => 2
        ];
        $goldInfo = $this->sendRequest('addGold', $gold);
        $data = [
            'user_id' => $userInfo['id'],
            'gold_id' => $goldInfo['id'],
            'order_sn' => $this->createOrderNo(),
            'pay_amount' => $goldInfo['price'],
            'pay_method' => 12,
            'order_title' => $params['gold_name'],
            'order_type' => 2
        ];
        $this->addBalance($data, $goldInfo, $userInfo);
    }

    public function addBalance($params, $goldInfo, $userInfo)
    {
        $time = time();
        $data = [
            'user_id' => $userInfo['id'],
            'gold_id' => $params['gold_id'],
            'order_sn' => $params['order_sn'],
            'order_type' => $params['order_type'] ?? 1,
            'gold_name' => $goldInfo['gold_name'],
            'pay_amount' => $params['pay_amount'],
            'amount' => $goldInfo['face_value'],
            'start_time' => date('Y-m-d 00:00:00', $time),
            'end_time' => date('Y-m-d 23:59:59', strtotime('+' . $goldInfo['valid_time'] . ' year', $time - 23 * 3600)),
            'created_at' => date('Y-m-d H:i:s', $time),
            'updated_at' => date('Y-m-d H:i:s', $time),
            'pay_method' => $params['pay_method'],
        ];
        UserBalance::insert($data);
        $userInfo['balance'] += $goldInfo['face_value'];
        $userInfo->save();
        $data = [
            'user_id' => $params['user_id'],
            'balance' => $goldInfo['face_value'],
            'type' => 1,
            'order_sn' => $params['order_sn'],
            'order_title' => $params['order_title'],
            'order_type' => $data['order_type'],
            'remain_balance' => $userInfo['balance'],
            'created_at' => date('Y-m-d H:i:s', $time),
            'recharge_amount' => $params['pay_amount']
        ];
        UserBalanceLog::insert($data);
    }

    public function sendRequest($method, $data)
    {
        $api = app('ApiRequestInner');
        $info = $api->request($method, 'POST', $data);
        if (isset($info['code']) && $info['code'] == 1) {
            return $info['data'];
        } else {
            throw new \Exception($info['message'] ?? '');
        }
    }

    public function createOrderNo($channel_id = 1, $is_test = 2)
    {
        return date('ymdHis') . $is_test . $channel_id . str_pad(rand(0, 999999), 6, STR_PAD_LEFT);
    }

}