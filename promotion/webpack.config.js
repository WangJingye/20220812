/**
 * npm install module_name [-S|-D|-g]:
 * -S: --save       dependencies     生产依赖 
 * -D: --save-dev   devDependencies  开发依赖
 * -g                                全局安装
 * npm install module_name 本地安装(将安装包放在 ./node_modules 下)
 * 
 * cnpm install -g  webpack-cli webpack
 * 
 * install:
 * npm cache clean --force
 * npm install --registry=https://registry.npm.taobao.org
 *
 * run:
 * webpack --progress --colors --watch
 */

var webpack = require('webpack');
var path = require('path');
var glob= require('glob');

const CleanWebpackPlugin = require("clean-webpack-plugin");
const VueLoaderPlugin = require('vue-loader/lib/plugin');



module.exports = {
    mode: "production",//production,development
   // devtool: 'eval-source-map',  //调试信息 //source-map,eval-source-map ,生产环境不要用
    entry:  {
        main: path.resolve(__dirname,'app/src','main.js'), //vue初始化文件
       //: path.resolve(__dirname,'src','module','aa.js'), //测试文件1
       // loadsh: path.resolve(__dirname,'src','module','loadsh.js'), //测试文件2
    },
    output: {
        path: path.join(__dirname,'public/js'),
        // filename: "bundle-[hash].js"
        filename: "[name].js",
        libraryTarget:'umd' //commonjs2和umd，前者是为node环境，后者是为浏览器环境。
    },
    resolve: {
        extensions: ['.js', '.vue', '.json'],
        alias: {
            // 'vue$': 'vue/dist/vue.esm.js',
            '@': path.join(__dirname, 'node_modules')
        }
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                exclude: /node_modules/
            },
            {
                test: /\.js$/,
                exclude : /node_modules/,
                loader: 'babel-loader',
            },
            {
                test:/\.css$/,
                loader:['style-loader','css-loader']
            },
            {
                test: /\.(eot|svg|ttf|woff|woff2)\w*/,
                loader: 'url-loader?limit=1000000'
            },
        ]
    },
    plugins:[
        new VueLoaderPlugin(),                               //添加VueLoad解析插件
        new webpack.BannerPlugin('Jack.Xu1@connext.com.cn'), //添加注释

        new webpack.optimize.OccurrenceOrderPlugin(), //调整模块的打包顺序，用到次数更多的会
        new CleanWebpackPlugin('dist/*.*', {
            root: __dirname,
            verbose: true,
            dry: false
        }),
        //定义环境变量
        new webpack.DefinePlugin({
            HOST:JSON.stringify("http://es6.org/"),

        }),
        new webpack.ProvidePlugin({
            //Vue: ['vue/dist/vue.min.js'],
            //$: 'jquery',
            //jQuery: 'jquery',
            //'window.jQuery': 'jquery',
            //Cookies:'js-cookie',
            //Lockr:'lockr',
            //Axios:'axios',
            //Nprogress:'nprogress',
        })


    ],
    optimization: {
        usedExports: true,
        splitChunks: {
            cacheGroups: {
                // commons: {
                //     chunks: 'all',
                //     minChunks: 2,
                //     maxInitialRequests: 5,
                //     minSize: 0
                // },
                vendor: {
                    test: /node_modules/,
                    chunks: "all", //必须三选一： "initial" | "all"(推荐) | "async" (默认就是async)
                    name: "vendor",
                    priority: 10,
                    enforce: true
                }
            }
        },
        // splitChunks: {
        //     chunks: "all", //async
        //     minSize: 20000,
        //     minChunks: 1,
        //     maxAsyncRequests: 5,
        //     maxInitialRequests: 3,
        //    // automaticNameDelimiter: '~',
        //     name: 'common',
        //     cacheGroups: {
        //         vendors: {
        //             test: /[\\/]node_modules[\\/]/,
        //             priority: -10,
        //         },
        //         default: {
        //             minChunks: 2,
        //             priority: -20,
        //             reuseExistingChunk: true
        //
        //         }
        //     }
        // }
    },
    externals: {
        jquery:'jQuery',
        vue:'Vue',
        lodash: {
            commonjs: 'lodash',
            commonjs2: 'lodash',
            amd: 'lodash',
            root: '_'
        },
        'element-ui': 'ELEMENT',
        'swiper': 'Swiper',
    },
    //配置如何展示性能提示
    performance: {
        hints: false
    }
}