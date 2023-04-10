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
                        <div class="layui-inline">
                            <input class="layui-input" name="product_id" autocomplete="off" placeholder="产品ID">
                        </div>
                        <div class="layui-inline">
                            <input class="layui-input" name="product_name" autocomplete="off" placeholder="产品名称">
                        </div>
                        <div class="layui-inline">
                            <select name="status">
                                <option value="">请选择</option>
                                <option value="1">已上架</option>
                                <option value="0">已下架</option>
                            </select>
                        </div>
                        商品类型:
                        <div class="layui-inline">
                            <select name="product_type">
                                <option value="">请选择</option>
                                <option value="0">小样</option>
                                <option value="1">正装</option>
                            </select>
                        </div>
                        <span class="layui-inline layui-btn" id="search">搜索</span>
                        <span class="layui-inline layui-btn" id="J_add_product">新增产品</span>
                        <span style="display: none" class="layui-inline layui-btn" id="upload">产品导入</span>
                        <a style="font-size: 14px;color:blue;display: none;" href="/file/导入商品模板.csv">模板下载</a>
                        <div class="layui-inline">
                            <input class="layui-input" name="page" autocomplete="off" placeholder="页码 如:2,每次导出50条商品">
                        </div>
                        <span class="layui-inline layui-btn" id="export">产品导出</span>
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
            , url: "{{ route('backend.goods.spu.import') }}" //上传接口
            , accept: 'file' //普通文件
            , done: function (res) {
                //如果上传失败
                if (res.code > 0) {
                    return layer.msg('成功导入');
                } else {
                    return layer.msg('上传失败');
                }
            }
            , error: function () {
                //请求异常回调
            }
        });
        //自定义验证规则
        form.verify({});
    });
    var dataTable = table.render({
        elem: '#list',
        id: 'table_list',
        height: 560,
        //数据接口
        url: "{{ route('backend.goods.spu.list') }}?{{$query_string}}",
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
             ,{field: 'product_name', title: '名称'}
            , {
                field: 'pic_url', title: '头图', align: 'center', width: 150, templet: function (d) {
                    return '<img src="' + d.list_img + '" width="100px">';
                }
            }
            , {field: 'product_id', title: '产品ID'}
            // , {field: 'price', title: '价格'}
            // , {field: 'store', sort:true,title: '库存'}
            // , {field: 'display_status_name', title: '状态'}

            , {
                title: '操作', align: 'center', templet: function (d) {
                    let opt = '';
                    if ( (d.status === 2) || (d.status === 0)) {
                        opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="display_on">上架</a>';
                    } else if(d.status === 1) {
                        opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="display_off">下架</a>';
                    }

                    opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">编辑</a>';
                    opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="sku">查看SKU列表</a>';
                    opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="cms">图文</a>';

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
        } else if(layEvent === 'cms'){
            layer.open({
                type: 2,
                area: ['100%', '100%'],
                offset: 't',
                maxmin: false,
                move: false,
                content: "{{ route('backend.goods.spu.cms') }}?id=" + data.id,
                end: function () {
                    table.reload('table_list')
                }
            });
        }else if (layEvent === 'sku') {
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
                product_id: $("input[name='product_id']").val() ? $("input[name='product_id']").val() : '',
                status: $("select[name='status']").find("option:selected").val() ? $("select[name='status']").find("option:selected").val() : '',
                product_type: $("select[name='product_type']").find("option:selected").val() ? $("select[name='product_type']").find("option:selected").val() : '',
            }
        });
    });

    $('#export').on('click',function () {
        var product_name = $("input[name='product_name']").val() ? $("input[name='product_name']").val() : '';
        var product_id = $("input[name='product_id']").val() ? $("input[name='product_id']").val() : '';
        var status = $("select[name='status']").find("option:selected").val() ? $("select[name='status']").find("option:selected").val() : '';
        var page = $("input[name='page']").val() ? $("input[name='page']").val() :1;

        window.open('/admin/goods/spu/export?page='+page+'&product_name='+product_name+'&product_id='+product_id+'&status='+status,"_blank");
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
    });
    $(document).keyup(function (event) {
        if (event.keyCode == 13) {
            $("#search").trigger("click");
        }
    });
    @endsection
</script>

