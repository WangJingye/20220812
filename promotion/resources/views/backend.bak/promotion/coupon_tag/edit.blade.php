@extends('backend.base')

@section('content')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body" style="padding: 15px;">
                <form id="add_form" class="layui-form"  action="">
                    <div class="layui-form-item">
                        <label class="layui-form-label">KEY</label>
                        <div class="layui-input-block">
                            <input name="key" lay-verify="required" value="{{$detail['key']??''}}" autocomplete="off" class="layui-input" type="text">
                        </div>
                    </div>
                    
                     <div class="layui-form-item">
                        <label class="layui-form-label">标签</label>
                        <div class="layui-input-block">
                            <input name="label" lay-verify="required" value="{{$detail['label']??''}}" autocomplete="off" class="layui-input" type="text">
                        </div>
                    </div>
                   
                    @if(isset($detail['id']))
                        <input type="hidden" name="id" value="{{$detail['id']}}">
                    @endif
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
        $.post("{{ route('backend.promotion.coupon_tag.post') }}",data.field,function(res){
            if(res.code!=0){
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

    });


    before = function(){
        layer.load();
    },
    done = function(res,uploadIns) {
        layer.closeAll('loading');
        if(res.code!=0){
            layer.msg(res.msg,{icon:5,anim:6});
            $('input[name="'+uploadIns.config.elem.attr('id')+'"]').val('');
        }else{
            $('input[name="'+uploadIns.config.elem.attr('id')+'"]').val(res.data.real);
            var input = uploadIns.config.elem.next();
        }
    }

    lay("input[name='start_time']").on('click', function(e){
        laydate.render({
            elem: "input[name='start_time']"
            ,type: 'datetime'
            ,show: true //直接显示
            ,closeStop: "input[name='start_time']"
        });
    });
    lay("input[name='end_time']").on('click', function(e){
        laydate.render({
            elem: "input[name='end_time']"
           ,type: 'datetime'
            ,show: true //直接显示
            ,closeStop: "input[name='end_time']"
        });
    });


@endsection
</script>