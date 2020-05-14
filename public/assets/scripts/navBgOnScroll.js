$(document).ready(function(){
    $(window).scroll(function(){
        if(window.innerWidth>991) {
            if ($(this).scrollTop() > 100) {
                $("#navbar").css("background-color", "rgb(255, 0, 83)");
            } else {
                $("#navbar").css("background-color", "rgba(0,0,0,0.0)");
            }
        }else{
            $("#navbar").css("background-color", "rgb(255, 0, 83)");
        }
    })
})