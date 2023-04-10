@extends('backend.base')
<link rel="stylesheet" href="{{ url('/static/admin/css/form.css') }}" media="all">
@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>创建分类</h2>
        </div>
        <div class="layui-card-body">
            <form method="post" class="layui-form">
                <input type="hidden" name="custom_prod_type" id="custom_prod_type" value="">
                <input type="hidden" name="include_style_number" id="include_style_number" value="">
                <input type="hidden" name="exclude_style_number" id="exclude_style_number" value="">
                <input type="hidden" name="selected_items" id="selected_items" value="">
                <div class="layui-form-item">
                    <label class="layui-form-label">是否一级分类</label>
                    <div class="layui-input-block">
                        <input type="radio" lay-filter="top" name="is_top" value="1" title="是" checked>
                        <input type="radio" lay-filter="top" name="is_top" value="2" title="否">
                    </div>
                </div>
                <div class="layui-form-item" style="display:none;" id="pCate_block">
                    <label for="" class="layui-form-label">上级分类</label>
                    <div class="layui-input-block">
                        <input type="text" id="tree" lay-filter="tree" class="layui-input">
                    </div>
                </div>
                <input type="hidden" name="parent_cat_id" id="parent_cat_id" value="">
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">分类名称</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="cat_name" lay-verify="required" value=""
                               placeholder="如：挚爱美礼">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">排序值</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="sort" lay-verify="required" value=""
                               placeholder="如：100  倒序排列">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">类目类型</label>
                    <div class="layui-input-block">
                        <input type="radio" name="cat_type" value="1" title="正常类目"  checked  >
                        <input type="radio" name="cat_type" value="2" title="活动类目" >
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">分类图片</label>
                    <div class="layui-input-block"
                         style="display: flex;display: -webkit-flex;flex-direction:column;width:15%;">
                        <input class="layui-input" type="hidden" id="category_kv_image" name="cat_kv_image"
                               value="" placeholder="如：挚爱美礼">
                        <img id="category_kv_image_src" width="100%" src="" style="display:none;"/>
                        <button type="button" class="layui-btn" id="category_kv_image_button" style="margin-top:10px;">
                            <i class="layui-icon">&#xe67c;</i>上传图片
                        </button>
                        <button type="button" class="layui-btn" id="del_category_kv_image_button" style="margin-top:10px;">
                            <i class="layui-icon">&#xe67c;</i>删除图片
                        </button>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">分享文案</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="share_content" value="" placeholder="如：挚爱美礼">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">类目介绍</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="cat_desc"
                               value="{{$detail->cat_desc??old('cat_desc')}}" placeholder="如：挚爱美礼">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">分享图片</label>
                    <div class="layui-input-block"
                         style="display: flex;display: -webkit-flex;flex-direction:column;width:15%;">
                        <input class="layui-input" type="hidden" id="share_image" name="share_image" value=""
                               placeholder="如：挚爱美礼">
                        <img id="share_image_src" width="100%" src="" style="display:none;"/>
                        <button type="button" class="layui-btn" id="share_image_button" style="margin-top:10px;">
                            <i class="layui-icon">&#xe67c;</i>上传图片
                        </button>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">分类下产品</label>
                    <div class="layui-input-block">
                        <div class="forins_controls">
                            <div class="label_search_box">
                                <span class="forins_input_box" id="js_tag_box1" data-value="">
                                    <input id="J_pids" name="pids" style="display:none;" value="">
                                    <input id="J_cids" name="cids" style="display:none;" value="">
                                </span>
                                <input id="relatedItemsJson" name="relatedItemsJson" style="display:none;" value="">
                                <button type="button" class="layui-btn layui-btn-warm layui-btn-sm forins_input_button">
                                    选择产品
                                </button>
                            </div>
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

    <div class="layui-form-pop" style="display:none;">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>分类下产品</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" lay-filter="extra">

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">选中的产品</label>
                    <div class="layui-input-block">
                        <div class="forins_controls">
                            <div class="label_search_box">
                            <span class="forins_input_box" id="js_tag_box0" data-value="">
                                <input id="tag_0" name="tag_0" style="display:none;" value="">
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">搜索商品</label>
                    <div class="layui-inline search_box">
                        <input class="layui-input" id="styleNbr" name="styleNbr" autocomplete="off" placeholder="请输入款号">
                    </div>
                    <span class="layui-inline layui-btn" id="search">搜索商品</span>
                </div>
                <table id="list" lay-filter="list"></table>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn J_submit_prod" lay-submit="" lay-filter="relate">立即提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
