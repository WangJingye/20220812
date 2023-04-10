<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>网站后台管理模版</title>
    <link rel="stylesheet" type="text/css" href="<?php echo url('/static/admin/layui/css/layui.css')?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo url('/static/admin/css/admin.css')?>" />
    <script src="{{ url('/static/admin/layuiadmin/layui/layui.js') }}"></script>
    <script src="{{ url('/static/admin/js/jquery.min.js') }}"></script>
    
</head>

<body>
<style>
    .layui-inline{
        display:flex;
    }
    select{
        border:1px solid #ccc;
    }
    #table-list th,#table-list td{
        text-align: center;
        border:1px solid #000 !important;
    }

    .order-status-filter ul{
        display: flex;
    }
    .order-status-filter li{
        flex:1;
        text-align: center;
    }
    .order-status-filter li a{
        width:100%;
    }
</style>


<div class="page-content-wrap">
    <form class="layui-form" action="">
        <input type="hidden" name="status" value="{{request('status','all')}}" />
        <div class="layui-form-item" >
            <div class="layui-inline" style="position: relative">
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input class="layui-input" id="name" name="name" autocomplete="off" placeholder="商品名称" value="{{request('name')}}">
                    </div>
                </div>

                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input class="layui-input" id="order_sn" name="order_sn" autocomplete="off" placeholder="订单号" value="{{request('order_sn')}}">
                    </div>
                </div>

                <div class="layui-inline">
                    <label class="layui-form-label">下单时间</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" id="start_time" name="start_time" placeholder="开始时间"  type="text" value="{{request('start_time')}}">
                    </div>
                    <div class="layui-input-inline">
                        <input class="layui-input" id="end_time" name="end_time" placeholder="结束时间" value="{{request('end_time')}}" type="text" >
                    </div>
                </div>
                <div class="layui-inline" style="position: absolute;right: 20px;">
                    <button class="layui-btn layui-btn-normal" lay-submit="search">搜索</button>
                </div>

            </div>
            <div class="layui-form-item" >
                <div class="layui-inline" style="position: relative">
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <input class="layui-input" id="customer_id" name="customer_id" autocomplete="off" placeholder="输入周友会员号" value="{{request('customer_id')}}">
                        </div>
                    </div>

                    <div class="layui-input-inline">
                        <select name="phone_code" >
                            <?php $phoneCode=['+852','+853','+86','+886'];?>
                            @foreach($phoneCode as $code)
                            <option value="{{$code}}" @if( request('phone_code')==$code) selected @endif>{{$code}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="layui-input-inline">
                    	<input style="display: inline-block;width: 60%;" class="layui-input " id="phone" name="phone" autocomplete="off" placeholder="输入手机号" value="{{request('phone')}}">
                    </div>

                    <div class="layui-inline" style="position: absolute;right: 20px;">
                        <button id="formSubmit" class="layui-btn" lay-submit lay-filter="formSubmit">导出</button>
                    </div>

                </div>


        </div>
    </form>
    <?php if(session('success')):?>
    <div class="message success"><?php echo session('success')?></div>
    <?php endif;?>
    <?php if(session('error')):?>
    <div class="message error"><?php echo session('error')?></div>
    <?php endif;?>


    <div class="layui-form" id="table-list">
        <div class="order-status-filter">
            <ul>
                <li><a href="{{$action->getStatusFilter('all')}}" class="layui-btn {{$action->getActionStatus('all')}}">全部订单</a></li>
                <li><a href="{{$action->getStatusFilter('pending')}}" class="layui-btn {{$action->getActionStatus('pending')}}">待付款</a></li>
                <li><a href="{{$action->getStatusFilter('paid')}}" class="layui-btn {{$action->getActionStatus('paid')}}" >已支付</a></li>
                <li><a href="{{$action->getStatusFilter('pending-shiped')}}" class="layui-btn {{$action->getActionStatus('pending-shiped')}}" >待发货</a></li>
                <li><a href="{{$action->getStatusFilter('finished-shiped')}}" class="layui-btn {{$action->getActionStatus('finished-shiped')}}" >已发货</a></li>
                <li><a href="{{$action->getStatusFilter('after-sales')}}" class="layui-btn {{$action->getActionStatus('after-sales')}}">售后</a></li>
                <li><a href="{{$action->getStatusFilter('cancel')}}" class="layui-btn {{$action->getActionStatus('cancel')}}" >已关闭</a></li>
            </ul>
        </div>
        <table class="layui-table" lay-even lay-skin="nob">
            <thead>
            <tr>
                <th class="hidden-xs" style="width:20%;text-align: center">产品图片</th>
                <th class="hidden-xs" style="width:10%;text-align: center">名称</th>
                <th class="hidden-xs" style="width:10%;text-align: center">原价</th>
                <th class="hidden-xs" style="width:15%;text-align: center">买家</th>
                <th class="hidden-xs" style="width:10%;text-align: center">实际支付价格</th>
                <th class="hidden-xs" style="width:10%;text-align: center">状态</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($list as $order)
                <tr>
                    <td colspan="7" style="text-align: left">
                        <span class="order_sn">
                            	商城订单号：{{$order->order_sn}}
                        </span>
                        <span class="created_at">
                            	创建时间：{{$order->created_at}}
                        </span>
                        <span class="created_at" >
                        		订单状态：{{$action->getOrderStatus($order)}} 
                        </span>
                        <span class="created_at">
                           <a class="layui-btn layui-btn-normal layui-btn-md" href="{{url('admin/sales/order/edit').toQuery(['id'=>$order->id])}}">订单详情</a>
                        </span>
                    </td>

                </tr>
                <tr>
                    <td colspan="7" style="padding:0;">
                    <table class="layui-table" lay-even lay-skin="nob">
                       <?php $i=0?>
                        @foreach ($order->goods as $good)
                            @if(request('status')=='after-sales')
                               @if(!$action->isAfterSales($good,$order))
                               		@continue
                               @endif
                            @endif
                                <tr>
                                    <td class="hidden-xs" style="width:20%;border:none !important;">
                                        <img src="{{$good->image}}" style="width:100px;"/>
                                    </td>
                                    <td class="hidden-xs" style="width:10%">
                                        <ul>
                                            <li>{{$good->name}}</li>
                                            <li>x {{$good->inventory}}</li>
                                            <li>款号：{{$good->section}}</li>
                                        </ul>
                                    </td>
                                    <td class="hidden-xs" style="width:10%">
                                        	￥{{floor($good->original_price)}}
                                    </td>
                                    @if($i==0)
                                    <td class="hidden-xs" style="width:15%" rowspan="{{count($order->goods)}}">
                                        <ul>
                                            <li>{{$order->customer_name}}</li>
                                            <li>{{$order->phone_code}} {{$order->phone}}</li>
                                        </ul>
                                    </td>
                                    @endif
                                    <td class="hidden-xs"  style="width:10%">￥{{floor($good->price)}}</td>
                                    <td class="hidden-xs"  style="width:10%">{{$action->getOrderGoodsStatus($order,$good)}}</td>
                                </tr>
                                <?php $i++?>
                        @endforeach
                    </table>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="page-wrap">
            <?php echo $list->appends($_GET)->links();?>
        </div>
    </div>
    <script type="text/javascript">
        layui.use(['layer', 'form','table','laydate'], function(){
      	  var layer = layui.layer
      	  ,form = layui.form;
        	var laydate = layui.laydate;
        	var table = layui.table;

        	laydate.render({
                elem: '#start_time'  // 输出框id
                ,type: 'datetime'
            });
            laydate.render({
                elem: '#end_time'  // 输出框id
                ,type: 'datetime'
            });

            form.on('submit(formSubmit)', function (data) {
    	        console.log(data);
    	        $.post("{{ route('backend.sales.order.export') }}", data.field, function (res) {
    	            if (res.code != 1) {
    	                return false;
    	            }
    	            var value = res.data.value;
    	            var columns = res.data.columns;
    	            console.log(data);
    	            table.exportFile(columns, value, 'csv'); //默认导出 csv，也可以为：xls
    	        }, 'json');
    	        return false;
    	    });

      	});
        
    </script>
</div>
</body>

</html>