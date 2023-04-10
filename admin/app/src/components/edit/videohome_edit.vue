<template>
    <div class="form-group">

        <div class="form-group" style="margin-top: 10px;">
            <el-form-item label="标题" >
                <div class="">
                    <el-input  v-model="currentElement.title"   size="mini" />
                </div>
            </el-form-item>
        </div>
        <div class="form-group" style="margin-top: 10px;">
            <el-form-item label="高度" >
                <div class="">
                    <el-input-number  v-model="currentElement.height" label="列" :min="0"  size="mini" />
                </div>
            </el-form-item>
        </div>
        <div class="form-group" style="margin-top: 10px;">
            <el-form-item label="截图PC" >
                <div class="">
                    <img :src="currentElement.src " style="width:100px;" />
                    <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input"
                           @change="xhrSubmitForCategory($event,currentElement,'src')">
                </div>
            </el-form-item>
        </div>
        <div class="form-group" style="margin-top: 10px;">
            <el-form-item label="截图h5" >
                <div class="">
                    <img :src="currentElement.src_h5 " style="width:100px;" />
                    <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input"
                           @change="xhrSubmitForCategory($event,currentElement,'src_h5')">
                </div>
            </el-form-item>
        </div>
        <div class="form-group" style="margin-top: 10px;">
            <el-form-item label="视频PC地址" >
                <div class="">
                    <el-input  v-model="currentElement.video"   size="mini" />
                </div>
            </el-form-item>
        </div>
        <div class="form-group" style="margin-top: 10px;">
            <el-form-item label="视频H5地址" >
                <div class="">
                    <el-input  v-model="currentElement.video_h5"   size="mini" />
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
        name: "videohome-edit",
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
                    var height =  750 / (response.info[0] / response.info[1]);
                    height = Math.round(height);
                    //this.$set(this.currentElement,'height',height);
                }else{
                    this.$refs.upload.clearFiles()
                    this.$alert(response.message,'提示');
                }

            },
            handleError(err, file, fileList){
                console.log('err',err);
                this.$alert('上传错误','提示');
            },
            xhrSubmitForCategory(e,currentElement,type){

                if(this.$root.$data.pageSource==""){
                    if(!UploadImage.setVue(this).check(e)) return false;
                }

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
                            var height =  750 / (response.info[0] / response.info[1]);
                            currentElement.height= Math.round(height);
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
