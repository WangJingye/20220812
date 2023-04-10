@extends('backend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                <a class="layui-btn layui-btn-normal" href="{{ route('backend.user.create') }}">添 加</a>
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
                    <a class="layui-btn layui-btn-xs" lay-event="role">角色</a>
                    <a class="layui-btn layui-btn-xs" lay-event="permission">权限</a>
                    <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="del">删除</a>
                </div>
            </script>
        </div>
    </div>
@endsection

<script>
@section('layui_script')
    //用户表格初始化
    var dataTable = table.render({
        elem: '#dataTable'
        ,height: 500
        ,url: "{{ route('backend.data') }}" //数据接口
        ,where:{model:"user"}
        ,page: true //开启分页
        ,cols: [[ //表头
            {checkbox: true,fixed: true}
            ,{field: 'id', title: 'ID', sort: true,width:80}
            ,{field: 'name', title: '昵称'}
            ,{field: 'email', title: '邮箱'}
            ,{field: 'created_at', title: '创建时间'}
            ,{fixed: 'right', width: 320, align:'center', toolbar: '#options'}
        ]]
    });

    //监听工具条
    table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
        var data = obj.data //获得当前行数据
            ,layEvent = obj.event; //获得 lay-event 对应的值
        if(layEvent === 'del'){
            layer.confirm('确认删除吗？', function(index){
                $.post("{{ route('backend.user.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                    if (result.code==0){
                        obj.del(); //删除对应行（tr）的DOM结构
                    }
                    layer.close(index);
                    layer.msg(result.msg,{icon:6})
                });
            });
        } else if(layEvent === 'edit'){
            location.href = '{{url('admin')}}/user/'+data.id+'/edit';
        } else if (layEvent === 'role'){
            location.href = '{{url('admin')}}/user/'+data.id+'/role';
        } else if (layEvent === 'permission'){
            location.href = '{{url('admin')}}/user/'+data.id+'/permission';
        }
    });

    //按钮批量删除
    $("#listDelete").click(function () {
        var ids = []
        var hasCheck = table.checkStatus('dataTable')
        var hasCheckData = hasCheck.data
        if (hasCheckData.length>0){
            $.each(hasCheckData,function (index,element) {
                ids.push(element.id)
            })
        }
        if (ids.length>0){
            layer.confirm('确认删除吗？', function(index){
                $.post("{{ route('backend.user.destroy') }}",{_method:'delete',ids:ids},function (result) {
                    if (result.code==0){
                        dataTable.reload()
                    }
                    layer.close(index);
                    layer.msg(result.msg,{icon:6})
                });
            })
        }else {
            layer.msg('请选择删除项',{icon:5})
        }
    })
@endsection
</script>



