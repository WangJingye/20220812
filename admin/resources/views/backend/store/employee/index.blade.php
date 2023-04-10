@extends('backend.base')

@section('content')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <label class="layui-form-label">搜索栏</label>
                        <div class="layui-inline">
                            <input class="layui-input" name="name" autocomplete="off" placeholder="员工名字">
                        </div>
                        <div class="layui-inline">
                            <input class="layui-input" name="store_name" autocomplete="off" placeholder="门店">
                        </div>
                        <div class="layui-inline">
                            <input class="layui-input" name="role_name" autocomplete="off" placeholder="职位">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <span class="layui-inline layui-btn" id="search">搜索</span>
                        <span class="layui-inline layui-btn" id="J_add_loc">新增员工</span>
                        <span class="layui-inline layui-btn" id="bind_all">员工绑定</span>
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
        url: "{{ route('backend.store.employee.list') }}?{{$query_string}}",
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
            {field: 'name', title: '导购姓名'}
            , {field: 'sid', title: '导购id'}
            , {field: 'phone', title: '手机号'}
            , {field: 'store_name', title: '门店'}
            , {field: 'role_name', title: '职位'}
            , {field: 'status', title: '职位状态',templet: function(d){
                    return (d.status==1)?'在职':'离职';
                }}
            , {field: 'is_bind', title: '绑定用户',templet: function(d){
                    return (d.is_bind==1)?'是':'否';
                }}
            , {field: 'created_at',title: '创建时间'}
            , {title: '操作', align: 'center', templet: function (d){
                    let opt = '';
                    opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">编辑</a>';
                    return opt;
                }
            }
        ]]
    });
    table.on('tool(list)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
        var data = obj.data; //获得当前行数据
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        if (layEvent === 'edit') {
            layer.open({
                type: 2,
                area: ['100%', '100%'],
                offset: 't',
                maxmin: false,
                move: false,
                content: "{{ route('backend.store.employee.edit') }}?id=" + data.id,
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
                name: $("input[name='name']").val(),
                store_name: $("input[name='store_name']").val(),
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
            content: "{{ route('backend.store.employee.add') }}",
            end: function () {
                table.reload('table_list')
            }
        });
    });
    $("#bind_all").on('click',function () {
        let url = "{{route('backend.store.employee.bindAll')}}";
        var loading = layer.load(1, {shade: [0.3]});
        $.get(url, function (res) {
            if (res.code === 1) {
                layer.msg('绑定成功', {
                    icon: 1,
                    shade: 0.3,
                });
            } else {
                layer.msg(res.message, {
                    icon: 2,
                    shade: 0.3,
                });
            }
            layer.close(loading);
            table.reload('table_list')
        });
    });

    $(document).keyup(function (event) {
        if (event.keyCode == 13) {
            $("#search").trigger("click");
        }
    });
    @endsection
</script>