@extends('backend.base')
<link rel="stylesheet" href="{{ url('/static/admin/css/form.css') }}" media="all">
@section('content')
<div class="layui-card">
    <div class="layui-card-header layuiadmin-card-header-auto">
        <h2>查看匹配规则</h2>
    </div>
    <div class="layui-card-body" id="app">
        <form method="post" class="layui-form">
            <div class="layui-form-item">
                <div class="layui-input-block">
                    @foreach ($ruleTypeMap as $ruleTypeCode=>$ruleTypeName)
                    @if($ruleTypeCode == $detail->rule_type)
                    <input type="radio" lay-filter="rule" name="rule_type" value="{{$ruleTypeCode}}" title="{{$ruleTypeName}}" checked>
                    @else
                    <input type="radio" lay-filter="rule" name="rule_type" value="{{$ruleTypeCode}}" title="{{$ruleTypeName}}">
                    @endif
                    @endforeach
                </div>
            </div>
            @if(1 == $detail->rule_type)
            <div class="layui-form-item" id="brand_block">
            @else
            <div class="layui-form-item" style="display:none;" id="brand_block">
            @endif
                <div class="layui-input-block">
                    @foreach ($prodBrandCollList as $prodBrandCollItem)
                    @if($prodBrandCollItem->code == $detail->step_o)
                        <input type="radio" lay-filter="brand" name="brand_coll" value="{{$prodBrandCollItem->code}}" title="{{$prodBrandCollItem->name}}" checked>
                    @else
                        <input type="radio" lay-filter="brand" name="brand_coll" value="{{$prodBrandCollItem->code}}" title="{{$prodBrandCollItem->name}}">
                    @endif
                    @endforeach
                </div>
            </div>
            @if(1 == $detail->rule_type&&$detail->is_child!= 0)
            <div class="layui-form-item" id="child_block">
            @else
            <div class="layui-form-item" style="display:none;" id="child_block">
            @endif
                <label class="layui-form-label">是否需要加选子系列</label>
                <div class="layui-input-block">
                    @if(1 == $detail->is_child)
                    <input type="radio" lay-filter="child" name="is_child" value="1" title="是" checked>
                    <input type="radio" lay-filter="child" name="is_child" value="2" title="否">
                    @else
                    <input type="radio" lay-filter="child" name="is_child" value="1" title="是">
                    <input type="radio" lay-filter="child" name="is_child" value="2" title="否" checked>
                    @endif
                </div>
            </div>
            @foreach ($prodBrandCollList as $prodBrandCollItem)
            @if(isset($prodBrandCollItem->sub))
            @if($prodBrandCollItem->code == $detail->step_o)
            <div class="layui-form-item sub-coll" id="{{$prodBrandCollItem->code}}">
            @else
            <div class="layui-form-item sub-coll" style="display:none;" id="{{$prodBrandCollItem->code}}">
            @endif
                <div class="layui-input-block">
                    @foreach ($prodBrandCollItem->sub as $subItem)
                    @if(isset($detail->step_t) && !empty($detail->step_t) && $subItem->code == $detail->step_t)
                    <input type="radio" lay-filter="sub_brand" name="sub_brand_coll" value="{{$subItem->code}}" title="{{$subItem->name}}" checked>
                    @else
                    <input type="radio" lay-filter="sub_brand" name="sub_brand_coll" value="{{$subItem->code}}" title="{{$subItem->name}}">
                    @endif
                    @endforeach
                </div>
            </div>
            @endif
            @endforeach
            @if(2 == $detail->rule_type)
            <div class="layui-form-item" id="usage_block">
            @else
            <div class="layui-form-item" style="display:none;" id="usage_block">
            @endif
                <div class="layui-input-block">
                    @foreach ($prodUsage as $prodUsageCode=>$prodUsageName)
                    @if($prodUsageCode == $detail->step_o)
                        <input type="radio" lay-filter="usage" name="usage" value="{{$prodUsageCode}}" title="{{$prodUsageName}}" checked>
                    @else
                        <input type="radio" lay-filter="usage" name="usage" value="{{$prodUsageCode}}" title="{{$prodUsageName}}">
                    @endif
                    @endforeach
                </div>
            </div>
            @if(3 == $detail->rule_type)
            <div class="layui-form-item" id="prod_type_block">
            @else
            <div class="layui-form-item" style="display:none;" id="prod_type_block">
            @endif
                <div class="layui-input-block">
                    @foreach ($prodTypeList as $prodTypeCode=>$prodTypeName)
                    @if($prodTypeCode == $detail->step_o)
                        <input type="radio" lay-filter="prodType" name="prod_type" value="{{$prodTypeCode}}" title="{{$prodTypeName}}" checked>
                    @else
                        <input type="radio" lay-filter="prodType" name="prod_type" value="{{$prodTypeCode}}" title="{{$prodTypeName}}">
                    @endif
                    @endforeach
                </div>
            </div>
            @if(4 == $detail->rule_type)
            <div class="layui-form-item" id="style_number_block">
            @else
            <div class="layui-form-item" style="display:none;" id="style_number_block">
            @endif
                <div class="layui-input-block">
                    <textarea placeholder="多个款号之间以逗号(,)间隔" name="style_number" class="layui-textarea">{{$detail->include_style_number}}</textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block" >
                    <picture-card
                            :images_list='<?php echo json_encode($detail->image??'[]')?>'
                            input_name="image"/>
                </div>
            </div>
        </form>
    </div>
