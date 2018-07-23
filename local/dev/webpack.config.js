'use strict';

let genConf = require('./config');
let config = genConf();

module.exports = {
    watch: true,
    devtool: config.devtool,
    context: config.es6Path,
    entry: config.entries,
    output: {
        path: config.jsCompiledPath,
        publicPath: config.jsPublicPath,
        filename: "[name].js"
    },
    plugins: config.plugins,
    resolve: {modules: config.modules, alias: config.aliases},
    module: {loaders: config.loaders}
};