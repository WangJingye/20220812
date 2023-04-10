import './bootstrap';

//import ElementUI from "element-ui";
//import 'element-ui/lib/theme-chalk/index.css';
const ElementUI = require('element-ui');
Vue.use(ElementUI);

import rawDisplayer from "./components/infra/raw-displayer.vue";
Vue.component("rawDisplayer", rawDisplayer);


var app = new Vue({
    el: '#app',
    data: {
        apiDomain:window.location.origin,
        queryString:window.location.search
    }

});





