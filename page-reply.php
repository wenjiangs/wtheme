<?php
if($paged == 0){
	$paged = 1;
}

$break_page = 10;
$args = array(
	'number'		=> $break_page,
	'offset'		=> ($paged-1)*$break_page,
	'user_id'		=> $current_user->ID,
	'post_type'		=> 'topic',
	'status'		=> 'approve'
);
$comment_args = array(
	'user_id' 		=> $current_user->ID, // use post_id, not post_ID
	'post_type'		=> 'topic',
	'count' 		=> true //return only the count
);
if(isset($_GET['type']) && $_GET['type'] == 'pending'){
	$args['status'] = 'hold';
}

$comment_count = get_comments($comment_args);
$comment_args['status'] = 'hold';
$comment_count_hold = get_comments($comment_args);
$comment_obj = get_comments($args);
$max_page = ceil($comment_count/$break_page);

get_header();
?>
<div class="container">
<div class="row">
<?php get_sidebar('user'); ?>
<div class="col-md-9">
<div class="wt-container">
	<h1 class="wt-setting-tit">我的回复</h1>
	<div class="wt-nav-tabs">
		<ul class="nav nav-tabs">
		  <li <?php if(!isset($_GET['type'])){ echo 'class="active"';} ?>><a href="/user/comment">已发布 <?php echo $comment_count; ?></a></li>
		  <li <?php if(isset($_GET['type']) && $_GET['type'] == 'pending'){ echo 'class="active"';} ?>><a href="/user/comment?type=pending">等待审核 <?php echo $comment_count_hold; ?></a></li>
		</ul>
	</div>
<div>
	<?php if(empty($comment_obj)){ ?>
	<div class="wp-info wp-info-default">
		<div class="wp-info-icon"><i class="fa fa-info"></i></div>
		<h3>还没有回复！</h3>
		<p>你在本站还没有对任何话题进行回复，赶紧去逛逛吧。</p>
	</div>
	<?php }else{?>
	<?php foreach($comment_obj as $comment_single){ $post_single = get_post($comment_single->comment_post_ID); ?>
	
	<div class="topic-item">
		<div class="topic-item-head">
			<h3><a href="<?php echo get_permalink($post_single->ID);?>"><?php echo $post_single->post_title; ?></a></h3>
			<p>
			<span class="wt-info-model">
			<a href="<?php echo get_author_posts_url($comment_single->user_id); ?>" target="_blank"><?php echo get_avatar($comment_single->user_id); ?></a>
			<a href="<?php echo get_author_posts_url($comment_single->user_id); ?>" target="_blank"><?php echo get_user_by('id', $comment_single->user_id)->data->display_name; ?></a>
			</span>
			<span class="wt-info-model"><?php echo date('Y-m-d', strtotime($post_single->post_date)); ?></span>
			<span class="wt-info-model"><i class="fa fa-thumbs-o-up"></i> 赞 0</span>
			<span class="wt-info-model"><i class="fa fa-thumbs-o-down"></i> 踩 0</span>
			<span class="wt-info-model"><a href="javascript:" class="reply_btn" this_comment_id="<?php echo $guestbook_item->id; ?>"><i class="fa fa-level-up"></i> 回复</a></span>
			</p>
		</div>
		<div class="topic-item-content"><?php echo wpautop($comment_single->comment_content); ?></div>
	</div>
	<?php } ?>
	<?php } ?>
</div>
<ul class="pagenavi">
	<?php pagenavi(5, $max_page);?>
</ul>
</div>
</div>
</div>
</div>
<?php
get_footer();
?>