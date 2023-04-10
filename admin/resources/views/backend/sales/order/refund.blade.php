@extends('backend.base')
@section('content')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body" style="padding: 15px;">
                <form id="add_form" class="layui-form"  action="">
                    <div class="layui-form-item">
                        <label class="layui-form-label">退货类型</label>
                        <div class="layui-input-inline">
                            <select name="return_type" lay-verify="required" readonly>
                                <option value="1" @if( $order['return_type']==1) selected @endif>整单退</option>
                                <option value="2" @if( $order['return_type']==2) selected @endif>部分退</option>
                            </select>
                        </div>
                        <label class="layui-form-label">原支付方式</label>
                        <div class="layui-input-inline">
                            <select name="payment_type" lay-verify="required" readonly>
                                <option value="0" @if( $order['payment_type']==0) selected @endif></option>
                                <option value="2" @if( $order['payment_type']==2) selected @endif>微信支付</option>
                                <option value="3" @if( $order['payment_type']==3) selected @endif>银联支付</option>
                                <option value="4" @if( $order['payment_type']==4) selected @endif>花呗支付</option>
                                <option value="5" @if( $order['payment_type']==5) selected @endif>货到付款</option>
                                <option value="6" @if( $order['payment_type']==6) selected @endif>微信小程序</option>
                            </select>
                        </div>
                        <input name="trade_type"  value="{{$order['trade_type']}}"
                               autocomplete="off"
                               class="layui-input" type="hidden">

                        <label class="layui-form-label">退款方式</label>
                        <div class="layui-input-inline">
                            <select name="return_pay_type" lay-verify="required" readonly>
                                <option value="0"></option>
                                @if( $order['payment_type']!=5)
                                    <option value="1" @if( $order['return_pay_type']==1) selected @endif>原路返还</option>
                                @endif
                                <option value="2" @if( $order['return_pay_type']==2) selected @endif>银行卡</option>
                                <option value="3" @if( $order['return_pay_type']==3) selected @endif>支付宝</option>
                                <option value="4" @if( $order['return_pay_type']==4) selected @endif>无需退款</option>
                            </select>
                        </div>

                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">银行</label>
                        <div class="layui-input-inline">
                            <input name="bank_name" lay-verify="" value="{{$order['bank_name']}}" autocomplete="off"
                                   class="layui-input" type="text" readonly>

                        </div>
                        <label class="layui-form-label">银行卡号</label>
                        <div class="layui-input-inline">
                            <input name="bank_no" lay-verify="" value="{{$order['bank_no']}}" autocomplete="off" class="layui-input"
                                   type="text" readonly>

                        </div>
                        <label class="layui-form-label">持卡人姓名</label>
                        <div class="layui-input-inline">
                            <input name="name" lay-verify="" value="{{$order['name']}}" autocomplete="off" class="layui-input"
                                   type="text" readonly>

                        </div>

                    </div>
                    <div class="alipay">
                        <div class="layui-form-item">

                            <label class="layui-form-label">支付宝账号</label>
                            <div class="layui-input-inline">
                                <input name="account_name" lay-verify="" value="{{$order['account_name']}}" autocomplete="off"
                                       class="layui-input"
                                       type="text" readonly>

                            </div>

                        </div>
                    </div>

                    <input name="order_id" value="{{$order['order_main_id']}}" autocomplete="off"
                           class="layui-input" type="hidden">

                    <input name="type" value="2" autocomplete="off"
                           class="layui-input" type="hidden">
                    <div class="layui-form-item layui-inline">
                        <label class="layui-form-label">退款金额</label>
                        <div class="layui-input-inline">
                            <input name="return_amount" lay-verify="required" value="{{$order['return_amount']}}"
                                   autocomplete="off" class="layui-input" type="text" readonly>
                        </div>

                        <label class="layui-form-label">原价</label>
                        <div class="layui-input-inline">
                            <input name="original_price" lay-verify="required" value="{{$order['original_price']}}"
                                   autocomplete="off" class="layui-input" type="text" readonly>
                        </div>
                        <label class="layui-form-label">订单金额</label>
                        <div class="layui-input-inline">
                            <input name="pay_amount" lay-verify="required" value="{{$order['pay_amount']}}"
                                   autocomplete="off" class="layui-input" type="text" readonly>
                        </div>

                    </div>
                    <div class="layui-form-item">
                        <label for="" class="layui-form-label">售后状态</label>
                        <div class="layui-input-block">
                            <select name="status" lay-verify="required">
                                <option value="0"></option>
                                <option value="1" @if( $order['status']==0) selected @endif>售后中</option>
                                <option value="2" @if( $order['status']==1) selected @endif>已完成</option>
                                <option value="3" @if( $order['status']==2) selected @endif>拒绝</option>
                            </select>
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
    form.on('submit(formSubmit)', function(data){
        $.post("{{ route('backend.sales.order.refund') }}",data.field,function(res){
            if(res.code!=1){
                layer.msg(res.message,{icon:5,anim:6});
                return false;
            }else{
                layer.msg(res.message,function(){
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.layer.close(index); //再执行关闭
                });

            }
        },'json');
        return false;
    });

    @endsection
</script>