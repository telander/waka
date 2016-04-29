/**
 * Created by jill on 16/4/26.
 */
(function (window, undefined) {

    /** @const */
    var DOMAIN_COOKIE = process.env.__DOMAIN_COOKIE; // cookie根目录
    /** @const */
    var DOMAIN_SERVER = process.env.__DOMAIN_SERVER; // 服务端域名
    /** @const */
    var PAYMENT_DOMAIN_SERVER = process.env.__PAYMENT_DOMAIN_SERVER; // 服务端域名
    /** @const */
    var CDN_PREFIX = process.env.__CDN; // CDN静态路径

    var
        window = this,
        undefined,
        document = window.document,
        ua = navigator.userAgent.toLowerCase(),
        waka = function () {
            return new waka.fn.init();
        };
    window.k = window.waka = waka;
    waka.DOMAIN_COOKIE = DOMAIN_COOKIE;
    waka.DOMAIN_SERVER = DOMAIN_SERVER;
    waka.PAYMENT_DOMAIN_SERVER = PAYMENT_DOMAIN_SERVER;
    waka.CDN_PREFIX = CDN_PREFIX;

    window.k = window.k || {};

    window.jQuery = window.$ = require("../3rdlib/jquery-2.1.1.min.js");

    waka.fn = waka.prototype = {
        init: function (config) { return this;}
    }

    waka.fn.init.prototype = waka.fn;

    waka.extend = waka.fn.extend = function (obj) {
        for (var prop in obj) {
            this[prop] = obj[prop];
        }
        return this;
    }

    var ajax = require("../modules/ajax.js");

    waka.extend({
        ajax: function(mySettings) {
            return ajax.send(mySettings);
        },
        stop: function () {
            return ajax.stop();
        },
        createSign: function () {
            return ajax.createSign();
        }
     });

    window.k.ajax.send = ajax.send;


    /*waka.fn.extend({

     });*/
    window.waka.template = window.template = require("../3rdlib/artTemplate3.js");
})(window);
