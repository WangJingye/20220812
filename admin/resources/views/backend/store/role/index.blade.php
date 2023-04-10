@extends('backend.base')

@section('content')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <label class="layui-form-label">搜索栏</label>
                        <div class="layui-inline">
                            <input class="layui-input" name="role_name" autocomplete="off" placeholder="职位名称">
                        </div>
                        <span class="layui-inline layui-btn" id="search">搜索</span>
                        <span class="layui-inline layui-btn" id="J_add_loc">新增职位名称</span>
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
            url: "{{ route('backend.store.role.list') }}?{{$query_string}}",
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
                    "data": res.data.pageData,
                    "count": res.data.count
                }
            },
            resizing: function () {
                table.resize('table_list');
            },
            //表头
            cols: [[
                {field: 'role_name', title: '职位名称'}
                , {field: 'created_at', width: 200, title: '创建时间'}
                , {
                    title: '操作', width: 300, align: 'center', templet: function (d) {
                        let opt = '';
                         opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit" href="edit?id='+d.id+'">编辑职位信息</a>';
                        return opt;
                    }
                }
            ]]
        });

    $('#search').on('click', function () {
        table.reload('table_list', {
            page: {curr: 1},
            where: {
                role_name: $("input[name='role_name']").val(),
            }
        });
    });

    $("#J_add_loc").on('click',function () {
        layer.open({
            type: 2,
            area: ['100%', '100%'],
            offset: 't',
            maxmin: false,
            move: false,
            content: "{{ route('backend.store.role.add') }}",
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