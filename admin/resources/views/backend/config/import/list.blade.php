@extends('backend.base')

@section('content')
    <script>
        function xhrSubmitPoster(e){
            var files = e.target.files[0];
            if(!/\.csv$/.test(files.name)){
                alert('必须上传csv文件');
            }
            var param = new FormData();
            param.append('file',files);
            $(e.target).before('<i style="font-size: 30px;" class="loading layui-icon layui-icon-loading layui-icon layui-anim layui-anim-rotate layui-anim-loop" />');
            $.ajax({
                url:'<?php echo route('backend.product.import.ajaxUpload')?>',
                type:'post',
                data:param,
                dataType:'json',
                processData: false,
                contentType:false,
                timeout:1000,
                error:function(XMLHttpRequest, textStatus, errorThrown){
                    $('i.loading').hide();
                    //alert('请耐心等待程序处理完毕');
                },
                complete:function(XMLHttpRequest, textStatus){
                    $('i.loading').hide();
                    alert('任务已提交,请耐心等待程序处理完毕');
                },
                success:function(res){
                    if(res.status==true){
                        $('i.loading').hide();
                        alert('任务已提交,请耐心等待程序处理完毕');
                       // window.location.reload();
                    }else{
                        alert(res.message);
                    }
                }
            })
        }
    </script>
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body">
                <form class="layui-form layui-form-pane"  method="POST">
                    上传CSV文件：
                    <input style="" type="file" onchange="xhrSubmitPoster(event)" style="borde:none;">
                </form>
                <table id="list" lay-filter="list"></table>
            </div>
        </div>
    </div>
@endsection
<script src="{{ url('/static/admin/js/jquery.min.js') }}"></script>
<script src="{{ url('/static/admin/laydate.js') }}"></script>
<style>
    .layui-table-cell {
        height: auto !important;
        white-space: normal;
    }
</style>
<script>

    @section('layui_script')

    table.render({
            elem: '#list'
            , id: 'table_list'
            , height: 500
            , url: "{{ route('backend.product.import.datalist') }}" //数据接口
            , page: true //开启分页
            , limits: [10]
            , limit: 10 //每页默认显示的数量
            , method:'post'
            , cols: [
                [ //表头
                    {
                        field: 'id', title: 'ID', sort: true, width:200
                    },
                    {
                        field: 'count', title: 'count', sort: true, width:200
                    },
                    {
                        field: 'percent', title: 'percent', sort: true, width:200
                    },
                    {
                        field: 'error', title: 'error', sort: true, width:200
                    },
                    {
                        field: 'error_ids', title: 'error_line', sort: true, width:200
                    },
                    {
                        field: 'created', title: 'created', sort: true, width:200
                    },
                    {
                        field: 'finished', title: 'finished', sort: true, width:200
                    },
                    {
                        field: 'message', title: 'message', sort: true, width:200
                    },

                    ,{
                    title: '操作', width: 300, align: 'center', templet: function (d) {
                        let opt = '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">编辑</a>';
                        return opt;
                    }
                }

                ]
            ]
            , response: {
                statusName: 'code' //规定数据状态的字段名称，默认：code
                , statusCode: 0 //规定成功的状态码，默认：0
                , msgName: 'msg' //规定状态信息的字段名称，默认：msg
            }
        }
    );

    table.on('sort(list)', function (obj) {
        let type = obj.type,
            field = obj.field,
            data = obj.data,//表格的配置Data
            thisData = [];

        //将排好序的Data重载表格
        table.reload('table_list', {
            initSort: obj,
            where:{
                order:field,
                dir:type
            }
        });
    });



    var active = {
        reload: function(){
            table.reload('table_list', {
                page: {curr: 1},
                where: {
                }
            });
        }
    };










    @endsection
</script>