@extends('backend.base')

@section('content')
<div class="layui-col-md12">
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form layui-form-pane" action="">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <span class="layui-btn sub " id="back">返回</span>
                    </div>
                </div>
            </form>
            <table id="list" lay-filter="list"></table>
            <script type="text/html" id="toolbar">
            </script>
        </div>
    </div>
</div>
@endsection

<script>
@section('layui_script')
    var dataTableIns = table.render({
        elem: '#list'
        ,id: 'table_list'
        ,height: 500
        ,url: "{{ route('backend.coupon.mycoupon.dataList',['uid'=>$uid]) }}" //数据接口
        ,page: true //开启分页
        ,method:'post'
        ,cols: [[ //表头
            {field: 'coupon_id', width:80, title: 'ID'}
            ,{field: 'display_name', title: '名称'}
            ,{field: 'type',title:"类型", width:80, templet:function (d) {
                if(d.type==1){return '满减券'}else{return '随单礼券'}
            }}
            ,{field: 'status',title:"状态", width:80, templet:function (d) {
                if(d.status==0){return '未核销'}else{return '已核销'}
            }}
            ,{field: 'start_time', width:180, title: '开始时间'}
            ,{field: 'end_time', width:180, title: '结束时间'}
            ,{field: 'created_at', width:180, title: '获取时间'}
        ]],
        parseData: function (res) {
            return {
                "code": res.code?0:1,
                "data": res.data.data,
                "count": res.data.total
            }
        },
        done: function(res, curr, count){}
    });
    table.on('tool(list)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
        var data = obj.data; //获得当前行数据
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        var tr = obj.tr; //获得当前行 tr 的DOM对象
    });
    function getGlobal(){
        return dataTableIns;
    }

    function cssReloadTable(){
        dataTableIns.reload({
            page: {curr: $(".layui-laypage-em").next().html()},
            where: {
                uid: {{$uid}},
            }
        });
    }
    $('#search').on('click', function(){
        var type = 'search';
        active[type] ? active[type].call(this) : '';
    });
    $('#back').on('click', function(){
        location.href = '{{url('admin')}}/member/index';
    });
    var active = {
        reload: function(){
            dataTableIns.reload({
                page: {curr: dataTableIns.config.page.curr ?dataTableIns.config.page.curr:1},
            });
        },
        search: function(){
            dataTableIns.reload({
                page: {curr: 1},
                where: {
                    uid: {{$uid}},
                }
            });
        }
    };
    lay("input[name='start_time']").on('click', function(e){
        laydate.render({
            elem: "input[name='start_time']"
            ,type: 'datetime'
        });
    });
    lay("input[name='end_time']").on('click', function(e){
        laydate.render({
            elem: "input[name='end_time']"
            ,type: 'datetime'
        });
    });
    $(document).keyup(function(event){
        if(event.keyCode ==13){
            $("#search").trigger("click");
        }
    });

@endsection
</script>