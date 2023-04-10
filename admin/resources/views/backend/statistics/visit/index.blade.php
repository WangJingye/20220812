@extends('backend.base')
@section('content')
    <div class="layui-tab" lay-filter="stat_summary">
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <div class="layui-col-md12">

                    <div class="layui-card">
                        <table id="summaryStat" lay-filter="test"></table>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>

<script>
    @section('layui_script')

    layui.use('table', function () {
        var table = layui.table;
        var dataUrl = "{{route('dsa.summary.daily')}}";
        //第一个实例
        table.render({
            elem: '#summaryStat'
            , height: 312
            , url: dataUrl //数据接口
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'id', title: 'ID', width: 120, sort: true}
                ,{field: 'ref_date', title: '日期', width: 120, sort: true, fixed: 'left'}
                , {field: 'visit_total', title: '累计用户数', width: 120, sort: true}
                , {field: 'share_pv', title: '转发次数', width: 120, sort: true}
                , {field: 'share_uv', title: '转发人数', width: 120, sort: true}
                , {field: 'updated_at', title: '更新日期', width: 177}
            ]]
            , response: {
                statusName: 'code' //规定数据状态的字段名称，默认：code
                , statusCode: 200 //规定成功的状态码，默认：0
                , msgName: 'msg' //规定状态信息的字段名称，默认：msg
            }
            , parseData: function (res) { //res 即为原始返回的数据
                return {
                    "code": res.code, //解析接口状态
                    "msg": res.msg, //解析提示文本
                    "count": res.count, //解析数据长度
                    "data": res.data //解析数据列表
                };
            }
        });

    });

    @endsection
</script>

@section('script')

@endsection
