@extends('backend.base')
<link rel="stylesheet" href="{{ url('/static/admin/css/form.css') }}" media="all">
<style>
    .layui-form-item {
        white-space: nowrap !important;
    }
    .layui-form-item .layui-input-block{
        margin-left:20px;
    }
    .layui-form-item .layui-input-inline{
        margin-left:20px;
    }
</style>
@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新SKU</h2>
        </div>
        <div class="layui-tab">
            <ul class="layui-tab-title">
                <li class="layui-this">基本信息</li>
                <li>安全库存</li>
                <li>渠道库存</li>
                <li>库存推送记录</li>

            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <div class="layui-card-body">
                        <form method="post" ref="form" class="layui-form">
                            {{csrf_field()}}
                            <input type="hidden" name="id" value="{{$detail->id}}">
                            <input type="hidden" name="product_idx" value="{{$detail->product_idx}}">
                            <div class="layui-form-item">
                                <label for="" class="layui-form-label">SKU ID</label>
                                <div class="layui-input-block">
                                    <div class=" layui-col-xs7">
                                        <input class="layui-input" type="text" name="sku_id" lay-verify="required"
                                               value="{{$detail->sku_id}}" readonly disabled="disabled">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label for="" class="layui-form-label">原价</label>
                                <div class="layui-input-block">
                                    <div class=" layui-col-xs5">
                                        <input class="layui-input" type="text" name="ori_price" lay-verify="required"
                                               value="{{$detail->ori_price}}" >
                                    </div>
                                </div>
                            </div>
                            <div style="display: none" class="layui-form-item">
                                <label for="" class="layui-form-label">尺寸</label>
                                <div class="layui-input-block">
                                    <div class=" layui-col-xs5">
                                        <input class="layui-input" type="text" name="size"
                                               value="{{$detail->size}}" >
                                    </div>
                                </div>
                            </div>
                            <div style="display: none" class="layui-form-item">
                                <label for="" class="layui-form-label">税收类型</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="revenue_type" value="1" title="护肤用化妆品" @if($detail->revenue_type == 1) checked @endif  >
                                    <input type="radio" name="revenue_type" value="2" title="护发用化妆品" @if($detail->revenue_type == 2) checked @endif>
                                    <input type="radio" name="revenue_type" value="3" title="刷子类制品" @if($detail->revenue_type == 3) checked @endif>
                                    <input type="radio" name="revenue_type" value="4" title="美容修饰类化妆品" @if($detail->revenue_type == 4) checked @endif>
                                </div>
                            </div>
                            <div style="display: none" class="layui-form-item">
                                <label for="" class="layui-form-label J_spec_type1 ">
                                    @if(in_array('color',$detail->spec_type))
                                        色号(规格)
                                    @else
                                        色号
                                    @endif
                                </label>
                                <div class="layui-input-block">
                                    <div class="layui-col-xs2">
                                        <input class="layui-input layui-col-xs5 J_spec" type="text" data-spectype="color" name="spec_color_code"
                                               value="{{$detail->spec_color_code}}" placeholder="色号code码，如：ffffff" >
                                    </div>
                                    <div class="layui-col-xs5" style="margin-left: 10px;">
                                        <input class="layui-input layui-col-xs5 " type="text" name="spec_color_code_desc"
                                               placeholder="请输入色号描述，如：红色" value="{{$detail->spec_color_code_desc}}" >
                                    </div>
                                    <div class="layui-col-xs2">
                                        <button @if(!in_array('color',$detail->spec_type)) style="display: none" @endif  type="button"  class="layui-btn J_spec_check">检测</button>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label for="" class="layui-form-label J_spec_type2">规格1</label>
                                <div class="layui-input-block">
                                    <div class="layui-col-xs2">
                                        <input  class="layui-input layui-col-xs5 J_spec" data-spectype="capacity_ml" type="text" name="spec_capacity_ml_code"
                                                value="{{$detail->spec_capacity_ml_code}}" placeholder="规格code" >
                                    </div>
                                    <div class="layui-col-xs5" style="margin-left: 10px;">
                                        <input class="layui-input layui-col-xs5 " type="text" name="spec_capacity_ml_code_desc"
                                               placeholder="规格描述" value="{{$detail->spec_capacity_ml_code_desc}}" >
                                    </div>
                                <!--
                                <div class="layui-col-xs2">
                                    <button @if(!in_array('capacity_ml',$detail->spec_type)) style="display: none" @endif  type="button"  class="layui-btn J_spec_check">检测</button>
                                </div>
                                -->
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label for="" class="layui-form-label J_spec_type3">规格2</label>
                                <div class="layui-input-block">
                                    <div class="layui-col-xs2">
                                        <input  class="layui-input  J_spec" data-spectype="capacity_g" type="text" name="spec_capacity_g_code"
                                                value="{{$detail->spec_capacity_g_code}}"  placeholder="规格code" >
                                    </div>
                                    <div class="layui-col-xs5" style="margin-left: 10px;">
                                        <input class="layui-input layui-col-xs5 " type="text" name="spec_capacity_g_code_desc"
                                               placeholder="规格描述" value="{{$detail->spec_capacity_g_code_desc}}" >
                                    </div>
                                    <div class="layui-col-xs2">
                                        <button @if(!in_array('capacity_g',$detail->spec_type)) style="display: none" @endif  type="button"  class="layui-btn J_spec_check">检测</button>
                                    </div>
                                </div>
                            </div>

                            <div style="display: none" class="layui-form-item">
                                <label for="" class="layui-form-label">包含SKU</label>
                                <div class="layui-input-block">
                                    <div class=" layui-col-xs5">
                                        <input class="layui-input" type="text" name="contained_sku_ids"
                                               value="{{$detail->contained_sku_ids}}" placeholder="固定礼盒需要设置包含sku，逗号分隔" >
                                    </div>
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label for="" class="layui-form-label">打包SKU</label>
                                <div class="layui-input-block">
                                    <div class=" layui-col-xs5">
                                        <input class="layui-input" type="text" name="include_skus"
                                               value="{{$detail->include_skus}}" placeholder="多个sku逗号分隔,请勿将打包sku的商品作为赠品">
                                    </div>
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label for="" class="layui-form-label">商品</label>
                                <div class="layui-input-block ">
                                    <div class="layui-col-xs8">
                                        <input class="layui-input" type="text" id="J_product_name" name="product_name"
                                               value="{{$detail->product_name??old('product_name')}}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">更新库存</label>
                                <div class="layui-input-block">
                                    <input type="hidden" name="control_stock" value="0" >
                                    <input name="control_stock" title="手动" @if(!empty($detail->control_stock)) checked="" @endif type="checkbox" value="1">
                                </div>
                                <div class="layui-input-block">
                                    <div class="layui-form-mid layui-word-aux">注意选择手动更新库存会导致OMS更新过来的库存无效!!!</div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-input-block">
                                    <button type="submit" class="layui-btn" lay-submit="" lay-filter="confirm">确 认
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="layui-tab-item">
                    <div class="layui-card-body">
                        <form method="post" class="layui-form form2">
                            {{csrf_field()}}

                            <div class="layui-form-item">
                                <label for="" class="layui-form-label">SKU ID</label>
                                <div class="layui-input-block layui-col-xs5">
                                    <input class="layui-input" type="text" name="id" lay-verify="required"
                                           value="{{$detail->sku_id??old('sku_id')}}" readonly disabled="disabled">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">设置安全库存</label>
                                <div class="layui-input-block layui-col-xs5">
                                    <input type="checkbox" {{ $stockinfo['is_secure'] ? 'checked' : ''  }} name="is_secure"
                                           lay-skin="switch" lay-filter="switch1"
                                           lay-text="ON|OFF" value="{{$stockinfo['is_secure']}}">

                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label for="" class="layui-form-label">安全库存数量</label>
                                <div class="layui-input-block layui-col-xs5">
                                    <input class="layui-input" type="text" name="secure" lay-verify="required||Ndouble"
                                           value="{{$stockinfo['secure']??old('secure')}}" {{ $stockinfo['is_secure'] ? '' : 'readonly'}}>
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label for="" class="layui-form-label">安全库存增量</label>
                                <div class="layui-input-block layui-col-xs5">
                                    <input class="layui-input" type="text" name="" value="{{$stockinfo['secureinc']}}" readonly>
                                </div>
                            </div>


                            <div class="layui-form-item">
                                <div class="layui-input-block">
                                    <button type="submit" class="layui-btn" lay-submit="" lay-filter="confirm2">确 认
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="layui-tab-item">
                    <div class="layui-card-body">
                        <form method="post" class="layui-form form3">
                            {{csrf_field()}}

                            <div class="layui-form-item">
                                <label for="" class="layui-form-label">SKU ID</label>
                                <div class="layui-input-block layui-col-xs5">
                                    <input class="layui-input" type="text" name="id" lay-verify="required"
                                           value="{{$detail->sku_id??old('sku_id')}}" readonly disabled="disabled">
                                </div>
                            </div>
                        <div class="layui-form-item layui-block">
                            <div class="layui-form-item layui-block">
                                <label for="" class="layui-form-label">库存</label>
                                <div class="layui-input-block layui-col-xs5">
                                    <input class="layui-input" type="text" name="id"
                                           value="{{$stockinfo['stock']}}" readonly disabled="disabled">
                                </div>
                            </div>
                            <div class="layui-form-item layui-block" >
                                @foreach($stockinfo['info'] as $k=>$v)
                                    <div class="layui-form-item layui-inline">
                                        <label for="" class="layui-form-label">{{$v['name']}}锁定库存</label>
                                        <div class="layui-input-inline" style="margin-left:20px;">
                                            <input class="layui-input" type="text"
                                                   lay-verify="required" disabled="disabled"
                                                   value="{{$stockinfo['lock_channel'.$v['id']]}}" readonly>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div style="display: none" class="layui-form-item">
                                <div class="layui-input-block">
                                    <button type="submit" class="layui-btn" lay-submit="" lay-filter="confirm3">确 认
                                    </button>
                                </div>
                            </div>
                        </div>
                        </form>
                        @if($detail->control_stock==1)
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">库存修改</label>
                            <div class="layui-input-block layui-input-inline">
                                <input class="layui-input" type="text" id="stock_update" value="0">
                                <div class="layui-form-mid layui-word-aux">此处为库存的增量修改,整数为增加负数为扣减!</div>
                            </div>
                            <button id="stock_update_btn" type="button" class="layui-btn layui-btn-normal">
                                <i class="layui-icon">&#xe608;</i> 增量更新库存
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="layui-tab-item">
                    <div class="layui-card-body">
                        <form class="layui-form layui-form-pane" action="">
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <label class="layui-form-label">开始日期</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="startdate" id="startdate" autocomplete="off" class="layui-input date">
                                    </div>
                                </div>
                                <div class="layui-inline">
                                    <label class="layui-form-label">结束日期</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="enddate" id="enddate" autocomplete="off" class="layui-input date">
                                    </div>
                                </div>
                                <span class="layui-inline layui-btn" id="search">搜索</span>
                            </div>

                        </form>
                        <table id="stocklog" lay-filter="list"></table>
                    </div>
                    {{--                    <table class="layui-hide" id="test"></table>--}}
                </div>

            </div>
        </div>
    </div>
