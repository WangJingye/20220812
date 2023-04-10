@extends('backend.base')

@section('content')

    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body">
                <form class="layui-form layui-form-pane" action="{{ route('dsb.prodstat.getconversionRate') }}" method="POST">
                    <div class="layui-form-item">

                        <div class="layui-inline">
                            <label class="layui-form-label">按日期搜索</label>
                            <div class="layui-input-inline">
                                <input class="layui-input" id="searchDate" name="searchDate" placeholder="选择日期"  type="text" value="{{request('searchDate')}}" autocomplete="off">
                            </div>
                            {{--<div class="layui-input-inline">--}}
                            {{--<input class="layui-input" id="end_time" name="end_time" placeholder="结束时间" value="{{request('end_time')}}" type="text"--}}
                            {{--autocomplete="off" >--}}
                            {{--</div>--}}
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <span class="layui-btn sub" id="search">搜索</span>
                        <button class="layui-btn sub layui-btn-primary export">导出</button>
                    </div>
                </form>
                <table id="list" lay-filter="list"></table>

                <!--导出表 不展示-->
                <div style="display: none;">
                    <table id="data_export">
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
<script src="{{ url('/static/admin/js/jquery.min.js') }}"></script>
<script src="{{ url('/static/admin/laydate.js') }}"></script>
<script type="text/javascript">
    laydate.render({
        elem: '#searchDate',  // 输出框id
        value: "{{date('Y-m-d',strtotime(date('Y-m-d')))}}",
        max: "{{date('Y-m-d',strtotime(date('Y-m-d')))}}",
    });
    laydate.render({
        elem: '#end_time',  // 输出框id
        value: "{{date('Y-m-d',strtotime(date('Y-m-d')))}}",
        max: "{{date('Y-m-d',strtotime(date('Y-m-d')))}}"
    });
</script>
<script>

    @section('layui_script')

    table.render({
            elem: '#list'
            , id: 'table_list'
            , height: 500
            , url: "{{ route('dsb.prodstat.getconversionRate') }}" //数据接口
            , page: true //开启分页
            , limits: [30]
            , limit: 30 //每页默认显示的数量
            , method:'post'
            ,cols: [[ //表头
                {field: 'title', title: '模块', width: 200, sort: false}
                , {field: 'page_visit_uv', title: '用户数', width: 300, sort: false}
                , {field: 'bounce_rate', title: '跳出率', width: 300, sort: false}
                , {field: 'conversion_rate', title: '转化率', width: 200, sort: false}

            ]]

            , response: {
                statusName: 'code' //规定数据状态的字段名称，默认：code
                , statusCode: 200 //规定成功的状态码，默认：0
                , msgName: 'msg', //规定状态信息的字段名称，默认：msg
            }
        }
    );

    $(".export").click(function(){
        var ins1 = table.render({
            elem: '#data_export',
            url: "{{ route('dsb.prodstat.getconversionRate') }}", //数据接口
            method: 'post',
            title: '商品访问次数',
            where: {
                searchDate: $("input[name='searchDate']").val(),
                end_time: $("input[name='end_time']").val(),
            },
            cols: [[ //表头
                {field: 'pdtId', title: '产品ID'}
                , {field: 'name', title: '产品名称'}
                , {field: 'series', title: '品牌'}
                , {field: 'scores', title: '商品访问次数'}

            ]]
            ,
            done: function (res, curr, count) {
                exportData = res.data.list;
                table.exportFile(ins1.config.id, exportData, 'csv');
                $('#search').click();
                return false;
            }
        });

        return false;

    });
    table.on('tool(list)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
        var data = obj.data.list; //获得当前行数据
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        var tr = obj.tr; //获得当前行 tr 的DOM对象
        if(layEvent === 'edit'){
            layer.open({
                type: 2,
                area: ['100%','100%'],
                offset: 't',
                fixed: false,
                maxmin: true,
                content: "{{ route('dsb.prodstat.getconversionRate') }}?id="+data.id,
                end: function(){
                    active['reload'].call(this);
                }
            });
        }else if(layEvent === 'change'){ //删除
            //向服务端发送删除指令
            $.ajax({
                type: "POST",
                url: "{{ route('dsb.prodstat.getconversionRate') }}",
                data: {id: data.id},
                success: function(res){
                    if( res.status == 200 ){
                        window.location.reload();
                    }else{
                        layer.msg(res.message, {
                            icon:5, anim:6
                        });
                    }
                }
            });
        }
    });
    var active = {
        reload: function(){
            table.reload('table_list', {
                page: {curr: 1},
                where: {
                    searchDate: $("input[name='searchDate']").val(),
                    end_time: $("input[name='end_time']").val(),
                }
            });
        }
    };
    $('#search').on('click', function(){
        var type = 'reload';
        active[type] ? active[type].call(this) : '';
    });

    $(document).keyup(function(event){
        if(event.keyCode ==13){
            $("#search").trigger("click");
        }
    });

    @endsection
</script>