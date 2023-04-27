@extends('backend.base')

@section('content')
    <style>
        .layui-table-cell {
            height: auto !important;
            white-space: normal;
        }
    </style>
    <div class="layui-col-md12">
        <form class="layui-form" action="" id="search-form" lay-filter="search-form" onsubmit="return false;">
            <div class="layui-form-item">
                <label class="layui-form-label">储值卡名称</label>
                <div class="layui-input-inline">
                    <input class="layui-input" name="gold_name" value="<?= request('gold_name')?>">
                </div>
                <label class="layui-form-label">手机号</label>
                <div class="layui-input-inline">
                    <input class="layui-input" name="phone" value="<?= request('phone')?>">
                </div>
                <label class="layui-form-label">订单号</label>
                <div class="layui-input-inline">
                    <input class="layui-input" name="order_sn" value="<?= request('order_sn')?>">
                </div>
                <label class="layui-form-label">开票状态</label>
                <div class="layui-input-inline">
                    <select name="is_invoice">
                        <option value="">无</option>
                        <?php foreach ([1 => '开票申请中', 2 => '已开票', 0 => '未开票'] as $k => $v): ?>
                        <option value="<?=$k?>" <?= (string)$k == request('is_invoice') ? 'selected' : ''?>><?=$v?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">储值卡状态</label>
                <div class="layui-input-inline">
                    <select name="status">
                        <option value="">无</option>
                        <?php foreach ([1 => '未使用', 2 => '已使用', 3 => '已退款', 4 => '退款申请中', 0 => '已过期'] as $k => $v): ?>
                        <option value="<?=$k?>" <?= (string)$k == request('status') ? 'selected' : ''?>><?=$v?></option>
                        <?php endforeach;?>
                    </select>
                </div>
                <div class="layui-inline" style="position: relative">
                    <label class="layui-form-label">购买时间</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" id="start_time" name="start_time" placeholder="开始时间" type="text"
                               value="{{request('start_time')}}" autocomplete="off">
                    </div>
                    <div class="layui-input-inline">
                        <input class="layui-input" id="end_time" name="end_time" placeholder="结束时间"
                               value="{{request('end_time')}}" type="text" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <button class="layui-btn layui-btn-normal" id="search">搜索</button>
                    <button class="layui-btn" id="recharge">手动充值</button>
                </div>
            </div>
        </form>
        <div class="layui-card">
            <div class="layui-card-body">
                <table id="list" lay-filter="list"></table>
            </div>
        </div>
    </div>
    <div style="display: none">
        <div class="refund-info-hidden">
            <form onsubmit="return false;" style="margin-top: 20px">
                <div class="layui-form-item">
                    <label class="layui-form-label">原支付方式</label>
                    <div class="layui-input-block">
                        <input type="text" class="layui-input payment_type" readonly value="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">退款方式</label>
                    <div class="layui-input-block">
                        <input type="text" class="layui-input return_type" readonly value="原路返还">
                    </div>
                </div>
                <input name="id" type="hidden">
                <input name="action_type" type="hidden" value="2">
                <div class="layui-form-item">
                    <label class="layui-form-label">退款金额</label>
                    <div class="layui-input-block">
                        <input name="refund_amount" lay-verify="required" value="" autocomplete="off"
                               class="layui-input"
                               type="text">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"></label>
                    <button class="layui-btn layui-btn-normal confirm-refund">确定</button>
                </div>
            </form>
        </div>
        <div class="invoice-info-hidden">
            <form onsubmit="return false;" style="margin-top: 20px">
                <input name="id" type="hidden">
                <div class="layui-form-item">
                    <label class="layui-form-label">开票类型</label>
                    <div class="layui-input-block">
                        <input type="text" class="layui-input type" readonly value="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">抬头</label>
                    <div class="layui-input-block">
                        <input readonly class="layui-input title" type="text">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">纳税人识别号</label>
                    <div class="layui-input-block">
                        <input readonly class="layui-input code" type="text">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">邮箱</label>
                    <div class="layui-input-block">
                        <input readonly class="layui-input email" type="text">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">预开票金额</label>
                    <div class="layui-input-block">
                        <input readonly class="layui-input pay_amount" type="text">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"></label>
                    <button class="layui-btn layui-btn-normal confirm-invoice">确定</button>
                </div>
            </form>
        </div>
        <div class="recharge-info-hidden">
            <form onsubmit="return false;" style="margin-top: 20px">
                <input name="id" type="hidden">
                <div class="layui-form-item">
                    <label class="layui-form-label">手机号</label>
                    <div class="layui-input-block">
                        <input type="text" class="layui-input type" placeholder="用户手机号" name="mobile" value="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">储值卡名称</label>
                    <div class="layui-input-block">
                        <input type="text" class="layui-input type" placeholder="储值卡显示名称" name="gold_name" value="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">充值金额</label>
                    <div class="layui-input-block">
                        <input class="layui-input title" placeholder="例如:2000" name="face_value" type="number">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">支付金额</label>
                    <div class="layui-input-block">
                        <input class="layui-input title" placeholder="例如：1000" name="price" type="number">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">有效期</label>
                    <div class="layui-input-block">
                        <select name="valid_time" class="layui-select" style="width: 100%">
                            <option value="">请选择</option>
                            <?php for($i = 1;$i <= 1;$i++):?>
                            <option value="<?=$i?>"><?= $i?>年</option>
                            <?php endfor;?>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"></label>
                    <button class="layui-btn layui-btn-normal confirm-recharge">确定</button>
                </div>
            </form>
        </div>
    </div>
