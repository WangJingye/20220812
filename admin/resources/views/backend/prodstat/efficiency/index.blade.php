@extends('backend.base')
@section('content')
<div class="layui-tab" lay-filter="stat_order">
    <div class="layui-tab-content">
        <div class="layui-col-md12 ">
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
                        <canvas id="myChart" width=860 height=400></canvas>
                    </div>
                </div>
            </div>
    </div>
</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>

@section('script')
<script>
    @section('layui_script');
    layui.use(['table','jquery','layer'], function () {
        var table = layui.table;
        var $ = layui.jquery;
        var layer = layui.layer;
        var targetsValue=0;
        var labels=[], datas=[], nums=[];
        var myChart = document.getElementById('myChart').getContext('2d');

       //日期 选择框
        laydate.render({
            elem: '#searchDailyDate', //指定元素
            value: "{{date('Y-m-d',strtotime(date('Y-m-d').' -1 day'))}}",
            max: "{{date('Y-m-d',strtotime(date('Y-m-d').' -1 day'))}}"
        });

        //监听表单搜索
        form.on('submit(searchDaily)', function (input) {
            getDailyData(input.field);
            return false;//阻止表单跳转
        });
        //数据请求
        function getDailyData(subData){
            console.log('查询日期为----',subData)
            $.ajax({
                url: "{{route('dsb.prodstat.orderCount')}}",
                type: 'GET',
                data: subData,
                success: function (sd) {
                    if(sd.code==200){
                        valueData = sd.data.data
                        valueData.forEach((v,i)=>{
                            labels.push(v.days)
                            datas.push(v.total_money)
                        });
                        getChart(labels, datas, myChart) 
                    }
                }
            });
        }
        
        function getChart(label, data, eleCont){
            return new Chart(eleCont, {
                type: 'bar',
                data:{
                    labels: label,//底部标签栏
                    datasets : [
                        {
                            label: '转化率',
                            fillColor: "rgba(220,220,220,0.5)",
                            strokeColor: "rgba(220,220,220,0.8)",
                            highlightFill: "rgba(220,220,220,0.75)",
                            highlightStroke: "rgba(220,220,220,1)",
                            backgroundColor: 'rgb(80,140,255)',
                            borderColor: 'rgb(79,255,169)',
                            data: data
                        }
                    ],
                },
                options: {
                    title: { //标题
                        display: true,
                        text: '转化率',
                    },
                    legend: { //图例
                        display: true,
                        color: "rgba(151,164,176,0.14)",
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: false
                            }
                        }]
                    }
                }

            })
        }
        
        //初始调用
        getDailyData()  
    });
    
   
    @endsection
    </script>

























