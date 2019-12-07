<?php
session_start();
$sid = session_id();
$uuid = $sid.ceil(time()/300);

$memcache = new Memcache;
$memcache->connect('127.0.0.1') or die (json_encode(array(
  'success' => false,
  'message' => '',
  'data' => 'Memcache connot connect',
  'code' => ''
)));

// 输出函数
function echoES($arr){
  echo "data: ".json_encode($arr)."\n\n";
}

if(!isset($_GET['ajax'])){
  $unToken = $memcache->get($uuid);
  if($unToken==false){
    $unToken = array(
      'uuid' => $uuid,
      'isLogin' => false,
      'isScan'  => false
    );
    // 有效期5分钟
    $memcache->set($uuid, $unToken, 0, 300);
    $qrcontent = array(
      'type' => 'scaningLogin',
      'content' => $uuid,
    );
  }
}

if(isset($_GET['ajax'])){

  $cacheUuid = $memcache->get($uuid);

  header('Content-Type: text/event-stream');
  header('Cache-Control: no-cache');

  if($cacheUuid){
    if($cacheUuid['isLogin']){
      
      // 登录
      $user_id = $cacheUuid['user_id'];
      $user_login = $cacheUuid['user_login'];
      
      wp_set_current_user($cacheUuid['user_id'], $cacheUuid['user_login']);
      if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
        wp_set_auth_cookie($user_id, true, true);
      }else{
        wp_set_auth_cookie($user_id);
      }
      do_action('wp_login', $user_login);
      
      // 登录成功清除缓存
      $memcache->delete($uuid);
      
      echoES(array(
        'code' => '',
        'success' => true,
        'message' => '',
        'data' => '',
      ));
    }else if($cacheUuid['isScan']){
      echoES(array(
        'code' => 2,
        'success' => false,
        'message' => '已扫码',
        'data' => '',
      ));
    }
  }else{
    echoES(array(
      'code' => 1,
      'success' => false,
      'message' => '二维码过期',
      'data' => '',
    ));
  }
  ob_flush();
  flush();
  exit;
}

get_header();
?>

<div class="slBox">
  <div class="qrcodeBox">
    <div id="qrcode"></div>
    <div class="expireInfo"><div class="expireInfo2">二维码已过期<br>请刷新网页</div></div>
  </div>
  <div class="slInfo">使用微信小程序或者文江博客APP扫码登录</div>
</div>

<script>
// 设置参数方式
var qrcode = new QRCode('qrcode', {
  text: '<?php echo json_encode($qrcontent); ?>',
  width: 200,
  height: 200,
  colorDark : '#000000',
  colorLight : '#ffffff',
  correctLevel : QRCode.CorrectLevel.H
});

// 使用 API
// qrcode.clear();
// qrcode.makeCode('new content');

//创建EventSource对象
var es = new EventSource("/scanning-login?ajax=1");
//接收到消息的回调函数
es.onmessage = function(res) {
  var data = JSON.parse(res.data);
  console.log(data);
  if(data.success){
    location.href = '/';
  }else{
    if(data.code == 1){
      $('.expireInfo').css('visibility', 'visible');
    }else if(data.code == 2){
      $('.expireInfo').css('visibility', 'visible').find('.expireInfo2').html('已扫码<br>请在手机上确认');
    }
  }
};

</script>

<?php get_footer(); ?>