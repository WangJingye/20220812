@extends('backend.base')

@section('content')
    <style>
        .layui-table-cell {
            height: auto !important;
            white-space: normal;
        }
        textarea{
            width: 120px; padding: 10px 0px;
        }
        body{overflow-y: scroll;}
    </style>
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <!--
                        <div class="layui-inline">
                            <input class="layui-input" name="product_name" autocomplete="off" placeholder="产品名称">
                        </div>
                        <span class="layui-inline layui-btn" id="search">搜索</span>
                        -->
                    </div>
                </form>


                <div class="layui-container " >
                    <span class="layui-inline layui-btn" id="J_add_ad">新增推荐</span>
                    <!--
                    <table id="list" lay-filter="list"></table>
                    -->

                    <form ref="form" class="layui-form" method="post" id="J_container" >

                    </form>
                </div>

                </div>
            </div>
        </div>
    </div>
@endsection


<div id="J_row_tpl" style="display: none;">
    <div class="layui-row" style="border: gray dashed 1px;padding: 10px;margin-top: 10px">

        <div class="layui-col-md1">
            ID:<span class="J_id" ></span>
        </div>
        <div class="layui-col-md2">
            <span>商品名称:</span>
            <input name="name" value="" class="layui-input layui-input-inline" type="text" style="width: 120px" />
        </div>
        <div class="layui-col-md3">
            <span>图片:</span>
            <input class="layui-input layui-input-inline " name="img" type="text" style="width: 120px" />
            <button type="button" class="layui-btn J_img">上传</button>
        </div>
        <div class="layui-col-md4">
            <span>时间:</span>
            <input class="layui-input layui-input-inline J_start_time" name="start_time" type="text" style="width: 150px" />
            &nbsp;~&nbsp;<input class="layui-input layui-input-inline J_end_time" name="end_time" type="text" style="width: 150px" />
        </div>
        <div class="layui-col-md3">
            <span>小程序链接:</span>
            <input class="layui-input layui-input-inline" name="link" type="text" style="width: 120px" />
        </div>
        <div class="layui-col-md3 row_textarea">
            <span>PC链接:</span>
            <textarea class="layui-input layui-input-inline" name="data1" type="text" style="width: 150px;" ></textarea>
        </div>
        <!--
        <div class="layui-col-md2 ">
            <span>data2:</span>
            <textarea class="layui-input layui-input-inline" name="data2" type="text" style="width: 120px;" ></textarea>
        </div>
        <div class="layui-col-md2 ">
            <span>data3:</span>
            <textarea class="layui-input layui-input-inline" name="data3" type="text" style="width: 120px;" ></textarea>
        </div>
        <div class="layui-col-md2 ">
            <span>data4:</span>
            <textarea class="layui-input layui-input-inline" name="data4" type="text" style="width: 120px;" ></textarea>
        </div>
        <div class="layui-col-md2">
            <span>data5:</span>
            <textarea class="layui-input layui-input-inline" name="data5" type="text" style="width: 120px;" ></textarea>
        </div>
        <div class="layui-col-md2">
            <span>data6:</span>
            <textarea class="layui-input layui-input-inline" name="data6" type="text" style="width: 120px;" ></textarea>
        </div>
        <div class="layui-col-md2">
            <span>data7:</span>
            <textarea class="layui-input layui-input-inline" name="data7" type="text" style="width: 120px;" ></textarea>
        </div>
        <div class="layui-col-md2">
            <span>data8:</span>
            <textarea class="layui-input layui-input-inline" name="data8" type="text" style="width: 120px;" ></textarea>
        </div>
        <div class="layui-col-md2">
            <span>data9:</span>
            <textarea class="layui-input layui-input-inline" name="data9" type="text" style="width: 120px;" ></textarea>
        </div>
        <div class="layui-col-md2">
            <span>data10:</span>
            <textarea class="layui-input layui-input-inline" name="data10" type="text" style="width: 120px;" ></textarea>
        </div>
        -->
        <div class="layui-col-md1">
            <span>排序:</span>
            <input class="layui-input layui-input-inline" name="asort" type="text" style="width: 50px" />
        </div>
        <div class="layui-col-md2">
            <button type="button" data-id="" data-locid="{{$loc_id}}" class="layui-btn J_save">保存</button>
        </div>
    </div>
</div>

<input id="J_list_json" type="hidden" value='{{$list_json}}' />

