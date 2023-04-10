@extends('backend.base') @section('content')
    <style>
        caption {
            text-align: left;
            font-weight: 800;
        }

        .layui-form input[type="checkbox"], .layui-form input[type=radio], .layui-form select {
            display: none;
        }

        .layui-form-item {
            white-space: nowrap !important;
        }

    </style>
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body" style="padding: 15px;">
                <ul class="layui-tab-title"></ul>
                <div class="layui-tab layui-tab-card">
                    <ul class="layui-tab-title">

                        <li onclick="window.history.go(-1)">订单列表</li>
                        <li class="layui-this">订单详情</li>
                    </ul>
                    <div class="layui-tab-content" style="">
                        <div class="layui-tab-item layui-show">
                            <div class="order-information">
                                <div class="layui-row layui-row layui-col-space20">
                                    <div class="layui-col-md12">
                                        <table class="layui-table" lay-skin="line" lay-size="">
                                            <colgroup>
                                                <col width="20%">
                                                <col width="20%">
                                                <col width="20%">
                                                <col width="20%">
                                                <col width="20%">
                                            </colgroup>
                                            <tbody>
                                            <tr>
                                                <td>订单编号：{{$order['order_sn']}}</td>
                                                <td>订单状态：{{$order['status_name']}}</td>
                                                <td>支付方式：
                                                    @if($order['payment_type'] ==1) 支付宝 @endif
                                                    @if($order['payment_type'] ==2) 微信 @endif
                                                    @if($order['payment_type'] ==3) 银联 @endif
                                                    @if($order['payment_type'] ==4) 花呗 @endif
                                                    @if($order['payment_type'] ==5) 货到付款 @endif
                                                    @if($order['payment_type'] ==6) 小程序支付 @endif


                                                </td>
                                                @if($order['order_type'] ==2)  <td>付邮试用</td> @endif
                                                <td>下单时间：{{$order['created_at']}}</td>
                                                <td>
                                                    @if($order['channel'] !=0)
                                                        @if($order['after_sale_id']>0)
                                                            <span order_id="{{$order['id']}}"
                                                                  class="afterSellInfo layui-btn layui-btn-normal layui-btn-md">
													售后中查看售后单信息
                                                    </span>
                                                        @endif

                                                        @if($order['after_sale_id']==0 && $order['order_status']>5 &&$order['order_status']!=7)
                                                            <span style="display: none" order_id="{{$order['id']}}" class="afterSell layui-btn layui-btn-normal layui-btn-md">发起售后</span>
                                                        @endif
                                                    @else
                                                        <span>订单类型：历史订单</span>
                                                    @endif
                                                </td>

                                            </tr>

                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>

                            <div class="customer-information">
                                <div class="layui-row layui-row">
                                    <table class="layui-table" lay-skin="line" lay-size="">
                                        <colgroup>
                                            <col width="20%">
                                            <col width="20%">
                                            <col width="20%">
                                            <col width="20%">
                                            <col width="20%">
                                        </colgroup>
                                        <tbody>
                                        <tr>
                                            <td>会员编号：{{$order['pos_id']}}</td>
                                            <td>会员id：{{$order['user_id']}}</td>
                                            <td>订单商品总件数：{{$order['total_num']}}</td>
                                            <td><span></span></td>
                                            <td><span></span></td>
                                            <td><span></span></td>

                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="order-items-information" style="margin-top:20px;">
                            <div class="layui-row layui-row">

                                <table class="layui-table" lay-skin="line" lay-size="">
                                    <caption>商品信息</caption>
                                    <colgroup>
                                        <col width="150">
                                        <col>
                                    </colgroup>

                                </table>

                                <!-- 赠品 -->
                                <table class="layui-table" lay-skin="line" lay-size="" lay-filter="detail">

                                    @foreach($order['gift_goods'] as $goods)
                                        @foreach($goods as $item)
                                            @if(($item['is_gift'] ==0 && $item['is_free']==0 && $item['type']==1) || ($item['is_free']!=2 &&$item['type']>1 && !empty($item['collections'])))
                                                <tr>
                                                    <td><img src="{{$item['pic']}}" style="width: 50px;"/></td>
                                                    <td>{{$item['name']}}(规格： {{$item['spec_desc']}})
                                                        x {{$item['qty']}}</td>
                                                    <td>{{number_format($item['order_amount_total'],2)}}</td>
                                                    <td>
                                                        @if($item['type'] ==1) 普通 @endif
                                                        @if($item['type'] ==2) 组合套装 @endif
                                                        @if($item['type'] ==3) 固定套装 @endif
                                                    </td>
                                                    <td>

                                                        @if(isset($item['applied_rule_ids']) && is_array($item['applied_rule_ids']))
                                                            @foreach($item['applied_rule_ids'] as $rule)

                                                                优惠规则： {{$rule['rule_name']}} {{$rule['discount']??''}}
                                                                <br>

                                                            @endforeach
                                                        @endif
                                                    </td>
                                                    <td>
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($item['type'] ==2 && empty($item['collections']) )
                                                <tr>
                                                    <td colspan="4">
                                                        <span style="margin-left:30px"><img src="{{$item['pic']}}"
                                                                                            style="width: 50px;"/></span>
                                                        <span>{{$item['name']}}(规格： {{$item['spec_desc']}})x {{$item['qty']}}</span>
                                                        <span> {{number_format($item['order_amount_total'],2)}}</span>

                                                        <span>
                                                             组合套装商品
                                                            </span>
                                                    </td>
                                                    <td>
                                                        @if(isset($item['applied_rule_ids']) && is_array($item['applied_rule_ids']))
                                                            @foreach($item['applied_rule_ids'] as $rule)

                                                                优惠规则： {{$rule['rule_name']}} {{$rule['discount']??''}}
                                                                <br>

                                                            @endforeach
                                                        @endif
                                                    </td>

                                                </tr>

                                            @endif
                                            @if($item['is_gift'] == 1 || $item['is_free'] == 1)
                                                <tr>
                                                    <td colspan="4">

                                                        <span style="margin-left:30px"><img src="{{$item['pic']}}"
                                                                                            style="width: 50px;"/></span>
                                                        <span>{{$item['name']}} (规格：{{$item['spec_desc']}})x {{$item['qty']}}</span>
                                                        <span> {{number_format($item['order_amount_total'],2)}}</span>

                                                        <span>
                                                                @if($item['is_free'] ==1) （小样） @endif
                                                            @if($item['is_gift'] ==1) （赠品） @endif
                                                            </span>


                                                    </td>
                                                    <td>
                                                        @if(isset($item['applied_rule_ids']) && is_array($item['applied_rule_ids']))
                                                            @foreach($item['applied_rule_ids'] as $rule)

                                                                优惠规则： {{$rule['rule_name']}} {{$rule['discount']??''}}
                                                                <br>

                                                            @endforeach
                                                        @endif
                                                    </td>

                                                </tr>
                                            @endif

                                            @if($item['is_free'] == 2&&!empty($item['collections']))
                                                <tr>

                                                    <td colspan="4">

                                                        <span style="margin-left:30px"><img src="{{$item['pic']}}"
                                                                                            style="width: 50px;"/></span>
                                                        <span>{{$item['name']}} (规格：{{$item['spec_desc']}})x {{$item['qty']}}</span>
                                                        <span> {{number_format($item['order_amount_total'],2)}}</span>

                                                        <span>
                                                                 @if($item['is_free'] == 2) 新客礼 @endif
                                                            </span>


                                                    </td>
                                                    <td>
                                                        @if(isset($item['applied_rule_ids']) && is_array($item['applied_rule_ids']))
                                                            @foreach($item['applied_rule_ids'] as $rule)

                                                                优惠规则： {{$rule['rule_name']}} {{$rule['discount']??''}}
                                                                <br>

                                                            @endforeach
                                                        @endif
                                                    </td>

                                                </tr>
                                            @endif

                                            @if($item['is_free'] ==2 && empty($item['collections']))
                                                <tr>
                                                    <td colspan="5">
                                                        <span style="margin-left:30px"><img src="{{$item['pic']}}"
                                                                                            style="width: 50px;"/></span>
                                                        <span>{{$item['name']}}(规格： {{$item['spec_desc']}})x {{$item['qty']}}</span>
                                                        <span>{{number_format($item['order_amount_total'],2)}}</span>

                                                        <span>
                                                             新客礼组合套装商品
                                                            </span>
                                                    </td>

                                                </tr>

                                            @endif

                                            @if($item['is_gift'] == 2&&!empty($item['collections']))
                                                <tr>
                                                    <td colspan="5">

                                                        <span style="margin-left:30px"><img src="{{$item['pic']}}"
                                                                                            style="width: 50px;"/></span>
                                                        <span>{{$item['name']}} (规格：{{$item['spec_desc']}})x {{$item['qty']}}</span>
                                                        <span> {{number_format($item['order_amount_total'],2)}}</span>

                                                        <span>
                                                               全场赠品
                                                            </span>


                                                    </td>

                                                </tr>
                                            @endif

                                            @if($item['is_gift'] ==2 && empty($item['collections']))
                                                <tr>
                                                    <td colspan="5">
                                                        <span style="margin-left:30px"><img src="{{$item['pic']}}"
                                                                                            style="width: 50px;"/></span>
                                                        <span>{{$item['name']}}(规格： {{$item['spec_desc']}})x {{$item['qty']}}</span>
                                                        <span>{{number_format($item['order_amount_total'],2)}}</span>

                                                        <span>
                                                             全场赠品组合套装商品
                                                            </span>
                                                    </td>

                                                </tr>

                                            @endif
                                        @endforeach


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
                                            <td><strong>商品原价：</strong></td>
                                            <td>￥{{number_format($order['total_product_price'],2)}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>商品快递费：</strong></td>
                                            <td>￥{{number_format($order['total_ship_fee'],2)}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>实付价：</strong></td>
                                            <td>￥{{number_format($order['total_amount'],2)}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <form id="update_form" class="layui-form" action="">
                            <input class="layui-input" type="hidden" name="id" value="{{$order['id']}}">
                            <input class="layui-input" type="hidden" name="order_sn" value="{{$order['order_sn']}}">
                            <div class="pay-information">
                                <div class="layui-row layui-row layui-col-space20">
                                    <div class="layui-col-md6">
                                        <table class="layui-table" lay-skin="line" lay-size="">
                                            <caption>配送信息</caption>
                                            <colgroup>
                                                <col width="100%">
                                            </colgroup>
                                            <tbody>
                                            <tr>
                                                <input type="hidden" name="type" value="1">
                                                <td>联系人 ：<input class="layui-input" style="width:150px;display: inline"
                                                                type="text" name="contact"
                                                                value="{{$order['contact']}}" lay-verify="required">
                                                    <input class="layui-input" style="width:150px;display: inline"
                                                           type="text" name="mobile"
                                                           value="{{$order['mobile']}}" lay-verify="required"></td>
                                            </tr>
                                            <tr>
                                                <td>地址区域
                                                    ：<input class="layui-input" style="width:100px;display: inline"
                                                            type="text" name="province" value="{{$order['province']}}"
                                                            lay-verify="required">
                                                    <input class="layui-input" style="width:100px;display: inline"
                                                           type="text" name="city" value="{{$order['city']}}"
                                                           lay-verify="required">
                                                    <input class="layui-input" style="width:120px;display: inline"
                                                           type="text" name="district" value="{{$order['district']}}"
                                                           lay-verify="required">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>详细地址
                                                    <textarea style="display:inline" placeholder="请输入详细地址信息"
                                                              class="layui-textarea layui-input"
                                                              name="address"
                                                              lay-verify="required">{{$order['address']}}</textarea>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>快递名称 ：{{$order['delivery_mode']}}</td>
                                            </tr>
                                            <tr>
                                                <td> 快递单号 ：{{$order['express_no']}}</td>
                                            </tr>


                                            <tr>
                                                <td style="display:none">备注 ：<textarea style="display:inline" placeholder="请输入备注信息"
                                                                  class="layui-textarea layui-input"
                                                                  name="remark">{{$order['remark']}}</textarea>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="layui-col-md6" style="display: none">
                                        <table class="layui-table" lay-skin="line" lay-size="">
                                            <caption>卡片信息</caption>
                                            <colgroup>
                                                <col width="100%">
                                            </colgroup>
                                            <tbody>
                                            <tr>
                                                <td>送给：<input class="layui-input" style="width:250px;display: inline"
                                                              type="text" name="card_to" value="{{$order['card_to']}}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>内容 <textarea style="display:inline" placeholder="请输入备注信息"
                                                                 class="layui-textarea layui-input"
                                                                 name="card_content">{{ $order['card_content']??old('card_content') }}</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>来自：<input class="layui-input" style="width:250px;display: inline"
                                                              type="text" name="card_from"
                                                              value="{{$order['card_from']}}"></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </form>
                        <div class="information">
                            <div class="layui-row layui-row layui-col-space20">
                                <div class="layui-col-md6">
                                    <form id="update_form" class="layui-form" action="">
                                        <table class="layui-table" lay-skin="line" lay-size="">
                                            <caption>发票信息:</caption>
                                            <tbody>
                                            <tr>
                                                <td><strong>开票类型：</strong></td>
                                                <td>{{$order['order_invoice']['type']}}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>抬头：</strong></td>
                                                <td>{{$order['order_invoice']['title']}}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>纳税人识别号：</strong></td>
                                                <td>{{$order['order_invoice']['number']}}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>邮箱：</strong></td>
                                                <td>{{$order['order_invoice']['email']}}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                                <div class="layui-col-md6">
                                    <table class="layui-table" lay-skin="line" lay-size="">
                                        <caption>订单状态更新:</caption>
                                        <colgroup>
                                            <col width="100%">
                                        </colgroup>
                                        <tbody>
                                        @foreach($order['order_status_log'] as $status)
                                            <tr>
                                                <td>{{$status['created_at']}}  {{$status['desc']}}</td>
                                            </tr>
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
            //自定义验证规则
            form.render();
            form.verify({
                number_code: function(value,item){ //value：表单的值、item：表单的DOM对象
                    if(!(/^[a-zA-Z0-9]{10,20}$/.test(value))){
                        return '税号必须是字母或数字格式';
                    }
                }
            });
            var order_sn = "{{$order['order_sn']}}";
            $('.do_action').on('click', function () {
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
                                    'order_sn': order_sn,
                                    'action': do_action,
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
            $('.view_express_info').on('click', function () {
                var ship_method = $(this).data('ship_method');
                var shipping_id = $(this).data('shipping_id');
                var data = {"ship_method": ship_method, "shipping_id": shipping_id};
                $.post("{{ route('backend.sales.order.getExpressInfo') }}", data, function (res) {
                    var msg = '';
                    if (res.code != 1) {
                        msg = '暂无物流信息';
                    } else {
                        msg = res.data.msg;
                    }
                    layer.open({
                        type: 1
                        , offset: 't'
                        , id: 'csslayerDemo'
                        , content: '<div style="padding: 20px;">' + msg + '</div>'
//                 ,btn: '关闭全部'
                        , btnAlign: 'c' //按钮居中
                        , shade: 0 //不显示遮罩
                        , yes: function () {
                            layer.closeAll();
                        }
                    });
                    return false;
                }, 'json');
            });

            $('#delete_free').on('click', function () {
                var items = '';

                $(".layui-table input[type='checkbox']").each(function (index, item) {
                    if (item.checked == true) {
                        items = items + item.value + ',';
                    }

                });

                var order_sn = "{{$order['order_sn']}}";

                console.log(items);
// return false;
                var data = {"order_sn": order_sn, "items": items, "type": 1};
                $.post("{{ route('backend.oms.order.free') }}", data, function (res) {
                    var msg = '';
                    if (res.code == 1) {
                        msg = '成功';
                    } else {
                        msg = res.message;
                    }
                    layer.open({
                        type: 1
                        , offset: 't'
                        , shade: true
                        , area: ['180px', '150px']
                        , id: 'csslayerDemo'
                        , scrollbar: false
                        , content: '<div style="padding: 20px;">' + msg + '</div>'
//                 ,btn: '关闭全部'
                        , btnAlign: 'c' //按钮居中
                        , shade: 0.3 //不显示遮罩
                        , end: function () {
                            location.reload();
                        }

                    });
                    return false;
                }, 'json');
            });
            $('#add_free').on('click', function () {
                var order_sn = "{{$order['order_sn']}}";
                var skus = $('input[name="skus"]').val();
                var data = {"order_sn": order_sn, "skus": skus};
                $.post("{{ route('backend.oms.order.free') }}", data, function (res) {
                    var msg = '';
                    if (res.code == 1) {
                        msg = '成功';
                    } else {
                        msg = res.message;
                    }
                    layer.open({
                        type: 1
                        , offset: 't'
                        , shade: true
                        , area: ['180px', '150px']
                        , id: 'csslayerDemo'
                        , scrollbar: false
                        , content: '<div style="padding: 20px;">' + msg + '</div>'
//                 ,btn: '关闭全部'
                        , btnAlign: 'c' //按钮居中
                        , shade: 0.3 //不显示遮罩
                        , end: function () {
                            location.reload();
                        }

                    });
                    return false;
                }, 'json');
            });

            $('.ruleDetail').on('click', function () {
                layer.open({
                    title: '规则详情',
                    type: 1,
                    content: '<pre style="padding: 20px;">' + $(this).data('detail') + '</pre>',
                    area: ['500px', '800px'],
                    offset: 't',
                    shadeClose: true,
                    fixed: true,
                    scrollbar: false,
                });
            });

            $('.afterSell').on('click', function () {
                var order_id = $(this).attr('order_id');

                layer.open({
                    title: '售后',
                    type: 2,
                    content: "{{ route('backend.sales.order.add') }}?type=1&id=" + order_id,
                    area: ['80%', '100%'],
                    offset: 't',
                    shadeClose: true,
                    fixed: true,
                    scrollbar: false,
                    end: function () {
                        location.reload();
                    }
                });
            });
            $('.afterSellInfo').on('click', function () {
                var order_id = $(this).attr('order_id');

                layer.open({
                    title: '售后',
                    type: 2,
                    content: "{{ route('backend.sales.order.edit') }}?type=2&id=" + order_id,
                    area: ['80%', '100%'],
                    offset: 't',
                    shadeClose: true,
                    fixed: true,
                    scrollbar: false,
                    end: function () {
                        location.reload();
                    }
                });
            });


            $('.refund').on('click', function () {
                var order_id = $(this).attr('order_id');
                var type = $(this).attr('refund_type');
                layer.open({
                    title: '退款',
                    type: 2,
                    content: "{{ route('backend.sales.order.refund.info') }}?type=" + type + "&id=" + order_id,
                    area: ['80%', '100%'],
                    offset: 't',
                    shadeClose: true,
                    fixed: true,
                    scrollbar: false,
                    end: function () {
                        location.reload();
                    }
                });
            });
            form.on('submit(formSubmit1)', function (data) {

// return false;
                $.post("{{ route('backend.oms.order.update') }}", data.field, function (res) {
                    if (res.code == 1) {
                        layer.open({
                            type: 1
                            ,
                            offset: 't'
                            ,
                            skin: 'layui-layer-molv'
                            ,
                            title: '提示'
                            ,
                            id: 'csslayerDemo'
                            ,
                            content: '<div style="padding: 50px;width:200px;height:60px;"><H2><i class="layui-icon layui-icon-face-smile" style="font-size: 30px; color: #1E9FFF;"></i>  ' + res.message + '</H2></div>'
//                 ,btn: '关闭全部'
                            ,
                            btnAlign: 'c' //按钮居中
                            ,
                            shade: 0.3 //不显示遮罩
                            ,
                            yes: function () {
                                layer.closeAll();
                            }
                        });
                        location.reload();
                    } else {

                        layer.open({
                            type: 1
                            ,
                            offset: 't'
                            ,
                            skin: 'layui-layer-molv'
                            ,
                            id: 'csslayerDemo'
                            ,
                            content: '<div style="padding: 50px;width:200px;height:60px;"><H2><i class="layui-icon layui-icon-face-smile" style="font-size: 30px; color:red;"></i>  ' + res.message + '</H2></div>'
//                 ,btn: '关闭全部'
                            ,
                            btnAlign: 'c' //按钮居中
                            ,
                            shade: 0.3 //不显示遮罩
                            ,
                            yes: function () {
                                layer.closeAll();
                            }
                        });
                    }
                }, 'json');
                return false;
            })
            form.on('submit(formSubmit2)', function (data) {
                console.log(data.field);

                $.post("{{ route('backend.oms.order.update') }}", data.field, function (res) {
                    if (res.code == 1) {
                        layer.open({
                            type: 1
                            ,
                            offset: 't'
                            ,
                            title: '提示'
                            ,
                            skin: 'layui-layer-molv'
                            ,
                            id: 'csslayerDemo'
                            ,
                            content: '<div style="padding: 50px;width:200px;height:60px;"><H2><i class="layui-icon layui-icon-face-smile" style="font-size: 30px; color: #1E9FFF;"></i>  ' + res.message + '</H2></div>'
//                 ,btn: '关闭全部'
                            ,
                            btnAlign: 'c' //按钮居中
                            ,
                            shade: 0.3 //不显示遮罩
                            ,
                            yes: function () {
                                layer.closeAll();
                            }
                        });
                        location.reload();
                    } else {

                        layer.open({
                            type: 1
                            ,
                            offset: 't'
                            ,
                            title: '提示'
                            ,
                            skin: 'layui-layer-molv'
                            ,
                            id: 'csslayerDemo'
                            ,
                            content: '<div style="padding: 50px;width:200px;height:60px;"><H2><i class="layui-icon layui-icon-face-smile" style="font-size: 30px; color: red;"></i>  ' + res.message + '</H2></div>'
//                 ,btn: '关闭全部'
                            ,
                            btnAlign: 'c' //按钮居中
                            ,
                            shade: 0.3 //不显示遮罩
                            ,
                            yes: function () {
                                layer.closeAll();
                            }
                        });
                    }

                }, 'json');
                return false;
            });
            @endsection
        </script>