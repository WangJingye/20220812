@extends('backend.base')

@section('content')

    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body">
                <form class="layui-form layui-form-pane" action="{{ route('backend.member.list') }}" method="POST">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">手机号</label>
                            <div class="layui-input-inline">

                                <input class="layui-input" name="phone" value="{{request('phone')}}" autocomplete="off" placeholder="手机号">
                            </div>
                        </div>

                        <div class="layui-inline">
                            <label class="layui-form-label">用户名</label>
                            <div class="layui-input-inline">
                                <input class="layui-input" name="name" value="{{request('name')}}" autocomplete="off" placeholder="用户名">
                            </div>
                        </div>

                        <div class="layui-inline">
                            <label class="layui-form-label">创建时间</label>
                            <div class="layui-input-inline">
                                <input class="layui-input" id="start_time" name="start_time" placeholder="开始时间"
                                       type="text" value="{{request('start_time')}}" autocomplete="off">
                            </div>
                            <div class="layui-input-inline">
                                <input class="layui-input" id="end_time" name="end_time" placeholder="结束时间"
                                       value="{{request('end_time')}}" type="text" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <span class="layui-btn sub" id="search">搜索</span>
                        <span class="layui-btn sub" id="export_member">导出会员</span>
                    </div>
                </form>
                <table id="list" lay-filter="list"></table>
                <!--导出表 不展示-->
                <div style="display: none;">
                    <table id="data_export">
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
<script src="{{ url('/static/admin/js/jquery.min.js') }}"></script>
<script src="{{ url('/static/admin/laydate.js') }}"></script>
<script type="text/javascript">
    laydate.render({
        elem: '#start_time'  // 输出框id
        , type: 'datetime'
    });
    laydate.render({
        elem: '#end_time'  // 输出框id
        , type: 'datetime'
    });

    /**
     * 导出接口数据的样例
     * @return {[type]} [description]
     */
    function exportApiDemo() {
        layui.use(['jquery', 'excel', 'layer'], function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var excel = layui.excel;

            // 模拟从后端接口读取需要导出的数据
            $.ajax({
                url: "{{ route('backend.member.list.export') }}"
                , method: 'post'
                    , data: {
                        phone: $("input[name='phone']").val(),
                        email: $("input[name='email']").val(),
                        pos_id: $("input[name='pos_id']").val(),
                        name: $("input[name='name']").val(),
                        level: $("#level").val(),
                        channel: $("#channel").val(),
                        from_activity: $("input[name='from_activity']").val(),
                        from_entrance: $("input[name='from_entrance']").val(),
                        source_type: $("#source_type").val(),
                        start_time: $("input[name='start_time']").val(),
                        end_time: $("input[name='end_time']").val(),
                    }
                , dataType: 'json'
                , success: function (res) {
                    var data = res;
                    // 重点！！！如果后端给的数据顺序和映射关系不对，请执行梳理函数后导出
                    data = excel.filterExportData(data, {
                            pos_id:'pos_id',
                            openid: 'openid',
                            channel: 'channel',
                            source_type: 'source_type',
                            level: function(value, line, data) {
                                if (value==0) {
                                    return '新客';
                                }
                                if (value==1) {
                                    return '银卡';
                                }
                                if (value==2) {
                                    return '金卡';
                                }
                                if (value==3) {
                                    return '老客';
                                }
                                if (value==4) {
                                    return '贵宾';
                                }

                                return '';
                            },
                            email: 'email',
                            name: 'name',
                        sex: function(value, line, data) {
                            if(value=='F'){
                                return '女';
                            }
                            if(value=='M'){
                                return '男';
                            }

                            return '未知'
                        },
                        channel: function(value, line, data) {
                            if (value==3) {
                                return 'pc';
                            }
                            if (value==2) {
                                return 'mobile';
                            }
                            if (value==1) {
                                return '小程序';
                            }
                        },

                        source_type: function(value, line, data) {
                            if (value==0) {
                                return '老官网';
                            }
                            if (value==1) {
                                return 'dlc新会员';
                            }
                            if (value==2) {
                                return '其他';
                            }
                            },
                            birth: 'birth',
                            points: 'points',
                            created_at: 'created_at',
                            updated_at: 'updated_at'
                    });
                    // 重点2！！！一般都需要加一个表头，表头的键名顺序需要与最终导出的数据一致
                    data.unshift({
                        pos_id: "会员号",
                        openid: "小程序openid",
                        channel: '设备来源',
                        source_type: '用户来源',
                        level: '等级',
                        email: '邮箱',
                        name: '用户名',
                        sex: '性别',
                        channel:'渠道',
                        source_type:'用户来源',
                        birth: '生日',
                        points: '积分',
                        created_at: '创建时间',
                        updated_at: '更新时间',
                    });

                    var timestart = Date.now();
                    excel.exportExcel({
                        sheet1: data
                    }, 'dlc用户.csv', 'csv');
                    var timeend = Date.now();

                    var spent = (timeend - timestart) / 1000;
                    layer.alert('单纯导出耗时 ' + spent + ' s');
                }
                , error: function () {
                    layer.alert('获取数据失败，请检查是否部署在本地服务器环境下');
                }
            });
        });
    }
