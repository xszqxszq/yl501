//exam.html
/*
function updateItem(item, data) {
    switch(data["status"]) {
    case -1:
        $(item).children().eq(1).addClass("waiting");
        $(item).children().eq(1).text("Waiting");
        break;
    case  0:
        $(item).children().eq(1).addClass("unknowError");
        $(item).children().eq(1).text("Unknow Error");
        break;
    case  1:
        $(item).children().eq(1).addClass("accepted");
        $(item).children().eq(1).text("Accepted");
        break;
    case  2:
        $(item).children().eq(1).addClass("wrongAnswer");
        $(item).children().eq(1).text("Wrong Answer");
        break;
    case  3:
        $(item).children().eq(1).addClass("runtimeError");
        $(item).children().eq(1).text("Runtime Error");
        break;
    case  4:
        $(item).children().eq(1).addClass("compileError");
        $(item).children().eq(1).text("Compile Error");
        break;
    case  5:
        $(item).children().eq(1).addClass("timeLimitExceeded");
        $(item).children().eq(1).text("Time Limit Exceeded");
        break;
    case  6:
        $(item).children().eq(1).addClass("memoryLimitExceeded");
        $(item).children().eq(1).text("Memory Limit Exceeded");
        break;
    case  7:
        $(item).children().eq(1).addClass("outputLimitExceeded");
        $(item).children().eq(1).text("Output Limit Exceeded");
        break;
    case  8:
        $(item).children().eq(1).addClass("judging");
        $(item).children().eq(1).text("Judging");
        break;
    }
    if(data["time"]   !== "") $(item).children().eq(2).text(data["time"]   + "ms");
    if(data["memory"] !== "") $(item).children().eq(3).text(data["memory"] + "KB");
}

$(document).ready(function() {
    //alert($("article").html());
    $.get("http://172.45.33.100/demo/request.php?id=24", function(data, status) {
        var item = $("article .item").toArray();
        for(i in data["testpoints"]) alert($(item[i]).html());updateItem(item[i], data["testpoints"][i]);
    });
});
*/
