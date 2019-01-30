<?php include "header.php"; $GLOBALS['page_name']="记录"; showHeader();?>

<?php
if (isset($_GET['id'])) {
    global $DB_conn;
    $status = $DB_conn->query("select * from submit where submitid='{$_GET['id']}'");
    if ($status->num_rows == 0) {
        showBlock('记录未找到');
        include "footer.php";
        exit(0);
    }
    $result = $status->fetch_assoc();
    $lists = array();
    if ($result['status'] == -1 || $result['status'] == 8) {
        $content = <<<EOF
<script>
function refreshPage() {
    window.location.reload();
}
setTimeout('refreshPage()',1000);
</script>
EOF;
    } else {
        $content = "";
    }
    $content .= "<p>" . status_color($result['status']) . "</p>";
    array_push($lists, $content);
    $testpoints = json_decode($result['info'], true)['testpoints'];
    $id = 1;
    if ($result['status'] != "4" && is_array($testpoints)) {
        foreach ($testpoints as $i) {
            array_push($lists, '<div style="display:inline-block;min-width:35px;">#' . $id ++ . "</div> <div style=\"display:inline-block;min-width:200px;\"><font style=\"font-size: 16px;\">" . status_color($i['status'], "16") . "</div><div style=\"display:inline-block;min-width:80px;\">" . $i['time'] . "ms</div><div style=\"display:inline-block;min-width:80px;\">" . $i['memory'] . "KB</div>" . "</font>");
        }
    }

    if (! empty(json_decode($result['info'], true)['compileinfo']))
        array_push($lists, "<div style=\"display: inline-block;\"><h3>编译信息</h3><pre>" . htmlspecialchars(base64_decode(json_decode($result['info'], true)['compileinfo'])) . "</pre></div>");

    $code = "<h3>代码</h3><pre><code class=\"language-cpp\">" . htmlspecialchars(base64_decode($result['code'])) . "</code></pre>";
    array_push($lists, $code);

    $record_info = "";
    $record_info .= "<h3>信息</h3>";
    $record_info .= '<div style="font-size:17px;">' . '<div class="listLeft">ID</div>' . $result['submitid'] . '</div>';
    $record_info .= '<div style="font-size:17px;">' . '<div class="listLeft">题目</div>' . '<a href="./problem.php?id=' . get_problem($result['problem'])['problem'] . '">' . (get_problem($result['problem'])['oj'] == 'oj' ? 'P' : '') . get_problem($result['problem'])['problem'] . " " . get_problem($result['problem'])['name'] . '</a>' . '</div>';
    $record_info .= '<div style="font-size:17px;">' . '<div class="listLeft">提交者</div><a href="./userinfo.php?id=' . get_user($result['user'])['user'] . '">' . get_user($result['user'])['nickname'] . '</a>' . '</div>';
    $record_info .= '<div style="font-size:17px;">' . '<div class="listLeft">提交时间</div>' . $result['submittime'] . '</div>';
    $record_info .= '<div style="font-size:17px;">' . '<div class="listLeft">语言</div>' . $result['language'] . '</div>';
    $record_info .= '<div style="font-size:17px;">' . '<div class="listLeft">时间</div>' . $result['time'] . 'ms</div>';
    $record_info .= '<div style="font-size:17px;">' . '<div class="listLeft">内存</div>' . (float) $result['memory'] . ' KB</div>';

    $aside = <<<EOF
<aside><div class="ul table"><div class="frame table"><div class="li">
{$record_info}
</div></div></aside>
EOF;
    array_push($lists, $aside);

    showProblemList($lists, "60%");
}
?>
<?php include "footer.php";?>