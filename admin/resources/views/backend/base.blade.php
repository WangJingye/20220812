<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>后台管理系统</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ url('/static/admin/layuiadmin/layui/css/layui.css') }}" media="all">
    <link rel="stylesheet" href="{{ url('/static/admin/layuiadmin/style/admin.css') }}" media="all">
    <link rel="stylesheet" href="{{ url('/static/admin/layuiadmin/style/dtree.css') }}" media="all">
    <link rel="stylesheet" href="{{ url('/static/admin/layuiadmin/style/font/dtreefont.css') }}" media="all">
</head>
<body>

<div class="layui-fluid">
    @yield('content')
</div>

<script src="{{ url('/static/admin/js/jquery.min.js') }}"></script>
<script src="{{ url('/static/admin/layuiadmin/layui/layui.js') }}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    layui.config({
        base: "{{ url('/static/admin/layuiadmin/') }}" //静态资源所在路径
    }).extend({
        index: 'lib/index', //主入口模块
        dtree: '/layui/lay/modules/dtree',
        excel: '/modules/excel'
    }).use(['element','form','layer','table','upload','laydate','dtree','laytpl', 'excel'],function () {
        var element = layui.element;
        var layer = layui.layer;
        var form = layui.form;
        var table = layui.table;
        var upload = layui.upload;
        var laydate = layui.laydate;
        var dtree = layui.dtree;
        var laytpl = layui.laytpl;
        var $ = layui.jquery;
        var excel = layui.excel;

        @yield('layui_script')
    });

</script>
@yield('script')
</body>
</html>