@endsection
<script>
    @section('layui_script')

    //注意：选项卡 依赖 element 模块，否则无法进行功能性操作
    layui.use('element', function () {
        var element = layui.element;

        //…
    });

    form.verify({
        Ndouble: [
            /^[0-9]\d*$/
            , '只能输入整数哦'
        ]
    });
    layui.use(['form','upload'], function () {
        var upload = layui.upload;
        //执行实例
        upload.render({
            elem: '#J_thumbnail_button' //绑定元素
            , url: "{{ route('backend.file.uploadPic') }}" //上传接口
            , done: function (res) {
                //如果上传失败
                if (res.code > 0) {
                    return layer.msg('上传失败');
                }
                //上传成功
                var path = res.path;
                $("#J_thumbnail").val(path);
                $("#J_thumbnail_src").attr("src", path).show();
            }
            , error: function () {
                //请求异常回调
            }
        });
        //监听提交
        form.on('submit(confirm)', function (data) {
            $.post("{{ route('backend.goods.sku.edit') }}", data.field, function (res) {
                if (res.code === 1) {
                    layer.msg('操作成功', {
                        icon: 1,
                        shade: 0.3,
                        offset: '300px',
                        time: 2000 //2秒关闭（如果不配置，默认是3秒）
                    }, function () {
                        window.location.reload();
                        // let index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                        // parent.layer.close(index); //再执行关闭
                    });
                    // window.parent.location.reload();
                } else {
                    layer.msg(res.message, {
                        icon: 2,
                        shade: 0.3,
                        offset: '300px',
                        time: 2000 //2秒关闭（如果不配置，默认是3秒）
                    });
                }
            }, 'json');
            return false;
        });


        $('.J_spec_check').on('click',function () {
            var i = $('.J_spec_check').index($(this));
            var specType = $('.J_spec').eq(i).data('spectype');
            var spec = $('.J_spec').eq(i).val();
            $.getJSON('{{ route('backend.goods.spu.checkSpec') }}'+'?specType='+specType+'&spec='+spec,function (ret) {
                var content = '';
                if(ret.data.legal==0){
                    content = '规格不合法，'+specType+'规格类型中无'+spec+'规格'
                }else{
                    content = '规格合法'
                }
                layer.open({
                    title: '规格检测'
                    ,content: content
                });
            });
        });

        var form2Init = $(".form2").serializeArray();
        var text2Init = JSON.stringify({dataform: form2Init});
        //监听提交
        form.on('submit(confirm2)', function (data) {
            //记录表单初始数据 如果未修改则不用提交
            var form2data = $(".form2").serializeArray();
            var text2 = JSON.stringify({dataform: form2data});

            if (text2 == text2Init) {
                layer.alert('无更改，无需提交');
                return false;
            }
            data.field.is_secure = $('input[name=is_secure]').val();

            if(data.field.is_secure == 1 && data.field.secure<=0){
                layer.alert('如果设置安全库存，安全库存不能为0');
                return false;
            }



            $.post("{{ route('backend.goods.update.secure') }}", data.field, function (res) {
                if (res.code === 1) {
                    layer.msg('操作成功', {
                        icon: 1,
                        shade: 0.3,
                        offset: '300px',
                        time: 2000 //2秒关闭（如果不配置，默认是3秒）
                    }, function () {
                        window.location.reload();
                        // let index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                        // parent.layer.close(index); //再执行关闭

                    });
                    // window.parent.location.reload();
                } else {
                    layer.msg(res.message, {
                        icon: 2,
                        shade: 0.3,
                        offset: '300px',
                        time: 2000 //2秒关闭（如果不配置，默认是3秒）
                    });
                }
            }, 'json');
            return false;
        });

        //存储旧的库存总数
        var oldstuck_total = 0;
        var channel_stock = new Array();
        $(".channel_stock").each(function (i, n) {
            var v = $(this).val();
            var name = $(this).attr('name');

            channel_stock[name] = v;
            oldstuck_total = oldstuck_total + Number(v);

        });


        //记录表单初始数据 如果未修改则不用提交
        var formInit = $(".form3").serializeArray();
        var textInit = JSON.stringify({dataform: formInit});

        var stock = $('input[name=stock]').val();
        //监听提交
        form.on('submit(confirm3)', function (data) {
            var formdata = $(".form3").serializeArray();

            var text = JSON.stringify({dataform: formdata});


            data.field.stock_tag = 0;
            if (text == textInit) {
                layer.alert('无更改，无需提交');
                return false;
            }

            var rotio_total = 0;
            var channel_rotio = new Array();
            $(".channel_rotio").each(function (i, n) {
                var v = $(this).val();
                channel_rotio[i] = v;
                rotio_total = rotio_total + Number(v);

            });
            if (rotio_total != 10) {
                layer.alert('渠道分配比例总值必须等于10');
                return false;
            }
            data.field.is_share = $('input[name=is_share]').val();
            console.log('1stock-------->>>',typeof stock);
            console.log('1data.stock------>>>',typeof data.field.stock);
            console.log('data.stock------>>>',data.field.stock);
            data.field.increment_tag = 0;

            if(data.field.is_share==1){
                if(Number(data.field.stock)<Number(stock)){
                    console.log('stock-------->>>',stock);
                    console.log('data.stock------>>>',data.field.stock);
                    layer.alert('设置库存增量，须大于当前原始库存');
                    return false;
                }
                console.log('stock-------->>>',Number(stock));
                console.log('data.stock------>>>',Number(data.field.stock));
                if(Number(data.field.stock)>Number(stock)){
                    var channel_total = 0;
                    $(".channel_stock").each(function (i, n) {
                        var v = $(this).val();
                       console.log(v);
                        console.log(n);
                        channel_total = channel_total + Number(v);

                    });
                    console.log('total-stock------>>>',Number(channel_total));
                    if(Number(channel_total)>0){
                        layer.alert('不能同时修改库存分配类型和库存增量');
                        return false;
                    }
                    data.field.increment_tag = 1;
                }
                data.field.is_share = 1;

            }

            if (data.field.is_share != 1) {
                data.field.increment_tag = 0;
                var stuck_total = 0;
                var new_channel_stock = new Array();
                $(".channel_stock").each(function (i, n) {
                    var v = $(this).val();
                    var name = $(this).attr('name');
                    new_channel_stock[name] = v;
                    stuck_total = stuck_total + Number(v);

                });

                if (stuck_total != oldstuck_total) {
                    layer.alert('渠道分配库存之和必须与原库存之和一致');
                    return false;
                }


                //判断渠道库存是否修改
                if (new_channel_stock != channel_stock) {
                    data.field.stock_tag = 1;
                } else {
                    data.field.stock_tag = 0;
                }

            }


            $.post("{{ route('backend.goods.channel.update') }}", data.field, function (res) {

                if (res.code === 1) {
                    layer.msg('操作成功', {
                        icon: 1,
                        shade: 0.3,
                        offset: '300px',
                        time: 2000 //2秒关闭（如果不配置，默认是3秒）
                    }, function () {
                        window.location.reload();
                        // let index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                        // parent.layer.close(index); //再执行关闭
                    });
                    // window.location.reload();
                } else {
                    layer.msg(res.message, {
                        icon: 2,
                        shade: 0.3,
                        offset: '300px',
                        time: 1000 //2秒关闭（如果不配置，默认是3秒）
                    });
                }
            }, 'json');
            return false;
        });

        //监听指定开关
        form.on('switch(switch1)', function (data) {
            if (this.checked) {
                $('input[name=secure]').removeAttr("readonly");
                $('input[name=is_secure]').val(1);
                layer.tips('温馨提示：请注意如果设置安全库存，则值不能为0', data.othis)
            } else {
                $('input[name=secure]').attr("readonly", "readonly")
                $('input[name=is_secure]').val(0);
                layer.tips('温馨提示：请注意如果取消安全库存，则安全库存将被分配', data.othis)
            }

        });
        //监听指定开关
        form.on('switch(switch2)', function (data) {
            if (this.checked) {

                var channel_total = 0;
                $(".channel_stock").each(function (i, n) {
                    var v = $(this).val();
                    channel_total = channel_total + Number(v);

                });
                if(channel_total==0){
                    $('.stock').removeAttr("readonly");
                }

                $('.channel_stock').attr("readonly", "readonly");
                $('.channel_rotio').attr("readonly", "readonly");
                $('input[name=is_share]').val(1);
                layer.tips('温馨提示：请注意如果设置共享库存，则渠道库存回收至总库存', data.othis)
            } else {
                $('.stock').attr("readonly", "readonly");
                $('.channel_stock').removeAttr("readonly");
                $('.channel_rotio').removeAttr("readonly");

                $('input[name=is_share]').val(0);
                layer.tips('温馨提示：请注意如果设置渠道库存，则值不能全为0', data.othis)
            }
            // layer.msg('开关checked：'+ (this.checked ? 'true' : 'false'), {
            //     offset: '6px'
            // });

        });


    });

    $('#stock_update_btn').on('click', function () {
        layer.confirm('确定要执行操作吗?', {icon: 3, title:'提示',
            yes: function(index){
                let sku = "{{$detail->sku_id}}";
                let qty = $('#stock_update').val();
                $.post("{{ route('backend.goods.sku.updateStock') }}", {sku:sku,qty:qty}, function (res) {
                    if (res.code === 1) {
                        layer.msg('操作成功', {
                            icon: 1,
                            shade: 0.3,
                            offset: '300px',
                            time: 2000 //2秒关闭（如果不配置，默认是3秒）
                        }, function () {
                            window.location.reload();
                        });
                    } else {
                        layer.msg(res.message, {
                            icon: 2,
                            shade: 0.3,
                            offset: '300px',
                            time: 2000 //2秒关闭（如果不配置，默认是3秒）
                        });
                    }
                }, 'json');
                layer.close(index);
            },
            cancel: function(index, layero){
                layer.close(index);
                reload();// 可以在这里刷新窗口
            }
        });
    });

    layui.use(['form', 'layedit', 'laydate'], function(){
        var form = layui.form
            ,layer = layui.layer
            ,layedit = layui.layedit
            ,laydate = layui.laydate;

        //日期
        laydate.render({
            elem: '#startdate'
        });
        laydate.render({
            elem: '#enddate'
        });
    });
    layui.use('table', function(){
        var table = layui.table;

        table.render({
            elem: '#stocklog'
            ,id: 'table_list'
            ,url:"{{ route('backend.goods.stock.log')}}?sku={{$detail->sku_id}}"
            ,page: true
            ,parseData: function (res) {
                return {
                    "code": 0,
                    "data": res.pageData,
                    "count": res.count
                }
            },
            resizing: function () {
                table.resize('table_list');
            }
            ,cols: [[
                {field:'id', title: 'ID', sort: true}
                ,{field:'sku_id', title: 'sku_id'}
                ,{field:'type', title: '操作类型'}
                ,{field:'num', title: '数量'}
                ,{field:'note', title: '备注'}
                ,{field:'created_at', title: '创建时间', sort: true}
            ]],
        });
    });
    $('#search').on('click', function () {
        table.reload('table_list', {
            page: {curr: 1},
            where: {
                start: $("#startdate").val(),
                end: $("#enddate").val(),
            }
        });
    });
    @endsection

</script>