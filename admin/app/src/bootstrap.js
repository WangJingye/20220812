//import upperFirst from 'lodash/upperFirst'
//import camelCase from 'lodash/camelCase'
import UploadImage from './components/edit/upload';



//载入vue插件的路径及扩展名
const requireComponent = require.context(
    './components',
    true,
    /[A-Za-z]\w+\.(vue|js)$/
    // /[A-Za-z]\w+\.(js)$/
)

//载入vue插件
requireComponent.keys().forEach(fileName => {

    var re=/^\.\/(bak|demo)/;
    var re1=/\.vue$/;
    if(re.test(fileName) || !re1.test(fileName)){
       // console.log(fileName);
    }else{
        const componentConfig = requireComponent(fileName)
        var file = /\/(.*)$/.exec(fileName);

        var i = fileName.lastIndexOf("/");
        var name = fileName.slice(i+1);

        //console.log(fileName.replace(/^\.\/(.*)\.\w+$/, '$1'));

        const componentName = _.upperFirst(
            _.camelCase(
                name.replace(/^(.*)\.\w+$/, '$1')
            )
        )
       //console.log(fileName,componentName);
        Vue.component(
            componentName,
            componentConfig.default || componentConfig
        )
    }
})


// import 'nprogress/nprogress.css';
// import Nprogress from 'nprogress';
//import Config from '../../config/env.config'
// import Axios from 'axios'
// import Lockr from 'lockr'
// import Cookies from 'js-cookie'
// import jQuery from 'jquery'
// import Vue from 'vue'

window.UploadImage = new UploadImage();
// window.Config = Config;
//window._ = require('lodash');
// window.$ = window.jQuery = jQuery;
// require('bootstrap');
// window.Vue = Vue;
// window.Axios = Axios;
// window.Lockr = Lockr;
// window.Cookies = Cookies;
// window.Nprogress = Nprogress;
// Axios.defaults.baseURL = Config.host;
// Axios.defaults.timeout = 1000 * 15
// Axios.defaults.withCredentials = true
// Axios.defaults.headers.session_id = Lockr.get('session_id') ? Lockr.get('session_id') : ''
// Axios.defaults.headers['Content-Type'] = 'application/json';
//
// jQuery(document).ajaxSend(function () {
//     Nprogress.start();
// });
// jQuery(document).ajaxComplete(function () {
//     Nprogress.done();
// });


