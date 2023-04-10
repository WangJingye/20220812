@extends('backend.base')
<link rel="stylesheet" href="{{ url('/static/admin/css/form.css') }}" media="all">
<style>
    .layui-form-item {
        white-space: nowrap !important;
    }

</style>
@section('content')

    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            @if($virtual==1)
                <h2>新增虚拟SKU</h2>
            @elseif($unreal==1)
                <h2>新增非真实SKU</h2>
            @else
                <h2>新增SKU</h2>
            @endif
        </div>
        <div class="layui-tab">
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <div class="layui-card-body">
                        <form method="post" class="layui-form">
                            {{csrf_field()}}
                        <input type="hidden" name="id" value="">
                        <input  type="hidden" name="virtual" value="{{$virtual??0}}">
                        <input  type="hidden" name="unreal" value="{{$unreal??0}}">
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">SKU ID</label>
                            <div class="layui-input-block">
                                <div class=" layui-col-xs7">
                                    <input class="layui-input" type="text" name="sku_id" lay-verify="required"
                                           value="" >
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">原价</label>
                            <div class="layui-input-block">
                                <div class=" layui-col-xs5">
                                    <input class="layui-input" type="text" name="ori_price" lay-verify="required"
                                           value="" >
                                </div>
                            </div>
                        </div>
                        <div style="display: none" class="layui-form-item">
                            <label for="" class="layui-form-label">尺寸</label>
                            <div class="layui-input-block">
                                <div class=" layui-col-xs5">
                                    <input class="layui-input" type="text" name="size"
                                           value="" >
                                </div>
                            </div>
                        </div>

                        <div style="display: none" class="layui-form-item">
                            <label for="" class="layui-form-label">税收类型</label>
                            <div class="layui-input-block">
                                <input type="radio" name="revenue_type" value="1" title="护肤用化妆品"  checked >
                                <input type="radio" name="revenue_type" value="2" title="护发用化妆品" >
                                <input type="radio" name="revenue_type" value="3" title="刷子类制品" >
                                <input type="radio" name="revenue_type" value="4" title="美容修饰类化妆品" >
                            </div>
                        </div>

                        <div style="display: none" class="layui-form-item">
                            <label for="" class="layui-form-label J_spec_type1 ">色号</label>
                            <div class="layui-input-block">
                                <div class="layui-col-xs2">
                                    <input class="layui-input layui-col-xs5 J_spec" type="text" data-spectype="color" name="spec_color_code"
                                           value="" placeholder="色号code码，如：ffffff" >
                                </div>
                                <div class="layui-col-xs5" style="margin-left: 10px;">
                                    <input class="layui-input layui-col-xs5" type="text" name="spec_color_code_desc"
                                           placeholder="请输入色号描述，如：红色" value="" >
                                </div>
                                <!--
                                <div class="layui-col-xs2">
                                    <button style="display: none" type="button"  class="layui-btn J_spec_check">检测</button>
                                </div>
                                -->
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label J_spec_type2">规格1</label>
                            <div class="layui-input-block">
                                <div class="layui-col-xs2">
                                    <input  class="layui-input layui-col-xs5 J_spec" data-spectype="capacity_ml" type="text" name="spec_capacity_ml_code"
                                           value="" placeholder="规格code" >
                                </div>
                                <div class="layui-col-xs5" style="margin-left: 10px;">
                                    <input class="layui-input layui-col-xs5" type="text" name="spec_capacity_ml_code_desc"
                                           placeholder="规格描述" value="" >
                                </div>
                                <div class="layui-col-xs2">
                                    <button style="display: none"  type="button"  class="layui-btn J_spec_check">检测</button>
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label J_spec_type3">规格2</label>
                            <div class="layui-input-block">
                                <div class="layui-col-xs2">
                                    <input  class="layui-input  J_spec" data-spectype="capacity_g" type="text" name="spec_capacity_g_code"
                                           value=""  placeholder="规格code" >
                                </div>
                                <div class="layui-col-xs5" style="margin-left: 10px;">
                                    <input class="layui-input layui-col-xs5" type="text" name="spec_capacity_g_code_desc"
                                           placeholder="规格描述" value="" >
                                </div>
                                <div class="layui-col-xs2">
                                    <button style="display: none" type="button"  class="layui-btn J_spec_check">检测</button>
                                </div>
                            </div>
                        </div>

                        <div style="display: none" class="layui-form-item">
                            <label for="" class="layui-form-label">包含SKU</label>
                            <div class="layui-input-block">
                                <div class=" layui-col-xs5">
                                    <input class="layui-input" type="text" name="contained_sku_ids"
                                           value="" placeholder="固定礼盒需要设置包含sku，逗号分隔" >
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">打包SKU</label>
                            <div class="layui-input-block">
                                <div class=" layui-col-xs5">
                                    <input class="layui-input" type="text" name="include_skus" value="" placeholder="多个sku逗号分隔,请勿将打包sku的商品作为赠品,如果主SKU为非真实SKU则打包商品在创建订单的时候会使用原价,其他情况价格为0">
                                </div>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">商品</label>
                            <div class="layui-input-block ">
                                <div class="layui-col-xs8">
                                    <input class="layui-input" type="text" id="J_product_name" name="product_name"
                                           value="">
                                    <input  type="hidden" id="J_product_idx" name="product_idx"
                                           value="">
                                </div>
                                <div class="layui-col-xs2">
                                    <button type="button"  class="layui-btn" id="J_select_product">选择商品</button>
                                </div>
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

            </div>
        </div>
    </div>
@endsection



