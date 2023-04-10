<template>
    <div>
        <template v-if="currentElement.tag=='blank_space'">
            <blank-space :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='txt'">
            <textEdit :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='anchor_point'">
            <anchorPoint :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='txt_simple'">
            <textEdit :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='product_detail_multi_part_text'">
            <productDetailMultiPartText :currentElement="currentElement"/>
        </template>
        <template v-if="currentElement.tag=='product_detail_title_desc'">
            <productDetailTitleDesc :currentElement="currentElement"/>
        </template>
        <template v-if="currentElement.tag=='product_detail_text'">
            <productDetailText :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='icon_title'">
            <iconTitle :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='icon_title_yellow'">
            <iconTitle1 :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='multi_line_text'">
            <multi-line-text :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='image'">
            <image-edit :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='image_map'">
            <image-map-edit :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='banner_image'">
            <image-edit :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='image_2text'">
            <image-edit2 :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='image_flex_simple'">
            <imageFlexSimple :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='image_flex_image'">
            <image-flex-image-edit :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='multi_image_product'">
            <multi-image-product :currentElement="currentElement"/>
        </template>
        <template v-if="currentElement.tag=='multi_image_product_recommend'">
            <multiImageProductRecommend :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='multi_image_product_high'">
            <multi-image-product :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='multi_image'">
            <multi-image :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='product_recommend'">
            <multi-image :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='multi_image_icon'">
            <multi-image-icon :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='image_flex_unlimited'">
            <image-flex :currentElement="currentElement"/>
        </template>
        <template v-if="currentElement.tag=='image_flex'">
            <image-flex :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='image_flex_right'">
            <image-flex-right :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='image_flex_hair'">
            <imageFlexHair :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='swiper'">
            <swiper :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='swiper_fraction'">
            <swiper-fraction :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='swiper_fraction_v2'">
            <swiper-fraction-v2 :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='x_swiper'">
            <xswiper :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='ranging_list'">
            <rangingList :currentElement="currentElement"/>
        </template>



        <template v-if="currentElement.tag=='multi_image_product_swiper'">
            <multi-image-product-swiper :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='product_swiper'">
            <product-swiper :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='navication_menu'">
            <navication-menu :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='image_on_text_swiper'">
            <image-on-text-swiper :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='category_list'">
            <categroy-list :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='in_text_animate'">
            <in-text-animate :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='in_text_animate_middle'">
            <in-text-animate :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='in_video_animate'">
            <in-video-animate :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='video'">
            <video-edit :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='video_home'">
            <videohome-edit :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='offline_appointment_pc'">
            <offlineAppointmentPc :currentElement="currentElement"/>
        </template>

        <template v-if="currentElement.tag=='offline_appointment_wechat'">
            <offlineAppointmentWechat :currentElement="currentElement"/>
        </template>






    </div>
</template>

