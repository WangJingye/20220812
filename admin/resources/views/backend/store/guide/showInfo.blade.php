@extends('backend.base')

@section('content')
    <label class="layui-form-label over-view" style="width: auto;margin-right: auto;"></label>
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

    var dataTable = table.render({
                elem: '#list',
                id: 'table_list',
                height: 500,
                //数据接口
                url: "{{route('backend.store.guide.showInfoList')}}?<?php echo $query_string?>",
                //开启分页
                page: true,
                method: 'get',
                limit: 10,
                text: {
                    //默认：无数据
                    none: '暂无相关数据'
                },
                parseData: function (res) {
                    $('.over-view').html(res.data.over_view);
                    return {
                        "code": 0,
                        "data": res.data.pageData,
                        "count": res.data.count
                    }
                    $('#over_view').html(res.data.over_view);
                },
                resizing: function () {
                    table.resize('table_list');
                },
                //表头
                cols: [[
                     {field: 'order_sn', title: '订单编号'}
                    , {field: 'guide_name', title: '导购名称'}
                    , {field: 'qty', title: '数量'}
                    , {field: 'product_amount_total', title: '总金额'}
                    , {field: 'name', title: '产品名称'}
                    , {field: 'original_price', title: '商品单价'}
                    , {field: 'spec_desc', title: '产品详细'}
                    , {field: 'created_at', title: '订单时间'}
                ]]
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
    @endsection
</script>