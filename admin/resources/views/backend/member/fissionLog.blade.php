<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>网站后台管理模版</title>
    <link rel="stylesheet" type="text/css" href="<?php echo url('/static/admin/layui/css/layui.css')?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo url('/static/admin/css/admin.css')?>" />
    <script src="{{ url('/static/admin/layuiadmin/layui/layui.js') }}"></script>
    <script src="{{ url('/static/admin/js/jquery.min.js') }}"></script>
    
</head>

<body>
<style>
    .layui-inline{
        display:flex;
    }
    select{
        border:1px solid #ccc;
    }
    #table-list th,#table-list td{
        text-align: center;
        border:1px solid #000 !important;
    }

    .order-status-filter ul{
        display: flex;
    }
    .order-status-filter li{
        flex:1;
        text-align: center;
    }
    .order-status-filter li a{
        width:100%;
    }
</style>
<div class="page-content-wrap">
    <div class="layui-form" id="table-list">
        <table class="layui-table" lay-even lay-skin="nob">
            <thead>
            <tr>
                <th class="hidden-xs" style="width:20%;text-align: center">id</th>
                <th class="hidden-xs" style="width:10%;text-align: center">用户名称</th>
                <th class="hidden-xs" style="width:10%;text-align: center">获得数量</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($list as $order)
                <tr>
                    <td  style="text-align: left">
                        <span class="order_sn">
                                {{$order['member_id']}}
                        </span>
                    </td>
                    <td  style="text-align: left">
                        <span class="created_at">
                                {{$order['member_name']}}
                        </span>
                    </td>
                    <td  style="text-align: left">
                        <span class="created_at" >
                                {{$order['c']}}
                        </span>
                        
                    </td>

                </tr>
               
            @endforeach
            </tbody>
        </table>
       
    </div>

</div>
</body>

</html>