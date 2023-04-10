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
                    <div class="layui-form-item {{$detail['getType']['type'] == 'ship_fee_try'?'layui-hide':''}}">
                        <label class="layui-form-label">规则条件</label>
                        <div class="layui-inline">
                            @if($detail['getType']['type'] == 'coupon')
                            <div class="layui-form-mid">
								满{{$detail['total_amount'][0]??''}}
                            </div>
                            <div class="layui-form-mid">
								减{{$detail['total_discount'][0]??''}}
                            </div>
                            @endif
                            @if($detail['getType']['type'] == 'coupon_discount')
                                <div class="layui-form-mid">
                                    {{$detail['product_discount']??''}}折
                                </div>
                            @endif
                        </div>
                    </div>
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
