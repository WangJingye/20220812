@extends('backend.base')
<link rel="stylesheet" href="{{ url('/static/admin/css/form.css') }}" media="all">
@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>查看分类</h2>
        </div>
        <div class="layui-card-body">
            <form action="" method="post" class="layui-form">
                {{csrf_field()}}
                <input type="hidden" name="id" value="{{$detail->id}}">
                <input type="hidden" name="custom_prod_type" id="custom_prod_type" value="{{$detail->include_style_number}}">
                <input type="hidden" name="include_style_number" id="include_style_number" value="{{$detail->include_style_number}}">
                <input type="hidden" name="exclude_style_number" id="exclude_style_number" value="{{$detail->exclude_style_number}}">
                <input type="hidden" name="selected_items" id="selected_items" value="{{$detail->selected_items}}">
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">分类ID</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="category_id" lay-verify="required" value="{{$detail->category_id??old('category_id')}}" readonly>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">分类Code</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="category_code" lay-verify="required" value="{{$detail->category_code??old('category_code')}}" readonly>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">分类名称</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="category_name" lay-verify="required" value="{{$detail->category_name??old('category_name')}}" placeholder="如：挚爱美礼" readonly>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">详情图</label>
                    <div class="layui-input-block" style="display: flex;display: -webkit-flex;flex-direction:column;width:15%;">
                        <input class="layui-input" type="hidden" id="category_kv_image" name="category_kv_image" value="{{$detail->category_kv_image??old('category_kv_image')}}" placeholder="如：挚爱美礼">
                        <img id="category_kv_image_src" width="100%" src="{{$detail->category_kv_image??old('category_kv_image')}}" style="display:none;"/>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">分享文案</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="share_content" value="{{$detail->share_content??old('share_content')}}" placeholder="如：挚爱美礼" readonly>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">分享图片</label>
                    <div class="layui-input-block" style="display: flex;display: -webkit-flex;flex-direction:column;width:15%;">
                        <input class="layui-input" type="hidden" id="share_image" name="share_image" value="{{$detail->share_image??old('share_image')}}" placeholder="如：挚爱美礼">
                        <img id="share_image_src" width="100%" src="{{$detail->share_image??old('share_image')}}" style="display:none;"/>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">分类下产品</label>
                    <div class="layui-input-block">
                        <div class="forins_controls">
                            <div class="label_search_box">
                                <span class="forins_input_box" id="js_tag_box1" data-value="{{$detail->relatedItemsJson??old('relatedItemsJson')}}">
                                    @foreach ($detail->relatedItems as $relatedItem)
                                    <span class="forins_input_tag"><div class="js_tag" data-id="{{$relatedItem->master_catalog_item}}">{{$relatedItem->master_catalog_item}}</div></span>
                                    @endforeach
                                    <input id="tag_1" name="tag_1" style="display:none;" value="{{$detail->relatedItemsStr??old('relatedItemsStr')}}">
                                </span>
                                <input id="relatedItemsJson" name="relatedItemsJson" style="display:none;" value="{{$detail->relatedItemsJson??old('relatedItemsJson')}}">
                                <button type="button" class="layui-btn layui-btn-warm layui-btn-sm forins_input_button">选择产品</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

<div class="layui-form-pop" style="display:none;">
    <div class="layui-card-header layuiadmin-card-header-auto">
        <h2>分类下产品</h2>
    </div>
    <div class="layui-card-body">
        <form class="layui-form" lay-filter="extra">
            <input type="hidden" name="cateIdx" id="cateIdx" value="{{$detail->id}}">

            <div class="layui-form-item">
                <label for="" class="layui-form-label">初始化品项</label>
                <div class="layui-input-block">
                    <div class="forins_controls">
                        <div class="label_search_box">
                            <span class="forins_input_box">
                                @foreach ($detail->initProdsArr as $initProd)
                                <span class="forins_input_tag"><div class="js_tag" data-id="{{$initProd}}">{{$initProd}}</div></span>
                                @endforeach
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="" class="layui-form-label">选中材质</label>
                <div class="layui-input-block init">
                    @foreach ($prodTypeList as $code => $name)
                        @if (in_array($code,$detail->customProdType))
                        <input type="checkbox" name="prodTypes[{{$code}}]" data-code="{{$code}}" id="" title="{{$name}}" checked="">
                        @else
                        <input type="checkbox" name="prodTypes[{{$code}}]" data-code="{{$code}}" id="" title="{{$name}}">
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="layui-form-item">
                <label for="" class="layui-form-label">增加款号</label>
                <div class="layui-input-block">
                    <textarea placeholder="多个款号之间以逗号(,)间隔" name="includeStyleNr" class="layui-textarea">{{$detail->include_style_number??old('include_style_number')}}</textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="" class="layui-form-label">排除款号</label>
                <div class="layui-input-block">
                    <textarea placeholder="多个款号之间以逗号(,)间隔" name="excludeStyleNr" class="layui-textarea">{{$detail->exclude_style_number??old('exclude_style_number')}}</textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="" class="layui-form-label">选中的产品</label>
                <div class="layui-input-block">
                    <div class="forins_controls">
                        <div class="label_search_box">
                            <span class="forins_input_box" id="js_tag_box0" data-value="{{$detail->selectedItemsJson??old('selectedItemsJson')}}">
                                @foreach ($detail->selectedItems as $selectedItem)
                                    <span class="forins_input_tag"><div class="js_tag" data-id="{{$selectedItem->master_catalog_item}}">{{$selectedItem->master_catalog_item}}</div><div class="icon_tag_del"></div></span>
                                @endforeach
                                <input id="tag_0" name="tag_0" style="display:none;" value="{{$detail->selectedItemsStr??old('selectedItemsStr')}}">
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
<script>
@section('layui_script')
    var prodIndex = null;
    if($("#share_image").val() !== ''){
        $("#share_image_src").show();
    }
    if($("#category_kv_image").val() !== ''){
        $("#category_kv_image_src").show();
    }

    var dataTable = null;
    $('.forins_input_button').on('click', function () {
        prodIndex = layer.open({
            title:'请选择要挂载的商品',
            type: 1,
            area: ['100%', '100%'],
            offset: 'auto',
            maxmin: true,
            // btn: ['确定', '取消'], //只是为了演示
            content: $(".layui-form-pop"),
        });
    });
@endsection
</script>