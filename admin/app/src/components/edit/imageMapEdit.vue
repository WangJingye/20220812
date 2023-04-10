<template >
    <div class="form-group">
        <div class="">
            <el-upload
                    ref="upload"
                    class="upload-image"
                    :before-upload="onBeforeUpload"
                    :action="ajaxUpload"
                    :http-request="uploadFile"
                    :on-error="handleError"
                    list-type="picture"
                    :show-file-list="false"
                    v-show="['wechat','h5'].includes($root.$data.mediaType)"
            >
                <el-button size="small" type="primary">上传</el-button>
                <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过500kb</div>
            </el-upload>
            <div class="list-group-item "  style="cursor: default;" v-if="currentElement.src">
                <img :src="currentElement.src " style="width:100px;" />
            </div>
        </div>
        <div class="form-group" style="margin-top: 10px;" v-if="false">
            <el-form-item label="高度"  v-if="false">
                <div class="">
                    <el-input-number v-model="currentElement.height" :min="100"
                                     label="高度" size="mini"></el-input-number>
                </div>
            </el-form-item>
        </div>
        <div class="form-group" style="margin-top: 10px;">
            <el-form-item label="行数" >
                <div class="">
                    <el-input-number @change="applyActions" v-model="currentElement.rows" :min="1" :max="10"
                                     label="行" size="mini" ></el-input-number>
                </div>
            </el-form-item>
        </div>
        <div class="form-group">
            <el-form-item label="列数" >

                <div class="">
                    <el-input-number @change="applyActions" v-model="currentElement.columns" :min="1" :max="3"
                                     label="列" size="mini"></el-input-number>
                </div>
            </el-form-item>
        </div>
        <div class="form-group">
            <template  v-for=" (rowsAction,row) in  currentElement.actions ">
                <div v-for=" (action,column) in  rowsAction ">
                    <span style="position: relative;top: 45px;">链接{{ row * currentElement.columns + column + 1 }}:</span>
                    <links :currentElement="action"  :key="row * currentElement.columns + column + 1"/>
                </div>
            </template>


        </div>

    </div>
</template>


<script>

    export default {
        props: ['currentElement'],
        name: "image-map-edit",
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
            applyActions(value){
                let rows = this.currentElement.rows;
                let columns = this.currentElement.columns;
                let actions = this.currentElement.actions;


                if(rows > actions.length){
                    let newRow=[];
                    for(let i = 0 ; i<columns;i++){
                        newRow.push({
                            'action':{
                                data:{},
                                type:'none',
                                route:false
                            }
                        });
                    }
                    actions.push(newRow);

                }
                if(rows < actions.length){
                    actions.pop();
                }

                if(columns > actions[0].length){
                    for(let i = 0; i <  actions.length; i++){
                        actions[i].push({
                            'action':{
                                data:{},
                                type:'none',
                                route:false
                            }
                        });
                    }
                }
                if(columns < actions[0].length){
                    for(let i = 0; i <  actions.length; i++){
                        actions[i].pop();
                    }
                }

            },

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
                                        self.currentElement.src=self.getOssImage() + response.file;
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
