@extends('backend.base')
<link rel="stylesheet" href="{{ url('/static/admin/css/form.css') }}" media="all">
<style>
    .layui-form-item {
        white-space: nowrap !important;
    }

</style>
@section('content')

    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>新增手工单</h2>
        </div>
        <div class="layui-tab">
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <div class="layui-card-body">
                        <form method="post" class="layui-form">
                            {{csrf_field()}}

                            <div class="layui-form-item">
                                <label for="" class="layui-form-label">用户ID</label>
                                <div class="layui-input-block">
                                    <div class=" layui-col-xs3">
                                        <input class="layui-input" type="text" name="user_id" lay-verify="required"
                                               value="">
                                    </div>
                                    <div class="layui-col-xs2">
                                        <button type="button" class="layui-btn" id="J_select_address">选择用户地址</button>
                                    </div>
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label class="layui-form-label">地址信息</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="contact" lay-verify="required" placeholder="收件人"
                                           autocomplete="off" class="layui-input" readonly>
                                </div>

                                <div class="layui-input-inline">
                                    <input type="text" name="mobile" lay-verify="required" placeholder="手机号"
                                           autocomplete="off" class="layui-input" readonly>
                                </div>
                                <div class="layui-col-xs1">
                                    <input type="text" name="province" lay-verify="required" placeholder="省"
                                           autocomplete="off" class="layui-input" readonly>
                                </div>
                                <div class="layui-col-xs1">
                                    <input type="text" name="city" lay-verify="required" placeholder="市"
                                           autocomplete="off" class="layui-input" readonly>
                                </div>
                                <div class="layui-col-xs1">
                                    <input type="text" name="district"  placeholder="区"
                                           autocomplete="off" class="layui-input" readonly>
                                </div>

                            </div>
                            <input type="hidden" name="shipping_address" value="">
                            <input type="hidden" name="goods_info" value="">
                            <div class="layui-form-item">
                                <label class="layui-form-label">详细地址</label>
                                <div class="layui-input-block">
                                    <div class=" layui-col-xs5">
                                        <input type="text" name="address" lay-verify="required" placeholder="详细地址"
                                               autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label for="" class="layui-form-label">SKU</label>
                                <div class="layui-input-block">
                                    <div class=" layui-col-xs5">
                                        {{--                                        <input class="layui-input" type="text" name="sku_ids"--}}
                                        {{--                                               value="" placeholder="手工单需要设置包含sku，英文逗号分隔">--}}
                                        <textarea placeholder="请输入备注信息"
                                                  class="layui-textarea layui-input"
                                                  name="sku_ids" id="sku_ids"></textarea>
                                    </div>
                                    <div class="layui-col-xs2">
                                        <button type="button" class="layui-btn" id="pre_product">一键预览</button>
                                    </div>
                                </div>
                            </div>


                            <div class="layui-card" style="margin-left: 100px;">

                                <div class="layui-card-body">
                                    <table class="layui-hide" id="product">
                                    </table>
                                </div>

                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">备注</label>
                                <div class="layui-input-block">
                                    <div class=" layui-col-xs5">
                                       <textarea placeholder="请输入备注信息"
                                                 class="layui-textarea layui-input"
                                                 name="remark" lay-verify="required"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <div class="layui-input-block">
                                    <button type="submit" class="layui-btn" lay-submit="" lay-filter="confirm">确 认
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection



<script type="text/html" id="barDemo">
    <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="select">选择</a>
</script>

<div class="layui-form-pop" style="display:none;">
    <div class="layui-card-body">
        <form class="layui-form" lay-filter="extra">
            <input type="hidden" name="cateIdx" id="cateIdx" value="">
            <table id="list" lay-filter="list"></table>


        </form>
    </div>
</div>


