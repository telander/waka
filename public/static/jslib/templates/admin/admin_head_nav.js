/**
 * Created by jill on 16/5/9.
 */
$(function(){
    $("#userMobileLoad").text(window.k.getAdminMobile());

    $("#userLogout").on("click", function() {
        var $this = $(this);
        window.k.ajax({
            type: "POST",
            url: $this.data("url"),
            success: function(data){
                if(data.ok == 1) {
                    window.location.href = "/admin/login";
                }
                else {
                    alert(data.msg || "退出登录失败，请联系管理员");
                }
            }
        });
    });

    $("#userModifyPassword").on("click", function() {
        var $this = $(this);
        alert("请联系管理员，暂时不开放");
        //window.k.ajax({
        //    type: "POST",
        //    url: $this.data("url"),
        //    success: function(data){
        //
        //    }
        //});
    });
});