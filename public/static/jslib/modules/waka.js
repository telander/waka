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
        },
        clientType: function() {
            if (ua.indexOf("micromessenger") !== -1) {
                return "wx";
            }
            return "other";
        },
        urlParams: function (name) {
            var reg = new RegExp("(^|\\?|&)" + name + "=([^&#]*)");
            var r = window.location.href.substring(1).match(reg);
            if (r != null) {
                return decodeURIComponent(r[2]);
            }
            return '';
        },
        weChatOpenId: function (callback) {
            if (waka.clientType() === "wx") {
                var code = waka.urlParams("code");
                if(waka.urlParams('fromAuth') === 'userinfo'){
                    if(callback && typeof callback=='function')callback();
                }else{
                    if (code) {
                        waka.ajax({
                            type: 'GET',
                            url: DOMAIN_SERVER + '/ajax/snsAjax/getWeChatOpenId',
                            data: {
                                'code': code
                            },
                            async: false,
                            success: function (data) {
                                if (data.ok === 0) {
                                    travo.wxOpenId = data.obj.openid;
                                    if(callback && typeof callback=='function')callback();
                                }else{
                                    alert(data.msg);
                                }
                            },
                            error:function(data){
                                alert(data.msg);
                            }
                        });
                    } else {
                        var url = 'http://tao.117go.com:8011/data/snsAjax/getWxOAuth2Redirect_Base?retUrl=' + encodeURIComponent(window.location.href);
                        window.location.replace(url);
                        return;
                    }
                }

            } else {
                travo.alert("请在微信中打开!");
            }
        },
     });

    window.k.ajax.send = ajax.send;


    /*waka.fn.extend({

     });*/
    window.waka.template = window.template = require("../3rdlib/artTemplate3.js");
})(window);
