$( document ).ready(function() {
  $(".name").click(function(){
    $(".user-settings ").slideToggle("slow");
    $(".user-settings *").slideToggle("slow");
  });
});
