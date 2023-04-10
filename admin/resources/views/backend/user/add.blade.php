@extends('backend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>新增用户</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form">
                @include('backend.user._form')
            </form>
        </div>
    </div>
@endsection

<script>
@section('layui_script')
    form.on('submit(formSubmit)', function(data){
        $.post("{{ route('backend.user.insert') }}",data.field,function(res){
            if(res.code!==1){
                layer.msg(res.msg,{icon:5,anim:6});
                return false;
            }
            var index = parent.layer.getFrameIndex(window.name);
            parent.layer.close(index);
        },'json');
        return false;
    });
@endsection
</script>
