<template>
    <div class="image_flex">
        <div class="">

            <img :src="currentElement.src " style="width:100px;" />

            <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input"
                   @change="xhrSubmitForCategory($event,currentElement)">

            <el-form-item label="对齐方式">
                <div class="">
                    <el-radio-group v-model="currentElement.align" size="mini">
                        <el-radio-button value="left" label="left">靠左</el-radio-button>
                        <el-radio-button value="center" label="center">居中</el-radio-button>
                        <el-radio-button value="right" label="right">靠右</el-radio-button>
                    </el-radio-group>
                </div>
            </el-form-item>
            <el-form-item label="标题" >
                <div class="">
                    <input class="form-control" v-model="currentElement.title" placeholder="标题">
                </div>
            </el-form-item>
            <el-form-item label="描述" >
                <div class="">
                    <input class="form-control" v-model="currentElement.desc" placeholder="描述">
                </div>
            </el-form-item>
            <el-form-item label="按钮" >
                <div class="">
                    <input class="form-control" v-model="currentElement.button" placeholder="按钮">
                </div>
            </el-form-item>
            <div class="links">
                <links :currentElement="currentElement" v-if="true" />
            </div>



        </div>
        </div>
</template>

<script>
    export default {
        props:["currentElement"],
        name: "image-flex",
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