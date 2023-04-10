@extends('backend.base')

@section('content')

    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body">
                <form class="layui-form layui-form-pane"  method="POST">
                    <div class="layui-form-item">
                        <span class="layui-btn sub" id="add" lay-event="add">添加</span>
                    </div>
                </form>
                <table id="list" lay-filter="list"></table>
            </div>
        </div>
    </div>
@endsection
<script src="{{ url('/static/admin/js/jquery.min.js') }}"></script>
<script src="{{ url('/static/admin/laydate.js') }}"></script>
<style>
    .layui-table-cell {
        height: auto !important;
        white-space: normal;
    }
</style>
<script>

    @section('layui_script')

    table.render({
            elem: '#list'
            , id: 'table_list'
            , height: 500
            , url: "{{ route('backend.point.dataList') }}" //数据接口
            , page: true //开启分页
            , limits: [10]
            , limit: 10 //每页默认显示的数量
            , method:'post'
            , cols: [
                [ //表头
                    {
                        field: 'id', title: 'ID', sort: true, width:200
                    },
                    {
                        field: 'status', title: '状态', sort: true, width:200
                    },
                    {
                        field: 'coupon_id', title: '优惠劵ID', sort: true, width:200
                    },
                    {
                        field: 'exchange_point', title: '兑换积分', sort: true, width:200
                    },

                    ,{
                    title: '操作', width: 300, align: 'center', templet: function (d) {
                        let opt = '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">编辑</a>';
                        return opt;
                    }
                }

                ]
            ]
            , response: {
                statusName: 'code' //规定数据状态的字段名称，默认：code
                , statusCode: 0 //规定成功的状态码，默认：0
                , msgName: 'msg' //规定状态信息的字段名称，默认：msg
            }
        }
    );

    table.on('sort(list)', function (obj) {
        let type = obj.type,
            field = obj.field,
            data = obj.data,//表格的配置Data
            thisData = [];

        //将排好序的Data重载表格
        table.reload('table_list', {
            initSort: obj,
            where:{
                order:field,
                dir:type
            }
        });
    });

    table.on('tool(list)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
        var data = obj.data; //获得当前行数据
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        var tr = obj.tr; //获得当前行 tr 的DOM对象
        if(layEvent === 'edit'){
            layer.open({
                type: 2,
                area: ['100%', '800px'],
                offset: 't',
                maxmin: true,
                fixed: false,
                content: "{{ route('backend.point.get') }}?id="+data.id,
                end: function(){
                    active['reload'].call(this);
                }
            });
        }


    });

    var active = {
        reload: function(){
            table.reload('table_list', {
                page: {curr: 1},
                where: {
                }
            });
        }
    };
    $('#add').on('click', function(){
        layer.open({
            type: 2,
            area: ['100%', '800px'],
            offset: 't',
            maxmin: true,
            fixed: false,
            content: "{{ route('backend.point.get') }}",
            end: function(){
                active['reload'].call(this);
            }
        });
    });









    @endsection
</script>