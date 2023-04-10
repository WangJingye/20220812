<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no"/>
    <title>网站后台管理模版</title>
    <link rel="stylesheet" type="text/css" href="<?php echo url('/static/admin/css/admin.css')?>"/>
    <link rel="stylesheet" type="text/css" href="<?php echo url('/static/admin/layuiadmin/layui/css/layui.css')?>"/>
    <script src="{{ url('/static/admin/layuiadmin/layui/layui.js') }}"></script>
    <script src="{{ url('/static/admin/js/jquery.min.js') }}"></script>
    <script src="{{ url('/ext/tablePage.js') }}"></script>

</head>

<body>
<style>
    .layui-inline {
        display: flex;
    }

    select {
        border: 1px solid #ccc;
    }

    #table-list th, #table-list td {
        text-align: center;
        border: 1px solid #c9c9c9;
    }

    .order-status-filter ul {
        display: flex;
    }

    .order-status-filter li {
        flex: 1;
        text-align: center;
    }

    .order-status-filter li a {
        width: 100%;
    }

    td span {
        padding-left: 10px;
        padding-right: 10px;
    }


</style>


<div class="page-content-wrap">
    <form class="layui-form" action="" id="search-form" lay-filter="search-form">
        <input type="hidden" name="status" value="{{request('status','all')}}"/>
        <div class="layui-form-item">
            <div class="layui-inline" style="position: relative">
                <div class="layui-input-inline">
                    <input class="layui-input" id="goods_name" name="goods_name" autocomplete="off"
                           placeholder="商品名称"
                           value="{{request('goods_name')}}">
                </div>

                <div class="layui-input-inline">
                    <input class="layui-input" id="order_sn" name="order_sn" autocomplete="off" placeholder="订单号"
                           value="{{request('order_sn')}}">
                </div>
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input class="layui-input" id="pos_id" name="pos_id" autocomplete="off"
                               placeholder="输入会员号" value="{{request('pos_id')}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input style="display: inline-block;" class="layui-input " id="mobile" name="mobile"
                               autocomplete="off" placeholder="输入手机号" value="{{request('mobile')}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input style="display: inline-block;" class="layui-input " id="contact" name="contact"
                               autocomplete="off" placeholder="输入收货人名字" value="{{request('contact')}}">
                    </div>
                </div>

                <div class="layui-inline" style="position: absolute;right: 20px;">
                    <button class="layui-btn layui-btn-normal" lay-submit="search">搜索</button>
                </div>
            </div>
            <div class="layui-inline" style="position: relative">

                {{--                <div class="layui-input-inline">--}}
                {{--                    <input class="layui-input" id="coupon_id" name="coupon_id" autocomplete="off"--}}
                {{--                           placeholder="输入优惠券id" value="{{request('coupon_id')}}">--}}
                {{--                </div>--}}

                {{--                <div class="layui-input-inline">--}}
                {{--                    <input style="display: inline-block;" class="layui-input " id="coupon_code" name="coupon_code"--}}
                {{--                           autocomplete="off" placeholder="输入优惠券码" value="{{request('coupon_code')}}">--}}
                {{--                </div>--}}
                <div class="layui-inline" style="position: absolute;right: 20px;">
                    <button id="formSubmit" class="layui-btn" lay-submit lay-filter="formSubmit">导出</button>
                </div>

            </div>


        </div>
        <div class="layui-form-item">
            <div class="layui-inline" style="position: relative">


                <div class="layui-inline">
                    <label class="layui-form-label">发货时间</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" id="start_send" name="start_send" placeholder="开始时间" type="text"
                               value="{{request('start_send')}}" autocomplete="off">
                    </div>
                    <div class="layui-input-inline">
                        <input class="layui-input" id="end_send" name="end_send" placeholder="结束时间"
                               value="{{request('end_send')}}" type="text" autocomplete="off">
                    </div>
                </div>

                <label class="layui-form-label">下单时间</label>
                <div class="layui-input-inline">
                    <input class="layui-input" id="start_time" name="start_time" placeholder="开始时间" type="text"
                           value="{{request('start_time')}}" autocomplete="off">
                </div>
                <div class="layui-input-inline">
                    <input class="layui-input" id="end_time" name="end_time" placeholder="结束时间"
                           value="{{request('end_time')}}" type="text" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">订单状态</label>
            <div class="layui-input-block">
                <input type="checkbox" name="order_status[1]" title="待支付"
                       @if(isset(request('order_status')[1])) checked="" @endif>
                <input type="checkbox" name="order_status[2]" title="已取消"
                       @if(isset(request('order_status')[2])) checked="" @endif>
                <input type="checkbox" name="order_status[3]" title="待发货"
                       @if(isset(request('order_status')[3])) checked="" @endif>
                <input type="checkbox" name="order_status[4]" title="待收货"
                       @if(isset(request('order_status')[4])) checked="" @endif>
                <input type="checkbox" name="order_status[5]" title="退款中"
                       @if(isset(request('order_status')[5])) checked="" @endif>
                <input type="checkbox" name="order_status[7]" title="已退款"
                       @if(isset(request('order_status')[7])) checked="" @endif>
                <input type="checkbox" name="order_status[8]" title="退货中"
                       @if(isset(request('order_status')[8])) checked="" @endif>
                <input type="checkbox" name="order_status[9]" title="已签收"
                       @if(isset(request('order_status')[9])) checked="" @endif>
                <input type="checkbox" name="order_status[10]" title="已完成"
                       @if(isset(request('order_status')[10])) checked="" @endif>
                <input type="checkbox" name="order_status[11]" title="已关闭"
                       @if(isset(request('order_status')[11])) checked="" @endif>
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
                <li><a href="{{$action->getStatusFilter('all')}}"
                       class="layui-btn {{$action->getActionStatus('all')}}">全部订单</a>
                </li>
                <li><a href="{{$action->getStatusFilter('pending')}}"
                       class="layui-btn {{$action->getActionStatus('pending')}}">待付款</a></li>
                <li><a href="{{$action->getStatusFilter('pending-shiped')}}"
                       class="layui-btn {{$action->getActionStatus('pending-shiped')}}">待发货</a></li>
                {{--                <li><a href="{{$action->getStatusFilter('paid')}}"--}}
                {{--                       class="layui-btn {{$action->getActionStatus('paid')}}">已支付</a></li>--}}
                <li><a href="{{$action->getStatusFilter('finished-shiped')}}"
                       class="layui-btn {{$action->getActionStatus('finished-shiped')}}">已发货</a></li>
                {{--                <li><a href="{{$action->getStatusFilter('after-sales')}}"--}}
                {{--                       class="layui-btn {{$action->getActionStatus('after-sales')}}">售后</a></li>--}}
                <li><a href="{{$action->getStatusFilter('cancel')}}"
                       class="layui-btn {{$action->getActionStatus('cancel')}}">已关闭</a></li>
                <li><a href="{{$action->getStatusFilter('finished')}}"
                       class="layui-btn {{$action->getActionStatus('finished')}}">已完成</a></li>
            </ul>
        </div>

        <div class="layui-btn-group demoTable" style="margin-top:10px;">
            <button class="layui-btn layui-btn-normal" data-type="getCheckData">批量审核</button>
        </div>
        <div class="layui-btn-group" style="margin-top:10px;">
            <a class="layui-btn layui-btn-normal" href="create">添加手工单</a>

        </div>
        <table class="layui-table" lay-skin="nob" lay-filter="list">
            <thead>
            <tr>
                <th style="width:4%">
                    <input type="checkbox" name="checkall" lay-skin="primary" lay-filter="allChoose" value="checkall">
                </th>
                <th class="hidden-xs" style="width:10%;text-align: center">产品图片</th>
                <th class="hidden-xs" style="width:10%;text-align: center">名称</th>
                <th class="hidden-xs" style="width:10%;text-align: center">类型</th>
                <th class="hidden-xs" style="width:10%;text-align: center">原价</th>
                <th class="hidden-xs" style="width:15%;text-align: center">买家</th>
                <th class="hidden-xs" style="width:10%;text-align: center">实际支付价格</th>
                <th class="hidden-xs" style="width:10%;text-align: center">状态</th>
            </tr>
            </thead>
            <tbody class="table_list">
            @foreach ($list['data'] as $order)

                <tr>
                    <td><input type="checkbox" name="id[]" lay-skin="primary"
                               lay-filter="id" value="{{$order['id']}}"></td>
                    <td colspan="7" style="text-align: left">
                        <span class="order_sn">
                            	商城订单号：{{$order['order_sn']}}
                        </span>
                        <span class="created_at">
                            	创建时间：{{$order['created_at']}}
                        </span>
                        <span class="state_name" style="margin-right:30px;">
                        		订单来源：  @if($order['channel'] ==1) 小程序 @endif
                            @if($order['channel'] ==2) mobile @endif
                            @if($order['channel'] ==3) pc @endif
                            @if($order['channel'] ==0) 历史数据 @endif
                            @if($order['channel'] ==4) 手工单 @endif

                            @if($order['order_type'] ==2) 付邮试用 @endif
                        </span>
                        <span>
                            支付总金额：{{number_format($order['total_amount'],2)}}
                        </span>
                        <span>支付方式：
                             @if(!$order['payment_type']) 未选择支付方式 @endif
                            @if($order['payment_type'] ==1) 支付宝支付 @endif
                            @if($order['payment_type'] ==2) 微信支付 @endif
                            @if($order['payment_type'] ==3) 银联支付 @endif
                            @if($order['payment_type'] ==4) 花呗支付 @endif
                            @if($order['payment_type'] ==5) 货到付款 @endif
                            @if($order['payment_type'] ==6) 小程序支付 @endif
                        </span>
                        <span class="state_name" style="margin-right:30px;">
                        		订单状态：{{$order['status_name']}}
                        </span>

                        <span class="order_status">
                            <a class="layui-btn layui-btn-normal layui-btn-md" href="/admin/oms/order/edit?id={{$order['id']}}">订单详情</a>
                           @if($order['channel'] !=0)
                                @foreach ($order['next_action'] as $actions)
                                    @if(($order['order_state']==7 && $actions['action']=='order_verify') || ($order['order_state']==7 && $actions['action']=='order_verify_no')|| ($order['order_state']==5 && $actions['action']=='cancle_delivery'))

                                    @else
                                        <a class="layui-btn layui-btn-normal layui-btn-md do_action"
                                           data-info="{{json_encode($order)}}"
                                           data-action="{{$actions['action']}}">{{$actions['action_name']}}</a>
                                    @endif
                                @endforeach
                            @endif
                        </span>

                        @if($order['total_amount']>0 && $order['total_product_price']>0 && $order['channel'] !=0)
                            @if($order['total_amount']/$order['total_product_price']<0.65)
                                <span style="color:red"> （部分商品折扣低于65折，请注意审核 !!!）</span>
                            @endif
                        @endif
                    </td>

                </tr>
                <tr>
                    <td></td>
                    <td colspan="7" style="text-align: left">
                        <span class="order_sn">
                            	收件人：{{$order['contact']}}
                        </span>
                        <span class="created_at">
                            	手机号：{{$order['mobile']}}
                        </span>
                        <span class="state_name" style="margin-right:30px;">
                        	用户区域:{{$order['province']}} {{$order['city']}} {{$order['district']}}
                        </span>
                        <span>
                           详细地址：{{$order['address']}}
                        </span>
                        <span>
                           备注：{{$order['remark']}}
                        </span>
                        <span>
                          活动渠道：{{$order['activity_channel']}} 活动入口：{{$order['activity']}}分享ID：{{$order['share_uid']}}
                        </span>
                        <span class="layui-btn layui-btn-xs datail" data-dispaly="1">
                         折叠
                        </span>
                    </td>
                </tr>

                <tr>
                    <td></td>
                    <td colspan="7" style="padding:0;">
                        <table class="layui-table" lay-skin="nob">

                            @foreach ($order['gift_goods'] as $s=>$goods)
                                <?php $i = 0?>
                                @foreach ($goods as $k=>$good)
                                    <tr
                                            @if($order['total_amount']>0 && $order['total_product_price']>0 && $order['channel']!=0)
                                            @if($order['total_amount']/$order['total_product_price']<0.75)
                                            style="background:#FF7A6A;"
                                            @endif
                                    @else
                                            @endif>
                                        <td class="hidden-xs" style="width:10%;border:none !important;">
                                            <img src="{{$good['pic']}}" style="width:100px;height:100px;"/>
                                        </td>
                                        <td class="hidden-xs" style="width:10%">
                                            <ul>
                                                <li>
                                                    {{$good['name']}}
                                                    @if($good['type'] == 3) (固定套装)@endif
                                                </li>
                                                <li>{{$good['spec_desc']}}</li>
                                                <li>x {{$good['qty']}}</li>
                                                <li>款号：{{$good['spec_property']}}</li>
                                            </ul>
                                        </td>
                                        <td class="hidden-xs" style="width:10%">
                                            @if($good['is_free'] == 1) 小样 @endif
                                            @if($good['is_free'] == 2) 新客礼 @endif
                                            @if($good['is_gift'] ==1) 赠品 @endif
                                            @if($good['is_gift'] ==2) 全场赠品 @endif
                                            @if($good['is_gift'] ==0 && $good['is_free']==0 &&$good['type']==1)普通 @endif
                                            @if($good['type']==2 && $good['collections']) 套装 @endif
                                            @if($good['type']==2 && empty($good['collections'])) 套装子商品 @endif
                                            @if($good['type']==2 && empty($good['collections']) && $good['is_free'] == 2)
                                                新人礼套装子商品 @endif
                                            @if($good['type']==3) 固定套装 @endif

                                        </td>
                                        <td class="hidden-xs" style="width:10%">
                                            ￥{{number_format($good['original_price'],2)}}
                                        </td>
                                        @if($i==0)
                                            <td class="hidden-xs" style="width:15%"
                                                rowspan="{{count($goods)}}">
                                                <ul>
                                                    <li>{{$order['contact']}}</li>
                                                    <li>{{$order['mobile']}}</li>
                                                </ul>
                                            </td>
                                        @endif
                                        <td class="hidden-xs" style="width:10%">
                                            ￥{{number_format($good['order_amount_total'],2)}}</td>
                                        <td class="hidden-xs" style="width:10%">
                                            @if($good['status']==2) 售后 @else {{$order['state_name']}} @endif
                                        </td>
                                    </tr>
                                    <?php $i++?>
                                @endforeach

                            @endforeach

                        </table>
                    </td>
                </tr>

            @endforeach
            </tbody>
        </table>
        <div id="page"></div>
    </div>
    <script type="text/javascript">
        layui.use(['layer', 'form', 'table', 'laydate'], function () {
            var layer = layui.layer
                , form = layui.form, table = layui.table;
            var laydate = layui.laydate;
            //点击全选, 勾选
            form.on('checkbox(allChoose)', function (data) {
                var child = $(".table_list input[type='checkbox']");
                child.each(function (index, item) {
                    item.checked = data.elem.checked;
                });
                form.render('checkbox');
            });

            var $ = layui.$, active = {
                getCheckData: function () { //获取选中数据
                    var child = $(".table_list input[type='checkbox']");
                    var items = '';
                    child.each(function (index, item) {
                        items = items + item.value + ',';
                        console.log(items);
                    });

                    $.post("{{ route('backend.oms.order.batch.delivery') }}", {'items': items}, function (res) {
                        if (res.code != 1) {
                            layer.alert(res.message);
                        } else {
                            layer.alert(res.message);
                        }

                        location.reload(); //删除成功后再刷新
                    })

                }
            };

            $('.datail').on('click', function () {
                if ($(this).attr('data-dispaly') == 1) {
                    $(this).parent().parent().next().hide();
                    $(this).attr('data-dispaly', 2)
                    $(this).html('展开');

                } else {
                    $(this).parent().parent().next().show();
                    $(this).attr('data-dispaly', 1)
                    $(this).html('折叠');
                }
            });
            $('.demoTable .layui-btn').on('click', function () {
                var type = $(this).data('type');
                active[type] ? active[type].call(this) : '';
            });
            var limits = 10;
            layui.laypage.render({
                elem: 'page',
                count: "{{ $list['total'] }}",
                curr: "{{  $list['current_page'] }}",
                limit: "{{ $list['per_page'] }}",
                limits: limits ? limits : [15, 30, 40, 50],
                layout: ['count', 'prev', 'page', 'next', 'skip'],
                jump: function (obj, first) {

                    if (!first) {
                        location.href = window.location.pathname + '?' + $("#search-form").serialize() + '&page=' + obj.curr + '&limit=' + obj.limit;
                    }
                }
            });


            laydate.render({
                elem: '#start_time'  // 输出框id
                , type: 'datetime'
            });
            laydate.render({
                elem: '#end_time'  // 输出框id
                , type: 'datetime'
            });
            laydate.render({
                elem: '#start_send'  // 输出框id
                , type: 'datetime'
            });
            laydate.render({
                elem: '#end_send'  // 输出框id
                , type: 'datetime'
            });
            $('.do_action').on('click', function () {
                var row_data = eval("(" + $(this).attr('data-info') + ")");
                var do_action = $(this).attr('data-action');
                layui.use('layer', function () {
                    var layer = layui.layer;
                    layer.msg('你确定执行该操作吗？', {
                        time: 2000,//2秒自动关闭
                        btn: ['确定', '取消'],
                        offset: '100px',
                        yes: function (index) {
                            $.ajax({
                                url: "{{ route('backend.oms.order.status.update') }}",
                                data: {
                                    'order_sn': row_data.order_sn,
                                    'action': do_action,
                                    'channel': row_data.channel,
                                    'status': row_data.order_status
                                },
                                type: "Post",
                                dataType: "json",
                                success: function (data) {
                                    console.log(data);
                                    if (data.code == 1) {
                                        layer.msg('操作成功');
                                        location.reload(); //删除成功后再刷新
                                    } else {
                                        layer.msg('操作失败');
                                    }


                                },
                                error: function (data) {
                                    $.messager.alert('错误', data.msg);
                                }
                            });
                            layer.close(index);
                        }
                    });
                });
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