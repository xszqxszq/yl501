//index.js

//reply

//dafeult val
var defaultReply = 3, pageReply = 10, prevReply, nowReply, nextReply, time = new Date();

//creat comment
function creatComment(comment) {
    var time = $("<span></span>").text(comment.time);
    var text = $("<span></span>").text(comment.text);
    var user = $("<a></a>").text(comment.user);
    user.prop("href", "");
    var commentFooter = $("<div class=\"comment-footer\"></div>").append(user, time, "<span class=\"fa fa-commenting fa-fw\"></span>");
    return $("<div class=\"li\"></div>").append(text, commentFooter);
}

//creat reply
function creatReply(reply) {
    var ans = new Array();
    for(i in reply) {
        ans[i] = creatComment(reply[i]);
    }
    return ans;
}

//append reply
function appendReply(select, reply) {
    select.append(creatReply(reply));
}

//prepend reply
function prependReply(select, reply) {
    select.prepend(creatReply(reply));
}

//load next reply
function loadNextReply(select) {
    prevReply = nowReply;
    nowReply = nextReply;
    appendReply(nextReply);
}

$(document).ready(function() {
    //add show/hide reply button
    $(".chat>.frame>.li").children(".comment-footer").append("<span class=\"fa fa-chevron-down fa-fw\"></span>");

    //add reply button
    $(".chat .comment-footer").append("<span class=\"fa fa-commenting fa-fw\"></span>");

    //add form
    //$(".chat>.frame>.li").append("<div class=\"reply-form ul table\"><div class=\"frame table\"><div class=\"li\"></div></div></div>");
    //$(".chat>.frame>.li").append("<div class=\"reply-form frame\"><p contenteditable=\"true\" style=\"\">ads</p></div>");

    //hide reply
    $(".chat .reply").data("display", "hide");
    /*
    //return reply in (begin, end)
    function getReply(select){
    return select.children("div" + ":gt(" + select.attr("begin") + ")" + ":lt(" + select.attr("end") + ")");
    }
    */
    //init reply
    $(".chat .reply").each(function() {
        var select = $(this);
        $.get("http://172.45.33.100/cgi-bin/reply.cgi?reply=a", function(data, status) {
            appendReply(select.children(), data);
            update(select, height(select.children().children().slice(0, defaultReply)));
        });

        //loadReply($(this).children(), reply);
        //if(!$(this).children().length){
        //update($(this), height($(this).children().children().slice(0, defaultReply)));
        //$(this).hide();
        //}
        /*$(this).attr({
          "begin": "-1",
          "end"  : defaultReply
          });*/
    });

    /*
    //resize reply height
    function replyResize(select){
    var height = 0;
    switch(select.attr("display")){
    case "show":
    getReply(select).each(function(){
    height += $(this).outerHeight();
    });
    select.css("height", height + "px");
    break;
    }
    }
    */

    //update reply height
    $(window).resize(function() {
        $(".chat .reply").each(function() {
            //replyResize($(this));
            //update reply display
            switch($(this).data("display")) {
            case "show":
                update($(this), height($(this).children()));
                //$(this).children("div.reply-form").css("display", "none");
                break;
            }
        });
    });
    /*$(window).resize();*/

    //show/hide button
    $(".chat .comment-footer>.fa-chevron-down").on("click", function() {
        var replySelect = $(this).parent().siblings(".reply");
        switch(replySelect.data("display")) {
        case "show":
            hide(replySelect);
            $(this).removeClass("fa-rotate-180");
            replySelect.data("display", "hide");
            //replyResize(replySelect);
            break;
        case "hide":
            show(replySelect, height(replySelect.children()));
            $(this).addClass("fa-rotate-180");
            replySelect.data("display", "show");
            //replyResize(replySelect);
            //update reply display
            //replySelect.children("div:not(.reply-form)").css("display", "");
            break;
        }
    });
    /*
    //reply button
    $(".chat>div>.comment-footer>.fa-commenting").click(function(){
    var replySelect = $(this).parent().siblings(".reply");
    switch(replySelect.attr("display")){
    case "show":
    showReply(replySelect);
    replySelect.attr("display", "reply");
    replyResize(replySelect);
    //update reply display
    //replySelect.children(".reply-form").css("display", "");
    break;
    case "hide":
    showReply(replySelect);
    $(this).siblings(".fa-chevron-down").addClass("fa-rotate-180");
    replySelect.attr("display", "reply");
    replyResize(replySelect);
    //update reply display
    //replySelect.children(".reply-form").css("display", "");
    break;
    }
    });

    $(".chat .reply .comment-footer>.fa-commenting").click(function(){
    $(this).parents(".reply").siblings(".comment-footer").children(".fa-commenting").click();
    });

    $(".chat .reply-form textarea").autoResize(function(){
    var replySelect = $(this).parents(".reply");
    replyResize(replySelect);
    });
    */

    $("#demo").click(function() {
        var select = $(this).prev();
        //*
        $.get("http://172.45.33.100/cgi-bin/reply.cgi?reply=b", function(data, status) {
          select.children().empty();
          appendReply(select.children(), data);
          update(select, height(select.children().children()));
        });
        //*/
        //hide(select.children().children());
    });
});
