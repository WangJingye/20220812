@extends('backend.base')

@section('content')
<div class="layui-col-md12">
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form layui-form-pane" action="">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">省份</label>
                        <div class="layui-input-inline">
                            <input class="layui-input" id="province" name="province" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item"-->
                    <div class="layui-inline">
                        <span class="layui-btn sub " id="search">搜索</span>
                        <span class="layui-btn sub layui-btn-warm" id="add">创建邮费配置</span>
                        <span class="layui-btn sub layui-btn-normal" id="config_default">更改默认配置</span>
                    </div>
                </div>
            </form>
            <table id="list" lay-filter="list"></table>
            <script type="text/html" id="toolbar">
                <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">编辑</a>
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
        ,url: "{{ route('backend.shipfee.dataList') }}" //数据接口
        ,page: true //开启分页
        ,method:'post'
        ,cols: [[ //表头
            {checkbox: true,fixed: true}
            ,{field: 'id', title: 'ID', sort: true,width:80}
            ,{field: 'province', title: '省份'}
            ,{field: 'ship_fee', title: '邮费'}
            ,{field: 'free_limit', title: '免运费金额'}
            ,{field: 'is_free', title: '全场免运费'}
            ,{field: 'action', title: '操作'}
        ]],
        done: function(res, curr, count){
            console.log(curr);
          }
    });
    table.on('tool(list)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
        var data = obj.data; //获得当前行数据
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        var tr = obj.tr; //获得当前行 tr 的DOM对象
        if(layEvent === 'edit'){
            var index = layer.open({
                type: 2,
                area: ['100%','100%'],
                offset: 't',
                maxmin: false,
                content: "{{ route('backend.shipfee.edit') }}?id="+data.id,
                end: function(){
                    cssReloadTable();
                }
            });
        }
    });
    function getGlobal(){
        return dataTableIns;
    }

    function cssReloadTable(){
        console.log($(".layui-laypage-em").next().html());
        dataTableIns.reload({
            page: {curr: $(".layui-laypage-em").next().html()},
            where: {
                province: $("input[name='province']").val(),
            }
        });
    }
    $('#search').on('click', function(){
        var type = 'search';
        active[type] ? active[type].call(this) : '';
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
                    province: $("input[name='province']").val(),
                }
            });
        }
    };
    $('#add').on('click', function () {
        var index = layer.open({
            type: 2,
            area: ['100%', '100%'],
            offset: 't',
            maxmin: false,
            content: "{{ route('backend.shipfee.edit') }}",
            end: function(){
                var type = 'reload';
                cssReloadTable();
            }
        });
    });
    $('#config_default').on('click', function () {
        var index = layer.open({
            type: 2,
            area: ['100%', '100%'],
            offset: 't',
            maxmin: false,
            content: "{{ route('backend.shipfee.edit') }}?is_default=1",
            end: function(){
                var type = 'reload';
                cssReloadTable();
            }
        });
    });
    lay("input[name='start_time']").on('click', function(e){
        laydate.render({
            elem: "input[name='start_time']"
            ,show: true //直接显示
            ,closeStop: "input[name='start_time']"
        });
    });
    lay("input[name='end_time']").on('click', function(e){
        laydate.render({
            elem: "input[name='end_time']"
            ,show: true //直接显示
            ,closeStop: "input[name='end_time']"
        });
    });
    $(document).keyup(function(event){
        if(event.keyCode ==13){
            $("#search").trigger("click");
        }
    });
@endsection
</script>