export default class  {

    vue="";

    constructor() {

    }

    setVue(vue){
        this.vue=vue;
        return this;
    }

    //type=image video image_video
    onBeforeUpload(file,check="image")
    {
        var enableType=['image/jpeg','image/jpg','image/gif','image/png'];
        if(check=="image"){
            enableType=['image/jpeg','image/jpg','image/gif','image/png'];
        }else if(check=="video"){
             enableType=['video/mp4'];
        }else if(check=="image_video"){
            enableType=['image/jpeg','image/jpg','image/gif','image/png','video/mp4'];
        }

        const isIMAGE = enableType.includes(file.type);

        console.log("上传文件：",file);
        if (!isIMAGE) {
            this.vue.$message.error('上传文件类型错误：' + enableType.join(" "));
            return false;
        }


        if(file.type=='video/mp4'){
            var  isLt512M = file.size / 1024 / 10120 < 1;
            if (!isLt512M) {
                this.vue.$message.error('视频上传文件大小不能超过 10M!');
                return false;
            }
        }else{
            var isLt512k = file.size / 1024 / 2024 < 1;
            if (!isLt512k) {
                this.vue.$message.error('图片上传文件大小不能超过 2M!');
                return false;
            }
        }

        if(this.vue.$root.$data.name == ""){
            this.vue.$message.error("文章名称必填");
            return false;
        }
        if(this.vue.$root.$data.key ==""){
            this.vue.$message.error("文章KEY不能为空");
            return false;
        }

        var self= this.vue;
        if(this.vue.$root.$data.id=="" && this.vue.$root.$data.key ){
            var params={
                name:this.vue.$root.$data.name,
                key:this.vue.$root.$data.key
            }
            $.ajax({
                async:false,
                url:self.$root.$data.apiDomain+'admin/page/ajaxKey',
                type:'post',
                data:params,
                dataType:'json',
                success:function(result){
                    if(result.status == 1){
                        self.$root.$data.id=result.pageId;
                    }else{
                        errorMessage= result.message;
                    }
                }
            })
        }
        if(!self.$root.$data.id > 0){
            self.$message.error(errorMessage);
            return false;
        }
        return isIMAGE
    }

    check(e){
        var errorMessage;
        var self= this.vue;
        if(this.vue.$root.$data.name == ""){
            this.vue.$message.error("文章名称必填");
            return false;
        }
        if(this.vue.$root.$data.key ==""){
            this.vue.$message.error("文章KEY必填");
            return false;
        }

        if(this.vue.$root.$data.id=="" && this.vue.$root.$data.key ){
            var params={
                name:this.vue.$root.$data.name,
                key:this.vue.$root.$data.key
            }
            $.ajax({
                async:false,
                url:self.$root.$data.apiDomain+'admin/page/ajaxKey',
                type:'post',
                data:params,
                dataType:'json',
                success:function(result){
                    if(result.status == 1){
                        self.$root.$data.id=result.pageId;
                    }else{
                        errorMessage= result.message;
                    }
                }
            })
        }
        if(!self.$root.$data.id > 0){
            self.$message.error(errorMessage);
            return false;
        }

        var files = e.target.files[0];

        return true;
    }

}