<script type="text/html" id="barDemo">
    <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="select">选择</a>
</script>

<div class="layui-form-pop" style="display:none;">
    <div class="layui-card-body">
        <form class="layui-form" lay-filter="extra">
            <input type="hidden" name="cateIdx" id="cateIdx" value="">
            <table id="list" lay-filter="list"></table>



        </form>
    </div>
</div>


<script>
    @section('layui_script')

    //注意：选项卡 依赖 element 模块，否则无法进行功能性操作
    layui.use('element', function () {
        var element = layui.element;

        //…
    });

    form.verify({
        Ndouble: [
            /^[0-9]\d*$/
            , '只能输入整数哦'
        ]
    });

    $('#J_select_product').on('click',function () {
        // var i = $(this).parents('.J_colle_product').index();
        // $("#J_search_product_index").val(i);
        prodIndex = layer.open({
            title: '请选择要挂载的商品',
            type: 1,
            area: ['100%', '100%'],
            offset: 't',
            maxmin: false,
            move: false,
            content: $(".layui-form-pop"),
        });

        // let styleNbr = $(this).parents('.J_colle_product').find('.J_search_content').val();
        let styleNbr = $("#J_product_name").val();
        dataTable = table.render({
            elem: '#list'
            , id: 'table_list'
            , url: "{{ route('backend.goods.spu.list') }}?product_name=" + styleNbr //数据接口
            //开启分页
            , page: true
            , method: 'get'
            ,async: false
            , limit: 10
            , text: {
                none: '暂无相关数据' //默认：无数据
            }
            , parseData: function (res) {
                return {
                    "code": 0,
                    "data": res.pageData,
                    "count": res.count
                }
            }
            , toolbar: '#toolbar' //开启头部工具栏，并为绑定左侧模板
            , defaultToolbar: ['filter']
            // ,where: {pid: '1'}
            , cols: [[ //表头
                {field: 'product_id', title: '产品ID'}
                , {field: 'product_name', title: '产品中文名'}
                , {field: 'created_at', title: '创建时间'}
                , {fixed: 'right', width:178, align:'center', toolbar: '#barDemo'}
            ]]
        });
    });

    layui.use(['form','upload'], function () {
        var upload = layui.upload;
        //执行实例
        upload.render({
            elem: '#J_thumbnail_button' //绑定元素
            , url: "{{ route('backend.file.uploadPic') }}" //上传接口
            , done: function (res) {
                //如果上传失败
                if (res.code > 0) {
                    return layer.msg('上传失败');
                }
                //上传成功
                var path = res.path;
                $("#J_thumbnail").val(path);
                $("#J_thumbnail_src").attr("src", path).show();
            }
            , error: function () {
                //请求异常回调
            }
        });
        //监听提交
        form.on('submit(confirm)', function (data) {
            $.post("{{ route('backend.goods.sku.insert') }}", data.field, function (res) {
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
                    window.parent.location.reload();
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
        var form2Init = $(".form2").serializeArray();
        var text2Init = JSON.stringify({dataform: form2Init});
        //监听提交
        form.on('submit(confirm2)', function (data) {
            //记录表单初始数据 如果未修改则不用提交
            var form2data = $(".form2").serializeArray();
            var text2 = JSON.stringify({dataform: form2data});

            if (text2 == text2Init) {
                layer.alert('无更改，无需提交');
                return false;
            }
            data.field.is_secure = $('input[name=is_secure]').val();

            if(data.field.is_secure == 1 && data.field.secure<=0){
                layer.alert('如果设置安全库存，安全库存不能为0');
                return false;
            }

            $.post("{{ route('backend.goods.update.secure') }}", data.field, function (res) {
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
                    window.parent.location.reload();
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

        //存储旧的库存总数
        var oldstuck_total = 0;
        var channel_stock = new Array();
        $(".channel_stock").each(function (i, n) {
            var v = $(this).val();
            var name = $(this).attr('name');

            channel_stock[name] = v;
            oldstuck_total = oldstuck_total + Number(v);

        });

        $('.J_spec_check').on('click',function () {
            var i = $('.J_spec_check').index($(this));
            var specType = $('.J_spec').eq(i).data('spectype');
            var spec = $('.J_spec').eq(i).val();
            $.getJSON('{{ route('backend.goods.spu.checkSpec') }}'+'?specType='+specType+'&spec='+spec,function (ret) {
                var content = '';
                if(ret.data.legal==0){
                    content = '规格不合法，'+specType+'规格类型中无'+spec+'规格'
                }else{
                    content = '规格合法'
                }
                layer.open({
                    title: '规格检测'
                    ,content: content
                });
            });
        });

        //监听工具条
        table.on('tool(list)', function(obj){
            var data = obj.data;
            if(obj.event === 'select'){
                console.log(data);
                $("#J_product_idx").val(data.id);
                $("#J_product_name").val(data.product_name);
                $('.J_spec_check').hide();

                $.each(data.spec_type,function (index,value) {
                    if(value == 'color'){
                        $(".J_spec_type1").attr('required',true).html("色号(规格)");
                        $('.J_spec_check').eq(0).show();
                    }
                    if(value == 'capacity_ml'){
                        $(".J_spec_type2").attr('required',true).html("规格1");
                        $('.J_spec_check').eq(1).show();
                    }
                    if(value == 'capacity_g'){
                        $(".J_spec_type3").attr('required',true).html("容量g(规格)");
                        $('.J_spec_check').eq(2).show();
                    }
                });

                form.render();
                layer.close(prodIndex)
            }
        });



    });

    @endsection
</script>