$(function() {
   $(".nav-menu-tree").on("click", function(e){
       var $this = $(this);
       e.preventDefault();
       $this.find(".pull-right").toggleClass("pull-right-rotate");
       $this.parent().find(".nav-menu-tree-ul").slideToggle(200);
   })
});