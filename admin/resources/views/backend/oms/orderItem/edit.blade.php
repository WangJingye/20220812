@extends('backend.base') @section('content')
<style>
caption{
	text-align:left;
	font-weight: 800;
}

</style>
<div class="layui-col-md12">
	<div class="layui-card">
		<div class="layui-card-body" style="padding: 15px;">
			<ul class="layui-tab-title">



			</ul>
			<div class="layui-tab layui-tab-card">
				<ul class="layui-tab-title">
					<li><a href="{{url('admin/sales/order')}}">订单列表</a></li>
					<li class="layui-this">订单详情</li>
				</ul>
				<div class="layui-tab-content" style="">
					<div class="layui-tab-item layui-show">
						<div class="order-information">
								<div class="layui-row layui-row layui-col-space20" >
									<div class="layui-col-md12">
										<table class="layui-table" lay-skin="line" lay-size="">
											<colgroup>
												<col width="30%">
												<col width="20%">
												<col width="30%">
												<col width="10%">
												<col width="10%">
											</colgroup>
											<tbody>
												<tr>
													<td>订单编号：{{$order->order_sn}}</td>
													<td>订单状态：{{$action->getOrderStatus($order)}}</td>
													<td>下单时间：{{$order->created_at}}</td>
													<td>
														 @if($action->getOrderRefundButton($order->id))
														 <span order_id="{{$order->id}}" class="refund layui-btn layui-btn-normal layui-btn-md" >
															 退款
														 </span>
														 @endif
													</td>
													<td>
														 @if($action->getDiffRefundButton($order))
														 <span order_id="{{$order->id}}" class="diffrefund layui-btn layui-btn-normal layui-btn-md" >
															 差价退款
														 </span>
														 @endif
													</td>
												</tr>
											</tbody>
										</table>
									</div>

								</div>
						</div>

						<div class="customer-information">
							<div class="layui-row layui-row" >
								<table class="layui-table" lay-skin="line" lay-size="">
									<colgroup>
										<col >
									</colgroup>
									<tbody>
									<tr>
										<td>周友会员编号：{{$order->customer_id}}</td>
									</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="order-items-information" style="margin-top:20px;">
						<div class="layui-row layui-row" >

							<table class="layui-table" lay-skin="line" lay-size="">
								<caption >商品信息</caption>
								<colgroup>
									<col width="150">
									<col>
								</colgroup>
								<tbody>
								@foreach($items as $goods)
								<thead>
								@if($order->service_type == 'address')
								<tr>
									<th colspan="6">
										物流商:{{$goods[0]->ship_method}} 运单号：{{$goods[0]->shipping_id}}
									</th>
									<th data-ship_method='{{$goods[0]->ship_method}}' data-shipping_id='{{$goods[0]->shipping_id}}' class="view_express_info" style="text-align: right">
										@if($goods[0]->ship_method)
										<span class="layui-btn layui-btn-normal" >查看物流</span>
										@endif
									</th>
								</tr>
								@endif
								@if($order->service_type == 'pickSelf')
								<tr>
									<th colspan="7">提货码:{{$goods[0]->pick_code}}</th>
								</tr>
								@endif
								</thead>
								@foreach($goods as $item)
									<tr>
										<td><img src ="{{$item->image}}" style="width: 50px;"/></td>
										<td>
											<li>{{$item->name}}</li>
											@if($item->option)
												@foreach(json_decode($item->option,true) as $option)
												<li>{{$option['name']??$option['key']}}:{{$option['value']}}</li>
												@endforeach
											@endif
											@if($item->content)
												<li>刻字：{{$item->content}}</li>
											@endif
											<li>{{$action->getDiffItemInfo($order->order_sn,$item->lineNbr)}}</li>
											<li>款号：{{$item->section}}</li>
										</td>
										<td>
											<ul>
												<li style="text-decoration:line-through">￥{{floor($item->original_price)}}</li>
												<li>￥{{floor($item->sale_pdt)}}</li>
											</ul>
										</td>
										<td>
											<ul>
												<li>
													{{--退款--}}
												</li>
												@if($item->point)
												<li>使用悦享钱:{{$item->point}}</li>
												@endif
												@if($item->give_point)
													<li>获取悦享钱:{{$action->getOrderItemEarnPoints($item)}}</li>
												@endif
											</ul>
										</td>
										<td>
											{{$action->getOrderGoodsStatus($order,$item)}}
										</td>
										<td>
											@if($action->getItemRefundButton($order->id,$item->id))
												<span order_id="{{$order->id}}" item_id="{{$item->id}}" class="refund layui-btn layui-btn-normal layui-btn-md">
													退款
												</span>
											@endif
										</td>
										<td>
											@if($action->getDiffItemRefundButton($order,$item))
												<span order_id="{{$order->id}}" item_id="{{$item->id}}" class="diffrefund layui-btn layui-btn-normal layui-btn-md">
													差价退款
												</span>
											@endif
										</td>
									</tr>
									@endforeach
									@endforeach
									</tbody>
							</table>

							<!-- 赠品 -->
							<table class="layui-table" lay-skin="line" lay-size="">
								@foreach($order->gift as $item)
									<tr>
										<td><img src ="{{$item->image}}" style="width: 50px;"/></td>
										<td>{{$item->name}}</td>
										<td>赠品</td>
									</tr>
								@endforeach
							</table>
							<div class="layui-col-md4 layui-col-md-offset8">
								<table class="layui-table" lay-skin="nob" lay-size="">
									<colgroup>
										<col width="150">
										<col>
									</colgroup>
									<tbody>
									<tr>
										<td><strong>商品折后价：</strong></td>
										<td>￥{{floor($order->sale_pdt)}}</td>
									</tr>
									<tr>
										<td><strong>使用满减优惠：</strong></td>
										<td>￥{{$order->promotionSale!=0?"-".floor($order->promotionSale):0}}</td>
									</tr>
									<tr>
										<td><strong>使用优惠券：</strong></td>
										<td>￥{{$order->coupon_discount!=0?"-".floor($order->coupon_discount):0}}</td>
									</tr>
									<tr>
										<td><strong>使用优惠码：</strong></td>
										<td>￥{{$order->code_discount!=0?"-".floor($order->code_discount):0}}</td>
									</tr>
									@if($action->hasDiff($order))
									<tr>
										<td><strong>补差：</strong></td>
										<td>￥{{$order->diff->diff_total_price}}</td>
									</tr>
									@endif
									<tr>
										<td><strong>订单总金额：</strong></td>
										<td>￥{{$action->getOrderTotalAmount($order)}}</td>
									</tr>
									<tr>
										<td><strong>可获得悦享钱：</strong></td>
										<td>￥{{floor($action->getOrderEarnPoints($order))}}</td>
									</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>


					<div class="pay-information">
							<div class="layui-row layui-row layui-col-space20" >
								<div class="layui-col-md6">
									<table class="layui-table" lay-skin="line" lay-size="">
										<caption>配送信息</caption>
										<colgroup>
											<col width="100%">
										</colgroup>
										<tbody>
											@if($order->service_type == 'address')
											<tr>
												<td>{{$order->consignee}} {{$order->phone}}</td>
											</tr>
											<tr>
												<td>{{$order->province}} {{$order->city}} {{$order->district}} {{$order->address}}</td>
											</tr>
											@endif
											@if($order->service_type == 'pickSelf')
											<tr>
												<td>{{$order->store_name}} </td>
											</tr>
											<tr>
												<td>{{$action->getStoreAddress($order->store_address)}} {{$order->store_open_hour}}</td>
											</tr>
											@endif
										</tbody>
									</table>
								</div>
								<div class="layui-col-md6">
									<table class="layui-table" lay-skin="line" lay-size="">
										<caption>发票信息:</caption>
										<colgroup>
											<col width="100%">
										</colgroup>
                                        <?php $invoice = $order['is_invoice'] == 2 && $order['invoice'] ? json_decode($order['invoice'], true) : []?>
                                        <tbody>
                                        <tr>
                                            <td>抬头：<?= $invoice['title'] ?? ''?></td>
                                        </tr>
                                        <tr>
                                            <td>纳税人识别号：<?= $invoice['code'] ?? ''?></td>
                                        </tr>
                                        </tbody>
									</table>
								</div>
							</div>
					</div>

					<div class="information">
						<div class="layui-row layui-row layui-col-space20" >
							<div class="layui-col-md6">
								<table class="layui-table" lay-skin="line" lay-size="">
									<caption>心意卡信息</caption>
									<colgroup>
										<col width="100%">
									</colgroup>
									<tbody>
									<tr><td>送给：{{$order->gift_to}}</td></tr>
									<tr><td>内容：{{$order->gift_content}}</td></tr>
									<tr><td>来自：{{$order->gift_from}}</td></tr>
									</tbody>
								</table>
							</div>
							<div class="layui-col-md6">
								<table class="layui-table" lay-skin="line" lay-size="">
									<caption>订单状态更新:</caption>
									<colgroup>
										<col width="100%">
									</colgroup>
									<tbody>
									@foreach($order->orderStatusHistory as $status)
									<tr><td>{{$status->created_at}} {{$status->comment}}</td></tr>
									@endforeach

									</tbody>
								</table>
							</div>
						</div>
					</div>


				</div>
			</div>
		</div>
	</div>
	@endsection

	<script>
