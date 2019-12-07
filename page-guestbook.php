<?php
get_header();
get_template_part('user-top');
global $user, $paged, $comment_count, $wpdb;
$user_views = get_user_meta($user->ID, 'views', true);
update_user_meta($user->ID, 'views', $user_views+1);

if(isset($_POST['guestbook_content'])){
	
	//删除非法HTML标签
	$guestbook_content = strip_tags($_POST['guestbook_content']);
	
	$commenter_id = $current_user->ID;
	if(isset($_POST['is_sys_user']) && $_POST['is_sys_user'] == 1){
		$commenter_id = $wpdb->get_var('SELECT * FROM wp_sys_user ORDER BY RAND() LIMIT 1');
	}
	
	if(!($_POST['guestbook_content'] == '')){
		$sql = 'insert into wp_guestbook values (NULL, '.$user->ID.', '.$commenter_id.', '.$_POST['parent_id'].', "'.$guestbook_content.'", 0, '.time().')';
		$is_sql = $wpdb->query($sql);
		if($is_sql){
			$success_message = '提交成功！';
		}
	}else{
		$error_message = '留言内容不能为空！';
	}
}

//查询留言
if($paged == 0){
	$paged = 1;
}
$break_page = 20;
$guestbook = $wpdb->get_results('select * from wp_guestbook where user_id = '.$user->ID.' order by publish_date desc limit '.($paged-1)*$break_page.','.$break_page);
$guestbook_count = $wpdb->get_var('select count(*) from wp_guestbook where user_id = '.$user->ID);
$max_page = ceil($guestbook_count/$break_page);

?>

<div class="container">
	<div class="row">
		<div class="col-md-8">
		<div class="wt-container">
			<div class="wt-nav-tabs">
				<h2 class="pull-left"><i class="fa fa-envelope"></i> 留言板（<?php echo $guestbook_count; ?>）</h2>
			</div>
			<?php if(!empty($guestbook)){ ?>
			<div class="wt-comments-list">
			<?php foreach($guestbook as $key => $guestbook_item){ ?>
			<div class="wt-comments-item" id="guestbook-<?php echo $guestbook_item->id; ?>">
				<div class="wt-avatar pull-left"><?php echo get_avatar($guestbook_item->commenter_id); ?></div>
				<div class="wt-comments-text">
					<div class="wt-comments-info">
						<span class="wt-info-model"><a href="<?php echo get_author_posts_url($guestbook_item->commenter_id); ?>" target="_blank"><?php echo get_user_by('id', $guestbook_item->commenter_id)->data->display_name; ?></a></span>
						<?php if(!($guestbook_item->parent_id == 0)){ ?>
						<span class="wt-info-model">回复 <a href="#guestbook-<?php echo $guestbook_item->parent_id; ?>">
						<?php
						$parent_user_id = $wpdb->get_var('select commenter_id from wp_guestbook where id = '.$guestbook_item->parent_id);
						echo get_user_by('id', $parent_user_id)->data->display_name;
						?>
						</a></span>
						<?php } ?>
						<span class="wt-info-model"><?php echo date('Y-m-d H:i:s', $guestbook_item->publish_date); ?></span>
						<span class="wt-comment_floor pull-right"><?php echo $guestbook_count-$key; ?> 楼</span>
					</div>
					<div class="wt-content wt-comment-content"><?php echo wpautop($guestbook_item->content); ?></div>
					<div class="wt-comments-tools">
						<span class="wt-info-model"><i class="fa fa-thumbs-o-up"></i> 0</span>
						<span class="wt-info-model"><i class="fa fa-thumbs-o-down"></i> 0</span>
						<span class="wt-info-model"><a href="javascript:" class="reply_btn" this_comment_id="<?php echo $guestbook_item->id; ?>"><i class="fa fa-level-up"></i> 回复</a></span>
					</div>
				</div>
			</div>
			<?php } ?>
			</div>
			<ul class="pagenavi" style="border-top:1px solid #EEE;">
				<?php pagenavi($p = 5, $max_page = $max_page);?>
			</ul>
			<?php }else{ ?>
			<div class="wp-info wp-info-warning">
				<div class="wp-info-icon"><i class="fa fa-info"></i></div>
				<h3>还没有收到任何留言！</h3>
				<p>还没有收到其他用户的留言，你可以成为第一个。</p>
			</div>
			<?php } ?>
		</div>
<div class="wt-container wt-pre-model wt-comment-form">
<?php if(is_user_logged_in()): global $current_user; get_currentuserinfo(); ?>
<h3 class="wt-comments-title">我要留言</h3>
<div class="wt-comments-item" id="comment_form">
	<form action="" method="post">
	<div class="wt-avatar pull-left"><?php echo get_avatar($current_user->ID); ?></div>
	<div class="wt-comments-text">
		<div class="wt-textarea"><textarea rows="4" name="guestbook_content" placeholder="你的留言，随便说点什么吧！" class="form-control"></textarea></div>
		<div class="wt-comments-submit">
			<button type="submit" class="btn btn-primary">我要留言</button>
			<input name="parent_id" id="comment_parent" value="0" type="hidden">
			<?php if($current_user->ID == 1){ ?>
			<span class="is_sys_user"><label for="is_sys_user"><input type="checkbox" id="is_sys_user" value="1" name="is_sys_user" /> 伪装成随机用户评论文章</label></span>
			<?php } ?>
		</div>
	</div>
	</form>
</div>
<?php else: ?>
<div class="wp-info wp-info-warning">
	<div class="wp-info-icon"><i class="fa fa-info"></i></div>
	<h3>您暂时不能留言！</h3>
	<p>管理员开启了需要登录才能够留言，你可以免费注册一个本站的账号。</p>
</div>
<?php endif; ?>
		</div>
		</div>
		<div class="col-md-4">
			<div class="page-side-count">
				<div class="wt-container">
					<div class="author-tatol-item">
					<span class="item-num"><?php echo count_user_posts($user->ID, 'post'); ?></span>
					<span class="item-name">文章</span>
					</div>
					<div class="author-tatol-item">
					<span class="item-num"><?php echo $comment_count; ?></span>
					<span class="item-name">评论</span>
					</div>
					<div class="author-tatol-item">
					<span class="item-num"><?php echo $user_views+1 ?></span>
					<span class="item-name">人气</span>
					</div>
				</div>
			</div>
			<div class="list-group">
				<a href="" class="list-group-item">我的收藏<span class="badge">1</span></a>
				<a href="" class="list-group-item">我的点赞</a>
			</div>
		</div>
	</div>
</div>
<?php
get_footer();
?>