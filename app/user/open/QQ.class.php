<?php
class QQ {
	public static $appid  = '';
	public static $appkey = '';
	public static $scope  = "get_user_info,add_topic,add_one_blog,add_album,upload_pic,list_album,add_share,check_page_fans,do_like,get_tenpay_address,get_info,get_other_info,get_fanslist,get_idolist,add_idol";
	public static $openid = '';
	public static $url    = '';

	public static function login(){
	    $state = md5(uniqid(rand(), TRUE)); //CSRF protection
	    iPHP::set_cookie("QQ_STATE",authcode($state,'ENCODE'));
	    $login_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code"
	        . "&client_id=" . self::$appid
	        . "&redirect_uri=" . urlencode(self::$url)
	        . "&state=" .$state
	        . "&scope=".self::$scope;
	    header("Location:$login_url");
	}
	public static function callback(){
		$state	= authcode(iPHP::get_cookie("QQ_STATE"), 'DECODE');

		if($_GET['state']!=$state && empty($_GET['code'])){
			self::login();
			exit;
		}

        $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
            . "client_id=" . self::$appid. "&redirect_uri=" . urlencode(self::$url)
            . "&client_secret=" . self::$appkey. "&code=" . $_GET["code"];

        $response = self::get_url_contents($token_url);

        if (strpos($response, "callback") !== false){
			$lpos     = strpos($response, "(");
			$rpos     = strrpos($response, ")");
			$response = substr($response, $lpos + 1, $rpos - $lpos -1);
			$msg      = json_decode($response);
            isset($msg->error) && self::login();
        }
        $params = array();
        parse_str($response, $params);
		iPHP::set_cookie("QQ_ACCESS_TOKEN",authcode($params["access_token"],'ENCODE'));
        self::access_token($params["access_token"]);
	}
	public static function access_token($access_token=""){
		$access_token	= authcode(iPHP::get_cookie("QQ_ACCESS_TOKEN"), 'DECODE');
	    $graph_url = "https://graph.qq.com/oauth2.0/me?access_token=".$access_token;
	    $str  = self::get_url_contents($graph_url);
	    if (strpos($str, "callback") !== false){
	        $lpos = strpos($str, "(");
	        $rpos = strrpos($str, ")");
	        $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
	    }

	    $user = json_decode($str);
	    isset($user->error) && self::login();

		self::$openid = $user->openid;
	    iPHP::set_cookie("QQ_OPENID",authcode($user->openid,'ENCODE'));
	}
	public static function get_openid(){
		self::$openid  = authcode(iPHP::get_cookie("QQ_OPENID"), 'DECODE');
		return self::$openid;
	}
	public static function get_user_info(){
		self::$openid  = authcode(iPHP::get_cookie("QQ_OPENID"), 'DECODE');
		$access_token  = authcode(iPHP::get_cookie("QQ_ACCESS_TOKEN"), 'DECODE');
		$get_user_info = "https://graph.qq.com/user/get_user_info?"
	        . "access_token=" . $access_token
	        . "&oauth_consumer_key=" .self::$appid
	        . "&openid=" .self::$openid
	        . "&format=json";

	    $info = self::get_url_contents($get_user_info);
	    $arr = json_decode($info, true);
	    $arr['avatar']	= $arr['figureurl_2'];
	    $arr['gender']	= $arr['gender']=="??"?'1':0;
	    return $arr;
	}
	public static function cleancookie(){
		iPHP::set_cookie('QQ_ACCESS_TOKEN', '',-31536000);
		iPHP::set_cookie('QQ_OPENID', '',-31536000);
		iPHP::set_cookie('QQ_STATE', '',-31536000);
	}
	public static function get_url_contents($url){
		$result =  file_get_contents($url);
	    // $ch = curl_init();
	    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    // curl_setopt($ch, CURLOPT_URL, $url);
	    // $result =  curl_exec($ch);
	    // curl_close($ch);
	    return $result;
	}
}