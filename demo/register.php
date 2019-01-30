<?php include "header.php"; $GLOBALS['page_name']="注册"; showHeader();?>

<?php
global $DB_conn;
if (isset($_COOKIE['user']) && isset($_COOKIE['password'])) {
    if ($DB_conn->query("select * from user where user='{$_COOKIE['user']}' and password='{$_COOKIE['password']}'")->num_rows == 0) {
        setcookie("user", NULL);
        setcookie("password", NULL);
    } else {
        header("Location: ./");
    }
}
if (!empty($_POST['username']) && !empty($_POST['nickname']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password2'])) {
    if ($DB_conn->query("select * from user where user='{$_COOKIE['user']}'")->num_rows != 0) {
        $error_message = "用户名已存在";
    }
    if ($_POST['password'] != $_POST['password2']) {
        $error_message = "两次输入的密码不一致";
    }
    $username = $_POST['username'];
    $nickname = $_POST['nickname'];
    $email    = $_POST['email'];
    $password = md5(md5(md5($_POST['password'].'233')));
    $DB_conn->query("insert into user (user,password,nickname,email) values ('{$username}','{$password}','{$nickname}','{$email}')");
    header("Location: ./login.php");
}
if(!isset($error_message))
    $error_message = "";
$self = htmlspecialchars($_SERVER["PHP_SELF"]);
$form =
<<<EOF
	<form method="post" action={$self} name=register>
        {$error_message}
		<p><div class="textframe"><input type="text" name="username" placeholder="用户名"></div></p>
		<p><div class="textframe"><input type="text" name="nickname" placeholder="昵称"></div></p>
        <p><div class="textframe"><input type="text" name="email" placeholder="邮箱"></div></p>
		<p><div class="textframe"><input type="password" name="password" placeholder="密码"></div></p>
		<p><div class="textframe"><input type="password" name="password2" placeholder="确认密码"></div></p>
	</form>
    <p style="width: 40%">
        <a onclick="register.submit();"><button class="blue" style="float: left; width: 100%;" id="submit">注册</button></a>
    </p>
<script>
$("input").keydown(function(event){
  if(event.keyCode == 13){
    $("#submit").trigger("click");
  }
});
</script>
EOF;
showBlock($form);
?>
	
<?php include "footer.php";?>
