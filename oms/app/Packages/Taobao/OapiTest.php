<?php
include "TopSdk.php";
date_default_timezone_set('Asia/Shanghai');
$request = new OrderProcessReportRequest;
$order = new Order;
$order->order_sn="D1234";
$order->order_id="W1234";
$order->order_type="JYCK";
$order->warehouse_code="W1234";
$order->remark="备注";
$order->warehouse_name="奇门仓储字段,说明,string(50),,";
$process = new Process;
$process->process_status="ACCEPT";
$process->operator_code="O1234";
$process->operator_name="老王";
$process->operate_time="2016-09-09 12:00:00";
$process->operate_info="处理中";
$process->remark="备注信息";
$request->order=$order;
$request->process = $process;
$request->Extend_props="";
//$request->remark="备注";
echo json_encode($request);die;
//$req->setRequest(json_encode($request));

//$c = new DingTalkClient(DingTalkConstant::$CALL_TYPE_OAPI, DingTalkConstant::$METHOD_POST , DingTalkConstant::$FORMAT_JSON);
//$req = new OapiMediaUploadRequest;
//$req->setType("image");
//$req->setMedia(array('type'=>'application/octet-stream','filename'=>'image.png', 'content' => file_get_contents('/Users/test/image.png')));
//$resp=$c->execute($req, "******","https://oapi.dingtalk.com/media/upload");
//$q = new DeliveryorderConfirmRequest();
$c = new TopClient;
$c->appkey = '11';
$c->secretKey = $secret;
//taobao.qimen.deliveryorder
//$req = new QimenDeliveryorderConfirmRequest;
$request = new DeliveryOrderConfirmRequest;
$deliveryOrder = new DeliveryOrder;
//$deliveryOrder->deliveryOrderCode="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->order_flag="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->source_order_code="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->source_platform_code="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->source_platform_name="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->create_time="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->place_order_time="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->pay_time="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->pay_no="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->pay_method="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->seller_id="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->seller_nick="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->shop_nick="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->buyer_nick="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->total_amount="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->item_amount="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->discount_amount="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->freight="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->ar_amount="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->got_amount="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->service_fee="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->logistics_code="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->logistics_name="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->express_code="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->logistics_area_code="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->is_urgency="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->invoice_flag="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->insurance_flag="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->buyer_message="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->seller_message="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->receive_order_time="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->is_cod="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->is_value_declared="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->declared_amount="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->delivery_note="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->sales_model="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->transpost_sum="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->business_memo="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->actual_amount="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->is_payment_collected="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->collected_amount="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->merge_order_flag="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->merge_order_codes="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->buyer_name="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->buyer_phone="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->fetch_item_location="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->priority_code="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->plan_delivery_date="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->plan_arrival_time="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->min_arrival_time="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->max_arrival_time="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->presale_order_type="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->warehouse_address_code="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->personal_package_note="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->personal_order_note="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->item_code="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->item_name="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->quantity="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->price="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->order_note="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->line_number="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->batch_code="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->produce_date="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->shelf_life="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->supplier_code="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->supplier_name="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->pack_code="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->uom_code="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->no_stack_tag="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->exception_code="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->pre_delivery_order_code="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->pre_delivery_order_id="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->schedule_date="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->transport_mode="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->remark="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->total_order_lines="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->order_source_code="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->modified_time="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->order_status="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->identify_code="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->shop_code="奇门仓储字段,说明,string(50),,";
//$deliveryOrder->service_code="奇门仓储字段,说明,string(50),,";
$deliveryOrder->delivery_order_code="T1234";
$deliveryOrder->delivery_order_id="C1234";
$deliveryOrder->warehouse_code="W1234";
$deliveryOrder->order_type="JYCK";
$deliveryOrder->order_sn="订单编号";
$deliveryOrder->status="NEW";
$deliveryOrder->out_biz_code="WB1234";
$deliveryOrder->confirm_type="0";
$deliveryOrder->order_confirm_time="2016-09-08 12:00:00";
$deliveryOrder->operator_code="O23";
$deliveryOrder->operator_name="老王";
$deliveryOrder->operate_time="2016-09-09 12:00:00";
$deliveryOrder->order_goods_info=[];
//$request->DeliveryOrder=$deliveryOrder;

