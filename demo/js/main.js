//main.js
//chat
function height(select) {
    var ans = 0;
    select.each(function() {
        ans += $(this).outerHeight(true);
    });
    return ans;
}

//hide
function hide(select) {
    select.each(function() {
        $(this).css({
            "transition": "height 0.5s, opacity 0.5s, margin 1s",
            "margin-top": "-5px",
            "opacity"   : "0",
            "height"    : "0"
        });
    });
}

//show
function show(select, height) {
    select.each(function() {
        $(this).css({
            "transition": "height 0.5s, opacity 0.5s, margin 1s",
            "margin-top": "",
            "opacity"   : "1",
            "height"    : height + "px"
        });
    });
}

//update
function update(select, height) {
    select.each(function() {
        $(this).css({
            "transition": "height 0.5s, opacity 0.5s, margin 1s",
            "height" : height + "px"
        });
    });
}
