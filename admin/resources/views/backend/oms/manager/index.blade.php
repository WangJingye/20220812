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
        <input type="hidden" name="status" value="{{request('status','pending-refunded')}}"/>
        <div class="layui-form-item">
            <div class="layui-inline" style="position: relative">
                <label class="layui-form-label">订单筛选</label>
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input class="layui-input" id="goods_name" name="goods_name" autocomplete="off"
                               placeholder="商品名称"
                               value="{{request('goods_name')}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input class="layui-input" id="order_sn" name="order_sn" autocomplete="off" placeholder="订单号"
                               value="{{request('order_sn')}}">
                    </div>
                </div>
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input class="layui-input" id="order_sn" name="pay_order_sn" autocomplete="off"
                               placeholder="支付单号"
                               value="{{request('pay_order_sn')}}" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline" style="position: relative">
                    <label class="layui-form-label">收货人筛选</label>
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
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
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
                <div class="layui-inline">
                    <button class="layui-btn layui-btn-normal" lay-submit="search">搜索</button>
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
                <li><a href="{{$action->getStatusFilter('pending-refunded')}}"
                       class="layui-btn {{$action->getActionStatus('pending-refunded')}}">待退款</a></li>
                <li><a href="{{$action->getStatusFilter('refunded')}}"
                       class="layui-btn {{$action->getActionStatus('refunded')}}">已退款</a></li>
            </ul>
        </div>
        <table class="layui-table" lay-skin="nob" lay-filter="list">
            <thead>
            <tr>

                <th class="hidden-xs" style="width:10%;text-align: center">产品图片</th>
                <th class="hidden-xs" style="width:10%;text-align: center">名称</th>
                <th class="hidden-xs" style="width:10%;text-align: center">类型</th>
                <th class="hidden-xs" style="width:10%;text-align: center">原价</th>
                <th class="hidden-xs" style="width:15%;text-align: center">买家</th>
                <th class="hidden-xs" style="width:10%;text-align: center">实际支付价格</th>
                <th class="hidden-xs" style="width:10%;text-align: center">状态</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($list['data'] as $order)

                <tr>
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

                            @if($order['order_type'] ==2) 付邮试用 @endif
                        </span>

                        <span>支付方式：
                             @if(!$order['payment_type']) 未选择支付方式 @endif
                            @if($order['payment_type'] ==1) 支付宝支付 @endif
                            @if($order['payment_type'] ==2) 微信支付 @endif
                            @if($order['payment_type'] ==3) 银联支付 @endif
                            @if($order['payment_type'] ==4) 花呗支付 @endif
                            @if($order['payment_type'] ==5) 货到付款 @endif
                            @if($order['payment_type'] ==6) 小程序支付 @endif
                            @if($order['payment_type'] ==10) 购物金支付 @endif
                            @if($order['payment_type'] ==11) 组合支付 @endif
                        </span>
                        <span class="state_name" style="margin-right:30px;">
                        		订单状态：{{$order['status_name']}}
                        </span>
                        <span class="order_status" data-info="{{json_encode($order)}}">
                              @if($order['is_exception'] ==1)
                                <span style="color:red">异常订单</span>
                            @endif
                            <a class="layui-btn layui-btn-normal layui-btn-md" href="edit?id={{$order['id']}}">订单详情</a>
                            @foreach ($order['next_action'] as $actions)
                                @if(($order['order_state']==7 && $actions['action']=='order_verify') || ($order['order_state']==7 && $actions['action']=='order_verify_no')|| ($order['order_state']==5 && $actions['action']=='cancle_delivery'))

                                @else
                                    <button class="layui-btn layui-btn-normal layui-btn-md do_action"
                                            data-action="{{$actions['action']}}">{{$actions['action_name']}}</button>
                                @endif
                            @endforeach
                        </span>
                    </td>

                </tr>

                <tr>
                    <td colspan="7" style="padding:0;">
                        <table class="layui-table" lay-skin="nob">

                            @foreach ($order['gift_goods'] as $s=>$goods)
                                <?php $i = 0?>
                                @foreach ($goods as $k=>$good)
                                    <tr>
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
                                            {{$order['status_name']}}
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
            $('.do_action').on('click', function () {
                var row_data = eval("(" + $(this).parent('span').attr('data-info') + ")");
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