//echo 'deliveryOrder:'.json_encode($deliveryOrder);
$invoices = new Invoice;
$invoices->type="奇门仓储字段,说明,string(50),,";
$invoices->remark="备注";
$invoices->header="XXX公司";
$invoices->amount="12.0";
$invoices->content="XXX公司报销XX元";
//echo 'invoices:'.json_encode($s);
$detail = new Detail;
$items = new Item;
$items->owner_code="奇门仓储字段,说明,string(50),,";
$items->title="奇门仓储字段,说明,string(50),,";
$items->english_name="奇门仓储字段,说明,string(50),,";
$items->short_name="奇门仓储字段,说明,string(50),,";
//$items->category_id="奇门仓储字段,说明,string(50),,";
//$items->category_name="奇门仓储字段,说明,string(50),,";
//$items->sku_property="奇门仓储字段,说明,string(50),,";
//$items->item_type="奇门仓储字段,说明,string(50),,";
//$items->tag_price="奇门仓储字段,说明,string(50),,";
//$items->retail_price="奇门仓储字段,说明,string(50),,";
//$items->cost_price="奇门仓储字段,说明,string(50),,";
//$items->purchase_price="奇门仓储字段,说明,string(50),,";
//$items->supplier_code="奇门仓储字段,说明,string(50),,";
//$items->supplier_name="奇门仓储字段,说明,string(50),,";
//$items->season_code="奇门仓储字段,说明,string(50),,";
//$items->season_name="奇门仓储字段,说明,string(50),,";
//$items->brand_code="奇门仓储字段,说明,string(50),,";
//$items->brand_name="奇门仓储字段,说明,string(50),,";
//$items->sn="奇门仓储字段,说明,string(50),,";
//$items->is_s_n_mgmt="奇门仓储字段,说明,string(50),,";
//$items->bar_code="奇门仓储字段,说明,string(50),,";
//$items->color="奇门仓储字段,说明,string(50),,";
//$items->size="奇门仓储字段,说明,string(50),,";
//$items->length="奇门仓储字段,说明,string(50),,";
//$items->width="奇门仓储字段,说明,string(50),,";
//$items->height="奇门仓储字段,说明,string(50),,";
//$items->volume="奇门仓储字段,说明,string(50),,";
//$items->gross_weight="奇门仓储字段,说明,string(50),,";
//$items->net_weight="奇门仓储字段,说明,string(50),,";
//$items->tare_weight="奇门仓储字段,说明,string(50),,";
//$items->safety_stock="奇门仓储字段,说明,string(50),,";
//$items->stock_unit="奇门仓储字段,说明,string(50),,";
//$items->stock_status="奇门仓储字段,说明,string(50),,";
//$items->product_date="奇门仓储字段,说明,string(50),,";
//$items->expire_date="奇门仓储字段,说明,string(50),,";
//$items->is_shelf_life_mgmt="奇门仓储字段,说明,string(50),,";
//$items->shelf_life="奇门仓储字段,说明,string(50),,";
//$items->reject_lifecycle="奇门仓储字段,说明,string(50),,";
//$items->lockup_lifecycle="奇门仓储字段,说明,string(50),,";
//$items->advent_lifecycle="奇门仓储字段,说明,string(50),,";
//$items->batch_code="奇门仓储字段,说明,string(50),,";
//$items->batch_remark="奇门仓储字段,说明,string(50),,";
//$items->is_batch_mgmt="奇门仓储字段,说明,string(50),,";
//$items->pack_code="奇门仓储字段,说明,string(50),,";
//$items->pcs="奇门仓储字段,说明,string(50),,";
//$items->origin_address="奇门仓储字段,说明,string(50),,";
//$items->approval_number="奇门仓储字段,说明,string(50),,";
//$items->is_fragile="奇门仓储字段,说明,string(50),,";
//$items->is_hazardous="奇门仓储字段,说明,string(50),,";
//$items->pricing_category="奇门仓储字段,说明,string(50),,";
//$items->is_sku="奇门仓储字段,说明,string(50),,";
//$items->package_material="奇门仓储字段,说明,string(50),,";
//$items->is_area_sale="奇门仓储字段,说明,string(50),,";
//$items->normal_qty="奇门仓储字段,说明,string(50),,";
//$items->defective_qty="奇门仓储字段,说明,string(50),,";
//$items->receive_qty="奇门仓储字段,说明,string(50),,";
$items->ex_code="奇门仓储字段,说明,string(50),,";
$items->discount_price="奇门仓储字段,说明,string(50),,";
//$items->inventory_type="奇门仓储字段,说明,string(50),,";
//$items->plan_qty="奇门仓储字段,说明,string(50),,";
//$items->source_order_code="奇门仓储字段,说明,string(50),,";
//$items->sub_source_order_code="奇门仓储字段,说明,string(50),,";
//$items->produce_code="奇门仓储字段,说明,string(50),,";
//$items->order_line_no="奇门仓储字段,说明,string(50),,";
//$items->actual_qty="奇门仓储字段,说明,string(50),,";
//$items->warehouse_code="奇门仓储字段,说明,string(50),,";
//$items->lock_quantity="奇门仓储字段,说明,string(50),,";
//$items->order_code="奇门仓储字段,说明,string(50),,";
//$items->order_type="奇门仓储字段,说明,string(50),,";
//$items->out_biz_code="奇门仓储字段,说明,string(50),,";
//$items->product_code="奇门仓储字段,说明,string(50),,";
//$items->paper_qty="奇门仓储字段,说明,string(50),,";
//$items->diff_quantity="奇门仓储字段,说明,string(50),,";
//$items->ext_code="奇门仓储字段,说明,string(50),,";
//$items->lack_qty="奇门仓储字段,说明,string(50),,";
//$items->reason="奇门仓储字段,说明,string(50),,";
//$items->sn_code="奇门仓储字段,说明,string(50),,";
//$items->goods_code="奇门仓储字段,说明,string(50),,";
//$items->standard_price="奇门仓储字段,说明,string(50),,";
//$items->reference_price="奇门仓储字段,说明,string(50),,";
//$items->discount="奇门仓储字段,说明,string(50),,";
//$items->actual_amount="奇门仓储字段,说明,string(50),,";
//$items->latest_update_time="奇门仓储字段,说明,string(50),,";
//$items->change_time="奇门仓储字段,说明,string(50),,";
//$items->temp_requirement="奇门仓储字段,说明,string(50),,";
//$items->channel_code="奇门仓储字段,说明,string(50),,";
//$items->origin_code="奇门仓储字段,说明,string(50),,";
$items->remark="备注";
$items->item_name="淘公仔";
$items->unit="个";
$items->price="12.0";
$items->quantity="12";
$items->amount="12.0";
$items->item_code="1234";
$items->item_id="1234";

