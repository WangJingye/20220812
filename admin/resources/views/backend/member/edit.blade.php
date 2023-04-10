@extends('backend.base')

@section('content')
    <div class="layui-col-md12" id="promotion_cart">
        <div class="layui-card">
            <div class="layui-card-body" style="padding: 15px;">
                <div class="layui-form-item">
                    <label class="class-label">openid:</label>
                    <div class="layui-input-inline" style="width: 240px;">
                        {{ $data['openid'] ?? '' }}
                    </div>
                    <label class="class-label">会员手机号:</label>
                    <div class="layui-input-inline" >
                        {{ $data['mobileNumber'] ?? '' }}
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="class-label">会员电邮:</label>
                    <div class="layui-input-inline" style="width: 240px;">
                        {{ $data['email'] ?? '' }}
                    </div>
                    <label class="class-label">居住地:</label>
                    <div class="layui-input-inline">
                        {{ $data['residenceCountry'] }}   
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="class-label">生日:</label>
                    <div class="layui-input-inline" style="width: 240px;">
                        {{ $data['dateOfBirth'] }}   
                    </div>
                
                    <label class="class-label">性别:</label>
                    <div class="layui-input-inline">
                        {{ $data['gender'] }}
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="class-label">姓氏:</label>
                    <div class="layui-input-inline" style="width: 240px;">
                        {{ $data['family_name'] }}
                    </div>
                     <label class="class-label">名称:</label>
                    <div class="layui-input-inline">
                        {{ $data['first_name'] }}
                    </div>
                   
                </div>

                <div class="layui-form-item">
                    <label class="class-label">会员编号:</label>
                    <div class="layui-input-inline" style="width: 240px;">
                        {{ $data['customer_id'] }}
                    </div>
                
                    <label class="class-label">状态:</label>
                    <div class="layui-input-block">
                        {{ $data['available'] }}   
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="class-label">入会来源:</label>
                    <div class="layui-input-inline" style="width: 240px;">
                        {{ $data['fromchannel'] ?? '' }}
                    </div>
                
                    <label class="class-label">是否授权:</label>
                    <div class="layui-input-block">
                        {{ $data['auth'] }}   
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="class-label">下单次数:</label>
                    <div class="layui-input-inline" style="width: 240px;">
                        {{ $data['orderCount'] }}
                    </div>
                
                    <label class="class-label">下单金额:</label>
                    <div class="layui-input-block">
                        {{ $data['orderMoney'] }}   
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="class-label">悦享钱余额:</label>
                    <div class="layui-input-inline" style="width: 240px;">
                        {{ $data['point'] }}
                    </div>
                
                    <label class="class-label">unionId:</label>
                    <div class="layui-input-block">
                        {{ $data['unionid'] }}   
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<style type="text/css">
    .class-label {
        float: left;
        display: block;
        padding: 2px 15px;
        width: 100px;
        font-weight: 600;
        line-height: 20px;
        text-align: right;
    }
</style>