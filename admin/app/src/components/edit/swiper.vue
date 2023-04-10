<template>
    <div class="multi_image_product">

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

            <el-form-item label="视频播放">
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

                        <template v-if="['pc','h5'].includes($root.$data.mediaType)">
                            <el-tabs type="border-card" class="type_tags" >
                                <el-tab-pane label="PC">
                                    <img :src="item.src.pc " style="width:100px;" />
                                    <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input"
                                           @change="xhrSubmitForCategory($event,item,'pc')">
                                </el-tab-pane>
                                <el-tab-pane label="H5">
                                    <img :src="item.src.h5 " style="width:100px;" />
                                    <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input"
                                           @change="xhrSubmitForCategory($event,item,'h5')">
                                </el-tab-pane>
                            </el-tabs>
                        </template>

                        <template v-if="$root.$data.mediaType=='wechat'">
                            <div>
                                <img :src="item.src.h5.src " style="width:100px;" />
                            </div>
                        </template>


                        <div class="links">
                            <links :currentElement="item" v-if="true" />
                        </div>

                    </div>
                </draggable>
            </template>
        </div>
    </div>
</template>


<script>
    export default {
        props: ['currentElement'],
        name: "swiper",
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
                //return UploadImage.setVue(this).onBeforeUpload(file);
            },
            getOssImage(){
                return this.$root.$data.ossDomain?this.$root.$data.ossDomain + '':"";
            },
            uploadFile: function (params) {
                var self = this,
                    file = params.file,
                    fileType = file.type,
                    isImage = fileType.indexOf('image') != -1;
                if (true) {
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

                                if(response.type[0]=='video'){
                                    var media = {
                                        "type":response.type[0],
                                        "src":self.getOssImage() + response.screenshot,
                                        "video":self.getOssImage() + response.file,
                                    }
                                }else{
                                    var media = {
                                        "type":response.type[0],
                                        "src":self.getOssImage() + response.file,
                                        "video":"",
                                    }
                                }
                                self.currentElement.nodes.push( {
                                    tag:'image',
                                    src:{
                                        pc:self.getOssImage() + response.file,
                                        h5:media
                                    },
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
                    src:{
                        pc:this.getOssImage() + response.file,
                        h5:this.getOssImage() + response.file
                    },
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

               // if(!UploadImage.setVue(this).check(e)) return false;

                var files = e.target.files[0];

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
                            item.src[type]=self.getOssImage() + response.file;
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
