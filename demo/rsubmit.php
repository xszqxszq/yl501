<?php
class RJ_Vjudge {
    var $problem  = NULL;
    var $langauge = NULL;
    var $oj       = NULL;
    var $result   = NULL;
    var $source   = NULL;
    public function submit($problem, $oj, $language, $code) {
        $this->problem  = $problem;
        $this->language = $language;
        $this->oj       = $oj;
        $this->source   = base64_encode(urlencode($code));
        $oj_url         = "https://cn.vjudge.net/problem/submit";
        $oj_conn        = curl_init();
        $oj_cookie      = "_ga=GA1.2.1931470708.1546503947; _gid=GA1.2.600492251.1546503947; JSESSIONID=3EC684BD9B18EFE85395A883F51B4071; Jax.Q=xszq|7R1G50TUBSQULGHWTYCAF5B4IQOL9M; _gat=1";
        $oj_post        = "language={$this->language}&share=0&source={$this->source}&captcha=&oj={$this->oj}&probNum={$this->problem}";
        echo "<p>".$oj_post."</p>";
        curl_setopt($oj_conn, CURLOPT_URL, $oj_url);
        curl_setopt($oj_conn, CURLOPT_POST, true);
        curl_setopt($oj_conn, CURLOPT_POSTFIELDS, $oj_post);
        curl_setopt($oj_conn, CURLOPT_COOKIE, $oj_cookie);
        curl_setopt($oj_conn, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($oj_conn, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($oj_conn, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($oj_conn, CURLOPT_SSL_VERIFYHOST, false);
        $this->result = curl_exec($oj_conn);
        curl_close($oj_conn);
        return $this->result;
    }
}
if (!empty($_POST['code']) && !empty($_POST['language']) && !empty($_POST['oj'] && !empty($_POST['problem']))) {
    $rjudger  = new RJ_Vjudge;
    $code     = urlencode($_POST['code']);
    $problem  = $_POST['problem'];
    $oj       = $_POST['oj'];
    $language = $_POST['language'];
    $rjudger->submit($problem, $language, $oj, $code);
    header('Content-Type:application/json; charset=utf-8');
    exit($rjudger->result);
} else {
    header('Content-Type:application/json; charset=utf-8');
    $error = array("error" => "Bad request");
    exit(json_encode($error));
}

?>
