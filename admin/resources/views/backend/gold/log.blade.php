@extends('backend.base')

@section('content')
    <style>
        .layui-table-cell {
            height: auto !important;
            white-space: normal;
        }
    </style>
    <form class="layui-form" action="" id="search-form" lay-filter="search-form" onsubmit="return false;">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">变动类型</label>
                <div class="layui-input-inline">
                    <select name="type" id="type">
                        <option value="">无</option>
                        <?php $typeList = [1 => '充值', 2 => '消费', 3 => '订单退款', 4 => '充值退款'];
                        foreach ($typeList as $k=>$v): ?>
                        <option value="<?=$k?>"><?=$v?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">订单编号</label>
                <div class="layui-input-inline">
                    <input class="layui-input" id="order_sn" name="order_sn" autocomplete="off" placeholder="订单编号"
                           value="">
                </div>
            </div>

            <div class="layui-inline">
                <label class="layui-form-label">用户昵称</label>
                <div class="layui-input-inline">
                    <input class="layui-input" id="nickname" name="nickname" autocomplete="off" placeholder="用户昵称"
                           value="">
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">手机号</label>
                <div class="layui-input-inline">
                    <input class="layui-input" id="mobile" name="mobile" autocomplete="off" placeholder="手机号"
                           value="">
                </div>
            </div>
            <div class="layui-inline" style="position: relative">
                <label class="layui-form-label">变动时间</label>
                <div class="layui-input-inline">
                    <input class="layui-input" id="start_time" name="start_time" placeholder="开始时间" type="text"
                           value="" autocomplete="off">
                </div>
                <div class="layui-input-inline">
                    <input class="layui-input" id="end_time" name="end_time" placeholder="结束时间"
                           value="" type="text" autocomplete="off">
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-inline">
                <button id="search" class="layui-btn layui-btn-normal" lay-submit="search">搜索</button>
                <button id="export" class="layui-btn" lay-submit lay-filter="formSubmit">导出</button>
            </div>
        </div>
    </form>
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body">
                <table id="list" lay-filter="list"></table>
            </div>
        </div>
    </div>
@endsection

<script>
    @section('layui_script')
    var typeList = <?=json_encode($typeList)?>;
    var dataTable = table.render({
        elem: '#list',
        id: 'table_list',
        height: 560,
        //数据接口
        url: "{{ route('backend.gold.getUserBalanceLogs') }}",
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
                "data": res.pageData,
                "count": res.count
            }
        },
        resizing: function () {
            table.resize('table_list');
        },
        //表头
        cols: [[
            {field: 'id', title: 'ID'},
            {field: 'phone', title: '手机号'},
            {field: 'nickname', title: '用户昵称'},
            {
                field: 'first_name', title: '用户姓名', templet: function (d) {
                    return d.first_name+d.last_name;
                }
            },
            {field: 'order_sn', title: '订单编号'},
            {field: 'order_title', title: '产品名称'},
            {
                field: 'type', title: '变动类型', width: 100, templet: function (d) {
                    return typeList[d.type];
                }
            },
            {field: 'balance', title: '变动金额'},
            {field: 'created_at', title: '变动时间'},
            {field: 'remain_balance', title: '储值余额'},
        ]]
    });

    // 自定义排序
    table.on('sort(list)', function (obj) {
        let type = obj.type,
            field = obj.field,
            data = obj.data,//表格的配置Data
            thisData = [];

        //将排好序的Data重载表格
        table.reload('table_list', {
            initSort: obj,
            where: {
                field: 'sort',
                order: type
            }
        });
    });
    laydate.render({
        elem: '#start_time'  // 输出框id
        , type: 'datetime'
    });
    laydate.render({
        elem: '#end_time'  // 输出框id
        , type: 'datetime'
    });
    $('#search').click(function () {
        let list = $('#search-form').serializeArray();
        let args = {};
        for (let item of list) {
            args[item['name']] = item['value'];
        }
        //将排好序的Data重载表格
        table.reload('table_list', {
            where: args
        });
    });
    $('#export').click(function () {
        $.post("{{ route('backend.gold.exportLog') }}", $('#search-form').serialize(), function (res) {
            if (res.code != 1) {
                return false;
            }
            var value = res.data.value;
            var columns = res.data.columns;
            table.exportFile(columns, value, 'xls'); //默认导出 csv，也可以为：xls
        }, 'json');
        return false;
    });
    $(document).keyup(function (event) {
        if (event.keyCode == 13) {
            $("#search").trigger("click");
        }
    });
    @endsection
</script>

