/**
 * Created by jill on 16/4/26.
 */


var h1 = $("h1");
var $i = 0;
h1.on("click", function(){
    $i++;
    $(this).text("hello world! " + $i);
});

var myCity = "";
var myProvince = "";
waka.getWeChatUser(function() {
    waka.submitWxLogin(true, function () {
        myCity = waka.user.city;
        myProvince = waka.user.province;
        alert("您的常居城市" + waka.user.province + myCity);
    });
});


