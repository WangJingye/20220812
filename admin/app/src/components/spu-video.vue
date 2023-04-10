<template>
    <div class="spu-video">
        <input type="hidden" :name="input_name" v-model="JSON.stringify(responseData)" />
        <div v-if="false">
            {{responseData}}
        </div>



        <div class="layui-form-item">
            <label  class="layui-form-label">视频截图:</label>
            <div class="layui-input-block">
                <img v-if="responseData.data.poster" :src="responseData.data.poster" style="width:100px;"/>
                <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input" @change="xhrSubmit($event)">
            </div>
        </div>
        <div class="layui-form-item">
            <label  class="layui-form-label">视频URL:</label>
            <div class="layui-input-block">
                <input class="layui-input" type="url" v-model="responseData.data.src" placeholder="视频url"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label  class="layui-form-label">自动运行:</label>
            <div class="layui-input-block">
                <el-radio v-model="responseData.data.isAuto" :label="true">自动</el-radio>
                <el-radio v-model="responseData.data.isAuto" :label="false">手动</el-radio>
            </div>
        </div>
        <div class="layui-form-item">
            <label  class="layui-form-label"></label>
            <div class="layui-input-block">
                <span class="layui-btn" @click="reset">删除视频</span>
            </div>
        </div>

    </div>
</template>


<script>
    export default {
        props: ['video_data','input_name'],
        name: "spu-video",
        data() {
            return {
                rawData:[],
                fileList:[],
                responseData: {
                    tag: "video",
                    data:{
                        poster:"",
                        src:"",
                        isAuto:false
                    }
                },
                ajaxUpload:this.$root.$data.apiDomain+'admin/page/ajaxUpload',
                dialogImageUrl: '',
                dialogVisible: false,
                disabled: false
            };
        },
        created:function (){
        },
        mounted: function () {

           this.responseData=this.video_data;
        },
        watch:{

        },
        computed:{
        },
        methods: {
            reset:function (){
               this.responseData.data.poster="";
               this.responseData.data.src="";
               this.responseData.data.isAuto="";
            },
            getOssImage(){
                return this.$root.$data.ossDomain + '';
            },
            handleError(err, file, fileList){
                console.log('err',err);
                this.$alert('上传错误','提示');
            },
            getFileList:function (){
                let files=[];
                for (let item of this.rawData){
                    files.push({
                        name:"",
                        url:item.data.src,
                    });
                }
                return files;
            },
            getResponseData:function (){
                let responseData=[];
                for(let item of this.fileList){
                    responseData.push({
                        type:"image",
                        data:{
                            src:item.url,
                            action:null
                        }
                    });
                }
                return responseData;
            },
            handleSuccess(response, file, fileList){
                this.fileList.push({
                    name:"",
                    url:this.getOssImage() + response.file
                });
            },
            onBeforeUpload(file)
            {
                const isIMAGE = file.type === 'image/jpeg'||'image/gif'||'image/png';
                const isLt512k = file.size / 1024 / 512 < 1;

                if (!isIMAGE) {
                    this.$message.error('上传文件只能是图片格式!');
                }
                if (!isLt512k) {
                    this.$message.error('上传文件大小不能超过 512k!');
                }
                return isIMAGE && isLt512k;
            },
            bgImg:function (url){
                return {
                   // backgroundImage:'url('+this.$root.$data.apiDomain+url+')'
                    backgroundImage:'url('+url+')'
                }
            },
            xhrSubmit(e){
                var files = e.target.files[0];
                if(files.size > (1024 * 512) ){
                    this.$alert('图片大小为：'+Math.round(files.size/1024)+'K,不能超过512K');
                    return false;
                }
                var self= this;
                var files = e.target.files[0];
                var param = new FormData();
                param.append('file',files);
                $.ajax({
                    url:self.ajaxUpload,
                    type:'post',
                    data:param,
                    dataType:'json',
                    processData: false,
                    contentType:false,
                    success:function(response){
                        if(response.status==true){
                            self.responseData.data.poster=self.getOssImage() + response.file;
                        }else{
                            self.$alert(response.message);
                        }
                    }
                })
            },

        }
    };
</script>
<style >
.layui-form-radioed,.layui-form-radio{
    display: none !important;
}
</style>
