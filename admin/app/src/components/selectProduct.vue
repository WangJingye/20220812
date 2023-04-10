<template>
    <div style="">
        <span  style="width:100%;" class="layui-btn layui-btn-normal" @click="dialogVisible = true">商品类型</span>
        <el-dialog class="select-product-dialog" title=" " :visible.sync="dialogVisible" :close-on-click-modal="false" width="80%" top="20px" append-to-body>
            <div slot="title" class="header-title">
                <div style="margin-top: 20px">
                    <el-radio-group v-model="type"  size="mini">
                        <el-radio-button v-for="o in typeOption" :label="o" :key="o"></el-radio-button>
                    </el-radio-group>
                    <span style="" @click="ajaxSearch" class="layui-btn layui-btn-small  layui-btn-normal" style="background: #409EFF">search</span>
                </div>

            </div>
            <div >
                <div >
                    <table class="layui-table" lay-skin="" lay-size="sm"
                           v-loading="loading"
                           element-loading-text="拼命加载中"
                           element-loading-spinner="el-icon-loading"
                           element-loading-background="rgba(0, 0, 0, 0.8)"
                        >
                        <colgroup>
                            <col width="100">
                            <col width="100">
                            <col width="100">
                            <col width="200">
                            <col width="200">
                            <col>
                        </colgroup>
                        <thead>
                        <tr>
                            <td>ID</td>
                            <td>图片</td>
                            <th>名称</th>
                            <th>SKU</th>
                            <th>状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <th><input v-model="search_name" /></th>
                            <th><input v-model="search_sku" /></th>
                        </tr>
                        <tr style="cursor: pointer" v-for="{id,image,sku,collection_name,name,display_status,desc,price} in items"  @dblclick="setSku({id,image,sku,collection_name,name,display_status,desc,price},item)">
                            <td>{{id}}</td>
                            <td><img :src="image"  width="80"/></td>
                            <td>{{name}}</td>
                            <td>{{sku}}</td>
                            <td>{{getStatus(display_status)}}</td>
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
        props: ['item'],
        name: "select-product",
        components: {},
        data() {
            return {
                dialogVisible: false,
                items:[],
                total:0,
                pageSize:5,
                loading:true,
                search_id:"",
                search_sku:"",
                search_name:"",
                current_page:1,
                type:"product",
                typeOption:[
                    "product",
                    "collection"
                ]

            };
        },

        created:function (){
            var _this = this;
            document.onkeydown = function(e) {
                let key = window.event.keyCode;
                if (key == 13) {
                    _this.ajaxSearch();
                }

            };
        },
        mounted: function () {
            this._getItemsByPage(1);
        },
        watch:{
            dialogVisible(val){
                if(val === true){
                    $('.edit-wrap').css('position','relative');
                }else{
                    $('.edit-wrap').css('position','unset');
                }
            }
        },
        computed:{
        },
        methods: {
            getStatus(status){
                if(status==1){
                    return '已上架';
                }else{
                    return '已下架';
                }
            },

            handleCurrentChange:function (currentPage){
                this._getItemsByPage(currentPage);
            },

            _getItemsByPage:function (num){
                if(num == 1){
                    this.items=this.$root.$data.ajax.products;
                    this.total=this.$root.$data.ajax.productsTotal;
                    this.loading=false;
                    this.current_page=num;
                }else{
                    this.cms=this.$root.$data.ajax.cms;
                    this.loading=true;
                    let url = this.$root.$data.apiDomain + 'admin/page/product/list?current_page='+num;

                    let params={
                        id:this.search_id,
                        product_id:this.search_sku,
                        name:this.search_name,
                    }
                    $.post(url, params,(response) => {
                        this.items = response.data.items;
                        this.total=response.data.count;
                        this.loading=false;
                        this.current_page=num;
                    }, 'json');
                }
            },
            setSku:function (sku,item){
                this.dialogVisible=false;
                this.$emit('setSku',sku,item);
            },
            ajaxSearch:function (){
                let params={
                    type:this.type,
                    id:this.search_id,
                    product_id:this.search_sku,
                    name:this.search_name,
                }
                this.loading=true;
                let url = this.$root.$data.apiDomain + 'admin/page/product/list';
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

</style>
