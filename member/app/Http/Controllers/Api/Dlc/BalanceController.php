<?php namespace App\Http\Controllers\Api\Dlc;

use App\Model\UserBalance;
use App\Model\UserBalanceLog;
use App\Service\BalanceService;
use App\Service\Dlc\UsersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class BalanceController extends ApiController
{
    private $balanceService;

    public function __construct()
    {
        $this->balanceService = new BalanceService();
    }

    public function applyRefund(Request $request)
    {
        try {
            $params = $request->all();
            $balanceInfo = UserBalance::query()->where('order_sn', $params['order_sn'])->first();
            if ($balanceInfo['status'] == 4) {
                throw new \Exception('退款已申请，请勿重复提交');
            }
            if (!in_array($balanceInfo['status'], [1, 2])) {
                throw new \Exception('当前记录有误');
            }
            $balanceInfo['status'] = 4;
            $balanceInfo['content'] = $params['content'];
            $balanceInfo->save();
            return $this->success('退款申请已提交');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function getBalanceInfo(Request $request)
    {
        $params = $request->all();
        if (!isset($params['id'])) {
            throw new \Exception('参数有误');
        }
        $userBalance = UserBalance::query()->where('id', $params['id'])->first();
        return $this->success('success', $userBalance);
    }

    public function getBalanceList(Request $request)
    {
        try {
            $params = $request->all();
            $limit = $params['limit'] ?? 5;
            $lastId = $params['lastId'] ?? 0;
            if (!isset($params['status'])) {
                throw new \Exception('参数有误');
            }
            $userId = $this->getUid();
            $query = UserBalance::query()
                ->where('user_id', $userId)
                ->where('order_type', 1);

            if ($params['status'] == 0) {
                $query = $query->where(function ($q1) {
                    $q1->where('status', '0')
                        ->orWhere('status', '3');
                });
            } else if ($params['status'] == 1) {
                $query = $query->where(function ($q1) {
                    $q1->where('status', '1')
                        ->orWhere('status', '4');
                });
            } else {
                $query = $query->where('status', $params['status']);
            }

            if ($lastId != 0) {
                $query = $query->where('id', '<', $lastId);
            }
            $list = $query->orderBy('id', 'desc')
                ->limit($limit)
                ->get()->toArray();
            return $this->success('success', $list);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function getUserBalanceList(Request $request)
    {
        $params = $request->all();
        $query = DB::table('tb_user_balance AS a')
            ->leftJoin('tb_users AS b', 'a.user_id', '=', 'b.id')
            ->select(['a.*', 'b.first_name', 'b.last_name', 'b.nickname', 'b.phone']);

        if (isset($params['status']) && $params['status'] !== '') {
            $query = $query->where('a.status', $params['status']);
        }

        if (isset($params['order_sn']) && $params['order_sn'] !== '') {
            $query = $query->where('a.order_sn', $params['order_sn']);
        }

        if (isset($params['gold_name']) && $params['gold_name'] !== '') {
            $query = $query->where('a.gold_name', $params['gold_name']);
        }

        if (isset($params['is_invoice']) && $params['is_invoice'] !== '') {
            $query = $query->where('a.is_invoice', $params['is_invoice']);
        }

        if (!empty($params['start_time'])) {
            $query = $query->where('a.created_at', '>=', $params['start_time']);
        }
        if (!empty($params['end_time'])) {
            $query = $query->where('a.created_at', '>=', $params['end_time']);
        }
        if (isset($params['phone']) && $params['phone'] !== '') {
            $query = $query->where('b.phone', $params['phone']);
        }
        if (isset($params['nickname']) && $params['nickname'] !== '') {
            $query = $query->where('b.nickname', 'like', '%' . $params['nickname'] . '%');
        }
        $data = $query->orderBy('a.id', 'desc')->paginate($params['limit'] ?? 10)->toArray();
        $res = [
            'pageData' => $data['data'],
            'count' => $data['total']
        ];
        return json_encode($res);
    }

    public function refundBalanceCard(Request $request)
    {
        try {
            $params = $request->all();
            if (!isset($params['id'])) {
                throw new \Exception('参数有误');
            }
            $this->balanceService->refundBalanceCard($params);
            return $this->success();
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    public function refundBalance(Request $request)
    {
        $params = $request->all();
        $userInfo = UsersService::getUserInfo($params['user_id']);
        $balance = $params['balance'];
        DB::beginTransaction();
        try {
            $userInfo['balance'] += $balance;
            $userInfo->save();
            $list = UserBalance::query()
                ->whereRaw('status = 2')
                ->whereRaw('used_amount != 0')
                ->where('user_id', $params['user_id'])
                ->orderBy('id', 'desc')
                ->get();
            foreach ($list as $item) {
                if ($balance < $item['used_amount']) {
                    $item['used_amount'] -= $balance;
                    $balance = 0;
                } else {
                    $balance = $balance - $item['used_amount'];
                    $item['used_amount'] = 0;
                    $item['status'] = 1;
                }
                $item->save();
                if ($balance == 0) {
                    break;
                }
            }
            $data = [
                'user_id' => $params['user_id'],
                'balance' => $params['balance'],
                'type' => 3,
                'order_sn' => $params['order_sn'],
                'order_title' => $params['order_title'],
                'remain_balance' => $userInfo['balance'],
                'created_at' => date('Y-m-d H:i:s'),
            ];
            UserBalanceLog::insert($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
        return $this->success();
    }

    public function useBalance(Request $request)
    {
        $params = $request->all();
        $userInfo = UsersService::getUserInfo($params['user_id']);
        $balance = $params['balance'];
        DB::beginTransaction();
        try {
            $userInfo['balance'] -= $balance;
            if ($userInfo['balance'] < 0) {
                return $this->error('储值余额不足', ['balance' => $userInfo['balance']]);
            }
            $userInfo->save();
            $list = UserBalance::query()
                ->whereRaw('(status = 1 or status = 2)')
                ->whereRaw('amount != used_amount')
                ->where('user_id', $params['user_id'])
                ->get();
            foreach ($list as $item) {
                $need = $item['amount'] - $item['used_amount'];
                if ($balance < $need) {
                    $item['used_amount'] += $balance;
                    $balance = 0;
                } else {
                    $item['used_amount'] += $need;
                    $balance = $balance - $need;
                }
                $item['status'] = 2;
                $item->save();
                if ($balance == 0) {
                    break;
                }
            }
            $data = [
                'user_id' => $params['user_id'],
                'balance' => $params['balance'],
                'type' => 2,
                'order_sn' => $params['order_sn'],
                'order_title' => $params['order_title'],
                'remain_balance' => $userInfo['balance'],
                'created_at' => date('Y-m-d H:i:s'),
            ];
            UserBalanceLog::insert($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
        return $this->success();
    }

    public function addBalance(Request $request)
    {
        try {
            $params = $request->all();
            DB::beginTransaction();
            $userInfo = UsersService::getUserInfo($params['user_id']);
            $goldInfo = $this->balanceService->sendRequest('getGoldInfo', ['id' => $params['gold_id']]);
            $this->balanceService->addBalance($params, $goldInfo, $userInfo);
            DB::commit();
            return $this->success();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }

    public function cancelInvoice(Request $request)
    {
        try {
            $params = $request->all();
            UserBalance::query()->where('id', $params['id'])->update([
                'is_invoice' => 0,
                'invoice' => ''
            ]);
            return $this->success([], '发票申请已取消');
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }


    /**
     * 申请开票
     */
    public function applyInvoice(Request $request)
    {
        try {
            $params = $request->all();
            $balanceInfo = UserBalance::query()->where('id', $params['id'])->first();
            if ($balanceInfo['status'] != 2) {
                return $this->error([], '只有已使用的储值卡才能申请开票');
            }
            if ($balanceInfo['is_invoice'] != 0) {
                return $this->error([], '当前储值卡已申请开票，不能重复申请');
            }
            $invoice = [
                'type' => $params['type'],
                'title' => $params['invoiceName'] ?? '',
                'code' => $params['code'] ?? '',
                'email' => $params['email'] ?? '',
            ];
            UserBalance::query()->where('id', $params['id'])->update([
                'is_invoice' => 1,
                'invoice' => json_encode($invoice)
            ]);
            return $this->success([], '发票申请已提交，请联系客服开票吧');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    /**
     * 确认开票
     * @param Request $request
     * @return mixed
     */
    public function invoice(Request $request)
    {
        try {
            $params = $request->all();
            UserBalance::query()->where('id', $params['id'])->update(['is_invoice' => 2]);
            return $this->success('开票完成');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 用户储值卡记录
     * @param Request $request
     * @return array
     * @throws ApiPlaintextException
     */
    public function getBalanceLogs(Request $request)
    {
        $params = $request->all();
        $query = DB::table('tb_user_balance_log AS a')
            ->leftJoin('tb_users AS b', 'a.user_id', '=', 'b.id')
            ->select(['a.*', 'b.phone', 'b.nickname', 'b.first_name', 'b.last_name']);
        if (isset($params['order_sn']) && $params['order_sn'] !== '') {
            $query = $query->where('a.order_sn', $params['order_sn']);
        }
        if (!empty($params['type'])) {
            $query = $query->where('a.type', $params['type']);
        }
        if (isset($params['mobile']) && $params['mobile'] !== '') {
            $query = $query->where('b.phone', $params['mobile']);
        }
        if (isset($params['nickname']) && $params['nickname'] !== '') {
            $query = $query->where('b.nickname', $params['nickname']);
        }
        if (!empty($params['start_time'])) {
            $query = $query->where('a.created_at', '>=', $params['start_time']);
        }
        if (!empty($params['end_time'])) {
            $query = $query->where('a.created_at', '<', $params['end_time']);
        }
        $query = $query->orderBy('a.id', 'desc');

        if (isset($params['isPage']) && $params['isPage'] === 0) {
            return $query->get()->toArray();
        } else {
            $list = $query->paginate($params['limit'] ?? 10)->toArray();
            $res = [
                'pageData' => $list['data'],
                'count' => $list['total'],
            ];
            return json_encode($res);
        }
    }

    public function exportBalanceLogs(Request $request)
    {
        $request->merge(['isPage' => 0]);
        $list = $this->getBalanceLogs($request);
        $typeList = [1 => '充值', 2 => '消费', 3 => '订单退款', 4 => '充值退款'];
        $data[] = [
            '手机号码', '用户姓名', '用户昵称', '订单编号', '产品名称', '下单时间', '支付时间', '退款时间', '订单状态',
            '订单类型', '储值卡充值金额', '翻倍系数', '翻倍后储值卡金额', '储值卡消耗金额', '变动时间', '储值账户余额',
        ];
        foreach ($list as $v) {
            $v = json_decode(json_encode($v), true);
            $item = [
                "\t" . $v['phone'],
                $v['first_name'] . $v['last_name'],
                $v['nickname'],
                "\t" . $v['order_sn'],
                $v['order_title'],
            ];
            if ($v['type'] == 2 || $v['type'] == 3) {
                $orderInfo = $this->balanceService->sendRequest('getOrderInfo', ['order_sn' => $v['order_sn']]);
                $item[] = $orderInfo['created_at'];
                $item[] = $orderInfo['payment_at'];
                $item[] = $orderInfo['return_pay_at'];
                $item[] = $orderInfo['status_name'];
                $item[] = $typeList[$v['type']];
                $item[] = '';
                $item[] = '';
                $item[] = '';
                $item[] = $v['balance'];
            } else {
                $item[] = $v['created_at'];
                $item[] = $v['created_at'];
                $item[] = '';
                $item[] = '';
                $item[] = $typeList[$v['type']];
                if ($v['type'] == 1) {
                    $item[] = $v['recharge_amount'];
                    $item[] = round($v['balance'] / $v['recharge_amount'], 2);
                    $item[] = $v['balance'];
                    $item[] = '';
                } else {
                    $item[] = '';
                    $item[] = '';
                    $item[] = '';
                    $item[] = $v['balance'];
                }
            }
            $item[] = $v['created_at'];
            $item[] = $v['remain_balance'];
            $data[] = $item;
        }
        return json_encode(['data' => $data]);
    }


    private function getOrderInfo($orderSn)
    {
        $result = $this->http->curl('getOrderInfo', ['order_sn' => $orderSn]);
        if (!isset($result['code']) || $result['code'] != 1) {
            throw new \Exception($result['message']);
        }
        return $result['data'];
    }

    public function exportBalanceLog(Request $request)
    {
        try {
            return $this->success('success', $this->balanceService->exportBalanceLog($request->all()));
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function recharge(Request $request)
    {
        try {
            $this->balanceService->recharge($request->all());
            return $this->success('充值成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}