@extends('backend.base')
<link rel="stylesheet" type="text/css" href="<?=url('/static/admin/layui/css/layui.css'); ?>"/>
<link rel="stylesheet" href="{{ url('/static/admin/css/form.css') }}" media="all">
<script src="{{ url('/static/admin/js/custom.js') }}"></script>

@section('content')
    <style>
        .edit-wrap-scroll{
            position: fixed;
            top:0px;
            right:20px;
        }
        .edit-wrap-scroll-menu{
            position: fixed;
            top:0px;
            left:0px;
        }
        .layui-form-item {
            white-space: nowrap !important;
        }
    </style>
    <div class="layui-card product-edit" id="app">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新产品集合</h2>
        </div>
        <div class="layui-card-body">

{{--            <el-form ref="form" class="layui-form" method="post" onsubmit="return check();">--}}
            <form class="layui-form" action="">
                {{csrf_field()}}
                <input type="hidden" name="id" value="{{$detail->id}}">
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">集合名称</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="colle_name" id="colle_name"
                               lay-verify="required" data-raw="{{$detail->colle_name??old('colle_name')}}"
                               value="{{$detail->colle_name??old('colle_name')}}" placeholder="如：挚爱美礼">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">集合ID</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="colle_id" id="colle_id"
                               lay-verify="required" value="{{$detail->colle_id??old('colle_id')}}"
                               placeholder="如：TZ100001">
                    </div>
                </div>
                <input type="hidden" name="custom_product_name" id="custom_product_name"
                       value="{{$detail->custom_product_name??old('custom_product_name')}}">
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">集合描述</label>
                    <div class="layui-input-block">
                        <textarea class="layui-textarea" name="colle_desc"
                                  >{{$detail->colle_desc??old('colle_desc')}}</textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">集合短描述</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="short_colle_desc"
                               value="{{$detail->short_colle_desc??old('short_colle_desc')}}" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">可搜索</label>
                    <div class="layui-input-block">
                        <input type="radio" name="can_search" value="1" title="可搜索"
                               @if($detail->can_search == 1)
                               checked
                                @endif
                        >
                        <input type="radio" name="can_search" value="0" title="不可搜索"
                               @if($detail->can_search == 0)
                               checked
                                @endif
                        >
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">展示时间</label>
                    <div class="layui-input-block">
                        <input type="input" name="display_start_time" id="start_time" value=" @if(!empty($detail->display_start_time)) {{date('Y-m-d H:i:s',$detail->display_start_time)}}  @endif "
                        >
                        ~
                        <input type="input" name="display_end_time" id="end_time" value=" @if(!empty($detail->display_end_time)) {{date('Y-m-d H:i:s',$detail->display_end_time)}}  @endif "
                        >
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">推荐类目ID</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="rec_cat_id"
                               value="{{$detail->rec_cat_id??old('rec_cat_id')}}" placeholder="为你推荐类目ID">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">优先目录</label>
                    <div class="layui-input-block">
                        @foreach($cats as $k=>$cat)
                            <input type="radio" name="priority_cat_id" value="{{$cat['cat_id']}}" title="{{$cat['cat_name']}}"
                                   @if($detail->priority_cat_id == $cat['cat_id'])
                                   checked
                                    @endif
                            >
                        @endforeach
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">商品模块</label>

                    <div class="layui-input-block ">
                        <div class="layui-container">
                            <button type="button" id="J_add_product" class="layui-btn">
                                <i class="layui-icon">&#xe608;</i> 添加
                            </button>
                        </div>
                        <div id="J_colle_products_chunk">

                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="submit" class="layui-btn" lay-submit="" lay-filter="confirm">确 认</button>
                    </div>
                </div>
            </form>
        </div>
        </div>

    <div id="J_tpl_product" style="display: none">
        <div class="layui-container J_colle_product"  style="margin: 15px 10px; width: 80%; padding-top: 20px; border: 1px gray dashed;">
            <div class="layui-row">
                <div class="layui-form-label layui-col-xs2">名称</div>
                <div class="layui-col-xs4">
                    <input type="text"  class="J_colle_product_name layui-input J_search_content" name="title" required  lay-verify="required" placeholder="请输入标题" autocomplete="off" >
                </div>
                <div class="layui-col-xs5 layui-text-center">
                    <button type="button" class="layui-btn J_search_product">
                        <i class="layui-icon"></i> 查询
                    </button>
                    <button type="button" class="layui-btn J_pre">
                        <i class="layui-icon"></i> 上移
                    </button>
                    <button type="button" class="layui-btn J_next">
                        <i class="layui-icon"></i> 下移
                    </button>
                </div>
            </div>

            <div class="layui-row">
                <label class="layui-form-label layui-col-xs2">
                    规格
                </label>
                <div class="layui-col-xs7 J_skus">

                </div>

            </div>

            <div class="layui-row layui-form-item">
                <label class="layui-form-label layui-col-xs2">
                    赠品
                </label>
                <div class="layui-col-xs6 ">
                        <input type="checkbox" name="close" class="J_is_freebie" value="1" lay-skin="switch" lay-text="ON|OFF">
                </div>
            </div>

            <div class="layui-form-item layui-row">
                <label class="layui-form-label"></label>
                <button type="button" class="layui-btn  layui-btn-danger J_del_product">
                    <i class="layui-icon">&#xe640;</i> 删除
                </button>
            </div>
        </div>
    </div>

    <div id="J_tpl_sku" style="display: none;">
        <input type="checkbox" class="J_colle_skus"  name="" title="">
    </div>

    <div class="layui-form-pop" style="display:none;">
        <div class="layui-card-body">
            <form class="layui-form" lay-filter="extra">
                <input type="hidden" name="cateIdx" id="cateIdx" value="{{$detail->id}}">
                <table id="list" lay-filter="list"></table>
            </form>
        </div>
    </div>

    <script type="text/html" id="barDemo">
        <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="select">选择</a>
    </script>


    <div style="display: none">
        <input type="hidden" id="J_init_products" value='<?php echo json_encode($detail->products); ?>' />
        <input type="hidden" id="J_search_product_index" value="" />
    </div>


    <style>

    </style>
    <script src="<?=url('/lib/jquery.validate.min.js'); ?>"></script>


    <script>
        var pageTitle = '';
        var showHeaderFooter = '';

        nodes.forEach(function (item){
          if(item.action==null || item.action==false){
              item.action={"data":[],"type":"none","route":false};
          }
        })
        console.log("nodes:",nodes);
    </script>
    <?php require_once './js/app.js.php'; ?>

    <script>
        $(window).scroll( function() {
            var  scrollTop=document.title=$(this).scrollTop();
            if(scrollTop > 910){
                $('.edit-wrap').addClass('edit-wrap-scroll').css('top',0);
                $('.element-list-wrap').addClass('edit-wrap-scroll-menu').css('top',0);
                $('.cms-content-wrap').css('marginLeft','22%');
            }else{
                $('.edit-wrap').removeClass('edit-wrap-scroll').css('top',910 - scrollTop);
                $('.element-list-wrap').removeClass('edit-wrap-scroll-menu').css('top',910 - scrollTop);
                $('.cms-content-wrap').css('marginLeft','0');
            }
            $('.cms-content-wrap').css('minHeight','800px');
        } );
    </script>
