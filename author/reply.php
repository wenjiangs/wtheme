<?php
$leaf = 1;
if(isset($_GET['leaf'])){
	$leaf = $_GET['leaf'];
}

$break_page = 10;
$args = array(
	'number'		=> $break_page,
	'offset'		=> ($leaf-1)*$break_page,
	'user_id'		=> $curauth->ID,
	'post_type'		=> 'topic',
	'status'		=> 'approve'
);
$comment_args = array(
	'user_id' 		=> $curauth->ID, // use post_id, not post_ID
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

?>
<?php if(empty($comment_obj)){ ?>
<div class="wp-info wp-info-default">
	<div class="wp-info-icon"><i class="fa fa-info"></i></div>
	<h3>还没有评论！</h3>
	<p>你在本站还没有对任何文章进行评论，赶紧去逛逛吧。</p>
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
		</p>
	</div>
	<div class="topic-item-content"><?php echo wpautop($comment_single->comment_content); ?></div>
</div>
<?php } ?>
<?php } ?>
<ul class="pagenavi">
	<?php author_pagenavi(5, $max_page);?>
</ul>