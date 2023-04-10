@extends('backend.base')

@section('content')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body" style="padding: 15px;">
                <form id="add_form" class="layui-form"  action="">
                    <div class="layui-form-item">
                        <label class="layui-form-label">名称</label>
                        <div class="layui-input-block">
                            <input name="name" lay-verify="required" value="" autocomplete="off" class="layui-input" type="text">
                        </div>
                    </div>
                    
                     <div class="layui-form-item">
                        <label class="layui-form-label">优惠码长度</label>
                        <div class="layui-input-block">
                            <input name="length" lay-verify="required|number" value="" autocomplete="off" class="layui-input" type="text">
                        </div>
                    </div>
                    
                    <div class="layui-form-item">
                        <label class="layui-form-label">优惠码数量</label>
                        <div class="layui-input-block">
                            <input name="size" lay-verify="required|number" value="" autocomplete="off" class="layui-input" type="text">
                        </div>
                    </div>
                    
                    <div class="layui-form-item">
                        <label class="layui-form-label">购物卷标签组</label>
                        <div class="layui-input-block">
                            <select name="coupon_tag_key" >
                            	@foreach($option as $k=>$v)
                            	<option value="{{$k}}">{{$v}}</option>
                            	@endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="layui-form-item">
                    <label class="layui-form-label">有效期</label>
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <input class="layui-input" name="start_time" placeholder="开始日期"  type="text">
                        </div>
                        <div class="layui-input-inline">
                            <input class="layui-input" name="end_time" placeholder="结束日期"  type="text">
                        </div>
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
        $.post("{{ route('backend.promotion.coupon.post') }}",data.field,function(res){
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