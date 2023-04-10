@extends('backend.base')

@section('content')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <label class="layui-form-label">搜索栏</label>
                        <div class="layui-inline">
                            <input class="layui-input" name="pos_id" autocomplete="off" placeholder="pos_id">
                        </div>
                        <span class="layui-inline layui-btn" id="search">搜索</span>
{{--                        <span class="layui-inline layui-btn" id="J_add_loc">新增门店</span>--}}
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
            url: "{{ route('backend.oms.order.item.list') }}?{{$query_string}}",
            //开启分页
            page: true,
            cellMinWidth:100,
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
                {field: 'id', title: 'id'}
                , {field: 'spu', title: 'spu'}
                , {field: 'is_gift', title: '是否是小样'}
                , {field: 'is_free', title: '是否是赠品'}
                , {field: 'sku', title: 'sku'}
                , {field: 'qty', title: '数量'}
                , {field: 'name', title: '名称'}
                , {field: 'original_price', title: '原单价'}
                , {field: 'product_amount_total', title: '产品总金额'}
                , {field: 'order_amount_total', title: '实际支付金额'}
                , {field: 'discount', title: '打折价'}
                , {field: 'type', title: '产品类型'}
                , {field: 'spec_property', title: '规格属性'}
                , {field: 'spec_desc', title: '规格描述'}
                , {field: 'created_at', width: 200, title: '创建时间'}
                , {
                    title: '操作', width: 300, align: 'center', templet: function (d) {
                        let opt = '';
                         opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit" href="edit?id='+d.id+'">编辑订单状态</a>';

                        return opt;
                    }
                }
            ]]
        });

    $('#search').on('click', function () {
        table.reload('table_list', {
            page: {curr: 1},
            where: {
                store_name: $("input[name='pos_id']").val(),
            }
        });
    });

    {{--$("#J_add_loc").on('click',function () {--}}
    {{--    layer.open({--}}
    {{--        type: 2,--}}
    {{--        area: ['100%', '100%'],--}}
    {{--        offset: 't',--}}
    {{--        maxmin: false,--}}
    {{--        move: false,--}}
    {{--        content: "{{ route('backend.store.add') }}",--}}
    {{--        end: function () {--}}
    {{--            table.reload('table_list')--}}
    {{--        }--}}
    {{--    });--}}
    {{--});--}}

    $(document).keyup(function (event) {
        if (event.keyCode == 13) {
            $("#search").trigger("click");
        }
    });
    @endsection
</script>