@extends('backend.base')
@section('content')
    <div class="layui-tab" lay-filter="stat_summary">
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <div class="layui-col-md12">
                     {{--搜索框--}}
                                        {{--<div class="layui-card">--}}
                                            {{--<div class="layui-card-body">--}}
                                                {{--<form class="layui-form layui-form-pane" action="">--}}
                                                    {{--<div class="layui-form-item">--}}
                                                        {{--<label class="layui-form-label" style="width: 200px">最大查询时间为昨日</label>--}}
                                                        {{--<div class="layui-inline">--}}
                                                            {{--<input class="layui-input" id="searchDailyDate" type="text" name="searchDailyDate">--}}
                                                        {{--</div>--}}
                                                        {{--<span class="layui-inline layui-btn" id="searchDaily" lay-filter="searchDaily" lay-submit>搜索</span>--}}

                                                    {{--</div>--}}
                                                {{--</form>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                    {{--图表--}}

                    <div class="layui-card">
                        <table id="summaryStat" lay-filter="test"></table>
                                                <div class="layui-card-body">
                                                    <div class="layui-inline">
                                                        <canvas id="dailyChart" width=860 height=600></canvas>
                                                    </div>
                                                </div>
                    </div>
                </div>
            </div>
        </div>

        {{--        <table id="demo" lay-filter="test"></table>--}}
    </div>

@endsection
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>

<script>
    @section('layui_script')

    layui.use('table', function () {
        var table = layui.table;
        var dataUrl = "{{route('dsa.summary.daily')}}";
        //第一个实例
        table.render({
            elem: '#summaryStat'
            , height: 480
            , url: dataUrl //数据接口
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'id', title: 'ID', width: 120, sort: true}
                , {field: 'visit_total', title: '累计访客数', width: 120, sort: true}
                , {field: 'visit_uv_new', title: '新访客数', width: 120, sort: true}
                , {field: 'visit_uv', title: '访客数', width: 100, sort: true}
                , {field: 'visit_pv', title: '访问次数', width: 120, sort: true}
                , {field: 'session_cnt', title: '打开次数', width: 100}
                , {field: 'stay_time_uv', title: '人均停留时间', width: 120}
                , {field: 'visit_depth', title: '访问深度', width: 90}
                , {field: 'share_pv', title: '转发次数', width: 90}
                , {field: 'share_uv', title: '转发人数', width: 90}
                , {field: 'updated_at', title: '更新日期', width: 120}
                ,{field: 'ref_date', title: '数据日期', width:120, sort: true, fixed: 'left'}

            ]]
            , response: {
                statusName: 'code' //规定数据状态的字段名称，默认：code
                , statusCode: 200 //规定成功的状态码，默认：0
                , msgName: 'msg' //规定状态信息的字段名称，默认：msg
            }
            , parseData: function (res) { //res 即为原始返回的数据
                return {
                    "code": res.code, //解析接口状态
                    "msg": res.msg, //解析提示文本
                    "count": res.count, //解析数据长度
                    "data": res.data //解析数据列表
                };
            }
        });

    });
    //日期 选择框
    laydate.render({
        elem: '#searchDailyDate', //指定元素
                value: "{{date('Y-m-d',strtotime(date('Y-m-d').' -1 day'))}}",
        max: "{{date('Y-m-d',strtotime(date('Y-m-d').' -1 day'))}}",
    });

    监听表单提交
    form.on('submit(searchDaily)', function (input) {
       console.log(submitUrl);
        getDailyData(input.field);
         return false;//阻止表单跳转
     });

    // function getDailyData(subData) {
    //     $.ajax({
    //         url: submitUrl,
    //         type: 'post',
    //         data: subData,
    //         beforeSend: function () {
    //             this.layerIndex = layer.load(0, {shade: [0.5, '#393D49']});
    //         },
    //         success: function (sd) {
    //             if (sd.status == 'error') {
    //                 layer.msg(sd.msg, {icon: 5});
    //                 // return;
    //             } else if (sd.status == 'success') {
    //                 layer.msg(sd.msg, {
    //                     icon: 6,//成功的表情
    //                     time: 2000 //1秒关闭（如果不配置，默认是3秒）
    //                 }, function () {
    //                     // location.reload();
    //                     //加载数据
    //
    //                     // addData(chart, 'Visit_uv', callDate.data_visit);
    //                 });
    //             }
    //             addData(sd.data);
    //             return;
    //         },
    //         complete: function () {
    //             layer.close(this.layerIndex);
    //         },
    //     });
    // }
    @endsection
