@extends('backend.base')

@section('content')
<div class="layui-col-md12">
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form layui-form-pane" action="">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">名称</label>
                        <div class="layui-input-inline">
                            <input class="layui-input" id="name" name="name" autocomplete="off">
                        </div>
                    </div>
                    <!--div class="layui-inline">
                        <label class="layui-form-label">有效期</label>
                        <div class="layui-input-inline">
                            <input class="layui-input" id="start_time" name="start_time" placeholder="开始时间" value="" type="text">
                        </div>
                        <div class="layui-input-inline">
                            <input class="layui-input" id="end_time" name="end_time" placeholder="结束时间" value="" type="text">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item"-->
                    <div class="layui-inline">
                        <label class="layui-form-label">状态</label>
                        <div class="layui-input-inline">
                            <select id="status" name="search_status">
                                <option value="-1">状态</option>
                                <option value="1">待激活</option>
                                <option value="2">激活</option>
                                <option value="3">禁用</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="layui-inline">
                        <span class="layui-btn sub " id="search">搜索</span>
                         <span class="layui-btn sub layui-btn-warm" id="add">创建规则</span>
                         <span class="layui-btn sub layui-btn-warm" id="top">当月TOP10</span>
                    </div>
                </div>
            </form>
            <table id="list" lay-filter="list"></table>
            <script type="text/html" id="toolbar">
                <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">编辑</a>
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="active">激活</a>
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="unactive">禁用</a>
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
        ,url: "{{ route('backend.fission.dataList') }}" //数据接口
        ,page: true //开启分页
        ,method:'post'
        ,cols: [[ //表头
            {checkbox: true,fixed: true}
            ,{field: 'id', title: 'ID', sort: true,width:80}
            ,{field: 'name', title: '名称'}
            ,{field: 'value_id', title: '优惠券/优惠码id'}
            ,{field: 'condition_value', title: '条件值'}
            ,{field: 'max_num', title: '赠送上线'}
            ,{field: 'type', title: '类型',sort: true}
            ,{field: 'datetime_period', title: '有效期',sort: true}
            ,{field: 'status', title: '状态'}
            ,{field: 'action', title: '操作'}
        ]],
        done: function(res, curr, count){
            //如果是异步请求数据方式，res即为你接口返回的信息。
            //如果是直接赋值的方式，res即为：{data: [], count: 99} data为当前页数据、count为数据总长度
//             console.log(res);
            
            //得到当前页码
            console.log(curr); 
            
            //得到数据总量
//             console.log(count);
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
//                 fixed: false,
                maxmin: false,
                content: "{{ route('backend.fission.update') }}?id="+data.id,
                end: function(){
//                     active['reload'].call(this);
                    cssReloadTable();
                }
            });
//             layer.full(index);
        }else if(layEvent === 'view'){
            var index = layer.open({
                type: 2,
                area: ['100%','100%'],
                offset: 't',
//                 fixed: false,
                maxmin: false,
                content: "{{ route('backend.fission.view') }}?id="+data.id,
                end: function(){
//                     active['reload'].call(this);
                }
            });
//             layer.full(index);
        }
        else if(layEvent === 'active'){ //激活
            layer.confirm('确认激活么', function(index){
                layer.close(index);
                $.ajax({
                    type: "POST",
                    url: "{{ route('backend.promotion.cart.active') }}",
                    data: {id:data.id},
                    success: function(res){
                        if( res.code == 1 ){
//                             window.location.reload();
//                          active['reload'].call(this);
                            cssReloadTable();
                        }else{
                            layer.msg(res.msg,{icon:5,anim:6});
                        }
                    }
                });
            });
        }else if(layEvent === 'unactive'){ //禁用
            layer.confirm('确认禁用么', function(index){
                layer.close(index);
                $.ajax({
                    type: "POST",
                    url: "{{ route('backend.promotion.cart.unactive') }}",
                    data: {id:data.id},
                    success: function(res){
                        if( res.code == 1 ){
//                             window.location.reload();
//                          active['reload'].call(this);
                            cssReloadTable();
                        }else{
                            layer.msg(res.msg,{icon:5,anim:6});
                        }
                    }
                });
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
                name: $("input[name='name']").val(),
                start_time:$('#start_time').val(),
                end_time:$('#end_time').val(),
                status:$('#status').val(),
                type:$('#type').val()
            }
        });
    }
    var active = {
        reload: function(){
//          var cr=dataTable.config.page.curr ?dataTable.config.page.curr:1;
            dataTableIns.reload({
                page: {curr: dataTableIns.config.page.curr ?dataTableIns.config.page.curr:1},
                where: {
                    name: $("input[name='name']").val(),
                    start_time:$('#start_time').val(),
                    end_time:$('#end_time').val(),
                    status:$('#status').val(),
                    type:$('#type').val()
                }
            });
        },
        search: function(){
            dataTableIns.reload({
                page: {curr: 1},
                where: {
                    name: $("input[name='name']").val(),
                    start_time:$('#start_time').val(),
                    end_time:$('#end_time').val(),
                    status:$('#status').val(),
                    type:$('#type').val()
                }
            });
        }
    };
    $('#search').on('click', function(){
        var type = 'search';
        active[type] ? active[type].call(this) : '';
    });
    $('#add').on('click', function () {
        var index = layer.open({
            type: 2,
            area: ['100%', '100%'],
            offset: 't',
            maxmin: false,
            content: "{{ route('backend.fission.edit') }}?type="+$(this).attr('id'),
            end: function(){
                var type = 'reload';
//                 active[type] ? active[type].call(this) : '';
                cssReloadTable();
            }
        });
//         layer.full(index);
    });
    $('#top').on('click', function () {
        var index = layer.open({
            type: 2,
            area: ['100%', '100%'],
            offset: 't',
            maxmin: false,
            content: "{{ route('backend.fission.log') }}",
            end: function(){
                var type = 'reload';
//                 active[type] ? active[type].call(this) : '';
                cssReloadTable();
            }
        });
//         layer.full(index);
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