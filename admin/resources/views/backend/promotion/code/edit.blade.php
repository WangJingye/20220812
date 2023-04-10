@extends('backend.base')

@section('content')
    <div class="layui-col-md12" id="promotion_cart">
        <div class="layui-card">
            <div class="layui-card-body" style="padding: 15px;">
                <form id="add_form" class="layui-form" lay-filter="promotion_form" action="">
                    
                    <div class="layui-form-item">
                        <label class="layui-form-label">优惠码类型 </label>
                        <div class="layui-form-mid">
                        	{{$detail['getType']['name']}}
                        	<input type="hidden" id="type" name="type" value="{{$detail['getType']['type']}}" />
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">类型</label>
                        <div class="layui-input-block">
                            <select lay-verify="required" name="type_list" lay-filter="type_list" >
                            	@foreach($detail['type_list'] as $k=>$v)
                            	<option {{$k==$detail['getType']['type']?'selected':''}} value="{{$k}}">{{$v}}</option>
                            	@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">优惠码 </label>
                        <div class="layui-input-block">
                            <div class="layui-inline">
                                <input style="width:300px;" name="code_code" lay-verify="required|code_code" value="{{$detail['code_code']??''}}" autocomplete="off" class="layui-input" type="text">
                            </div>
                            <div class="layui-inline">
                                <button type="button" id="make_code_code" class="layui-btn layui-btn-normal">生成随机码</button>
                            </div>
                        </div>
                        <div class="layui-input-block">
                            <div class="layui-form-mid layui-word-aux">只允许输入3-20位英文或数字,不区分大小写</div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">优惠码名称</label>
                        <div class="layui-input-block">
                            <input name="name" lay-verify="required" value="{{$detail['name']??''}}" autocomplete="off" class="layui-input" type="text">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">优先级</label>
                        <div class="layui-input-block">
                            <input name="priority" lay-verify="required|int" placeholder="值越大优先" value="{{$detail['priority']??''}}" autocomplete="off" class="layui-input" type="text">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">优惠码库存</label>
                        <div class="layui-input-block">
                            <input name="code_stock" lay-verify="required|number|css_int" value="{{$detail['code_stock']??''}}" autocomplete="off" class="layui-input" type="text">
                        </div>
                    </div>
                    
                    <div class="layui-form-item">
                        <label class="layui-form-label">优惠码条件</label>
                        <div id="rule" class="layui-inline">
                        	@if($detail['getType']['type'] == 'code_product_discount')
                            <div class="layui-input-inline">
                                <input class="layui-input" lay-verify="required|int_percent|css_bigger_ten" name="product_discount" placeholder="百分比折扣" value="{{$detail['product_discount']??''}}" type="text">
                            </div>
                            @endif
                        	@if($detail['getType']['type'] == 'code_full_reduction_of_order')
                        	<div class="layui-form-mid">满</div>
                            <div class="layui-input-inline">
                                <input class="layui-input" lay-verify="required|number|css_int" name="total_amount[0]" placeholder="满" value="{{$detail['total_amount'][0]??''}}" type="text">
                            </div>
                            <div class="layui-form-mid">减</div>
                            <div class="layui-input-inline">
                                <input class="layui-input" lay-verify="required|number|css_int" name="total_discount[0]" placeholder="减" value="{{$detail['total_discount'][0]??''}}" type="text">
                            </div>
                            <div class="layui-input-inline"><button id="add_more_product_discount" type="button" class="layui-btn">添加</button></div>
                            @isset($detail['total_amount'])
                            @foreach($detail['total_amount'] as $k=>$n)
                            @if($k != 0)
                            <div class="add_more_product"><hr style="background-color:transparent;">
                        	<div class="layui-input-inline">
                            	<input class="layui-input" lay-verify="required|number|css_int" name="total_amount[{{$k}}]" placeholder="满" value="{{$detail['total_amount'][$k]??''}}" type="text">
                            </div>
                            <div class="layui-input-inline">
                                <input class="layui-input" lay-verify="required|number|css_int" name="total_discount[{{$k}}]" placeholder="减" value="{{$detail['total_discount'][$k]??''}}" type="text">
                            </div>
                            <div class="layui-input-inline"><button type="button" class="layui-btn del_product_discount">删除</button></div>
                            </div>
                            @endif
                            @endforeach
                            @endisset
                            @endif
                            @if($detail['getType']['type'] == 'code_order_n_discount')
                            <div class="layui-form-mid">每满</div>
                            <div class="layui-input-inline">
                                <input class="layui-input" lay-verify="required|number|css_int" name="step_amount[0]" placeholder="每满" value="{{$detail['step_amount'][0]??''}}" type="text">
                            </div>
                            <div class="layui-form-mid">减</div>
                            <div class="layui-input-inline">
                                <input class="layui-input" lay-verify="required|number|css_int" name="step_discount[0]" placeholder="减" value="{{$detail['step_discount'][0]??''}}" type="text">
                            </div>
                            @isset($detail['step_amount'])
                            @foreach($detail['step_amount'] as $k=>$n)
                            @if($k != 0)
                            <div class="add_more_product"><hr style="background-color:transparent;">
                        	<div class="layui-input-inline">
                            	<input class="layui-input" lay-verify="required|number|css_int" name="step_amount[{{$k}}]" placeholder="每满" value="{{$detail['step_amount'][$k]??''}}" type="text">
                            </div>
                            <div class="layui-input-inline">
                                <input class="layui-input" lay-verify="required|number|css_int" name="step_discount[{{$k}}]" placeholder="减" value="{{$detail['step_discount'][$k]??''}}" type="text">
                            </div>
                            <div class="layui-input-inline"><button type="button" class="layui-btn del_product_discount">删除</button></div>
                            </div>
                            @endif
                            @endforeach
                            @endisset
                            @endif
                            @if($detail['getType']['type'] == 'code_n_piece_n_discount')
                            <div class="layui-form-mid">N件</div>
                            <div class="layui-input-inline">
                                <input class="layui-input" lay-verify="required|number|int_percent" name="nn_n[0]" placeholder="n件" value="{{$detail['nn_n'][0]??''}}" type="text">
                            </div>
                            <div class="layui-form-mid">N折</div>
                            <div class="layui-input-inline">
                                <input class="layui-input" lay-verify="required|number|int_percent|css_bigger_ten" name="nn_discount[0]" placeholder="百分比折扣" value="{{$detail['nn_discount'][0]??''}}" type="text">
                            </div>
                            <div class="layui-input-inline"><button id="add_more_product_discount" type="button" class="layui-btn">添加</button></div>
                            @isset($detail['nn_n'])
                            @foreach($detail['nn_n'] as $k=>$n)
                            @if($k != 0)
                            <div class="add_more_product"><hr style="background-color:transparent;">
                        	<div class="layui-input-inline">
                            	<input class="layui-input" lay-verify="required|number|int_percent" name="nn_n[{{$k}}]" placeholder="n件" value="{{$detail['nn_n'][$k]??''}}" type="text">
                            </div>
                            <div class="layui-input-inline">
                                <input class="layui-input" lay-verify="required|number|int_percent|css_bigger_ten" name="nn_discount[{{$k}}]" placeholder="百分比折扣" value="{{$detail['nn_discount'][$k]??''}}" type="text">
                            </div>
                            <div class="layui-input-inline"><button type="button" class="layui-btn del_product_discount">删除</button></div>
                            </div>
                            @endif
                            @endforeach
                            @endisset
                            @endif
                            @if($detail['getType']['type'] == 'code_gift')
                            <div class="layui-input-inline">
                                <select lay-verify="required" id="condition_type" lay-filter="condition_type" name="condition_type" >
                                	<option value="">赠送条件</option>
                                	<option {{(isset($detail['gift_amount']) and $detail['gift_amount']) ?'selected':'' }} value="1">满足多少元</option>
                                	<option {{(isset($detail['gift_n']) and $detail['gift_n']) ?'selected':'' }} value="2">满足多少件</option>
                                </select>
                            </div>    
                            @endif
                        </div>
                    </div>
                    @if($detail['getType']['type'] == 'code_gift')
                    <div class="layui-form-item " id="condition_value" style="display:{{((isset($detail['gift_n']) and $detail['gift_n']) or (isset($detail['gift_amount']) and $detail['gift_amount']) ) ?'block':'none' }};">
                        <div class="layui-form-item">
                            <label class="layui-form-label">选择步长</label>
                            <div class="layui-input-block">
                                <input name="is_step" type="hidden" value="0">
                                <input name="is_step" title="条件叠加" @if(!empty($detail['is_step'])) checked="" @endif type="checkbox" value="1">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">条件值</label>
                            <div class="layui-input-block">
                                <div id="div_gift_amount" class="layui-input-inline" style="display:{{(isset($detail['gift_amount']) and $detail['gift_amount']) ?'block':'none' }};">
                                    <input class="layui-input" lay-verify="gift" id="gift_amount" name="gift_amount" placeholder="满" value="{{$detail['gift_amount']??''}}" type="text">
                                </div>
                                <div id="div_gift_n" class="layui-input-inline" style="display:{{(isset($detail['gift_n']) and $detail['gift_n']) ?'block':'none' }};">
                                    <input class="layui-input" lay-verify="gift" id="gift_n" name="gift_n" placeholder="件" value="{{$detail['gift_n']??''}}" type="text">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">赠品</label>
                        <div class="layui-input-block">
                        	<input class="layui-input" lay-verify="required" id="gwp_skus" name="gwp_skus" placeholder="skus,逗号分隔" value="{{$detail['gwp_skus']??''}}" type="text">
                        </div>
                    </div>
                    @endif
                    @if($detail['getType']['type'] != 'product_coupon')
                        <div class="layui-form-item">
                            <label class="layui-form-label">全场</label>
                            <div class="layui-input-block">
                                <input name="is_whole" type="hidden" value="0">
                                <input name="is_whole" title="选择" @if(!empty($detail['is_whole'])) checked="" @endif type="checkbox" value="1">
                            </div>
                        </div>
                    @endif
					<div class="layui-form-item" style="margin-top: 5px;">
                        <label class="layui-form-label">系列</label>
                        <div style="margin-bottom: 10px;" class="layui-input-block">
                            <input type="checkbox" lay-filter="allChoose" title="全选" />
                        </div>
                        <div class="layui-input-block seach-box" >
                        	@foreach($categoryData as $cats)
                        	<input type="checkbox" {{in_array($cats['id'],$detail['cids_arr'])?'checked':''}} lay-filter="cats" name="cats[{{$cats['id']}}]" title="{{$cats['name']}}">
                            @endforeach
                        </div>
                    </div>

                    <div class="layui-form-item" style="margin-top: 5px;">
                        <label class="layui-form-label">增加商品</label>
                        <div class="layui-input-block">
                            <input name="add_sku" value="{{$detail['add_sku']??''}}" placeholder="产品ID,逗号分割" class="layui-input"/>
                        </div>
                    </div>
                    <div class="layui-form-item" style="margin-top: 5px;">
                        <label class="layui-form-label">排除商品</label>
                        <div class="layui-input-block">
                            <input name="exclude_sku" value="{{$detail['exclude_sku']??''}}" class="layui-input"/>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">有效期</label>
                        <div class="layui-inline">
                            <div class="layui-input-inline">
                                <input class="layui-input" lay-verify="required" id="start_time" name="start_time" placeholder="开始日期" value="{{$detail['start_time']??''}}" type="text">
                            </div>
                            <div class="layui-input-inline">
                                <input class="layui-input" lay-verify="required" id="end_time" name="end_time" placeholder="结束日期" value="{{$detail['end_time']??''}}" type="text">
                            </div>
                        </div>
                    </div>
                    
                    <div class="layui-form-item">
                        <label class="layui-form-label">叠加</label>
                        <div class="layui-input-block" id="div-addloading">
                            <div id="addloading">点击加载</div>
                        </div>
                    </div>


                    @if(isset($detail['id']))
                        <input type="hidden" name="id" value="{{$detail['id']}}">
                    @endif

					
                    <div class="layui-form-item">
                        <div class="layui-input-block" >
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
    form.on('submit(formSubmit)', function (data) {
        console.log(data);
        $.post("{{ route('backend.promotion.cart.post') }}", data.field, function (res) {
            if (res.code != 1) {
                layer.open({
                    type: 1
                    ,offset: ['30%','40%'] //具体配置参考：http://www.layui.com/doc/modules/layer.html#offset
                    ,id: 'csslayerDemo' //防止重复弹出
                    ,content: '<div style="padding: 20px;">'+ res.msg +'</div>'
                    ,btnAlign: 'c' //按钮居中
                    ,shade: 0 //不显示遮罩
                    ,yes: function(){
                        layer.closeAll();
                    }
                });
                return false;
            }
            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
            parent.layer.close(index); //再执行关闭
        }, 'json');
        return false;
    });

    //商品折扣
    @if($detail['getType']['type'] == 'code_n_piece_n_discount')
    var counter = {{isset($detail['nn_n'])?count($detail['nn_n']):1}};
    lay("#add_more_product_discount").on('click',function(e){
    	var str = ('<div class="add_more_product"><hr style="background-color:transparent;"><div class="layui-input-inline">');
		 str = str + ('<input class="layui-input" lay-verify="required|number|int_percent" name="nn_n['+ counter +']" placeholder="n件" value="" type="text">');
		 str = str + ('</div>');
		 str = str + ('<div class="layui-input-inline">');
		 str = str + ('<input class="layui-input" lay-verify="required|number|int_percent|css_bigger_ten" name="nn_discount['+ counter +']" placeholder="百分比折扣" value="" type="text">');
		 str = str + ('</div>');
		 str = str + '<div class="layui-input-inline"><button type="button" class="layui-btn del_product_discount">删除</button></div>';
		 str = str + '</div>';
		$('#rule').append(str);
    	form.render();
    	counter++;
    	lay('.del_product_discount').on('click',function(e){
			$(this).parent().parent().remove();
    		form.render();
    		--counter;
    		});
	});
    lay('.del_product_discount').on('click',function(e){
		$(this).parent().parent().remove();
		form.render();
		--counter;
		});
	@endif
	
    //满减
    @if($detail['getType']['type'] == 'code_full_reduction_of_order')
	var counter = {{isset($detail['total_amount'])?count($detail['total_amount']):1}};
    lay("#add_more_product_discount").on('click',function(e){
    	var str = ('<div class="add_more_product"><hr style="background-color:transparent;"><div class="layui-input-inline">');
		 str = str + ('<input class="layui-input" lay-verify="required|number|css_int" name="total_amount['+ counter +']" placeholder="满" value="" type="text">');
		 str = str + ('</div>');
		 str = str + ('<div class="layui-input-inline">');
		 str = str + ('<input class="layui-input" lay-verify="required|number|css_int" name="total_discount['+ counter +']" placeholder="减" value="" type="text">');
		 str = str + ('</div>');
		 str = str + '<div class="layui-input-inline"><button type="button" class="layui-btn del_product_discount">删除</button></div>';
		 str = str + '</div>';
		$('#rule').append(str);
    	form.render();
    	counter++;
    	lay('.del_product_discount').on('click',function(e){
			$(this).parent().parent().remove();
    		form.render();
    		--counter;
    		});
	});
    lay('.del_product_discount').on('click',function(e){
		$(this).parent().parent().remove();
		form.render();
		--counter;
		});
	@endif;

	//每满减
    @if($detail['getType']['type'] == 'code_order_n_discount')
	var counter = {{isset($detail['step_amount'])?count($detail['step_amount']):1}};
    lay("#add_more_product_discount").on('click',function(e){
    	var str = ('<div class="add_more_product"><hr style="background-color:transparent;"><div class="layui-input-inline">');
		 str = str + ('<input class="layui-input" lay-verify="required|number|css_int" name="step_amount['+ counter +']" placeholder="满" value="" type="text">');
		 str = str + ('</div>');
		 str = str + ('<div class="layui-input-inline">');
		 str = str + ('<input class="layui-input" lay-verify="required|number|css_int" name="step_discount['+ counter +']" placeholder="减" value="" type="text">');
		 str = str + ('</div>');
		 str = str + '<div class="layui-input-inline"><button type="button" class="layui-btn del_product_discount">删除</button></div>';
		 str = str + '</div>';
		$('#rule').append(str);
    	form.render();
    	counter++;
    	lay('.del_product_discount').on('click',function(e){
			$(this).parent().parent().remove();
    		form.render();
    		--counter;
    		});
	});
    lay('.del_product_discount').on('click',function(e){
		$(this).parent().parent().remove();
		form.render();
		--counter;
		});
	@endif;
    
	//选择不同的优惠码
    form.on('select(type_list)', function(data){
  	  	console.log(data);
  	  	var value = data.value;
  	  	var url = "{{ route('backend.promotion.cart.edit') }}?type="+value;
    	var index = parent.layer.getFrameIndex(window.name)
    	parent.layer.iframeSrc(index,url);
  	});

    //condition_type
    form.on('select(condition_type)', function(data){
  	  	console.log(data);
  	  	var value = data.value;
		$('#gift_amount').val('');
  		$('#gift_n').val('');
  	  	if(!value){
			$('#condition_value').hide();
			return;
  	  	}
    	if(value == '1'){
			$('#div_gift_amount').show();
			$('#div_gift_n').hide();
    	}
    	if(value == '2'){
			$('#div_gift_amount').hide();
			$('#div_gift_n').show();
    	}
    	$('#condition_value').show();
  	});

    form.on('checkbox(cats)', function(data){
  	  console.log(data.value);
  	});

    form.on('checkbox(allChoose)', function (data) {
        var child = $(".seach-box input[type='checkbox']");
        child.each(function (index, item) {
            item.checked = data.elem.checked;
        });
        form.render('checkbox');
    });

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
		var data = {start_time:start_time,end_time:end_time,type:type};
		var addrules = [];
		@isset($detail['addrules'])
		addrules = [ "{!!implode('","',$detail['addrules'])!!}" ];
		@endisset
		$.post("{{ route('backend.promotion.cart.getCrossRules') }}", data, function (res) {
            $('#div-addloading input').remove('.dynamiccheckout');//layui-form-checkbox
    		$('#div-addloading div').remove('.layui-form-checkbox');//
    		var checked = '';

            //增加叠加全选
            $('#div-addloading input').remove('#checkall-addloading');
            $('#div-addloading').append('<input type="checkbox" id="checkall-addloading" lay-filter="allChoose-addloading" title="全选" />');

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
	lay("input[name='end_time']").on('blur', function (e) {
        loadCrossRules();
    });
	loadCrossRules();

    //增加叠加全选
    form.on('checkbox(allChoose-addloading)', function (data) {
        var child = $("#div-addloading input[type='checkbox'].dynamiccheckout");
        child.each(function (index, item) {
            item.checked = data.elem.checked;
        });
        form.render('checkbox');
    });

    //生成随机码
    lay("#make_code_code").on('click',function(e){
        var rand = randomCodeCode(3,20);
        $("input[name='code_code']").val(rand)
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
			end_time:function (value,item){
				var start_time = $('#start_time').val();
				var end_time = value;
				start_time = new Date(Date.parse(start_time));
				end_time = new Date(Date.parse(end_time));
// 				console.log(start_time);
// 				console.log(end_time);
				if(start_time > end_time){
					return '结束日期不对';	
				}
			},
			int_percent:function (value,item){
				var percent = value;
				var reg = new RegExp("^(\\d|[1-9]\\d)$");
			    if(!reg.test(percent) || parseInt(percent) < 1) {
				    return '请填入1-99的整数';
			    }
			},
			css_int: [/^\d+$/,'请输入正确的整数'],
			css_bigger_ten:function(value,item){
				if(parseInt(value) < 10){
					return '不能小于10';
				}
			},
			code_len:function (value,item){
				var len = parseInt(value);
			    if(len < 3 || len > 49) {
				    return '请填入3-49的整数';
			    }
			},
			len10:function(value,item){
				if(value.toString().length > 10){
					return '只能输入10个字符';
				}
			},
			gift:function (value,item){
				var gift_n = $('#gift_n').val();
				var gift_amount = $('#gift_amount').val();
				if(!gift_amount && !gift_n){
					return '必填一个规则条件';
				}
				var reg = new RegExp("^\\d+$");
				if(value){
					if(!reg.test(value) || parseInt(value) < 1) {
					    return '请填入整数';
				    }
				}
			},
            code_code:function (value,item){
                if(value!=null && value!=""){
                    var reg = new RegExp("^[0-9a-zA-Z]{3,20}$");
                    if(value){
                        if(!reg.test(value) || parseInt(value) < 1) {
                            return '只允许输入英文或数字';
                        }
                    }
                }
            },
        }
    );

    function randomCodeCode(min, max){
        var str = "",
        title = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
        arr = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
        range = Math.round(Math.random() * (max-min)) + min;
        for(var i=0; i<range; i++){
            if(i==0){
                pos = Math.round(Math.random() * (title.length-1));
            }else{
                pos = Math.round(Math.random() * (arr.length-1));
            }
            str += arr[pos];
        }
        return str;
    }
    @endsection
</script>
