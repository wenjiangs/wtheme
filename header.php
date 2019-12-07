<?php
//判断哪些页面需要登录才可以访问
if(is_page(array('articles', 'user', 'comment', 'topic', 'reply', 'message', 'avatar',
'avatar', 'security', 'account', 'send', 'publish', 'read', 'new-topic', 'accusation',
'account-record', 'notiy'))){
	if(!is_user_logged_in()){
		header("Location:".wp_login_url(get_permalink()));
		exit;
	}
}
?>
<!Doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<meta content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0;" name="viewport">
<title><?php
global $custom_title;
if(isset($custom_title))
	echo $custom_title;

wp_title('', true, 'right');

if(is_single()){
	$cat_single_arr = get_the_category();
	foreach($cat_single_arr as $cat_single_item){
		echo ' - '.$cat_single_item->cat_name;
	}
}

if(!is_home()) echo ' - ';
bloginfo('name');
if($paged>0) echo ' - 第'.$paged.'页';
if(is_home()){ echo ' - '; bloginfo('description');}
?></title>
<meta name="description" content="<?php
if(is_single() || is_page()){
	if (!empty($post->post_excerpt)){
  		remove_filter('the_excerpt', 'wpautop');
    	the_excerpt();
	}else{
    	echo str_replace('"', "'", wp_trim_words($post->post_content, 120));
	}
}else if(is_category()){
	echo category_description();
}else if(is_tag()){
	echo tag_description();
}else if(is_home()){
	echo get_option('web_des');
}
?>">
<meta name="keywords" content="<?php
if(is_home()){
	echo get_option('web_key');
}else if(is_category()){
	echo get_category($cat)->name;
}else if(is_single()){
	$tags = get_the_tags();
	if(is_array($tags)){
		foreach($tags as $tag){
			echo $tag->name.',';
		}
	}
}
?>">
<link rel="shortcut icon" href="/favicon.ico">
<link href="/wp-content/themes/wtheme/css/bootstrap.min.css" rel="stylesheet">
<link href="/wp-content/themes/wtheme/css/line-number.css" rel="stylesheet">
<link href="/wp-content/themes/wtheme/css/prism.css" rel="stylesheet">
<link href="/wp-content/themes/wtheme/css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" href="/wp-content/themes/wtheme/css/nprogress.css">
<link rel="stylesheet" href="http://libs.wenjiangs.com/element-ui/theme-chalk/index.css">
<link href="//at.alicdn.com/t/font_940179_s8yi7bi0tn.css" rel="stylesheet">
<link href="/wp-content/themes/wtheme/style.css?v=8" rel="stylesheet">
<script src="/wp-content/themes/wtheme/js/jquery.min.js"></script>
<script src="/wp-content/themes/wtheme/js/bootstrap.min.js"></script>
<script src="/wp-content/themes/wtheme/js/prism.js"></script>
<script src="/wp-content/themes/wtheme/js/jquery.lazyload.js"></script>
<script src="/wp-content/themes/wtheme/js/vue.js"></script>
<script src="/wp-content/themes/wtheme/js/country-data.js"></script>
<script src="http://libs.wenjiangs.com/element-ui/index.js"></script>
<script src="/wp-content/themes/wtheme/js/nprogress.js"></script>
<script src="/wp-content/themes/wtheme/js/jquery.pjax.js"></script>
<script src="/wp-content/themes/wtheme/js/qrcode.js"></script>
<script src="http://libs.wenjiangs.com/MathJax/MathJax.js?config=TeX-AMS_HTML"></script>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<nav class="navbar navbar-default">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">菜单</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?php echo bloginfo('url'); ?>"><img src="/wp-content/themes/wtheme/images/logo.svg" /></a>
    </div>

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <?php echo get_top_menu(); ?>
      </ul>
      <form method="get" action="/search" class="navbar-form navbar-left">
			<button class="b"><i class="fa fa-search"></i></button><input class="form-control s" name="w" <?php if(isset($_GET['w'])) echo 'value="'.$_GET['w'].'"';?> placeholder="搜索关键词" />
		</form>
		<?php
		if(is_user_logged_in()){
			global $current_user;
			get_currentuserinfo();
			//更新用户最后活动的时间
			update_user_meta($current_user->ID, 'last_activity', date('Y-m-d H:i:s'));
		?>
		<ul class="nav navbar-nav navbar-right navbar-user">
		<li><a href="/notify" class="navbar-only-icon"><i class="fa fa-bell-o"></i></a></li>
		<li><a href="/message" class="navbar-only-icon"><i class="fa fa-comment-o"></i></a></li>
		<li><a href="/publish" class="navbar-only-icon" pjax="exclude"><i class="fa fa-file-text-o"></i></a></li>
		<li class="dropdown">
          <a href="javascript:" class="dropdown-toggle navbar-user-avatar" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
		  <?php echo get_avatar($current_user->ID); ?>
		  <?php echo $current_user->data->display_name;?> <i class="fa fa-angle-down"></i>
		  </a>
          <ul class="dropdown-menu navbar-dropdown">
            <li><a href="<?php echo get_author_posts_url($current_user->ID); ?>"><i class="fa fa-home"></i> 我的主页</a></li>
            <li><a href="/user"><i class="fa fa-heart"></i> 个人中心</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="/setting"><i class="fa fa-cog"></i> 个人设置</a></li>
            <?php if($current_user->ID == 1){ ?>
            <li><a href="/wp-admin/" pjax="exclude"><i class="fa fa-cog"></i> 后台管理</a></li>
            <?php } ?>
            <li role="separator" class="divider"></li>
            <li><a href="/wp-login.php?action=logout" pjax="exclude"><i class="fa fa-power-off"></i> 退出</a></li>
          </ul>
        </li>
		</ul>
		<?php }else{ ?>
      <ul class="nav navbar-nav navbar-right navbar-user-no-login">
        <li><a href="/oauth/weibo/" pjax="exclude"><i class="fa fa-weibo"></i></a></li>
        <li><a href="/oauth/qq/" pjax="exclude"><i class="fa fa-qq"></i></a></li>
        <li><a href="<?php echo wp_login_url(); ?>" pjax="exclude">登录</a></li>
      </ul>
		<?php } ?>
    </div>
  </div>
</nav>
<!-- 手机端导航 -->
<div class="mobileNav">
  <div class="mnItem">
    <a href="/">
      <i class="wjsp wjsp-shouye"></i>
      <span>首页</span>
    </a>
  </div>
  <div class="mnItem">
    <a href="/topic">
      <i class="wjsp wjsp-shequguanli"></i>
      <span>话题</span>
    </a>
  </div>
  <div class="mnItem">
    <a href="/message">
      <i class="wjsp wjsp-xiaoxi"></i>
      <span>消息</span>
    </a>
  </div>
  <div class="mnItem">
    <a href="/doc">
      <i class="wjsp wjsp-File"></i>
      <span>手册</span>
    </a>
  </div>
  <div class="mnItem">
    <a href="/user">
      <i class="wjsp wjsp-wode"></i>
      <span>我的</span>
    </a>
  </div>
</div>
<!-- 手机端导航 end -->
<div id="pjax-container" class="mainPage">