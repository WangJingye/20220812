@extends('backend.base')
@section('content')
    <div class="layui-tab" lay-filter="stat_user">
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <div class="layui-col-md12">
                    {{-- 搜索框--}}
                    <div class="layui-card">
                        <div class="layui-card-body">
                            <form class="layui-form layui-form-pane" action="">

                                <div class="layui-form-item">
                                    <div class="layui-col-md12">
                                        开始时间 = 结束时间 - 查询天数
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label" style="width: 200px">结束时间</label>
                                        <div class="layui-inline">
                                            <input class="layui-input" id="searchDate" type="text" name="searchDate">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label" style="width: 160px">查询天数</label>
                                        <div class="layui-inline">
                                            <select name="searchDays" lay-verify="required">
                                                <option value="0" selected>1天</option>
                                                <option value="6">7天</option>
                                                <option value="29">30天</option>
                                            </select>
                                        </div>
                                    </div>
                                    <span class="layui-inline layui-btn" id="search" lay-filter="search" lay-submit>搜索</span>

                                </div>
                            </form>
                        </div>
                    </div>

                    {{--图表--}}
                    <div class="layui-card">
                        <div class="layui-card-body">
                            <div class="layui-inline">
                                <canvas id="userChart" width=860 height=400></canvas>
                            </div>
                        </div>
                        <div class="layui-card-body">
                            <div class="layui-inline">
                                <canvas id="newUserChart" width=860 height=400></canvas>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>

<script>
    @section('layui_script')
    //日期 选择框
    laydate.render({
        elem: '#searchDate', //指定元素
        {{--        value: "{{date('Y-m-d',strtotime(date('Y-m-d').' -1 day'))}}",--}}
        max: "{{date('Y-m-d',strtotime(date('Y-m-d').' -1 day'))}}",
    });

    //监听表单提交
    form.on('submit(search)', function (input) {
        getDailyData(input.field);
        return false;//阻止表单跳转
    });

    function getDailyData(subData) {
        $.ajax({
            url: submitUrl,
            type: 'post',
            data: subData,
            beforeSend: function () {
                this.layerIndex = layer.load(0, {shade: [0.5, '#393D49']});
            },
            success: function (sd) {
                if (sd.status == 'error') {
                    layer.msg(sd.msg, {icon: 5});
                    return;
                } else if (sd.status == 'success') {
                    layer.msg(sd.msg, {
                        icon: 6,//成功的表情
                        time: 2000 //1秒关闭（如果不配置，默认是3秒）
                    }, function () {
                        // location.reload();
                        //加载数据
                    });
                    addData(sd.data);
                }
                return;
            },
            complete: function () {
                layer.close(this.layerIndex);
            },
        });
    }
    @endsection
</script>

@section('script')
    <script>
        submitUrl = "{{route('dsa.visit.user.portrait')}}";
        var userChart = new Chart(document.getElementById('userChart').getContext('2d'), {
            type: 'polarArea',
            data: {

                labels: ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'],
                datasets: [
                    {
                        // label: 'aa',
                        backgroundColor: [
                            'rgba(216,220,94,0.8)',
                            'rgba(125,220,104,0.8)',
                            'rgba(49,220,183,0.8)',
                            'rgba(51,220,53,0.8)',
                            'rgba(71,89,245,0.8)',
                            'rgba(145,64,220,0.8)',
                            'rgba(220,39,214,0.8)',
                            'rgba(220,132,35,0.8)',
                            'rgba(220,88,34,0.8)',
                            'rgba(220,47,6,0.95)'
                        ],
                        highlight: "rgba(191,186,198,0.93)",
                        borderAlign: "inner",
                        data: [10, 10, 10, 10, 10, 10, 10, 10, 10, 10],

                    },
                ],
            },
            options: {
                title: {
                    display: true,
                    text: '活跃用户分布(访问最多前10)'
                },
            }
        });

        var newUserChart = new Chart(document.getElementById('newUserChart').getContext('2d'), {
            type: 'polarArea',
            data: {

                labels: ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'],
                datasets: [
                    {
                        // label: 'aa',
                        backgroundColor: [
                            'rgba(216,220,94,0.8)',
                            'rgba(125,220,104,0.8)',
                            'rgba(49,220,183,0.8)',
                            'rgba(51,220,53,0.8)',
                            'rgba(71,89,245,0.8)',
                            'rgba(145,64,220,0.8)',
                            'rgba(220,39,214,0.8)',
                            'rgba(220,132,35,0.8)',
                            'rgba(220,88,34,0.8)',
                            'rgba(220,47,6,0.95)'
                        ],
                        highlight: "rgba(191,186,198,0.93)",
                        borderAlign: "inner",
                        data: [10, 10, 10, 10, 10, 10, 10, 10, 10, 10],

                    },
                ],
            },
            options: {
                title: {
                    display: true,
                    text: '新用户分布(访问最多前10)'
                },
            }
        });

        //更新数据
        function addData(d) {
            userChart.config.data.labels = d.user.lables;
            userChart.config.data.datasets[0].data = d.user.data;

            newUserChart.config.data.labels = d.newUser.lables;
            newUserChart.config.data.datasets[0].data = d.newUser.data;

            userChart.update();
            newUserChart.update();
        }

    </script>
@endsection
