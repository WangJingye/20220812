<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>ECharts</title>
    <link rel="stylesheet" type="text/css" href="{{ url('/static/admin/dashboard/scss/reset.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ url('/static/admin/dashboard/scss/index.css')}}?t=21" />
    <script src="https://cdn.bootcss.com/jquery/3.4.1/jquery.min.js"></script>
    <!-- 引入 echarts.js -->
    <script src="{{ url('/static/admin/dashboard/echarts.min.js')}}"></script>
    <script src="{{ url('/static/admin/dashboard/main.js?t=21')}}"></script>
    <script>
        function getOrderReportData(res, callback) {
            let _parame=res.parame
            console.log('order_parame',_parame)
            $.post("{{url('admin/dashboard/analysis/getOrderReportData')}}",_parame, function(data) {
                callback && callback(data)
            });

        }
        function getDashboardRequest(res,callback) {
            let _parame=res.parame
            console.log('allData_parame',_parame)
            $.post("{{url('admin/dashboard/allData')}}",_parame, function(data) {
                callback && callback(data)
            });
        }
    </script>
</head>

<body>
<!-- 商城概览 -->
<div class="shop-area">
    <div class="title">
        <div class="left-title">
            <div class="icon"></div>
            <h1>商城概览</h1>
        </div>
        <div class="nav" id="shopChangeTime">
            <p class="cur">累计</p>
            <p>昨天</p>
            <p>近7天</p>
            <p>近1个月</p>
        </div>
    </div>
    <ul class="sale" id="shopSale">
        <li>
            <p class="desc">总的销售额</p>
            <p class="price">暂无</p>
        </li>
        <li>
            <p class="desc">平均订单金额（客单价）</p>
            <p class="price">暂无</p>
        </li>
        <li>
            <p class="desc">总的成交订单量</p>
            <p class="price">暂无</p>
        </li>
        <li>
            <p class="desc">总的购买人数</p>
            <p class="price">暂无</p>
        </li>
    </ul>
</div>
<!-- 订单概览 -->
<div class="shop-area">
    <div class="title">
        <div class="left-title">
            <div class="icon"></div>
            <h1>订单概览</h1>
        </div>
        <div class="nav" id="orderChangeTime">
            <p class="cur">昨天</p>
            <p>近7天</p>
            <p>近1个月</p>
        </div>
    </div>
    <ul class="sale" id="orderSale">
        <li>
            <p class="desc">创建订单量</p>
            <p class="price">暂无</p>
        </li>
        <li>
            <p class="desc">支付订单量</p>
            <p class="price">暂无</p>
        </li>
        <li>
            <p class="desc">发货订单量</p>
            <p class="price">暂无</p>
        </li>
        <li>
            <p class="desc">签收订单量</p>
            <p class="price">暂无</p>
        </li>
    </ul>
</div>
<!-- 销售数据 -->
<div class="shop-area sales-data">
    <div class="title">
        <div class="left-title">
            <div class="icon"></div>
            <h1>销售数据</h1>
        </div>
        <div class="nav" id="salesChangeTime">
            <p class="cur">昨天</p>
            <p>近7天</p>
            <p>近1个月</p>
        </div>
    </div>
    <ul class="pie-chart">
        <li class="pie-li" id="onlineSales">
            <h2>线上商城</h2>
            <div class="pie-summary">
                <div class="pie">
                    <div id="onlineShopping" style=" width: 258px;height: 258px;"></div>
                    <ol class="nav" id="onlineNav">
                    </ol>
                </div>
                <ol class="summary" id="summaryOnlineSales">
                    <li class="sum-li">
                        <p class="desc">新老客访问订单占比率</p>
                        <p class="ratio">暂无</p>
                    </li>
                    <li class="sum-li">
                        <p class="desc">老客的复购率</p>
                        <p class="ratio">暂无</p>
                    </li>
                </ol>
            </div>
            <div class="ranking-list">
                <ol class="ranking-nav" id="onlineRankingnav">
                    <li class="ranking-nav-li" style="display: none;">明星系列销售</li>
                    <li class="ranking-nav-li cur">商品销量Top5</li>
                </ol>
                <div class="table" id="onlineTable"></div>
            </div>
        </li>
        <li class="pie-li">
            <h2>线下销售</h2>
            <div class="pie-summary">
                <div class="pie">
                    <div id="offlineSales" style=" width: 258px;height: 258px;"></div>
                    <ol class="nav" id="offlineNav"></ol>
                </div>
            </div>
            <div class="ranking-list">
                <ol class="ranking-nav" id="offlineRankingnav">
                    <li class="ranking-nav-li " style="display: none;">明星系列销售</li>
                    <li class="ranking-nav-li cur">商品销量Top5</li>
                    <li class="ranking-nav-li">BA销售额Top5</li>
                    <li class="ranking-nav-li">BA所属专卖店Top5</li>
                    <li class="ranking-nav-li">BA所属城市Top5</li>
                </ol>
                <div class="table" id="offlineTable"></div>
            </div>
        </li>
        <li class="pie-li">
            <h2>O2O销售</h2>
            <div class="pie-summary">
                <div class="pie">
                    <div id="o2oSales" style=" width: 258px;height: 258px;"></div>
                    <ol class="nav" id="o2oNav"></ol>
                </div>
            </div>
            <div class="ranking-list">
                <ol class="ranking-nav" id="o2oRankingnav">
                    <li class="ranking-nav-li " style="display: none;">明星系列销售</li>
                    <li class="ranking-nav-li cur">商品销量Top5</li>
                    <li class="ranking-nav-li">BA销售额Top5</li>
                    <li class="ranking-nav-li">BA所属专卖店Top5</li>
                    <li class="ranking-nav-li">BA所属城市Top5</li>
                </ol>
                <div class="table" id="o2oTable"></div>
            </div>
        </li>
    </ul>
