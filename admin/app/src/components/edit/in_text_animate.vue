<template>
    <div class="form-group">
        <div class="">
            <div class="list-group-item" >
                    <img :src="currentElement.nodes.src" style="width:100px;" />
                    <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input" @change="xhrSubmitForCategory($event,currentElement)">
            </div>

            <div class="form-group" style="margin-top: 10px;">
                <el-form-item label="背景颜色" >
                    <div class="">
                        <input style="height: 30px" type="color" class="form-control" v-model="currentElement.nodes.bgColor" placeholder="背景颜色">
                    </div>
                </el-form-item>
            </div>

            <div class="form-group" >
                <el-form-item label="标题" >
                    <div class="">
                        <el-input v-model="currentElement.nodes.title" label="标题"  />
                    </div>
                </el-form-item>
            </div>
            <div class="form-group" >
                <el-form-item label="描述" >
                    <div class="">
                        <el-input type="textarea" v-model="currentElement.nodes.desc" label="描述"  />
                    </div>
                </el-form-item>
            </div>
            <div class="form-group">
                <el-form-item label="动画滑入方式">
                    <div class="">
                        <el-radio-group v-model="currentElement.type" size="mini">
                            <el-radio-button value="left" label="left">左边滑入</el-radio-button>
                            <el-radio-button value="right" label="right">右边滑入</el-radio-button>
                        </el-radio-group>
                    </div>
                </el-form-item>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props:["currentElement"],
        name: "in-text-animate",
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
        methods:{
            getOssImage(){
                return this.$root.$data.ossDomain?this.$root.$data.ossDomain + '':"";
            },
            xhrSubmitForCategory(e,item){

                console.log(item);

                if(!UploadImage.setVue(this).check(e)) return false;

                var self= this;
                var files = e.target.files[0];
                var data = new FormData();
                data.append('pageId',self.$root.$data.id);
                data.append('file',files);
                $.ajax({
                    url:self.ajaxUpload,
                    type:'post',
                    data:data,
                    dataType:'json',
                    processData: false,
                    contentType:false,
                    success:function(response){
                        if(response.status==true){
                           item.nodes.src=self.getOssImage() + response.file;
                        }else{
                            self.$alert(response.message);
                        }
                    }
                })
            },
        }
    }
</script>

<style scoped>

</style>