</script>

@section('script')
    {{--    <script>--}}
    {{--        var dateNow = new Date();--}}
    {{--        var submitUrl = "{{route('dsa.retain.daily')}}";--}}
    {{--        var myChart;--}}
    {{--        var dailyCont = document.getElementById('dailyChart').getContext('2d');--}}

    {{--        var labelsList = ['当天', '1天后', '2天后', '3天后', '4天后', '5天后', '6天后', '7天后', '14天后', '30天后'];--}}

    {{--        function getChart(labels, eleCont) {--}}
    {{--            return new Chart(eleCont, {--}}
    {{--                type: 'bar',--}}
    {{--                data: {--}}
    {{--                    labels: labels,--}}
    {{--                    datasets: [--}}
    {{--                        {--}}
    {{--                            label: 'Visit_uv_new',--}}
    {{--                            fillColor: "rgba(220,220,220,0.5)",--}}
    {{--                            strokeColor: "rgba(220,220,220,0.8)",--}}
    {{--                            highlightFill: "rgba(220,220,220,0.75)",--}}
    {{--                            highlightStroke: "rgba(220,220,220,1)",--}}
    {{--                            backgroundColor: 'rgb(80,140,255)',--}}
    {{--                            borderColor: 'rgb(79,255,169)',--}}
    {{--                            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],--}}

    {{--                        },--}}
    {{--                        {--}}
    {{--                            label: 'Visit_uv',--}}
    {{--                            fillColor: "rgba(220,220,220,0.5)",--}}
    {{--                            strokeColor: "rgba(220,220,220,0.8)",--}}
    {{--                            highlightFill: "rgba(220,220,220,0.75)",--}}
    {{--                            highlightStroke: "rgba(220,220,220,1)",--}}
    {{--                            backgroundColor: 'rgb(102,255,121)',--}}
    {{--                            borderColor: 'rgb(79,255,169)',--}}
    {{--                            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],--}}

    {{--                        }--}}
    {{--                    ],--}}
    {{--                },--}}
    {{--                options: {--}}
    {{--                    title: { //标题--}}
    {{--                        display: true,--}}
    {{--                        text: '日留存统计',--}}
    {{--                        // fontColor: "#e9e9e9",--}}
    {{--                    },--}}
    {{--                    legend: { //图例--}}
    {{--                        display: true,--}}
    {{--                        color: "rgba(151,164,176,0.14)",--}}
    {{--                        // labels: {--}}
    {{--                        //     fontColor: "#c9c9c9",--}}
    {{--                        // }--}}
    {{--                    },--}}
    {{--                    scales: {--}}
    {{--                        yAxes: [{--}}
    {{--                            ticks: {--}}
    {{--                                beginAtZero: true--}}
    {{--                            }--}}
    {{--                        }]--}}
    {{--                    }--}}

    {{--                }--}}
    {{--            });--}}
    {{--        }--}}

    {{--        //更新数据--}}
    {{--        function addData(d) {--}}
    {{--            // console.log(myChart);--}}
    {{--            myChart.config.data.datasets[0].data = d.data_visit_new;--}}
    {{--            myChart.config.data.datasets[1].data = d.data_visit;--}}
    {{--            myChart.update();--}}
    {{--        }--}}

    {{--        myChart = getChart(labelsList.daily, dailyCont)--}}
    {{--    </script>--}}
@endsection
