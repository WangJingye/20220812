@extends('backend.base')
<style>
    td span {
        padding-left: 10px;
        padding-right: 10px;
    }
</style>
@section('content')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body" style="padding: 15px;">
                <form id="add_form" class="layui-form" action="">
                    <div class="layui-form-item">
                        <label class="layui-form-label">退货类型</label>
                        <div class="layui-input-inline">
                            <select name="return_type" lay-verify="required">
                                <option value="1" @if( $order['return_type']==1) selected @endif>整单退</option>
                                <option value="2" @if( $order['return_type']==2) selected @endif>部分退</option>
                            </select>
                        </div>
                        <label class="layui-form-label">原支付方式</label>
                        <div class="layui-input-inline">
                            <select name="payment_type" lay-verify="required">
                                <option value="1" @if( $order['payment_type']==1) selected @endif>支付宝支付</option>
                                <option value="2" @if( $order['payment_type']==2) selected @endif>微信支付</option>
                                <option value="3" @if( $order['payment_type']==3) selected @endif>银联支付</option>
                                <option value="4" @if( $order['payment_type']==4) selected @endif>花呗支付</option>
                                <option value="5" @if( $order['payment_type']==5) selected @endif>货到付款</option>
                                <option value="6" @if( $order['payment_type']==6) selected @endif>微信小程序</option>
                            </select>
                        </div>
                        <label class="layui-form-label">退款方式</label>
                        <div class="layui-input-inline">
                            <select name="return_pay_type" lay-verify="required" lay-filter="return_pay_type">
                                <option value="0"></option>
                                @if( $order['payment_type']!=5)
                                    <option value="1" @if( $order['return_pay_type']==1) selected @endif>原路返还</option>
                                @endif
                                <option value="2" @if( $order['return_pay_type']==2) selected @endif>银行卡</option>
                                <option value="3" @if( $order['return_pay_type']==3) selected @endif>支付宝</option>
                                <option value="4" @if( $order['return_pay_type']==4) selected @endif>无需退款</option>
                            </select>
                        </div>

                        <input name="trade_type"  value="{{$order['trade_type']}}"
                               autocomplete="off"
                               class="layui-input" type="hidden">

                    </div>
                    <div class="bank">
                        <div class="layui-form-item">
                            <label class="layui-form-label">银行</label>
                            <div class="layui-input-inline">
                                <input name="bank_name" lay-verify="" value="{{$order['bank_name']}}" autocomplete="off"
                                       class="layui-input" type="text">

                            </div>
                            <label class="layui-form-label">银行卡号</label>
                            <div class="layui-input-inline">
                                <input name="bank_no" lay-verify="" value="{{$order['bank_no']}}" autocomplete="off"
                                       class="layui-input"
                                       type="text">

                            </div>
                            <label class="layui-form-label">持卡人姓名</label>
                            <div class="layui-input-inline">
                                <input name="name" lay-verify="" value="{{$order['name']}}" autocomplete="off"
                                       class="layui-input"
                                       type="text">

                            </div>

                        </div>
                    </div>
                    <div class="alipay" >
                        <div class="layui-form-item">

                            <label class="layui-form-label">支付宝账号</label>
                            <div class="layui-input-inline">
                                <input name="account_name" lay-verify="" value="{{$order['account_name']}}" autocomplete="off"
                                       class="layui-input"
                                       type="text">

                            </div>

                        </div>
                    </div>

                    <input name="order_id" value="{{$order['order_main_id']}}" autocomplete="off"
                           class="layui-input" type="hidden">
                    <input name="action_type" value="2" autocomplete="off"
                           class="layui-input" type="hidden">


                    <div class="layui-form-item">
                        <label class="layui-form-label">快递类型</label>
                        <div class="layui-input-inline">
                            <div class="layui-input-inline">
                                <select name="express_type" >
                                    <option value="0" @if( $order['express_type']==0) selected @endif>无</option>
                                    <option value="1" @if( $order['express_type']==1) selected @endif >顺丰</option>
                                    <option value="2" @if( $order['express_type']==2) selected @endif>申通</option>
                                    <option value="3" @if( $order['express_type']==3) selected @endif>EMS</option>
                                    <option value="4" @if( $order['express_type']==4) selected @endif>宅急送</option>
                                    <option value="5" @if( $order['express_type']==5) selected @endif>百世汇通</option>
                                    <option value="6" @if( $order['express_type']==6) selected @endif>天天快递</option>
                                    <option value="7" @if( $order['express_type']==7) selected @endif >韵达</option>
                                    <option value="8" @if( $order['express_type']==8) selected @endif>JD</option>
                                </select>
                            </div>
                        </div>
                        <label class="layui-form-label">快递单号</label>
                        <div class="layui-input-inline">
                            <input name="express_no" lay-verify="" value="{{$order['express_no']}}"
                                   autocomplete="off"
                                   class="layui-input" type="text">
                        </div>
                    </div>
                    <div class="layui-form-item layui-inline">
                        <label class="layui-form-label">退款金额</label>
                        <div class="layui-input-inline">
                            <input name="return_amount" lay-verify="required|maxnumber" value="{{$order['return_amount']}}"
                                   autocomplete="off" class="layui-input" type="text" >
                        </div>

                        <label class="layui-form-label">原价</label>
                        <div class="layui-input-inline">
                            <input name="original_price" lay-verify="required" value="{{$order['original_price']}}"
                                   autocomplete="off" class="layui-input" type="text">
                        </div>
                        <label class="layui-form-label">订单金额</label>
                        <div class="layui-input-inline">
                            <input name="pay_amount" lay-verify="required" value="{{$order['pay_amount']}}"
                                   autocomplete="off" class="layui-input" type="text">
                        </div>

                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">售后状态</label>
                        <div class="layui-input-inline">
                            <select name="status" lay-verify="required">
                                <option value="1" @if( $order['status']==1) selected @endif>售后中</option>
                                <option value="2" @if( $order['status']==2) selected @endif>已完成</option>
                                <option value="3" @if( $order['status']==3) selected @endif>拒绝</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">商品信息</label>
                        <div class="layui-input-block">
                            <!-- 赠品 -->

                            <table class="layui-table" lay-skin="line" lay-size="" lay-filter="detail">

                                @foreach($gift_goods as $goods)
                                    {{--                                      <div class="test">--}}


                                    @foreach($goods as $item)

                                        @if(($item['is_gift'] ==0 && $item['is_free']==0 && $item['type']==1) || ($item['type']>1 && !empty($item['collections'])))
                                            <tr>

                                                <td><img src="{{$item['pic']}}" style="width: 50px;"/></td>
                                                <td>{{$item['name']}}(规格： {{$item['spec_desc']}})
                                                    x {{$item['qty']}}</td>
                                                <td>{{$item['original_price']}}</td>
                                                <td>{{$item['order_amount_total']}}</td>
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

                                                        @if($item['status'] ==3) 已退回 @endif
                                                        @if($item['inventory_type'] =='CC') 残次 @endif


                                                </td>

                                            </tr>
                                        @endif
                                        @if($item['type'] ==2 && empty($item['collections']))
                                            <tr>

                                                <td colspan="7">
                                                    <span style="margin-left:50px"><img src="{{$item['pic']}}"
                                                                                        style="width: 50px;"/></span>
                                                    <span>{{$item['name']}}(规格： {{$item['spec_desc']}})x {{$item['qty']}}</span>
                                                    <span>原价：{{number_format($item['original_price'],2)}} </span>
                                                    <span> 实际金额：{{number_format($item['order_amount_total'],2)}}</span>
                                                    <span>组合套装商品</span>
                                                    @if($item['status'] ==3) 已退回 @endif

                                                </td>
                                            </tr>

                                        @endif
                                        @if($item['is_gift'] == 1 || $item['is_free'] == 1)
                                            <tr>

                                                <td colspan="7">

                                                    <span style="margin-left:30px"><img src="{{$item['pic']}}"
                                                                                        style="width: 50px;"/></span>
                                                    <span>{{$item['name']}} (规格：{{$item['spec_desc']}})x {{$item['qty']}}</span>
                                                    <span> 原价：{{number_format($item['original_price'],2)}}</span>
                                                    <span> 实际金额：{{number_format($item['order_amount_total'],2)}}</span>

                                                    <span>
                                                                @if($item['is_free'] ==1) （小样） @endif
                                                        @if($item['is_gift'] ==1) （赠品） @endif
                                                            </span>

                                                    @if($item['status'] ==3) 已退回 @endif


                                                </td>

                                            </tr>
                                        @endif
                                            @if($item['is_free'] == 2&&!empty($item['collections']))
                                                <tr>
                                                    <td colspan="5" >

                                                        <span style="margin-left:30px"><img src="{{$item['pic']}}" style="width: 50px;"/></span>
                                                        <span>{{$item['name']}} (规格：{{$item['spec_desc']}})x {{$item['qty']}}</span>
                                                        <span> {{number_format($item['order_amount_total'],2)}}</span>

                                                        <span>
                                                                 新客礼
                                                            </span>

                                                        @if($item['status'] ==3) 已退回 @endif

                                                    </td>

                                                </tr>
                                            @endif

                                            @if($item['is_free'] ==2 && empty($item['collections']))
                                                <tr>
                                                    <td colspan="5" >
                                                        <span style="margin-left:30px"><img src="{{$item['pic']}}" style="width: 50px;"/></span>
                                                        <span>{{$item['name']}}(规格： {{$item['spec_desc']}})x {{$item['qty']}}</span>
                                                        <span> {{number_format($item['order_amount_total'],2)}}</span>

                                                        <span>
                                                             新客礼组合套装商品
                                                            </span>
                                                        @if($item['status'] ==3) 已退回 @endif
                                                    </td>

                                                </tr>

                                            @endif
                                            @if($item['is_gift'] == 2&&!empty($item['collections']))
                                                <tr>
                                                    <td colspan="5" >

                                                        <span style="margin-left:30px"><img src="{{$item['pic']}}" style="width: 50px;"/></span>
                                                        <span>{{$item['name']}} (规格：{{$item['spec_desc']}})x {{$item['qty']}}</span>
                                                        <span> {{number_format($item['order_amount_total'],2)}}</span>

                                                        <span>
                                                                全场赠品
                                                            </span>

                                                        @if($item['status'] ==3) 已退回 @endif

                                                    </td>

                                                </tr>
                                            @endif

                                            @if($item['is_gift'] ==2 && empty($item['collections']))
                                                <tr>
                                                    <td colspan="5" >
                                                        <span style="margin-left:30px"><img src="{{$item['pic']}}" style="width: 50px;"/></span>
                                                        <span>{{$item['name']}}(规格： {{$item['spec_desc']}})x {{$item['qty']}}</span>
                                                        <span> {{number_format($item['order_amount_total'],2)}}</span>

                                                        <span>
                                                             全场赠品组合套装商品
                                                            </span>
                                                        @if($item['status'] ==3) 已退回 @endif
                                                    </td>

                                                </tr>

                                            @endif
                                    @endforeach
                                @endforeach
                            </table>


                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label for="" class="layui-form-label">商品退货原因</label>
                        <div class="layui-input-block">
                                           <textarea style="min-height:100px;" placeholder="请输入退货信息"
                                                     class="layui-textarea"
                                                     name="question_desc"
                                                     lay-verify="required">{{$order['question_desc']}}</textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label for="" class="layui-form-label">操作备注</label>
                        <div class="layui-input-block">

                            @foreach($order['remark'] as $r)

                                <textarea style="min-height:50px;" class="layui-textarea"
                                          lay-verify="required"> 内容：{{$r['note']}} &nbsp;&nbsp;时间：{{$r['date']}}</textarea>
                            @endforeach
                            <textarea style="min-height:50px;" placeholder="请输入备注信息"
                                      class="layui-textarea"
                                      name="remark"
                                      lay-verify="required"></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button id="formSubmit" class="layui-btn" lay-submit lay-filter="formSubmit">提交</button>
                        </div>
                    </div>


                </form>
            </div>
        </div>
    </div>
@endsection

<script>
    @section('layui_script')
    form.verify({
        maxnumber: function (value) {
            if(value){
                var amount = parseFloat($('input[name=pay_amount]').val());
                if(amount<value){
                    return '退款金额异常';
                }

            }
        },

    });

    form.on('select(return_pay_type)', function (data) {
        if (data.value == 1) {
            $('.bank').hide();
            $('.alipay').hide();

        }
        if (data.value == 2) {
            $('.bank').show();
            $('.alipay').hide();

        }
        if (data.value == 3) {
            $('.bank').hide();
            $('.alipay').show();
        }
        if (data.value == 4) {
            $('.bank').hide();
            $('.alipay').hide();
        }
        form.render('select');
    });


    form.on('submit(formSubmit)', function (data) {
        console.log(data.field)
        $.post("{{ route('backend.sales.order.after.action') }}", data.field, function (res) {
            if (res.code != 1) {
                layer.msg(res.message, {icon: 5, anim: 6});
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