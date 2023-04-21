@extends('backend.base')
@section('content')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body" style="padding: 15px;">
                <form id="add_form" class="layui-form" onsubmit="return false;">
                    <div class="layui-form-item">
                        <label class="layui-form-label">退货类型</label>
                        <div class="layui-input-block">
                            <select name="return_type" lay-verify="required">
                                <option value="1"  selected>整单退</option>
                            </select>
                        </div>
                        </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">原支付方式</label>
                        <div class="layui-input-block">
                            <select name="payment_type" lay-verify="required">
                                <option value="0" @if( $order['payment_type']==0) selected @endif></option>
                                <option value="2" @if( $order['payment_type']==2) selected @endif>微信支付</option>
                                <option value="10" @if( $order['payment_type']==10) selected @endif>储值卡支付</option>
                                <option value="11" @if( $order['payment_type']==11) selected @endif>组合支付</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">退款方式</label>
                        <div class="layui-input-block">
                            <select name="return_pay_type" lay-verify="required">
                                <option value="0"></option>
                                <option value="1"  selected >原路返还</option>
                            </select>
                        </div>

                    </div>
                    <input name="type" value="1" autocomplete="off"
                           class="layui-input" type="hidden">
                    <input name="order_id" value="{{$order['id']}}" autocomplete="off"
                           class="layui-input" type="hidden">
                    <input name="action_type" value="2" autocomplete="off"
                           class="layui-input" type="hidden">



                    <div class="layui-form-item layui-block">
                        <label class="layui-form-label">退款金额</label>
                        <div class="layui-input-block">
                            <input name="return_amount" lay-verify="required" value="{{$order['total_amount']}}"
                                   autocomplete="off" class="layui-input" type="text">
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