</div>
<style>
 .el-upload--picture-card{
     display: none !important;
 }
</style>
<script src="<?=url('/lib/app.js'); ?>"></script>
<?php require_once './js/picturecard.js.php'; ?>
<link rel="stylesheet" href="<?=url('/lib/app/index.css'); ?>">
@endsection
<script>
@section('layui_script')
    var prodIndex = null;
    layui.use(['upload','form'], function(){
        var upload = layui.upload;

        upload.render({
            elem: '#image_button' //绑定元素
            ,url: "{{ route('backend.page.ajaxUpload') }}" //上传接口
            ,done: function(res){
                if(res.status==true){
                    $("#image").val(res.file);
                    $("#image_src").attr("src",res.file).show();
                }else{
                    alert(res.message);
                }
            }
            ,error: function(){
            //请求异常回调
            }
        });
    });
    if($("#image").val() !== ''){
        $("#image_src").show();
    }

    var dataTable = null;
    form.on('radio(rule)', function(data){
        $('input:radio[name=brand_coll]').each(function(index, el) {
            el.checked = false;
        });
        $('input:radio[name=is_child]').each(function(index, el) {
            el.checked = false;
        });
        $('input:radio[name=sub_brand_coll]').each(function(index, el) {
            el.checked = false;
        });
        $('input:radio[name=usage]').each(function(index, el) {
            el.checked = false;
        });
        $('input:radio[name=prod_type]').each(function(index, el) {
            el.checked = false;
        });
        form.render('radio');
        $(".sub-coll").hide();
        $("#brand_block").hide();
        $("#child_block").hide();
        $("#usage_block").hide();
        $("#prod_type_block").hide();
        $("#style_number_block").hide();
        let val = data.value;
        if(val == 1){
            $("#brand_block").show();
        }else if(val == 2){
            $("#usage_block").show();
        }else if(val == 3){
            $("#prod_type_block").show();
        }else if(val == 4){
            $("#style_number_block").show();
        }
    });
    form.on('radio(brand)', function(data){
        $('input:radio[name=is_child]').each(function(index, el) {
            el.checked = false;
        });
        $('input:radio[name=sub_brand_coll]').each(function(index, el) {
            el.checked = false;
        });
        form.render('radio');
        $(".sub-coll").hide();
        let val = data.value;
        if($("#"+val).length>0){
            $("#child_block").show();
            $("#"+val).show();
        }else{
            $("#child_block").hide();
        }
    });
    form.on('radio(child)', function(data){
        let val = data.value;
        if(val == 2){
            $('input:radio[name=sub_brand_coll]').each(function(index, el) {
                el.checked = false;
            });
            form.render('radio');
        }
    });
    form.on('radio(sub_brand)', function(data){
        let val = data.value;
        $('input:radio[name=is_child]').each(function(index, el) {
            if($(this).val() == 1){
                el.checked = true;
            }else{
                el.checked = false;
            }
        });
        form.render('radio');
    });
    var action = {
        check:function(pid,deRawProds){
            if(deRawProds){
                let re = $.inArray(String(pid), deRawProds);
                if(re === -1){
                    return false;
                }else{
                    return true;
                }
            }else{
                return false;
            }
        }
    }
    var getCheckBox = function(dom){
        let list = [];
        $(dom).find(".layui-form-checkbox").each(function(){
            if($(this).hasClass("layui-form-checked")){
                list.push($(this).prev().data('code'));
            }
        })
        $("#custom_prod_type").val(list.join(","));
    }
@endsection
</script>