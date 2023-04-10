@extends('backend.base')

@section('content')
    <div class="layui-col-md12" id="promotion_cart">
        <div class="layui-card">
            <div class="layui-card-body" style="padding: 15px;">
                <form id="add_form" class="layui-form" action="">
                    
                    <div class="layui-form-item">
                        <label class="layui-form-label">规则类型 </label>
                        <div class="layui-form-mid">
                        	{{$detail['getType']['name']}}
                        	<input type="hidden" id="type" name="type" value="{{$detail['getType']['type']}}" />
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">规则名称</label>
                        <div class="layui-form-mid">
                        	{{$detail['name']??''}}
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">显示名称</label>
                        <div class="layui-form-mid">
                            {{$detail['display_name']??''}}
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">优先级</label>
                        <div class="layui-form-mid">
                        	{{$detail['priority']??''}}
                        </div>
                    </div>
                    @if($detail['getType']['type'] == 'ship_fee_try')
                        <div class="layui-form-item">
                            <label class="layui-form-label">运费</label>
                            <div class="layui-form-mid">
                                {{$detail['ship_fee']??''}}
                            </div>
                        </div>
                    @endif
                    @if($detail['getType']['type'] == 'coupon')
                        <div class="layui-form-item">
                            <label class="layui-form-label">库存</label>
                            <div class="layui-form-mid">
                                <input name="coupon_stock" lay-verify="required|number" value="{{$detail['coupon_stock']??''}}" autocomplete="off"
                                       class="layui-input" type="hidden">
								{{$detail['coupon_stock']??''}}
                            </div>
                        </div>
                        @isset($detail['coupon_stock_used'])
                        <div class="layui-form-item">
                            <label class="layui-form-label">领取数量</label>
                            <div class="layui-form-mid">
                                {{$detail['coupon_stock_used']??''}}
                            </div>
                        </div>
                        @endisset
                    @endif
                    @if($detail['getType']['type'] == 'product_coupon')
                        <div class="layui-form-item">
                            <label class="layui-form-label">库存</label>
                            <div class="layui-form-mid">
                                <input name="coupon_stock" lay-verify="required|number" value="{{$detail['coupon_stock']??''}}" autocomplete="off"
                                       class="layui-input" type="hidden">
                                {{$detail['coupon_stock']??''}}
                            </div>
                        </div>
{{--                        <div class="layui-form-item">--}}
{{--                            <label class="layui-form-label">随单礼券oss图片路径</label>--}}
{{--                            <div class="layui-form-mid">--}}
{{--                                {{$detail['product_coupon_pic']??''}}--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="layui-form-item">--}}
{{--                            <label class="layui-form-label">随单礼券商品名称</label>--}}
{{--                            <div class="layui-form-mid">--}}
{{--                                {{$detail['product_coupon_name']??''}}--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        @isset($detail['coupon_stock_used'])
                            <div class="layui-form-item">
                                <label class="layui-form-label">领取数量</label>
                                <div class="layui-form-mid">
                                    {{$detail['coupon_stock_used']??''}}
                                </div>
                            </div>
                        @endisset
                    @endif
                    <div class="layui-form-item {{$detail['getType']['type'] == 'ship_fee_try'?'layui-hide':''}}">
                        <label class="layui-form-label">规则条件</label>
                        <div class="layui-inline">
                        	@if($detail['getType']['type'] == 'product_discount')
                            <div class="layui-form-mid">
								{{$detail['product_discount']??''}}折
                            </div>
                            @endif
                            @if($detail['getType']['type'] == 'n_piece_n_discount')
                            <div class="layui-form-mid">
								{{$detail['nn_n'][0]??''}}件
                            </div>
                            <div class="layui-form-mid">
								{{$detail['nn_discount'][0]??''}}折
                            </div>
                            @isset($detail['nn_n'])
                            @foreach($detail['nn_n'] as $k=>$n)
                            @if($k != 0)
                            <div class="add_more_product"><hr style="background-color:transparent;">
                            	<div class="layui-form-mid">
    								{{$detail['nn_n'][$k]??''}}件
                                </div>
                                <div class="layui-form-mid">
    								{{$detail['nn_discount'][$k]??''}}折
                                </div>
                            </div>
                            @endif
                            @endforeach
                            @endisset
                            @endif
                        	@if($detail['getType']['type'] == 'full_reduction_of_order')
                            <div class="layui-form-mid">
								满{{$detail['total_amount'][0]??''}}
                            </div>
                            <div class="layui-form-mid">
								减{{$detail['total_discount'][0]??''}}
                            </div>
                            @isset($detail['total_amount'])
                            @foreach($detail['total_discount'] as $k=>$n)
                            @if($k != 0)
                            <div class="add_more_product"><hr style="background-color:transparent;">
                            	<div class="layui-form-mid">
    								满{{$detail['total_amount'][$k]??''}}
                                </div>
                                <div class="layui-form-mid">
    								减{{$detail['total_discount'][$k]??''}}
                                </div>
                            </div>
                            @endif
                            @endforeach
                            @endisset
                            @endif
                            @if($detail['getType']['type'] == 'order_n_discount')
                            <div class="layui-form-mid">
								每满{{$detail['step_amount'][0]??''}}
                            </div>
                            <div class="layui-form-mid">
								减{{$detail['step_discount'][0]??''}}
                            </div>
                            @isset($detail['step_amount'])
                            @foreach($detail['step_amount'] as $k=>$n)
                            @if($k != 0)
                            <div class="add_more_product"><hr style="background-color:transparent;">
                            	<div class="layui-form-mid">
    								每满{{$detail['step_amount'][$k]??''}}
                                </div>
                                <div class="layui-form-mid">
    								减{{$detail['step_discount'][$k]??''}}
                                </div>
                            </div>
                            @endif
                            @endforeach
                            @endisset
                            @endif
                            @if($detail['getType']['type'] == 'coupon')
                            <div class="layui-form-mid">
								满{{$detail['total_amount'][0]??''}}
                            </div>
                            <div class="layui-form-mid">
								减{{$detail['total_discount'][0]??''}}
                            </div>
                            @endif
                                @if($detail['getType']['type'] == 'product_coupon')
                                    <div class="layui-form-mid">
                                        实物sku: {{$detail['product_coupon_sku']??''}}
                                    </div>
                                @endif
                            @if($detail['getType']['type'] == 'gift')
                            <div class="layui-form-mid">
								{{(isset($detail['gift_amount']) and $detail['gift_amount']) ?'满xxx元':'' }}
								{{(isset($detail['gift_n']) and $detail['gift_n']) ?'满xxx件':'' }}
                            </div>
                            @endif
                                @if($detail['getType']['type'] == 'free_try')
                                    <div class="layui-form-mid">
                                        {{(isset($detail['gift_amount']) and $detail['gift_amount']) ?'满xxx元':'' }}
                                        {{(isset($detail['gift_n']) and $detail['gift_n']) ?'满xxx件':'' }}
                                    </div>
                                @endif
                        </div>
                    </div>
                    @if($detail['getType']['type'] == 'gift')
                    <div class="layui-form-item ">
                        <label class="layui-form-label">选择步长</label>
                        <div class="layui-form-mid">
                            <div class="layui-input-inline">{{empty($detail['is_step'])?'否':'是'}}</div>
                        </div>
                    </div>
                    <div class="layui-form-item " id="condition_value" >
                        <label class="layui-form-label">条件值</label>
                        <div class="layui-form-mid">
                            <div id="div_gift_amount" class="layui-input-inline" style="display:{{(isset($detail['gift_amount']) and $detail['gift_amount']) ?'block':'none' }};">
								满{{$detail['gift_amount']}}元
                            </div>
                            <div id="div_gift_n" class="layui-input-inline" style="display:{{(isset($detail['gift_n']) and $detail['gift_n']) ?'block':'none' }};">
								满{{$detail['gift_n']}}件
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">赠品</label>
                        <div class="layui-form-mid">
                            {{$detail['gwp_skus']}}
                        </div>
                    </div>
                    @endif
                    @if($detail['getType']['type'] == 'gift')
                        <div class="layui-form-item ">
                            <label class="layui-form-label">随机赠送</label>
                            <div class="layui-form-mid">
                                <div class="layui-input-inline">{{empty($detail['is_rand'])?'否':'是'}}</div>
                            </div>
                        </div>
                    @endif
                    @if($detail['getType']['type'] == 'free_try')
                        <div class="layui-form-item " id="condition_value" >
                            <label class="layui-form-label">条件值</label>
                            <div class="layui-form-mid">
                                <div id="div_gift_amount" class="layui-input-inline" style="display:{{(isset($detail['gift_amount']) and $detail['gift_amount']) ?'block':'none' }};">
                                    满{{$detail['gift_amount']}}元
                                </div>
                                <div id="div_gift_n" class="layui-input-inline" style="display:{{(isset($detail['gift_n']) and $detail['gift_n']) ?'block':'none' }};">
                                    满{{$detail['gift_n']}}件
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">试用装</label>
                            <div class="layui-form-mid">
                                {{$detail['gwp_skus']}}
                            </div>
                        </div>
                    @endif
                    <div class="layui-form-item ">
                        <label class="layui-form-label">全场</label>
                        <div class="layui-form-mid">
                            <div class="layui-input-inline">{{empty($detail['is_whole'])?'否':'是'}}</div>
                        </div>
                    </div>
					<div class="layui-form-item" style="margin-top: 5px;">
                        <label class="layui-form-label">系列</label>
                        <div class="layui-input-block" >
                        	@foreach($categoryData as $cats)
                        	<input type="checkbox" {{in_array($cats['id'],$detail['cids_arr'])?'checked':''}} lay-filter="cats" name="cats[{{$cats['id']}}]" title="{{$cats['name']}}">
                            @endforeach
                        </div>
                    </div>

                    <div class="layui-form-item" style="margin-top: 5px;">
                        <label class="layui-form-label">增加商品</label>
                        <div class="layui-form-mid">
                        	{{$detail['add_sku']??''}}
                        </div>
                    </div>
                    <div class="layui-form-item" style="margin-top: 5px;">
                        <label class="layui-form-label">排除商品</label>
                        <div class="layui-form-mid">
                        	{{$detail['exclude_sku']??''}}
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">有效期</label>
                        <div class="layui-inline">
                            <div class="layui-form-mid">
                            	<input class="layui-input" lay-verify="required" id="start_time" name="start_time" placeholder="开始日期" value="{{$detail['start_time']??''}}" type="hidden">
                                {{$detail['start_time']??''}}
                            </div>
                            <div class="layui-form-mid">--</div>
                            <div class="layui-form-mid">
                            	<input class="layui-input" lay-verify="required" id="end_time" name="end_time" placeholder="结束日期" value="{{$detail['end_time']??''}}" type="hidden">
                                {{$detail['end_time']??''}}
                            </div>
                        </div>
                    </div>
                    @if($detail['getType']['type'] == 'coupon' or  $detail['getType']['type'] == 'product_coupon')
                    <div class="layui-form-item">
                        <label class="layui-form-label">领取后过期天数</label>
                        <div class="layui-inline">
                            <div class="layui-input-inline">
                                {{$detail['expire_days']??''}}
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item ">
                        <label class="layui-form-label">新人券</label>
                        <div class="layui-form-mid">
                            <div class="layui-input-inline">{{empty($detail['is_new'])?'否':'是'}}</div>
                        </div>
                    </div>
                    <div class="layui-form-item" style="margin-top: 5px;">
                        <label class="layui-form-label">触发SKU</label>
                        <div class="layui-input-block">
                            <div class="layui-input-inline">{{$detail['trigger_sku']}}</div>
                        </div>
                    </div>
                    @endif
                   
                    <div class="layui-form-item {{$detail['getType']['type'] == 'ship_fee_try'?'layui-hide':''}}">
                        <label class="layui-form-label">叠加</label>
                        <div class="layui-input-block" id="div-addloading">
                        </div>
                    </div>

                    @if(isset($detail['id']))
                        <input type="hidden" name="id" value="{{$detail['id']}}">
                    @endif
					
                    <div class="layui-form-item">
                        <div class="layui-input-block" >
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<script>
    @section('layui_script')
    //选择不同的优惠码
    form.on('select(type_list)', function(data){
  	  	console.log(data);
  	  	var value = data.value;
  	  	var url = "{{ route('backend.promotion.cart.edit') }}?type="+value;
    	var index = parent.layer.getFrameIndex(window.name)
    	parent.layer.iframeSrc(index,url);
  	});
  	
    form.on('checkbox(cats)', function(data){
  	  console.log(data.value);
  	});
	
    //自定义验证规则
    form.verify(
        {
            int: [/^\d+$/,'请输入正确的整数'],
            _required: function (value, item) {
                 if(!value){
                     return "必填项不能为空";
                 }
            },

        },
    );


    before = function () {
        layer.load();
    },
        done = function (res, uploadIns) {
            layer.closeAll('loading');
            if (res.code != 0) {
                layer.msg(res.msg, {icon: 5, anim: 6});
                $('input[name="' + uploadIns.config.elem.attr('id') + '"]').val('');
            } else {
                $('input[name="' + uploadIns.config.elem.attr('id') + '"]').val(res.data.real);
                var input = uploadIns.config.elem.next();
            }
        }

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
    function loadCrossRules(){
    	var start_time = $('#start_time').val();
		var end_time = $('#end_time').val();
		var type = $('#type').val();
		var id = {{$detail['id']}};
		var data = {start_time:start_time,end_time:end_time,type:type,id:id};
		var addrules = [];
		@isset($detail['addrules'])
		addrules = [ "{!!implode('","',$detail['addrules'])!!}" ];
		@endisset
		$.post("{{ route('backend.promotion.cart.getCrossRules') }}", data, function (res) {
            $('#div-addloading input').remove('.dynamiccheckout');//layui-form-checkbox
    		$('#div-addloading div').remove('.layui-form-checkbox');//
    		var checked = '';
            $.each(res,function(i,value){
                checked = '';
                if($.inArray(value.id.toString(),addrules) != '-1'){
                    checked = 'checked';
                }
            	$('#div-addloading').append('<input class="dynamiccheckout" '+checked+' type="checkbox" name="addrule['+ value.id +']"  title="'+ value.name +'" >');
            });
            form.render();
        }, 'json');
    }
	lay("#addloading").on('click',function(e){
		loadCrossRules();
		});
	loadCrossRules();
    @endsection
</script>
