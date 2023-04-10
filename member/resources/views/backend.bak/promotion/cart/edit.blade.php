@extends('backend.base')

@section('content')
    <div class="layui-col-md12" id="promotion_cart">
        <div class="layui-card">
            <div class="layui-card-body" style="padding: 15px;">
                <form id="add_form" class="layui-form" action="">
                    <div class="layui-form-item">
                        <label class="layui-form-label">规则名称</label>
                        <div class="layui-input-block">
                            <input name="name" lay-verify="required" value="{{$detail['name']??''}}" autocomplete="off"
                                   class="layui-input" type="text">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">规则类型 </label>
                        <div class="layui-input-block">
                            <select v-model="rule.type"  @click="clean" name="type" lay-ignore>
                                <option v-for="(v,k) in rule_type" :value="k">${v}</option>
                            </select>
                        </div>
                    </div>


                    <template v-if="rule.type=='full_reduction_of_order'">
                        <div class="layui-form-item">
                            <label class="layui-form-label">订单金额</label>
                            <div class="layui-input-block">
                                <input v-model="rule.subtoal" lay-verify="required|number" class="layui-input" class="layui-input"/>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">折扣金额</label>
                            <div class="layui-input-block">
                                <input v-model="rule.discount" lay-verify="required|number" class="layui-input" class="layui-input"/>
                            </div>
                        </div>



                    </template>


                    <template v-if="rule.type=='order_discount'">
                        <div class="layui-form-item">
                            <label class="layui-form-label">折扣比例</label>
                            <div class="layui-input-block">
                                <el-input-number v-model="rule.discount" @change="handleChange" :min="0.1" :max="1" step="0.1" label="描述文字"></el-input-number>
                            </div>
                        </div>



                    </template>
                    <template v-if="rule.type=='n_piece_n_discount'">
                        <div class="layui-form-item">
                            <label class="layui-form-label">N件</label>
                            <div class="layui-input-block">
                                <input v-model="rule.piece" lay-verify="required|int" class="layui-input" class="layui-input"/>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">N折</label>
                            <div class="layui-input-block">
                                <el-input-number v-model="rule.discount"  @change="handleChange" :min="0.1" :max="1" step="0.1" label="描述文字"></el-input-number>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">SKU</label>
                            <div class="layui-input-block">
                                <el-transfer
                                        v-model="rule.sku"
                                        :data="productArray"
                                        filterable
                                        filter-placeholder="请输入商品SKU"
                                        :titles="['源', '目标']"
                                        :filter-method="filterSku"
                                        :props="{
                                          key: 'sku',
                                          label: 'name'
                                        }"
                                ></el-transfer>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">分类</label>
                            <div class="layui-input-block">
                                <el-transfer
                                        v-model="rule.category"
                                        :data="categoryArray"
                                        filterable
                                        filter-placeholder="请输入分类名称"
                                        :titles="['源', '目标']"
                                        :filter-method="filterCategory"
                                        :props="{
                                          key: 'id',
                                          label: 'name'
                                        }"
                                ></el-transfer>
                            </div>
                        </div>
                    </template>
                    <template v-if="rule.type=='is_free_handwork'">
                        <label class="layui-form-label">是否免手工费</label>
                        <div class="layui-input-block">
                            <el-radio v-model="rule.free" label="1">是</el-radio>
                            <el-radio v-model="rule.free" label="0">否</el-radio>
                        </div>
                    </template>


                    <div class="layui-form-item" style="margin-top: 5px;">
                        <label class="layui-form-label">使用类型</label>
                        <div class="layui-input-block">
                            <select name="type_of_use" v-model="type_of_use" lay-ignore @change="coupon_group='';">
                                <option v-for="(v,k) in type_of_use_option" :value="k">${v}</option>
                            </select>
                        </div>
                    </div>
                    <div v-show="type_of_use=='coupon_group'">
                        <label class="layui-form-label">优惠劵标签</label>
                        <div class="layui-input-block">
                            <select name="coupon_group" v-model="coupon_group" lay-ignore>
                                <option v-for="v in couponTag" :value="v.key">${v.label}</option>
                            </select>
                        </div>
                    </div>
					<div class="layui-form-item">
                        <label class="layui-form-label">状态</label>
                        
                        <div class="layui-input-block" style="line-height: 38px;">
                       	 	开启<input class="status" type="radio" value="1" name="status" {{isset($detail['status']) && $detail['status']?'checked':''}} lay-ignore/>
                       	 	关闭<input class="status" type="radio" value="0" name="status" {{isset($detail['status']) &&  !$detail['status']?'checked':''}} lay-ignore/>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">权重</label>
                        <div class="layui-input-block">
                            <input name="priority" lay-verify="required|number" value="{{$detail['priority']??''}}"
                                   autocomplete="off" class="layui-input" type="text">
                        </div>
                    </div>


                    @if(isset($detail['id']))
                        <input type="hidden" name="id" value="{{$detail['id']}}">
                    @endif


                    <div class="layui-form-item">
                        ${rule}
                        <textarea name="content" style="display: none;">${rule}</textarea>
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
    form.on('submit(formSubmit)', function (data) {
        $.post("{{ route('backend.promotion.cart.post') }}", data.field, function (res) {
            if (res.code != 0) {
                layer.msg(res.msg, {icon: 5, anim: 6});
                return false;
            }
            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
            parent.layer.close(index); //再执行关闭
        }, 'json');
        return false;
    });

    //自定义验证规则
    form.verify({int: [                  //validateMoney:自定义的验证方式名
            /^\d+$/                   //正整数 的正则表达式
            ,'请输入正确的整数'
        ]});


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


    @endsection
