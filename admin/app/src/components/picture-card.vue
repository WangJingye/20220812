<template>
    <div class="picture-card" style="margin-top: 60px;">
        <span class="layui-btn layui-btn-normal"  @click="dialogVisibleKV = true" style="position: absolute;left: 20px;top: 20px;">KV JSON</span>
        <input type="hidden" :name="input_name" v-model="JSON.stringify(responseData)" />

        <el-dialog class="" title="KV JSON" :visible.sync="dialogVisibleKV" :close-on-click-modal="false" :modal="false" width="80%" top="0">
            <rawDisplayer class="col-6" :value="responseData" title="KV JSON"/>
        </el-dialog>

        <div style="">
            <el-dialog class="select-product-dialog" title=" " :visible.sync="dialogVisible" :close-on-click-modal="false" width="80%" top="200px">
                <div >
                    <div >
                        <table class="layui-table" lay-skin="" lay-size="sm">
                            <colgroup>
                                <col width="200"/>
                                <col width="100"/>
                                <col width="100"/>
                            </colgroup>
                            <thead>
                            <tr>
                                <td>图片</td>
                                <th>类型</th>
                                <th>视频地址</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                        <img :src="currentItem.data.src" style="width:50%;"  />

                                </td>
                                <td>

                                    <el-radio-group v-model="currentItem.tag" size="mini" @change="changeTag">
                                        <el-radio-button   value="image" label="image">图片</el-radio-button>
                                        <el-radio-button   value="video" label="video">视频</el-radio-button>
                                    </el-radio-group>
                                    <video ref="video_tag" muted style="" width="200"   :src="currentItem.data.video"     loop autoplay   v-if="currentItem.tag=='video'">
                                        your browser does not support the video tag
                                    </video>
                                </td>
                                <td>
                                    <el-input type="textarea" v-show="currentItem.tag=='video'" v-model="currentItem.data.video" label="视频"  />
                                    <input v-show="currentItem.tag=='video'" style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input"
                                           @change="xhrSubmitForCategory($event,currentItem)">
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </el-dialog>
        </div>
        <draggable
                :list="fileList"
                class="el-upload-list el-upload-list--picture-card"
                ghost-class="ghost"
        >
            <div
                    class="el-upload-list__item is-success"
                    v-for="(element,index) in fileList"
                    :key="index"
                    style="
                                    height: 100%;
                                    background-origin: padding-box;
                                    background-position-x: 50%;
                                    background-position-y: 50%;
                                    background-repeat: no-repeat;
                                    background-size: contain;"
                    :style="bgImg(element.url)"
            >
                <span class="el-upload-list__item-actions">
                    <span v-if="responseData[index].tag=='video'"  class="el-upload-list__item-delete"><i class="el-icon-video-play"></i></span>
                    <span @click="remove(fileList,index)" class="el-upload-list__item-delete"><i class="el-icon-delete"></i></span>
                    <span @click="changeType(fileList,index)" class="el-upload-list__item-delete"><i class="el-icon-s-tools"></i></span>
                </span>
            </div>
        </draggable>
        <el-upload
                style="display: inline;"
                :file-list="fileList"
                :action="ajaxUpload"
                :on-remove="handleRemove"
                :before-upload="onBeforeUpload"
                :on-success="handleSuccess"
                :on-error="handleError"
                list-type="picture-card"
                :multiple="true"
                :auto-upload="true">
            <i slot="default" class="el-icon-plus"></i>
            <div  slot="file"  slot-scope="{file}"
                                    style="
                                    height: 100%;
                                    background-origin: padding-box;
                                    background-position-x: 50%;
                                    background-position-y: 50%;
                                    background-repeat: no-repeat;
                                    background-size: contain;"
                                    :style="bgImg(file.url)"
                                    >
                <span class="el-upload-list__item-actions">

                    <span v-if="!disabled" class="el-upload-list__item-delete" @click="handleRemove(file)">
                      <i class="el-icon-delete"></i>
                    </span>
                </span>
            </div>
        </el-upload>


    </div>
</template>


