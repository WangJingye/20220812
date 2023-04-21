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
                <span class="layui-inline layui-btn" id="add-gold">添加</span>
                <table id="list" lay-filter="list"></table>
            </div>
        </div>
    </div>
@endsection

<script>
    @section('layui_script')
    var dataTable = table.render({
        elem: '#list',
        id: 'table_list',
        height: 560,
        //数据接口
        url: "{{ route('backend.gold.list') }}",
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
            , {field: 'gold_name', title: '名称'}
            , {field: 'price', title: '储值卡售价'}
            , {field: 'rate', title: '倍数'}
            , {field: 'face_value', title: '面值'},
            {
                field: 'valid_time', title: '有效期', width: 150, templet: function (d) {
                    return d.valid_time + '年';
                }
            },
            {
                field: 'link_start_time', title: '链接有效期', width: 320, templet: function (d) {
                    return d.link_start_time + '~'+ d.link_end_time ;
                }
            }
            , {
                title: '操作', align: 'center', templet: function (d) {
                    let opt = '';
                    if ((d.status === 2)) {
                        opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="display_on">上架</a>';
                    } else if (d.status === 1) {
                        opt += '<a class="layui-btn layui-btn-xs" lay-event="display_off">下架</a>';
                    }
                    opt += '<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="delete">删除</a>';

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
            where: {
                field: 'sort',
                order: type
            }
        });
    });

    table.on('tool(list)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
        var data = obj.data; //获得当前行数据
        console.log(data);
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        var tr = obj.tr; //获得当前行 tr 的DOM对象
        if (layEvent === 'display_on') {
            layer.confirm('确定上架储值卡吗？', function (index) {
                let subData = {};
                subData.id = data.id;
                subData.status = 1;
                $.get("{{ route('backend.gold.changeStatus') }}", subData, function (res) {
                    table.reload('table_list')
                }, 'json');
                layer.close(index);
            });
        } else if (layEvent === 'display_off') {
            layer.confirm('确定下架储值卡吗？', function (index) {
                let subData = {};
                subData.id = data.id;
                subData.status = 2;
                $.get("{{ route('backend.gold.changeStatus') }}", subData, function (res) {
                    table.reload('table_list')
                }, 'json');
                layer.close(index);
            });
        } else if (layEvent === 'delete') {
            layer.confirm('确定删除储值卡吗？', function (index) {
                let subData = {};
                subData.id = data.id;
                subData.status = 0;
                $.get("{{ route('backend.gold.delete') }}", subData, function (res) {
                    table.reload('table_list')
                }, 'json');
                layer.close(index);
            });
        }
    });

    $("#add-gold").on('click', function () {
        layer.open({
            type: 2,
            area: ['100%', '100%'],
            offset: 't',
            maxmin: false,
            move: false,
            content: "{{ route('backend.gold.add') }}",
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

