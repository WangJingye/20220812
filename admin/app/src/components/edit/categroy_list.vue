<template>
    <div class="categroy_list">

        <div class="">
                    <div class="form-group" style="margin-top: 10px;">
                        <el-form-item label="分类ID" >
                            <div class="">
                                <input class="form-control" v-model="currentElement.category_id" placeholder="分类ID" readonly>
                            </div>
                        </el-form-item>
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <el-form-item label="分类名称" >
                            <div class="">
                                <input class="form-control" v-model="currentElement.category_name" placeholder="分类名称" readonly>
                            </div>
                        </el-form-item>
                    </div>
                    <select-category @setCategoryId="setCategoryId"  style="width:100%"/>
                </div>
        </div>
</template>


<script>
    export default {
        props: ['currentElement'],
        name: "categroy-list",
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
            setCategoryId:function (category){
                console.log(category);
                this.currentElement.category_id=category.id;
                this.currentElement.category_name=category.label;
            },




        }
    };
</script>
<style scoped>

</style>
