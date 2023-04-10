//import upperFirst from 'lodash/upperFirst'
//import camelCase from 'lodash/camelCase'

//载入vue插件的路径及扩展名
const requireComponent = require.context(
    './components',
    false,
    /[A-Za-z]\w+\.(vue|js)$/
    // /[A-Za-z]\w+\.(js)$/
)

//载入vue插件
requireComponent.keys().forEach(fileName => {
    const componentConfig = requireComponent(fileName)

    //console.log(fileName.replace(/^\.\/(.*)\.\w+$/, '$1'));

    const componentName = _.upperFirst(
        _.camelCase(
            fileName.replace(/^\.\/(.*)\.\w+$/, '$1')
        )
    )
    Vue.component(
        componentName,
        componentConfig.default || componentConfig
    )
})


//import 'nprogress/nprogress.css';
//import Config from '../../config/env.config'
// import Nprogress from 'nprogress';
// import Axios from 'axios'
// import Lockr from 'lockr'
// import Cookies from 'js-cookie'
// import jQuery from 'jquery'
// import Vue from 'vue'


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


