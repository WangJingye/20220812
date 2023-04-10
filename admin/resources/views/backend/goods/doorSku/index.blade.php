@extends('backend.base')

@section('content')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <label class="layui-form-label">搜索栏</label>
                        <div class="layui-inline">
                            <input class="layui-input" name="prodId" autocomplete="off" placeholder="SKU ID">
                        </div>
                        <span class="layui-inline layui-btn" id="search">搜索</span>
                    </div>
                </form>
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
            url: "{{ route('backend.goods.doorSku.list') }}",
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
                {field: 'sku', title: 'SKU ID'}
                , {
                    title: '操作', width: 300, align: 'center', templet: function (d) {
                        let opt = '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">编辑SKU信息</a>';
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
        if (layEvent === 'edit') {
            layer.open({
                type: 2,
                area: ['100%', '100%'],
                offset: 't',
                maxmin: false,
                move: false,
                content: "{{ route('backend.goods.doorSku.get') }}?skuIdx=" + data.id,
                end: function () {
                    table.reload('table_list')
                }
            });
        }
    });
    $('#search').on('click', function () {
        table.reload('table_list', {
            page: {curr: 1},
            where: {
                prodId: $("input[name='prodId']").val(),
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