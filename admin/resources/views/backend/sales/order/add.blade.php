@extends('backend.base')
<style>
    td span {
        padding-left: 10px;
        padding-right: 10px;
    }
</style>
@section('content')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body" style="padding: 15px;">
                <form id="add_form" class="layui-form" action="">

                    <input name="action_type" value="1" autocomplete="off"
                           class="layui-input" type="hidden">
                    <input name="return_type"  value="1" autocomplete="off"
                           class="layui-input" type="hidden">
                    <input name="after_ids"  value="" autocomplete="off"
                           class="layui-input" type="hidden">
                    <input name="pos_id"  value="{{$order['pos_id']}}" autocomplete="off"
                           class="layui-input" type="hidden">
                    <input name="order_sn"  value="{{$order['order_sn']}}" autocomplete="off"
                           class="layui-input" type="hidden">
                    <input name="order_id"  value="{{$order['id']}}" autocomplete="off"
                           class="layui-input" type="hidden">
                    <input name="total_pay_amount" lay-verify="required" value="{{$order['total_amount']}}"
                           autocomplete="off" class="layui-input" type="hidden">
                    <div class="layui-form-item">
                        <label class="layui-form-label">快递类型</label>
                        <div class="layui-input-inline">
                            <select name="express_type" >
                                <option value="0">无</option>
                                <option value="1">顺丰</option>
                                <option value="2">申通</option>
                                <option value="3">EMS</option>
                                <option value="4">宅急送</option>
                                <option value="5">百世汇通</option>
                                <option value="6">天天快递</option>
                                <option value="7">韵达</option>
                                <option value="8">JD</option>
                            </select>
                        </div>
                        <label class="layui-form-label">快递单号</label>
                        <div class="layui-input-inline">
                            <input name="express_no"  value="" autocomplete="off"
                                   class="layui-input" type="text" lay-verify="">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">支付类型</label>
                        <div class="layui-input-inline">
                            <select name="payment_type" >
                                    <option value="0" @if( $order['payment_type']==0) selected @endif></option>
                                    <option value="1" @if( $order['payment_type']==1) selected @endif>支付宝支付</option>
                                    <option value="2" @if( $order['payment_type']==2) selected @endif>微信支付</option>
                                    <option value="3" @if( $order['payment_type']==3) selected @endif>银联支付</option>
                                    <option value="4" @if( $order['payment_type']==4) selected @endif>花呗支付</option>
                                    <option value="5" @if( $order['payment_type']==5) selected @endif>货到付款</option>
                                    <option value="6" @if( $order['payment_type']==6) selected @endif>微信小程序</option>
                                </select>
                            </select>
                        </div>
                        @if( $order['payment_type']!=5)
                        <label class="layui-form-label">详细支付类型</label>
                        <div class="layui-input-inline">

                            <select name="trade_type" >
                                <option value="0" @if( $order['trade_type']==0) selected @endif></option>
                                <option value="1" @if( $order['trade_type']==1) selected @endif>微信支付js</option>
                                <option value="2" @if( $order['trade_type']==2) selected @endif>web支付宝支付</option>
                                <option value="3" @if( $order['trade_type']==3) selected @endif>花呗支付Huabai</option>
                                <option value="4" @if( $order['trade_type']==4) selected @endif>银联支付</option>
                                <option value="5" @if( $order['trade_type']==5) selected @endif>货到付款</option>
                                <option value="6" @if( $order['trade_type']==6) selected @endif>微信小程序</option>
                                <option value="7" @if( $order['trade_type']==7) selected @endif>native微信扫码</option>
                                <option value="8" @if( $order['trade_type']==8) selected @endif>h5mweb</option>
                                <option value="9" @if( $order['trade_type']==9) selected @endif>pc支付宝</option>
                            </select>
                        </div>
                        @endif
                    </div>

                    <div class="layui-form-item layui-inline">
                        <label class="layui-form-label">退款金额</label>
                        <div class="layui-input-inline">
                            <input name="refund_amount" lay-verify="required" value="{{$order['total_amount']}}"
                                   autocomplete="off" class="layui-input" type="text">
                        </div>
                        <label class="layui-form-label">原价</label>
                        <div class="layui-input-inline">
                            <input name="goods_amount" lay-verify="required" value="{{$order['total_product_price']}}"
                                   autocomplete="off" class="layui-input" type="text">
                        </div>
                        <label class="layui-form-label">订单金额</label>
                        <div class="layui-input-inline">
                            <input name="pay_amount" lay-verify="required" value="{{$order['total_amount']}}"
                                   autocomplete="off" class="layui-input" type="text">
                        </div>
{{--                        <button class="layui-btn get-total">计算选中商品金额</button>--}}

                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">商品信息</label>
                        <div class="layui-input-block">
                            <!-- 赠品 -->
                            <table class="layui-table" lay-skin="line" lay-size="" lay-filter="detail">

                                @foreach($order['gift_goods'] as $goods)
                                    {{--                                      <div class="test">--}}
                                    @foreach($goods as $item)
                                        @if(($item['is_gift'] ==0 && $item['is_free']==0 && $item['type']==1) || ($item['type']>1 && !empty($item['collections'])))
                                            <tr>
                                                <td><input type="checkbox" name="item_id[]" lay-skin="primary"
                                                           lay-filter="sku-id" value="{{json_encode($item)}}"
                                                           class="number{{$item['id']}}" checked></td>
                                                <td><img src="{{$item['pic']}}" style="width: 50px;"/></td>
                                                <td>{{$item['name']}}(规格： {{$item['spec_desc']}})
                                                    x {{$item['qty']}}</td>
                                                <td>{{$item['original_price']}}</td>
                                                <td>{{$item['order_amount_total']}}</td>
                                                <td>
                                                    @if($item['type'] ==1) 普通 @endif
                                                    @if($item['type'] ==2) 组合套装 @endif
                                                    @if($item['type'] ==3) 固定套装 @endif
                                                </td>
                                                {{--                                                <td>--}}
                                                {{--                                                    <input name="refund_num[{{$item['id']}}]" lay-verify="required" value="{{$item['qty']}}" autocomplete="off" class="layui-input number{{$item['id']}}" type="text" style="width:100px;">--}}
                                                {{--                                                </td>--}}
                                                <td>
                                                    @if(isset($item['applied_rule_ids']) && is_array($item['applied_rule_ids']))
                                                    @foreach($item['applied_rule_ids'] as $rule)

                                                        优惠规则： {{$rule['rule_name']}} {{$rule['discount']??''}}
                                                        <br>

                                                    @endforeach
                                                    @endif
                                                </td>

                                            </tr>
                                        @endif
                                        @if($item['type'] ==2 && empty($item['collections']))
                                            <tr>
                                                <td><input type="checkbox" name="item_id[]" lay-skin="primary" lay-filter="sku-id" data-qty = "{{$item['qty']}}" value="{{json_encode($item)}}" class="number{{$item['id']}}" checked></td>
                                                <td colspan="7">
                                                    <span style="margin-left:50px"><img src="{{$item['pic']}}"
                                                                                        style="width: 50px;"/></span>
                                                    <span>{{$item['name']}}(规格： {{$item['spec_desc']}})x {{$item['qty']}}</span>
                                                    <span>原价：{{$item['original_price']}} </span>
                                                    <span> 实际金额：{{$item['order_amount_total']}}</span>
                                                    <span>组合套装商品</span>


                                                </td>
                                                {{--                                                <td colspan="2" >--}}
                                                {{--                                                    <span><input name="refund_num[{{$item['id']}}]"  lay-verify="required" value="{{$item['qty']}}" autocomplete="off" class="layui-input number{{$item['id']}}" type="text" style="width:100px;"></span>--}}

                                                {{--                                                </td>--}}
                                            </tr>

                                        @endif
                                        @if($item['is_gift'] == 1 || $item['is_free'] == 1)
                                            <tr>

                                                <td><input type="checkbox" name="item_id[]" lay-skin="primary" lay-filter="sku-id"  value="{{json_encode($item)}}" class="number{{$item['id']}}" checked></td>

                                                <td colspan="7">

                                                    <span style="margin-left:30px"><img src="{{$item['pic']}}"
                                                                                        style="width: 50px;"/></span>
                                                    <span>{{$item['name']}} (规格：{{$item['spec_desc']}})x {{$item['qty']}}</span>
                                                    <span> 原价：{{$item['original_price']}}</span>
                                                    <span> 实际金额：{{number_format($item['order_amount_total'],2)}}</span>

                                                    <span>
                                                                @if($item['is_free'] ==1) （小样） @endif
                                                        @if($item['is_gift'] ==1) （赠品） @endif
                                                            </span>


                                                </td>

                                            </tr>
                                            @endif
                                            @if($item['is_free'] == 2&&!empty($item['collections']))
                                                <tr>
                                                    <td><input type="checkbox" name="item_id[]" lay-skin="primary" lay-filter="sku-id"  value="{{json_encode($item)}}" class="number{{$item['id']}}" checked></td>
                                                    <td colspan="7" >
                                                        <span style="margin-left:30px"><img src="{{$item['pic']}}" style="width: 50px;"/></span>
                                                        <span>{{$item['name']}} (规格：{{$item['spec_desc']}})x {{$item['qty']}}</span>
                                                        <span> {{number_format($item['order_amount_total'],2)}}</span>

                                                        <span>
                                                                 @if($item['is_free'] == 2) 新人礼 @endif
                                                            </span>



                                                    </td>

                                                </tr>
                                            @endif

                                            @if($item['is_free'] ==2 && empty($item['collections']))
                                                <tr>
                                                    <td><input type="checkbox" name="item_id[]" lay-skin="primary" lay-filter="sku-id"  value="{{json_encode($item)}}" class="number{{$item['id']}}" checked></td>
                                                    <td colspan="7" >
                                                        <span style="margin-left:30px"><img src="{{$item['pic']}}" style="width: 50px;"/></span>
                                                        <span>{{$item['name']}}(规格： {{$item['spec_desc']}})x {{$item['qty']}}</span>
                                                        <span> {{number_format($item['order_amount_total'],2)}}</span>

                                                        <span>
                                                             新人礼组合套装商品
                                                            </span>
                                                    </td>

                                                </tr>

                                            @endif

                                            @if($item['is_gift'] == 2&&!empty($item['collections']))
                                                <tr>
                                                    <td><input type="checkbox" name="item_id[]" lay-skin="primary" lay-filter="sku-id"  value="{{json_encode($item)}}" class="number{{$item['id']}}" checked></td>
                                                    <td colspan="7" >
                                                        <span style="margin-left:30px"><img src="{{$item['pic']}}" style="width: 50px;"/></span>
                                                        <span>{{$item['name']}} (规格：{{$item['spec_desc']}})x {{$item['qty']}}</span>
                                                        <span> {{number_format($item['order_amount_total'],2)}}</span>

                                                        <span>
                                                                 全场赠品
                                                            </span>



                                                    </td>

                                                </tr>
                                            @endif

                                            @if($item['is_gift'] ==2 && empty($item['collections']))
                                                <tr>
                                                    <td><input type="checkbox" name="item_id[]" lay-skin="primary" lay-filter="sku-id"  value="{{json_encode($item)}}" class="number{{$item['id']}}" checked></td>
                                                    <td colspan="7" >
                                                        <span style="margin-left:30px"><img src="{{$item['pic']}}" style="width: 50px;"/></span>
                                                        <span>{{$item['name']}}(规格： {{$item['spec_desc']}})x {{$item['qty']}}</span>
                                                        <span> {{number_format($item['order_amount_total'],2)}}</span>

                                                        <span>
                                                             全场赠品组合套装商品
                                                            </span>
                                                    </td>

                                                </tr>

                                            @endif
                                            @endforeach
                                            @endforeach
                            </table>


                            {{--                            <div class="layui-col-md4 layui-col-md-offset8">--}}
                            {{--                                <table class="layui-table" lay-skin="nob" lay-size="">--}}
                            {{--                                    <colgroup>--}}
                            {{--                                        <col width="150">--}}
                            {{--                                        <col>--}}
                            {{--                                    </colgroup>--}}
                            {{--                                    <tbody>--}}
                            {{--                                    <tr>--}}
                            {{--                                        <td><strong>商品原价：</strong></td>--}}
                            {{--                                        <td>￥{{floor($order['total_product_price'])}}</td>--}}
                            {{--                                    </tr>--}}
                            {{--                                    <tr>--}}
                            {{--                                        <td><strong>商品折后价：</strong></td>--}}
                            {{--                                        <td>￥{{floor($order['total_amount'])}}</td>--}}
                            {{--                                    </tr>--}}
                            {{--                                    <tr>--}}
                            {{--                                        <td><strong>商品包装费：</strong></td>--}}
                            {{--                                        <td>￥{{floor($order['total_wrap_fee'])}}</td>--}}
                            {{--                                    </tr>--}}
                            {{--                                    <tr>--}}
                            {{--                                        <td><strong>商品快递费：</strong></td>--}}
                            {{--                                        <td>￥{{floor($order['total_ship_fee'])}}</td>--}}
                            {{--                                    </tr>--}}
                            {{--                                    <tr>--}}
                            {{--                                        <td><strong>积分抵扣：</strong></td>--}}
                            {{--                                        <td>￥{{floor($order['total_point_discount'])}}</td>--}}
                            {{--                                    </tr>--}}
                            {{--                                    <tr>--}}
                            {{--                                        <td><strong>使用积分：</strong></td>--}}
                            {{--                                        <td>￥{{floor($order['used_points'])}}</td>--}}
                            {{--                                    </tr>--}}
                            {{--                                    <tr>--}}
                            {{--                                        <td><strong>优惠金额：</strong></td>--}}
                            {{--                                        <td>￥{{floor($order['total_discount'])}}</td>--}}
                            {{--                                    </tr>--}}

                            {{--                                    </tbody>--}}
                            {{--                                </table>--}}
                            {{--                            </div>--}}
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label for="" class="layui-form-label">商品退货原因</label>
                        <div class="layui-input-block">
                                           <textarea style="min-height:100px;" placeholder="请输入退货信息"
                                                     class="layui-textarea"
                                                     name="question_desc"
                                                     lay-verify="required"></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label for="" class="layui-form-label">操作备注</label>
                        <div class="layui-input-block">
                                           <textarea style="min-height:100px;" placeholder="请输入备注信息"
                                                     class="layui-textarea"
                                                     name="remark"
                                                     lay-verify="required"></textarea>
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
    //监听指定开关

    // form.on('checkbox(sku-id)', function(data){
    //     var select = data.value;
    //     select =  eval('(' + select + ')');
    //     if(data.elem.checked==true){
    //
    //         if(select.type==2 && select.collections){
    //
    //             $(".layui-table input[type='checkbox']").each(function (index, item) {
    //                     var goods = item.value;
    //                     goods =  eval('(' + goods + ')');
    //                     if(goods.collection_id == select.collection_id){
    //                         $(item).prop("checked",true);
    //                     }
    //
    //             });
    //         }
    //         if(select.type==2 && select.collections==''){
    //
    //             var tag = false;
    //             var obj = null;
    //             $(".layui-table input[type='checkbox']").each(function (index, item) {
    //                 var goods = item.value;
    //                 goods =  eval('(' + goods + ')');
    //                 if(goods.collection_id == select.collection_id && goods.collections) {
    //                     obj = item;
    //                 }
    //                 if(goods.collection_id == select.collection_id && goods.collections==''){
    //                     if(item.checked){
    //                         tag = true;
    //                     }else{
    //                         tag = false;
    //                         return false;
    //                     }
    //                 }
    //
    //
    //             });
    //             if(tag){
    //                 $(obj).prop("checked",true);
    //             }
    //
    //         }
    //
    //
    //     }else{
    //         if(select.type==2 && select.collections){
    //
    //             $(".layui-table input[type='checkbox']").each(function (index, item) {
    //                 var goods = item.value;
    //                 goods =  eval('(' + goods + ')');
    //                 if(goods.collection_id== select.collection_id){
    //                     $(item).prop("checked",false);
    //                 }
    //
    //             });
    //         }
    //         if(select.type==2 && select.collections==''){
    //
    //             var obj = null;
    //             $(".layui-table input[type='checkbox']").each(function (index, item) {
    //                 var goods = item.value;
    //                 goods =  eval('(' + goods + ')');
    //                 if(goods.collection_id == select.collection_id && goods.collections) {
    //                     obj = item;
    //                     return false;
    //                 }
    //
    //             });
    //
    //                 $(obj).prop("checked",false);
    //
    //         }
    //
    //
    //     }
    //
    //     form.render('checkbox');
    //
    // });

    form.on('checkbox(sku-id)', function (data) {
        var select = data.value;
        select =  eval('(' + select + ')');
        if(data.elem.checked==true) {
            if (select.type == 2) {

                $(".layui-table input[type='checkbox']").each(function (index, item) {
                    var goods = item.value;
                    goods = eval('(' + goods + ')');
                    if (goods.collection_id == select.collection_id) {
                        $(item).prop("checked", true);
                    }

                });
            }

        }else{
            if(select.type==2){

                $(".layui-table input[type='checkbox']").each(function (index, item) {
                    var goods = item.value;
                    goods =  eval('(' + goods + ')');
                    if(goods.collection_id== select.collection_id){
                        $(item).prop("checked",false);
                    }

                });
            }

        }
        form.render('checkbox');
        console.log(select);
                 getTotal()

    });

    function getTotal() {
        var origianl_total = 0;
        var total = 0;
        var tota_select = 0;
        var selected = 0;
        var ids = '';
        // var collection = [];
        $(".layui-table input[type='checkbox']").each(function (index, item) {

            if (item.checked == true) {

                var goods = item.value;
                goods = eval('(' + goods + ')');
                // if(goods.type==2 && goods.collections){
                //     collection.push(goods.collection_id)
                // }
                //
                if((goods.type==2 && goods.collections=='') || (goods.type==2 && goods.collections=='[]')|| goods.is_free>0 || goods.is_gift>0){

                }else{
                    total += parseFloat(goods.order_amount_total);
                    origianl_total += parseFloat(goods.original_price);


                }
                selected ++;
                ids = ids + goods.id+',';

            }
            tota_select = index+1;
        });
        if(tota_select == selected){
            $("input[name='return_type']").val(1);
        }else{
            $("input[name='return_type']").val(2);
        }
        // console.log(ids);
        // console.log(tota_select);
        // console.log(selected);

        //return_type
        $("input[name='after_ids']").val(ids);
        $("input[name='goods_amount']").val(origianl_total.toFixed(4));
        $("input[name='pay_amount']").val(total.toFixed(4));
        $("input[name='refund_amount']").val(total.toFixed(4));

    }

    form.on('submit(formSubmit)', function (data) {

        $.post("{{ route('backend.sales.order.after.action') }}", data.field, function (res) {
            if (res.code != 1) {
                layer.msg(res.message, {icon: 5, anim: 6});
                return false;
            } else {
                layer.msg(res.message, function () {
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.layer.close(index); //再执行关闭
                });

            }
        }, 'json');
        return false;
    });

    @endsection
</script>