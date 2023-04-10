@extends('backend.base')
@section('content')
    <div class="layui-tab" lay-filter="stat_order">
        <div class="layui-tab-content">
           <div class="layui-col-md12 ">
                    <div class="layui-card">
                        <div class="layui-card-body">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">销售目标</div>
                                <div class="layui-form-item">
                                    <div class="layui-inline layui-col-xs3 layui-col-sm3 layui-col-md2">
                                        <label >本周已完成</label>
                                        <div id="thisWeek"></div>
                                    </div>
                                    <div  class="layui-inline layui-col-xs3 layui-col-sm3 layui-col-md2">
                                        <label>本月目标</label>
                                        <div id="targets" style="color: blue;cursor: pointer;">设置目标</div>
                                        <!-- <input class="layui-input" id="targets" type="text" name="targets" id="targets">
                                        <span class="layui-inline layui-btn" id="searchDaily" lay-filter="searchDaily" lay-submit>搜索</span> -->
                                    </div>
                                    <div class="layui-inline layui-col-xs3 layui-col-sm3 layui-col-md2">
                                        <label>上周</label>
                                        <div id="lastweek"></div>
                                    </div>
                                    <div class="layui-inline layui-col-xs3 layui-col-sm3 layui-col-md2">
                                        <label>本月进度</label>
                                        <div id="rate"></div>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
            <div class="layui-tab-item layui-show">
                <div class="layui-col-md12">
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
        $('#targets').click(function(){
            layer.prompt({title: '请设置本月目标', formType: 0}, function(value, index){
                //点击确认的回调
                  targetsValue=value;
                  if(isNaN(targetsValue)){
                    layer.msg('请输入数字')
                    return
                  }
                  $.ajax({
                    url: "{{route('dsb.prodstat.setOrderRate')}}",
                    type: 'GET',
                    data: {'targets':targetsValue},
                    success: function (info) {
                        if(info.code== 200)
                        {
                            layer.close(index);
                            layer.msg('设置成功');
                            window.location.reload();
                        }
                    }
                });
                    
            });
        })

        $.ajax({
            url: "{{route('dsb.prodstat.orderCount')}}",
            type: 'GET',
            data: '',
            success: function (sd) {
                var labels=[],datas=[], nums=[];
                if(sd.code==200){
                    valueData = sd.data.data

                    valueData.forEach((v,i)=>{
                       labels.push(v.days)
                       datas.push(v.total_money)
                       nums.push(v.num)
                    });
                    document.getElementById('thisWeek').innerHTML = sd.data.thisWeek;
                    document.getElementById('lastweek').innerHTML = sd.data.lastWeek;
                    //document.getElementById('name1').value= sd.data.month;
                    document.getElementById('rate').innerHTML = sd.data.rate;
                    document.getElementById('targets').innerHTML = sd.data.targets+ '&nbsp;&nbsp;设置目标';

                }

            // 设置图表的数据
                var tempData = {
                    labels: labels,//底部标签栏
                    datasets :  
                        [

                        {
                            label: '订单数',
                            backgroundColor: [
                                'rgba(  255,69,0,0.2)'
                            ],
                            borderColor:'rgba(  255,69,0,0.2)',
                            //backgroundColor:'rgba(255,255,255,1)',
                            //fillColor: "rgba(151,187,205,0.5)",
                            //strokeColor: "rgba(151,187,205,0.8)",
                            //highlightFill: "rgba(151,187,205,0.75)",
                            //highlightStroke: "rgba(151,187,205,1)",
                            //highlight: "rgba(191,186,198,0.93)",
                            data: nums,
                            // Changes this dataset to become a line
                            type: 'line',
                            yAxisID: 'y-axis-2',
                        },
                        {
                            label: '销售额',
                            backgroundColor: [
                                'rgba(100,149,237,1)'
                            ],
                            borderColor:'rgba(100,149,237,0.8)',
                            backgroundColor:'rgba(100,149,237,0.8)',
                            fillColor: "rgba(151,187,205,0.5)",
                            strokeColor: "rgba(151,187,205,0.8)",
                            highlightFill: "rgba(151,187,205,0.75)",
                            highlightStroke: "rgba(151,187,205,1)",
                            highlight: "rgba(191,186,198,0.93)",
                           // bar: "inner",
                            data: datas,
                            yAxisID: 'y-axis-1',
                        }
    
                    ],
                    };
                    var optionData = {
                        responsive: true,
                        title: {
                            display: true,
                            text: '销售走势'
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: true
                        },
                        scales: {
                            yAxes: [{
                                type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                                display: true,
                                position: 'left',
                                id: 'y-axis-1',
                            }, {
                                type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                                display: true,
                                position: 'right',
                                id: 'y-axis-2',
                                gridLines: {
                                    drawOnChartArea: false
                                }
                            }],
                        }
                    }
                // 获取所选canvas元素的内容
                // 
               //var ctx = document.getElementById('myChart');
                var ctx = document.getElementById('myChart').getContext('2d');
                //var ctx = $('#myChart');
                //var ctx = 'myChart';
                //var ctx = document.getElementById("myChart");
                //设置图表高度与宽度
                ctx.height=400;
                ctx.width=860;
                // 初始化一个新的柱状图
                var myLineChart = new Chart(ctx, {
                    type: 'bar', //想要什么图就在这里改,甜甜圈、折线图、圆之类的可以参照官网
                    data: tempData,
                    options: optionData
                });
            }
        });
    });
   
    @endsection
    </script>

