$batchs = new Batch;
$batchs->batch_code="奇门仓储字段,说明,string(50),,";
$batchs->product_date="奇门仓储字段,说明,string(50),,";
$batchs->expire_date="奇门仓储字段,说明,string(50),,";
$batchs->produce_code="奇门仓储字段,说明,string(50),,";
$batchs->inventory_type="奇门仓储字段,说明,string(50),,";
$batchs->actual_qty="奇门仓储字段,说明,string(50),,";
$batchs->quantity="奇门仓储字段,说明,string(50),,";
$batchs->remark="备注";
$items->batchs = $batchs;
//echo 'items:'.json_encode($items);
//$priceAdjustment = new PriceAdjustment;
//$priceAdjustment->type="奇门仓储字段,说明,string(50),,";
//$priceAdjustment->standard_price="奇门仓储字段,说明,string(50),,";
//$priceAdjustment->discount="奇门仓储字段,说明,string(50),,";
//$priceAdjustment->start_date="奇门仓储字段,说明,string(50),,";
//$priceAdjustment->end_date="奇门仓储字段,说明,string(50),,";
//$priceAdjustment->remark="备注";
//$items->priceAdjustment = $priceAdjustment;
//$detail->items = $items;
//$invoices->detail = $detail;
$invoices->code="CODE123";
$invoices->number="NUM123";
$deliveryOrder->invoices = $invoices;
//$deliveryRequirements = new DeliveryRequirements;
//$deliveryRequirements->schedule_type="奇门仓储字段,说明,string(50),,";
//$deliveryRequirements->schedule_day="奇门仓储字段,说明,string(50),,";
//$deliveryRequirements->schedule_start_time="奇门仓储字段,说明,string(50),,";
//$deliveryRequirements->schedule_end_time="奇门仓储字段,说明,string(50),,";
//$deliveryRequirements->delivery_type="奇门仓储字段,说明,string(50),,";
//$deliveryRequirements->remark="备注";
//$deliveryOrder->deliveryRequirements = $deliveryRequirements;
//$senderInfo = new SenderInfo;
//$senderInfo->company="奇门仓储字段,说明,string(50),,";
//$senderInfo->name="奇门仓储字段,说明,string(50),,";
//$senderInfo->zip_code="奇门仓储字段,说明,string(50),,";
//$senderInfo->tel="奇门仓储字段,说明,string(50),,";
//$senderInfo->mobile="奇门仓储字段,说明,string(50),,";
//$senderInfo->province="奇门仓储字段,说明,string(50),,";
//$senderInfo->city="奇门仓储字段,说明,string(50),,";
//$senderInfo->area="奇门仓储字段,说明,string(50),,";
//$senderInfo->town="奇门仓储字段,说明,string(50),,";
//$senderInfo->detail_address="奇门仓储字段,说明,string(50),,";
//$senderInfo->email="奇门仓储字段,说明,string(50),,";
//$senderInfo->country_code="奇门仓储字段,说明,string(50),,";
//$senderInfo->id="奇门仓储字段,说明,string(50),,";
//$senderInfo->car_no="奇门仓储字段,说明,string(50),,";
//$senderInfo->fax="奇门仓储字段,说明,string(50),,";
//$senderInfo->birth_date="奇门仓储字段,说明,string(50),,";
//$senderInfo->career="奇门仓储字段,说明,string(50),,";
//$senderInfo->nick="奇门仓储字段,说明,string(50),,";
//$senderInfo->id_type="奇门仓储字段,说明,string(50),,";
//$senderInfo->id_number="奇门仓储字段,说明,string(50),,";
//$senderInfo->country_code_ciq="奇门仓储字段,说明,string(50),,";
//$senderInfo->country_code_cus="奇门仓储字段,说明,string(50),,";
//$senderInfo->gender="奇门仓储字段,说明,string(50),,";
//$senderInfo->remark="备注";
//$deliveryOrder->senderInfo = $senderInfo;
//$receiverInfo = new ReceiverInfo;
//$receiverInfo->company="奇门仓储字段,说明,string(50),,";
//$receiverInfo->name="奇门仓储字段,说明,string(50),,";
//$receiverInfo->zip_code="奇门仓储字段,说明,string(50),,";
//$receiverInfo->tel="奇门仓储字段,说明,string(50),,";
//$receiverInfo->mobile="奇门仓储字段,说明,string(50),,";
//$receiverInfo->province="奇门仓储字段,说明,string(50),,";
//$receiverInfo->city="奇门仓储字段,说明,string(50),,";
//$receiverInfo->area="奇门仓储字段,说明,string(50),,";
//$receiverInfo->town="奇门仓储字段,说明,string(50),,";
//$receiverInfo->detail_address="奇门仓储字段,说明,string(50),,";
//$receiverInfo->email="奇门仓储字段,说明,string(50),,";
//$receiverInfo->country_code="奇门仓储字段,说明,string(50),,";
//$receiverInfo->id="奇门仓储字段,说明,string(50),,";
//$receiverInfo->car_no="奇门仓储字段,说明,string(50),,";
//$receiverInfo->fax="奇门仓储字段,说明,string(50),,";
//$receiverInfo->birth_date="奇门仓储字段,说明,string(50),,";
//$receiverInfo->career="奇门仓储字段,说明,string(50),,";
//$receiverInfo->nick="奇门仓储字段,说明,string(50),,";
//$receiverInfo->id_type="奇门仓储字段,说明,string(50),,";
//$receiverInfo->id_number="奇门仓储字段,说明,string(50),,";
//$receiverInfo->country_code_ciq="奇门仓储字段,说明,string(50),,";
//$receiverInfo->country_code_cus="奇门仓储字段,说明,string(50),,";
//$receiverInfo->gender="奇门仓储字段,说明,string(50),,";
//$receiverInfo->remark="备注";
//$deliveryOrder->receiverInfo = $receiverInfo;
//$pickerInfo = new Package;
//$pickerInfo->express_code="物流公司编码(SF=顺丰、EMS=标准快递、EYB=经济快件、ZJS=宅急送、YTO=圆通、ZTO=中通 (ZTO)、HTKY=百世汇通、 UC=优速、STO=申通、TTKDEX=天天快递、QFKD=全峰、FAST=快捷、POSTB=邮政小包、GTO=国通、YUNDA=韵达、JD=京东配送、DD=当当宅配、 AMAZON=亚马逊物流、OTHER=其他;只传英文编码)";
//$pickerInfo->logistics_name="物流名称";
//$pickerInfo->express_code="运单号";
//$pickerInfo->package_code="包裹编号";
//$deliveryOrder->pickerInfo = $pickerInfo;
$orderLines = new OrderLine;
$orderLines->order_line_no="奇门仓储字段,说明,string(50),,";
$orderLines->order_source_code="奇门仓储字段,说明,string(50),,";
//$orderLines->sub_source_code="奇门仓储字段,说明,string(50),,";
//$orderLines->item_code="奇门仓储字段,说明,string(50),,";
//$orderLines->item_id="奇门仓储字段,说明,string(50),,";
//$orderLines->item_name="奇门仓储字段,说明,string(50),,";
//$orderLines->plan_qty="奇门仓储字段,说明,string(50),,";
//$orderLines->sku_property="奇门仓储字段,说明,string(50),,";
//$orderLines->purchase_price="奇门仓储字段,说明,string(50),,";
//$orderLines->retail_price="奇门仓储字段,说明,string(50),,";
//$orderLines->inventory_type="奇门仓储字段,说明,string(50),,";
//$orderLines->product_date="奇门仓储字段,说明,string(50),,";
//$orderLines->expire_date="奇门仓储字段,说明,string(50),,";
//$orderLines->produce_code="奇门仓储字段,说明,string(50),,";
//$orderLines->batch_code="奇门仓储字段,说明,string(50),,";
//$orderLines->actual_qty="奇门仓储字段,说明,string(50),,";
//$orderLines->source_order_code="奇门仓储字段,说明,string(50),,";
//$orderLines->sub_source_order_code="奇门仓储字段,说明,string(50),,";
//$orderLines->ext_code="奇门仓储字段,说明,string(50),,";
//$orderLines->actual_price="奇门仓储字段,说明,string(50),,";
//$orderLines->discount_amount="奇门仓储字段,说明,string(50),,";
//$orderLines->owner_code="奇门仓储字段,说明,string(50),,";
//$orderLines->quantity="奇门仓储字段,说明,string(50),,";
//$orderLines->out_biz_code="奇门仓储字段,说明,string(50),,";
//$orderLines->product_code="奇门仓储字段,说明,string(50),,";
//$orderLines->stock_in_qty="奇门仓储字段,说明,string(50),,";
//$orderLines->stock_out_qty="奇门仓储字段,说明,string(50),,";
//$orderLines->warehouse_code="奇门仓储字段,说明,string(50),,";
//$orderLines->delivery_order_id="奇门仓储字段,说明,string(50),,";
//$orderLines->status="奇门仓储字段,说明,string(50),,";
//$orderLines->qr_code="奇门仓储字段,说明,string(50),,";
//$orderLines->pay_no="奇门仓储字段,说明,string(50),,";
//$orderLines->taobao_item_code="奇门仓储字段,说明,string(50),,";
//$orderLines->discount_price="奇门仓储字段,说明,string(50),,";
//$orderLines->color="奇门仓储字段,说明,string(50),,";
//$orderLines->size="奇门仓储字段,说明,string(50),,";
//$orderLines->standard_price="奇门仓储字段,说明,string(50),,";
//$orderLines->reference_price="奇门仓储字段,说明,string(50),,";
//$orderLines->discount="奇门仓储字段,说明,string(50),,";
//$orderLines->standard_amount="奇门仓储字段,说明,string(50),,";
//$orderLines->settlement_amount="奇门仓储字段,说明,string(50),,";
//$orderLines->location_code="奇门仓储字段,说明,string(50),,";
//$orderLines->amount="奇门仓储字段,说明,string(50),,";
//$orderLines->move_out_location="奇门仓储字段,说明,string(50),,";
//$orderLines->move_in_location="奇门仓储字段,说明,string(50),,";
//$orderLines->exception_qty="奇门仓储字段,说明,string(50),,";
//$orderLines->sub_delivery_order_id="奇门仓储字段,说明,string(50),,";
//$orderLines->sn_code="货品sn编码";
$batchs = new Batch;
$batchs->batch_code="批次号";
$batchs->product_date="奇门仓储字段,说明,string(50),,";
$batchs->expire_date="奇门仓储字段,说明,string(50),,";
$batchs->produce_code="奇门仓储字段,说明,string(50),,";
$batchs->inventory_type="奇门仓储字段,说明,string(50),,";
$batchs->quantity="奇门仓储字段,说明,string(50),,";
$batchs->remark="备注";
$batchs->sn_code="货品sn编码";
$orderLines->batchs = $batchs;
$orderLines->remark="remark";
$snList = new SnList;
$snList->sn="";
$orderLines->snList = $snList;
//$deliveryOrder->orderLines = $orderLines;
$items = new Item;
$items->owner_code="奇门仓储字段,说明,string(50),,";
$items->title="奇门仓储字段,说明,string(50),,";
$items->item_code="奇门仓储字段,说明,string(50),,";
$items->item_id="奇门仓储字段,说明,string(50),,";
$items->item_name="奇门仓储字段,说明,string(50),,";
$items->english_name="奇门仓储字段,说明,string(50),,";
//$items->short_name="奇门仓储字段,说明,string(50),,";
//$items->category_id="奇门仓储字段,说明,string(50),,";
//$items->category_name="奇门仓储字段,说明,string(50),,";
$items->sku_property="奇门仓储字段,说明,string(50),,";
$items->item_type="奇门仓储字段,说明,string(50),,";
//$items->tag_price="奇门仓储字段,说明,string(50),,";
//$items->retail_price="奇门仓储字段,说明,string(50),,";
//$items->cost_price="奇门仓储字段,说明,string(50),,";
//$items->purchase_price="奇门仓储字段,说明,string(50),,";
//$items->supplier_code="奇门仓储字段,说明,string(50),,";
//$items->supplier_name="奇门仓储字段,说明,string(50),,";
//$items->season_code="奇门仓储字段,说明,string(50),,";
//$items->season_name="奇门仓储字段,说明,string(50),,";
//$items->brand_code="奇门仓储字段,说明,string(50),,";
//$items->brand_name="奇门仓储字段,说明,string(50),,";
//$items->sn="奇门仓储字段,说明,string(50),,";
//$items->is_s_n_mgmt="奇门仓储字段,说明,string(50),,";
//$items->bar_code="奇门仓储字段,说明,string(50),,";
//$items->color="奇门仓储字段,说明,string(50),,";
//$items->size="奇门仓储字段,说明,string(50),,";
//$items->length="奇门仓储字段,说明,string(50),,";
//$items->width="奇门仓储字段,说明,string(50),,";
//$items->height="奇门仓储字段,说明,string(50),,";
//$items->volume="奇门仓储字段,说明,string(50),,";
//$items->gross_weight="奇门仓储字段,说明,string(50),,";
//$items->net_weight="奇门仓储字段,说明,string(50),,";
//$items->tare_weight="奇门仓储字段,说明,string(50),,";
//$items->safety_stock="奇门仓储字段,说明,string(50),,";
//$items->stock_unit="奇门仓储字段,说明,string(50),,";
//$items->stock_status="奇门仓储字段,说明,string(50),,";
//$items->product_date="奇门仓储字段,说明,string(50),,";
//$items->expire_date="奇门仓储字段,说明,string(50),,";
//$items->is_shelf_life_mgmt="奇门仓储字段,说明,string(50),,";
//$items->shelf_life="奇门仓储字段,说明,string(50),,";
//$items->reject_lifecycle="奇门仓储字段,说明,string(50),,";
//$items->lockup_lifecycle="奇门仓储字段,说明,string(50),,";
//$items->advent_lifecycle="奇门仓储字段,说明,string(50),,";
$items->batch_code="奇门仓储字段,说明,string(50),,";
$items->batch_remark="奇门仓储字段,说明,string(50),,";

