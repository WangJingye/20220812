@extends('backend.base')

@section('content')
<div class="layui-col-md12">
    <div class="layui-card">
        <div class="layui-card-body" style="padding: 15px;">
            <form id="add_form" class="layui-form" action="">
                <div class="layui-form-item">
                    <label class="layui-form-label">手机号</label>
                    <div class="layui-input-block">
                        <textarea name="mobiles" class="layui-textarea"></textarea>
                    </div>
                </div>
                <input type="hidden" name="coupon_id" value="{{$id}}">
                <div class="layui-form-item">
                    <div class="layui-input-block" >
                        <button id="formSubmit" class="layui-btn" lay-submit lay-filter="formSubmit">提交</button>
                        <span class="layui-btn sub " id="back">返回</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script>
    @section('layui_script')
    form.on('submit(formSubmit)', function (data) {
        loading = layer.load(1, {shade: [0.3]});
        $.post("{{ route('backend.coupon.couponsend.update') }}", data.field, function (res) {
            layer.close(loading);
            if (res.code === 1) {
                layer.msg('操作成功', {
                    icon: 1,
                    shade: 0.3,
                });
            } else {
                layer.msg(res.message, {
                    icon: 2,
                    shade: 0.3,
                });
            }
        }, 'json');
        return false;
    });
    $('#back').on('click', function(){
        location.href = '{{url('admin')}}/promotion/cart';
    });
    @endsection
</script>
