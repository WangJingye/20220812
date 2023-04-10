<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Arden-admin</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ url('/static/admin/layuiadmin/layui/css/layui.css') }}" media="all">
    <link rel="stylesheet" href="{{ url('/static/admin/layuiadmin/style/admin.css') }}" media="all">
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
        index: 'lib/index' //主入口模块
    }).use(['element','form','layer','table','upload','laydate','tree'],function () {
        var element = layui.element;
        var layer = layui.layer;
        var form = layui.form;
        var table = layui.table;
        var upload = layui.upload;
        var laydate = layui.laydate;
        var tree = layui.tree;

        @yield('layui_script')
    });

</script>
@yield('script')
</body>
</html>



