@extends('backend.base')

@section('content')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body">
                <table id="list" lay-filter="list"></table>
                <script type="text/html" id="toolbar">
                    <div class="layui-btn-container">
{{--                        <button class="layui-btn layui-btn-sm" lay-event="store">保存修改</button>--}}
                    </div>
                </script>
            </div>
        </div>
    </div>
@endsection

<script>
    @section('layui_script')
    // 自定义模块，这里只需要开放soulTable即可
    layui.config({
        base: '/ext/',   // 模块所在目录
    }).extend({
        soulTable: 'soulTable'  // 模块别名
    });
    layui.use(['table', 'soulTable'], function () {
        var table = layui.table, soulTable = layui.soulTable;
        var dataTable = table.render({
            elem: '#list'
            , id: 'table_list'
            // ,height: 500
            , data: {!! $detail !!} //数据接口
            , page: false //开启分页
            , method: 'post'
            , toolbar: '#toolbar' //开启头部工具栏，并为绑定左侧模板
            , defaultToolbar: ['filter']
            , limit: Number.MAX_VALUE
            , text: {
                none: '暂无相关数据' //默认：无数据
            }
            , rowDrag: {
                done: function (obj) {
                    this.data = obj.cache;
                }
            }
            // ,where: {pid: '1'}
            , cols: [[ //表头
                {field: 'sku', title: 'SKU ID'}
                , {field: 'created_at', title: '创建时间'}
                , {field: 'updated_at', title: '更新时间'}
                // ,{field: 'sort', title: '排序（可编辑）', edit: 'text'}
            ]]
            , done: function () {
                soulTable.render(this)
            }
        });
        table.on('toolbar(list)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
            var layEvent = obj.event;
            if (layEvent === 'store') {
                let data = obj.config.data;
                $.post("{{ route('backend.goods.spu.editRelateSkus') }}", {"data": JSON.stringify(data)}, function (res, status) {
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
            }
        });
        //监听单元格编辑
        table.on('edit(list)', function (obj) {
            console.log(obj)
        });
    })
    $(document).keyup(function (event) {
        if (event.keyCode == 13) {
            $("#search").trigger("click");
        }
    });
    @endsection
</script>