<script>


    @section('layui_script')
    layui.use(['upload', 'form','laydate'], function () {

        function initList() {
            var jsonStr = $("#J_list_json").val();
            var jsonArr = JSON.parse(jsonStr);
            var row = $("#J_row_tpl");
            $.each(jsonArr,function (i,record) {
                var tpl = addRecord(record);
                // tpl.find(".J_id").html(record.id);
                // tpl.find(".J_save").attr('data-id',record.id);
                // $.each(record,function (k,v) {
                //     tpl.find("input[name="+k+"]").attr('value',v);
                //     tpl.find("textarea[name="+k+"]").html(v);
                // });
                $("#J_container").append(tpl.html());
                form.render();
            });
        }
        initList();


        var upload = layui.upload;

        upload.render({
            elem: '.J_img' //绑定元素
            , url: "{{ route('backend.file.uploadPic') }}" //上传接口
            , accept: 'file' //普通文件
            , done: function (res) {
                var item = this.item;
                //如果上传失败
                if (res.code > 0) {
                    return layer.msg('上传失败');
                } else {
                    var path = res.path;
                    $(item).prev().val(path);
                    return layer.msg('上传成功');
                }
            }
            , error: function () {
                //请求异常回调
            }
        });
        //自定义验证规则
        form.verify({});

        //执行一个laydate实例
        laydate.render({
            elem: '.J_start_time' //指定元素
            ,type: 'datetime'
            ,format:'yyyy-MM-dd HH:mm:ss'
        });

        laydate.render({
            elem: '.J_end_time' //指定元素
            ,type: 'datetime'
            ,format:'yyyy-MM-dd HH:mm:ss'
        });




        function addRecord(record) {
            var row = $("#J_row_tpl");
            var tpl = row.clone();
            tpl.find(".J_id").html(record.id);
            tpl.find(".J_save").attr('data-id',record.id);
            var adStatus = 0;
            var adStatusDesc = "禁用";
            if(record.status == 0) {
                adStatus=1;
                adStatusDesc = "启用";
                tpl.find(".J_opt").removeClass('layui-btn-danger');
            }
            tpl.find(".J_opt").attr('data-id',record.id);
            tpl.find(".J_opt").attr('data-status',adStatus);
            tpl.find(".J_opt").html(adStatusDesc);

            $.each(record,function (k,v) {
                tpl.find("input[name="+k+"]").attr('value',v);
                tpl.find("textarea[name="+k+"]").html(v);
            });
            return tpl;
            // $("#J_container").append(tpl.html());
        }

        $("#J_add_ad").on('click',function () {
            $.get("{{route('backend.ad.item.insert')}}?loc_id={{$loc_id}}",function (res) {
                if(res.code == 1){
                    var tpl = addRecord(res.data);
                    $("#J_container").prepend(tpl.html());
                    form.render();
                }
            })
        });

    });

    // $('#J_container').on('click','.J_img',function () {
    //     $('#J_container').find('.J_img').removeClass('J_up');
    //     $(this).prev().addClass('J_up');
    // });


    $("#J_container").on('click','.J_save',function () {
        me = $(this);
        var parent = me.parents('.layui-row');
        let str = "";
        parent.find('input').each(function (i) {
            var k = $(this).attr('name');
            var v = $(this).val();
            if(v){
                if(str){
                    str += ',';
                }
                str += '"'+k+'":"'+v+'"';
            }
        });
        parent.find('textarea').each(function (i) {
            var k = $(this).attr('name');
            var v = $(this).val();
            if(v){
                if(str){
                    str += ',';
                }
                str += '"'+k+'":"'+v+'"';
            }
        });
        str = '{'+str+'}';
        obj = $.parseJSON(str);
        obj.id = me.data('id');
        obj.loc_id = me.data('locid');
        $.get("{{ route('backend.ad.item.update') }}",obj,function (res) {
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
        },'json');

        console.log(str);
    });

    $("#J_container").on('click','.J_opt',function () {
        me = $(this);
        var obj = new Object();
        obj.id = me.data('id');
        obj.status = me.data('status');

        $.get("{{ route('backend.ad.item.update') }}",obj,function (res) {
            if (res.code === 1) {
                layer.msg('操作成功', {
                    icon: 1,
                    shade: 0.3,
                    offset: '300px',
                    time: 2000 //2秒关闭（如果不配置，默认是3秒）
                }, function () {
                    if(obj.status){
                        me.attr('data-status',1);
                        me.html("禁用");
                        me.addClass('layui-btn-danger');
                    }else{
                        me.attr('data-status',0);
                        me.html("启用");
                        me.removeClass('layui-btn-danger');
                    }
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
        },'json');

        console.log(str);
    });

    var dataTable = table.render({
        elem: '#list',
        id: 'table_list',
        height: 560,
        //数据接口
        url: "{{ route('backend.ad.item.list') }}?loc_id=1&{{$query_string}}",
        //开启分页
        page: true,
        method: 'get',
        limit: 10,
        text: {
            //默认：无数据
            none: '暂无相关数据'
        },
        parseData: function (res) {
            return {
                "code": 0,
                "data": res.pageData,
                "count": res.count
            }
        },
        resizing: function () {
            table.resize('table_list');
        },
        //表头
        cols: [[
             {field: 'id', title: 'ID'}
             ,{field: 'name', width:200,edit:'text', title: '名称'}
             ,{field: 'link',width:200, title: '名称'}
             ,{field: 'start_time',width:200, title: '名称'}
             ,{field: 'end_time',width:200, title: '名称'}
             ,{field: 'data1',width:200, title: 'data1'}
             ,{field: 'data2',width:200, title: 'data2'}
             ,{field: 'data3', title: 'data3'}
             ,{field: 'data4', title: 'data4'}
             ,{field: 'data5', title: 'data5'}
             ,{field: 'data6', title: 'data6'}
             ,{field: 'data7', title: 'data7'}
             ,{field: 'data8', title: 'data8'}
             ,{field: 'data9', title: 'data9'}
             ,{field: 'data10', title: 'data10'}
            , {
                field: 'img', title: '头图', align: 'center', width: 150, templet: function (d) {
                    return '<img src="' + d.img + '" width="100px">';
                }
            }
            , {field: 'product_id', title: '产品ID'}
            // , {field: 'price', title: '价格'}
            // , {field: 'store', sort:true,title: '库存'}
            // , {field: 'display_status_name', title: '状态'}
            , {
                title: '操作', align: 'center', templet: function (d) {
                    let opt = '';
                    if (d.status == 0) {
                        opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="display_on">上架</a>';
                    } else {
                        opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="display_off">下架</a>';
                    }

                    opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">编辑</a>';
                    opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="sku">查看SKU列表</a>';

                    return opt;
                }
            }
        ]]
    });
           // 自定义排序
    table.on('sort(list)', function (obj) {
         let type = obj.type,
             field = obj.field,
             data = obj.data,//表格的配置Data
             thisData = [];

         //将排好序的Data重载表格
         table.reload('table_list', {
             initSort: obj,
              where:{
                field:'sort',
                order:type
            }
        });
     });

    table.on('tool(list)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
        var data = obj.data; //获得当前行数据
        console.log(data);
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        var tr = obj.tr; //获得当前行 tr 的DOM对象
        if (layEvent === 'edit') {
            layer.open({
                type: 2,
                area: ['100%', '100%'],
                offset: 't',
                maxmin: false,
                move: false,
                content: "{{ route('backend.goods.spu.get') }}?id=" + data.id,
                end: function () {
                    table.reload('table_list')
                }
            });
        } else if (layEvent === 'sku') {
            layer.open({
                title: 'SKU排序',
                type: 2,
                area: ['100%', '100%'],
                offset: 't',
                maxmin: false,
                move: false,
                content: "{{ route('backend.goods.sku') }}?product_idx=" + data.id,
                end: function () {
                    table.reload('table_list')
                }
            });
        }  else if (layEvent === 'display_on') {
            layer.confirm('确定上架产品吗？', function (index) {
                let subData = {};
                subData.id = data.id;
                subData.status = 1;
                $.get("{{ route('backend.goods.spu.changeStatus') }}", subData, function (res) {
                    table.reload('table_list')
                }, 'json');
                layer.close(index);
            });
        } else if (layEvent === 'display_off') {
            layer.confirm('确定下架产品吗？', function (index) {
                let subData = {};
                subData.id = data.id;
                subData.status = 0;
                $.get("{{ route('backend.goods.spu.changeStatus') }}", subData, function (res) {
                    table.reload('table_list')
                }, 'json');
                layer.close(index);
            });
        } else if (layEvent === 'raw') {
            window.open("{{ route('backend.goods.spu.rawData') }}?catalogItem=" + data.master_catalog_item);
        }
    });
    $('#search').on('click', function () {
        table.reload('table_list', {
            page: {curr: 1},
            where: {
                product_name: $("input[name='product_name']").val() ? $("input[name='product_name']").val() : '',
                status: $("select[name='status']").find("option:selected").val() ? $("select[name='status']").find("option:selected").val() : '',
            }
        });
    });

    $("#J_add_product").on('click',function () {
        layer.open({
            type: 2,
            area: ['100%', '100%'],
            offset: 't',
            maxmin: false,
            move: false,
            content: "{{ route('backend.goods.spu.add') }}",
            end: function () {
                table.reload('table_list')
            }
        });
    })
    $(document).keyup(function (event) {
        if (event.keyCode == 13) {
            $("#search").trigger("click");
        }
    });
    @endsection
</script>