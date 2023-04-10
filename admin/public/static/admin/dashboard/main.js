window.onload = function () {
    //小季接口全局变量声明
    let _orderReportData = {}
    let onlineContent = '商品销量Top5'
    let offlineContent = '商品销量Top5'
    let o2oContent = '商品销量Top5'
    //挖空圆饼图
    // 线上商城
    let myOnlineShopping = echarts.init(document.getElementById('onlineShopping'));
    myOnlineShopping.setOption({
        title: [
            {
                text: '',
                x: 'center',
                y: 'center',
                textStyle: {
                    color: '#121212',
                    fontSize: 24,
                    fontWeight: 'normal'
                }
            }, {
                subtext: '总销售金额',
                x: '50%',
                y: '50%',
                textAlign: 'center',
                subtextStyle: {
                    color: '#666',
                    fontSize: 12
                }
            }
        ],
        color: ['#7585a2', '#8797b5', '#9faabe', '#61769d'],
        series: [
            {
                type: 'pie',
                radius: ['100%', '55%'],
                // data: [
                //     {
                //         name: '1800',
                //         value: 1800
                //     }, {
                //         name: '2400',
                //         value: 2400
                //     }, {
                //         name: '5500',
                //         value: 5500
                //     }, {
                //         name: '4800',
                //         value: 4800
                //     }
                // ],
                data: [],
                label: {
                    show: true,
                    position: 'inside'
                },
                animation: false,
                itemStyle: {
                    normal: {
                        borderWidth: 1,
                        borderColor: '#fff'
                    }
                }
            }
        ]
    });
    // 线下销售
    let myOfflineSales = echarts.init(document.getElementById('offlineSales'));
    myOfflineSales.setOption({
        title: [
            {
                text: '',
                x: 'center',
                y: 'center',
                textStyle: {
                    color: '#121212',
                    fontSize: 24,
                    fontWeight: 'normal'
                }
            }, {
                subtext: '总销售金额',
                x: '50%',
                y: '50%',
                textAlign: 'center',
                subtextStyle: {
                    color: '#666',
                    fontSize: 12
                }
            }
        ],
        color: ['#7585a2', '#8797b5', '#9faabe', '#61769d'],
        series: [
            {
                type: 'pie',
                radius: ['100%', '55%'],
                data: [],
                label: {
                    show: true,
                    position: 'inside'
                },
                animation: false,
                itemStyle: {
                    normal: {
                        borderWidth: 1,
                        borderColor: '#fff'
                    }
                }
            }
        ]
    });
    //O2O销售
    let myO2oSales = echarts.init(document.getElementById('o2oSales'));
    myO2oSales.setOption({
        title: [
            {
                text: '',
                x: 'center',
                y: 'center',
                textStyle: {
                    color: '#121212',
                    fontSize: 24,
                    fontWeight: 'normal'
                }
            }, {
                subtext: '总销售金额',
                x: '50%',
                y: '50%',
                textAlign: 'center',
                subtextStyle: {
                    color: '#666',
                    fontSize: 12
                }
            }
        ],
        color: ['#7585a2', '#8797b5', '#9faabe', '#61769d'],
        series: [
            {
                type: 'pie',
                radius: ['100%', '55%'],
                // data: [
                //     {
                //         name: '1800',
                //         value: 1800
                //     }, {
                //         name: '2400',
                //         value: 2400
                //     }, {
                //         name: '5500',
                //         value: 5500
                //     }, {
                //         name: '4800',
                //         value: 4800
                //     }
                // ],
                data: [],
                label: {
                    show: true,
                    position: 'inside'
                },
                animation: false,
                itemStyle: {
                    normal: {
                        borderWidth: 1,
                        borderColor: '#fff'
                    }
                }
            }
        ]
    });
    //新会员注册渠道来源占比
    let myNewMember = echarts.init(document.getElementById('newMember'));
    myNewMember.setOption({
        color: ['#7585a2', '#8797b5', '#9faabe', '#61769d'],
        series: [
            {
                type: 'pie',
                radius: '100%',
                data: [],
                label: {
                    show: true,
                    position: 'inside'
                },
                animation: false,
                itemStyle: {
                    normal: {
                        borderWidth: 1,
                        borderColor: '#fff'
                    }
                }
            }
        ],
    });
    //登录总人数占比
    let mySignIin = echarts.init(document.getElementById('signIin'));
    mySignIin.setOption({
        color: ['#7585a2', '#8797b5', '#9faabe', '#61769d'],
        series: [
            {
                type: 'pie',
                radius: '100%',
                data: [],
                label: {
                    show: true,
                    position: 'inside'
                },
                animation: false,
                itemStyle: {
                    normal: {
                        borderWidth: 1,
                        borderColor: '#fff'
                    }
                }
            }
        ],
    });
    //条形统计图
    //商品访问Top3
    let myProductVisit = echarts.init(document.getElementById('productVisit'));
    myProductVisit.setOption({
        xAxis: {
            type: 'category',
            data: [],
            axisTick: {
                show: false
            },
            axisLine: {
                lineStyle: {
                    color: "#989898"
                }
            },
            axisLabel: {
                show: true,
                interval: 0,
                formatter: function (value) {
                    return (value.length > 5 ? (value.slice(0, 5) + "...") : value)
                }
            },
            triggerEvent: true // 设置为true后，可触发事件。实现x轴文字过长，显示省略号，hover上去显示全部的功能
        },
        yAxis: {
            type: 'value',
            axisTick: {
                show: false
            },
            axisLine: {
                lineStyle: {
                    color: "#989898"
                }
            }
        },
        color: ['#7585a2'],
        series: [{
            data: [],
            type: 'bar',
            barWidth: 24,
            label: {
                show: true,
                position: 'top'
            },
        }],
        animation: false,
    });
    //商品分享Top3
    let myCommoditySharing = echarts.init(document.getElementById('commoditySharing'));
    myCommoditySharing.setOption({
        xAxis: {
            type: 'category',
            data: [],
            axisTick: {
                show: false
            },
            axisLine: {
                lineStyle: {
                    color: "#989898"
                }
            },
            axisLabel: {
                show: true,
                interval: 0,
                formatter: function (value) {
                    return (value.length > 5 ? (value.slice(0, 5) + "...") : value)
                }
            },
            triggerEvent: true // 设置为true后，可触发事件。实现x轴文字过长，显示省略号，hover上去显示全部的功能
        },
        yAxis: {
            type: 'value',
            axisTick: {
                show: false
            },
            axisLine: {
                lineStyle: {
                    color: "#989898"
                }
            }
        },
        color: ['#7585a2'],
        series: [{
            data: [],
            type: 'bar',
            barWidth: 24,
            label: {
                show: true,
                position: 'top'
            },
        }],
        animation: false,
    });
    //商品类别访问Top3
    let myProductCategories = echarts.init(document.getElementById('productCategories'));
    myProductCategories.setOption({
        xAxis: {
            type: 'category',
            data: [],
            axisTick: {
                show: false
            },
            axisLine: {
                lineStyle: {
                    color: "#989898"
                }
            },
            axisLabel: {
                show: true,
                interval: 0,
                formatter: function (value) {
                    return (value.length > 5 ? (value.slice(0, 5) + "...") : value)
                }
            },
            triggerEvent: true // 设置为true后，可触发事件。实现x轴文字过长，显示省略号，hover上去显示全部的功能
        },
        yAxis: {
            type: 'value',
            axisTick: {
                show: false
            },
            axisLine: {
                lineStyle: {
                    color: "#989898"
                }
            }
        },
        color: ['#7585a2'],
        series: [{
            data: [],
            type: 'bar',
            barWidth: 24,
            label: {
                show: true,
                position: 'top'
            },
        }],
        animation: false,
    });
    // 倒条形统计图
    // 商品名称搜索次数Top10
    let myProductSearch = echarts.init(document.getElementById('productSearch'));
    myProductSearch.setOption({
        grid: {
            show: true,
            containLabel: true,
            x: 0,
            y: 0,
            x2: 80,
            y2: 0,
            borderColor: '#6b778b'
        },
        xAxis: {
            type: 'value',
            axisTick: {
                show: false
            },
            axisLabel: {
                show: false,
            },
            axisLabel: {
                show: true,
                interval: 0,
                formatter: function (value) {
                    return (value.length > 5 ? (value.slice(0, 5) + "...") : value)
                }
            },
            triggerEvent: true // 设置为true后，可触发事件。实现x轴文字过长，显示省略号，hover上去显示全部的功能
        },
        yAxis: {
            type: 'category',
            data: [],
            axisTick: {
                show: false
            },
            axisLine: {
                lineStyle: {
                    color: "#989898"
                }
            }
        },
        color: ['#7585a2'],
        series: [{
            type: 'bar',
            data: [],
            barWidth: 19,
            label: {
                show: true,
                position: 'insideLeft',
            },
        }],
        animation: false,
    });
    //页面访问次数Top10
    let myPageVisits = echarts.init(document.getElementById('pageVisits'));
    myPageVisits.setOption({
        grid: {
            show: true,
            containLabel: true,
            x: 0,
            y: 0,
            x2: 0,
            y2: 0,
            borderColor: '#6b778b'
        },
        xAxis: {
            type: 'value',
            axisTick: {
                show: false
            },
            axisLabel: {
                show: false,
            },
        },
        yAxis: {
            type: 'category',
            data: [],
            axisTick: {
                show: false
            },
            axisLine: {
                lineStyle: {
                    color: "#989898"
                }
            }
        },
        color: ['#7585a2'],
        series: [{
            type: 'bar',
            data: [],
            barWidth: 19,
            label: {
                show: true,
                position: 'insideLeft',
            },
        }],
        animation: false,
    });
    /*页面初始事件*/
    setTimeout(function () {
        window.onresize = function () {
            myOnlineShopping.resize();
            myOfflineSales.resize();
            myO2oSales.resize();
            myNewMember.resize();
            mySignIin.resize();
            myProductVisit.resize();
            myCommoditySharing.resize();
            myProductCategories.resize();
            myProductSearch.resize();
            myPageVisits.resize();
        }
    }, 200)
    // 商城概览
    orderReporOverview({
        viewType: '1',
        dateType: '4',
    })
    // 订单概览
    orderReporOverview({
        viewType: '2',
        dateType: '1',
    })
    // 销售数据
    orderReporOverview({
        viewType: '3',
        dateType: '1',
    })
    // 会员数据
    DashboarReporOverview({
        viewType: '1',
        dateType: '4',
    })
    // 商品数据
    DashboarReporOverview({
        viewType: '2',
        dateType: '4',
    })
    // 小程序数据
    DashboarReporOverview({
        viewType: '3',
        dateType: '4',
    })

    /*逻辑事件*/

    // 统计图鼠标移上
    function echartTip(params) {
        if (params.componentType == 'xAxis') {
            let tt = $('#tip');
            tt.html(params.value);
            tt.css('left', params.event.event.x - 20);
            tt.css('bottom', 0);
            tt.show();
        }
    }

    // 商品排行榜
    function rankingTable(name, _arr) {
        console.log(name + '排行榜', _arr)
        let _htmls = ''
        _htmls = `
        <ol class="tab-row">
            <li class="tab-row-li serial-number">序号</li>
            <li class="tab-row-li serial-name">系列名称</li>
            <li class="tab-row-li sales-volume">销售金额</li>
        </ol>`
        _arr.forEach((v, i) => {
            let _index = i + 1
            _htmls += `<ol class="tab-row">
                <li class="tab-row-li serial-number">${_index++}</li>
                <li class="tab-row-li serial-name">${v.name || v.guide_name}</li>
                <li class="tab-row-li sales-volume">${v.amount}</li>
            </ol>`
        });
        if (name == 'online') {
            $("#onlineTable").html(_htmls);
        } else if (name == 'offline') {
            $("#offlineTable").html(_htmls);
        } else if (name == 'o2o') {
            $("#o2oTable").html(_htmls);
        }
    }

    // 累计昨天近7天近1个月
    function getTime(_viewType, _content, type) {
        let _id = 4
        switch (_content) {
            case '累计':
                _id = 4
                break;
            case '近1个月':
                _id = 3
                break;
            case '近7天':
                _id = 2
                break;
            case '昨天':
                _id = 1
                break;
        }
        let _parame = {
            viewType: _viewType,
            dateType: String(_id)
        }
        if (type == 'order') {
            orderReporOverview(_parame)
        } else if (type == 'dashboard') {
            DashboarReporOverview(_parame)
        }

    }

    /*页面交互*/
    // 统计图鼠标移上效果
    myProductVisit.on('mouseover', function (params) {
        echartTip(params)
    });
    myProductVisit.on('mouseout', function (params) {
        $('#tip').hide();
    })
    myCommoditySharing.on('mouseover', function (params) {
        echartTip(params)
    });
    myCommoditySharing.on('mouseout', function (params) {
        $('#tip').hide();
    })
    myProductCategories.on('mouseover', function (params) {
        echartTip(params)
    });
    myProductCategories.on('mouseout', function (params) {
        $('#tip').hide();
    })
    // 商城概览时间切换
    $('#shopChangeTime p').click(function (event) {
        $(this).addClass('cur');
        $(this).siblings().removeClass('cur');
        let _content = $(this).html()
        getTime('1', _content, 'order')
    })
    // 订单概览时间切换
    $('#orderChangeTime p').click(function (event) {
        $(this).addClass('cur');
        $(this).siblings().removeClass('cur');
        let _content = $(this).html()
        getTime('2', _content, 'order')
    })
    //销售数据时间切换
    $('#salesChangeTime p').click(function (event) {
        $(this).addClass('cur');
        $(this).siblings().removeClass('cur');
        let _content = $(this).html()
        getTime('3', _content, 'order')
    })
    // 会员数据时间切换
    $('#memberChangeTime p').click(function (event) {
        $(this).addClass('cur');
        $(this).siblings().removeClass('cur');
        let _content = $(this).html()
        getTime('1', _content, 'dashboard')
    })
    // 商品数据时间切换
    $('#commodityChangeTime p').click(function (event) {
        $(this).addClass('cur');
        $(this).siblings().removeClass('cur');
        let _content = $(this).html()
        getTime('2', _content, 'dashboard')
    })
    // 小程序数据时间切换
    $('#appletChangeTime p').click(function (event) {
        $(this).addClass('cur');
        $(this).siblings().removeClass('cur');
        let _content = $(this).html()
        getTime('3', _content, 'dashboard')
    })
    // 线上商品排行榜切换
    $('#onlineRankingnav li').click(function (event) {
        $(this).addClass('cur');
        $(this).siblings().removeClass('cur');
        $('#onlineTable').empty();
        let _content = $(this).html()
        onlineContent = _content
        getOnlineList(_content)
    })

    function getOnlineList(_content) {
        let _arr = null
        switch (_content) {
            case '明星系列销售':
                _arr = _orderReportData.onlineStarTotalSales
                break;
            case '商品销量Top5':
                _arr = _orderReportData.onlineNormalTotalSales
                break;
        }
        if (_arr && _arr.length) rankingTable('online', _arr)
    }

    // 线下销售排行榜切换
    $('#offlineRankingnav li').click(function (event) {
        $(this).addClass('cur');
        $(this).siblings().removeClass('cur');
        $('#offlineTable').empty();
        let _content = $(this).html()
        offlineContent = _content
        getOfflineList(_content)
    })
    // o2o商品排行榜切换
    $('#o2oRankingnav li').click(function (event) {
        $(this).addClass('cur');
        $(this).siblings().removeClass('cur');
        $('#o2oTable').empty();
        let _content = $(this).html()
        o2oContent = _content
        getO2oList(_content)
    })

    function getOfflineList(_content) {
        let _arr = null
        switch (_content) {
            case '明星系列销售':
                _arr = _orderReportData.offlineStarTotalSales
                break;
            case '商品销量Top5':
                _arr = _orderReportData.offlineGoodsListTop
                break;
            case 'BA销售额Top5':
                _arr = _orderReportData.offlineGuidesListTop
                break;
            case 'BA所属专卖店Top5':
                _arr = _orderReportData.offlineStoresListTop
                break;
            case 'BA所属城市Top5':
                _arr = _orderReportData.offlineCitysListTop
                break;
        }
        if (_arr && _arr.length) rankingTable('offline', _arr)
    }

    function getO2oList(_content) {
        let _arr = null
        switch (_content) {
            case '明星系列销售':
                _arr = _orderReportData.o2oStarTotalSales
                break;
            case '商品销量Top5':
                _arr = _orderReportData.goodsListTop
                break;
            case 'BA销售额Top5':
                _arr = _orderReportData.guidesListTop
                break;
            case 'BA所属专卖店Top5':
                _arr = _orderReportData.storesListTop
                break;
            case 'BA所属城市Top5':
                _arr = _orderReportData.citysListTop
                break;
        }
        if (_arr && _arr.length) rankingTable('o2o', _arr)
    }

    /*数据请求*/

    // 小季接口
    function orderReporOverview(_parame) {
        getOrderReportData({
            parame: _parame,
        }, callbackA)
    }

    function callbackA(res) {
        _orderReportData = Object.assign(_orderReportData, res.data)
        console.log('小季接口', _orderReportData)
        if (_orderReportData.totalSalesAmount) $('#shopSale li').eq(0).find('.price').html(_orderReportData.totalSalesAmount)
        if (_orderReportData.averageSalesAmount) $('#shopSale li').eq(1).find('.price').html(_orderReportData.averageSalesAmount)
        if (_orderReportData.totalSalesCount) $('#shopSale li').eq(2).find('.price').html(_orderReportData.totalSalesCount)
        if (_orderReportData.totalUserCount) $('#shopSale li').eq(3).find('.price').html(_orderReportData.totalUserCount)

        if (_orderReportData.totalCreatedSalesCount) $('#orderSale li').eq(0).find('.price').html(_orderReportData.totalCreatedSalesCount)
        if (_orderReportData.totalPaidSalesCount) $('#orderSale li').eq(1).find('.price').html(_orderReportData.totalPaidSalesCount)
        if (_orderReportData.totalShippedSalesCount) $('#orderSale li').eq(2).find('.price').html(_orderReportData.totalShippedSalesCount)
        if (_orderReportData.totalReceivedSalesCount) $('#orderSale li').eq(3).find('.price').html(_orderReportData.totalReceivedSalesCount)
        if (_orderReportData.orderAccount) $('#summaryOnlineSales .sum-li').eq(0).find('.ratio').html(_orderReportData.orderAccount)
        if (_orderReportData.userSecPurchase) $('#summaryOnlineSales .sum-li').eq(1).find('.ratio').html(_orderReportData.userSecPurchase)
        // 线上商城
        let _onlineTotalSales = _orderReportData.onlineTotalSales
        if (_onlineTotalSales && _onlineTotalSales.list) {
            //nav
            let _html = ''
            _html = ``
            _onlineTotalSales.list.forEach(v => {
                _html += `<li class="nav-li">
                <div class="nav-icon"></div>
                <p>${v.cat_name}</p>
                </li>`
            });
            $('#onlineNav').html(_html)
            //线上商城圆饼图
            let _onlinedata = []
            _orderReportData.onlineTotalSales.list.forEach(v => {
                let _obj = {
                    name: v.amount,
                    value: Math.round(v.rawAmount)
                }
                _onlinedata.push(_obj)
            });
            myOnlineShopping.setOption({
                title: [
                    {
                        text: _orderReportData.onlineTotalSales.total,
                    },
                ],
                series: [
                    {
                        data: _onlinedata,
                    }
                ]
            });
        }
        // 线上商城
        let _offlineTotalSales = _orderReportData.offlineTotalSales
        if (_offlineTotalSales && _offlineTotalSales.list) {
            //nav
            let _html = ''
            _html = ``
            _offlineTotalSales.list.forEach(v => {
                _html += `<li class="nav-li">
                <div class="nav-icon"></div>
                <p>${v.cat_name}</p>
                </li>`
            });
            $('#offlineNav').html(_html)
            //线上商城圆饼图
            let _offlinedata = []
            _offlineTotalSales.list.forEach(v => {
                let _obj = {
                    name: v.amount,
                    value: Math.round(v.rawAmount)
                }
                _offlinedata.push(_obj)
            });
            myOfflineSales.setOption({
                title: [
                    {
                        text: _offlineTotalSales.total,
                    },
                ],
                series: [
                    {
                        data: _offlinedata,
                    }
                ]
            });
        }
        // O2O销售
        let _typesListTop = _orderReportData.typesListTop
        if (_typesListTop && _typesListTop.list) {
            //nav
            let _html = ''
            _html = ``
            _typesListTop.list.forEach(v => {
                _html += `<li class="nav-li">
                <div class="nav-icon"></div>
                <p>${v.cat_name}</p>
                </li>`
            });
            $('#o2oNav').html(_html)
            //o圆饼图

            let _o2odata = []
            _orderReportData.typesListTop.list.forEach(v => {
                let _obj = {
                    name: v.amount,
                    value: Math.round(v.rawAmount)
                }
                _o2odata.push(_obj)
            });
            myO2oSales.setOption({
                title: [
                    {
                        text: _orderReportData.typesListTop.total,
                    },
                ],
                series: [
                    {
                        data: _o2odata,
                    }
                ]
            });
        }
        // 线上商城排行榜默认显示
        if (onlineContent) getOnlineList(onlineContent)
        // 线下销售排行榜默认显示
        if (offlineContent) getOfflineList(offlineContent)
        // O2O销售排行榜默认显示
        if (o2oContent) getO2oList(o2oContent)
    }

    // kendo接口
    function DashboarReporOverview(_parame) {
        getDashboardRequest({
            parame: _parame,
        }, callbackB)
    }

    function callbackB(res) {
        console.log('kendo接口', res)
        let _memberData = res.data.member_data
        let _productData = res.data.product_data
        let _miniData = res.data.mini_data
        let _omsData = res.data.oms_data
        //会员数据
        if (_memberData) {
            $('#memberSale li').eq(0).find('.price').html(_memberData.register_count.mini)
            $('#memberSale li').eq(1).find('.price').html(_memberData.register_count.h5)
            $('#memberSale li').eq(2).find('.price').html(_memberData.register_count.pc)
            $('#memberSale li').eq(3).find('.price').html(_memberData.register_count.fission)
            $('#memberSale li').eq(4).find('.price').html(_memberData.register_count.guide)
            // 新会员注册nav
            if (_memberData.register_percent) {
                let _html = ''
                _html = ``
                for (const key in _memberData.register_percent) {
                    _html += `<li class="nav-li">
                    <div class="nav-icon"></div>
                    <div class="con">
                        <span>${key}</span>
                        <span>${_memberData.register_percent[key]}%</span>
                    </div>
                </li>`;
                }
                $('#newMemberNav').html(_html)
            }
            // 登录总人数nav
            if (_memberData.login_percent) {
                let _html = ''
                _html = ``
                for (const key in _memberData.login_percent) {
                    _html += `<li class="nav-li">
                    <div class="nav-icon"></div>
                    <div class="con">
                        <span>${key}</span>
                        <span>${_memberData.login_percent[key]}%</span>
                    </div>
                </li>`;
                }
                $('#signIinNav').html(_html)
            }
            //新会员注册渠道来源占比圆饼图
            let _newmemberdata = []
            for (const key in _memberData.register_percent) {
                let _obj = {
                    name: _memberData.register_percent[key] + '%',
                    value: _memberData.register_percent[key]
                }
                _newmemberdata.push(_obj)
            }
            //登录总人数占比圆饼图
            let _logindata = []
            for (const key in _memberData.login_percent) {
                let _obj = {
                    name: _memberData.login_percent[key] + '%',
                    value: _memberData.login_percent[key]
                }
                _logindata.push(_obj)
            }
            //新会员注册渠道来源占比
            myNewMember.setOption({
                series: [
                    {
                        data: _newmemberdata,
                    }
                ],
            });
            //登录总人数占比
            mySignIin.setOption({
                series: [
                    {
                        data: _logindata,
                    }
                ],
            });
        }
        //商品数据
        if (_productData) {
            let topProd = {
                topName: [],
                topScores: []
            }
            _productData.top_prod_view.forEach(v => {
                topProd.topName.push(v.prodName)
                topProd.topScores.push(v.scores)
            });
            let topShare = {
                topName: [],
                topScores: []
            }
            _productData.top_share.forEach(v => {
                topShare.topName.push(v.prodName)
                topShare.topScores.push(v.scores)
            });
            let topCat = {
                topName: [],
                topScores: []
            }
            _productData.top_cat_view.forEach(v => {
                topCat.topName.push(v.prod_cat_name)
                topCat.topScores.push(v.scores)
            });
            let topSearch = {
                topName: [],
                topScores: []
            }
            _productData.top_search.forEach(v => {
                topSearch.topName.push(v.keyword)
                topSearch.topScores.push(v.scores)
            });
            //商品访问Top3
            myProductVisit.setOption({
                xAxis: {
                    data: topProd.topName,
                },
                series: [{
                    data: topProd.topScores,
                }],
            });
            //商品分享Top3
            myCommoditySharing.setOption({
                xAxis: {
                    data: topShare.topName,
                },
                series: [{
                    data: topShare.topScores,
                }],
            });
            //商品类别访问Top3
            myProductCategories.setOption({
                xAxis: {
                    data: topCat.topName,
                },
                series: [{
                    data: topCat.topScores,
                }],
            });
            // 商品名称搜索次数Top10
            myProductSearch.setOption({
                yAxis: {
                    data: topSearch.topName,
                },
                series: [{
                    data: topSearch.topScores,
                }],
            });
        }

        //小程序数据
        if (_miniData) {
            let topMiniapp = {
                topName: [],
                topScores: []
            }
            $('#miniappSale li').eq(0).find('.price').html(_miniData.pv)
            $('#miniappSale li').eq(1).find('.price').html(_miniData.uv)
            $('#miniappSale li').eq(2).find('.price').html(_miniData.avg_stay_seconds)
            _miniData.top_page_view.forEach(v => {
                topMiniapp.topName.push(v.name)
                topMiniapp.topScores.push(v.pv)
            });
            //页面访问次数Top10
            myPageVisits.setOption({
                yAxis: {
                    data: topMiniapp.topName,
                },
                series: [{
                    data: topMiniapp.topScores,
                }],
            });
        }
        //OMS数据
        if (_omsData) {
            $('#ladder .area').eq(0).find('.block').html(`进店人数：${_omsData.uv}人`)
            $('#ladder .area').eq(1).find('.block').html(`商品页访问人数：${_omsData.pdt_uv}人`)
            $('#ladder .area').eq(2).find('.block').html(`下单人数：${_omsData.created_order_uv}人`)
            $('#ladder .area').eq(3).find('.block').html(`支付人数：${_omsData.paid_order_uv}人`)
            $('#ladder .area').eq(0).find('span').html(`商品访问转化率：${_omsData.pdt_view_percent}`)
            $('#ladder .area').eq(1).find('span').html(`下单率：${_omsData.create_order_percent}`)
            $('#ladder .area').eq(2).find('span').html(`支付率：${_omsData.paid_percent}`)
        }
    }
}