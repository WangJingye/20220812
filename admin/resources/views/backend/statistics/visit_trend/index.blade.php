@extends('backend.base')
@section('content')
    <div class="layui-tab" lay-filter="stat_visit_trend">
        <ul class="layui-tab-title">
            <li class="layui-this" lay-id="daily">每日数据明细</li>
            <li lay-id="monthly" lay-url="{{route('dsa.retain.monthly')}}">每月数据明细</li>
            <li lay-id="weekly" lay-url="{{route('dsa.retain.weekly')}}">每周数据明细</li>
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
                            <table class="layui-table">
                                <colgroup>
                                    <col width=25%">
                                    <col width="25%">
                                    <col width="25%">
                                    <col width="25%">
                                </colgroup>
                                <thead>
                                <tr>
                                    <th>查询日期</th>
                                    <th>人均停留时长 (stay_time_uv,单位：秒)</th>
                                    <th>次均停留时长 (stay_time_session,单位：秒)</th>
                                    <th>平均访问深度（visit_depth）</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="ref_date">{{date('0000-00-00')}}</td>
                                    <td class="stay_time_uv">0</td>
                                    <td class="stay_time_session">0</td>
                                    <td class="visit_depth">0</td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="layui-inline">
                                <canvas id="dailyStayChart" width=860 height=400></canvas>
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
                            <table class="layui-table">
                                <colgroup>
                                    <col width=25%">
                                    <col width="25%">
                                    <col width="25%">
                                    <col width="25%">
                                </colgroup>
                                <thead>
                                <tr>
                                    <th>查询日期</th>
                                    <th>人均停留时长 (stay_time_uv,单位：秒)</th>
                                    <th>次均停留时长 (stay_time_session,单位：秒)</th>
                                    <th>平均访问深度（visit_depth）</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="ref_date">{{date('0000-00-00')}}</td>
                                    <td class="stay_time_uv">0</td>
                                    <td class="stay_time_session">0</td>
                                    <td class="visit_depth">0</td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="layui-inline">
                                <canvas id="monthlyStayChart" width=860 height=400></canvas>
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
                            <table class="layui-table">
                                <colgroup>
                                    <col width=25%">
                                    <col width="25%">
                                    <col width="25%">
                                    <col width="25%">
                                </colgroup>
                                <thead>
                                <tr>
                                    <th>查询日期</th>
                                    <th>人均停留时长 (stay_time_uv,单位：秒)</th>
                                    <th>次均停留时长 (stay_time_session,单位：秒)</th>
                                    <th>平均访问深度（visit_depth）</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="ref_date">{{date('0000-00-00')}}</td>
                                    <td class="stay_time_uv">0</td>
                                    <td class="stay_time_session">0</td>
                                    <td class="visit_depth">0</td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="layui-inline">
                                <canvas id="weeklyStayChart" width=860 height=400></canvas>
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
    element.on('tab(stat_visit_trend)', function (data) {
        var tabAttr = this.getAttribute('lay-id');
        if ('daily' === tabAttr) {
            myChart = getChart(labels, dailyCont, labtitle.daily);
            submitUrl = "{{route('dsa.visit.trend.daily')}}";
        } else if ('monthly' === tabAttr) {
            myChart = getChart(labels, monthlyCont, labtitle.monthly);
            submitUrl = "{{route('dsa.visit.trend.monthly')}}";
        } else if ('weekly' === tabAttr) {
            myChart = getChart(labels, weeklyCont, labtitle.weekly);
            submitUrl = "{{route('dsa.visit.trend.weekly')}}";
        }
    });

    //监听表单提交
    form.on('submit(searchDaily)', function (input) {
        getDailyData(input.field);
        return false;//阻止表单跳转
    });
    form.on('submit(searchMonthly)', function (input) {
        getDailyData(input.field);
        return false;//阻止表单跳转
    });
    form.on('submit(searchWeekly)', function (input) {
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

                        // addData(chart, 'Visit_uv', callDate.data_visit);
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
        var dateNow = new Date();
        var submitUrl = "{{route('dsa.visit.trend.daily')}}";
        var myChart;
        var dailyCont = document.getElementById('dailyStayChart').getContext('2d');
        var monthlyCont = document.getElementById('monthlyStayChart').getContext('2d');
        var weeklyCont = document.getElementById('weeklyStayChart').getContext('2d');

        $('.stay_time_uv').html(0);
        $('.stay_time_session').html(0);
        $('.visit_depth').html(0);

        var labels = ['打开次数(session_cnt)', '访问次数(visit_pv)', '访问人数(visit_uv)', '新用户数(visit_uv_new)'];
        var labtitle = {
            'daily': '日趋势统计',
            'monthly': '月趋势统计',
            'weekly': '周趋势统计',
        };

        function getChart(labels, eleCont, labtitle) {
            return new Chart(eleCont, {
                type: 'horizontalBar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: '',
                            fillColor: "rgba(220,220,220,0.5)",
                            strokeColor: "rgba(220,220,220,0.8)",
                            highlightFill: "rgba(220,220,220,0.75)",
                            highlightStroke: "rgba(220,220,220,1)",
                            backgroundColor: 'rgb(80,140,255)',
                            borderColor: 'rgb(79,255,169)',
                            data: [0, 0, 0, 0],

                        },
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
                        color: "rgba(151,0,0,0)",
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
            $('.ref_date').html(d.ref_date);
            $('.stay_time_uv').html(d.stay_time_uv);
            $('.stay_time_session').html(d.stay_time_session);
            $('.visit_depth').html(d.visit_depth);
            myChart.config.data.datasets[0].data = d.info;
            // myChart.config.data.datasets[1].data = d.data_visit;
            myChart.update();
        }

        myChart = getChart(labels, dailyCont, labtitle.daily)
    </script>
@endsection
