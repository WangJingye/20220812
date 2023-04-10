layui.use(['layer', 'form', 'jquery'], function() {
    var layer = layui.layer;
	var form = layui.form;
    var $ = layui.jquery;
    var dataUrl='';
    var subData='';
    var searchLabel=[],searchData=[],visitLabel=[],visitData=[],shareLabel=[],shareData=[],collectLabel=[],collectData=[],
        addLabel=[],addData=[],categoryLabel=[],categoryData=[]
    var searchChart=document.getElementById('searchChart').getContext('2d');
    var transRateChart=document.getElementById('transRateChart').getContext('2d');
    var transRateChart2=document.getElementById('transRateChart2').getContext('2d');
    var goodsVisitChart=document.getElementById('goodsVisitChart').getContext('2d');
    var goodsShareChart=document.getElementById('goodsShareChart').getContext('2d');
    var goodsCollectChart=document.getElementById('goodsCollectChart').getContext('2d');
    var goodsAddChart=document.getElementById('goodsAddChart').getContext('2d');
    var goodsCategoryChart=document.getElementById('goodsCategoryChart').getContext('2d');

    //数据请求
    $.ajax({
        url: 'collects',
        type: 'GET',
        data: subData,
        success: function (sd) {
            //赋值
            if(sd.code==200){
                console.log(sd.data)
               var _searchData=sd.data.searchData//搜索
               var _visitData=sd.data.goodsVisitData//商品访问
               var _goodsShareData=sd.data.goodsShareData//商品分享
               var _goodsCollectData=sd.data.goodsCollectData//商品分享
               var _goodsAddData=sd.data.goodsAddData//商品分享
               var _goodsCategoryData=sd.data.goodsCategoryData//商品分享
               var _lastDay = sd.data.lastDay//昨日日期

               $('#yesterdayDate').html(sd.data.title)

               $('#pvNum').html(sd.data.lists.visit_pv)
               $('#uvNum').html(sd.data.lists.visit_uv)

               //搜索
               if(_searchData.length==0){
                   $('#searchArea').hide()
                   $('#searchNoData').html('暂无搜索数据')
               }else{
                    _searchData.forEach(v=>{
                        searchLabel.push(v.keyword)
                        searchData.push(v.count)
                    })
                    getSearchChart(searchLabel, searchData, searchChart)
               }
               
                //商品
                if(_visitData.length==0){
                    $('#goodsVisit').hide()
                    $('#visitNoData').html('暂无商品访问数据')
                }else{
                    _visitData.forEach(v=>{
                        visitLabel.push(v.pdtId)
                        visitData.push(v.scores)
                   })
                    getGoodsChart(visitLabel,visitData,goodsVisitChart,'商品访问次数','商品访问Top3')
                }

                if(_goodsShareData.length==0){
                    $('#goodsShare').hide()
                    $('#shareNoData').html('暂无商品分享数据')
                }else{
                    _goodsShareData.forEach(v=>{
                        shareLabel.push(v.pdtId)
                        shareData.push(v.scores)
                   })
                    getGoodsChart(shareLabel,shareData,goodsShareChart,'商品分享次数','商品分享Top3')
                }
              
                if(_goodsCollectData.length==0){
                    $('#goodsCollect').hide()
                    $('#collectNoData').html('暂无商品收藏数据')
                }else{
                    _goodsCollectData.forEach(v=>{
                        collectLabel.push(v.pdtId)
                        collectData.push(v.scores)
                   })
                    getGoodsChart(collectLabel,collectData,goodsCollectChart,'商品收藏次数','商品收藏Top3')
                }

                if(_goodsAddData.length==0){
                    $('#goodsAdd').hide()
                    $('#addNoData').html('暂无商品加购数据')
                }else{
                    _goodsAddData.forEach(v=>{
                        addLabel.push(v.pdtId)
                        addData.push(v.scores)
                    })
                    getGoodsChart(addLabel,addData,goodsAddChart,'商品加购次数','商品加购Top3')
                   
                }
               
                if(_goodsCategoryData.length==0){
                    $('#goodsCategory').hide()
                    $('#categoryNoData').html('暂无商品类别访问数据')
                }else{
                    _goodsCategoryData.forEach(v=>{
                        categoryLabel.push(v.typeName)
                        categoryData.push(v.count)
                    })
                     getGoodsChart(categoryLabel,categoryData,goodsCategoryChart,'商品类别访问次数','商品类别访问Top3')
                } 
               //转化率
               getTransRateChart(dataTrans(sd.data.rates.rate1),transRateChart,'购买转化率')
               getTransRateChart(dataTrans(sd.data.rates.rate2),transRateChart2,'加购转化率')
              
               //数据转换
               function dataTrans(data){
                    var _data=parseInt(data)
                    var dataArr=[]
                    dataArr[0]=_data
                    dataArr[1]=Number(100-_data)
                    return dataArr
               }
            }
        }
    });

    //搜索前十图表
    function getSearchChart(label, data, eleCont){
        return new Chart(eleCont, {
            type: 'horizontalBar',
            data:{
                labels: label,//底部标签栏
                datasets : [
                    {
                        label: '搜索次数',
                        fillColor: "rgba(220,220,220,0.5)",
                        strokeColor: "rgba(220,220,220,0.8)",
                        highlightFill: "rgba(220,220,220,0.75)",
                        highlightStroke: "rgba(220,220,220,1)",
                        backgroundColor: 'rgb(75,192,192,0.8)',
                        borderColor: 'rgb(79,255,169,0.5)',
                        data: data
                    }
                ],
            },
            options: {
                title: { //标题
                    display: true,
                    text: '搜索次数Top 10',
                },
                legend: { //图例
                    display: false,
                    color: "rgba(151,164,176,0.14)",
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: false
                        }
                    }]
                },
                tooltips: false,
                hover: {
                    animationDuration: 0  // 防止鼠标移上去，数字闪烁
                },
                animation: {           // 这部分是数值显示的功能实现
                    onComplete: function () {
                        var chartInstance = this.chart,
                        ctx = chartInstance.ctx;
                        // 以下属于canvas的属性（font、fillStyle、textAlign...）
                        ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                        ctx.fillStyle = "#666";
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';
                        this.data.datasets.forEach(function (dataset, i) {
                            var meta = chartInstance.controller.getDatasetMeta(i);
                            meta.data.forEach(function (bar, index) {
                                var data = dataset.data[index];
                                ctx.fillText(data, bar._model.x+5 , bar._model.y+6);
                            });
                        });
                    }
                }
            }
        })
    }

    //转化率
    function getTransRateChart(data,eleCont,text){
        return new Chart(eleCont, {
            type: 'doughnut',
			data: {
				datasets: [{
					data: data,
					backgroundColor: ['rgba(219,73,73,0.8)','rgba(234,225,225,1)'],
					label: ''
				}],
				labels: ''
			},
			options: {
				responsive: true,
				legend: {
					position: 'top',
				},
				title: {
					display: true,
					text: text
				},
				animation: {
					animateScale: true,
					animateRotate: true
                },
                cutoutPercentage: 70,
                tooltips: false,
                hover: {
                    animationDuration: 0  // 防止鼠标移上去，数字闪烁
                },
                animation: {           // 这部分是数值显示的功能实现
                    onComplete: function () {
                        var chartInstance = this.chart,
                        ctx = chartInstance.ctx;
                        // 以下属于canvas的属性（font、fillStyle、textAlign...）
                        ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                        ctx.fillStyle = "#666";
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';
                        this.data.datasets.forEach(function (dataset, i) {
                            var meta = chartInstance.controller.getDatasetMeta(i);
                            meta.data.forEach(function (bar, index) {
                                var data = dataset.data[0];
                                ctx.fillText(data+'%', bar._model.x, bar._model.y);
                            });
                        });
                    }
                }
			}
        })
    }

    //商品图标
    function getGoodsChart(label, data, eleCont,labelTip,title){
        return new Chart(eleCont, {
            type: 'bar',
            data:{
                labels: label,//底部标签栏
                datasets : [
                    {
                        label: labelTip,
                        fillColor: "rgba(220,220,220,0.5)",
                        strokeColor: "rgba(220,220,220,0.8)",
                        highlightFill: "rgba(220,220,220,0.75)",
                        highlightStroke: "rgba(220,220,220,1)",
                        backgroundColor: 'rgb(80,140,255,0.8)',
                        borderColor: 'rgb(79,255,169,0.5)',
                        data: data
                    }
                ],
            },
            options: {
                title: { //标题
                    display: true,
                    text: title,
                    padding: 30
                },
                legend: { //图例
                    display: false,
                    color: "rgba(151,164,176,0.14)",
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: false
                        }
                    }]
                },
                tooltips: false,
                hover: {
                    animationDuration: 0  // 防止鼠标移上去，数字闪烁
                },
                animation: {           // 这部分是数值显示的功能实现
                    onComplete: function () {
                        var chartInstance = this.chart,
                        ctx = chartInstance.ctx;
                        // 以下属于canvas的属性（font、fillStyle、textAlign...）
                        ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                        ctx.fillStyle = "#666";
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';
                        this.data.datasets.forEach(function (dataset, i) {
                            var meta = chartInstance.controller.getDatasetMeta(i);
                            meta.data.forEach(function (bar, index) {
                                var data = dataset.data[index];
                                ctx.fillText(data, bar._model.x, bar._model.y-5);
                            });
                        });
                    }
                }
            }

        })
    }

    //修改title名
    $('.more').click(function(){
        var title = $(this)[0].dataset.name
        $(this).html(title)
    })
})