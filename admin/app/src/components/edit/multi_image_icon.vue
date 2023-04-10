<template>
    <div class="multi_image_icon">

        <div class="">
            <files :currentElement="currentElement" v-if="false" />
            <el-upload
                    v-if="currentElement.nodes==false || currentElement.nodes.length <8"
                    ref="upload"
                    class="upload-image"
                    :before-upload="onBeforeUpload"
                    :action="ajaxUpload"
                    :http-request="uploadFile"
                    :on-error="handleError"
                    list-type="picture"
                    :multiple="true"
                    :show-file-list="false"
            >
                <el-button size="small" type="primary">上传</el-button>
                <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过500kb</div>
            </el-upload>

            <div class="form-group" style="margin-top: 10px;">
                <el-form-item label="标题" >
                    <div class="">
                        <input class="form-control" v-model="currentElement.title" placeholder="标题">
                    </div>
                </el-form-item>
            </div>

            <div class="form-group" v-if="false">
                <el-form-item label="高度" >
                    <div class="">
                        <el-input-number v-model="currentElement.height" label="高度" size="mini"></el-input-number>
                    </div>
                </el-form-item>
            </div>


            <div class="form-group">
                <template v-if="['pc','h5'].includes($root.$data.mediaType)">
                    <el-form-item label="列数/pc" >
                        <div class="">
                            <el-input-number v-model="currentElement.columns.pc" label="列" :min="2" :max="6" size="mini"></el-input-number>
                        </div>
                    </el-form-item>
                    <el-form-item label="列数/h5" >
                        <div class="">
                            <el-input-number v-model="currentElement.columns.h5" label="列" :min="1" :max="3" size="mini"></el-input-number>
                        </div>
                    </el-form-item>
                </template>
                <template v-if="$root.$data.mediaType=='wechat'">
                    <el-form-item label="列数" >
                        <div class="">
                            <el-input-number v-model="currentElement.columns.h5" label="列" :min="1" :max="3" size="mini"></el-input-number>
                        </div>
                    </el-form-item>
                </template>
            </div>


            <template v-if="currentElement.nodes!==false">
                <draggable
                        class="dragArea list-group "
                        :list="currentElement.nodes"
                        @change="sort"
                        handle=".handle"
                >
                    <div class="list-group-item " v-for="(item,i) in currentElement.nodes" :key="i" style="position: relative;cursor: default;">
                        <i class="el-icon-document handle" style="position: absolute;left: 0px;top:0px;cursor: move"></i>
                        <i class="el-icon-delete" style="position: absolute;right: 0px;top:0px;" @click="removeAt(currentElement,i)"></i>

                        <div>
                            <img :src="item.src " style="width:100px;" />
                            <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input"
                                   @change="xhrSubmitForCategory($event,item)">
                        </div>
                        <div class="links">
                            <links :currentElement="item" v-if="true" />
                        </div>
                        <el-form-item label="标题" >
                            <div class="">
                                <input class="form-control" v-model="item.title" placeholder="标题">
                            </div>
                        </el-form-item>
                    </div>
                </draggable>
            </template>
        </div>
    </div>
</template>


<script>
    export default {
        props: ['currentElement'],
        name: "multi-image-icon",
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
            onBeforeUpload(file)
            {
                const isIMAGE = file.type === 'image/jpeg'||'image/gif'||'image/png';
                var errorMessage="pageId异常错误";

                if (!isIMAGE) {
                    this.$message.error('上传文件只能是图片格式!');
                }


                if(this.$root.$data.name == ""){
                    this.$message.error("文章名称必填");
                    return false;
                }
                if(this.$root.$data.key ==""){
                    this.$message.error("文章KEY不能为空");
                    return false;
                }

                var self= this;
                if(this.$root.$data.id=="" && this.$root.$data.key ){
                    var params={
                        name:this.$root.$data.name,
                        key:this.$root.$data.key
                    }
                    $.ajax({
                        async:false,
                        url:self.$root.$data.apiDomain+'admin/page/ajaxKey',
                        type:'post',
                        data:params,
                        dataType:'json',
                        success:function(result){
                            if(result.status == 1){
                                self.$root.$data.id=result.pageId;
                            }else{
                                errorMessage= result.message;
                            }
                        }
                    })
                }
                if(!self.$root.$data.id > 0){
                    self.$message.error(errorMessage);
                    return false;
                }
                return isIMAGE
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
                                var height =  750 / (response.info[0] / response.info[1]);
                                height = Math.round(height);


                                if(height > self.currentElement.height){
                                    self.currentElement.height=height;
                                }

                                if(self.currentElement.nodes===false){
                                    self.currentElement.nodes=[];
                                }

                                self.currentElement.nodes.push( {
                                    tag:'image',
                                    src:self.getOssImage() + response.file,
                                    'action':{
                                        data:{},
                                        type:'none',
                                        route:false
                                    },
                                    height:height
                                });
                            }else{
                                self.$alert(response.message);
                            }
                        }
                    })
                }
            },
            swiperSuccess(response, file, fileList){
                var height =  750 / (response.info[0] / response.info[1]);
                height = Math.round(height);


                if(height > this.currentElement.height){
                    this.currentElement.height=height;
                }

                if(this.currentElement.nodes===false){
                    this.currentElement.nodes=[];
                }

                this.currentElement.nodes.push( {
                    tag:'image',
                    src:this.getOssImage() + response.file,
                    'action':{
                        data:{},
                        type:'none',
                        route:false
                    },
                    height:height
                });
            },
            handleError(err, file, fileList){
                console.log('err',err);
                this.$alert('上传错误','提示');
            },
            xhrSubmitForCategory(e,item,type){

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
                            item.src=self.getOssImage() + response.file;
                        }else{
                            self.$alert(response.message);
                        }
                    }
                })
            },
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

        }
    };
</script>
<style scoped>

</style>
