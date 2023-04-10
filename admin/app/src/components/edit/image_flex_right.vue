<template>
    <div class="image_flex_right">
        <div class="">
            <el-upload
                    ref="upload"
                    class="upload-image"
                    :before-upload="onBeforeUpload"
                    :action="ajaxUpload"
                    :http-request="uploadFile"
                    :on-error="handleError"
                    list-type="picture"
                    :multiple="false"
                    :show-file-list="false"
            >
                <el-button size="small" type="primary">上传</el-button>
                <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过500kb</div>
            </el-upload>



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
                        <img :src="item.src " style="width:100px;" />
                        <el-form-item label="宽高比" >
                            <div class="">
                                <input class="form-control" v-model="item.aspect_ratio" placeholder="" >
                            </div>
                        </el-form-item>
                        <template v-if="false">

                        <el-form-item label="宽度" v-if="true">
                            <div class="">
                                <div style="margin-top: 15px;">
                                    <el-slider v-model="item.width" :min="60" :max="100"  show-input> </el-slider>
                                </div>
                            </div>
                        </el-form-item>
                        <el-form-item label="对齐方式">
                            <div class="">
                                <el-radio-group v-model="item.align" size="mini">
                                    <el-radio-button value="auto" label="auto">自动</el-radio-button>
                                    <el-radio-button value="flex-start" label="flex-start">靠左</el-radio-button>
                                    <el-radio-button value="center" label="center">居中</el-radio-button>
                                    <el-radio-button value="flex-end" label="flex-end">靠右</el-radio-button>
                                </el-radio-group>
                            </div>
                        </el-form-item>
                        </template>
                        <el-form-item label="MarginTop" v-if="false">
                            <div class="">
                                <div style="margin-top: 15px;">
                                    <el-slider  v-model="item.MarginTop" :min="-100" :max="100" show-input> </el-slider>
                                </div>
                            </div>
                        </el-form-item>
                        <el-form-item label="标题" >
                            <div class="">
                                <input class="form-control" v-model="item.title" placeholder="标题">
                            </div>
                        </el-form-item>
                        <el-form-item label="描述" >
                            <div class="">
                                <input class="form-control" v-model="item.desc" placeholder="描述">
                            </div>
                        </el-form-item>
                        <el-form-item label="按钮" >
                            <div class="">
                                <input class="form-control" v-model="item.button" placeholder="按钮">
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
        props:["currentElement"],
        name: "image-flex-right",
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
                                    aspect_ratio:(response.info[0] / response.info[1]).toFixed(2),
                                    width:100,
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
                    aspect_ratio:(response.info[0] / response.info[1]).toFixed(2),
                    width:100,
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

    }
</script>

<style scoped>

</style>