@extends('backend.base')
@section('content')
    <div class="layui-tab" lay-filter="stat_page">
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <div class="layui-col-md12">
                    {{-- 搜索框--}}
                    <div class="layui-card">
                        <div class="layui-card-body">
                            <form class="layui-form layui-form-pane" action="">

                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <label class="layui-form-label" style="width: 200px">最新数据查询时间为昨日</label>
                                        <div class="layui-inline">
                                            <input class="layui-input" id="searchDate" type="text" name="searchDate">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label" style="width: 160px">请选择排序方式</label>
                                        <div class="layui-inline">
                                            <select name="searchSort" lay-verify="required">
                                                <option value="" disabled="">请选择排序方式</option>
                                                <option value="page_visit_uv" selected>访问人数(page_visit_uv)</option>
                                                <option value="page_visit_pv">访问次数(page_visit_pv)</option>
                                                <option value="page_staytime_pv">次均停留时长(page_staytime_pv)</option>
                                                <option value="entrypage_pv">进入页次数(entrypage_pv)</option>
                                                <option value="exitpage_pv">退出页次数(exitpage_pv)</option>
                                                <option value="exitpage_pv">跳出率(bounce_rate)</option>
                                                <option value="page_share_pv">转发次数(page_share_pv)</option>
                                                <option value="page_share_uv">转发人数(page_share_uv)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <span class="layui-inline layui-btn" id="search" lay-filter="search" lay-submit>搜索</span>

                                </div>
                            </form>
                        </div>

                    </div>

                    {{-- 表单 --}}
                    <div class="layui-card">
                        <div class="layui-card-body">
                            <fieldset class="layui-elem-field">
                                <legend>当前数据来源时间为</legend>
                            </fieldset>

                            <div class="layui-inline">
                                <table id="page_list" lay-filter="page_list"></table>
                            </div>
                        </div>
                    </div>

                    {{--图表--}}
                    <div class="layui-card">
                        <div class="layui-card-body">
                            <div class="layui-inline">
                                <canvas id="pageChart" width=860 height=400></canvas>
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

                table.render({
                    elem: '#page_list'
                    , height: 450
                    , weight: 1600
                    // , url: tabData //数据接口
                    , page: true //开启分页
                    , cols: [[ //表头
                        //{field: 'id', title: 'ID', width: 60, fixed: 'left'},
                        {field: 'page_path', title: '页面路径', width: 200 }
                        , {field: 'page_visit_pv', title: '访问次数', width: 90}
                        , {field: 'page_visit_uv', title: '访问人数', width: 90}
                        , {field: 'page_staytime_pv', title: '次均停留时长', width: 115 }
                        , {field: 'entrypage_pv', title: '入口页次数', width: 105 }
                        , {field: 'exitpage_pv', title: '跳离页次数', width: 105 }
                        , {field: 'bounce_rate', title: '跳出率', width: 105 }
                        , {field: 'page_share_pv', title: '转发次数', width: 90 }
                        , {field: 'page_share_uv', title: '转发人数', width: 90 }
                    ]]
                    , data: sd.data.list
                });
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
        var submitUrl = "{{route('dsa.visit.page')}}";
        {{--var tabData = "{{route('dsa.visit.page.list')}}";--}}
        // var dailyCont = document.getElementById('pageChart').getContext('2d');
        // var labels = [];
        // var labData = [];
        var myChart = new Chart(document.getElementById('pageChart').getContext('2d'), {
            type: 'horizontalBar',
            data: {
                labels: [],
                datasets: [
                    {
                        label: '',
                        fillColor: "rgba(220,220,220,0.5)",
                        strokeColor: "rgba(220,220,220,0.8)",
                        highlightFill: "rgba(220,220,220,0.75)",
                        highlightStroke: "rgba(220,220,220,1)",
                        backgroundColor: 'rgb(80,140,255)',
                        borderColor: 'rgb(79,255,169)',
                        data: [],

                    },
                ],
            },
            options: {
                title: { //标题
                    display: true,
                    text: '页面访问统计',
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

        //更新数据
        function addData(d) {
            myChart.config.data.labels = d.labels;
            myChart.config.data.datasets[0].label = d.label;
            myChart.config.data.datasets[0].data = d.lab_data;
            myChart.config.options.title.text = '页面访问统计：' + d.ref_date;
            myChart.update();
        }

    </script>
@endsection
