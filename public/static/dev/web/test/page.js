/**
 * Created by jill on 16/4/26.
 */

alert("welcome to home page");

var h1 = $("h1");
var $i = 0;
h1.on("click", function(){
    $i++;
    $(this).text("hello world! " + $i);
})