<script>
    import draggable from "@/vuedraggable";
    export default {
        props: ['images_list','input_name'],
        name: "picture-card",
        components: {draggable},
        data() {
            return {
                rawData:[],
                fileList:[],
                responseData:[],
                ajaxUpload:this.$root.$data.apiDomain+'admin/page/ajaxUploadProduct' + this.$root.$data.queryString,
                dialogImageUrl: '',
                dialogVisible: false,
                dialogVisibleKV:false,
                disabled: false,
                currentItem:{data:{}},
            };
        },
        created:function (){
            this.rawData=JSON.parse(decodeURIComponent(this.images_list));
            this.fileList=this.getFileList();
            this.responseData=this.getResponseData();
        },
        mounted: function () {
        },
        watch:{
            'fileList':function (fileList){
                let responseData=[];
                for(let item of fileList){
                    if(item.tag=='video'){
                        responseData.push({
                            tag:'video',
                            data:{
                                src:item.url,
                                video:item.video
                            }
                        });

                    }else{
                        responseData.push({
                            tag:'image',
                            data:{
                                src:item.url
                            }
                        });
                    }
                }
                this.responseData=responseData;
            }
        },
        computed:{
        },
        methods: {
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
                    if(item.tag=='video'){
                        files.push({
                            tag:item.tag,
                            url:item.data.src,
                            video:item.data.video
                        });
                    }else{
                        files.push({
                            tag:item.tag,
                            url:item.data.src,
                        });
                    }

                }
                return files;
            },
            getResponseData:function (){
                let responseData=[];
                for(let item of this.fileList){
                    if(item.tag=='video'){
                        responseData.push({
                            tag:'video',
                            data:{
                                src:item.url,
                                video:item.video
                            }
                        });

                    }else{
                        responseData.push({
                            tag:'image',
                            data:{
                                src:item.url
                            }
                        });
                    }

                }
                return responseData;
            },
            handleSuccess(response, file, fileList){
                if(response.type[0]=='video'){
                    this.fileList.push({
                        name:"",
                        tag:"video",
                        url:this.getOssImage() + response.screenshot,
                        video:this.getOssImage() + response.file
                    });
                }else{
                    this.fileList.push({
                        name:"",
                        tag:"image",
                        url:this.getOssImage() + response.file
                    });
                }
            },
            onBeforeUpload(file)
            {

                let enableType=['video/mp4','image/jpeg','image/jpg','image/gif','image/png'];
                let isIMAGE = enableType.includes(file.type);
                if (!isIMAGE) {
                    this.$message.error('上传文件类型错误：' + enableType.join(" "));
                    return false;
                }
                if(file.type=='video/mp4'){
                    var  isLt512M = file.size / 1024 / 10120 < 1;
                    if (!isLt512M) {
                        this.$message.error('视频上传文件大小不能超过 10M!');
                        return false;
                    }
                }else{
                    var isLt512k = file.size / 1024 / 2024 < 1;
                    if (!isLt512k) {
                        this.$message.error('图片上传文件大小不能超过 2M!');
                        return false;
                    }
                }
            },
            bgImg:function (url){
                return {
                   // backgroundImage:'url('+this.$root.$data.apiDomain+url+')'
                    backgroundImage:'url('+url+')'
                }
            },
            handleRemove(file) {

                this.fileList.forEach((item,key)=>{
                    if(item.uid==file.uid){
                        //console.log(file.uid,item.uid,key);
                        // this.$delete(fileList,key);
                        this.fileList.splice(key, 1);
                    }
                    console.log( this.fileList);
                });
            },
            remove(list,idx){
                list.splice(idx, 1);
            },
            changeType(list,idx){
                this.dialogVisible=true;
                this.currentItem = this.responseData[idx];
                console.log(this.currentItem);
            },
            handlePictureCardPreview(file) {
                this.dialogImageUrl = file.url;
                this.dialogVisible = true;
            },
            changeTag:function (e){
             //   console.log(e);
                if(e=='image'){
                    this.$delete(this.currentItem.data,'video');
                }
            },
            xhrSubmitForCategory(e,element){
                var self= this;
                var file = e.target.files[0];
                var data = new FormData();

                let enableType=['video/mp4'];
                let isIMAGE = enableType.includes(file.type);
                if (!isIMAGE) {
                    this.$message.error('上传文件类型错误：' + enableType.join(" "));
                    return false;
                }
                if(file.type=='video/mp4'){
                    var  isLt512M = file.size / 1024 / 10120 < 1;
                    if (!isLt512M) {
                        this.$message.error('视频上传文件大小不能超过 10M!');
                        return false;
                    }
                }
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
                            self.$set(element.data,'video',self.getOssImage() + response.file)
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
ul.el-upload-list.el-upload-list--picture-card{
    display: none !important;
}
.el-icon-plus{
    line-height: unset;
}

.el-upload--picture-card {
    width: 100px !important;
    height: 100px !important;
    line-height: 98px !important;
}
.el-upload-list--picture-card .el-upload-list__item {
    width: 100px !important;
    height: 100px !important;
}
div.picture-card [class^="el-icon-plus"] {
    line-height: 98px !important;
}
.el-upload-list--picture-card .el-upload-list__item-actions span + span {
    margin-left: 0px;
}

</style>
