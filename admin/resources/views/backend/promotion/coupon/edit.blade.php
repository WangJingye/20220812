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
                        <label class="layui-form-label">类型</label>
                        <div class="layui-input-block">
                            <select lay-verify="required" name="type_list" lay-filter="type_list" >
                                @foreach($detail['coupon_type_list'] as $k=>$v)
                                    <option {{$k==$detail['getType']['type']?'selected':''}} value="{{$k}}">{{$v}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">规则名称</label>
                        <div class="layui-input-block">
                            <input name="name" lay-verify="required" value="{{$detail['name']??''}}" autocomplete="off"  class="layui-input" type="text">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">显示名称</label>
                        <div class="layui-input-block">
                            <input name="display_name" lay-verify="required|len10" value="{{$detail['display_name']??''}}" autocomplete="off"  class="layui-input" type="text">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">优先级</label>
                        <div class="layui-input-block">
                            <input name="priority" lay-verify="required|int" placeholder="值越大优先" value="{{$detail['priority']??''}}" autocomplete="off" class="layui-input" type="text">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">库存</label>
                        <div class="layui-input-block">
                            <input name="coupon_stock" lay-verify="required|number|css_int" value="{{$detail['coupon_stock']??''}}" autocomplete="off"
                                   class="layui-input" type="text">
                        </div>
                    </div>
                    @isset($detail['coupon_stock_used'])
                        <div class="layui-form-item">
                            <label class="layui-form-label">领取数量</label>
                            <div class="layui-input-block">
                                {{$detail['coupon_stock_used']??''}}
                            </div>
                        </div>
                    @endisset
                    <div class="layui-form-item {{$detail['getType']['type'] == 'ship_fee_try'?'layui-hide':''}}">
                        <label class="layui-form-label">规则条件</label>
                        <div id="rule" class="layui-inline">
                            @if($detail['getType']['type'] == 'coupon')
                                <div class="layui-input-inline">
                                    <input class="layui-input" lay-verify="required|number|css_int|css_bigger_one" name="total_amount[0]" placeholder="满" value="{{$detail['total_amount'][0]??''}}" type="text">
                                </div>
                                <div class="layui-input-inline">
                                    <input class="layui-input" lay-verify="required|number|css_int" name="total_discount[0]" placeholder="减" value="{{$detail['total_discount'][0]??''}}" type="text">
                                </div>
                            @endif
                            @if($detail['getType']['type'] == 'coupon_discount')
                                <div class="layui-input-inline">
                                    <input class="layui-input" lay-verify="required|number|int_percent|css_bigger_ten" name="product_discount" placeholder="百分比折扣" value="{{$detail['product_discount']??''}}" type="text">
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">全场</label>
                        <div class="layui-input-block">
                            <input name="is_whole" type="hidden" value="0">
                            <input name="is_whole" title="选择" @if(!empty($detail['is_whole'])) checked="" @endif type="checkbox" value="1">
                        </div>
                    </div>
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
                        <label class="layui-form-label">增加商品id</label>
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
                                <input class="layui-input" lay-verify="required|end_time" id="end_time" name="end_time" placeholder="结束日期" value="{{$detail['end_time']??''}}" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">领取后过期天数</label>
                        <div class="layui-inline">
                            <div class="layui-input-inline">
                                <input name="expire_days" lay-verify="dlc_int" value="{{$detail['expire_days']??''}}" class="layui-input"/>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">新人券</label>
                        <div class="layui-input-block">
                            <input name="is_new" type="hidden" value="0">
                            <input name="is_new" title="选择" @if(!empty($detail['is_new'])) checked="" @endif type="checkbox" value="1">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">使用须知</label>
                        <div class="layui-input-block">
                            <textarea name="notice" class="layui-textarea">{{$detail['notice']??''}}</textarea>
                        </div>
                    </div>
                    <div class="layui-form-item" style="margin-top: 5px;">
                        <label class="layui-form-label">触发SKU</label>
                        <div class="layui-input-block">
                            <input name="trigger_sku" value="{{$detail['trigger_sku']??''}}" placeholder="触发自动发券的产品sku,多个用英文逗号分割" class="layui-input"/>
                        </div>
                    </div>

                    <div class="layui-form-item {{$detail['getType']['type'] == 'ship_fee_try'?'layui-hide':''}} ">
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
    //n件n折扣
            @if($detail['getType']['type'] == 'n_piece_n_discount')
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
            ,zIndex:1
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
            dlc_int:function (value,item){
                if(value.toString().length > 0) {
                    var reg = new RegExp("^\\d+$");
                    if(!reg.test(value) || parseInt(value) < 1) {
                        return '请填入整数';
                    }
                }
            },
            css_int: [/^\d+$/,'请输入正确的整数'],
            css_bigger_one:function(value,item){
                if(parseInt(value) == 0){
                    return '不能等于0';
                }
                if(parseInt(value) < 1 ){
                    return '不能等于0';
                }
            },
            css_bigger_ten:function(value,item){
                if(parseInt(value) < 10){
                    return '不能小于10';
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
            }
        }
    );
    @endsection
</script>
