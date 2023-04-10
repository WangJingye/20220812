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
            <label class="layui-form-label">订单筛选</label>
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
            <div class="layui-inline" style="position: relative">
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
        <div class="layui-inline">
        </div>
        <div class="layui-form-item">
            <div class="layui-inline">
                <button class="layui-btn layui-btn-normal" lay-submit="search">搜索</button>
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
                <li><a href="{{$action->getStatusFilter(0)}}"
                       class="layui-btn {{$action->getActionStatus(0)}}">所有</a></li>
                <li><a href="{{$action->getStatusFilter(1)}}"
                       class="layui-btn {{$action->getActionStatus(1)}}">未审核</a></li>
                <li><a href="{{$action->getStatusFilter(2)}}"
                       class="layui-btn {{$action->getActionStatus(2)}}">等待退回</a></li>
            </ul>
        </div>
        <div class="layui-btn-group demoTable" style="margin-top:10px;">
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
                            @if($order['channel'] ==4) 手工 @endif

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
                            @if($order['order_type'] ==2) 付邮试用 @endif
                        </span>
                        <span class="state_name" style="margin-right:30px;">
                            订单状态：{{$order['status_name']}}
                        </span>
                        <div>

                        </div>
                    </td>
                </tr>
                @if(!empty($order['returnapply']))
                <tr>
                    <td></td>
                    <td colspan="7" style="text-align: left">
                        <span class="order_sn">
                            退货退款申请内容：{{$order['returnapply']['content']}}
                        </span>
                        <span class="created_at">
                            退货退款申请状态：{{$order['is_allow_return']!=0?($order['is_allow_return']==1?'同意':'拒绝'):'未审核'}}
                        </span>
                        <span class="created_at">
                            仓库确认收到退货：{{$order['is_return_wms']?'是':'否'}}
                        </span>
                    </td>
                </tr>
                @endif
                <tr>
                    <td></td>
                    <td colspan="7" style="text-align: left">
                        <a class="layui-btn layui-btn-normal layui-btn-md" href="edit?id={{$order['id']}}">订单详情</a>
                        @if($order['is_apply_return']==1 && $order['is_allow_return']==0)
                            <button class="layui-btn layui-btn-normal returnBtn" data-confirm="请确认是否同意退货退款？" lay-data="{{route('backend.oms.returnapply.returnApplyStatusChange',['orderId'=>$order['id'],'type'=>'allow'])}}">同意退货退款</button>
                            <button class="layui-btn layui-btn-warm returnBtn" data-confirm="请确认是否拒绝退货退款？" lay-data="{{route('backend.oms.returnapply.returnApplyStatusChange',['orderId'=>$order['id'],'type'=>'forbid'])}}">拒绝退货退款</button>
                        @elseif($order['is_apply_return']==1 && $order['is_allow_return']==1 && $order['is_return_wms']==0)
                            <button class="layui-btn layui-btn-normal returnBtn" data-confirm="请确认是否确认仓库收到退货？" lay-data="{{route('backend.oms.returnapply.returnApplyStatusChange',['orderId'=>$order['id'],'type'=>'confirm'])}}">确认仓库收到退货</button>
                        @elseif(in_array($order['order_status'],[3,4]))
                            <button class="layui-btn layui-btn-normal returnBtn" data-confirm="请确认是否直接同意退货退款？" lay-data="{{route('backend.oms.returnapply.returnApplyStatusChange',['orderId'=>$order['id'],'type'=>'allow'])}}">客服允许退货</button>
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
        layui.use(['layer', 'form', 'table', 'laydate','upload'], function () {
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
                    var child = $(".table_list input[type='checkbox']:checked");
                    var items = '';
                    child.each(function (index, item) {
                        items = items + item.value + ',';
                    });

                    $.post("{{ route('backend.oms.order.batch.delivery') }}", {'items': items}, function (res) {
                        if (res.code != 1) {
                            layer.msg(res.message);
                        } else {
                            layer.msg(res.message, {
                                time: 3000, end:function() {
                                    location.reload(); //删除成功后再刷新
                                }
                            });

                        }

                    })

                },
                set_exception: function () { //获取选中数据
                    var child = $(".table_list input[type='checkbox']:checked");
                    var items = '';
                    child.each(function (index, item) {
                        items = items + item.value + ',';
                        console.log(items);
                    });

                    $.post("{{ route('backend.oms.order.batch.delivery') }}", {
                        'items': items,
                        'type': 1,
                        'method': 'exception'
                    }, function (res) {
                        if (res.code != 1) {
                            layer.msg(res.message);
                        } else {

                            layer.msg('操作成功', {
                                time: 3000,
                                end:function () {
                                    location.reload(); //删除成功后再刷新
                                }
                            });
                        }
                        return false;
                    })
                },
                cancel_exception: function () { //获取选中数据
                    var child = $(".table_list input[type='checkbox']:checked");
                    var items = '';
                    child.each(function (index, item) {
                        items = items + item.value + ',';
                    });
                    $.post("{{ route('backend.oms.order.batch.delivery') }}", {
                        'items': items,
                        'type': 0,
                        'method': 'exception'
                    }, function (res) {
                        if (res.code != 1) {
                            layer.msg(res.message);
                        } else {
                            layer.msg('操作成功', {
                                time: 3000,
                                end:function () {
                                    location.reload(); //删除成功后再刷新
                                }
                            });



                        }
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
                var row_data = eval("(" + $(this).parent('span').attr('data-info') + ")");
                var do_action = $(this).attr('data-action');
                layui.use('layer', function () {
                    var layer = layui.layer;
                    layer.msg('你确定执行该操作吗？', {
                        time: 5000,//2秒自动关闭
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
                                        layer.msg('操作成功', {
                                            time: 3000,
                                            end:function() {
                                                location.reload(); //删除成功后再刷新
                                            }
                                        });


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
            lay(".returnBtn").on('click', function(e){
                let that = $(this);
                let confirm_text = that.attr('data-confirm');
                layer.confirm(confirm_text, function(index){
                    layer.close(index);
                    let url = that.attr('lay-data');
                    var loading = layer.load(1, {shade: [0.3]});
                    $.get(url, function (res) {
                        if (res.code === 1) {
                            layer.msg('操作成功', {
                                icon: 1,
                                shade: 0.3,
                            },function(){
                                layer.close(loading);
                                location.reload()
                            });
                        } else {
                            layer.msg(res.message, {
                                icon: 2,
                                shade: 0.3,
                            },function(){
                                layer.close(loading);
                                location.reload()
                            });
                        }
                    });
                });
            });
            var upload = layui.upload;
            var loading;
            upload.render({
                elem: '.uploadExcel'
                ,before: function(){
                    // layer.tips('接口地址：'+ this.url, this.item, {tips: 1});
                    loading = layer.load(1, {shade: [0.3]});
                }
                ,done: function(res, index, upload){
                    if (res.code === 1) {
                        layer.msg('操作成功', {
                            icon: 1,
                            shade: 0.3,
                        }, function(){
                            layer.close(loading);
                            location.reload()
                        });
                    } else {
                        layer.msg(res.message, {
                            icon: 2,
                            time: 2000
                        }, function(){
                            layer.close(loading);
                            location.reload()
                        });
                    }

                }
            })
            $("#export_warehouse_order").on('click', function () {
                $.post("{{ route('backend.oms.order.export',['type'=>'warehouse']) }}",[], function (res) {
                    if (res.code != 1) {
                        return false;
                    }
                    var value = res.data.value;
                    var columns = res.data.columns;
                    table.exportFile(columns, value, 'xls'); //默认导出 csv，也可以为：xls
                }, 'json');
                return false;
            });
        });
    </script>
</div>
</body>

</html>