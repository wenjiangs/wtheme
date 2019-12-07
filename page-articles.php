<?php
$break_page = 20;
if($paged == 0){
	$paged = 1;
}

$post_arr = array(
	'numberposts'	=> $break_page,
	'author'		=> $current_user->ID,
	'offset'		=> ($paged-1)*$break_page,
	'post_author'	=> $current_user->ID
);

if(isset($_GET['status'])){
	$post_arr['post_status'] = $_GET['status'];
}

$posts = get_posts($post_arr);
$count_posts = count_user_posts($current_user->ID, 'post');
$max_page = ceil($count_posts/$break_page);

//文章状态统计
$count_pending = $wpdb->get_var('select count(*) from wp_posts where post_type = "post" and post_status = "pending" and post_author = '.$current_user->ID);
$count_draft = $wpdb->get_var('select count(*) from wp_posts where post_type = "post" and post_status = "draft" and post_author = '.$current_user->ID);
$count_trash = $wpdb->get_var('select count(*) from wp_posts where post_type = "post" and post_status = "trash" and post_author = '.$current_user->ID);
$count_private = $wpdb->get_var('select count(*) from wp_posts where post_type = "post" and post_status = "private" and post_author = '.$current_user->ID);

get_header();
?>
<div class="container">
<div class="row">
<?php get_sidebar('user'); ?>
<div class="col-md-9">
<div class="wt-container" style="overflow:auto;">
	<h1 class="wt-setting-tit">我的文章 <a href="/public" class="btn btn-success btn-xs">写文章</a></h1>
	<ul class="nav nav-tabs wt-nav-tabs nav-tabs-bottom">
		<li <?php if(!isset($_GET['status'])) echo 'class="active"'; ?>><a href="/user/articles">已发布 <?php echo $count_posts; ?></a></li>
		<li <?php if(isset($_GET['status']) && $_GET['status'] == "pending") echo 'class="active"'; ?>><a href="/user/articles?status=pending">待审核 <?php echo $count_pending; ?></a></li>
		<li <?php if(isset($_GET['status']) && $_GET['status'] == "draft") echo 'class="active"'; ?>><a href="/user/articles?status=draft">草稿箱 <?php echo $count_draft; ?></a></li>
		<li <?php if(isset($_GET['status']) && $_GET['status'] == "trash") echo 'class="active"'; ?>><a href="/user/articles?status=trash">回收站 <?php echo $count_trash; ?></a></li>
		<li <?php if(isset($_GET['status']) && $_GET['status'] == "private") echo 'class="active"'; ?>><a href="/user/articles?status=private"><i class="fa fa-lock"></i> 私密文章 <?php echo $count_private; ?></a></li>
	</ul>
	<?php if(empty($posts)){ ?>
	<div class="wp-info wp-info-warning">
		<div class="wp-info-icon"><i class="fa fa-info"></i></div>
		<h3>没有找到任何内容！</h3>
		<p>您查找的内容可能被删除或者更换了名字，亦或者是您手残输错误，当然不排除阁下人品问题，也有可能是电信网通那头接口生锈了。</p>
	</div>
	<?php }else{ ?>
	<table class="article_table">
		<?php foreach($posts as $posts_item){ ?>
		<tr>
			<td><a href="<?php echo get_permalink($posts_item->ID); ?>"><?php echo $posts_item->post_title; ?></a></td>
			<td><?php echo date('Y-m-d', strtotime($posts_item->post_date)); ?> (<?php echo get_post_meta($posts_item->ID, 'views', true); ?>/<?php echo $posts_item->comment_count; ?>)</td>
			<td align="right" width="100"><div class="dropdown post_dropdown">
			<a href="javascript:" id="post_dropdown_<?php echo $posts_item->ID; ?>" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">编辑 <i class="fa fa-caret-down"></i></a>
			<ul class="dropdown-menu" aria-labelledby="post_dropdown_<?php echo $posts_item->ID; ?>">
				<li><a href="/publish?id=<?php echo $posts_item->ID; ?>">编辑</a></li>
				<li><a href="/user/articles?do=trash&id=<?php echo $posts_item->ID; ?>">移到回收站</a></li>
				<li><a href="/user/articles?do=private&id=<?php echo $posts_item->ID; ?>">转为私密文章</a></li>
			</ul>
			</td>
		</tr>
		<?php } ?>
	</table>
	<ul class="pagenavi">
		<?php pagenavi(5, $max_page);?>
	</ul>
	<?php } ?>
</div>
</div>
</div>
</div>
<?php get_footer();?>