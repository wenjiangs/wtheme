<!doctype html>
<html lang="en">
<head>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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

<!--vendors styles-->
<link rel="stylesheet" href="/wp-content/themes/wtheme/website-development/css/font-awesome.min.css">
<link rel="stylesheet" href="/wp-content/themes/wtheme/website-development/fancybox/jquery.fancybox-1.3.4.css">

<!-- Bootstrap CSS / Color Scheme -->
<link rel="stylesheet" href="/wp-content/themes/wtheme/website-development/css/default.css" id="theme-color">
</head>
<body>

<!--navigation-->
<nav class="navbar navbar-shadow navbar-expand-md navbar-light bg-white fixed-top sticky-navigation">
	<a class="navbar-brand" href="/website-development">
		<img src="/wp-content/themes/wtheme/images/logo.svg">
	</a>
	<button class="navbar-toggler navbar-toggler-right border-0" type="button" data-toggle="collapse" 
			data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
		<span data-feather="grid"></span>
	</button>
	<div class="collapse navbar-collapse" id="navbarCollapse">
		<ul class="navbar-nav ml-auto">
			<li class="nav-item">
				<a class="nav-link page-scroll" href="/website-development">首页</a>
			</li>
			<li class="nav-item">
				<a class="nav-link page-scroll" href="/about">关于我</a>
			</li>
			<li class="nav-item">
				<a class="nav-link page-scroll" href="/website-development#services">服务项目</a>
			</li>
			<li class="nav-item">
				<a class="nav-link page-scroll" href="/website-development#process">服务流程</a>
			</li>
			<li class="nav-item">
				<a class="nav-link page-scroll" href="/myworks">案例展示</a>
			</li>
			<li class="nav-item">
				<a class="nav-link page-scroll" href="/website-development#contact">联系我们</a>
			</li>
		</ul>
	</div>
</nav>