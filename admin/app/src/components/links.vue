<template>
    <div class="links" style="margin-top: 20px;background: #f5f7fa">
        <div class="">
            <el-form-item label="链接类型">
                <select v-model="currentElement.action.type" placeholder="请选择链接类型" @change="changeType()" class="el-input__inner inner_link_type">
                    <option label="无链接" value="none"></option>
                    <option label="小程序" value="miniapp_link" v-if="true"></option>
                    <option label="外部小程序" value="miniapp_other_link" v-if="true"></option>
                    <option label="外链" value="outer_link" v-if="true"></option>
                    <option label="内链" value="inner_link"></option>
                    <option label="视频" value="popVideo" v-if="false"></option>
                </select>
            </el-form-item>

            <el-form-item label="小程序地址" v-if="currentElement.action.type=='miniapp_link'" >
                <el-input   v-model="currentElement.action.data.path"  ></el-input>
            </el-form-item>

            <el-form-item label="小程序APPID" v-if="currentElement.action.type=='miniapp_other_link'" >
              <el-input   v-model="currentElement.action.data.appid" ></el-input>
            </el-form-item>
            <el-form-item label="小程序地址" v-if="currentElement.action.type=='miniapp_other_link'" >
              <el-input   v-model="currentElement.action.data.path"  ></el-input>
            </el-form-item>

            <el-form-item label="外链地址" v-if="currentElement.action.type=='outer_link'" >
                <el-input type="url"  v-model="currentElement.action.data.path" placeholder="https://" ></el-input>
            </el-form-item>

            <template v-if="currentElement.action.type=='inner_link'">
                <el-form-item label="小程序页面">
                    <div class="el-select">
                        <select  ref="inner_link_type" v-model="currentElement.action.route.pageKey" @change="changeRoute()" required class="el-input__inner inner_link_type" style="width:191px;">
                            <option value="">请选择页面</option>
                            <option :value="page.pageKey" v-for="page in pages" :key="page.pageKey">{{page.name}}</option>
                        </select>
                    </div>
                </el-form-item>
            </template>
        </div>

        <div  v-if="currentElement.action.type=='inner_link'">
            <template v-if="currentElement.action.route.pageKey=='product'" >
                <el-form-item label="商品ID" >
                    <el-input  v-model="currentElement.action.route.param" placeholder="商品ID" />
                    <select-product @setSku="setSku" style="width:100%"/>
                </el-form-item>
            </template>
            <template v-if="currentElement.action.route.pageKey=='category'">
                <el-form-item label="分类ID" >
                    <el-input  v-model="currentElement.action.route.param" placeholder="分类ID" />
                    <select-category   @setCategoryId="setCategoryId"/>
                </el-form-item>
            </template>
            <template v-if="currentElement.action.route.pageKey=='cms'">
                <el-form-item label="活动落地页" >
                    <select v-model="currentElement.action.data.path" @change="setCmsId" required placeholder="请选择活动页面" class="el-input__inner inner_link_type">
                        <option value="">请选择页面</option>
                        <option  v-for="{id,key,name} in cms" :value="currentElement.action.route.route + '?code=' + key" :label="name" :key="id"></option>
                    </select>
                    <el-input  v-model="currentElement.action.route.param" placeholder="静态页面key"  style="margin-bottom: 2px;"/>
                </el-form-item>
            </template>

            <template v-if="currentElement.action.route.pageKey=='trial_by_post'">
                <el-form-item label="付邮试用" >
                    <select v-model="currentElement.action.data.path" @change="setrialByPost" required placeholder="请选择付邮试用页面" class="el-input__inner inner_link_type">
                        <option value="">请选择页面</option>
                        <option  v-for="{id,name} in trialByPost" :value="currentElement.action.route.route + '?code=' + id" :label="name" :key="id"></option>
                    </select>
                    <el-input  v-model="currentElement.action.route.param" placeholder="付邮活动id"  style="margin-bottom: 2px;"/>
                </el-form-item>
            </template>
        </div>


        <template v-if="currentElement.action.type=='popVideo'">
            <div class="form-group">

                <el-form-item label="视频截图" >
                    <el-upload
                            ref="upload-poster"
                            class="upload-image"
                            :before-upload="onBeforeUpload"
                            :action="ajaxUpload"
                            :file-list="fileList"
                            :on-success="handleSuccess"
                            :on-error="handleError"
                            list-type="picture"
                            :show-file-list="false"
                    >
                        <el-button size="small" type="primary">上传</el-button>
                        <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过500kb</div>
                    </el-upload>

                    <div class="list-group-item "  style="cursor: default;" v-if="currentElement.action.data.poster">
                        <img  :src="currentElement.action.data.poster  " style="width:100px;" />
                    </div>

                </el-form-item>

            </div>

            <div class="form-group">
                <el-form-item label="视频地址" >
                    <el-input type="url"   v-model="currentElement.action.data.src" placeholder="视频地址" ></el-input>
                </el-form-item>
            </div>
            <div class="form-group">
                <template>
                    <el-form-item label="自动运行" >
                        <el-radio v-model="currentElement.action.data.isAuto" label="1">是</el-radio>
                        <el-radio v-model="currentElement.action.data.isAuto" label="0">否</el-radio>
                    </el-form-item>
                </template>
            </div>
        </template>








    </div>
</template>


