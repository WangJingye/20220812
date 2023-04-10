@extends('backend.base')
<link rel="stylesheet" href="{{ url('/static/admin/css/form.css') }}" media="all">
<link rel="stylesheet" href="{{ url('/static/admin/css/form.css') }}" media="all">
<script src="<?=url('/lib/app.js')?>"></script>
<style>
    .layui-input-block {
        margin-left: 180px !important;
        line-height: 36px;
    }
    .layui-form-label {
        width: 140px !important;
    }
</style>
@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新</h2>
        </div>
        <div class="layui-card-body" id="app">
            <form method="post" class="layui-form">
                <input name="id"  type="hidden" value="{{$detail['id']??''}}" class="layui-input"/>
                {{csrf_field()}}
                <div class="layui-form-item">
                    <?php if($detail['id']):?>


                    <label for="" class="layui-form-label">ID</label>
                    <div class="layui-input-block">
                        {{$detail['id']??old('id')}}
                    </div>
                    <?php endif?>
                </div>

                <div class="layui-form-item" >
                    <label class="layui-form-label">状态</label>
                    <div class="layui-input-block">
                        <select lay-verify="required" name="status" lay-filter="type_list" >
                            @foreach(['下线','上线'] as $k=>$v)
                                <option {{$k==$detail['status']?'selected':''}} value="{{$k}}">{{$v}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                

                <div class="layui-form-item" >
                    <label for="" class="layui-form-label">优惠劵ID</label>
                    <div class="layui-input-block">
                        <?php if($detail['id']):?>
                            <?php echo $detail['coupon_id']?>
                        <?php else:?>
                            <input name="coupon_id" value="{{$detail['coupon_id']??''}}" class="layui-input"/>
                        <?php endif?>

                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">兑换积分</label>
                    <div class="layui-input-block">
                        <input name="exchange_point" value="{{$detail['exchange_point']??''}}" class="layui-input"/>
                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="submit" class="layui-btn" lay-submit="" lay-filter="confirm">确 认
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        function showImage(ele){
            layer.open({
                type: 1,
                area: ['50%', 'auto'],
                offset: 't',
                maxmin: true,
                fixed: false,
                content: "<img style='width:100%' src='"+ele.src+"' />"

            });
        }


    </script>
@endsection
<script>

    @section('layui_script')
    layui.use(['form'], function () {
        //监听提交
        form.on('submit(confirm)', function (data) {

            $.post("{{ route('backend.point.post') }}", data.field, function (res) {
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
            return false;
        });

    })
    ;

    @endsection
</script>