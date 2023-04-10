@extends('backend.base')

@section('content')
<div class="layui-col-md12">
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form layui-form-pane" action="">
                <div class="layui-form-item">
                    <label class="layui-form-label">名称</label>
                    <div class="layui-inline">
                        <input class="layui-input" name="name" autocomplete="off">
                    </div>
                </div>
                <div class="layui-form-item">
                    <span class="layui-btn sub" id="search">搜索</span>
                    <span class="layui-btn sub" id="add">添加</span>
                </div>
            </form>
            @if (session('msg'))
            <div class="el-icon-warning-outline" style="border:1px solid #e8e8e8;color:green">
                    {{ session('msg') }}
                </div>
            @endif
            <table id="list" lay-filter="list"></table>
            <script type="text/html" id="toolbar">
                
                <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">查看</a>
                <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="active">激活</a>
                <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="unactive">禁用</a>
                
            </script>
        </div>
    </div>
</div>
@endsection

<script>
@section('layui_script')
        

var dataTable = table.render({
    elem: '#list'
    ,id: 'table_list'
    ,height: 500
    ,url: "{{ route('backend.promotion.gift.dataList') }}" //数据接口
    ,page: true //开启分页
    ,method:'post'
    ,cols: [[ //表头
        {checkbox: true,fixed: true}
        ,{field: 'id', title: 'ID', sort: true,width:80}
        ,{field: 'name', title: '名称'}
        ,{field: 'qty', title: '库存'}
        ,{field: 'status', title: '状态'}
        ,{field: 'action', title: '操作'}
    ]]
});
table.on('tool(list)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
    var data = obj.data; //获得当前行数据
    var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
    var tr = obj.tr; //获得当前行 tr 的DOM对象
    if(layEvent === 'edit'){
        layer.open({
            type: 2,
            area: ['100%', '100%'],
            offset: 't',
            maxmin: true,
            fixed: false,
            content: "{{ route('backend.promotion.gift.edit') }}?id="+data.id,
            end: function(){
                active['reload'].call(this);
            }
        });
    }else if(layEvent === 'view'){
        layer.open({
            type: 2,
            area: ['100%', '100%'],
            offset: 't',
            maxmin: true,
            fixed: false,
            content: "{{ route('backend.promotion.gift.view') }}?id="+data.id,
            end: function(){
                active['reload'].call(this);
            }
        });
    }
    else if(layEvent === 'active'){
    	layer.confirm('确认激活么', function(index){
            layer.close(index);
            $.ajax({
                type: "POST",
                url: "{{ route('backend.promotion.gift.active') }}",
                data: {id:data.id},
                success: function(res){
                    if( res.code == 1 ){
                        window.location.reload();
                    }else{
                        layer.msg(res.msg,{icon:5,anim:6});
                    }
                }
            });
        });
    }else if (layEvent === 'unactive'){
    	layer.confirm('确认禁用么', function(index){
            layer.close(index);
            $.ajax({
                type: "POST",
                url: "{{ route('backend.promotion.gift.unactive') }}",
                data: {id:data.id},
                success: function(res){
                    if( res.code == 1 ){
                        window.location.reload();
                    }else{
                        layer.msg(res.msg,{icon:5,anim:6});
                    }
                }
            });
        });
    }
});
var active = {
    reload: function(){
        table.reload('table_list', {
            page: {curr: 1},
            where: {
                name: $("input[name='name']").val(),
            }
        });
    }
};
$('#search').on('click', function(){
    var type = 'reload';
    active[type] ? active[type].call(this) : '';
});
$('#add').on('click', function () {
    layer.open({
        type: 2,
        area: ['100%', '100%'],
        offset: 't',
        maxmin: true,
        content: "{{ route('backend.promotion.gift.edit') }}",
        end: function(){
            var type = 'reload';
            active[type] ? active[type].call(this) : '';
        }
    });
});

lay("input[name='sd']").on('click', function(e){
    laydate.render({
        elem: "input[name='sd']"
        ,show: true //直接显示
        ,closeStop: "input[name='sd']"
    });
});
lay("input[name='ed']").on('click', function(e){
    laydate.render({
        elem: "input[name='ed']"
        ,show: true //直接显示
        ,closeStop: "input[name='ed']"
    });
});
$(document).keyup(function(event){
    if(event.keyCode ==13){
        $("#search").trigger("click");
    }
});
@endsection
</script>