</div>
<!-- 会员数据 -->
<div class="shop-area membership-data">
    <div class="title">
        <div class="left-title">
            <div class="icon"></div>
            <h1>会员数据</h1>
        </div>
        <div class="nav" id="memberChangeTime">
            <p class="cur">累计</p>
            <p>昨天</p>
            <p>近7天</p>
            <p>近1个月</p>
        </div>
    </div>
    <ul class="sale" id="memberSale">
        <li>
            <p class="desc">小程序新会员注册数</p>
            <p class="price">暂无</p>
        </li>
        <li>
            <p class="desc">H5新会员注册数</p>
            <p class="price">暂无</p>
        </li>
        <li>
            <p class="desc">Pc新会员注册数</p>
            <p class="price">暂无</p>
        </li>
        <li>
            <p class="desc">用户分享招募的会员数量</p>
            <p class="price">暂无</p>
        </li>
        <li>
            <p class="desc">BA分享招募的会员数量</p>
            <p class="price">暂无</p>
        </li>
    </ul>
    <ul class="circular-chart">
        <li class="circular-li">
            <h3>新会员注册渠道来源占比</h3>
            <div class="circular-pie">
                <div id="newMember" style="width: 258px;height: 258px;"></div>
                <ol class="nav" id="newMemberNav"></ol>
            </div>
        </li>
        <li class="circular-li">
            <h3>登录总人数占比</h3>
            <div class="circular-pie">
                <div id="signIin" style="width: 258px;height: 258px;"></div>
                <ol class="nav" id="signIinNav">
                    <!-- <li class="nav-li">
                        <div class="nav-icon"></div>
                        <div class="con">
                            <span>小程序</span>
                            <span>893</span>
                        </div>
                    </li>
                    <li class="nav-li">
                        <div class="nav-icon"></div>
                        <div class="con">
                            <span>H5</span>
                            <span>389</span>
                        </div>
                    </li>
                    <li class="nav-li">
                        <div class="nav-icon"></div>
                        <div class="con">
                            <span>PC</span>
                            <span>267</span>
                        </div>
                    </li> -->
                </ol>
            </div>
        </li>
    </ul>
</div>
<!-- 商品数据 -->
<div class="shop-area commodity-data">
    <div class="title">
        <div class="left-title">
            <div class="icon"></div>
            <h1>商品数据</h1>
        </div>
        <div class="nav" id="commodityChangeTime">
            <p class="cur">累计</p>
            <p>昨天</p>
            <p>近7天</p>
            <p>近1个月</p>
        </div>
    </div>
    <div class="statistical-chart">
        <ul class="statistical-ul">
            <li class="statistical-li">
                <h3>商品访问Top3</h3>
                <div id="productVisit" style="width:324px;height:300px;"></div>
                @can('miniapp.prodstat.view')
                    <a class="more"  data-name="商品访问次数" href="<?php echo e(route('dsb.prodstat.view')); ?>">查看更多</a>
                @endcan

            </li>
            <li class="statistical-li">
                <h3>商品分享Top3</h3>
                <div id="commoditySharing" style="width:324px;height:300px;"></div>
                @can('miniapp.prodstat.share')
                    <a class="more" data-name="商品分享次数" href="<?php echo e(route('dsb.prodstat.share')); ?>">查看更多</a>
                @endcan
            </li>
            <li class="statistical-li">
                <h3>商品类别访问Top3</h3>
                <div id="productCategories" style="width:324px;height:300px;"></div>
                @can('miniapp.prodstat.prodtypeview')
                    <a class="more" data-name="商品类别访问次数" href="<?php echo e(route('dsb.prodstat.prodtypeview')); ?>">查看更多</a>
                @endcan

            </li>
        </ul>
        <div id="tip" class="tip-name hide"></div>
    </div>
    <div class="reverse-statistical-chart">
        <h2>商品名称搜索次数Top10</h2>
        <div id="productSearch" style="width: 100%;height:345px;"></div>
    </div>
</div>
<!-- 小程序数据 -->
<div class="shop-area applet-data">
    <div class="title">
        <div class="left-title">
            <div class="icon"></div>
            <h1>小程序数据</h1>
        </div>
        <div class="nav" id="appletChangeTime">
            <p class="cur">累计</p>
            <p>昨天</p>
            <p>近7天</p>
            <p>近1个月</p>
        </div>
    </div>
    <ul class="sale" id="miniappSale">
        <li>
            <p class="desc">PV</p>
            <p class="price">暂无</p>
        </li>
        <li>
            <p class="desc">UV</p>
            <p class="price">暂无</p>
        </li>
        <li>
            <p class="desc">人均访问时长</p>
            <p class="price">暂无</p>
        </li>
    </ul>
    <h2>页面访问次数Top10</h2>
    <div class="page-num">
        <div id="pageVisits" style="flex-grow: 1;height: 349px;"></div>
        <div class="ladder" id="ladder">
            <div class="area">
                <div class="block"></div>
                <div class="arrow">
                    <img src="{{ url('/static/admin/dashboard/images/down.png')}}" />
                    <span></span>
                </div>
            </div>
            <div class="area">
                <div class="block"></div>
                <div class="arrow">
                    <img src="{{ url('/static/admin/dashboard/images/down.png')}}" />
                    <span></span>
                </div>
            </div>
            <div class="area">
                <div class="block"></div>
                <div class="arrow">
                    <img src="{{ url('/static/admin/dashboard/images/down.png')}}" />
                    <span></span>
                </div>
            </div>
            <div class="area">
                <div class="block"></div>
            </div>
        </div>
    </div>

</div>
</body>
</html>