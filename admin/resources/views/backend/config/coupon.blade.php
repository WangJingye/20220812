@extends('backend.base')

@section('content')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body" style="padding: 15px;">
                <form id="add_form" class="layui-form"  action="">
                    <div class="layui-form-item">
                        <label class="layui-form-label" style="width:200px;">首次购物优惠券配置</label>
                        <div class="layui-input-inline" style="min-width:300px;" >
                            <select  name="new_member_coupon" lay-filter="new_member_coupon" >
                            	<option value="-1">选择优惠券</option>
                            	@foreach($detail['coupon_list'] as $k=>$v)
                            	<option {{$v['id']==$detail['new_member_coupon']?'selected':''}} value="{{$v['id']}}">{{$v['name']}}</option>
                            	@endforeach
                            </select>
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
    form.on('submit(formSubmit)', function(data){
        $.post("{{ route('backend.config.coupon.save') }}",data.field,function(res){
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


	//pic upload 
    var $ = layui.jquery
    ,upload = layui.upload;
    
    //普通图片上传
    var uploadInst = upload.render({
      elem: '#test1'
      ,url: "{{ route('backend.config.coupon.uploadPic') }}"
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