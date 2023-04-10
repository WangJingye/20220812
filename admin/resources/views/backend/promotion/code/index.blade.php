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
					<span class="layui-btn sub" id="code_product_discount">添加直接折扣-优惠码</span>
                    <span class="layui-btn sub" id="code_full_reduction_of_order">添加满减-优惠码</span>
                    <span class="layui-btn sub" id="code_order_n_discount">添加每满减-优惠码</span>
                    <span class="layui-btn sub" id="code_n_piece_n_discount">添加折扣-优惠码</span>
                    <span class="layui-btn sub" id="code_gift">添加赠品-优惠码</span>
                </div>
            </form>
            <table id="list" lay-filter="list"></table>
            <script type="text/html" id="toolbar">
                <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">编辑</a>
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
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
        ,url: "{{ route('backend.promotion.code.dataList') }}" //数据接口
        ,page: true //开启分页
        ,method:'post'
        ,cols: [[ //表头
            {checkbox: true,fixed: true}
            ,{field: 'id', title: 'ID', sort: true,width:80}
            ,{field: 'name', title: '名称'}
            ,{field: 'type_label', title: '类型',sort: true}
            ,{field: 'priority', title: '权重',sort: true}
            ,{field: 'status', title: '状态'}
            ,{fixed: 'right', width: 150, align:'center', toolbar: '#toolbar'}
        ]]
    });
    table.on('tool(list)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
        var data = obj.data; //获得当前行数据
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        var tr = obj.tr; //获得当前行 tr 的DOM对象
        if(layEvent === 'edit'){
            layer.open({
                type: 2,
                area: ['100%','100%'],
                offset: 't',
                fixed: false,
                maxmin: true,
                content: "{{ route('backend.promotion.code.edit') }}?id="+data.id,
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
                    name: $("input[name='name']").val(),
                }
            });
        }
    };
    $('#search').on('click', function(){
        var type = 'reload';
        active[type] ? active[type].call(this) : '';
    });
    $('#code_full_reduction_of_order,#code_order_n_discount,#code_n_piece_n_discount,#code_gift,#code_product_discount').on('click', function () {
        layer.open({
            type: 2,
            area: ['100%', '100%'],
            offset: 't',
            maxmin: true,
            content: "{{ route('backend.promotion.code.edit') }}?type="+$(this).attr('id'),
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