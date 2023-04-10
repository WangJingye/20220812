@extends('backend.base') 
@section('content')
<div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body" style="padding: 15px;">
                <form id="add_form" class="layui-form"  action="">
                	<div class="layui-form-item">
                        <label class="layui-form-label" style="width:200px;">实际支付金额:  {{$amount??''}}</label>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">金额</label>
                        <div class="layui-input-block">
                            <input name="refund_amount" lay-verify="required" value="" autocomplete="off" class="layui-input" type="text">
                        </div>
                    </div>
                            <input type="hidden"  name="order_id" value="{{$order_id}}" />
                            <input type="hidden"  name="item_id" value="{{$item_id}}" />
                            <input type="hidden"  name="type" value="{{$type}}" />                             
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
        $.post("{{ route('backend.sales.order.refundAction') }}",data.field,function(res){
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