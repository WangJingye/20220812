@extends('backend.base')

@section('content')
    <div class="layui-col-md12" id="promotion_cart">
        <div class="layui-card">
            <div class="layui-card-body" style="padding: 15px;">
                <el-form class="layui-form" action="">
                    <div class="layui-form-item">
                        <label class="class-label">主账号用户ID:</label>
                        <div class="layui-input-block">
                            <input type="text" id="J_master_id" name="title" required  lay-verify="required" placeholder="请输入标题" style="width: 200px" autocomplete="off" class="layui-input-inline layui-input ">
                            <!--
                            <button class="layui-btn J_select_user"  > 选择</button>
                            -->
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="class-label">副账号用户ID:</label>
                        <div class="layui-input-block">
                            <input type="text" id="J_slave_id" name="title" required  lay-verify="required" placeholder="请输入标题" style="width: 200px" autocomplete="off" class="layui-input-inline layui-input ">
                            <!--
                            <button class="layui-btn J_select_user"  > 选择</button>
                            -->
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="class-label"></label>
                        <button class="layui-btn layui-btn-danger " id="J_merge" >合并</button>
                    </div>

                </el-form>

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

<script>
    @section('layui_script')
    layui.use(['upload', 'form'], function () {
        $('#J_merge').on('click',function () {
            var master_id = $('#J_master_id').val();
            var slave_id = $('#J_slave_id').val();
            layer.open({
                title: '合并账户',
                type: 2,
                area: ['100%', '100%'],
                offset: 't',
                maxmin: false,
                move: false,
                content: "{{ route('backend.member.merge.getSlaveAndMasterMember') }}?master_id="+master_id+"&slave_id="+slave_id,
                end: function () {
                    table.reload('table_list')
                }
            });
        });
    });

    @endsection
</script>