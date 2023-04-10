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
                            <input class="layui-input" name="colle_name" autocomplete="off" placeholder="集合名称">
                        </div>
                        <div class="layui-inline">
                            <select name="status">
                                <option value="">请选择</option>
                                <option value="1">已上架</option>
                                <option value="0">已下架</option>
                            </select>
                        </div>
                        <span class="layui-inline layui-btn" id="search">搜索</span>
                        <span class="layui-inline layui-btn" id="add">新增集合</span>
                    </div>
                </form>
                <table id="list" lay-filter="list"></table>
            </div>
        </div>
    </div>
@endsection

<script>
    @section('layui_script')
    $("#add").on('click',function () {
        layer.open({
            type: 2,
            area: ['100%', '100%'],
            offset: 't',
            maxmin: false,
            move: false,
            content: "{{ route('backend.goods.collection.add') }}"
        });
    });

    var dataTable = table.render({
        elem: '#list',
        id: 'table_list',
        height: 560,
        //数据接口
        url: "{{ route('backend.goods.collection.list') }}",
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
            , {field: 'product_name', title: '名称'}
            , {field: 'product_id', title: '套装ID'}
            , {
                field: 'kv_image', title: '头图', align: 'center', width: 150, templet: function (d) {
                    return '<img src="' + d.kv_image + '" width="100px">';
                }
            }
            , {field: 'product_desc', title: '套装描述'}
            , {
                title: '操作', width: 300, align: 'center', templet: function (d) {
                    let opt = '';
                    if (d.status === 0) {
                        opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="display_on">上架</a>';
                    } else {
                        opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="display_off">下架</a>';
                    }

                    opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" target="_blank" href="https://apiuat.dlc.com.cn/detail?id='+d.id+'-2" >查看</a>';
                    opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">编辑</a>';
                    opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="del">删除</a>';
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
                content: "{{ route('backend.goods.collection.get') }}?id=" + data.id,
                end: function () {
                    table.reload('table_list')
                }
            });
        }else if(layEvent === 'cms'){
            layer.open({
                type: 2,
                area: ['100%', '100%'],
                offset: 't',
                maxmin: false,
                move: false,
                content: "{{ route('backend.goods.collection.cms') }}?id=" + data.id,
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
                content: "{{ route('backend.goods.spu.relateSkus') }}?prodIdx=" + data.id,
                end: function () {
                    table.reload('table_list')
                }
            });
        }  else if (layEvent === 'display_on') {
            layer.confirm('确定上架产品吗？', function (index) {
                let subData = {};
                subData.id = data.id;
                subData.status = 1;
                $.get("{{ route('backend.goods.collection.changeStatus') }}", subData, function (res) {
                    table.reload('table_list')
                }, 'json');
                layer.close(index);
            });
        } else if (layEvent === 'display_off') {
            layer.confirm('确定下架产品吗？', function (index) {
                let subData = {};
                subData.id = data.id;
                subData.status = 0;
                $.get("{{ route('backend.goods.collection.changeStatus') }}", subData, function (res) {
                    table.reload('table_list')
                }, 'json');
                layer.close(index);
            });
        }else if (layEvent === 'del') {
            layer.confirm('确定删除商品集合吗？', function (index) {
                let subData = {};
                subData.id = data.id;
                subData.status = -1;
                $.get("{{ route('backend.goods.collection.changeStatus') }}", subData, function (res) {
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
                colle_name: $("input[name='colle_name']").val() ? $("input[name='colle_name']").val() : '',
                status: $("select[name='status']").find("option:selected").val() ? $("select[name='status']").find("option:selected").val() : '',
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