@endsection

<script>
    @section('layui_script')
    initProductChunk();

    $("#J_add_product").bind('click',function () {
        products = $("#J_colle_products_chunk").children('.J_colle_product');
        pNum = products.length;
        tpl = $('#J_tpl_product');
        child = tpl.clone();
        // child = products.eq(0);
        // console.log(child.html());

        // child.find('.J_colle_skus').attr('name','chunks['+pNum+'][skus][]');
        $("#J_colle_products_chunk").append(child.html());
        form.render();
        resetProductChunkName();
        // console.log(tpl.html());
    });

    function resetProductChunkName(){
        products = $("#J_colle_products_chunk").children('.J_colle_product');
        products.each(function(index){
            $(this).find('.J_colle_skus').attr('name','chunks['+index+'][skus][]');
            $(this).find('.J_is_freebie').attr('name','chunks['+index+'][is_freebie]')
        });
    }

    function initProductChunk(){
        // var len = products.length
        var str = $("#J_init_products").val();
        if(str == '') return;
        products = $.parseJSON(str);
        // console.log(products);
        $.each(products,function (index,product) {
            var spuTpl = $('#J_tpl_product');
            var skuTpl = $('#J_tpl_sku');
            var spuHtml = spuTpl.clone();
            spuHtml.find('.J_colle_product_name').attr('value',product.product_name)
            // console.log(product.product_name);
            // console.log(spuHtml.html());
            var skus = product.skus;
            if(product.is_freebie){
                spuHtml.find('.J_is_freebie').attr('checked',true);
            }

            // console.log(skus);
            $.each(skus,function (i,sku) {
                // console.log(sku);
                skuHtml = skuTpl.clone();
                skuHtml.children().attr('value',sku.sku_id);
                skuHtml.children().attr('title',sku.spec_desc);
                if(sku.selected){
                    skuHtml.children().attr('checked',true);
                }

                // console.log(sku.color);
                // console.log(skuHtml);
                spuHtml.find('.J_skus').append(skuHtml.html())
            });
            $("#J_colle_products_chunk").append(spuHtml.html());
            resetProductChunkName();
            form.render();
        });
        // for(i=0;i<len;i++){
        //     var tpl = $('#J_tpl_product');
        //     var child = tpl.clone();
        //     child.find('.J_colle_product_name').attr(products.)
        // }

    }

    //监听提交
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

        //执行一个laydate实例
        laydate.render({
            elem: '#start_time' //指定元素
            ,format: 'yyyy-MM-dd HH:mm:ss' //可任意组合
        });
        //执行一个laydate实例
        laydate.render({
            elem: '#end_time' //指定元素
            ,format: 'yyyy-MM-dd HH:mm:ss' //可任意组合
        });

        form.on('submit(confirm)', function (data) {
            $.post("{{ route('backend.goods.collection.update') }}", data.field, function (res) {
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

    $('#J_colle_products_chunk').on('click','.J_search_product',function () {
        var i = $(this).parents('.J_colle_product').index();
        $("#J_search_product_index").val(i);
        prodIndex = layer.open({
            title: '请选择要挂载的商品',
            type: 1,
            area: ['100%', '100%'],
            offset: 't',
            maxmin: false,
            move: false,
            content: $(".layui-form-pop"),
        });

        let styleNbr = $(this).parents('.J_colle_product').find('.J_search_content').val();
        dataTable = table.render({
            elem: '#list'
            , id: 'table_list'
            , url: "{{ route('backend.goods.spu.list') }}?retSku=1&product_name=" + styleNbr //数据接口
            //开启分页
            , page: true
            , method: 'get'
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
                {type:'radio'}
                ,{field: 'product_id', title: '商品ID'}
                , {field: 'product_name', title: '产品中文名'}
                , {fixed: 'right', width:178, align:'center', toolbar: '#barDemo'}
            ]]
        });
    });

    $('#J_colle_products_chunk').on('click','.J_del_product',function () {
        $(this).parents('.J_colle_product').remove();
        resetProductChunkName()
    });

    $('#J_colle_products_chunk').on('click','.J_pre',function () {
        var i = $(this).parents('.J_colle_product').index();
        if(i == 0) return;
        var parent = $(this).parents('.J_colle_product');
        // alert($(this).parents('.J_colle_product').prop("outerHTML"));
        $("#J_colle_products_chunk").children().eq(i-1).before(parent.prop("outerHTML"));
        parent.remove();
        resetProductChunkName()
    });

    $('#J_colle_products_chunk').on('click','.J_next',function () {
        var i = $(this).parents('.J_colle_product').index();
        var len = $("#J_colle_products_chunk").children().length - 1;
        if(i == len)  return;
        var parent = $(this).parents('.J_colle_product');
        // alert($(this).parents('.J_colle_product').prop("outerHTML"));
        $("#J_colle_products_chunk").children().eq(i+1).after(parent.prop("outerHTML"));
        parent.remove();
        resetProductChunkName()
    });

    //监听工具条
    table.on('tool(list)', function(obj){
        var data = obj.data;
        if(obj.event === 'select'){
            var skuTpl = $("#J_tpl_sku")
            var i = $('#J_search_product_index').val();
            var prod = $('#J_colle_products_chunk').children().eq(i);
            prod.find('.J_colle_product_name').val(data.product_name);
            var skus = data.skus;
            prod.find('.J_skus').children().remove();
            $.each(skus,function (i,sku) {
                skuHtml = skuTpl.clone();
                skuHtml.children().attr('value',sku.sku_id);
                skuHtml.children().attr('title',sku.spec_desc);
                skuHtml.children().attr('checked',true);
                prod.find('.J_skus').append(skuHtml.html())
            });
            resetProductChunkName();
            form.render();
            layer.close(prodIndex)
        } else if(obj.event === 'del'){
            layer.confirm('真的删除行么', function(index){
                obj.del();
                layer.close(index);
            });
        } else if(obj.event === 'edit'){
            layer.alert('编辑行：<br>'+ JSON.stringify(data))
        }
    });

    @endsection
</script>

