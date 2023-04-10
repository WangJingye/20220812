@extends('backend.base')

@section('content')
    <style>
        .layui-table-cell {
            height: auto !important;
            white-space: normal;
        }
    </style>
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body">
                <table id="list" lay-filter="list"></table>
            </div>
        </div>
        <input type="hidden" id='J_cat_id' value="{{$cat_id}}">
    </div>
@endsection

<script>
    @section('layui_script')
    layui.config({
        base: '/ext/',   // 模块所在目录
    }).extend({
        soulTable: 'soulTable'  // 模块别名
    });
    layui.use([ 'form', 'table','soulTable'], function () {
        var table = layui.table,
            soulTable = layui.soulTable;
        table.render({
            elem: '#list',
            id: 'table_list',
            height: 560,
            rowDrag: {/*trigger: 'row',*/ done: function(obj) {
                    // 完成时（松开时）触发
                    // 如果拖动前和拖动后无变化，则不会触发此方法
                    // console.log(obj) // 当前行数据
                    // console.log(obj.row) // 当前行数据
                    // console.log(obj.cache) // 改动后全表数据
                    // console.log(obj.oldIndex) // 原来的数据索引
                    // console.log(obj.newIndex) // 改动后数据索引

                    var data = obj.cache;
                    var ids = '';
                    $.each(data,function(i,n){
                        if(ids){
                            ids = ids+','+n.id
                        }else{
                            ids = n.id
                        }
                    });
                    var params = {};
                    params.ids = ids;
                    params.cat_id = $('#J_cat_id').val();
                    if(ids){
                        $.post("{{ route('backend.goods.category.batchChangeSort') }}", params, function (res) {
                            if (res.code === 1) {
                                layer.msg('操作成功', {
                                    icon: 1,
                                    shade: 0.3,
                                    offset: '300px',
                                    time: 2000 //2秒关闭（如果不配置，默认是3秒）
                                }, function () {
                                    // let index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                                    // parent.layer.close(index); //再执行关闭
                                });
                            } else {
                                layer.msg(res.message, {
                                    icon: 2,
                                    shade: 0.3,
                                    offset: '300px',
                                    time: 2000 //2秒关闭（如果不配置，默认是3秒）
                                });
                            }
                        }, 'json');
                    }
                }},
            //数据接口
            url: "{{ route('backend.goods.category.getCatProdAndColleList') }}?{{$query_string}}",
            //开启分页
            page: false,
            method: 'get',
            limit: 10,
            text: {
                //默认：无数据
                none: '暂无相关数据'
            },
            parseData: function (res) {
                return {
                    "code": 0,
                    "data": res.data,
                    "count": res.count
                }
            },
            resizing: function () {
                table.resize('table_list');
            },
            //表头
            cols: [[
                {field: 'product_idx', title: '商品自增ID'}
                ,{field: 'product_name', title: '名称'}

                , {field: 'product_id', title: '商品ID(SPU)'}
                , {
                    title: '商品类型', align: 'center', templet: function (d) {
                        let opt = '';
                        if (d.product_type === 1) {
                            opt = "商品";
                        }else if(d.product_type === 2){
                            opt = "商品集合";
                        }
                        return opt;
                    }
                }
                , {
                    title: '商品状态', align: 'center', templet: function (d) {
                        let opt = '';
                        if (d.status) {
                            opt = "上架";
                        }else{
                            opt = "下架";
                        }
                        return opt;
                    }
                }
                // , {
                //     title: '排序值', align: 'center', templet: function (d) {
                //         let opt = '';
                //         opt += '<input style="width:80px" class="layui-input J_sort_num" name="product_name" value="'+d.sort+'" placeholder="产品名称">';
                //
                //         opt += '<button type="button" data-id='+d.id+' class="layui-inline   layui-btn J_save_sort" >保存</button>'
                //
                //         return opt;
                //     }
                // }
                // , {field: 'price', title: '价格'}
                // , {field: 'store', sort:true,title: '库存'}
                // , {field: 'display_status_name', title: '状态'}

            ]],
            done: function () {
                soulTable.render(this);
            }
        });
    });



    @endsection
</script>