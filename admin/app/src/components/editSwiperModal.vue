<template>
    <div style="">
        <span  style="width:100%;" class="layui-btn layui-btn-normal" @click="swiperDialogVisible = true">查看详情</span>
        <el-dialog class="select-product-dialog" title=" " :visible.sync="swiperDialogVisible" :close-on-click-modal="false" width="80%" top="20px" append-to-body>
            <div slot="title" class="header-title">

                <el-form-item label="TAB名称" >
                    <div class="">
                        <el-input v-model="tab.name" :min="1" style="width:50%;"></el-input>
                        <el-button @click="swiperDialogVisible = false">确定</el-button>
                    </div>
                </el-form-item>

                <el-form-item label="上传" >
                    <div class="">
                        <el-upload
                                v-if=""
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
                    </div>
                </el-form-item>

                    <draggable
                            v-if="tab.list.length > 0"
                            class="dragArea list-group "
                            :list="tab.list"
                            @change="sort"
                            handle=".handle"
                    >
                        <div class="list-group-item " :class="'cate-' + i " v-for="(item,i) in tab.list" :key="i" style="position: relative;cursor: default;">
                            <i class="el-icon-document handle" style="position: absolute;left: 0px;top:0px;cursor: move"></i>
                            <i class="el-icon-delete" style="position: absolute;right: 0px;top:0px;" @click="removeAt(tab,i)"></i>
                            <i class="el-icon-zoom-out" @click="zoomOut(i)" style="position: absolute;left: 20px;top:0px;"></i>
                            <i class="el-icon-zoom-in" @click="zoomIn(i)" style="position: absolute;left: 20px;top:0px;" ></i>
                            <div class="links">

                            </div>
                            <div>
                                <el-form-item label="title" >
                                    <el-input v-model="item.title"  ></el-input>
                                </el-form-item>
                                <el-form-item label="src" >
                                    <img :src="item.src " style="width:100px;" />
                                    <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input" @change="xhrSubmitForCategory($event,item,'src')">
                                </el-form-item>
                                <el-form-item label="logo" >
                                    <img v-if="item.logo" :src="item.logo " style="width:100px;" />
                                    <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input" @change="xhrSubmitForCategory($event,item,'logo')">
                                </el-form-item>
                                <div class="links">
                                    <links :currentElement="item" v-if="true" />
                                </div>

                            </div>
                        </div>
                    </draggable>
                    <div>
                        <el-button style="width:100%;" type="primary" @click="swiperDialogVisible = false">确定</el-button>
                    </div>

            </div>
            <div >

            </div>
        </el-dialog>
    </div>
</template>


<script>
    export default {
        props: ['tab'],
        name: "editSwiperModal",
        components: {},
        data() {
            return {
                swiperDialogVisible: false,
                fileList: [{
                    name: "",
                }],
                apiDomain:this.$root.$data.apiDomain,
                ajaxUpload:this.$root.$data.apiDomain+'admin/page/ajaxUpload'
            };
        },

        created:function (){
            var _this = this;
            document.onkeydown = function(e) {
                let key = window.event.keyCode;
                if (key == 13) {
                }
            };
        },
        mounted: function () {
            if(this.$root.$data.pageSource!=""){
                this.ajaxUpload=this.$root.$data.uploadUrl;
            }
        },
        watch:{
            swiperDialogVisible(val){
                if(val === true){
                    $('.edit-wrap').css('position','relative');
                }else{
                    $('.edit-wrap').css('position','fixed');
                }
            }
        },
        computed:{
        },
        methods: {
            onBeforeUpload(file)
            {

                if(this.$root.$data.pageSource==""){
                    return UploadImage.setVue(this).onBeforeUpload(file);
                }else{
                    return true;
                }
            },
            handleError(err, file, fileList){
                console.log('err',err);
                this.$alert('上传错误','提示');
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
                                self.tab.list.push( {
                                    "title": "",
                                    "src": self.getOssImage() + response.file,
                                    "logo":false,
                                    'action':{
                                        data:{},
                                        type:'none',
                                        route:false
                                    },
                                });
                            }else{
                                self.$alert(response.message);
                            }
                        }
                    })
                }
            },
            xhrSubmitForCategory(e,item,type){

                if(this.$root.$data.pageSource==""){
                    if(!UploadImage.setVue(this).check(e)) return false;
                }

                var files = e.target.files[0];
                if(files.size > (1024 * 512) ){
                    this.$alert('图片大小为：'+Math.round(files.size/1024)+'K,不能超过512K');
                    return false;
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
                            item[type]=self.getOssImage() + response.file;
                        }else{
                            self.$alert(response.message);
                        }
                    }
                })
            },
            sort(evt){
                let _this=this;
                let files = [...tab.list];
                this.tab.list=[];
                files.forEach(function (file){
                    _this.tab.list.push(file);
                });
            },
            removeAt(element,idx) {
                this.tab.list.splice(idx, 1);
            },
            zoomOut:function (i){
                $('.cate-'+i).css('overflow','hidden').height(50);
                $('.cate-'+i).find('.el-icon-zoom-in').show();
                $('.cate-'+i).find('.el-icon-zoom-out').hide();
            },
            zoomIn:function (i){
                $('.cate-'+i).css('overflow','auto').height('auto');
                $('.cate-'+i).find('.el-icon-zoom-in').hide();
                $('.cate-'+i).find('.el-icon-zoom-out').show();

            },
        }
    };
</script>
<style >
    .select-product-dialog>.el-dialog>.el-dialog__body{
        padding-top:0px !important;
    }

</style>
