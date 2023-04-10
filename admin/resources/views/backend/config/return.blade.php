@extends('backend.base')

@section('content')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body" style="padding: 15px;">
                <form id="add_form" class="layui-form" action="">
                    <div class="layui-form-item">
                        <div class="layui-input-block" style="display: flex;display: -webkit-flex;flex-direction:column;width:40%;">
                            <input class="layui-input" type="hidden" id="image" name="image" value="{{$detail->image}}">
                            <img id="image_src" width="100%" src="{{$detail->image}}" style="display:none;"/>
                            <button type="button" class="layui-btn" id="image_button" style="margin-top:10px;">
                                <i class="layui-icon">&#xe67c;</i>上传图片
                            </button>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button id="formSubmit" class="layui-btn" lay-submit lay-filter="formSubmit">提交</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<script>
@section('layui_script')
    var prodIndex = null;
    layui.use(['upload','form'], function(){
        var upload = layui.upload;

        upload.render({
            elem: '#image_button' //绑定元素
            ,url: "{{ route('backend.file.uploadPic') }}" //上传接口
            ,done: function(res){
                //如果上传失败
                if(res.code > 0){
                    return layer.msg('上传失败');
                }
                //上传成功
                var path = res.path;
                $("#image").val(path);
                $("#image_src").attr("src",path).show();
            }
            ,error: function(){
            //请求异常回调
            }
        });

        //监听提交
        form.on('submit(formSubmit)', function(data){
            $.post("{{ route('backend.config.return.save') }}",data.field,function(res){
                if(res.code!=1){
                    layer.msg(res.msg,{icon:5,anim:6});
                    return false;
                }else{
                    layer.msg('保存成功');
                }
        
            },'json');
            return false;
        });

        //自定义验证规则
        form.verify({

        });
    });
    if($("#image").val() !== ''){
        $("#image_src").show();
    }
@endsection
</script>
