<?php include "header.php"; $GLOBALS['page_name'] = "首页"; showHeader();?>

<?php
echo <<<s
<main>
      <article class="index">
        <div class="ul list">
          <div class="frame list">
            <div class="li">
              <b class="title">公告:</b>
            <div class="ul list">
            <div class="frame list">
              <div class="li">#1 <b>提交界面会自动刷新，无需再手动刷新</b></div>
		      <div class="li">#2 上传头像请访问 <b><a href="https://cn.gravatar.com/">gravatar</a></b></div>
            </div>
            </div>
            </div>
            
        </div>
      </div>
</article>
</main>
s;
?>

<?php include "footer.php";?>
