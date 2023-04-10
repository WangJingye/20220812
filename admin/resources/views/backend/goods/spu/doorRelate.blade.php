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
    })
    $(document).keyup(function (event) {
        if (event.keyCode == 13) {
            $("#search").trigger("click");
        }
    });
    @endsection
</script>