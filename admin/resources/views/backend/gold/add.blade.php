@extends('backend.base')
<link rel="stylesheet" href="{{ url('/static/admin/css/form.css') }}" media="all">
<script src="{{ url('/static/admin/js/custom.js') }}"></script>

@section('content')
    <style>
        .layui-form-item {
            white-space: nowrap !important;
        }
    </style>

    <div class="layui-card product-edit" id="app">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加储值卡</h2>
        </div>
        <div class="layui-card-body">

            <el-form ref="form" class="layui-form" method="post" onsubmit="return check();">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">名称</label>

                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="gold_name" id="gold_name"
                               lay-verify="required|len_limit" lay-max="50" data-raw=""
                               value="" placeholder="如：面值A">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">实际金额</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="number" name="price"
                               value="" placeholder="如：1000" lay-verify="required|numberb">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">倍数</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="number" name="rate" value="" placeholder="如：1.1" lay-verify="required">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">有效期</label>
                    <div class="layui-input-block">
                        <select name="valid_time" class="layui-select" lay-verify="required">
                            <option value="">请选择</option>
                            <?php for($i = 1;$i <= 1;$i++):?>
                            <option value="<?=$i?>"><?= $i?>年</option>
                            <?php endfor;?>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">链接有效期</label>
                    <div class="layui-input-block">
                        <div class="layui-input-inline">
                            <input class="layui-input" id="link_start_time" name="link_start_time" placeholder="开始时间"
                                   type="text" autocomplete="off" lay-verify="required">
                        </div>
                        <div class="layui-input-inline">
                            <input class="layui-input" id="link_end_time" name="link_end_time" placeholder="结束时间"
                                   type="text" autocomplete="off" lay-verify="required">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item" style="margin-top: 5px">
                    <div class="layui-input-block">
                        <button type="submit" class="layui-btn" lay-submit="" lay-filter="confirm">确 认</button>
                    </div>
                </div>
            </el-form>
        </div>
    </div>

    <link rel="stylesheet" href="<?=url('/lib/app/index.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?=url('/static/admin/layui/css/layui.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/static/admin/css/admin.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/css/bootstrap.min.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/css/swiper.min.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="//at.alicdn.com/t/font_1458973_xkrtaihi4e8.css"/>
    <?php require_once './js/app.js.php'; ?>
@endsection

<script>
    @section('layui_script')
    layui.use('element', function () {
        var element = layui.element;
    });

    //监听提交
    layui.use([ 'form'], function () {
        form.on('submit(confirm)', function (data) {
            $.post("{{ route('backend.gold.insert') }}", data.field, function (res) {
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
        laydate.render({
            elem: '#link_start_time',  // 输出框id
            type: 'datetime',
            trigger: 'click'
        });
        laydate.render({
            elem: '#link_end_time',   // 输出框id
            type: 'datetime',
            trigger: 'click'
        });
        form.verify({
            numberb: [
                /^[0-9]\d*$/
                , '只允许正整数'
            ],
            len_limit: function (value, item) {
                var max = item.getAttribute('lay-max');
                if (value.length > max) {
                    return '不能大于' + max + '个字符的长度';
                }
            }
        });
    });

    @endsection
</script>