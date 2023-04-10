<?php

namespace App\Http\Controllers\Api\Invoice;

use App\Http\Controllers\Controller;
use App\Model\OrderItem;
use Illuminate\Http\Request;
use App\Services\Api\BaiWang\BaiWangService;
use App\Model\Order;
use App\Model\OmsInvoice;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * 获取开票地址/发票地址
     * @param Request $request
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function invoiceQuery(Request $request)
    {
        $orderSn = $request->order_sn;
        $orderList = Order::where('order_sn', $orderSn)->get()->toArray();
        if (empty($orderList)) {
            return $this->error('未找到订单信息');
        }
        $orderRow = $orderList[0];
        if ($orderRow['has_invoice'] === 5) {
            return $this->error('获取开票地址失败，当前订单不符合开票条件');
        }
        if ($orderRow['has_invoice'] === 1) {
            return $this->error('该订单正在开票中，请耐心等待');
        }
        //2为自动开票，3为小程序开票
        if ($orderRow['has_invoice'] === 2 || $orderRow['has_invoice'] === 3) {
            return $this->success('success', ['invoice_url' => $orderRow['invoice_url'] ?: '', 'invoice_path' => $orderRow['invoice_path'] ?: '']);
        }
        //开票地址不为空，查询开票结果，开票未完成返回开票地址
        if (!empty($orderRow['invoice_url'])) {
            DB::beginTransaction();
            try {
                $invoiceInfo = BaiWangService::getInstance()->invoiceQuery($orderRow['order_sn']);
                //更新开票信息
                OmsInvoice::updateOrCreate(
                    ['order_sn' => $orderSn],
                    ['order_sn' => $orderSn, 'pos_id' => $orderRow['pos_id'], 'total_free' => $orderRow['total_amount'], 'title' => $invoiceInfo['title'], 'type' => empty($invoiceInfo['number']) ? 'person' : 'company', 'email' => $invoiceInfo['email'], 'phone' => $invoiceInfo['phone'], 'number' => $invoiceInfo['number']]
                );
                Order::where('id', $orderRow['id'])->update(['has_invoice' => 3, 'invoice_path' => $invoiceInfo['invoicePath'], 'invoice_download_url' => $invoiceInfo['invoiceDownloadUrl']]);
                DB::commit();
                return $this->success('success', ['invoice_url' => $orderRow['invoice_url'] ?: '', 'invoice_path' => $invoiceInfo['invoicePath'] ?: '']);
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->success('success', ['invoice_url' => $orderRow['invoice_url'] ?: '', 'invoice_path' => '']);
            }
        } else {
            try {
                $orderInfo = [];
                $orderInfo['order_sn'] = $orderRow['order_sn'];
                $orderInfo['total_amount'] = $orderRow['total_amount'];
                $orderItems = OrderItem::where('order_main_id', $orderRow['id'])->where(function ($mq) {
                    $mq->where('status', 1)->orWhere('status', 2);
                })->where('is_gift', 0)->where('is_free', 0)->where('order_amount_total', '<>', '0.0000')->get()->toArray();
                if (empty($orderItems)) {
                    Order::where('id', $orderRow['id'])->update(['has_invoice' => 5]);
                    return $this->error('获取开票地址失败，当前订单不符合开票条件');
                }
                $goodsList = [];
                foreach ($orderItems as $orderItem) {
                    if ($orderItem['type'] === 2) {
                        if (!empty($orderItem['collections'])) {
                            $deCollections = json_decode($orderItem['collections'], true);
                            if (!empty($deCollections)) {
                                continue;
                            }
                        }
                    }
                    $goodInfo = [
                        'sku' => $orderItem['sku'],
                        'name' => $orderItem['name'],
//                        'revenue_type' => $orderItem['revenue_type'],
                        'qty' => $orderItem['qty'],
                        'order_amount_total' => $orderItem['order_amount_total']
                    ];
                    $goodsList[] = $goodInfo;
                }
                $orderInfo['goodsList'] = $goodsList;
                //开票
                $invoiceInfo = BaiWangService::getInstance()->invoiceUpload($orderInfo);
                //更新开票信息
                Order::where('id', $orderRow['id'])->update(['invoice_url' => $invoiceInfo['url']]);

                return $this->success('success', ['invoice_url' => $invoiceInfo['url'] ?: '', 'invoice_path' => '']);
            } catch (\Exception $e) {
                return $this->error('获取开票地址失败，请重试');
            }
        }
    }

    /**
     * 更新开票结果
     * @param Request $request
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function invoiceResult(Request $request)
    {
        $orderSn = $request->order_sn;
        $orderList = Order::where('order_sn', $orderSn)->get()->toArray();
        if (empty($orderList)) {
            return $this->error('未找到订单信息');
        }
        $orderRow = $orderList[0];
        if ($orderRow['has_invoice'] !== 0) {
            return $this->success('success');
        }
        //开票地址不为空，查询开票结果，开票未完成返回开票地址
        if (empty($orderRow['invoice_url'])) {
            return $this->success('success');
        }
        try {
            $invoiceInfo = BaiWangService::getInstance()->invoiceQuery($orderRow['order_sn']);
            DB::beginTransaction();
            //更新开票信息
            Order::where('id', $orderRow['id'])->update(['has_invoice' => 3, 'invoice_path' => $invoiceInfo['invoicePath'], 'invoice_download_url' => $invoiceInfo['invoiceDownloadUrl']]);
            OmsInvoice::updateOrCreate(
                ['order_sn' => $orderSn],
                ['order_sn' => $orderSn, 'pos_id' => $orderRow['pos_id'], 'total_free' => $orderRow['total_amount'], 'title' => $invoiceInfo['title'], 'type' => empty($invoiceInfo['number']) ? 'person' : 'company', 'email' => $invoiceInfo['email'], 'phone' => $invoiceInfo['phone'], 'number' => $invoiceInfo['number']]
            );
            DB::commit();
            return $this->success('success', ['invoice_url' => $orderRow['invoice_url'] ?: '', 'invoice_path' => $invoiceInfo['invoicePath'] ?: '']);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->success('success');
        }
    }

    /**
     * 发送发票到邮箱
     * @param Request $request
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendInvoiceEmail(Request $request)
    {
        $orderSn = $request->order_sn;
        $email = $request->email;
        $orderList = Order::where('order_sn', $orderSn)->with(['orderInvoice'])->get()->toArray();
        if (empty($orderList)) {
            return $this->error('未找到订单信息');
        }
        $orderRow = $orderList[0];
        if ($orderRow['has_invoice'] !== 2 && $orderRow['has_invoice'] !== 3) {
            return $this->error('未找到发票信息');
        }
        //无发票信息
        if (empty($orderRow['order_invoice'])) {
            return $this->error('无法发送');
        }
        //发票信息没有邮箱同时也没有提供发送邮箱
        if ((!isset($orderRow['order_invoice']['email']) || empty($orderRow['order_invoice']['email'])) && (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))) {
            return $this->error('无法发送');
        }
        try {
            //版式文件生成
            $formatFileInfo = BaiWangService::getInstance()->formatFileBuild($orderRow['order_sn'], $orderRow['order_invoice']['email'] ?: $email);
            DB::beginTransaction();
            //更新开票信息
            Order::where('id', $orderRow['id'])->update(['invoice_download_url' => $formatFileInfo['invoiceDownloadUrl']]);
            if (!isset($orderRow['order_invoice']['email']) || empty($orderRow['order_invoice']['email'])) {
                OmsInvoice::updateOrCreate(
                    ['order_sn' => $orderSn],
                    ['email' => $email]
                );
            }
            DB::commit();
            return $this->success('发票发送成功，请注意查收');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('发票发送失败，请重试');
        }
    }
}
