@extends('backend.base')

<script src="<?=url('/static/admin/js/jquery.min.js'); ?>"></script>
<link rel="stylesheet" href="{{ url('/static/admin/css/form.css') }}" media="all">

<script src="{{ url('/static/admin/js/custom.js') }}"></script>
@section('content')
    <style>
        .layui-form-item {
            white-space: nowrap !important;
        }
    </style>
    <div class="layui-card product-edit" id="app">
        <div class="layui-card-body">
        <div class="layui-form-item">
            <label for="" class="layui-form-label">付邮活动名称</label>
            <div class="layui-input-block">
                {{$detail['display_name']}}
            </div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label">邮费</label>
            <div class="layui-input-block">
                <div class="layui-form-mid">
                    {{$detail['money']}}
                </div>
            </div>
        </div>
        <div class="layui-form-item">
                <label class="layui-form-label">sku信息</label>
                <div class="layui-input-block">
                {{$detail['add_sku']}}
                </div>
        </div>
            <div class="layui-form-item">
                <label class="layui-form-label">有效期</label>
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        {{$detail['start_time']}} --{{$detail['end_time']}} 
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="<?=url('/lib/app/index.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?=url('/static/admin/css/admin.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/css/bootstrap.min.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/css/swiper.min.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="//at.alicdn.com/t/font_1458973_xkrtaihi4e8.css"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/static/admin/layui/css/layui.css'); ?>"/>


@endsection
