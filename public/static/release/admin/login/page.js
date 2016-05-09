/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ({

/***/ 0:
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(10);


/***/ },

/***/ 10:
/***/ function(module, exports) {

	$(function(){
	    var loginForm = $("#loginForm"),
	        $loginFormMobile = $('#loginFormMobile'),
	        $loginFormPassword = $('#loginFormPassword');

	    var retUrl = window.k.urlParams('retUrl') == false ? "/admin" : window.k.urlParams('retUrl');
	    loginForm.on("submit", function(e) {
	        e.preventDefault();
	        var fields = loginForm.serializeArray(),
	            submitData = {};
	        for(var i=0,len=fields.length; i<len; i++) {
	            if (fields[i].name == 'mobile') {
	                fields[i].value = $.trim(fields[i].value);
	                if (fields[i].value.length < 4) {
	                    $loginFormMobile.addClass('has-error');
	                    return;
	                }
	                submitData.mobile = fields[i].value;
	            } else if (fields[i].name == 'password') {
	                fields[i].value = $.trim(fields[i].value);
	                if (fields[i].value.length < 1) {
	                    $loginFormPassword.addClass('has-error');
	                    return;
	                }
	                submitData.password = fields[i].value;
	            }
	        }
	        window.k.ajax({
	            type: "POST",
	            url: loginForm.attr('action'),
	            data: submitData,
	            success: function(data) {
	                if(data.ok == 1) {
	                    window.location.href = retUrl;
	                }
	                else {
	                    if(data.msg) {
	                        alert(data.msg);
	                    }
	                }
	            }
	        });
	    })
	});

/***/ }

/******/ });