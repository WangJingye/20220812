@extends('backend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新角色</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('backend.role.update',['role'=>$role])}}" method="post" class="layui-form">
                {{method_field('put')}}
                <input type="hidden" name="id" value="{{$role->id}}">
                @include('backend.role._form')
            </form>
        </div>
    </div>
@endsection