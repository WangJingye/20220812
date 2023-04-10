@extends('backend.base')

@section('content')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body" style="padding: 15px;">
                <form id="add_form" class="layui-form"  action="">
                    <div class="layui-form-item">
                        <label class="layui-form-label">规则名称</label>
                        <div class="layui-input-block">
                            <input name="name" lay-verify="required" value="{{$detail['name']??''}}" autocomplete="off" class="layui-input" type="text">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">分类名称</label>
                         <div class="layui-input-block">
                            <select name="category_id">
                                @foreach($categories as $cate)
                                    <option value="{{$cate['id']}}" @if(isset($detail["category_id"]) && $cate['id']==$detail["category_id"]) selected @endif >{{$cate['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">折扣类型</label>
                        <div class="layui-input-block">
                         <select name="apply">
                            @foreach(['fixed'=>'固定折扣','percent'=>'百分比折扣'] as $val=>$label)
                                    <option value="{{$val}}" @if(isset($detail["apply"]) && $val==$detail["apply"]) selected @endif >{{$label}}</option>
                            @endforeach
                          </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">折扣</label>
                        <div class="layui-input-block">
                            <input name="discount" lay-verify="required|number" value="{{$detail['discount']??''}}" autocomplete="off" class="layui-input" type="text">
                        </div>
                    </div>
                    
                    <div class="layui-form-item">
                    <label class="layui-form-label">有效期</label>
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <input class="layui-input" name="start_time" placeholder="开始日期" value="{{$detail['start_time']??''}}" type="text">
                        </div>
                        <div class="layui-input-inline">
                            <input class="layui-input" name="end_time" placeholder="结束日期" value="{{$detail['end_time']??''}}" type="text">
                        </div>
                    </div>
                	</div>
                	 <div class="layui-form-item">
                        <label class="layui-form-label">状态</label>
                        
                        <div class="layui-input-block">
                       	 	开启<input type="radio" value="1" name="status" {{$detail['status']?'checked':''}}/>
                       	 	关闭<input type="radio" value="0" name="status" {{!$detail['status']?'checked':''}}/>
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
        $.post("{{ route('backend.promotion.category.post') }}",data.field,function(res){
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