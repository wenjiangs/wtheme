<?php
if(is_user_logged_in()){
	global $current_user;
	get_currentuserinfo();
}else{
	header('location:/');
	exit;
}

if(isset($_FILES['upload_file'])){
	$file = $_FILES['upload_file'];
	foreach($file['name'] as $key => $file_name_item){
		$tmpname = $file['tmp_name'][$key];
		if(is_uploaded_file($tmpname)){
			if($file['error'][$key] == 0){
				if($file['type'][$key] == "image/jpeg" || $file['type'][$key] == "image/jpg" || $file['type'][$key] == "image/png" || $file['type'][$key] == "image/gif"){
					if($file['size'][$key] < 1024*1024){
						
						$upload_dir = wp_upload_dir();
						$file_dir = $upload_dir['path'];
						
						//获取文件名和文件扩展名
						$file_info = pathinfo($file["name"][$key]);
						$temp_name = date('YmdHis').rand(10, 99);
						//移动文件
						$move = move_uploaded_file($tmpname, $file_dir.'/'.$temp_name.'.'.$file_info['extension']);
						if($move){
							//$suc = array('code' => 1, 'msg' => '/wp-content/uploads/temp/'.$temp_name.'.'.$file_info['extension']);
							//更新数据库
							$post_arr = array(
								'post_title'		=> $file_info["filename"],
								'post_mime_type' 	=> $file['type'][$key],
								'guid'				=> $upload_dir['url'].'/'.$temp_name.'.'.$file_info['extension'],
								'post_type'			=> 'attachment',
								'post_status'		=> 'inherit',
							);
							wp_insert_post($post_arr);
						}else{
							$err = array('code' => 0, 'msg' => '上传失败！');
						}
						
					}else{
						$err = array('code' => 0, 'msg' => '文件太大！');
					}
				}else{
					$err = array('code' => 0, 'msg' => '文件格式错误！');
				}
			}else{
				$err = array('code' => 0, 'msg' => '文件上传失败！');
			}
		}
	}
	header('location:/user/media-zone');
}
?>
<!Doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php
global $custom_title;
if(isset($custom_title))
	echo $custom_title;
if(is_author()){
	global $store;
	echo $store->store_name;
}else{
	wp_title('', true, 'right');
}
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
<link href="/wp-content/themes/wtheme/css/bootstrap.min.css" rel="stylesheet">
<link href="/wp-content/themes/wtheme/css/prism.css" rel="stylesheet">
<link href="/wp-content/themes/wtheme/css/font-awesome.min.css" rel="stylesheet">
<link href="/wp-content/themes/wtheme/style.css" rel="stylesheet">
<script src="/wp-content/themes/wtheme/js/jquery.min.js"></script>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<ul class="nav nav-tabs media_tabs" role="tablist">
	<li <?php if(!isset($_GET['action'])) echo 'class="active"'; ?>><a href="/user/media-zone/">媒体管理</a></li>
	<li <?php if(isset($_GET['action'])) echo 'class="active"'; ?>><a href="/user/media-zone/?action=update">上传媒体</a></li>
	<li style="float:right; padding-right:15px;">
		<?php if(isset($_GET['sm'])){ ?>
		<button type="button" style="float:right; font-size:12px; margin:8px 15px 0 10px;" class="btn btn-info view_all">查看全部</button>
		<?php } ?>
		<div class="pull-right message_search">
			<form style="float:left;">
				<button type="submit" class="btn btn-primary message_search_btn"><i class="fa fa-search"></i> 搜索</button>
				<input type="text" <?php if(isset($_GET['sm'])) echo 'value="'.$_GET['sm'].'"';?> class="form-control order_search_text" name="sm" placeholder="输入文件标题">
			</form>
		</div>
	</li>
</ul>
<!-- Tab panes -->
<?php if(isset($_GET['action'])){ ?>
<div class="mediazone_update_box">
	<h2><i class="fa fa-cloud-upload"></i></h2>
	<div>点击下面的按钮或者拖拽文件上传</div>
	<button class="btn btn-primary btn-lg upload_file_btn">请选择文件</button>
	<form enctype="multipart/form-data" method="post" class="hide upload_file_form">
	<input type="file" name="upload_file[]" multiple="multiple" accept="image/*" class="upload_file"/>
	</form>
	<?php if(isset($err)){ ?>
	<div class="alert alert-danger" style="margin:15px auto; max-width:300px; font-size:14px;"><?php echo $err['msg']; ?></div>
	<?php } ?>
</div>
<script>
$(function(){
	$('.upload_file_btn').click(function(){
		$('.upload_file').trigger('click');
	})
	$('.upload_file').change(function(){
		$('.upload_file_form').submit();
	})
})
</script>
<?php } ?>
<?php
if(!isset($_GET['action'])){
//查询多媒体
if($paged == 0){
	$paged = 1;
}
$break_page = 30;

$sql = 'select * from wp_posts where post_author = '.$current_user->ID.' and post_mime_type <> "" ';
$total_sql = 'select count(*) from wp_posts where post_author = '.$current_user->ID.' and post_mime_type <> ""';

if(isset($_GET['sm'])){
	$sql .= ' and post_title like "%'.$_GET['sm'].'%"';
	$total_sql .= ' and post_title like "%'.$_GET['sm'].'%"';
}

$sql .= ' order by ID desc limit '.($paged-1)*$break_page.', '.$break_page;;

$mediazone = $wpdb->get_results($sql);
$total = $wpdb->get_var($total_sql);
$max_page = ceil($total/$break_page);
?>
<ul class="mediazone_list">
	<?php
  foreach($mediazone as $mediazone_item){
    $mediazone_item->src = $mediazone_item->guid;
    if($mediazone_item->post_mime_type == "application/zip" || $mediazone_item->post_mime_type == "application/rar"){
      $mediazone_item->guid = "/wp-includes/images/media/archive.png";
    }
  ?>
	<li>
		<input type="hidden" value="<?php echo $mediazone_item->guid; ?>" name="img_url" class="img_url"/>
		<div class="mediazone_item">
			<i class="fa fa-check"></i>
			<div class="mediazone_img">
        <img src="<?php echo $mediazone_item->guid; ?>"
          type="<?php echo $mediazone_item->post_mime_type; ?>"
          srcReal="<?php echo $mediazone_item->src; ?>"
          title="<?php echo $mediazone_item->post_title; ?>"
        >
      </div>
			<h3><span><?php echo $mediazone_item->post_title; ?></span></h3>
		</div>
	</li>
	<?php } ?>
</ul>
<ul class="pagenavi">
	<?php pagenavi($p=5, $max_page); ?>
</ul>
<script>
$(function(){
	$('.mediazone_list li').click(function(){
		$(this).toggleClass('checked');
	})
	$('.view_all').click(function(){
		location.href = "/user/media-zone";
	})
})
</script>
<?php } ?>
<?php wp_footer(); ?>
</body>
</html>