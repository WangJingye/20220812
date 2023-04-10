<template>
    <div class="multi_image_product_swiper">

        <div class="">
            <div class="form-group">
                <template v-if="['pc','h5'].includes($root.$data.mediaType)">
                    <el-form-item label="列数/pc" >
                        <div class="">
                            <el-input-number :disabled="true" v-model="currentElement.columns.pc" label="列" :min="2" :max="4" size="mini"></el-input-number>
                        </div>
                    </el-form-item>
                    <el-form-item label="列数/h5" >
                        <div class="">
                            <el-input-number :disabled="true" v-model="currentElement.columns.h5" label="列" :min="1" :max="2" size="mini"></el-input-number>
                        </div>
                    </el-form-item>
                </template>
                <template v-if="$root.$data.mediaType=='wechat'">
                    <el-form-item label="列数" >
                        <div class="">
                            <el-input-number v-model="currentElement.columns.h5" label="列" :min="1" :max="2" size="mini"></el-input-number>
                        </div>
                    </el-form-item>
                </template>
            </div>
            <div class="form-group">
                    <el-form-item label="添加商品" >
                        <div class="">
                            <el-button  size="mini" @click="addProduct()">添加一个商品</el-button>
                        </div>
                    </el-form-item>
            </div>



            <draggable
                    class="dragArea list-group "
                    :list="currentElement.nodes"
                    @change="sort"
                    handle=".handle"
            >
                <div class="list-group-item " v-for="(item,i) in currentElement.nodes" :key="i" style="position: relative;cursor: default;">
                    <i class="el-icon-document handle" style="position: absolute;left: 0px;top:0px;cursor: move"></i>
                    <i class="el-icon-delete" style="position: absolute;right: 0px;top:0px;" @click="removeAt(currentElement,i)"></i>
                    <div class="form-group" style="margin-top: 10px;">
                        <el-form-item label="商品SPU" >
                            <div class="">
                                <input class="form-control" v-model="item.sku" placeholder="商品SPU" readonly>
                            </div>
                        </el-form-item>
                    </div>
                    <select-product @setSku="setSku" :item="item" style="width:100%"/>
                </div>
            </draggable>
        </div>
    </div>
</template>


<script>
    export default {
        props: ['currentElement'],
        name: "product-swiper",
        components: {},
        data() {
            return {
                fileList: [{
                    name: "",
                    url: this.currentElement.tag=='image'?this.currentElement.src:""
                }],
                apiDomain:this.$root.$data.apiDomain,
                ajaxUpload:this.$root.$data.apiDomain+'admin/page/ajaxUpload'
            };
        },
        mounted: function () {
        },
        watch: {},
        methods: {
            sort(evt){
                let _this=this;
                let files = [...this.currentElement.nodes];
                this.currentElement.nodes=[];
                files.forEach(function (file){
                    _this.currentElement.nodes.push(file);
                });
            },
            removeAt(element,idx) {
                element.nodes.splice(idx, 1);
                if(element.nodes.length ==0){
                    element.height = 0;
                }else{
                    element.height=(_.maxBy(element.nodes, 'height')).height;
                }
            },
            setSku:function (product,item){
                console.log(product,item);
                item.product_id=product.id;
                item.sku=product.sku;
                item.src=product.image;
                item.name=product.name;
                item.desc=product.desc;
                item.price=product.price;
            },
            addProduct:function(){
                let product = {product_id:"",sku:"",src:"",name:"",desc:"",price:""};
                this.currentElement.nodes.push(product);
            }




        }
    };
</script>
<style scoped>

</style>
