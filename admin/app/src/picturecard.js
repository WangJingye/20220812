const ElementUI = require('element-ui');
Vue.use(ElementUI);
import pictureCard from "./components/picture-card";
Vue.component('picture-card',pictureCard);

Vue.config.productionTip = false;

var app = new Vue({
    el: '#app',
    data: {
        apiDomain:window.location.origin+'/',
        ossDomain:process.env.APP_ENV.process.env.OSS_DOMAIN,
        dialogVisible: false,
    },
    mounted: function () {
        //console.log(process.env);
    },
    methods:{

    }



});





