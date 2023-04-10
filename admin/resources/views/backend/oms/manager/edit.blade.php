@extends('backend.base') @section('content')
    <style>
        caption {
            text-align: left;
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
                        <li><a href="{{url('admin/oms/order/manager/index')}}">订单列表</a></li>
                        <li class="layui-this">订单详情</li>
                    </ul>
                    <div class="layui-tab-content" style="">
                        <div class="layui-tab-item layui-show">
                            <div class="order-information">
                                <div class="layui-row layui-row layui-col-space20">
                                    <div class="layui-col-md12">
                                        <table class="layui-table" lay-skin="line" lay-size="">
                                            <colgroup>
                                                <col width="10%">
                                                <col width="5%">
                                                <col width="5%">
                                                <col width="10%">
                                                <col width="30%">
                                            </colgroup>
                                            <tbody>
                                            <tr>
                                                <td>订单编号：{{$order['order_sn']}}</td>
                                                <td>订单状态：{{$order['status_name']}}</td>
                                                <td>支付方式：
                                                    @if($order['payment_type'] ==1) 支付宝支付 @endif
                                                    @if($order['payment_type'] ==2) 微信支付 @endif
                                                    @if($order['payment_type'] ==3) 银联支付 @endif
                                                    @if($order['payment_type'] ==4) 花呗支付 @endif
                                                    @if($order['payment_type'] ==5) 货到付款 @endif
                                                    @if($order['payment_type'] ==6) 小程序支付 @endif

                                                </td>
                                                <td>下单时间：{{$order['created_at']}}</td>
                                                <td>
                                                    @if($order['after_sale_id']>0)
                                                        <span order_id="{{$order['id']}}"
                                                              class="afterSellInfo layui-btn layui-btn-normal layui-btn-md">
													售后中查看售后单信息
                                                    </span>
                                                    @endif
                                                    @if(($order['order_status']==5 && $order['is_return_wms']==1))
                                                        <span order_id="{{$order['id']}}" refund_type="2"
                                                              class="refund layui-btn layui-btn-normal layui-btn-md">
													直接退款
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
                                <div class="layui-row layui-row">
                                    <table class="layui-table" lay-skin="line" lay-size="">
                                        <colgroup>
                                            <col>
                                        </colgroup>
                                        <tbody>
                                        <tr>
                                            <td>会员编号：{{$order['pos_id']}}</td>
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
                                            @if(($item['is_gift'] ==0 && $item['is_free']==0 && $item['type']==1) || ($item['type']>1 && !empty($item['collections'])))
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

                                                </tr>
                                            @endif
                                            @if($item['type'] ==2 && empty($item['collections']))
                                                <tr>
                                                    <td colspan="5">
                                                        <span style="margin-left:30px"><img src="{{$item['pic']}}"
                                                                                            style="width: 50px;"/></span>
                                                        <span>{{$item['name']}}(规格： {{$item['spec_desc']}})x {{$item['qty']}}</span>
                                                        <span> {{number_format($item['order_amount_total'],2)}}</span>

                                                        <span>
                                                             组合套装商品
                                                            </span>
                                                    </td>

                                                </tr>

                                            @endif
                                            @if($item['is_gift'] == 1 || $item['is_free'] == 1)
                                                <tr>
                                                    <td colspan="5">

                                                        <span style="margin-left:30px"><img src="{{$item['pic']}}"
                                                                                            style="width: 50px;"/></span>
                                                        <span>{{$item['name']}} (规格：{{$item['spec_desc']}})x {{$item['qty']}}</span>
                                                        <span> {{number_format($item['order_amount_total'],2)}}</span>

                                                        <span>
                                                                @if($item['is_free'] ==1) （小样） @endif
                                                            @if($item['is_gift'] ==1) （赠品） @endif
                                                            </span>


                                                    </td>

                                                </tr>
                                            @endif

                                            @if($item['is_free'] == 2&&!empty($item['collections']))
                                                <tr>
                                                    <td colspan="5">

                                                        <span style="margin-left:30px"><img src="{{$item['pic']}}"
                                                                                            style="width: 50px;"/></span>
                                                        <span>{{$item['name']}} (规格：{{$item['spec_desc']}})x {{$item['qty']}}</span>
                                                        <span> {{number_format($item['order_amount_total'],2)}}</span>

                                                        <span>
                                                                 @if($item['is_free'] == 2) 新客礼 @endif
                                                            </span>


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
                                            <td><strong>商品包装费：</strong></td>
                                            <td>￥{{number_format($order['total_wrap_fee'],2)}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>商品快递费：</strong></td>
                                            <td>￥{{number_format($order['total_ship_fee'],2)}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>积分抵扣：</strong></td>
                                            <td>￥{{number_format($order['total_point_discount'],2)}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>使用积分：</strong></td>
                                            <td>￥{{number_format($order['used_points'],2)}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>优惠抵扣金额：</strong></td>
                                            <td>￥{{number_format($order['total_discount'],2)}}</td>
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

                                                <td>联系人 ：<input class="layui-input" style="width:150px;display: inline"
                                                                type="text" name="contact"
                                                                value="{{$order['contact']}}">
                                                    <input class="layui-input" style="width:150px;display: inline"
                                                           type="text" name="mobile"
                                                           value="{{$order['mobile']}}"></td>
                                            </tr>
                                            <tr>
                                                <td>地址区域
                                                    ：<input class="layui-input" style="width:100px;display: inline"
                                                            type="text" name="province" value="{{$order['province']}}">
                                                    <input class="layui-input" style="width:100px;display: inline"
                                                           type="text" name="city" value="{{$order['city']}}">
                                                    <input class="layui-input" style="width:120px;display: inline"
                                                           type="text" name="district" value="{{$order['district']}}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>详细地址
                                                    ：<input class="layui-input" style="width:250px;display: inline"
                                                            type="text" name="address" value="{{$order['address']}}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>备注 ：<input class="layui-input" style="width:320px;display: inline"
                                                               type="text" name="remark" value="{{$order['remark']}}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>快递名称 ：{{$order['delivery_mode']}}</td>
                                            </tr>
                                            <tr>
                                                <td> 快递单号 ：{{$order['express_no']}}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="layui-col-md6">
                                        <table class="layui-table" lay-skin="line" lay-size="">
                                            <caption>发票信息:</caption>
                                            <colgroup>
                                                <col width="100%">
                                            </colgroup>
                                            <tbody>
                                            <tr>
                                                <td>开票类型：
                                                    <div class="layui-input-inline">
                                                        <select name="order_invoice[type]" lay-verify="required">
                                                            <option value="person"
                                                                    @if( @$order['order_invoice']['type']=='person') selected @endif>
                                                                个人
                                                            </option>
                                                            <option value="company"
                                                                    @if( @$order['order_invoice']['type']=='company') selected @endif>
                                                                公司
                                                            </option>
                                                            <option value=""
                                                                    @if(!@$order['order_invoice']['type']) selected @endif></option>
                                                        </select>
                                                    </div>

                                                </td>
                                            </tr>

                                            <tr>
                                                <td>抬头：
                                                    <input class="layui-input" style="width:250px;display: inline"
                                                           type="text" name="order_invoice[title]"
                                                           value="{{@$order['order_invoice']['title']}}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>纳税人识别号：
                                                    <input class="layui-input" style="width:250px;display: inline"
                                                           type="text" name="order_invoice[number]"
                                                           value="{{@$order['order_invoice']['number']}}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>邮箱：
                                                    <input class="layui-input" style="width:250px;display: inline"
                                                           type="text" name="order_invoice[email]"
                                                           value="{{@$order['order_invoice']['email']}}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>预开票金额：{{@$order['order_invoice']['total_free']}}</td>
                                            </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="information">
                                <div class="layui-row layui-row layui-col-space20">
                                    <div class="layui-col-md6">
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


                        </form>

                    </div>
                </div>
            </div>
        </div>
        @endsection

        <script>
            @section('layui_script')

            form.on('submit(formSubmit)', function (data) {


            });
            // layui.use(['layer', 'form', 'table', 'laydate'], function () {
            //
            //     table = layui.table;
            //     //监听行工具事件
            //     table.on('tool(detail)', function (obj) { //注：tool 是工具条事件名，test 是 table 原始容器的属性 lay-filter="对应的值"
            //
            //     });
            // });
            //自定义验证规则
            form.verify({});
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
            form.on('submit(formSubmit)', function (data) {
                console.log(data.field);
                // return false;
                $.post("{{ route('backend.oms.order.update') }}", data.field, function (res) {
                    if (res.code != 1) {
                        layer.msg(res.message, {icon: 5, anim: 6, offset: '300px'});
                        return false;
                    } else {
                        layer.msg(res.message, function () {
                            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                            parent.layer.close(index); //再执行关闭
                        });

                    }
                }, 'json');
                return false;
            });
            @endsection
        </script>