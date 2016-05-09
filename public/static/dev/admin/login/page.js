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