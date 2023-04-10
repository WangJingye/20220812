<template>
    <div class="multi_image_product_swiper">

        <div class="">
            <files :currentElement="currentElement" v-if="false" />
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
              <el-form-item label="上传图片">
                <el-button size="small" type="primary">上传</el-button>
                <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过500kb</div>
              </el-form-item>
            </el-upload>
            <el-form-item label="风格">
              <select v-model="currentElement.style" required class="el-input__inner inner_link_type">
                <option value='style_1'>风格1</option>
                <option value='style_2'>风格2</option>
                <option value='style_3'>风格3</option>
              </select>
            </el-form-item>

            <div class="form-group">
                <template v-if="['pc','h5'].includes($root.$data.mediaType)">
                    <el-form-item label="列数/pc" v-if="false">
                        <div class="">
                            <el-input-number :disabled="true" v-model="currentElement.columns.pc" label="列" :min="2" :max="4" size="mini"></el-input-number>
                        </div>
                    </el-form-item>
                    <el-form-item label="列数/h5" v-if="false">
                        <div class="">
                            <el-input-number :disabled="true" v-model="currentElement.columns.h5" label="列" :min="1" :max="2" size="mini"></el-input-number>
                        </div>
                    </el-form-item>
                </template>
                <template v-if="$root.$data.mediaType=='wechat' && false" >
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

                        <template v-if="['pc','h5'].includes($root.$data.mediaType)">
                            <el-tabs type="border-card" class="type_tags" >
                                <el-tab-pane label="active">
                                    <img :src="item.src.active " style="width:100px;" />
                                    <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input"
                                           @change="xhrSubmitForCategory($event,item,'active')">
                                </el-tab-pane>
                                <el-tab-pane label="hover">
                                    <img :src="item.src.hover " style="width:100px;" />
                                    <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input"
                                           @change="xhrSubmitForCategory($event,item,'hover')">
                                </el-tab-pane>
                            </el-tabs>
                        </template>

                        <template v-if="$root.$data.mediaType=='wechat'">
                            <div>
                                <img :src="item.src.active " style="width:100px;" />
                                <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input"
                                       @change="xhrSubmitForCategory($event,item,'active')">
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
        name: "multi-image-product-swiper",
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
                                    src:{
                                        active:self.getOssImage() + response.file,
                                        hover:self.getOssImage() + response.file
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
                        active:this.getOssImage() + response.file,
                        hover:this.getOssImage() + response.file
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
