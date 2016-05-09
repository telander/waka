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
        cookie: function (name, value, time) {
            var exDate = new Date();
            if (value === undefined) {
                if (document.cookie.length > 0) {
                    var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");
                    if (arr = document.cookie.match(reg)) {
                        return decodeURIComponent(arr[2]);
                    } else {
                        return null;
                    }
                }
            } else if (value === null) {
                exDate.setTime(exDate.getTime() - 1);
                document.cookie = name + "=" + encodeURIComponent(value) + ";expires=" + exDate.toGMTString();
            } else {
                if (time === undefined) {
                    time = 240; //默认10天过期
                }
                exDate.setTime(exDate.getTime() + time * 3600 * 1000);
                document.cookie = name + "=" + encodeURIComponent(value) + ";expires=" + exDate.toGMTString() + ";path=/;domain=" + DOMAIN_COOKIE;
            }
        },
        isLogin: function() {
            return !!waka.cookie("WAKAUID") ? true : false;
        },
        getAdminMobile: function() {
            return !!waka.cookie("WAKAUMB") ? waka.cookie("WAKAUMB") : "GUEST";
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
                            url: DOMAIN_SERVER + '/ajax/snsApi/getWeChatOpenId',
                            data: {
                                'code': code
                            },
                            async: false,
                            success: function (data) {
                                if (data.ok === 1) {
                                    waka.wxOpenId = data.obj.openid;
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
                        var url = DOMAIN_SERVER + '/ajax/snsApi/getWxOAuth2Redirect_Base?retUrl=' + encodeURIComponent(window.location.href);
                        window.location.replace(url);
                        return;
                    }
                }

            } else {
                alert("请在微信中打开!");
            }
        },
        getWeChatUser: function(callback) {
            if(!waka.weChatUserInfo && waka.clientType() == "wx") {
                var _getWechatUser = function(openId) {
                    var code = waka.urlParams('code');
                    var fromAuth = waka.urlParams('fromAuth');
                    // snsapi_userinfo用户同意授权
                    if(fromAuth == 'userinfo') {
                        if(code) {
                            waka.ajax({
                                url: "/ajax/snsApi/getWeChatOpenIdUserInfo",
                                data: {
                                    code: code
                                },
                                type: "POST",
                                async: false,
                                success: function(data) {
                                    if(data.ok == 1) {
                                        var openid = data.obj.openid;
                                        var weChatUserInfo = data.obj.weChatUserInfo;
                                        waka.weChatUserInfo = weChatUserInfo;
                                    }
                                    else {
                                        waka.ajax({url: "/indexApi/s?req=" + encodeURIComponent("/ajax/snsApi/getWeChatOpenIdUserInfo") + "&code=" + code});
                                    }
                                    if(callback && typeof callback == 'function'){callback(data)}
                                },
                                error: function() {
                                    waka.ajax({url: "/indexApi/s?req=" + encodeURIComponent("/ajax/snsApi/getWeChatOpenIdUserInfo") + "&code=" + code});
                                }
                            })
                        }
                        // 授权失败
                        else {
                            alert("授权拒绝！");
                        }
                    }
                    // 直接根据openid拿用户信息
                    else {
                        waka.ajax({
                            url: "/ajax/snsApi/getWeChatUserInfoByOpenId",
                            data: {
                                openid: openId
                            },
                            type: "POST",
                            async: false,
                            success: function(data) {
                                if(data.ok == 1) {
                                    var weChatUserInfo = data.obj.weChatUserInfo;
                                    if(weChatUserInfo) {
                                        waka.weChatUserInfo = weChatUserInfo;
                                    }
                                    else {
                                        var search = window.location.search;
                                        if(search) {
                                            var addon = "&fromAuth=userinfo";
                                        }
                                        else {
                                            var addon = "?fromAuth=userinfo";
                                        }
                                        window.location.replace("/ajax/snsApi/getWxOAuth2Redirect_UserInfo?retUrl=" + encodeURIComponent(window.location.href + addon));
                                    }
                                    if(callback && typeof callback == 'function'){callback(data)}
                                }
                                else {
                                    waka.ajax({url: "/indexApi/s?req=" + encodeURIComponent("/ajax/snsApi/getWeChatUserInfoByOpenId") + "&openid=" + openid});
                                }
                            },
                            error: function() {
                                waka.ajax({url: "/indexApi/s?req=" + encodeURIComponent("/ajax/snsApi/getWeChatUserInfoByOpenId") + "&openid=" + openid});
                            }
                        })
                    }
                };

                if(waka.wxOpenId) {
                    _getWechatUser(waka.wxOpenId);
                }
                else {
                    waka.weChatOpenId(function () {
                        _getWechatUser(waka.wxOpenId);
                    });
                }
            }

        },

        submitWxLogin: function(needUser, callback) {
            if(!waka.isLogin() && waka.clientType() == "wx") {
                var login = function(openId) {
                    if (!!openId) {
                        waka.ajax({
                            type: "POST",
                            url: DOMAIN_SERVER + "/ajax/userApi/submitWxLogin",
                            data: {
                                'openId': openId
                            },
                            async: false,
                            success: function(data) {
                                if(data.ok == 1) {
                                    if (needUser) {
                                        waka.user = data.obj;
                                    }
                                    if (callback && typeof callback == 'function')callback();
                                }
                                else {
                                    alert(data.msg + data.code);
                                }
                            },
                            error: function(data) {
                                alert(data.msg);
                            }
                        });
                    }
                    else {
                        alert("微信自动登录失败，请稍后重试");
                    }
                };

                if(waka.wxOpenId) {
                    login(waka.wxOpenId);
                }
                else {
                    waka.weChatOpenId(function () {
                        login(waka.wxOpenId);
                    });
                }
            }
            return;
        }
    });

    window.k.ajax.send = ajax.send;


    /*waka.fn.extend({

     });*/
    window.waka.template = window.template = require("../3rdlib/artTemplate3.js");
    require("../bootstrap/js/bootstrap.js");
})(window);
