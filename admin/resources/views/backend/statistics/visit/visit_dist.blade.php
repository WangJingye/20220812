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
                                        <label class="layui-form-label" style="width: 200px">最大查询时间为昨日</label>
                                        <div class="layui-inline">
                                            <input class="layui-input" id="searchDate" type="text" name="searchDate">
                                        </div>
                                    </div>
                                    {{--                                    <div class="layui-inline">--}}
                                    {{--                                        <label class="layui-form-label" style="width: 160px">请选择排序方式</label>--}}
                                    {{--                                        <div class="layui-inline">--}}
                                    {{--                                            <select name="searchSort" lay-verify="required">--}}
                                    {{--                                                <option value="" disabled="">请选择排序方式</option>--}}
                                    {{--                                                <option value="page_visit_pv" selected>访问次数(page_visit_pv)</option>--}}
                                    {{--                                                <option value="page_visit_uv">访问人数(page_visit_uv)</option>--}}
                                    {{--                                                <option value="page_staytime_pv">次均停留时长(page_staytime_pv)</option>--}}
                                    {{--                                                <option value="entrypage_pv">进入页次数(entrypage_pv)</option>--}}
                                    {{--                                                <option value="exitpage_pv">退出页次数(exitpage_pv)</option>--}}
                                    {{--                                                <option value="page_share_pv">转发次数(page_share_pv)</option>--}}
                                    {{--                                                <option value="page_share_uv">转发人数(page_share_uv)</option>--}}
                                    {{--                                            </select>--}}
                                    {{--                                        </div>--}}
                                    {{--                                    </div>--}}
                                    <span class="layui-inline layui-btn" id="search" lay-filter="search" lay-submit>搜索</span>

                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- 表单 --}}
                    {{--                    <div class="layui-card">--}}
                    {{--                        <div class="layui-card-body">--}}
                    {{--                            <div class="layui-inline">--}}
                    {{--                                <table id="page_list" lay-filter="page_list"></table>--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}

                    {{--图表--}}
                    <div class="layui-card">
                        <div class="layui-card-body">
                            <div class="layui-inline">
                                <canvas id="distAscChart" width=860 height=400></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="layui-card">
                        <div class="layui-card-body">
                            <div class="layui-inline">
                                <canvas id="distAsiChart" width=860 height=400></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="layui-card">
                        <div class="layui-card-body">
                            <div class="layui-inline">
                                <canvas id="distAdiChart" width=860 height=400></canvas>
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

                // table.render({
                //     elem: '#page_list'
                //     , height: 400
                //     , weight: 1600
                //     // , url: tabData //数据接口
                //     , page: false //开启分页
                //     , cols: [[ //表头
                //         {field: 'id', title: 'ID', width: 80, fixed: 'left'}
                //         , {field: 'page_path', width: 200, title: '页面路径'}
                //         , {field: 'page_visit_pv', title: '访问次数'}
                //         , {field: 'page_visit_uv', title: '访问人数'}
                //         , {field: 'page_staytime_pv', title: '次均停留时长'}
                //         , {field: 'entrypage_pv', title: '进入出页次数'}
                //         , {field: 'exitpage_pv', title: '退出页次数'}
                //         , {field: 'page_share_pv', title: '转发次数'}
                //         , {field: 'page_share_uv', title: '转发人数'}
                //     ]]
                //     , data: sd.data.list
                // });
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
        submitUrl = "{{route('dsa.visit.dist')}}";
        var ascChart = new Chart(document.getElementById('distAscChart').getContext('2d'), {
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
                    text: '访问来源分布access_source_session_cnt(访问最多前10)'
                },
            }
        });

        //访问时长分布
        var asiChart = new Chart(document.getElementById('distAsiChart').getContext('2d'), {
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
                    text: '访问时长分布access_staytime_info(访问时间最长前10)'
                },
            }
        });

        //访问深度的分布
        var adiChart = new Chart(document.getElementById('distAdiChart').getContext('2d'), {
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
                    text: '访问深度分布access_depth_info(访问深度最深前10)'
                },
            }
        });

        //更新数据
        function addData(d) {
            ascChart.config.data.labels = d.assc.labList;
            ascChart.config.data.datasets[0].data = d.assc.data;

            asiChart.config.data.labels = d.asi.labList;
            asiChart.config.data.datasets[0].data = d.asi.data;

            adiChart.config.data.labels = d.adi.labList;
            adiChart.config.data.datasets[0].data = d.adi.data;

            // ascChart.config.data.datasets[0].label = d.label;
            // ascChart.config.options.title.text = '页面访问统计：' + d.ref_date;
            ascChart.update();
            asiChart.update();
            adiChart.update();
        }

    </script>
@endsection
