@extends('backend.base')

@section('content')
    <style>
        .layui-table-cell {
            height: auto !important;
            white-space: normal;
        }
    </style>
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <span class="layui-inline layui-btn" id="upload">批量发放优惠券</span>
                    </div>
                </form>
                <table id="list" lay-filter="list"></table>
            </div>
        </div>
    </div>
@endsection

<script>
    @section('layui_script')
    layui.use(['upload', 'form'], function () {
        var upload = layui.upload;

        upload.render({
            elem: '#upload' //绑定元素
            , url: "{{ route('backend.member.import.coupon') }}" //上传接口
            , accept: 'file' //普通文件
            , done: function (res) {
                //如果上传失败
                if (res.code == 0) {
                    return layer.msg('上传失败');
                }else{
                    num = res.data.num;
                    return layer.msg('成功发放'+num+'个优惠券');
                }
            }
            , error: function () {
                //请求异常回调
            }
        });
        //自定义验证规则
        form.verify({});
    });
    @endsection
</script>

