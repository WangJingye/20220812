@extends('backend.base')

@section('content')
    <div class="layui-col-md12" id="promotion_cart">
        <div class="layui-card">
            <div class="layui-card-body" style="padding: 15px;">
                <div class="layui-form-item">
                    <label class="class-label">账户信息</label>
                    <div class="layui-input-block">
                        <span>主账号ID:{{$master_id}}</span>&nbsp;&nbsp;&nbsp;&nbsp;<span>副账号ID:{{$slave_id}}</span>
                    </div>
                </div>

                <form ref="form" class="layui-form" method="post" onsubmit="return false">
                    <input type="hidden" name="master_id" value="{{$master_id}}" />
                    <input type="hidden" name="slave_id" value="{{$slave_id}}" />
                    @foreach($fields as $field=>$desc)
                    <div class="layui-form-item">
                        <label class="class-label">{{$desc}}:</label>
                        <div class="layui-input-block">
                            @foreach($infos[$field] as $k=>$v)
                                <input type="radio" name="{{$field}}" value=@if($k==0) "m" @else "s" @endif title="{{$v}}" @if($k==0) checked @endif >
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    <div class="layui-form-item">
                        <label class="class-label"></label>
                        <div class="layui-input-block">
                            <button type="submit" lay-submit=""  class="layui-btn" lay-filter="confirm">确 认</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

<style type="text/css">
    .class-label {
        float: left;
        display: block;
        padding: 2px 15px;
        width: 100px;
        font-weight: 600;
        line-height: 20px;
        text-align: right;
    }
</style>


<script>
    @section('layui_script')
    layui.use(['upload', 'form'], function () {
        // var form = layui.form;
        var form = layui.form;

        form.on('submit(confirm)', function (data) {
            console.log(data.field);
            layer.confirm('确定合并账户么？', function (index) {
                $.post("{{ route('backend.member.merge.mergeSlaveMemberIntoMasterMember') }}", data.field, function (res) {
                    if (res.code === 1) {
                        layer.msg('操作成功', {
                            icon: 1,
                            shade: 0.3,
                            offset: '300px',
                            time: 2000 //2秒关闭（如果不配置，默认是3秒）
                        }, function () {
                            let index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                            parent.layer.close(index); //再执行关闭
                        });
                    } else {
                        layer.msg(res.message, {
                            icon: 2,
                            shade: 0.3,
                            offset: '300px',
                            time: 2000 //2秒关闭（如果不配置，默认是3秒）
                        });
                    }
                }, 'json');
                layer.close(index);
                return false;
            });
        });
    });

    @endsection
</script>