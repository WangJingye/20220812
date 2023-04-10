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

                <el-form-item label="Top One">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr><th>背景</th><th>素材</th><th>商品</th><th rowspan="2"></th></tr>
                            <tr>
                                <td>
                                    <img :src="tab.top.bg " style="width:100px;"  v-if="tab.top.bg"/>
                                    <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input" @change="xhrSubmitForCategory($event,tab.top,'bg')">
                                </td>
                                <td>
                                    <video :src="tab.top.middle.src" controls="controls" style="width: 100px;" v-if="tab.top.middle.src && tab.top.middle.type=='video'">
                                        your browser does not support the video tag
                                    </video>
                                    <img :src="tab.top.middle.src" style="width:100px;"  v-if="tab.top.middle.src && tab.top.middle.type=='image'" />
                                    <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input" @change="xhrSubmitForCategory($event,tab.top.middle,'src','video')">
                                    <table style="margin-top: 10px">
                                        <tr><td>位置：</td><td>
                                            <el-radio-group v-model="tab.top.middle.position"  size="mini">
                                                <el-radio-button value="left_top" label="left_top">左上</el-radio-button>
                                                <el-radio-button value="right_top" label="right_top">右上</el-radio-button>
                                                <el-radio-button vluae="center" label="center">居中</el-radio-button>
                                                <el-radio-button value="left_bottom" label="left_bottom">左下</el-radio-button>
                                                <el-radio-button value="right_bottom" label="right_bottom">右下</el-radio-button>
                                            </el-radio-group>
                                        </td></tr>
                                        <tr><td>宽：</td><td>
                                            <el-input-number v-model="tab.top.middle.width" :step="1" :max=750 step-strictly />
                                        </td></tr>
                                        <tr><td>高：</td><td>
                                            <el-input-number v-model="tab.top.middle.height" :step="1"  step-strictly />
                                        </td></tr>

                                    </table>
                                </td>
                                <td>
                                    <img :src="tab.top.product " style="width:100px;"  v-if="tab.top.product"/>
                                    <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input" @change="xhrSubmitForCategory($event,tab.top,'product')">
                                </td>
                            </tr>
                        </table>

                    </div>
                </el-form-item>

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
        name: "editRankingListModel",
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
                return UploadImage.setVue(this).onBeforeUpload(file);
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
            xhrSubmitForCategory(e,item,type,check){

                if(!UploadImage.setVue(this).onBeforeUpload(e.target.files[0],check)){
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
                            item['type']=response.type[0];
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
