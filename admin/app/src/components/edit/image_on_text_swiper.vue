<template>
    <div class="image_on_text_swiper">
        <div class="">
            <el-upload
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
                            <img :src="item.src" style="width:100px;" />
                            <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input"
                                   @change="xhrSubmitForCategory($event,item,'active')">
                        </div>



                        <el-form-item label="图片上的文字" >
                            <div class="">
                                <input class="form-control" v-model="item.on_text.title" placeholder="标题">
                                <input class="form-control" v-model="item.on_text.desc" placeholder="描述">
                            </div>
                        </el-form-item>
                        <el-form-item label="图片描述" >
                            <div class="">
                                <input class="form-control" v-model="item.description" placeholder="图片描述">
                            </div>
                        </el-form-item>

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
        name: "image_on_text_swiper",
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
                return UploadImage.setVue(this).onBeforeUpload(file);
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
                                    action:{
                                        data:{},
                                        type:'none',
                                        route:false
                                    },
                                    on_text:{
                                        title:"",
                                        desc:""
                                    },
                                    description:"",
                                    height:height
                                });
                            }else{
                                self.$alert(response.message);
                            }
                        }
                    })
                }
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
