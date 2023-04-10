@extends('backend.base')
<link rel="stylesheet" href="{{ url('/static/admin/css/form.css') }}" media="all">
<script src="{{ url('/static/admin/js/custom.js') }}"></script>

@section('content')
    <style>
        .edit-wrap-scroll {
            position: fixed;
            top: 0px;
            right: 20px;
        }

        .edit-wrap-scroll-menu {
            position: fixed;
            top: 0px;
            left: 0px;
        }
    </style>
    <div class="layui-card product-edit" id="app">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新产品</h2>
        </div>
        <div class="layui-card-body">

            <el-form ref="form" class="layui-form" method="post" onsubmit="return check();">
                {{csrf_field()}}
                <input type="hidden" name="id" value="{{$detail->id}}">
                <input type="hidden" name="old_word" value="{{$detail->word}}">
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">关键字</label>
                    <div class="layui-input-block" style="width: 500px">
                        <input class="layui-input" type="text" name="word" lay-verify="required"
                               placeholder="跳转关键字"  value="{{$detail->word}}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">跳转类型</label>
                    <div class="layui-input-block" style="width: 150px">
                        <select name="type" lay-filter="hasMainMaterial" style="display:none;">
                            <option value="1" @if($detail->type == 1) selected @endif >商品详情页</option>
                            <option value="2" @if($detail->type == 2) selected @endif >商品列表页</option>
                            <option value="3" @if($detail->type == 3) selected @endif >专题页</option>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">Code</label>
                    <div class="layui-input-block" style="width: 300px">
                        <input class="layui-input" type="text" name="code"
                               value="{{$detail->code}}" placeholder="商品ID/类目ID/专题ID">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="submit" class="layui-btn" lay-submit="" lay-filter="confirm">确 认</button>
                    </div>
                </div>
            </el-form>
        </div>
    </div>

    <style>

    </style>
    <script src="<?=url('/lib/app.js'); ?>"></script>
    <script src="<?=url('/lib/jquery.validate.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?=url('/lib/app/index.css'); ?>">

    <link rel="stylesheet" type="text/css" href="<?=url('/static/admin/layui/css/layui.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/static/admin/css/admin.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/css/bootstrap.min.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/css/swiper.min.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="//at.alicdn.com/t/font_1458973_xkrtaihi4e8.css"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/static/admin/css/cms.css'); ?>"/>

    <?php require_once './js/app.js.php'; ?>

@endsection

<script>
    @section('layui_script')
    //监听提交
    layui.use(['form'], function () {
        form.on('submit(confirm)', function (data) {
            $.post("{{ route('backend.goods.search.updateRedirect') }}", data.field, function (res) {
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
    $("#product_name").bind('change', function () {
        let rawPdtName = $("#product_name").data("raw");
        let curPdtName = $("#product_name").val();
        if (rawPdtName !== curPdtName) {
            $("#custom_product_name").val(curPdtName);
        }
    })
    $("#kv_images").bind('change', function () {
        var val = formatJson($("#kv_images").val(), false);
        if (typeof val != "undefined") {
            $("#kv_images").val(val);
        }
    })
    $("#detail_images").bind('change', function () {
        var val = formatJson($("#detail_images").val(), false);
        if (typeof val != "undefined") {
            $("#detail_images").val(val);
        }
    })
    if ($("#kv_images").val()) {
        $("#kv_images").val(formatJson($("#kv_images").val(), false));
    }
    if ($("#detail_images").val()) {
        $("#detail_images").val(formatJson($("#detail_images").val(), false));
    }
    if ($("#custom_product_name").val()) {
        $("#product_name").val($("#custom_product_name").val());
    }
    @endsection
</script>