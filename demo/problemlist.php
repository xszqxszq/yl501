<?php include "header.php"; $GLOBALS['page_name']="题目"; showHeader();?>

<?php
global $DB_conn;
if (! empty($_GET['oj'])) {
    $oj = $DB_conn->real_escape_string($_GET['oj']);
} else {
    $oj = "oj";
}
if (! empty($_GET['page']) && is_numeric($_GET['page'])) {
    $page = $DB_conn->real_escape_string($_GET['page']);
} else {
    $page = 1;
}
if (isset($_GET['wd'])) {
    $wd = $DB_conn->real_escape_string($_GET["wd"]);
    $wdCode = "and (upper(name) like binary concat('%',upper('" . $wd . "'),'%')";
    $wdCode .= " or upper(problem) like binary concat('%',upper('" . $wd . "'),'%'))";
} else {
    $wd = "";
    $wdCode = 'and 1';
}
$currURI_nw = $_SERVER['PHP_SELF'] . "?oj={$oj}";
$currURI = $currURI_nw . "&wd={$wd}";
$pageLimit = $GLOBALS['page_limit'];
$recordCount = ($DB_conn->query("select count(1) from problem where oj='{$oj}' {$wdCode}")->fetch_row()[0]);
$pageCount = ceil((float) $recordCount / (float) $pageLimit);

$limit = ($page - 1) * $pageLimit;
$problem = $DB_conn->query("select * from problem where oj='{$oj}' {$wdCode} limit {$limit},{$pageLimit}");
$probList = array();
while ($row = $problem->fetch_assoc()) {
    array_push($probList, '<span>' . $row['problem'] . "</span> <a href=\"./problem.php?id={$row['problem']}\">" . $row['name'] . "</a>");
}
if (1 <= $page && $page <= $pageCount) {
    $button = "<div class=\"ul pagination\"><div class=\"frame pagination\">";
    if ($page == 1)
        $prepage = 1;
    else
        $prepage = $page - 1;
    if ($page == $pageCount)
        $nextpage = $page;
    else
        $nextpage = $page + 1;
    $button = $button . <<<s
<a class="li" href="{$currURI}&page=1"><span class="fa fa-angle-double-left fa-fw" style="color: #3399FF"></span></a>
s;
    $button = $button . <<<s
<a class="li" href="{$currURI}&page={$prepage}"><span class="fa fa-angle-left fa-fw" style="color: #3399FF"></span></a>
s;
    $pageOffset = $pageCount - $page;
    if ($page <= 4)
        $pageOffset = 9 - $page;
    elseif ($pageOffset > 4)
        $pageOffset = 4;
    for ($i = $page - (8 - $pageOffset); $i <= $page + $pageOffset; $i ++) {
        if ($i <= 0) {
            continue;
        }
        if ($i > $pageCount)
            break;
        if ($i == $page)
            $button = $button . <<<s
    <a class="li active" href="{$currURI}&page={$i}"><span>{$i}</span></a>
s;
        else
            $button = $button . <<<s
    <a class="li" href="{$currURI}&page={$i}"><span>{$i}</span></a>
s;
    }
    $button = $button . <<<s
<a class="li" href="{$currURI}&page={$nextpage}"><span class="fa fa-angle-right fa-fw" style="color: #3399FF"></span></a>
s;
    $button = $button . <<<s
<a class="li" href="{$currURI}&page={$pageCount}"><span class="fa fa-angle-double-right fa-fw" style="color: #3399FF"></span></a>
s;
    $button = $button . "</div></div>";
    array_push($probList, $button);

    
} else {
    array_push($probList, '<div class="li">题目未找到</div>');
}
$aside = '<aside>';
$aside .= '<div class="ul table"><div class="frame table">';
$aside .= '<div class="li">搜索</div>';
$aside .= '<div class="li">' . '<p><div class="textframe" style="display:inline-block; width: 60%;"><input style="width: 100%; min-height: 35px;" type="text" name="wd" value="' . $wd . '"></div>' . ' <button class="blue" id="search">搜索</button>' . '</p></div>';
$aside .= <<<script
<script>
$("#search").click(function(){
    window.location.replace("{$currURI_nw}&wd="+encodeURIComponent($("input[name=wd]").val()));
});
$("input").keydown(function(event){
  if(event.keyCode == 13){
    $("#search").trigger("click");
  }
});
</script>
script;
$aside .= '</div></div>';

include 'source.php';
$aside .= $GLOBALS['prob_source'];
$aside .= '</aside>';
array_push($probList, $aside);
showProblemList($probList);
?>

<?php include "footer.php";?>
