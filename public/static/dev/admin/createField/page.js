/**
 * Created by jill on 16/5/9.
 */

require("../../../jslib/templates/admin/admin_head_nav.js");
require("../../../jslib/templates/admin/admin_left_nav.js");
$(function() {
    var $thisNav = $("#navLeftFocus_createField");
    $thisNav.addClass("active");
    $thisNav.parent().css({"display": "block"});
});
