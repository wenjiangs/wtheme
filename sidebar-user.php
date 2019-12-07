<?php
if(is_user_logged_in()){
	global $current_user;
	$comment_args = array(
		'post_author' => $current_user->ID, // use post_id, not post_ID
		'count' => true //return only the count
	);
	$comment_count = get_comments($comment_args);
}
?>

<div class="col-md-3 ucenter_side">
	<div class="store_side_user">
		<div class="store_side_avatar"><?php echo get_avatar($current_user->ID); ?></div>
		<h3 class="store_side_user_name">
		<?php if(is_user_logged_in()){ global $current_user; ?>
		<a href="/user"><?php echo $current_user->data->display_name; ?></a>
		<?php }else{ ?>
		请登录
		<?php } ?>
		</h3>
		<ul class="store_side_integral">
			<li><?php echo count_user_posts($current_user->ID, 'post'); ?><span>文章</span></li>
			<li><?php echo $comment_count; ?><span>评论</span></li>
		</ul>
		<h3 class="ucenter_side_tit">文章管理</h3>
		<ul class="store_side_user_nav">
			<li><a href="/user/articles">文章管理</a></li>
			<li><a href="/user/comment">评论管理</a></li>
			<li><a href="/user/topic">话题管理</a></li>
			<li><a href="/user/reply">回复管理</a></li>
		</ul>
		<h3 class="ucenter_side_tit">账户设置</h3>
		<ul class="store_side_user_nav">
			<li><a href="/setting/avatar">头像设置</a></li>
			<li><a href="/setting">基本资料</a></li>
			<li><a href="/setting/security">密码管理</a></li>
			<li><a href="/setting/account">账号管理</a></li>
			<li><a href="/setting/account-record">登录历史</a></li>
		</ul>
	</div>
</div>