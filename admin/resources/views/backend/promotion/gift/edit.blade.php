@extends('backend.base')

@section('content')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body" style="padding: 15px;">
                <form id="add_form" class="layui-form"  action="">
                    <div class="layui-form-item">
                        <label class="layui-form-label">名称</label>
                        <div class="layui-input-block">
                            <input name="name" lay-verify="required" value="{{$detail['name']??''}}" autocomplete="off" class="layui-input" type="text">
                        </div>
                    </div>
                                      
                    <div class="layui-form-item">
                        <label class="layui-form-label">库存</label>
                        <div class="layui-input-block">
                            <input name="qty" lay-verify="required|number|css_int" value="{{$detail['qty']??''}}" autocomplete="off" class="layui-input" type="text">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">图片</label>
                        <div class="layui-upload">
                          <button type="button" class="layui-btn" id="test1">上传图片</button>
                          <div class="layui-upload-list" >
                            <img class="layui-upload-img" id="demo1">
                            <p id="demoText"></p>
                          </div>
                        </div>  
                    </div>
                    @isset($detail['oss_pic'])
                    <div class="layui-form-item">
                        <label class="layui-form-label">图片</label>
                        <div class="layui-upload">
                          <div class="layui-upload-list">
                            <img class="layui-upload-img" src="{{$detail['oss_pic']}}">
                            <p id="demoText"></p>
                          </div>
                        </div> 
                    </div>
                    @endisset
        			@if(isset($detail['id']))
                        <input type="hidden" name="id" value="{{$detail['id']}}">
                    @endif
                    <input id="pic" readonly name="pic" lay-verify="varchar" value="{{$detail['pic']??''}}" autocomplete="off" class="layui-input" type="hidden">
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
    form.on('submit(formSubmit)', function(data){
        $.post("{{ route('backend.promotion.gift.post') }}",data.field,function(res){
            if(res.code!=1){
                layer.msg(res.msg,{icon:5,anim:6});
                return false;
            }else{
                var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                parent.layer.close(index); //再执行关闭
            }
       
        },'json');
        return false;
    });

    //自定义验证规则
    form.verify({
    	css_int: [/^\d+$/,'请输入正确的整数']
    });

    lay("input[name='start_time']").on('click', function (e) {
        laydate.render({
            elem: "input[name='start_time']"
            , type: 'datetime'
            , show: true //直接显示
            , closeStop: "input[name='start_time']"
        });
    });
    lay("input[name='end_time']").on('click', function (e) {
        laydate.render({
            elem: "input[name='end_time']"
            , type: 'datetime'
            , show: true //直接显示
            , closeStop: "input[name='end_time']"
        });
    });

	//pic upload 
    var $ = layui.jquery
    ,upload = layui.upload;
    
    //普通图片上传
    var uploadInst = upload.render({
      elem: '#test1'
      ,url: "{{ route('backend.promotion.gift.uploadPic') }}"
      ,before: function(obj){
        //预读本地文件示例，不支持ie8
        obj.preview(function(index, file, result){
          $('#demo1').attr('src', result); //图片链接（base64）
        });
      }
      ,done: function(res){
        //如果上传失败
        if(res.code > 0){
          return layer.msg('上传失败');
        }
        //上传成功
        var path = res.path;
        $('#pic').val(path);
      }
      ,error: function(){
        //演示失败状态，并实现重传
        var demoText = $('#demoText');
        demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs demo-reload">重试</a>');
        demoText.find('.demo-reload').on('click', function(){
          uploadInst.upload();
        });
      }
    });

@endsection
</script>