<script>
    export default {
        props: ['currentElement'],
        name: "links",
        components: {},
        data() {
            return {
                pages:this.$root.$data.config.pages,
                cms:[],
                trialByPost:[],
                fileList: [{
                    name: "",
                    url: this.currentElement.tag=='image'?this.currentElement.action.data.poster:""
                }],
                apiDomain:this.$root.$data.apiDomain,
                ajaxUpload:this.$root.$data.apiDomain+'admin/page/ajaxUpload'
            };
        },

        created:function (){
            //console.log(this.currentElement.action);
            if(this.currentElement.action.route === false){
                this.$set(this.currentElement.action,'route',{});
            }

        },
        mounted: function () {
            this.cms=this.$root.$data.ajax.cms;
            this.trialByPost=this.$root.$data.ajax.trialByPost;
        },
        watch:{
            // 'currentElement.action.route.pageKey':function (val,old){
            //
            //     if(val){
            //         let page=_.find(this.pages,{'pageKey':val});
            //         this.currentElement.action.route.name=page.name;
            //         this.currentElement.action.route.route=page.route;
            //         this.currentElement.action.route.param='';
            //     }else{
            //         _.find(this.pages,{'pageKey':val});
            //         this.currentElement.action.route.name="";
            //         this.currentElement.action.route.route="";
            //         this.currentElement.action.route.param="";
            //     }
            //
            // }
        },
        computed:{
        },
        methods: {
            getOssImage(){
                return this.$root.$data.ossDomain + '';
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
            handleSuccess(response, file, fileList){
                if(response.status==true){
                    this.$set(this.currentElement.action.data,'poster', this.getOssImage() + response.file);
                }else{
                    this.$refs.upload-poster.clearFiles()
                    this.$alert(response.message,'提示');
                }

            },
            handleError(err, file, fileList){
                console.log('err',err);
                this.$alert('上传错误','提示');
            },
            changeType:function (){


                for(var key in this.currentElement.action.data){
                    delete this.currentElement.action.data[key];
                }

                if(this.currentElement.action.type == 'none'){
                    this.currentElement.action.route={
                        "pageKey": "",
                        "name": "",
                        "route": "",
                        "param": ""
                    };
                }

                if(this.currentElement.action.type == 'miniapp_link' ){
                    if(this.currentElement.action.hasOwnProperty('route')){
                        //this.$delete(this.currentElement.action,'route');
                        this.currentElement.action.route={
                            "pageKey": "",
                            "name": "",
                            "route": "",
                            "param": ""
                        };
                    }
                }

                if(this.currentElement.action.type == 'outer_link' ){
                    if(this.currentElement.action.hasOwnProperty('route')){
                        //this.$delete(this.currentElement.action,'route');
                        this.currentElement.action.route={
                            "pageKey": "",
                            "name": "",
                            "route": "",
                            "param": ""
                        };
                    }
                }

                if(this.currentElement.action.type == 'inner_link'){
                    //this.$set(this.currentElement.action,'route','');
                    this.currentElement.action.route={
                        "pageKey": "",
                        "name": "",
                        "route": "",
                        "param": ""
                    };
                }

                if(this.currentElement.action.type == 'popVideo'){
                    this.$set(this.currentElement.action.data,'isAuto','1');
                    //delete this.currentElement.action.route;
                    this.currentElement.action.route={
                        "pageKey": "",
                        "name": "",
                        "route": "",
                        "param": ""
                    };

                }

                if(this.currentElement.action.type == 'newMP'){
                    //delete this.currentElement.action.route;
                    this.currentElement.action.route={
                        "pageKey": "",
                        "name": "",
                        "route": "",
                        "param": ""
                    };
                }

                this.currentElement.action.data={
                    "path":"",
                    "type":""
                };


            },
            changeRoute:function (){
                this.currentElement.action.data={
                    "path":"",
                    "type":""
                };
                this.currentElement.action.route.param="";

            },
            setCategoryId:function (data){
                //this.$set(this.currentElement.action.data,'category_id', data.label);
                this.$set(this.currentElement.action.data,'path',this.currentElement.action.route.route+ "?code=" + data.id);
                this.$set(this.currentElement.action.route,'param',data.id);
            },
            setSku:function (product){
                //this.$set(this.currentElement.action.data,'product_id',sku);
                //this.$set(this.currentElement.action.data,'path',this.currentElement.action.route.route+ "?code=" + product.id);
                //this.$set(this.currentElement.action.route,'param',product.id);
                this.currentElement.action.route.param=product.id
                console.log(product,this.currentElement.action.route,this.currentElement.action.data);
            },
            setCmsId:function (e){
                console.log(this.currentElement.action.data.path);
                var re = /code=(.*)$/i;
                let $match=re.exec(this.currentElement.action.data.path);
                let id ='';
                if($match){
                    id = $match[1];
                }
                this.$set(this.currentElement.action.route,'param',id);
                // this.currentElement.action.route.param=id;
            },
            setrialByPost:function (e){
                console.log(this.currentElement.action.data.path);
                var re = /code=(.*)$/i;
                let $match=re.exec(this.currentElement.action.data.path);
                let id ='';
                if($match){
                    id = $match[1];
                }
                this.$set(this.currentElement.action.route,'param',id);
                // this.currentElement.action.route.param=id;
            }



        }
    };
</script>
<style scoped>


</style>
