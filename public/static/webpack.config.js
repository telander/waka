var paramConfig = require('./configs/config.json');
var webpack = require("webpack");
var glob = require("glob");

var ExtractTextPlugin = require("extract-text-webpack-plugin");
var HtmlWebpackPlugin = require("html-webpack-plugin");

var cssExtractPlugin = new ExtractTextPlugin("[name].css");
var htmlExtractPlugin = new ExtractTextPlugin("[name].html");
//替换变量
var definePlugin = new webpack.DefinePlugin({
    'process.env.__DOMAIN_COOKIE':JSON.stringify(paramConfig.product.DOMAIN_COOKIE),
    'process.env.__DOMAIN_SERVER':JSON.stringify(paramConfig.product.DOMAIN_SERVER),
    'process.env.__PAYMENT_DOMAIN_SERVER':JSON.stringify(paramConfig.product.PAYMENT_DOMAIN_SERVER),
    'process.env.__CDN':JSON.stringify(paramConfig.product.CDN)
});

//var glob = require("glob");
console.log(paramConfig.product);
console.log(__dirname);

var entries = {
        'newlib/js/core/app.min': ["./jslib/modules/waka.js"],
        'newlib/js/core/app.mobile.min': ["./jslib/modules/waka.js"]
        //'newlib/js/core/app.min': ["./jslib/3rdlib/jquery-1.10.2.min.js", "./jslib/3rdlib/less-1.7.3.min.js", "./jslib/3rdlib/artTemplate3.js", "./newlib/js/core/app.js"],
        //'newlib/js/core/app.mobile.min': ["./jslib/3rdlib/jquery-2.1.1.min.js"]

    };

var files = glob.sync("./dev/web/**/page.js", {});
var pagePrefix = "release/";
console.log(files);
files.forEach(function(filepath) {
    var name = filepath.replace("dev/", pagePrefix, 1).substr(2);
    name = name.substr(0, name.length - 3);
    entries[name] = [filepath];
});

module.exports = {
    entry: entries,
    output: {
        path: "./",
        filename: "[name].js"
    },
    plugins: [
        definePlugin,
        cssExtractPlugin,
        htmlExtractPlugin
        //new HtmlWebpackPlugin({filename: "[name].html"})
    ],
    module: {
        loaders: [
            {
                test: /\.less$/,
                loader: cssExtractPlugin.extract("style-loader", "css-loader!less-loader")
            },
            {
                test: /\.html/,
                loader: htmlExtractPlugin.extract("html-loader!ejs-html-loader!ejs-loader")
            },
            {
                test: /\.ejs/,
                loader: "ejs?title=ejsloader"
            }
        ]
    }
}

