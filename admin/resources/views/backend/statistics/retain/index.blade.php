@extends('backend.base')
@section('content')
    <div class="layui-tab" lay-filter="stat_retain">
        <ul class="layui-tab-title">
            <li class="layui-this" lay-id="daily" lay-url="{{route('dsa.retain.daily')}}">日留存</li>
            <li lay-id="monthly" lay-url="{{route('dsa.retain.monthly')}}">月留存</li>
            <li lay-id="weekly" lay-url="{{route('dsa.retain.weekly')}}">周留存</li>
        </ul>
        <div class="layui-tab-content">
            <!-- 日留存 -->
            <div class="layui-tab-item layui-show">
                <div class="layui-col-md12">
                    {{-- 搜索框--}}
                    <div class="layui-card">
                        <div class="layui-card-body">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">
                                    <label class="layui-form-label" style="width: 200px">最大查询时间为昨日</label>
                                    <div class="layui-inline">
                                        <input class="layui-input" id="searchDailyDate" type="text" name="searchDailyDate">
                                    </div>
                                    <span class="layui-inline layui-btn" id="searchDaily" lay-filter="searchDaily" lay-submit>搜索</span>

                                </div>
                            </form>
                        </div>
                    </div>
                    {{--图表--}}
                    <div class="layui-card">
                        <div class="layui-card-body">
                            <div class="layui-inline">
                                <canvas id="dailyChart" width=860 height=400></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 月留存 -->
            <div class="layui-tab-item" lay-filter="retain_month">
                <div class="layui-col-md12">
                    {{-- 搜索框--}}
                    <div class="layui-card">
                        <div class="layui-card-body">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">
                                    <label class="layui-form-label" style="width: 200px">最大查询时间为上月</label>
                                    <div class="layui-inline">
                                        <input class="layui-input" id="searchMonthlyDate" type="text" name="searchMonthlyDate">
                                    </div>
                                    <span class="layui-inline layui-btn" id="searchMonthly" lay-filter="searchMonthly" lay-submit>搜索</span>
                                </div>
                            </form>
                        </div>
                    </div>
                    {{--图表--}}
                    <div class="layui-card">
                        <div class="layui-card-body">
                            <div class="layui-inline">
                                <canvas id="monthlyChart" width=860 height=400></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 周留存 -->

            <div class="layui-tab-item">

                <div class="layui-col-md12">
                    {{-- 搜索框--}}
                    <div class="layui-card">
                        <div class="layui-card-body">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">
                                    <label class="layui-form-label" style="width: 200px">最大查询时间为上周</label>
                                    <div class="layui-inline">
                                        <input class="layui-input" id="searchWeeklyDate" type="text" name="searchWeeklyDate">
                                    </div>
                                    <span class="layui-inline layui-btn" id="searchWeekly" lay-filter="searchWeekly" lay-submit>搜索</span>
                                </div>
                            </form>
                        </div>
                    </div>
                    {{--图表--}}
                    <div class="layui-card">
                        <div class="layui-card-body">
                            <div class="layui-inline">
                                <canvas id="weeklyChart" width=860 height=400></canvas>
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
        elem: '#searchDailyDate', //指定元素
        {{--        value: "{{date('Y-m-d',strtotime(date('Y-m-d').' -1 day'))}}",--}}
        max: "{{date('Y-m-d',strtotime(date('Y-m-d').' -1 day'))}}",
    });

    laydate.render({
        elem: '#searchMonthlyDate', //指定元素
        type: 'month',
        {{--value: "{{date('Y-m-d',strtotime(date('Y-m-01').' -1 day'))}}",--}}
        max: "{{date('Y-m-d',strtotime(date('Y-m-01').' -1 day'))}}",
    });

    laydate.render({
        elem: '#searchWeeklyDate', //指定元素
        // value: dateNow,
        max: "{{date('Y-m-d',strtotime(date('Y-m-01').' -1 day'))}}",
    });


    //监听Tab切换，以改变地址hash值
    element.on('tab(stat_retain)', function (data) {
        var tabAttr = this.getAttribute('lay-id');

        if ('daily' === tabAttr) {
            myChart = getChart(labelsList.daily, dailyCont, labtitle.daily);
            submitUrl = "{{route('dsa.retain.daily')}}";
        } else if ('monthly' === tabAttr) {
            myChart = getChart(labelsList.monthly, monthlyCont, labtitle.monthly);
            submitUrl = "{{route('dsa.retain.monthly')}}";
        } else if ('weekly' === tabAttr) {
            myChart = getChart(labelsList.weekly, weeklyCont, labtitle.weekly);
            submitUrl = "{{route('dsa.retain.weekly')}}";
        }
    });

    //监听表单提交
    form.on('submit(searchDaily)', function (input) {
        console.log(submitUrl);
        getDailyData(input.field);
        return false;//阻止表单跳转
    });
    form.on('submit(searchMonthly)', function (input) {
        console.log(submitUrl);
        getDailyData(input.field);
        return false;//阻止表单跳转
    });
    form.on('submit(searchWeekly)', function (input) {
        console.log(submitUrl);
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
                    // return;
                } else if (sd.status == 'success') {
                    layer.msg(sd.msg, {
                        icon: 6,//成功的表情
                        time: 2000 //1秒关闭（如果不配置，默认是3秒）
                    }, function () {
                        // location.reload();
                        //加载数据

                        // addData(chart, 'Visit_uv', callDate.data_visit);
                    });
                }
                addData(sd.data);
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
        var dateNow = new Date();
        var submitUrl = "{{route('dsa.retain.daily')}}";
        var myChart;
        var dailyCont = document.getElementById('dailyChart').getContext('2d');
        var monthlyCont = document.getElementById('monthlyChart').getContext('2d');
        var weeklyCont = document.getElementById('weeklyChart').getContext('2d');

        var labelsList = {
            'daily': ['当天', '1天后', '2天后', '3天后', '4天后', '5天后', '6天后', '7天后', '14天后', '30天后'],
            'monthly': ['当月', '1月后'],
            'weekly': ['当周', '第2周后', '第3周后', '第4周后', '第5周'],
        };
        var labtitle = {
            'daily': '日留存统计',
            'monthly': '周留存统计',
            'weekly': '月留存统计',
        };

        function getChart(labels, eleCont, labtitle) {
            return new Chart(eleCont, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Visit_uv_new(新增用户留存)',
                            fillColor: "rgba(220,220,220,0.5)",
                            strokeColor: "rgba(220,220,220,0.8)",
                            highlightFill: "rgba(220,220,220,0.75)",
                            highlightStroke: "rgba(220,220,220,1)",
                            backgroundColor: 'rgb(80,140,255)',
                            borderColor: 'rgb(79,255,169)',
                            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],

                        },
                        {
                            label: 'Visit_uv(活跃用户留存)',
                            fillColor: "rgba(220,220,220,0.5)",
                            strokeColor: "rgba(220,220,220,0.8)",
                            highlightFill: "rgba(220,220,220,0.75)",
                            highlightStroke: "rgba(220,220,220,1)",
                            backgroundColor: 'rgb(102,255,121)',
                            borderColor: 'rgb(79,255,169)',
                            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],

                        }
                    ],
                },
                options: {
                    title: { //标题
                        display: true,
                        text: labtitle,
                        // fontColor: "#e9e9e9",
                    },
                    legend: { //图例
                        display: true,
                        color: "rgba(151,164,176,0.14)",
                        // labels: {
                        //     fontColor: "#c9c9c9",
                        // }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }

                }
            });
        }

        //更新数据
        function addData(d) {
            // console.log(myChart);
            myChart.config.data.datasets[0].data = d.data_visit_new;
            myChart.config.data.datasets[1].data = d.data_visit;
            myChart.update();
        }

        myChart = getChart(labelsList.daily, dailyCont, labtitle.daily)
    </script>
@endsection