<script>
            @section('layui_script')
    var prodIndex = null;
    // 自定义模块
    layui.config({
        base: '/ext/',   // 模块所在目录
    }).extend({
        treeSelect: 'treeSelect/treeSelect'
    }).use(['upload', 'form', 'treeSelect'], function () {
        var upload = layui.upload;
        //执行实例
        upload.render({
            elem: '#share_image_button' //绑定元素
            , url: "{{ route('backend.file.uploadPic') }}" //上传接口
            , done: function (res) {
                //如果上传失败
                if (res.code > 0) {
                    return layer.msg('上传失败');
                }
                //上传成功
                var path = res.path;
                $("#share_image").val(path);
                $("#share_image_src").attr("src", path).show();
            }
            , error: function () {
                //请求异常回调
            }
        });
        upload.render({
            elem: '#category_kv_image_button' //绑定元素
            , url: "{{ route('backend.file.uploadPic') }}" //上传接口
            , done: function (res) {
                //如果上传失败
                if (res.code > 0) {
                    return layer.msg('上传失败');
                }
                //上传成功
                var path = res.path;
                $("#category_kv_image").val(path);
                $("#category_kv_image_src").attr("src", path).show();
            }
            , error: function () {
                //请求异常回调
            }
        });


        var treeSelect = layui.treeSelect;
        treeSelect.render({
            // 选择器
            elem: '#tree',
            // 数据
            data: "{{ route('backend.goods.category.pCateListNoSub') }}",
            // 异步加载方式：get/post，默认get
            type: 'post',
            // 占位符
            placeholder: '请选择上级分类',
            // 是否开启搜索功能：true/false，默认false
            search: true,
            // 一些可定制的样式
            style: {
                folder: {
                    enable: false
                },
                line: {
                    enable: true
                }
            },
            // 点击回调
            click: function (d) {
                let cur = d.current;
                $("#parent_cat_id").val(cur.id);
                console.log(d);
            },
            // 加载完成后的回调函数
            success: function (d) {

            }
        });

        //监听提交
        form.on('submit(relate)', function (data) {
            $.post("{{ route('backend.goods.category.calculateProdIds') }}", data.field, function (res) {
                let addHtml = '';
                let newProdsIds = [];
                $.each(res, function (i, v) {
                    addHtml += '<span class="forins_input_tag"><div class="js_tag" data-id="' + v + '">' + v + '</div><div class="icon_tag_del"></div></span>';
                    newProdsIds.push(v);
                })
                addHtml += '<input id="tag_1" name="tag_1" style="display:none;" value="' + newProdsIds.join(",") + '">';
                $("#js_tag_box1").data("value", newProdsIds);
                $("#js_tag_box1").html(addHtml);
                $("#relatedItemsJson").val(JSON.stringify(newProdsIds));
                getCheckBox($(".init"));
                $("#include_style_number").val(data.field.includeStyleNr);
                $("#exclude_style_number").val(data.field.excludeStyleNr);
                $("#selected_items").val($("#tag_0").val());
                layer.close(prodIndex);
            }, 'json');
            return false;
        });
        //监听提交
        form.on('submit(confirm)', function (data) {
            $.post("{{ route('backend.goods.category.create') }}", data.field, function (res) {
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
    });
    if ($("#share_image").val() !== '') {
        $("#share_image_src").show();
    }
    if ($("#category_kv_image").val() !== '') {
        $("#category_kv_image_src").show();
    }

    var dataTable = null;
    form.on('radio(top)', function (data) {
        let val = data.value;
        if (val == 2) {
            $("#pCate_block").show();
        } else {
            $("#pCate_block").hide();
        }
    });
    $("#js_tag_box1").on("click", ".icon_tag_del", function () {
        let id = $(this).parent().children(".js_tag").data("id");
        let rawProdsInfo = $("#js_tag_box1").data("value");
        let newProdsInfo = [];
        let newProdsIds = [];
        $.each(rawProdsInfo, function (i, v) {
            if (v.master_catalog_item == id) {
                return true;
            } else {
                newProdsInfo.push(v);
                newProdsIds.push(v.master_catalog_item);
            }
        })
        $("#js_tag_box1").data("value", newProdsInfo);
        $("#tag_1").val(newProdsIds.join(","));
        $("#relatedItemsJson").val(JSON.stringify(newProdsInfo));
        $(this).parent().remove();
    })

    $("#del_category_kv_image_button").on("click",function () {
        $("#category_kv_image").val("");
    });

    $("#js_tag_box0").on("click", ".icon_tag_del", function () {
        let id = $(this).parent().children(".js_tag").data("id");
        let rawProdsInfo = $("#js_tag_box0").data("value");
        let newProdsInfo = [];
        let newProdsIds = [];
        $.each(rawProdsInfo, function (i, v) {
            if (v.master_catalog_item == id) {
                return true;
            } else {
                newProdsInfo.push(v);
                newProdsIds.push(v.master_catalog_item);
            }
        })
        $("#js_tag_box0").data("value", newProdsInfo);
        $("#tag_0").val(newProdsIds.join(","));
        $(this).parent().remove();
    })
    $('.forins_input_button').on('click', function () {
        prodIndex = layer.open({
            title: '请选择要挂载的商品',
            type: 1,
            area: ['100%', '100%'],
            offset: 't',
            maxmin: false,
            move: false,
            content: $(".layui-form-pop"),
        });
    });
    $('#search').on('click', function () {
        let styleNbr = $("#styleNbr").val();
        dataTable = table.render({
            elem: '#list'
            , id: 'table_list'
            , url: "{{ route('backend.goods.spu.list') }}?product_name=" + styleNbr //数据接口
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
                {checkbox: true, fixed: true}
                , {field: 'product_id', title: '商品ID'}
                , {field: 'product_name', title: '产品中文名'}
            ]]
        });
    });

    $('#search_collection').on('click', function () {
        let styleNbr = $("#styleNbr").val();
        dataTable = table.render({
            elem: '#list'
            , id: 'table_list'
            , url: "{{ route('backend.goods.collection.list') }}?colle_name=" + styleNbr //数据接口
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
                {checkbox: true, fixed: true}
                , {field: 'id', title: '商品自增ID'}
                , {field: 'product_id', title: '商品ID'}
                , {field: 'product_name', title: '产品中文名'}
                , {
                    title: '商品类型', align: 'center', templet: function (d) {
                        let opt = '';
                        if (d.product_type === 2) {
                            opt = "商品集合";
                        }else{
                            opt = "商品";
                        }
                        return opt;
                    }
                }
            ]]
        });
    });

    //监听提交
    $('.J_submit_prod').on('click', function (data) {
        let pIds = [];  //商品IDs
        let cIds = [];  //集合IDs
        var id = $("#J_id").val();
        $(".J_added_prod").each(function () {
            var id = $(this).data('id');
            if($(this).data('ptype') == 2){
                cIds.push(id)
            }else{
                pIds.push(id)
            }
        });
        if(cIds.length === 0 && pIds.length === 0) return;

        // let allIds = [];
        // var id = $("#J_id").val();
        $(".J_added_prod").each(function (k,v) {

            var pTypeDesc = '';
            var id = $(this).data('id');
            var ptype = $(this).data('ptype');
            if($(this).data('ptype') == 2){
                pTypeDesc = '集合';
            }else{
                pTypeDesc = '商品';
            }
            var aHtml = '<span class="forins_input_tag">' +
                '<div class="js_tag" data-id="'+id+'">'+ pTypeDesc + ':'+id+'</div> ' +
                '<div  data-pid="'+id+'" data-ptype="'+ptype+'" class="icon_tag_del J_del_product"></div> </span>'


            // var id = $(this).data('id');
            // var product_id = $(this).text();
            // allIds.push(id);
            //
            // var aHtml = '<span class="forins_input_tag">' +
            //     '<div class="js_tag" data-id="'+id+'">'+product_id+'</div> ' +
            //     '<div   data-id="'+id+'" class="icon_tag_del J_del_product"></div> </span>'
            $("#js_tag_box1").append(aHtml);

        });
        // if(allIds.length === 0) return;
        if(cIds.length === 0 && pIds.length === 0) return;

        layer.close(prodIndex); //再执行关闭
        // $.each(data,function (k,v) {
        //     var aHtml = '<span class="forins_input_tag">' +
        //         '<div class="js_tag" data-id="'+v.id+'">'+v.product_id+'</div> ' +
        //         '<div  data-id="'+v.id+'" class="icon_tag_del J_del_product"></div> </span>'
        //     $("#js_tag_box1").append(aHtml);
        // });
        $("#J_pids").attr('value',pIds.toString());
        $("#J_cids").attr('value',cIds.toString());
        $("#js_tag_box0").children().remove();
        return false;
    });

    $("#js_tag_box1").on('click','.J_del_product',function () {
        var me = $(this);
        var pid = me.data('id');
        var id = $("#J_id").val();
        me.parent().remove();
        return;
    })

    table.on('checkbox(list)',function (obj) {
        var data = obj.data;
        var pTypeDesc = '';
        if(data.product_type == 2){
            pTypeDesc = '集合';
        }else{
            pTypeDesc = '商品';
        }
        if($(this).is(":checked")){
            var addHtml = '<span class="forins_input_tag"><div class="js_tag J_added_prod" data-ptype="'+data.product_type+'" data-id="' + data.id + '">'+ pTypeDesc + ':' + data.id + '</div><div class="icon_tag_del"></div></span>';
            $("#js_tag_box0").append(addHtml);
        }else{
            $(".J_added_prod").each(function (i) {
                var id = $(this).data("id");
                if(id == data.id){
                    $(this).parent().remove();
                }
            })
        }
    });

    table.on('toolbar(list)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
        var layEvent = obj.event;
        var checkStatus = table.checkStatus(obj.config.id);
        if (layEvent === 'getCheckData') {
            let data = checkStatus.data;
            let rawProds = $("#tag_0").val();
            let rawProdsVal = $("#js_tag_box0").data("value");
            let rawProdsInfo = rawProdsVal ? rawProdsVal : [];
            let deRawProds = rawProds ? rawProds.split(",") : [];
            let addHtml = '';
            $.each(data, function (i, v) {
                let validateRe = action.check(v.master_catalog_item, deRawProds);
                if (!validateRe) {
                    deRawProds.push(String(v.master_catalog_item));
                    rawProdsInfo.push({"master_catalog_item": v.master_catalog_item});
                    addHtml += '<span class="forins_input_tag"><div class="js_tag" data-id="' + v.master_catalog_item + '">' + v.master_catalog_item + '</div><div class="icon_tag_del"></div></span>';
                }
            })
            let newRawProds = deRawProds.join(",");
            $("#tag_0").before(addHtml);
            $("#tag_0").val(newRawProds);
            $("#js_tag_box0").data("value", rawProdsInfo);
        }
    });
    var action = {
        check: function (pid, deRawProds) {
            if (deRawProds) {
                let re = $.inArray(String(pid), deRawProds);
                if (re === -1) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return false;
            }
        }
    }
    var getCheckBox = function (dom) {
        let list = [];
        $(dom).find(".layui-form-checkbox").each(function () {
            if ($(this).hasClass("layui-form-checked")) {
                list.push($(this).prev().data('code'));
            }
        })
        $("#custom_prod_type").val(list.join(","));
    }
    @endsection
</script>