'use strict';

//requiries
let webpack = require('webpack');
let path = require('path');

//paths
const bowerPath = path.resolve(__dirname, 'bower_components');
const nodeModulesPath = path.resolve(__dirname, 'node_modules');
const es6Path = path.resolve(__dirname, 'es6');
const jsCompiledPath = path.resolve(__dirname, '../templates/axioma/scripts');
const jsPublicPath = '/local/templates/axioma/scripts/';

let entries = {
    "app": "./app",
    "index": "./index",
    "filter": "./filter",
    "search": "./search",
    "menu": "./menu",
    "user": "./user",
    "catalog": "./catalog",
    "basket": "./basket",
    "order": "./order",
    "form": "./form",
    "card": "./card",
    "actions": "./actions",
    "services": "./services",
    "oauth": "./oauth",
    "faq": "./faq",
    "personal": "./personal"
};

let aliases = {
    //from es6Path
    'easyzoom': "common/easyzoom.js",
    'lib': "common/lib.js",
    'map_data': "common/map_data.js",
    'url-js': "common/url.js",
    'manup-js': "vendor/manup.js",
    //from bowerPath
    'jquery': "jquery/dist/jquery.min.js",
    'slick-js': "slick.js/slick/slick.min.js",
    'slick-css': "slick.js/slick/slick.css",
    'mousewheel': "jquery-mousewheel/jquery.mousewheel.min.js",
    'scrollbox-js': "scrollbox/dist/js/scrollbox.min.js",
    'scrollbox-css': "scrollbox/dist/css/scrollbox.min.css",
    'ionrangeslider-js': "ionrangeslider/js/ion.rangeSlider.min.js",
    'ionrangeslider-css': "ionrangeslider/css/ion.rangeSlider.css",
    'ionrangeslider-skin': "ionrangeslider/css/ion.rangeSlider.skinHTML5.css",
    'jquery-placeholder': "jquery-placeholder/jquery.placeholder.min.js",
    'bootstrap-tab': "bootstrap/js/tab.js",
    'bootstrap-collapse': "bootstrap/js/collapse.js",
    'history-js': "history.js/scripts/bundled/html5/jquery.history.js",
    //'jquery.maskedinput': "jquery.maskedinput/dist/jquery.maskedinput.min.js",
    'jquery-mask-plugin': "jquery-mask-plugin/dist/jquery.mask.min.js",
    'inputmask': "inputmask/dist/jquery.inputmask.bundle.js",
    'fancybox-js': "fancybox/dist/jquery.fancybox.min.js",
    'fancybox-css': "fancybox/dist/jquery.fancybox.min.css",
    'air-datepicker-js': "air-datepicker/dist/js/datepicker.min.js",
    'air-datepicker-css': "air-datepicker/dist/css/datepicker.min.css",
    'autocomplete': "devbridge-autocomplete/dist/jquery.autocomplete.min.js"
};


//plugins
const CommonsChunkPlugin = new webpack.optimize.CommonsChunkPlugin({
    name: 'common',
    minChunks: 2,
    filename: 'common.js'
});

const DedupePlugin = new webpack.optimize.DedupePlugin();
const NoErrorsPlugin = new webpack.NoErrorsPlugin();

const ProvidePlugin = new webpack.ProvidePlugin({
    "$": "jquery",
    "jQuery": "jquery",
    "window.jQuery": "jquery"
});

const ContextReplacementPlugin = new webpack.ContextReplacementPlugin(/moment[\/\\]locale$/, /ru|en/);

const LoaderOptionsPlugin = new webpack.LoaderOptionsPlugin({
    minimize: true,
    debug: false
});

const UglifyJsPlugin = new webpack.optimize.UglifyJsPlugin({
    compress: {
        warnings: false,
        drop_console: true,
        unsafe: true
    },
    output: {
        comments: false
    },
    sourceMap: false
});

//loaders
const babelLoader = {
    test: /\.js$/,
    exclude: /(node_modules|bower_components)/,
    loader: 'babel-loader',
    query: {presets: ['es2015']}
};

const fileLoader = {
    test: /\.(png|jpg|gif|svg|ttf|eot|woff|woff2)$/,
    loader: 'file-loader?name=[path][name].[ext]'
};

const cssLoader = {
    test: /\.css$/,
    loader: 'style-loader!css-loader'
};

module.exports = (dev) => {
    let config = {
        entries: entries,
        aliases: aliases,
        bowerPath: bowerPath,
        nodeModulesPath: nodeModulesPath,
        es6Path: es6Path,
        jsCompiledPath: jsCompiledPath,
        jsPublicPath: jsPublicPath
    };


    let plugins = [
        CommonsChunkPlugin,
        //ContextReplacementPlugin,
        //DedupePlugin,
        ProvidePlugin
    ];

    let modules = [
        bowerPath,
        nodeModulesPath,
        es6Path
    ];

    let loaders = [
        babelLoader,
        cssLoader,
        fileLoader
    ];


    if (dev !== true) {
        plugins.push(UglifyJsPlugin, LoaderOptionsPlugin);
        //config.devtool = false;
    } else {
        //config.devtool = "cheap-source-map";
    }

    config.devtool = false;
    config.plugins = plugins;
    config.modules = modules;
    config.loaders = loaders;

    return config;
};