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
                <div class="layui-form-item">
                    <span class="layui-inline layui-btn" id="add">创建匹配规则</span>
                </div>
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
            height: 500,
            //数据接口
            url: "{{ route('backend.config.spp.list') }}",
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
            //表头
            cols: [[
                {field: 'id', width: 100, title: 'ID'}
                , {
                    field: 'picture', title: '图片', align: 'center', width: 150, templet: function (d) {
                        return '<img src="' + d._image + '" width="100px">'
                    }
                }
                , {field: '_rule_type', title: '类型'}
                , {field: '_content', title: '内容'}
                , {
                    fixed: 'right', title: '操作', width: 300, align: 'center', templet: function (d) {
                        let opt = '';
                        opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="look">查看</a>';
                        opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">编辑</a>';
                        opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="del">删除</a>';

                        return opt;
                    }
                }
            ]]
        });
    table.on('tool(list)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
        var data = obj.data; //获得当前行数据
        console.log(data);
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        var tr = obj.tr; //获得当前行 tr 的DOM对象
        if (layEvent === 'look') {
            layer.open({
                type: 2,
                area: ['100%', '100%'],
                offset: 't',
                maxmin: false,
                move: false,
                content: "{{ route('backend.config.spp.look') }}?ruleId=" + data.id,
                end: function () {
                    table.reload('table_list')
                }
            });
        } else if (layEvent === 'edit') {
            layer.open({
                type: 2,
                area: ['100%', '100%'],
                offset: 't',
                maxmin: false,
                move: false,
                content: "{{ route('backend.config.spp.get') }}?ruleId=" + data.id,
                end: function () {
                    table.reload('table_list')
                }
            });
        } else if (layEvent === 'del') {
            layer.confirm('确定删除吗？', function (index) {
                let subData = {};
                subData.ruleId = data.id;
                $.post("{{ route('backend.config.spp.del') }}", subData, function (res) {
                    table.reload('table_list')
                }, 'json');
                layer.close(index);
            });
        }
    });
    $('#add').on('click', function () {
        location.href = "{{ route('backend.config.spp.add') }}";
    });
    $(document).keyup(function (event) {
        if (event.keyCode == 13) {
            $("#search").trigger("click");
        }
    });
    @endsection
</script>