<script>
    import draggable from "@/vuedraggable";
    import ImageEdit from "./edit/imageEdit";
    import Swiper from "./edit/swiper.vue";
    export default {
        props:['currentElement'],
        name: "element-edit",
        components: {
            Swiper,
            ImageEdit,
            draggable
        },
        data() {
            return {
                pages:'',
                fileList: [{
                    name: "",
                    url: this.currentElement.tag=='image'?this.currentElement.src:""
                }],
                authorOptions: [],
                apiDomain:this.$root.$data.apiDomain,
                ajaxUpload:this.$root.$data.apiDomain+'admin/page/ajaxUpload'
            };
        },
        mounted: function () {
            this.pages=this.$root.$data.config.pages;
        },
        watch:{
            currentElement:function (currentElement){
               this.fileList=[{
                   name: "",
                   url: this.currentElement.tag=='image'?currentElement.src:""
               }];
            },
            'currentElement.src':function (val, oldVal){
                this.fileList=[{
                    name: '',
                    url: this.currentElement.tag=='image'?val:""
                }];
            },
            'currentElement.author':function (val, oldVal){
                let item=_.find(this.authorOptions, function(o) { return o.value == val; });
                if(this.currentElement.tag=='author'){
                    this.currentElement.icon=item.icon;
                }

            },
            'currentElement.textIndentCount':function (val, oldVal){
                if(this.currentElement.tag=='text' || this.currentElement.tag=='textarea'){
                    this.currentElement.style.textIndent=val * this.currentElement.style.fontSize;
                }
            },
        },
        methods: {
            removeImg(item){
                item.src="";
            },
            addProduct(){
                this.currentElement.nodes.push(
                    {
                        src:'',
                        collection_name:'',
                        name:'',
                        id:'',
                    }
                );
            },
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
            applyCouponActions(value){
                let rows = this.currentElement.rows;
                let columns = this.currentElement.columns;
                let actions = this.currentElement.actions;
               // console.log(value);


                if(rows > actions.length){
                    let newRow=[];
                    for(let i = 0 ; i<columns;i++){
                        newRow.push({
                            'coupon_id':"",
                            'coupon_name':""
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
                            'coupon_id':"",
                            'coupon_name':""
                        });
                    }
                }
                if(columns < actions[0].length){
                    for(let i = 0; i <  actions.length; i++){
                        actions[i].pop();
                    }
                }

            },
            getOssImage(){

                return this.$root.$data.ossDomain?this.$root.$data.ossDomain + '':"";
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
                    this.currentElement.src=this.getOssImage() + response.file;

                    var height =  750 / (response.info[0] / response.info[1]);
                    height = Math.round(height);
                    this.$set(this.currentElement,'height',height);


                }else{
                    this.$refs.upload.clearFiles()
                    this.$alert(response.message,'提示');
                }

            },
            videoHandleSuccess(response, file, fileList){
                if(response.status==true){
                    this.$set(this.currentElement,'screenshot',this.getOssImage() + response.file);
                }else{
                    this.$refs.upload.clearFiles()
                    this.$alert(response.message,'提示');
                }

            },

            swiperSuccess(response, file, fileList){
                //console.log(response.info[0],response.info[1],response.info.mime);
                var height =  750 / (response.info[0] / response.info[1]);
                    height = Math.round(height);

                if(this.currentElement.tag=='swiper_horizontal') {
                    height=Math.round(height / 3);
                }
                if(height > this.currentElement.height){
                    this.currentElement.height=height;
                }



                if(this.currentElement.nodes===false){
                    this.currentElement.nodes=[];
                }

                if(    this.currentElement.tag=='swiper'
                    || this.currentElement.tag=='swiper_title'
                    || this.currentElement.tag=='swiper_horizontal'
                    || this.currentElement.tag=='multi_image'
                    || this.currentElement.tag=='product_recommend'
                    || this.currentElement.tag=='column_2_title'
                    || this.currentElement.tag=='column_2_btn'
                    || this.currentElement.tag=='image_flex'
                    || this.currentElement.tag=='image_flex_right'
                    || this.currentElement.tag=='multi_image_product'
                ){
                    this.currentElement.nodes.push( {
                        tag:'image',
                        src:this.getOssImage() + response.file,
                        // url:'',
                        'action':{
                            data:{},
                            type:'none',
                            route:false
                        },
                        height:height
                    });
                }
            },
            xhrSubmitPoster(e,item){
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

                        var height =  750 / (response.info[0] / response.info[1]);
                        height = Math.round(height);
                        if(height > self.currentElement.height){
                            self.currentElement.height=height;
                        }
                        item.image={
                            tag:'image',
                            src:self.getOssImage() + response.file,
                            url:'',
                            'action':{
                                data:{},
                                type:'none',
                                route:false
                            },
                            height:height
                        };
                    }else{
                        self.$alert(response.message);
                    }
                }
            })
            },
            handleError(err, file, fileList){
                console.log('err',err);
                this.$alert('上传错误','提示');
            },
            removeAt(element,idx) {
                element.nodes.splice(idx, 1);
                if(element.nodes.length ==0){
                    element.height = 0;
                }else{
                    element.height=(_.maxBy(element.nodes, 'height')).height;
                }
            },
            removeLabel(list,idx){
                list.splice(idx, 1);
            },
            sort(evt){
                let _this=this;
                let files = [...this.currentElement.nodes];
                this.currentElement.nodes=[];
                files.forEach(function (file){
                    _this.currentElement.nodes.push(file);
                });
            },
            sortSub(evt,item){
                console.log(111);
            },
            addLabel(){

                let label = this.$refs.labelItem.value;
                if(this.currentElement.label===false){
                    this.currentElement.label = [];
                }
                this.currentElement.label.push(label);
                this.$refs.labelItem.value="";
            },
            setSku:function (product,item){
               // console.log(product,item);
                item.src=product.image;
                item.collection_name=product.collection_name;
                item.name=product.name;
                item.id=product.id;
            },
            setCoupon:function (obj,coupon){
                console.log();
                coupon.coupon_id=obj.id;
                coupon.coupon_name=obj.name;
                coupon.require=obj.total_amount;
                coupon.price=obj.total_discount;
            },
            addTab:function (){
                if(this.currentElement.nodes == false){
                    this.currentElement.nodes =[];
                }
                if(this.currentElement.newTabName){
                    const tab=this._newTab(this.currentElement.newTabName);
                    this.currentElement.nodes.push(tab);
                    this.currentElement.newTabName="";
                }
            },
            _newTab:function (name){
                const productArray=[];
                if(this.currentElement.productCount){
                    for (var i =0;i<this.currentElement.productCount;i++){
                        productArray.push({
                            src:'',
                            name:'',
                            id:''
                        });
                    }
                }
                return {
                    tabName:name,
                    image:"",
                    products:productArray,
                }
            },
            addCategory:function (){
                if(this.currentElement.nodes == false){
                    this.currentElement.nodes =[];
                }
                if(this.currentElement.newCategoryName){
                    const category=this._newCategory(this.currentElement.newCategoryName);
                    this.currentElement.nodes.push(category);
                    this.currentElement.newCategoryName="";
                }
            },
            _newCategory:function (name){
                return {
                    name:name,
                    style:3,
                    list:[{
                        name:name,
                        src:'',
                        action:{
                            data:{},
                            type:'none',
                            route:false
                        },
                    }],
                }
            },
            xhrSubmitForCategory(e,item){
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

                            item.list[0].src=self.getOssImage() + response.file;


                        }else{
                            self.$alert(response.message);
                        }
                    }
                })
            },
            addSubdCategory:function (item){
                if(this.currentElement.newSubCategoryName){
                    const category=this._newSubCategory(this.currentElement.newSubCategoryName);
                    item.list.push(category);
                    this.currentElement.newSubCategoryName="";
                }
            },
            _newSubCategory:function (name){
                return {
                        name:name,
                        src:'',
                        action:{
                            data:{},
                            type:'none',
                            route:false
                        },
                    };
            },
            xhrSubmitForSubCategory(e,item){
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
                            item.src=self.getOssImage() + response.file;
                        }else{
                            self.$alert(response.message);
                        }
                    }
                })
            },
            zoomOut:function (i){
                $('.cate-'+i).height(50);
                $('.cate-'+i).find('.el-icon-zoom-in').show();
                $('.cate-'+i).find('.el-icon-zoom-out').hide();
            },
            zoomIn:function (i){
                $('.cate-'+i).height('auto');
                $('.cate-'+i).find('.el-icon-zoom-in').hide();
                $('.cate-'+i).find('.el-icon-zoom-out').show();

            },
            setCategoryName:function (event,item){
                item.name=event;
            }


        }
    };
</script>
<style scoped>
    .list-group-item{
        cursor: move;
    }
    .list-group-item>i.el-icon-delete{
        cursor: default;
    }
</style>




