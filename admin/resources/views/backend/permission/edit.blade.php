@extends('backend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新权限</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('backend.permission.update',['permission'=>$permission])}}" method="post">
                {{method_field('put')}}
                <input type="hidden" name="id" value="{{ $permission->id }}">
                @include('backend.permission._from')
            </form>
        </div>
    </div>
@endsection
