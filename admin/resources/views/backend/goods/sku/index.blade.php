@extends('backend.base')

@section('content')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <label class="layui-form-label">搜索栏</label>
                        <div class="layui-inline">
                            <input class="layui-input" name="sku_id" autocomplete="off" placeholder="SKU ID">
                        </div>
                        <span class="layui-inline layui-btn" id="search">搜索</span>
                        <span class="layui-inline layui-btn" id="J_add_sku">新增SKU</span>
                        <span class="layui-inline layui-btn" id="J_add_virtual_sku">新增虚拟SKU</span>
                        <span class="layui-inline layui-btn layui-btn-warm" id="J_add_unreal_sku">新增非真实SKU</span>
                        <span class="layui-inline layui-btn" id="export_sku">导出SKU</span>
                        <a class="layui-btn uploadExcel layui-btn-normal" href="javascript:;" lay-data="{url: '{{route('backend.config.data.import',['action'=>'stock'])}}', accept: 'file'}">导入库存</a>
                        <a class="layui-btn" href="{{url('static/demo/importstock.xlsx')}}" >下载导入库存模板</a>
                    </div>
                </form>
                <table id="list" lay-filter="list"></table>
            </div>
        </div>
    </div>
@endsection

<script>
    @section('layui_script')
    var upload = layui.upload;
    var loading;
    upload.render({
        elem: '.uploadExcel'
        ,before: function(){
            // layer.tips('接口地址：'+ this.url, this.item, {tips: 1});
            loading = layer.load(1, {shade: [0.3]});
        }
        ,done: function(res, index, upload){
            if (res.code === 1) {
                layer.msg('操作成功', {
                    icon: 1,
                    shade: 0.3,
                }, function(){
                    layer.close(loading);
                    location.reload()
                });
            } else {
                layer.msg(res.message, {
                    icon: 2,
                    time: 2000
                }, function(){
                    layer.close(loading);
                    location.reload()
                });
            }

        }
    })
    var dataTable = table.render({
            elem: '#list',
            id: 'table_list',
            height: 500,
            //数据接口
            url: "{{ route('backend.goods.sku.list') }}?{{$query_string}}",
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
                {field: 'sku_id', title: 'SKU ID'}
                , {field: 'ori_price', title: '价格'}
                // , {field: 'store', title: '库存'}
                , {
                    title: '库存', align: 'center', templet: function (d) {
                        let opt = '';
                        if ((d.stock_info.is_share !== undefined) && (d.stock_info.is_share === '1')) {
                            opt += '<span>共享库存:' + d.stock_info.stock + '</span>';
                        } else {
                            if (d.stock_info.channel1 !== undefined) {
                                opt += '<span>小程序:' + d.stock_info.channel1 + '</span>';
                            }
                            if (d.stock_info.channel2 !== undefined) {
                                opt += '<span>|mobile:' + d.stock_info.channel2 + '</span>';
                            }
                            if (d.stock_info.channel3 !== undefined) {
                                opt += '<span>|PC:' + d.stock_info.channel3 + '</span>';
                            }
                            if (d.stock_info.stock !== undefined) {
                                opt += '<span>|其他/共享:' + d.stock_info.stock + '</span>';
                            }
                        }
                        return opt;
                    }
                },
                {field: 'include_skus', title: '打包的SKU'}
                // , {
                //     title: '锁定库存', align: 'center', templet: function (d) {
                //         let opt = '';
                //         if (d.stock_info.lock_channel1 !== undefined) {
                //             opt += '<span>小程序:'+d.stock_info.lock_channel1+'</span>';
                //         }
                //         if (d.stock_info.lock_channel2 !== undefined) {
                //             opt += '<span>|mobile:'+d.stock_info.lock_channel2+'</span>';
                //         }
                //         if (d.stock_info.lock_channel3 !== undefined) {
                //             opt += '<span>|PC:'+d.stock_info.lock_channel3+'</span>';
                //         }
                //         return opt;
                //     }
                // }
                , {
                    title: '操作', width: 300, align: 'center', templet: function (d) {
                        let opt = '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">编辑SKU信息</a>';
                        return opt;
                    }
                }
            ]]
        });
    table.on('tool(list)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
        var data = obj.data; //获得当前行数据
        console.log(data);
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        var tr = obj.tr; //获得当前行 tr 的DOM对象
        if (layEvent === 'edit') {
            layer.open({
                type: 2,
                area: ['100%', '100%'],
                offset: 't',
                maxmin: false,
                move: false,
                content: "{{ route('backend.goods.sku.get') }}?skuIdx=" + data.id,
                end: function () {
                    table.reload('table_list')
                }
            });
        } else if (layEvent === 'cms') {
            layer.open({
                type: 2,
                area: ['100%', '100%'],
                offset: 't',
                maxmin: false,
                move: false,
                content: "{{ route('backend.goods.sku.cms') }}?id=" + data.id,
                end: function () {
                    table.reload('table_list')
                }
            });
        } else {
            layer.open({
                type: 2,
                area: ['100%', '100%'],
                offset: 't',
                maxmin: false,
                move: false,
                content: "{{ route('backend.goods.sku.getStock') }}?sku_id=" + data.sku_id,
                end: function () {
                    table.reload('table_list')
                }
            });
        }
    });
    $('#search').on('click', function () {
        table.reload('table_list', {
            page: {curr: 1},
            where: {
                sku_id: $("input[name='sku_id']").val(),
            }
        });
    });

    $("#J_add_sku").on('click', function () {
        layer.open({
            type: 2,
            area: ['100%', '100%'],
            offset: 't',
            maxmin: false,
            move: false,
            content: "{{ route('backend.goods.sku.add') }}",
            end: function () {
                table.reload('table_list')
            }
        });
    });
    $("#J_add_virtual_sku").on('click', function () {
        layer.open({
            type: 2,
            area: ['100%', '100%'],
            offset: 't',
            maxmin: false,
            move: false,
            content: "{{ route('backend.goods.sku.add') }}?type=virtual",
            end: function () {
                table.reload('table_list')
            }
        });
    });
    $("#J_add_unreal_sku").on('click', function () {
        layer.open({
            type: 2,
            area: ['100%', '100%'],
            offset: 't',
            maxmin: false,
            move: false,
            content: "{{ route('backend.goods.sku.add') }}?type=unreal",
            end: function () {
                table.reload('table_list')
            }
        });
    });

    $("#export_sku").on('click', function () {
        $.post("{{ route('backend.goods.sku.export') }}",[], function (res) {
            if (res.code != 1) {
                return false;
            }
            console.log(res);
            // return false;
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