@section('layui_script')



    form.on('submit(formSubmit)', function(data){


    });

    //自定义验证规则
    form.verify({

    });

    $('.view_express_info').on('click',function(){
        var ship_method = $(this).data('ship_method');
        var shipping_id = $(this).data('shipping_id');
		var data = {"ship_method":ship_method,"shipping_id":shipping_id};
		$.post("{{ route('backend.sales.order.getExpressInfo') }}", data, function (res) {
			var msg = '';
            if (res.code != 1) {
                msg = '暂无物流信息';
            }else{
				msg = res.data.msg;
            }
            layer.open({
                type: 1
                ,offset: 't'
                ,id: 'csslayerDemo'
                ,content: '<div style="padding: 20px;">'+ msg +'</div>'
//                 ,btn: '关闭全部'
                ,btnAlign: 'c' //按钮居中
                ,shade: 0 //不显示遮罩
                ,yes: function(){
                  layer.closeAll();
                }
              });
            return false;
        }, 'json');
	});


    $('.ruleDetail').on('click', function(){
    	layer.open({
    	  title:'规则详情',
  		  type: 1,
  		  content: '<pre style="padding: 20px;">'+$(this).data('detail')+'</pre>',
    	  area: ['500px', '800px'],
    	  offset: 't',
    	  shadeClose:true,
    	  fixed :true,
    	  scrollbar :false,
  		});
    });

	$('.refund').on('click',function(){
		var order_id = $(this).attr('order_id');
		var item_id = $(this).attr('item_id');
		layer.open({
	    	  title:'退款',
	  		  type: 2,
	  		  content: "{{ route('backend.sales.order.refund') }}?type=2&order_id="+order_id+"&item_id="+item_id,
	    	  area: ['600px', '800px'],
	    	  offset: 't',
	    	  shadeClose:true,
	    	  fixed :true,
	    	  scrollbar :false,
	    	  end:function(){
					location.reload();
		    	  }
	  		});
		});

		$('.diffrefund').on('click',function(){
			var order_id = $(this).attr('order_id');
			var item_id = $(this).attr('item_id');
			layer.open({
		    	  title:'差价退款',
		  		  type: 2,
		  		  content: "{{ route('backend.sales.order.refund') }}?type=1&order_id="+order_id+"&item_id="+item_id,
		    	  area: ['600px', '800px'],
		    	  offset: 't',
		    	  shadeClose:true,
		    	  fixed :true,
		    	  scrollbar :false,
		    	  end:function(){
						location.reload();
			    	  }
		  		});
			});

@endsection
</script>