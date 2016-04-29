
var _ajaxRequests = {},
    _ajaxRequestsId = 0,
    _beforeSend = function(xhr, settings) {
        xhr.id = 'k' + (_ajaxRequestsId++);
        _ajaxRequests[xhr.id] = xhr;
    },
    _complete = function(xhr, status) {
        if (status == 'timeout' /*|| (status == 'abort' && !xhr.manAbort)*/) {
            delete _ajaxRequests[xhr.id];
            alert('网络不给力哦！请稍后再试!');
            //if(window.k.closeWebview) window.k.closeWebview();
        } else {
            delete _ajaxRequests[xhr.id];
        }
    },
    _bizFailHandler = undefined;
//_bizFailHandler = function(data){
//    if (data.ok === 6003){
//        window.k.login();
//    }else{
//        alert(data.msg);
//    }
//};

var encrypt = {s:function(){}};

function getSessionId() {
    var name = "PHPSESSID=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}

module.exports = {
    setBizFailHandler: function(handler) {
        _bizFailHandler = handler;
    },
    send: function(mySettings) {
        var _mySettings = {};
        _mySettings.timeout = mySettings.timeout || 10000;
        _mySettings.beforeSend = function(xhr, settings) {
            _beforeSend(xhr, settings);
            if (mySettings.beforeSend) mySettings.beforeSend(xhr, settings);
        };
        _mySettings.complete = function(xhr, status) {
            _complete(xhr, status);
            if (mySettings.complete) mySettings.complete(xhr, status);
        };
        _mySettings.error = function(xhr, errorType, error) {
            if (mySettings.error) {
                mySettings.error(xhr, errorType, error);
            } else {
                if (status == 'error') {
                    alert('网络不给力哦！请稍后再试!');
                }
            }
        };
        _mySettings.success =  function(data, textStatus,jqXHR) {
            if (!!data && data.ok != 0) {
                if (mySettings.bizFail) {
                    mySettings.bizFail(data);
                } else {
                    if (_bizFailHandler) {
                        _bizFailHandler(data);
                    } else {
                        if (mySettings.success) {
                            mySettings.success(data, textStatus, jqXHR);
                        }
                    }
                }
            } else {
                if (mySettings.success) {
                    mySettings.success(data, textStatus, jqXHR);
                }
            }
        };
        if (mySettings.processData == undefined || mySettings.processData == null) {
            mySettings.processData = true;
        } else {
            mySettings.processData = mySettings.processData;
        }
        if (mySettings.xhrFields == undefined || mySettings.xhrFields == null) {
            mySettings.xhrFields = {
                withCredentials: true
            }
        } else {
            mySettings.xhrFields = mySettings.xhrFields;
        }
        if (mySettings.processData) {
            _mySettings.data = {};
            if (!mySettings.data) {
                mySettings.data = {};
            }
            var indexOfQuery = mySettings.url.indexOf('?');
            if (indexOfQuery > 0) {
                var url = mySettings.url.substring(0, indexOfQuery);
                var urlQueryString = mySettings.url.substring(indexOfQuery + 1);
                var urlQueries = urlQueryString.split('&');
                for (var i = 0, l = urlQueries.length; i < l; i++) {
                    var kv = urlQueries[i].split('=');
                    if (kv.length == 2) {
                        _mySettings.data[kv[0]] = decodeURIComponent(kv[1]);
                    }
                }
                _mySettings.url = url;
            } else {
                _mySettings.url = mySettings.url;
            }
            if (typeof mySettings.data == 'object') {
                for (var key in mySettings.data) {
                    _mySettings.data[key] = mySettings.data[key];
                }
            } else if (typeof mySettings.data == 'string' && mySettings.data.length > 0) {
                var kvs = mySettings.data.split('&');
                for (var i = 0, l = kvs.length; i < l; i++) {
                    var kv = kvs[i].split('=');
                    if (kv.length == 2) {
                        _mySettings.data[kv[0]] = decodeURIComponent(kv[1]);
                    }
                }
            }
            _mySettings.data.timestr = (new Date()).getTime();
            var dataKeys = [];
            for (var key in _mySettings.data) {
                dataKeys.push(key);
            }
            dataKeys.sort();
            var paramStr = '';
            for (var i = 0, l = dataKeys.length; i < l; i++) {
                if (_mySettings.data[dataKeys[i]] == undefined || _mySettings.data[dataKeys[i]] == null) continue;
                var qValue = _mySettings.data[dataKeys[i]] + '';
                if (qValue.length < 1) continue;
                paramStr += dataKeys[i] + '=' + $.trim(qValue) + '&';
            }
            _mySettings.data.sign = encrypt.s(paramStr + 'tzls117go!@#' + getSessionId());
        } else {
            _mySettings.data = mySettings.data || {};
            var _data = {};
            var indexOfQuery = mySettings.url.indexOf('?');
            if (indexOfQuery > 0) {
                var url = mySettings.url.substring(0, indexOfQuery);
                var urlQueryString = mySettings.url.substring(indexOfQuery + 1);
                var urlQueries = urlQueryString.split('&');
                for (var i = 0, l = urlQueries.length; i < l; i++) {
                    var kv = urlQueries[i].split('=');
                    if (kv.length == 2) {
                        _data[kv[0]] = decodeURIComponent(kv[1]);
                    }
                }
                _mySettings.url = url;
            } else {
                _mySettings.url = mySettings.url;
            }
            if (mySettings.kdata) {
                for (var key in mySettings.kdata) {
                    _data[key] = mySettings.kdata[key];
                }
            }
            _data.timestr = (new Date()).getTime();
            var dataKeys = [];
            for (var key in _data) {
                dataKeys.push(key);
            }
            dataKeys.sort();
            var paramStr = '';
            for (var i = 0, l = dataKeys.length; i < l; i++) {
                if (!_data[dataKeys[i]]) continue;
                var qValue = _data[dataKeys[i]] + '';
                if (qValue.length < 1) continue;
                paramStr += dataKeys[i] + '=' + $.trim(qValue) + '&';
            }
            _mySettings.url += '?' + paramStr + 'sign=' + encrypt.s(paramStr + 'tzls117go!@#' + getSessionId());
        }

        for (var key in mySettings) {
            if (mySettings.hasOwnProperty(key)) {
                if (key != 'timeout' &&
                    key != 'beforeSend' &&
                    key != 'complete' &&
                    key != 'error' &&
                    key != 'data' &&
                    key != 'url' &&
                    key != 'success') {
                    _mySettings[key] = mySettings[key];
                }
            }
        }
        $.ajax(_mySettings);
    },
    stop: function() {
        for (var key in _ajaxRequests) {
            _ajaxRequests[key].manAbort = true;
            _ajaxRequests[key].abort();
        }
    }
};
