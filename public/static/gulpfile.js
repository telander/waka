/**
 * Created by jill on 16/4/27.
 */

var gulp = require("gulp");
var gutil = require("gulp-util");
var $ = require('gulp-load-plugins')();
var fileinclude = require("gulp-file-include");
var preprocess = require("gulp-preprocess");
var replacepath = require("gulp-replace-path");
var replace = require("gulp-replace");
var less = require("gulp-less");
var cssmin = require('gulp-minify-css');
var webpack = require("webpack");
var del = require('del');
var runSequence = require("run-sequence");
var webpackConfig = require("./webpack.config.js");
var paramConfig = require('./configs/config.json');
var product = gulp.env.product;
var nodeEnv = product ? 'product' : 'debug';


gulp.task('html:copy', function () {
    return gulp.src([
        'dev/**/*.html'
    ])
        .pipe(fileinclude({
            prefix: '@@',
            basepath: '@file'
        }))
        .pipe(replacepath("@@path"))
        .pipe(preprocess({context: { NODE_ENV: nodeEnv}}))
        //modify gulp-replace manually
        .pipe(replace("", "", {skipBinary: true, customReplace: true, localDest: "release/", basePath: paramConfig.debug.CDN + "static"}))
        .pipe(gulp.dest('release'));
});

gulp.task("img:copy", function() {
    return gulp.src([
        "dev/**/*.jpg",
        "dev/**/*.png",
        "dev/**/*.jpeg",
        "dev/**/*.gif",
    ])
        .pipe(gulp.dest("release"));
});

gulp.task("less:build", function() {
    return gulp.src([
        'dev/**/*.less'
    ])
        .pipe(less())
        .pipe(cssmin())
        .pipe(gulp.dest("release"));
});

gulp.task("js:webpack:build", function(callback) {
    var myConfig = Object.create(webpackConfig);
    webpack(myConfig, function(err, stats) {
        if(err) throw new gutil.PluginError("webpack", err);
        gutil.log("[webpack]", stats.toString({
            colors: true
        }));
        callback();
    });
});


gulp.task("clean", function(callback) {
    return del(["release/**", "newlib/**"]).then(function(paths) {
        console.log('Deleted files and folders:\n', paths.join('\n'));
    })
});

gulp.task("build", function(callback) {
    runSequence("clean", ["js:webpack:build"], ["img:copy", "less:build", "html:copy"], callback);
});