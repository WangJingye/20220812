import './bootstrap';
import config from './config';

//import ElementUI from "element-ui";
//import 'element-ui/lib/theme-chalk/index.css';
const ElementUI = require('element-ui');
import VueAwesomeSwiper from 'vue-awesome-swiper'
import 'swiper/dist/css/swiper.css'
Vue.use(VueAwesomeSwiper)
Vue.use(ElementUI);

import VueMaterial from 'vue-material'
import 'vue-material/dist/vue-material.css'
Vue.use(VueMaterial);



import rawDisplayer from "./components/infra/raw-displayer.vue";
Vue.component("rawDisplayer", rawDisplayer);

Vue.config.productionTip = false;

var app = new Vue({
    el: '#app',
    data: {
        apiDomain:window.location.origin+'/',
        ossDomain:'',
        queryString:window.location.search,
        showHeaderFooter:showHeaderFooter,
        config:config,
        dialogVisible: false,
        id:pageId,
        title:pageTitle,
        name:pageName,
        key:pageKey,
        nodes:nodes,
        tagWindow:'22%',
        showWindow:'38%',
        editWindow:'40%',
        mediaType:mediaType,
        uploadUrl:"",
        pageSource:"",
        ajax:{},
        breadcrumbs:breadcrumbs,
    },
    methods:{
        newBreadcrumbs:function (){
            this.breadcrumbs.push({
                'name':"",
                'url':"",
            });
        },
        removeBreadcrumbs(element,idx) {
            this.breadcrumbs.splice(idx, 1);
        },
        changeText:function (event){

            $('input[name="share_title"]').val(event.target.value);
        },
        xhrSubmitPoster:function (e,input_class){
            var files = e.target.files[0];
            if(files.size > (1024 * 512) ){
                $(e.target).val("");
                alert('图片大小为：'+Math.round(files.size/1024)+'K,不能超过512K');
                return false;
            }
            var param = new FormData();
            param.append('file',files);
            $(e.target).before('<i style="font-size: 30px;" class="loading layui-icon layui-icon-loading layui-icon layui-anim layui-anim-rotate layui-anim-loop" />');
            $('span.submitFm').hide();
            $.ajax({
                url: this.apiDomain + 'admin/page/ajaxUpload',
                type:'post',
                data:param,
                dataType:'json',
                processData: false,
                contentType:false,
                success:function(res){
                if(res.status==true){
                    if( $('input.'+input_class).val()){
                        $('img.'+input_class).attr('src',res.file);
                    }
                    if($('img.'+input_class).length==0){
                        $(e.target).before('<img class="'+input_class+'" src="'+res.file+'" style="width:100px;vertical-align: bottom;" />');
                    }
                    $('input.'+input_class).val(res.file);
                    $('i.loading').hide();
                    $('span.submitFm').show();

                }else{
                    alert(res.message);
                }
            }
        })
        },
        syncToH5:function($event){
            return false;
            if(confirm('确定复制?')){
                 this.$set(this.nodes,'h5',JSON.parse(JSON.stringify(this.nodes['wechat'])));
            }
        },
        syncTowechat:function(){
            return false;
            if(confirm('确定复制?')){
                this.$set(this.nodes,'wechat',JSON.parse(JSON.stringify(this.nodes['h5'])));
            }
        },
        switchMedia:function($event,$type){
            if($type=="h5"){
                this.tagWindow="22%";
                this.showWindow="38%";
                $(".phone").show();
                this.mediaType="h5";
                $('ul.cms-content').css('width',"380px");
            }
            if($type=="pc"){
                this.tagWindow="0%";
                this.showWindow="60%";
                $(".phone").hide();
                $('ul.cms-content').css('width',"100%");
                this.mediaType="pc";
            }
        },
        tabClick:function(tab, event){
            console.log(tab.name);
            if(tab.name=='wechat'){
                this.tagWindow="22%";
                this.showWindow="38%";
                $(".phone").show();
                $('ul.cms-content').css('width',"380px");
            }
            this.mediaType=tab.name;
        }
    },
    mounted: function () {
        this.$nextTick(function () {
            this.switchMedia(null,this.mediaType);
        })
        let self=this;

        let url = this.apiDomain + 'admin/page/page/list';
        $.post(url, (response) => {
            this.$set(self.ajax,'cms',response.data)
        }, 'json');

        url = this.apiDomain + 'admin/page/tree';
        $.post(url, (response) => {
            this.$set(self.ajax,'treeData',response.tree)
        }, 'json');

        url = this.apiDomain + 'admin/page/product/list?current_page=1';
        let params={};
        $.post(url, params,(response) => {
            this.$set(self.ajax,'products',response.data.items);
            this.$set(self.ajax,'productsTotal',response.data.count);
        }, 'json');

         url = this.apiDomain + 'admin/trial/backend/trial/dataList';
        $.post(url, (response) => {
            this.$set(self.ajax,'trialByPost',response.data)
        }, 'json');

    }


});

//app.$set(app.$data,'title',pageTitle);
//app.$set(app.$data,'showHeaderFooter',showHeaderFooter);

Vue.filter('getImage', function ($path) {
    return app.ossDomain + '' + $path;
})

Vue.filter('getBgImage', function ($path) {
    return {
        backgroundImage:'url('+$path+')'
    }
})

Vue.filter('getOssBgImage', function ($path) {
    return {
        backgroundImage:'url('+app.ossDomain + '' +$path+')'
    }
})






