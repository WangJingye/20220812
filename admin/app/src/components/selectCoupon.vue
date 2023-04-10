<template>
    <div style="">
        <span  style="width:100%;" class="layui-btn layui-btn-normal" @click="dialogVisible = true">选择优惠劵</span>
        <el-dialog class="select-product-dialog" title=" " :visible.sync="dialogVisible" :close-on-click-modal="false" width="60%" top="20px">
            <div slot="title" class="header-title">
                <span>请选择优惠劵</span>
                <span style="" @click="ajaxSearch" class="layui-btn layui-btn-small  layui-btn-normal">search</span>
            </div>
            <div >
                <div >
                    <table class="layui-table" style="width:100%;" lay-skin="" lay-size="sm"
                           v-loading="loading"
                           element-loading-text="拼命加载中"
                           element-loading-spinner="el-icon-loading"
                           element-loading-background="rgba(0, 0, 0, 0.8)"
                        >
                        <colgroup>
                            <col width="100">
                            <col width="200">
                            <col width="200">
                            <col width="200">
                            <col>
                        </colgroup>
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>优惠劵名称</th>
                            <th>条件</th>
                            <th>折扣</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th></th>
                            <th><input v-model="search_name" /></th>
                            <th></th>
                            <th></th>
                        </tr>
                        <tr v-for="{id,name,total_amount,total_discount} in items" @click="setCoupon({id,name,total_amount,total_discount},coupon)">
                            <td>{{id}}</td>
                            <td>{{name}}</td>
                            <td>满足{{total_amount}}元</td>
                            <td>立减{{total_discount}}元</td>
                        </tr>
                        </tbody>
                    </table>
                    <div id="layui-table-page1">
                        <div class="layui-box layui-laypage layui-laypage-default" id="layui-laypage-14">
                            <el-pagination
                                    background
                                    @current-change="handleCurrentChange"
                                    layout="total,prev, pager, next"
                                    :page-size="pageSize"
                                    :total="total">
                            </el-pagination>
                        </div>
                    </div>
                </div>
            </div>
        </el-dialog>
    </div>
</template>


<script>
    export default {
        props: ['coupon'],
        name: "select-coupon",
        components: {},
        data() {
            return {
                dialogVisible: false,
                items:[],
                total:0,
                pageSize:5,
                loading:true,
                search_id:"",
                search_name:"",
                current_page:1,

            };
        },

        created:function (){

        },
        mounted: function () {
            this._getItemsByPage(1);
        },
        watch:{
            dialogVisible(val){
                if(val === true){
                    $('.edit-wrap').css('position','relative');
                }else{
                    $('.edit-wrap').css('position','fixed');
                }
            }
        },
        computed:{
        },
        methods: {

            handleCurrentChange:function (currentPage){
                this._getItemsByPage(currentPage);
            },

            _getItemsByPage:function (num){
                this.loading=true;
                let url = this.$root.$data.apiDomain + 'admin/page/couponList?current_page='+num;
                $.post(url, (response) => {
                    this.items = response.data.items;
                    this.total=response.data.count;
                    this.loading=false;
                    this.current_page=num;
                }, 'json');
            },
            setCoupon:function (couponObj,coupon){
                this.dialogVisible=false;
                this.$emit('setCoupon',couponObj,coupon);
            },
            ajaxSearch:function (){
                let params={
                    name:this.search_name,
                }
                this.loading=true;
                let url = this.$root.$data.apiDomain + 'admin/page/couponList';
                $.post(url,params, (response) => {
                    this.items = response.data.items;
                    this.total=response.data.count;
                    this.loading=false;
                }, 'json');

            }
        }
    };
</script>

<style >
    .select-product-dialog>.el-dialog>.el-dialog__body{
        padding-top:0px !important;
    }
    /*.el-dialog__wrapper.select-product-dialog{*/
        /*z-index:19999999 !important;*/
    /*}*/

</style>