</script>
@section('script')
    <style>
        .layui-form input[type="checkbox"], .layui-form input[type="radio"], .layui-form select {
            display: block;
            width: 100%;
            height: 38px;
            line-height: 1.3;
            line-height: 38px \9;
            border-width: 1px;
            border-style: solid;
            background-color: #fff;
            border-radius: 2px;
        }

        .layui-form-select, .layui-form-select,.layui-icon.layui-icon-ok,.layui-unselect.layui-form-checkbox,.layui-unselect.layui-form-radio.layui-form-radioed,.layui-unselect.layui-form-radio{
            display: none;
        }

        .el-checkbox{
            display: block !important;
        }

        .rule-detail {
            border:1px solid #e8e8e8;
        }
        .el-icon-minus::before,.el-icon-plus::before{
            height: 38px;
            line-height: 38px;
        }
        input.status{
	       display:inline !important;;
        	width:auto !important;
        	height:auto !important;
        	line-height:38px;
        }
    </style>
    <script src="{{ url('/lib/vue.js') }}"></script>
    <script src="{{ url('/lib/element_ui.js') }}"></script>
    <link rel="stylesheet" href="{{ url('/lib/app/index.css') }}"/>
    <script type="text/javascript">
        var app = new Vue({
            el: '#promotion_cart',
            delimiters: ['${', '}'],
            data: function () {
                return {
                    productArray: <?php echo json_encode($productData)?>,
                    categoryArray:<?php echo json_encode($categoryData)?>,
                    couponTag:<?php echo json_encode($detail['coupon_tag_options'])?>,
                    'rule':<?php echo $detail['content'] ?? "{}";?>,
                    "rule_type":<?php echo json_encode($detail['rule_type_options'])?>,
                    "type_of_use_option":<?php echo json_encode(['auto'=>'自动','coupon_group'=>'购物卷标签'])?>,
                    'type_of_use': '{{$detail['type_of_use']??"''"}}',
                    'coupon_group': '{{$detail['coupon_group']??"''"}}',

                }
            },
            mounted: function () {

            },
            methods: {
                filterSku(query, item) {
                    return item.sku.indexOf(query) > -1;
                },
                filterCategory(query, item) {
                    return item.name.indexOf(query) > -1;
                },
                clean: function () {
                    for (var key in this.rule) {
                        if (key !== 'type') {
                            delete this.rule[key];
                        }

                    }
                },
                addCondition: function (rule) {
                    promotion_condition = this.$refs.promotion_condition.value;
                    var item = this.newCondition(promotion_condition);
                    if (!rule.hasOwnProperty('condition')) {
                        this.$set(rule, "condition", []);
                    }
                    rule.condition.push(item);

                },
                newCondition: function (promotion_condition) {
                    return {
                        'name': promotion_condition,
                        'operational': '',
                        'value': [],
                        'flag': false,
                    };
                },
                removeCondition: function (rule, index) {
                    rule.splice(index, 1);
                },
                selectSku: function () {
                    this.$alert('<strong>这是 <i>HTML</i> 片段</strong>', 'HTML 片段', {
                        dangerouslyUseHTMLString: true
                    });
                }

            }


        });
    </script>
@endsection