//$items->pack_code="奇门仓储字段,说明,string(50),,";
//$items->pcs="奇门仓储字段,说明,string(50),,";
//$items->origin_address="奇门仓储字段,说明,string(50),,";
//$items->approval_number="奇门仓储字段,说明,string(50),,";
//$items->is_fragile="奇门仓储字段,说明,string(50),,";
//$items->is_hazardous="奇门仓储字段,说明,string(50),,";
//$items->pricing_category="奇门仓储字段,说明,string(50),,";
//$items->is_sku="奇门仓储字段,说明,string(50),,";
//$items->package_material="奇门仓储字段,说明,string(50),,";
//$items->price="奇门仓储字段,说明,string(50),,";
//$items->is_area_sale="奇门仓储字段,说明,string(50),,";
//$items->quantity="奇门仓储字段,说明,string(50),,";
//$items->normal_qty="奇门仓储字段,说明,string(50),,";
//$items->defective_qty="奇门仓储字段,说明,string(50),,";
//$items->receive_qty="奇门仓储字段,说明,string(50),,";
//$items->ex_code="奇门仓储字段,说明,string(50),,";
//$items->discount_price="奇门仓储字段,说明,string(50),,";
//$items->inventory_type="奇门仓储字段,说明,string(50),,";
//$items->plan_qty="奇门仓储字段,说明,string(50),,";
//$items->source_order_code="奇门仓储字段,说明,string(50),,";
//$items->sub_source_order_code="奇门仓储字段,说明,string(50),,";
//$items->produce_code="奇门仓储字段,说明,string(50),,";
//$items->order_line_no="奇门仓储字段,说明,string(50),,";
//$items->actual_qty="奇门仓储字段,说明,string(50),,";
//$items->amount="奇门仓储字段,说明,string(50),,";
//$items->unit="奇门仓储字段,说明,string(50),,";
//$items->warehouse_code="奇门仓储字段,说明,string(50),,";
//$items->lock_quantity="奇门仓储字段,说明,string(50),,";
//$items->order_code="奇门仓储字段,说明,string(50),,";
//$items->order_type="奇门仓储字段,说明,string(50),,";
//$items->out_biz_code="奇门仓储字段,说明,string(50),,";
//$items->product_code="奇门仓储字段,说明,string(50),,";
//$items->paper_qty="奇门仓储字段,说明,string(50),,";
//$items->diff_quantity="奇门仓储字段,说明,string(50),,";
//$items->ext_code="奇门仓储字段,说明,string(50),,";
//$items->lack_qty="奇门仓储字段,说明,string(50),,";
//$items->reason="奇门仓储字段,说明,string(50),,";
//$items->sn_code="奇门仓储字段,说明,string(50),,";
//$items->goods_code="奇门仓储字段,说明,string(50),,";
//$items->standard_price="奇门仓储字段,说明,string(50),,";
//$items->reference_price="奇门仓储字段,说明,string(50),,";
//$items->discount="奇门仓储字段,说明,string(50),,";
//$items->actual_amount="奇门仓储字段,说明,string(50),,";
//$items->latest_update_time="奇门仓储字段,说明,string(50),,";
//$items->change_time="奇门仓储字段,说明,string(50),,";
//$items->temp_requirement="奇门仓储字段,说明,string(50),,";
//$items->channel_code="奇门仓储字段,说明,string(50),,";
//$items->origin_code="奇门仓储字段,说明,string(50),,";
$items->remark="备注";
$batchs = new Batch;
$batchs->batch_code="奇门仓储字段,说明,string(50),,";
$batchs->product_date="奇门仓储字段,说明,string(50),,";
$batchs->expire_date="奇门仓储字段,说明,string(50),,";
$batchs->produce_code="奇门仓储字段,说明,string(50),,";
$batchs->inventory_type="奇门仓储字段,说明,string(50),,";
$batchs->actual_qty="奇门仓储字段,说明,string(50),,";
$batchs->quantity="奇门仓储字段,说明,string(50),,";
$batchs->remark="备注";
$items->batchs = $batchs;
//$priceAdjustment = new PriceAdjustment;
//$priceAdjustment->type="奇门仓储字段,说明,string(50),,";
//$priceAdjustment->standard_price="奇门仓储字段,说明,string(50),,";
//$priceAdjustment->discount="奇门仓储字段,说明,string(50),,";
//$priceAdjustment->start_date="奇门仓储字段,说明,string(50),,";
//$priceAdjustment->end_date="奇门仓储字段,说明,string(50),,";
//$priceAdjustment->remark="备注";
//$items->priceAdjustment = $priceAdjustment;
//$deliveryOrder->items = $items;
$packages = new Package;
$packages->logistics_code="奇门仓储字段,说明,string(50),,";
$packages->logistics_name="奇门仓储字段,说明,string(50),,";
$packages->express_code="奇门仓储字段,说明,string(50),,";
$packages->package_code="奇门仓储字段,说明,string(50),,";
//$packages->length="奇门仓储字段,说明,string(50),,";
//$packages->width="奇门仓储字段,说明,string(50),,";
//$packages->height="奇门仓储字段,说明,string(50),,";
//$packages->weight="奇门仓储字段,说明,string(50),,";
//$packages->volume="奇门仓储字段,说明,string(50),,";
//$packages->invoice_no="奇门仓储字段,说明,string(50),,";
//$packages->theoretical_weight="奇门仓储字段,说明,string(50),,";
$packages->remark="备注";
//$packageMaterialList = new PackageMaterial;
//$packageMaterialList->type="奇门仓储字段,说明,string(50),,";
//$packageMaterialList->quantity="奇门仓储字段,说明,string(50),,";
//$packageMaterialList->remark="备注";
//$packages->packageMaterialList = $packageMaterialList;
$items = new Item;
//$items->logistics_code="奇门仓储字段,说明,string(50),,";
//$items->item_code="奇门仓储字段,说明,string(50),,";
//$items->item_id="奇门仓储字段,说明,string(50),,";
//$items->item_name="奇门仓储字段,说明,string(50),,";
//$items->ext_code="奇门仓储字段,说明,string(50),,";
//$items->bar_code="奇门仓储字段,说明,string(50),,";
//$items->quantity="奇门仓储字段,说明,string(50),,";
//$items->pack_item_price="奇门仓储字段,说明,string(50),,";
//$items->plan_qty="奇门仓储字段,说明,string(50),,";
//$items->actual_qty="奇门仓储字段,说明,string(50),,";
//$items->batch_code="奇门仓储字段,说明,string(50),,";
//$items->product_date="奇门仓储字段,说明,string(50),,";
//$items->expire_date="奇门仓储字段,说明,string(50),,";
//$items->produce_code="奇门仓储字段,说明,string(50),,";
//$items->remark="备注";
$packages->items = [];
//$deliveryOrder->packages = $packages;
$relatedOrders = new RelatedOrder;
$relatedOrders->order_code="奇门仓储字段,说明,string(50),,";
$relatedOrders->order_type="奇门仓储字段,说明,string(50),,";
$relatedOrders->remark="备注";
//$deliveryOrder->relatedOrders = $relatedOrders;
$request->DeliveryOrder= $deliveryOrder;
$packages = new Package;
$packages->remark="备注";
$packages->logistics_code="SF";
$packages->logistics_name="顺丰";
$packages->express_code="Y1234";
$packages->package_code="LG1234";
//$packages->length="12.0";
//$packages->width="12.0";
//$packages->height="12.0";
//$packages->theoretical_weight="12.0";
//$packages->weight="12.0";
//$packages->volume="12.0";
$packages->invoice_no="IN1234";
//$packageMaterialList = new PackageMaterial;
//$packageMaterialList->remark="备注";
//$packageMaterialList->type="XLL";
//$packageMaterialList->quantity="12";
//$packages->packageMaterialList = $packageMaterialList;
$items = new Item;
$items->logistics_code="奇门仓储字段,说明,string(50),,";
$items->item_name="奇门仓储字段,说明,string(50),,";
$items->ext_code="奇门仓储字段,说明,string(50),,";
$items->bar_code="奇门仓储字段,说明,string(50),,";
$items->pack_item_price="奇门仓储字段,说明,string(50),,";
$items->plan_qty="奇门仓储字段,说明,string(50),,";
$items->actual_qty="奇门仓储字段,说明,string(50),,";
$items->batch_code="奇门仓储字段,说明,string(50),,";
$items->product_date="奇门仓储字段,说明,string(50),,";
$items->expire_date="奇门仓储字段,说明,string(50),,";
$items->produce_code="奇门仓储字段,说明,string(50),,";
$items->remark="备注";
$items->item_code="I1234";
$items->item_id="WI1234";
$items->quantity="11";
$packages->items = [];
$request->Packages = $packages;
$orderLines = new OrderLine;
$orderLines->sku_property="奇门仓储字段,说明,string(50),,";
$orderLines->purchase_price="奇门仓储字段,说明,string(50),,";
$orderLines->retail_price="奇门仓储字段,说明,string(50),,";
$orderLines->source_order_code="奇门仓储字段,说明,string(50),,";
$orderLines->sub_source_order_code="奇门仓储字段,说明,string(50),,";
$orderLines->actual_price="奇门仓储字段,说明,string(50),,";
$orderLines->discount_amount="奇门仓储字段,说明,string(50),,";
$orderLines->quantity="奇门仓储字段,说明,string(50),,";
$orderLines->out_biz_code="奇门仓储字段,说明,string(50),,";
$orderLines->product_code="奇门仓储字段,说明,string(50),,";
$orderLines->stock_in_qty="奇门仓储字段,说明,string(50),,";
$orderLines->stock_out_qty="奇门仓储字段,说明,string(50),,";
$orderLines->warehouse_code="奇门仓储字段,说明,string(50),,";
$orderLines->delivery_order_id="奇门仓储字段,说明,string(50),,";
$orderLines->status="奇门仓储字段,说明,string(50),,";
$orderLines->pay_no="奇门仓储字段,说明,string(50),,";
$orderLines->taobao_item_code="奇门仓储字段,说明,string(50),,";
$orderLines->discount_price="奇门仓储字段,说明,string(50),,";
//$orderLines->color="奇门仓储字段,说明,string(50),,";
//$orderLines->size="奇门仓储字段,说明,string(50),,";
//$orderLines->standard_price="奇门仓储字段,说明,string(50),,";
//$orderLines->reference_price="奇门仓储字段,说明,string(50),,";
//$orderLines->discount="奇门仓储字段,说明,string(50),,";
//$orderLines->standard_amount="奇门仓储字段,说明,string(50),,";
//$orderLines->settlement_amount="奇门仓储字段,说明,string(50),,";
//$orderLines->location_code="奇门仓储字段,说明,string(50),,";
//$orderLines->amount="奇门仓储字段,说明,string(50),,";
//$orderLines->move_out_location="奇门仓储字段,说明,string(50),,";
//$orderLines->move_in_location="奇门仓储字段,说明,string(50),,";
//$orderLines->exception_qty="奇门仓储字段,说明,string(50),,";
//$orderLines->sub_delivery_order_id="奇门仓储字段,说明,string(50),,";
$orderLines->remark="备注";
$orderLines->order_line_no="1";
$orderLines->order_source_code="P1234";
$orderLines->sub_source_code="J1234";
$orderLines->item_code="I1234";
$orderLines->item_id="WI1234";
$orderLines->inventory_type="ZP";
$orderLines->owner_code="OW1234";
$orderLines->item_name="淘公仔";
$orderLines->ext_code="PL1234";
$orderLines->plan_qty="12";
$orderLines->actual_qty="12";
$orderLines->batch_code="P1234";
$orderLines->product_date="2016-09-09";
$orderLines->expire_date="2017-09-09";
$orderLines->produce_code="P2345";
$batchs = new Batch;
$batchs->quantity="奇门仓储字段,说明,string(50),,";
$batchs->remark="备注";
$batchs->batch_code="PC1234";
$batchs->product_date="2016-09-09";
$batchs->expire_date="2017-09-09";
$batchs->produce_code="PH1234";
$batchs->inventory_type="ZP";
$batchs->actual_qty="12";
$batchs->sn_code="货品sn编码";
$orderLines->batchs = $batchs;
$orderLines->qr_code="one;two";
$orderLines->sn_code="货品sn编码";


$request->Extend_props="";
echo json_encode($request);
die;

dump($req);
$resp = $c->execute($req);
var_dump($resp)

?>