@endsection

<script>
    @section('layui_script')
    var dataTable = table.render({
        elem: '#list',
        id: 'table_list',
        height: 560,
        //数据接口
        url: "{{ route('backend.gold.getUserBalanceList') }}",
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
            {field: 'id', title: 'ID', width: 60},
            {field: 'order_sn', title: '订单编号'},
            {
                field: 'nickname',
                title: '用户昵称<hr style="margin: 0;padding: 0">手机号',
                align: "center",
                width: 130,
                templet: function (d) {
                    return d.nickname + '<hr style="margin: 0;padding: 0">' + d.phone;
                }
            },
            {
                field: 'first_name', title: '姓名', templet: function (d) {
                    return d.first_name + d.last_name;
                }
            },
            {field: 'gold_name', title: '储值卡'},
            {
                field: 'amount',
                title: '已使用<hr style="margin: 0;padding: 0">面值',
                align: "center",
                templet: function (d) {
                    return d.used_amount + '<hr style="margin: 0;padding: 0">' + d.amount;
                },
            },
            {
                field: 'status', title: '储值卡状态', width: 100, templet: function (d) {
                    let statusList = {1: '未使用', 2: '已使用', 3: '已退款', 4: '退款申请中', 0: '已过期'};
                    return statusList[d.status];
                }
            },
            {
                field: 'is_invoice', title: '开票状态', width: 100, templet: function (d) {
                    let statusList = {1: '已开票', 2: '开票申请中', 0: '未开票'};
                    return statusList[d.is_invoice];
                }
            },
            {
                field: 'start_time', title: '有效期', width: 180, templet: function (d) {
                    return '<div>' + d.start_time + '</div><div>' + d.end_time + '</div>';
                }
            },
            {field: 'created_at', title: '购买时间', width: 120},
            {
                title: '操作', align: 'center', templet: function (d) {
                    let opt = '';
                    if (d.is_invoice === 1) {
                        opt += '<a class="layui-btn layui-btn-success layui-btn-xs" lay-event="invoice">开票</a>';
                    }
                    if (d.status === 4) {
                        opt += '<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="refund">退款</a>';
                    }
                    return opt;
                }
            }
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

    table.on('tool(list)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
        var data = obj.data; //获得当前行数据
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        var tr = obj.tr; //获得当前行 tr 的DOM对象
        if (layEvent === 'invoice') {
            let dom = $('.invoice-info-hidden').clone();
            dom.find('form').addClass('invoice-info-form');
            layer.open({
                type: 1,
                content: dom.html(),
                title: '开票信息',
                area: ['80%', '100%'],
                offset: 't',
                fixed: true,
                end: function () {
                    table.reload('table_list')
                }
            });
            let form = $('.invoice-info-form');
            let invoice = JSON.parse(data.invoice);
            form.find('.code').val(invoice['code']);
            form.find('.title').val(invoice['title']);
            form.find('.email').val(invoice['email']);
            form.find('.type').val(invoice['type'] == 1 ? '个人' : '企业');
            form.find('.pay_amount').val(data.pay_amount);
            form.find('[name=id]').val(data.id);
        }
        if (layEvent === 'refund') {
            let dom = $('.refund-info-hidden').clone();
            dom.find('form').addClass('refund-info-form');
            layer.open({
                type: 1,
                content: dom.html(),
                title: '退款信息',
                area: ['80%', '100%'],
                offset: 't',
                fixed: true,
                end: function () {
                    table.reload('table_list')
                }
            });
            let payMethods = {2: '微信支付', 10: '储值余额支付', 11: '组合支付', 12: '人工充值'};
            let form = $('.refund-info-form');
            form.find('.payment_type').val(payMethods[data.pay_method]);
            let refundAmount = data.pay_amount - data.used_amount;
            form.find('[name=refund_amount]').val(refundAmount > 0 ? refundAmount : 0);
            form.find('[name=id]').val(data.id);
        }
    });
    $('body').on('click', '.confirm-refund', function () {
        let args = $('.refund-info-form').serialize();
        $.post("{{ route('backend.gold.refund') }}", args, function (res) {
            layer.msg(res.msg);
            if (res.code == 1) {
                $('.refund-info-form').parents('.layui-layer-page').find('.layui-layer-close').click()
            }
        });
    }).on('click', '.confirm-invoice', function () {
        let args = $('.invoice-info-form').serialize();
        $.post("{{ route('backend.gold.invoice') }}", args, function (res) {
            layer.msg(res.msg);
            if (res.code == 1) {
                $('.invoice-info-form').parents('.layui-layer-page').find('.layui-layer-close').click()
            }
        });
    }).on('click', '.confirm-recharge', function () {
        let form = $('.recharge-info-form');
        if (form.find('input[name=gold_name]').val() == '') {
            layer.msg('请输入储值卡名称');
            return false;
        }
        if (form.find('input[name=face_value]').val() == '') {
            layer.msg('请输入充值金额');
            return false;
        }
        if (form.find('input[name=price]').val() == '') {
            layer.msg('请输入实付金额');
            return false;
        }
        if (form.find('input[name=valid_time]').val() == '') {
            layer.msg('请输入有效期');
            return false;
        }
        if (form.find('input[name=mobile]').val() == '') {
            layer.msg('请输入手机号');
            return false;
        }
        if (form.find('input[name=face_value]').val() <= 0) {
            layer.msg('充值金额必须大于0');
            return false;
        }
        if (form.find('input[name=price]').val() <= 0) {
            layer.msg('实付金额必须大于0');
            return false;
        }
        let args = form.serialize();
        $.post("{{ route('backend.gold.recharge') }}", args, function (res) {
            layer.msg(res.msg);
            if (res.code == 1) {
                $('.recharge-info-form').parents('.layui-layer-page').find('.layui-layer-close').click()
            }
        });
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
    $('#recharge').click(function () {
        let dom = $('.recharge-info-hidden').clone();
        dom.find('form').addClass('recharge-info-form');
        layer.open({
            type: 1,
            content: dom.html(),
            title: '手动充值',
            area: ['80%', '100%'],
            offset: 't',
            fixed: true,
            end: function () {
                table.reload('table_list')
            }
        });
    });
    $(document).keyup(function (event) {
        if (event.keyCode == 13) {
            $("#search").trigger("click");
        }
    });
    @endsection
</script>

