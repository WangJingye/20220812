<template>
    <div class="form-group">
        <div class="">
            <div>
                <el-upload
                        ref="upload"
                        class="upload-image"
                        :before-upload="onBeforeUpload"
                        :action="ajaxUpload"
                        :http-request="uploadFile"
                        :on-error="handleError"
                        list-type="picture"
                        :show-file-list="false"
                        v-show="['pc','h5'].includes($root.$data.mediaType)"
                >
                    <el-button size="small" type="primary">上传</el-button>
                    <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过500kb</div>
                </el-upload>
                <template v-if="['pc','h5'].includes($root.$data.mediaType)">
                    <el-tabs type="border-card" class="type_tags" >
                        <el-tab-pane label="pc">
                            <img :src="currentElement.src.pc " style="width:100px;" />
                            <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input"
                                   @change="xhrSubmitForCategory($event,currentElement,'pc')">
                        </el-tab-pane>
                        <el-tab-pane label="h5" >
                            <img :src="currentElement.src.h5 " style="width:100px;"  />
                            <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input"
                                   @change="xhrSubmitForCategory($event,currentElement,'h5')">
                        </el-tab-pane>
                    </el-tabs>
                </template>

                <template v-if="$root.$data.mediaType=='wechat'">
                    <div>
                        <img :src="currentElement.src.h5 " style="width:100px;" />
                        <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input"
                               @change="xhrSubmitForCategory($event,currentElement,'h5')">
                    </div>
                </template>
                <el-form-item label="text1" >
                    <div class="">
                        <el-input v-model="currentElement.text1"  label="文本1" ></el-input>
                    </div>
                </el-form-item>
                <el-form-item label="text2" >
                    <div class="">
                        <el-input v-model="currentElement.text2"  label="文本2" ></el-input>
                    </div>
                </el-form-item>

                <el-form-item label="按钮" >
                    <div class="">
                        <el-input v-model="currentElement.button"  label="按钮" ></el-input>
                    </div>
                </el-form-item>
            </div>
        </div>
        <div class="">
            <links :currentElement="currentElement" v-if="true" />
        </div>
    </div>
</template>


<script>

    export default {
        props: ['currentElement'],
        name: "image-flex-image-edit",
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
                            currentElement.src[type]=self.getOssImage() + response.file;
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