</script>
<script>

    @section('layui_script')

    table.render({
        elem: '#list'
        , id: 'table_list'
        , height: 500
        , cellMinWidth: 80
        , url: "{{ route('backend.member.list') }}" //数据接口
        , page: true //开启分页
        , limits: [8, 10, 20]
        , limit: 10 //每页默认显示的数量
        , method: 'post'
        , cols: [
            [ //表头
                {
                    checkbox: true, fixed: true
                }, {
                field: 'id', title: 'ID', sort: true,
            }, {
                field: 'name', title: '名字',
            }, {
                field: 'phone', title: '手机号',
            }, {
                field: 'created_at', title: '创建时间'
            },{field: 'action', title: '操作',width:150}
            ]
        ]
    });
    $(".export").click(function () {
        var ins1 = table.render({
            elem: '#data_export',
            url: "{{ route('backend.member.list.export') }}", //数据接口
            method: 'post',
            title: '用户信息',
            where: {
                phone: $("input[name='phone']").val(),
                email: $("input[name='email']").val(),
                pos_id: $("input[name='pos_id']").val(),
                name: $("input[name='name']").val(),
                level: $("#level").val(),
                channel: $("#channel").val(),
                from_activity: $("input[name='from_activity']").val(),
                from_entrance: $("input[name='from_entrance']").val(),
                source_type: $("#source_type").val(),
                start_time: $("input[name='start_time']").val(),
                end_time: $("input[name='end_time']").val(),
            },
            cols: [[
                {field: 'pos_id', title: '会员号'},
                {field: 'openid', title: 'openid'},
                {field: 'unionid', title: 'unionid'},
                {field: 'fromchannel', title: '首次入会来源'},
                {field: 'orderCount', title: '小程序下单次数'},
                {field: 'orderMoney', title: '小程序下单金额'},
                {field: 'mobileNumber', title: '会员手机号'},
                {field: 'familyName', title: '姓'},
                {field: 'firstName', title: '名'},
                {field: 'gender', title: '性别'},
                {field: 'dateOfBirth', title: '生日'},
                {field: 'residenceCountry', title: '居住地'},
                {field: 'email', title: '邮箱'},
                {field: 'available', title: '状态'},
            ]],
            done: function (res, curr, count) {
                exportData = res;
                table.exportFile(ins1.config.id, exportData, 'csv');
                $('#search').click();
                return false;
            }
        });

        return false;

    });
    table.on('tool(list)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
        var data = obj.data; //获得当前行数据
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        var tr = obj.tr; //获得当前行 tr 的DOM对象
        if (layEvent === 'edit') {
            layer.open({
                type: 2,
                area: ['100%', '100%'],
                offset: 't',
                fixed: false,
                maxmin: true,
                content: "{{ route('backend.member.list.edit') }}?id=" + data.id,
                end: function () {
                    active['reload'].call(this);
                }
            });
        } else if (layEvent === 'change') { //删除
            //向服务端发送删除指令
            $.ajax({
                type: "POST",
                url: "{{ route('backend.member.list.destroy') }}",
                data: {id: data.id},
                success: function (res) {
                    if (res.status == 1) {
                        window.location.reload();
                    } else {
                        layer.msg(res.message, {
                            icon: 5, anim: 6
                        });
                    }
                }
            });
        }else if(layEvent === 'couponList'){
            location.href = '{{url('admin')}}/coupon/mycoupon/index?uid='+data.id;
        }
    });
    var active = {
        reload: function () {
            table.reload('table_list', {
                page: {curr: 1},
                where: {
                    phone: $("input[name='phone']").val(),
                    email: $("input[name='email']").val(),
                    pos_id: $("input[name='pos_id']").val(),
                    name: $("input[name='name']").val(),
                    level: $("#level").val(),
                    channel: $("#channel").val(),
                    from_activity: $("input[name='from_activity']").val(),
                    from_entrance: $("input[name='from_entrance']").val(),
                    source_type: $("#source_type").val(),
                    start_time: $("input[name='start_time']").val(),
                    end_time: $("input[name='end_time']").val(),
                }
            });
        }
    };
    $('#search').on('click', function () {
        var type = 'reload';
        active[type] ? active[type].call(this) : '';
    });

    //设置操作按钮
    function setOperate(data) {
        var availableState = data.available; //获取处理状态
        var btn = "";
        // btn += '<button class="layui-btn layui-btn-xs" lay-event="edit">查看</button>';
        //如果处理状态为审核的话按钮就是转销售
        if (availableState == 1) {
            btn += '<button class="layui-btn layui-btn-xs layui-btn-danger" lay-event="change">禁用</button>';
        } else if (availableState == 2) {
            btn += '<button class="layui-btn layui-btn-xs layui-btn-normal" lay-event="change">解封</button>';
        }
        return btn;
    }

    $("#export_member").on('click', function () {
        $.post("{{ route('backend.member.exportMember') }}",[], function (res) {
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