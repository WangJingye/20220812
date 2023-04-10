@extends('backend.base')

@section('content')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <label class="layui-form-label">搜索栏</label>
                        <div class="layui-inline">
                            <input class="layui-input" name="title" autocomplete="off" placeholder="标示位名称">
                        </div>
                        <span class="layui-inline layui-btn" id="search">搜索</span>
                        @if(auth()->user()->id==1)
                        <span class="layui-inline layui-btn" id="J_add_loc">新增标示位</span>
                        @endif
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
            url: "{{ route('backend.ad.location.list') }}?{{$query_string}}",
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
                {field: 'title', title: '标题'}
                , {field: 'start_time', width: 200, title: '开始时间'}
                , {field: 'end_time', width: 200, title: '结束时间'}
                , {field: 'remark', title: '说明'}
                , {
                    title: '操作', width: 300, align: 'center', templet: function (d) {
                        let opt = '';
                        if (d.status == 0) {
                            opt += '<a class="layui-btn layui-btn layui-btn-xs" lay-event="display_on">启用</a>';
                        } else {
                            opt += '<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="display_off">禁用</a>';
                        }
                         opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">编辑标示位</a>';
                            opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="ads">广告信息</a>';
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
                content: "{{ route('backend.ad.location.edit') }}?id=" + data.id,
                end: function () {
                    table.reload('table_list')
                }
            });
        } else if(layEvent === 'ads'){
            layer.open({
                type: 2,
                area: ['100%', '100%'],
                offset: 't',
                maxmin: false,
                move: false,
                content: "{{ route('backend.ad.item.index') }}?loc_id=" + data.id,
                end: function () {
                    table.reload('table_list')
                }
            });
        } else if(layEvent === 'display_on'){
            $.get("{{ route('backend.ad.location.update') }}?id="+data.id+"&status=1", function (res) {
                if (res.code === 1) {
                    layer.msg('操作成功', {
                        icon: 1,
                        shade: 0.3,
                        offset: '300px',
                        time: 2000 //2秒关闭（如果不配置，默认是3秒）
                        ,end: function () {
                            table.reload('table_list')
                        }
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
        } else if(layEvent === 'display_off'){
            $.get("{{ route('backend.ad.location.update') }}?id="+data.id+"&status=0", function (res) {
                if (res.code === 1) {
                    layer.msg('操作成功', {
                        icon: 1,
                        shade: 0.3,
                        offset: '300px',
                        time: 2000 //2秒关闭（如果不配置，默认是3秒）
                        ,end: function () {
                            table.reload('table_list')
                        }
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
        } else {
            layer.open({
                type: 2,
                area: ['100%', '100%'],
                offset: 't',
                maxmin: false,
                move: false,
                content: "{{ route('backend.ad.item.list') }}?loc_id=" + data.id,
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
                title: $("input[name='title']").val(),
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
            content: "{{ route('backend.ad.location.add') }}",
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