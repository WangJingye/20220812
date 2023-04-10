@extends('backend.base')

@section('content')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            
                            <div class="layui-inline">
                                <select id="store_id" lay-filter="store_id">
                                    <option value="0">请选择门店</option>
                                     @foreach($detail['stores'] as $k=>$v)
                                        <option value="{{$k}}" @if( $detail['role_name']==$k) selected @endif>{{$v}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="layui-inline">
                                <select id="guide_id">
                                    <option value="0">请选择导购</option>
                                     @foreach($detail['guides'] as $k=>$v)
                                        <option value="{{$k}}" @if( $detail['role_name']==$k) selected @endif>{{$v}}</option>
                                    @endforeach
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
                        </div>
                        <span class="layui-inline layui-btn" id="search">搜索</span>
                    </div>
                </form>
                <table id="list" lay-filter="list"></table>
            </div>
        </div>
    </div>
@endsection

<script>
            @section('layui_script')

    var dataTable = table.render({
                elem: '#list',
                id: 'table_list',
                height: 500,
                //数据接口
                url: "{{ route('backend.store.guide.list') }}?{{$query_string}}",
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
                        "data": res.data.pageData,
                        "count": res.data.count
                    }
                },
                resizing: function () {
                    table.resize('table_list');
                },
                //表头
                cols: [[
                    {field: 'guide_id', title: '导购ID', width:80}
                    , {field: 'store_code', title: '门店编码',width:100}
                    , {field: 'guide_name', title: '导购名称'}
                    , {field: 'store_name', title: '门店名称'}
                    , {field: 'address', title: '省',width:80}
                    , {field: 'city_name', title: '市', width:90}
                    , {field: 'money', title: '总金额',width:100}
                    , {field: 'order_count',title: '订单数',  width:80}
                    , {field: 'time', title: '日期'}
                    , {
                        title: '操作', width: 100, align: 'center', templet: function (d) {
                            let opt = '';
                            opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit" href="showInfo?guide_id='+d.guide_id+'&time='+ d.time+'">查看</a>';

                            return opt;
                        }
                    }
                ]]
            });

    $('#search').on('click', function () {
       var start_time = $('#start_time').val();
       var end_time = $('#end_time').val();
        var page = '{curr: 1}';
        if(start_time.length > 0) {
            var page = false;
        }

        table.reload('table_list', {
            page: page,
            where: {
                store_id: $('#store_id').val(),
                guide_id: $('#guide_id').val(),
                city_name: $('#city_name').val(),
                start_time: start_time,
                end_time: end_time
            }
        });
    });

    $("#J_add_loc").on('click',function () {
        layer.open({
            type: 2,
            area: ['100%', '100%'],
            offset: 't',
            maxmin: false,
            move: false,
            content: "{{ route('backend.store.add') }}",
            end: function () {
                table.reload('table_list')
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

    $(document).keyup(function (event) {
        if (event.keyCode == 13) {
            $("#search").trigger("click");
        }
    });

    layui.use('form', function(){
        var form = layui.form;
        form.on('select(city_name)',function(data){
            var city_name = data.value;
            $.ajax({
                url:"{{ route('backend.store.guide.getStoreFromCity') }}",
                data:{'city_name':city_name},
                type:"get",
                dataType:"json",
                success:function(data){
                    $("#store_id").empty();
                    $("#guide_id").empty();
                    var option = '<option value="0">请选择门店</option>';
                    for(var i=0; i<data.data.length; i++){
                        option += '<option value="'+ data.data[i].id+'"'
                        option += ">"+data.data[i].store_name+"</option>"; //动态添加数据
                    }
                    $("#store_id").append(option);
                    layui.form.render("select");
                }
            });
        });
    });

        layui.use('form', function(){
            var form = layui.form;
            form.on('select(store_id)',function(data){
                var store_id = data.value;
                $.ajax({
                    url:"{{ route('backend.store.guide.getStoreFromCity') }}",
                    data:{'store_id':store_id},
                    type:"get",
                    dataType:"json",
                    success:function(data){
                        $("#guide_id").empty();
                        var option = '<option value="0">请选择导购</option>';
                        for(var i=0; i<data.data.length; i++){
                            option += '<option value="'+ data.data[i].id+'"'
                            option += ">"+data.data[i].really_name+"</option>"; //动态添加数据
                        }
                        $("#guide_id").append(option);
                        layui.form.render("select");
                    }
                });
            });
        });
        layui.use('form', function(){
            var form = layui.form;
            form.on('select(province)',function(data){
                var province = data.value;
                $.ajax({
                    url:"{{ route('backend.store.guide.getStoreFromCity') }}",
                    data:{'province':province},
                    type:"get",
                    dataType:"json",
                    success:function(data){
                        $("#guide_id").empty();
                        $("#store_id").empty();
                        $("#city_name").empty();
                        var option = '<option value = "0">请选择城市</option>';
                        for(var i=0; i<data.data.length; i++){
                            option += '<option value="'+ data.data[i]+'"'
                            option += ">"+data.data[i]+"</option>"; //动态添加数据
                        }
                        $("#city_name").append(option);
                        layui.form.render("select");
                    }
                });
            });
        });
    @endsection

</script>