<script>
    @section('layui_script')

    //注意：选项卡 依赖 element 模块，否则无法进行功能性操作
    layui.use('element', function () {
        var element = layui.element;

        //…
    });

    form.verify({
        Ndouble: [
            /^[0-9]\d*$/
            , '只能输入整数哦'
        ]
    });
    var sku_ids = $('#sku_ids').val();


    $('#pre_product').on('click', function () {
        sku_ids = $('#sku_ids').val();
        table.render({
            elem: '#product'
            ,totalRow: true
            , url: "{{ route('backend.oms.add.sku') }}?sku_ids=" + sku_ids
            , page: { //支持传入 laypage 组件的所有参数（某些参数除外，如：jump/elem） - 详见文档
                layout: ['limit', 'count', 'prev', 'page', 'next', 'skip'] //自定义分页布局
                //,curr: 5 //设定初始在第 5 页
                , groups: 1 //只显示 1 个连续页码
                , first: false //不显示首页
                , last: false //不显示尾页

            }
            , cols: [[
                {type: 'checkbox', fixed: 'left'}
                ,{field: 'id',title:'sku',unresize: true, fixed: 'left',totalRowText: '合计'}
                // ,{field: 'id',title:'sku',unresize: true}
                ,{field: 'product_name',title:'商品'}
                ,{field: 'spec_desc',title:'规格'}
                ,{field: 'price', title: '价格',totalRow: true}
                ,{field: 'num', title: '数量', totalRow: true}

            ]]
            , parseData: function (res) {
                return {
                    "code": 0,
                    "data": res.data,
                }
            }
            , done: function (res) {
                //如果是异步请求数据方式，res即为你接口返回的信息。
                //如果是直接赋值的方式，res即为：{data: [], count: 99} data为当前页数据、count为数据总长度
                if (res.code == 2 || res.data.length < 1) {
                    layer.msg('预览失败，请确认sku信息', {
                        icon: 2,
                        shade: 0.3,
                        offset: '300px',
                        time: 2000 //2秒关闭（如果不配置，默认是3秒）
                    }, function () {
                        let index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                        parent.layer.close(index); //再执行关闭
                    });
                }

            }

        });
    });


    $('#J_select_address').on('click', function () {
        prodIndex = layer.open({
            title: '请选择用户地址',
            type: 1,
            area: ['100%', '100%'],
            offset: 't',
            maxmin: false,
            move: false,
            content: $(".layui-form-pop"),
        });

        // let styleNbr = $(this).parents('.J_colle_product').find('.J_search_content').val();
        let user_id = $('input[name="user_id"]').val();

        dataTable = table.render({
            elem: '#list'
            , id: 'table_list'
            , url: "{{ route('backend.member.address') }}?user_id=" + user_id //数据接口
            //开启分页
            , page: false
            , method: 'get'
            , async: false
            , limit: 10
            , text: {
                none: '暂无相关数据' //默认：无数据
            }
            , parseData: function (res) {
                return {
                    "code": 0,
                    "data": res.data,
                }
            }
            , toolbar: '#toolbar' //开启头部工具栏，并为绑定左侧模板
            , defaultToolbar: ['filter']
            // ,where: {pid: '1'}
            , cols: [[ //表头
                {field: 'id', title: '地址id'}
                , {field: 'name', title: '收件人'}
                , {field: 'mobile', title: '手机号'}
                , {field: 'province', title: '省'}
                , {field: 'city', title: '市'}
                , {field: 'area', title: '区'}
                , {field: 'address', title: '具体地址'}
                , {fixed: 'right', width: 178, align: 'center', toolbar: '#barDemo'}
            ]]
        });
    });

    layui.use(['form'], function () {

        form.on('submit(confirm)', function (data) {
            $.post("{{ route('backend.oms.add.insert') }}", data.field, function (res) {
                if (res.code === 1) {
                    layer.msg('操作成功', {
                        icon: 1,
                        shade: 0.3,
                        offset: '300px',
                        time: 2000 //2秒关闭（如果不配置，默认是3秒）
                    }, function () {
                        let index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                        parent.layer.close(index); //再执行关闭
                    });
                    window.location.href="{{ route('backend.oms.add') }}";
                } else {
                    layer.msg(res.message, {
                        icon: 2,
                        shade: 0.3,
                        offset: '300px',
                        time: 2000 //2秒关闭（如果不配置，默认是3秒）
                    });
                }
            }, 'json');
            return false;
        });


        //监听工具条
        table.on('tool(list)', function (obj) {
            var data = obj.data;
            if (obj.event === 'select') {
                console.log(data);
                $('input[name="province"]').val(data.province);
                $('input[name="city"]').val(data.city);
                $('input[name="mobile"]').val(data.mobile);
                $('input[name="contact"]').val(data.name);
                $('input[name="address"]').val(data.address);
                $('input[name="district"]').val(data.area);
                $('input[name="shipping_address"]').val(JSON.stringify(data));
                // form.render();
                layer.close(prodIndex)
            }
        });


    });

    @endsection
</script>