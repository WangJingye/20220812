<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Api\BaiWang\BaiWangService;
use App\Model\Order;
use App\Model\OrderItem;
use App\Tools\Lock;

/**
 * ╔═════════════╦══════════════════════════════════════════
 * ║File Name    ║   AutoInvoice.php
 * ╠═ ═ ═ ═ ═ ═ ═╬═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═
 * ║Class Name   ║   AutoInvoice
 * ╠═ ═ ═ ═ ═ ═ ═╬═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═
 * ║Created Date ║   2020-07-31
 * ╠═ ═ ═ ═ ═ ═ ═╬═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═
 * ║Created By   ║   william.ji@connext.com.cn
 * ╠═ ═ ═ ═ ═ ═ ═╬═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═
 * ║Copy Right   ║   CONNEXT
 * ╠═ ═ ═ ═ ═ ═ ═╬═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═
 * ║Use For      ║   获取token
 * ╚═════════════╩══════════════════════════════════════════
 */
class AutoInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autoInvoice {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     * 获取客户的Token
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $action = $this->argument('action') ?? '';
        if (Lock::processLock('autoInvoice' . $action)) {
            //获取最终状态的订单去开票
            if ($action === 'refund') {
                //部分售后完成的订单，需注意部分退款失败的情况
                $orderRows = Order::where('order_status', '7')->where('order_state', '25')->where('has_invoice', '1')->with(['orderInvoice'])->limit(50)->get()->toArray();
            } elseif ($action === 'normal') {
                //已完成的订单
                $orderRows = Order::where('order_status', '10')->where('has_invoice', '1')->with(['orderInvoice'])->limit(50)->get()->toArray();
            }
//            $orderRows = Order::where('has_invoice', '1')->with(['orderInvoice'])->limit(50)->get()->toArray();
            if (!empty($orderRows)) {
                foreach ($orderRows as $orderRow) {
                    try {
                        $orderInfo = [];
                        $orderInfo['order_sn'] = $orderRow['order_sn'];
                        $orderInfo['type'] = $orderRow['order_invoice']['type'];
                        $orderInfo['title'] = $orderRow['order_invoice']['title'];
                        $orderInfo['number'] = $orderRow['order_invoice']['number'] ?: '';
                        $orderInfo['total_amount'] = $orderRow['total_amount'];
                        $orderItems = OrderItem::where('order_main_id', $orderRow['id'])->where(function ($mq) {
                            $mq->where('status', 1)->orWhere('status', 2);
                        })->where('is_gift', 0)->where('is_free', 0)->where('order_amount_total', '<>', '0.0000')->get()->toArray();
                        if (empty($orderItems)) {
                            Order::where('id', $orderRow['id'])->update(['has_invoice' => 5]);
                            echo '[AutoInvoice]#' . $orderRow['order_sn'] . '#Invoice with no Product#' . date('Y-m-d H:i:s') . "\r\n";
                        } else {
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
//                                'revenue_type' => $orderItem['revenue_type'],
                                    'qty' => $orderItem['qty'],
                                    'order_amount_total' => $orderItem['order_amount_total']
                                ];
                                $goodsList[] = $goodInfo;
                            }
                            $orderInfo['goodsList'] = $goodsList;
                            //开票
                            BaiWangService::getInstance()->invoiceOpen($orderInfo);
                            //版式文件生成
                            $formatFileInfo = BaiWangService::getInstance()->formatFileBuild($orderInfo['order_sn'], $orderRow['order_invoice']['email']);
                            //查询开票信息
                            $invoiceInfo = BaiWangService::getInstance()->invoiceQuery($orderInfo['order_sn']);
                            //更新开票信息
                            Order::where('id', $orderRow['id'])->update(['has_invoice' => 2, 'invoice_path' => $invoiceInfo['invoicePath'], 'invoice_download_url' => $formatFileInfo['invoiceDownloadUrl']]);
                            echo '[AutoInvoice]#' . $orderRow['order_sn'] . '#Success#' . date('Y-m-d H:i:s') . "\r\n";
                        }
                    } catch
                    (\Exception $e) {
                        echo '[AutoInvoice]#' . $orderRow['order_sn'] . '#' . $e->getMessage() . '#' . date('Y-m-d H:i:s') . "\r\n";
                    }
                }
            } else {
                echo '[AutoInvoice]##No Order#' . date('Y-m-d H:i:s') . "\r\n";
            }
        } else {
            echo '[AutoInvoice]##Last Process Still Running#' . date('Y-m-d H:i:s') . "\r\n";
        }
    }
}
