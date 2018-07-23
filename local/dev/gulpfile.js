'use strict';

const path = require('path');

let gulp = require('gulp');
let webpack = require('webpack');
let compass = require('gulp-compass');
let gutil = require('gulp-util');
let del = require('del');

let webpackConf = require('./webpack.config');
let webpackDevConf = require('./webpack.dev.config');

let sass_src = path.resolve(__dirname, 'sass');
let dist = path.resolve(__dirname, '../templates/axioma');

//run sass
gulp.task('compass', function () {
    gulp.src('.' + sass_src + '/*.scss')
            .pipe(compass({
                config_file: './config.rb',
                css: dist,
                sass: sass_src,
                task: 'watch'
            }))
            .pipe(gulp.dest('app/assets/temp'));
});


// run webpack
gulp.task("webpack", function (callback) {
    webpack(webpackConf, function (err, stats) {
        if (err)
            throw new gutil.PluginError("webpack", err);
        gutil.log("[webpack]", stats.toString({
            // output options
        }));
        //callback();
    });
});

gulp.task('clean-css', function () {
    del.sync(dist + '/styles/*.css', {force: true});
    return del.sync(dist + '/*.css', {force: true});
});

gulp.task('clean-js', function () {
    return del.sync(dist + '/scripts/*.js', {force: true});
});

// run webpack-dev
gulp.task("webpack-dev", function (callback) {
    webpack(webpackDevConf, function (err, stats) {
        if (err)
            throw new gutil.PluginError("webpack", err);
        gutil.log("[webpack]", stats.toString({
            // output options
        }));
        //callback();
    });
});

// Rerun the task when a file changes
gulp.task('watch', function () {
    gulp.watch(['compass']);
    gulp.watch(['webpack']);
});

// Rerun the task when a file changes
gulp.task('watch-dev', function () {
    gulp.watch(['compass']);
    gulp.watch(['webpack-dev']);
});


//default task
gulp.task('dev', ['watch-dev', 'webpack-dev', 'compass']);

//default task
gulp.task('default', ['watch', 'webpack', 'compass']);