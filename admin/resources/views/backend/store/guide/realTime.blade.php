@extends('backend.base')

@section('content')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <div class="layui-inline" style="margin-right: 50px;">
                            <div class="layui-inline">
                                <input class="layui-input" id="order_sn" name="order_sn" placeholder="订单编号" value="" type="text">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <select id="guide_id">
                                <option value="0">请选择导购</option>
                                 @foreach($searchData['guides'] as $k=>$v)
                                    <option value="{{$k}}">{{$v}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-inline">
                            <select id="order_status">
                                <option value="">订单状态</option>
                                <option value="1">未付款</option>
                                <option value="3">已支付</option>
                                <option value="4">已发货</option>
                                <option value="10">已完成</option>
                                <option value="2">已取消</option>
                            </select>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">筛选时间</label>
                            <div class="layui-input-inline">
                                <input class="layui-input" id="start_time" name="start_time" placeholder="开始时间" value="" type="text">
                            </div>
                            <div class="layui-input-inline">
                                <input class="layui-input" id="end_time" name="end_time" placeholder="结束时间" value="" type="text">
                            </div>
                        </div>
                        <span class="layui-inline layui-btn" id="search">搜索</span>
                        <label class="layui-form-label over-view" style="width: auto;margin-right: auto;"></label>
                    </div>
                </form>
                <table id="list" lay-filter="list"></table>
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
        ,url: "{{ route('backend.store.guide.list') }}" //数据接口
        ,page: true //开启分页
        ,method:'post',
        parseData: function (res) {
            return {
                "code": 0,
                "data": res.list,
                "count": res.total
            }
        },
        resizing: function () {
            table.resize('table_list');
        },
        //表头
        cols: [[
            {field: 'order_sn', title: '订单编号'}
            , {field: 'total_num', title: '商品数量'}
            , {field: 'status_name', title: '订单状态'}
            , {field: 'total_amount', title: '付款金额'}
            , {field: 'store_name', title: '门店名称'}
             , {field: 'guide_name', title: '导购姓名'}
            , {field: 'created_at', title: '订单时间'}
        ]],
        done: function(res, curr, count){

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
                order_sn: $("input[name='order_sn']").val(),
                start_time:$('#start_time').val(),
                end_time:$('#end_time').val(),
                guide_id:$('#guide_id').val(),
                order_status:$('#order_status').val(),
                store_id:$('#store_id').val()

            }
        });
    }
    var active = {
        reload: function(){
//          var cr=dataTable.config.page.curr ?dataTable.config.page.curr:1;
            dataTableIns.reload({
                page: {curr: dataTableIns.config.page.curr ?dataTableIns.config.page.curr:1},
                where: {
                    order_sn: $("input[name='order_sn']").val(),
                    start_time:$('#start_time').val(),
                    end_time:$('#end_time').val(),
                    guide_id:$('#guide_id').val(),
                    order_status:$('#order_status').val(),
                    store_id:$('#store_id').val()
                }
            });
        },
        search: function(){
            dataTableIns.reload({
                page: {curr: 1},
                where: {
                    order_sn: $("input[name='order_sn']").val(),
                    start_time:$('#start_time').val(),
                    end_time:$('#end_time').val(),
                    guide_id:$('#guide_id').val(),
                    order_status:$('#order_status').val(),
                    store_id:$('#store_id').val()
                }
            });
        }
    };
    $('#search').on('click', function(){
        table.reload('table_list', {
            page: {curr: 1},
            where: {
                order_sn: $("input[name='order_sn']").val(),
                start_time:$('#start_time').val(),
                end_time:$('#end_time').val(),
                guide_id:$('#guide_id').val(),
                order_status:$('#order_status').val(),
                store_id:$('#store_id').val()
            }
        });
    });
    
    $('#top').on('click', function () {
        var index = layer.open({
            type: 2,
            area: ['100%', '100%'],
            offset: 't',
            maxmin: false,
            content: "{{ route('backend.store.guide.realTimeList') }}",
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