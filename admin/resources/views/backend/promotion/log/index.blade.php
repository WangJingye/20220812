@extends('backend.base')

@section('content')
<div class="layui-col-md12">
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form layui-form-pane" action="">
                <div class="layui-form-item">
                    <div class="layui-inline">
                    	<label class="layui-form-label">促销ID</label>
                    	<div class="layui-input-inline">
                        	<input class="layui-input" name="ruleId" autocomplete="off">
                        </div>
                    </div>
                    <div class="layui-inline">
                    	<label class="layui-form-label">赠品ID</label>
                    	<div class="layui-input-inline">
                        	<input class="layui-input" name="giftId" autocomplete="off">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <span class="layui-btn sub" id="search">搜索</span>
                    </div>
                </div>
                <div class="layui-form-item">
                    
                </div>
            </form>
            @if (session('msg'))
            <div class="el-icon-warning-outline" style="border:1px solid #e8e8e8;color:green">
                    {{ session('msg') }}
                </div>
            @endif
            <table id="list" lay-filter="list"></table>
        </div>
    </div>
</div>
@endsection

<script>
@section('layui_script')
        

var dataTable = table.render({
    elem: '#list'
    ,id: 'table_list'
    //,height: 500
    ,url: "{{ route('backend.promotion.log.dataList') }}" //数据接口
    ,page: true //开启分页
    ,method:'post'
    ,cols: [[ //表头
        {checkbox: true,fixed: true}
        ,{field: 'id', title: 'ID', sort: true}
        ,{field: 'userId', title: '操作人ID'}
        ,{field: 'userEmail', title: '操作人Email'}
        ,{field: 'created_at', title: '操作时间'}
        ,{field: 'updated_at', title: '更新时间'}
        ,{field: 'actionType', title: '操作类型'}
        ,{field: 'ruleId', title: '促销ID'}
        ,{field: 'giftId', title: '赠品ID'}
    ]]
});

var active = {
    reload: function(){
        table.reload('table_list', {
            page: {curr: 1},
            where: {
                ruleId: $("input[name='ruleId']").val(),
                giftId: $("input[name='giftId']").val(),
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