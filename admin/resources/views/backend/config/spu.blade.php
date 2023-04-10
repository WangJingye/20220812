@extends('backend.base')

@section('content')
    <style>
        .layui-input-block{
            display: inline-block;
        }
    </style>
    <div class="layui-card product-edit" id="app">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>商品详情组件图片</h2>
        </div>
        <div class="layui-card-body">
            <el-form ref="form" class="layui-form" method="post" onsubmit="return check();">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <div  class="layui-input-block" style="margin-left: 10px;">产品展示</div>
                    <div class="layui-input-block">
                        <img src="<?php echo  env('OSS_DOMAIN')?>/{{$prod_default}}" class="prod_default_img" style="width:300px;"/>
                    </div>

                    <div class="layui-input-block">
                        <div class="layui-upload">
                            <button type="button" class="layui-btn" id="prod_default"><i class="layui-icon layui-icon-upload"></i>上传图片</button>
                        </div>
                    </div>


                </div>
                <div class="layui-form-item">
                    <div  class="layui-input-block" style="margin-left: 10px;">模特展示</label>
                    <div class="layui-input-block">
                        <img src="<?php echo env('OSS_DOMAIN')?>/{{$model_default}}"  class="model_default_img" style="width:300px;"/>
                    </div>
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn" id="model_default" style="margin-top:10px;">
                            <i class="layui-icon"></i>上传图片
                        </button>
                    </div>
                </div>
        </div>
    </div>

@endsection

<script>
    @section('layui_script')
    layui.use('upload', function(){
        var $ = layui.jquery
            ,upload = layui.upload;
        //普通图片上传
        var uploadInst = upload.render({
            elem: '#prod_default'
            ,size: 512
            ,url: '<?php echo url('admin/config/spu/upload/prod_default')?>'
            ,before: function(obj){
            }
            ,done: function(res){
                //如果上传失败
                if(res.status !== true){
                    return layer.msg(res.message);
                }
                $('.prod_default_img').attr('src',"<?php echo  env('OSS_DOMAIN')?>/" +res.file);
            }
            ,error: function(){
            }
        });

        var uploadInst = upload.render({
            elem: '#model_default'
            ,size: 512
            ,url: '<?php echo url('admin/config/spu/upload/model_default')?>'
            ,before: function(obj){
            }
            ,done: function(res){
                //如果上传失败
                if(res.status !== true){
                    return layer.msg(res.message);
                }
                $('.model_default_img').attr('src',"<?php echo  env('OSS_DOMAIN')?>/" +res.file );
            }
            ,error: function(){
            }
        });
    });
    @endsection
</script>