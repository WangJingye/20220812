<template>
    <div class="form-group">

        <div class="form-group" style="margin-top: 10px;">
            <el-form-item label="高度" >
                <div class="">
                    <el-input-number  v-model="currentElement.height" label="列" :min="0"  size="mini" />
                </div>
            </el-form-item>
        </div>
        <div class="form-group" style="margin-top: 10px;">
            <el-form-item label="截图" >
                <div class="">
                    <img :src="currentElement.src " style="width:100px;" />
                    <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input"
                           @change="xhrSubmitForCategory($event,currentElement,'src','image')">
                </div>
            </el-form-item>
        </div>
        <div class="form-group" style="margin-top: 10px;" v-if="false">
            <el-form-item label="小程序截图" >
                <div class="">
                    <img :src="currentElement.wechat_src " style="width:100px;" />
                    <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input"
                           @change="xhrSubmitForCategory($event,currentElement,'wechat_src')">
                </div>
            </el-form-item>
        </div>
        <div class="form-group" style="margin-top: 10px;">
            <el-form-item label="视频地址" >
                <div class="">
                    <el-input  v-model="currentElement.video"   size="mini" />
                    <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input"
                           @change="xhrSubmitForCategory($event,currentElement,'video','video')">
                </div>
            </el-form-item>
        </div>
        <el-form-item label="自动运行">
            <div class="">
                <el-radio-group v-model="currentElement.isAuto" size="mini">
                    <el-radio-button value="true" label="true">自动</el-radio-button>
                    <el-radio-button value="false" label="false">手动</el-radio-button>
                </el-radio-group>
            </div>
        </el-form-item>
        <el-form-item label="循环">
            <div class="">
                <el-radio-group v-model="currentElement.isLoop" size="mini">
                    <el-radio-button value="true" label="true">是</el-radio-button>
                    <el-radio-button value="false" label="false">否</el-radio-button>
                </el-radio-group>
            </div>
        </el-form-item>

    </div>
</template>


<script>

    export default {
        props: ['currentElement'],
        name: "video-edit",
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
            if(this.$root.$data.pageSource!=""){
                this.ajaxUpload=this.$root.$data.uploadUrl;
            }

        },
        watch: {},
        methods: {
            onBeforeUpload(file)
            {
                if(this.$root.$data.pageSource==""){
                    return UploadImage.setVue(this).onBeforeUpload(file);
                }else{
                    return true;
                }
            },
            getOssImage(){
                return this.$root.$data.ossDomain?this.$root.$data.ossDomain + '':"";
            },
            uploadFile: function (params) {
                var self = this,
                    file = params.file,
                    fileType = file.type,
                    isImage = fileType.indexOf('image') != -1;
                if (isImage) {
                    var data = new FormData();
                    data.append('pageId',self.$root.$data.id);
                    data.append('file',file);
                    $.ajax({
                        url:self.ajaxUpload,
                        type:'post',
                        data:data,
                        dataType:'json',
                        processData: false,
                        contentType:false,
                        success:function(response){
                            if(response.status==true){
                                self.currentElement.src.pc=self.getOssImage() + response.file;
                                self.currentElement.src.h5=self.getOssImage() + response.file;
                                //var height =  750 / (response.info[0] / response.info[1]);
                                //height = Math.round(height);
                                //this.$set(this.currentElement,'height',height);
                            }else{
                                self.$alert(response.message);
                            }
                        }
                    })
                }
            },
            handleSuccess(response, file, fileList){
                if(response.status==true){
                    this.currentElement.src.pc=this.getOssImage() + response.file;
                    this.currentElement.src.h5=this.getOssImage() + response.file;
                }else{
                    this.$refs.upload.clearFiles()
                    this.$alert(response.message,'提示');
                }

            },
            handleError(err, file, fileList){
                console.log('err',err);
                this.$alert('上传错误','提示');
            },
            xhrSubmitForCategory(e,currentElement,type,check){

                // if(!UploadImage.setVue(this).onBeforeUpload(e.target.files[0],check)){
                //     return false;
                // }
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
                            if(check=='video'){
                                 var height =  750 / (response.info[0] / response.info[1]);
                                currentElement.height= Math.round(height);
                                currentElement.src=self.getOssImage() + response.screenshot;
                            }
                            currentElement[type]=self.getOssImage() + response.file;
                        }else{
                            self.$alert(response.message);
                        }
                    }
                })
            },

        }
    };
</script>
<style scoped>

</style>
