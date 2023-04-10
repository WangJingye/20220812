@extends('backend.base')

@section('content')
    <style>
        td {
            word-wrap: break-word;
            word-break: break-all;
        }
    </style>
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <input class="layui-input" name="cateName" autocomplete="off" placeholder="分类名称">
                        </div>
                        <span class="layui-inline layui-btn" id="search">搜索</span>
                        <span class="layui-inline layui-btn" id="add">创建分类</span>
                        <span style="display: none" class="layui-inline layui-btn" id="import">挂载分类商品</span>
                    </div>
                </form>
                <table id="list" lay-filter="list"></table>
                <div id="laypage"></div>
            </div>
        </div>
    </div>
@endsection

<script>
    @section('layui_script')
    // 自定义模块
    layui.config({
        base: '/ext/',   // 模块所在目录
    }).extend({
        treeTable: 'treeTable/treeTable'
    }).use(['table', 'treeTable','upload'], function () {
        var upload = layui.upload;
        upload.render({
            elem: '#import' //绑定元素
            , url: "{{ route('backend.goods.category.handleCatSortCsv') }}" //上传接口
            , accept: 'file' //普通文件
            , done: function (res) {
                //如果上传失败
                if (res.code > 0) {
                    return layer.msg('成功导入');
                } else {
                    return layer.msg('上传失败');
                }
            }
            , error: function () {
                //请求异常回调
            }
        });


        var table = layui.table, treeTable = layui.treeTable;
        var _url = "{{ route('backend.goods.category.list') }}";
        var renderTable = function () {
            treeTable.render({
                elem: '#list',
                tree: {
                    arrowType: 'arrow2',   // 自定义箭头风格
                    iconIndex: 0,  // 折叠图标显示在第几列
                    idName: 'id',  // 自定义id字段的名称
                    pidName: 'parent_cat_id',  // 自定义标识是否还有子节点的字段名称
                    // haveChildName: 'haveChild',  // 自定义标识是否还有子节点的字段名称
                    isPidData: true  // 是否是pid形式数据
                },
                id: 'table_list',
                height: 'full-20',
                text: {
                    //默认：无数据
                    none: '暂无相关数据'
                },
                reqData: function (data, callback) {
                    // 在这里写ajax请求，通过callback方法回调数据
                    $.get(_url, function (res) {
                        callback(res.pageData);  // 参数是数组类型
                    });
                },
                //表头
                cols: [
                    {field: 'cat_name',width: 400, title: '分类名称'}
                    , {field: 'id', title: '分类ID'}
                    , {field: 'cat_type_name', title: '分类类型'}
                    , {
                        fixed: 'right', title: '操作', width: 100, align: 'center', templet: function (d) {
                            let opt = '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">编辑</a>';
                            if (d.status === 0) {
                                opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="display_on">上架</a>';
                            } else if(d.status === 1) {
                                opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="display_off">下架</a>';
                            }
                            opt += '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="prod">产品列表</a>';
                            return opt;
                        }
                    }
                ]
            });
        };
        renderTable();
        treeTable.on('tool(list)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
            var data = obj.data; //获得当前行数据
            var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
            var tr = obj.tr; //获得当前行 tr 的DOM对象
            if (layEvent === 'look') {
                layer.open({
                    title: '查看产品',
                    type: 2,
                    area: ['100%', '100%'],
                    offset: 't',
                    maxmin: false,
                    move: false,
                    content: "{{ route('backend.goods.category.look') }}?cateIdx=" + data.id,
                });
            } else if (layEvent === 'edit') {
                layer.open({
                    title: '查看产品',
                    type: 2,
                    area: ['100%', '100%'],
                    offset: 't',
                    maxmin: false,
                    move: false,
                    content: "{{ route('backend.goods.category.get') }}?cateIdx=" + data.id,
                    end: function () {
                        // window.location.reload()
                    }
                });
            } else if (layEvent === 'prod') {
                layer.open({
                    title: '产品排序',
                    type: 2,
                    area: ['100%', '100%'],
                    offset: 't',
                    maxmin: false,
                    move: false,
                    content: "{{ route('backend.goods.category.relate') }}?cat_id=" + data.id,
                });
            }else if (layEvent === 'display_on') {
                layer.confirm('确定上架类目吗？', function (index) {
                    let subData = {};
                    subData.cat_id = data.id;
                    $.get("{{ route('backend.goods.category.upCat') }}", subData, function (res) {
                        if (res.code === 1) {
                            layer.msg('操作成功', {
                                icon: 1,
                                shade: 0.3,
                                offset: '300px',
                                time: 2000 //2秒关闭（如果不配置，默认是3秒）
                            }, function () {
                                tableTree.reload('table_list')
                            });
                        } else {
                            layer.msg('操作失败', {
                                icon: 2,
                                shade: 0.3,
                                offset: '300px',
                                time: 2000 //2秒关闭（如果不配置，默认是3秒）
                            });
                        }
                        window.location.reload()
                    }, 'json');
                    layer.close(index);
                });
            }else if (layEvent === 'display_off') {
                layer.confirm('确定下架类目吗？', function (index) {
                    let subData = {};
                    subData.cat_id = data.id;
                    $.get("{{ route('backend.goods.category.offCat') }}", subData, function (res) {
                        if (res.code === 1) {
                            layer.msg('操作成功', {
                                icon: 1,
                                shade: 0.3,
                                offset: '300px',
                                time: 2000 //2秒关闭（如果不配置，默认是3秒）
                            }, function () {
                                // treeTable.reload('table_list')
                            });
                        } else {
                            layer.msg('操作失败', {
                                icon: 2,
                                shade: 0.3,
                                offset: '300px',
                                time: 2000 //2秒关闭（如果不配置，默认是3秒）
                            });
                        }
                        window.location.reload()
                    }, 'json');
                    layer.close(index);
                });
            }
        });
        $('#search').on('click', function () {
            _url = "{{ route('backend.goods.category.list') }}?cateName=" + $("input[name='cateName']").val();
            renderTable();
        });

        $('#add').on('click', function () {
            layer.open({
                title: '查看产品',
                type: 2,
                area: ['100%', '100%'],
                offset: 't',
                maxmin: false,
                move: false,
                content: "{{ route('backend.goods.category.add') }}",
                end:function(){
                    renderTable();
                }
            });
        });
        $(document).keyup(function (event) {
            if (event.keyCode == 13) {
                $("#search").trigger("click");
            }
        });
    })
    @endsection
</script>