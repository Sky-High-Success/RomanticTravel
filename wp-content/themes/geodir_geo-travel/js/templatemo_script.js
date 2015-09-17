/* Credit: http://www.templatemo.com */
jQuery(document).ready(function($)
{
    $ = jQuery ;
    $(window).load( function() {
        $('.external-link').unbind('click');    
    });    
    //templatemo_banner_slide function
    $('.banner').unslider({fluid: true});
    $(window).on("load scroll resize", function(){
        banner_height = ($(document).width()/1920) * 760;
        //        banner_height = ($(document).width()/1920) * 760;
        $('.banner').height(banner_height);
        $('.banner ul li').height(banner_height);
        if(banner_height > 280){
            caption_margin_top = (banner_height-250)/2;
            $('.banner .slide_caption:hidden').show();
            $('.banner .slide_caption').css({"margin-top":caption_margin_top});
        }else{
            $('.banner .slide_caption').hide();
        }
        $("#templatemo_banner_slide > ul > li").css({"background-size":"cover"});
    });
    //mobile menu and desktop menu
    hide_left = $(document).width();
    $("#mobile_menu").css({left: hide_left});
    $("#mobile_menu").hide();
    $("#mobile_menu_btn").click(function(){
        if($('#mobile_menu').is(':visible')) {
            hide_left = $(document).width();
            $("#mobile_menu").animate({left: hide_left},1000,function(){
                $("#mobile_menu").hide();
            });
        }else{
            $("#mobile_menu").show();
            show_left = $(document).width() - 250 ;
            $("#mobile_menu").animate({left: show_left},1000,function() {
            });
            
        }
        return false;
    });
    
    $("#mobile_menu_hide_btn").click(function(){
        if($('#mobile_menu').is(':visible')) {
            hide_left = $(document).width();
            
            $("#mobile_menu").animate({left: hide_left},1000,function(){
                $("#mobile_menu").hide();
            });
        }else{
            $("#mobile_menu").show();
            show_left = $(document).width() - 250 ;
            $("#mobile_menu").animate({left: show_left},1000);
        }
        return false;
    });
 
    jQuery.fn.anchorAnimate = function(settings) {
        settings = jQuery.extend({
            speed : 1100
        }, settings);	
        return this.each(function(){
            var caller = this
            $(caller).click(function (event){
                event.preventDefault();
                var locationHref = window.location.href;
                var elementClick = $(caller).attr("href");
                var destination = $(elementClick).offset().top ;
                //hide the mobile menu
                hide_left = $(document).width();
                $("#mobile_menu").animate({left: hide_left},1000,function(){
                    $("#mobile_menu").hide();
                });

                $("html,body").stop().animate({ scrollTop: destination}, settings.speed, function(){
                    // Detect if pushState is available
                    if(history.pushState) {
                        history.pushState(null, null, elementClick);
                    }
                });
                return false;
            });
        });
    };
    
    
    //animate scroll function calll
//    $("#mobile_menu a").anchorAnimate();
    //main menu auto select and animate scroll
    $("#templatemo_main_menu ul").singlePageNav({offset: jQuery('#templatemo_main_menu').outerHeight()});
    //define main menu position
    $(window).on("resize scroll load",function(){
        top_banner_and_slider_height = $("#templatemo_banner_top").outerHeight() + $("#templatemo_banner_slide").outerHeight() +  $("#templatemo_banner_logo").outerHeight() ;
        if($(document).scrollTop() < (top_banner_and_slider_height - $(window).height() + 105) ){
            menu_top = $(document).scrollTop() + $(window).height() - 105 ;
            $("#templatemo_main_menu").css({"position":"absolute","top":menu_top});
        }else if( $(document).scrollTop() < top_banner_and_slider_height ){
            menu_top = 0;
            $("#templatemo_main_menu").css({"position":"relative","top":menu_top});
        }else{
            menu_top = 0;
            $("#templatemo_main_menu").css({"position":"fixed","top":menu_top});
        }
    });
});