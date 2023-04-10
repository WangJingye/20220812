@extends('backend.base')

@section('content')
    <div class="layui-col-md12">
        <div class="layui-tab">
            <ul class="layui-tab-title">
                <li class="layui-this">商品详情推荐</li>
                <li>空购物车页推荐</li>
                <li>搜索页推荐</li>
                <li>支付成功页推荐</li>
                <li>搜索精选商品</li>
            </ul>


            <div class="layui-tab-content" style="padding: 15px;">
                @foreach($recs as $i=>$rec)
                    <div class="layui-tab-item @if($i == 0) layui-show @endif">
                        <form class="layui-form"  action="">
                            <input type="hidden" name="config_name" value="{{$rec}}" />
                            <div class="layui-form-item ">
                                <div class="layui-input-block" style="display: flex;flex-direction: column;">
                                    @foreach ($opts as $k => $option)
                                        <input type="radio" name="config_value" value="{{$k}}" title="{{$option}}" @if (isset($data[$rec]['config_value']) && $data[$rec]['config_value']  == $k) checked @endif >
                                    @endforeach
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label class="layui-form-label">商品ID:</label>
                                <div class="layui-input-block">
                                    <input type="text" name="extension[product_id]" value="{{$data[$rec]['extension']['product_id']??''}}" autocomplete="off" class="layui-input" placeholder="选择自定义商品时需要填写的商品">
                                </div>
                                <div class="layui-form-mid layui-word-aux"></div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-input-block">
                                    <button id="formSubmit" class="layui-btn" lay-submit lay-filter="formSubmit">提交</button>
                                </div>
                            </div>
                        </form>
                    </div>

                @endforeach

            </div>
        </div>
    </div>
@endsection

<script>
@section('layui_script')
    form.on('submit(formSubmit)', function(data){
        $.post("{{ route('backend.config.recommend.save') }}",data.field,function(res){
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

@endsection
</script>
