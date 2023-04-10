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
                    <div class="layui-inline">
                    	<label class="layui-form-label">有效期</label>
                    	<div class="layui-input-inline">
                        	<input class="layui-input" id="start_time" name="start_time" placeholder="开始时间" value="" type="text">
                        </div>
                        <div class="layui-input-inline">
                        	<input class="layui-input" id="end_time" name="end_time" placeholder="结束时间" value="" type="text">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
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
                    	<label class="layui-form-label">类型</label>
                    	<div class="layui-input-inline">
                    		<select id="type" name="search_type">
                        	<option value="-1">类型</option>
                        	<option value="product_discount">直接折扣</option>
                        	<option value="n_piece_n_discount">多件多折</option>
                        	<option value="full_reduction_of_order">满减</option>
                        	<option value="order_n_discount">每满减</option>
                        	<option value="gift">赠品</option>
                        	<option value="coupon">满减券</option>
{{--                            <option value="coupon_discount">直接折扣-优惠券</option>--}}
                            <option value="product_coupon">随单礼券</option>
{{--                            <option value="free_try">试用装</option>--}}
{{--                        	<option value="code_product_discount">直接折扣优惠码</option>--}}
{{--                        	<option value="code_n_piece_n_discount">多件多折优惠码</option>--}}
{{--                        	<option value="code_full_reduction_of_order">满减优惠码</option>--}}
{{--                        	<option value="code_order_n_discount">每满减优惠码</option>--}}
{{--                        	<option value="code_gift">赠品优惠码</option>--}}
                        </select>
                    	</div>
                    </div>
                    <div class="layui-inline">
                    	<span class="layui-btn sub " id="search">搜索</span>
                    </div>
                </div>
                <div class="layui-form-item">
                	<table class="layui-table" style="width:90%;">
                          <tbody>
                            <tr>
                              <td>创建优惠</td>
                              <td >
                                <span class="layui-btn sub layui-btn-warm" id="product_discount">直接折扣</span>
                                <span class="layui-btn sub layui-btn-warm" id="n_piece_n_discount">多件多折</span>
                                <span class="layui-btn sub layui-btn-warm" id="full_reduction_of_order">满减</span>
                                <span class="layui-btn sub layui-btn-warm" id="gift">赠品</span>
                                <span class="layui-btn sub layui-btn-normal" id="coupon">满减券</span>
                                <span class="layui-btn sub layui-btn-normal" id="product_coupon">随单礼券</span>
{{--                                <span class="layui-btn sub layui-btn-warm" id="code_product_discount">优惠码</span>--}}
{{--                                  <span class="layui-btn sub layui-btn-warm" id="free_try">试用装</span>--}}
                              </td>
                            </tr>
                          </tbody>
                        </table>
                </div>
            </form>
            <table id="list" lay-filter="list"></table>
            <script type="text/html" id="toolbar">
                <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">编辑</a>
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="active">激活</a>
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="unactive">禁用</a>
            </script>
            <script type=" text/html" id="messToolbar">
                <a class="layui-btn" lay-event="mess_active" id="mess_active"><i class="layui-icon"></i>批量激活</a>
                <a class="layui-btn layui-btn-danger" lay-event="mess_unactive">批量禁用</a>
            </script>
        </div>
    </div>
</div>
@endsection

<script>
@section('layui_script')
    var dataTableIns = table.render({
        elem: '#list'
        ,id: 'list'
        ,height: 500
        ,url: "{{ route('backend.promotion.cart.dataList') }}" //数据接口
        ,page: true //开启分页
        ,method:'post'
        ,toolbar:'#messToolbar'
        ,cols: [[ //表头
            {checkbox: true,fixed: true}
            ,{field: 'id', title: 'ID', sort: true,width:80}
            ,{field: 'name', title: '名称'}
            ,{field: 'type_label', title: '类型',sort: true}
            ,{field: 'priority', title: '优先级',sort: true}
            ,{field: 'datetime_period', title: '有效期',sort: true}
            ,{field: 'status', title: '状态'}
            ,{field: 'action', title: '操作'}
        ]],
        done: function(res, curr, count){
            //如果是异步请求数据方式，res即为你接口返回的信息。
            //如果是直接赋值的方式，res即为：{data: [], count: 99} data为当前页数据、count为数据总长度
            //得到当前页码
            console.log(curr); 
            //得到数据总量
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
                content: "{{ route('backend.promotion.cart.edit') }}?id="+data.id,
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
                content: "{{ route('backend.promotion.cart.view') }}?id="+data.id,
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
                        	cssReloadTable();
                        }else{
                            layer.msg(res.msg,{icon:5,anim:6});
                        }
                    }
                });
            });
        }else if(layEvent === 'send_coupon'){
            location.href = '{{url('admin')}}/coupon/couponsend/edit?id='+data.id;
        }

    });

    table.on('toolbar(list)', function (obj) {
        var checkStatus = table.checkStatus(obj.config.id);
        //定义数组存放批量删除的行的id
        var listId = [];
        //获得所有选中行的数据
        var datas = checkStatus.data;
        //进行遍历所有选中行数据，拿出每一行的id存储到数组中
        $.each(datas, function (i, data) {
            listId.push(data.id)
        });
        if (listId.length <= 0) {
            return false;
        }
        switch (obj.event) {
            case 'mess_active':
                layer.confirm('确认批量激活吗?', function (index) {
                    $.ajax({
                        url: "{{ route('backend.promotion.cart.messActive') }}",
                        type: "post",
                        contentType: "application/json;charset=UTF-8",
                        dataType: 'json',
                        data: JSON.stringify({"ids": listId}),
                        success: function (res) {
                            if( res.code == 1 ){
                                cssReloadTable();
                            }else{
                                layer.msg(res.msg,{icon:5,anim:6});
                            }
                        }
                    });
                    layer.close(index);
                });
                break;
            case 'mess_unactive':
                layer.confirm('确认批量禁用吗?', function (index) {
                    $.ajax({
                        url: "{{ route('backend.promotion.cart.messUnactive') }}",
                        type: "post",
                        contentType: "application/json;charset=UTF-8",
                        dataType: 'json',
                        data: JSON.stringify({"ids": listId}),
                        success: function (res) {
                            if( res.code == 1 ){
                                cssReloadTable();
                            }else{
                                layer.msg(res.msg,{icon:5,anim:6});
                            }
                        }
                    });
                    layer.close(index);
                });
                break;
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
//         	var cr=dataTable.config.page.curr ?dataTable.config.page.curr:1;
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
    $('#full_reduction_of_order,#order_n_discount,#n_piece_n_discount,#coupon,#product_coupon,#code_product_discount,#gift,#product_discount,#free_try,#ship_fee_try').on('click', function () {
        var index = layer.open({
            type: 2,
            area: ['100%', '100%'],
            offset: 't',
            maxmin: false,
            content: "{{ route('backend.promotion.cart.edit') }}?type="+$(this).attr('id'),
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