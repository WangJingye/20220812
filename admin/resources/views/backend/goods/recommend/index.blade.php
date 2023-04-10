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
                            <input class="layui-input" name="flag" autocomplete="off" placeholder="标识">
                        </div>
                        <span class="layui-inline layui-btn" id="search">搜索</span>
                        <span class="layui-inline layui-btn" id="add">新增推荐</span>
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
            content: "{{ route('backend.goods.recommend.add') }}",
            end: function () {
                table.reload('table_list')
            }
        });
    });

    layui.use('table', function(){
        // var table = layui.table;
        //
        // //监听单元格编辑
        // table.on('edit(list)', function(obj){
        //     var value = obj.value //得到修改后的值
        //         ,data = obj.data //得到所在行所有键值
        //         ,field = obj.field; //得到字段
        //     layer.msg('[ID: '+ data.id +'] ' + field + ' 字段更改为：'+ value);
        // });




    });

    var dataTable = table.render({
        elem: '#list',
        id: 'table_list',
        height: 560,
        //数据接口
        url: "{{ route('backend.goods.recommend.list') }}",
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
            table.reload('table_list');
        },
        //表头
        cols: [[
            {field: 'id', title: 'ID'}
            , {field: 'cat_id',edit:'text', title: '类目ID'}
            , {field: 'flag',edit:'text', title: '推荐标识'}
            , {field: 'rec_desc',edit:'text', title: '推荐描述'}
            , {
                title: '操作', width: 300, align: 'center', templet: function (d) {
                    let opt = '';
                    if (d.deleted_at === null) {
                        if (d.status === 0) {
                            opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="display_on">启用</a>';
                        } else {
                            opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="display_off">禁用</a>';
                        }
                    }

                    opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="save">保存</a>';

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
        if (layEvent === 'save') {
            // let subData = {};
            // subData.id = data.id;
            // subData.cat_id = $('#J_catid').val();
            // subData.flag = $('#J_flag').val();
            $.get("{{ route('backend.goods.recommend.changeStatus') }}", data, function (res) {
                if(res.code > 0){
                    layer.msg('更新成功');
                }else{
                    layer.msg('更新失败');
                }
                // console.log(res);
                // table.reload('table_list')
            }, 'json');
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