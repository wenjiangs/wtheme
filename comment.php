<?php
global $this_post_id;
if(comments_open()){
?>
<div class="wt-container wt-pre-model wt-comment-form">
<?php
if(is_user_logged_in()){
global $current_user;
get_currentuserinfo();
?>
<h3 class="wt-comments-title">发布评论</h3>
<div class="wt-comments-item" id="comment_form">
	<form action="<?php echo bloginfo('url'); ?>/wp-comments-post.php" method="post">
	<div class="wt-avatar pull-left"><?php echo get_avatar($current_user->ID); ?></div>
	<div class="wt-comments-text">
		<div class="wt-textarea"><textarea rows="4" name="comment" placeholder="你的评论，随便说点什么吧！" class="form-control"></textarea></div>
		<div class="wt-comments-submit">
			<button type="submit" class="btn btn-primary">发布评论</button>
			<input name="comment_post_ID" value="<?php echo $this_post_id; ?>" id="comment_post_ID" type="hidden">
			<input name="comment_parent" id="comment_parent" value="0" type="hidden">
			<?php if($current_user->ID == 1){ ?>
			<span class="is_sys_user"><label for="is_sys_user"><input type="checkbox" id="is_sys_user" value="1" name="is_sys_user" /> 伪装成随机用户评论文章</label></span>
			<?php } ?>
		</div>
	</div>
	</form>
</div>
<?php }else{ ?>
<?php if(get_option('comment_registration')){ //只有登录以后才能评论 ?>
<div class="wp-info wp-info-warning">
	<div class="wp-info-icon"><i class="fa fa-info"></i></div>
	<h3>您暂时不能评论！</h3>
	<p>管理员开启了需要登录才能够评论，你可以免费注册一个本站的账号。</p>
</div>
<?php }else{ ?>
<h3 class="wt-comments-title">发布评论</h3>
<div class="wt-comments-item" id="comment_form">
	<form action="<?php echo bloginfo('url'); ?>/wp-comments-post.php" method="post">
	<div class="row wt-other-input">
	<div class="col-md-4"><input class="form-control" placeholder="你的昵称（必填）" name="author"/></div>
	<div class="col-md-4"><input class="form-control" placeholder="你的邮箱，不会被公开（必填）" name="email"/></div>
	<div class="col-md-4"><input class="form-control" placeholder="你的个人站点" name="url"/></div>
	</div>
	<div class="wt-textarea"><textarea rows="4" name="comment" placeholder="你的评论，随便说点什么吧！" class="form-control"></textarea></div>
		<div class="wt-comments-submit">
			<button type="submit" class="btn btn-primary">发布评论</button>
			<input name="comment_post_ID" value="<?php echo $this_post_id; ?>" id="comment_post_ID" type="hidden">
			<input name="comment_parent" id="comment_parent" value="0" type="hidden">
		</div>
	</form>
</div>
<?php } ?>
<?php } ?>
</div>

<?php
if(get_comments_number()){
$comments = get_comments(array('post_id'=>$this_post_id));
	?>
	<div class="wt-container wt-pre-model">
	<div class="wt-comments">
		<h3 class="wt-comments-title">评论（<?php echo $comment_number = get_comments_number(); ?>）</h3>
		<div class="wt-comments-list">
		<?php foreach($comments as $key => $comments_item){ ?>
			<div class="wt-comments-item" id="comment-<?php echo $comments_item->comment_ID; ?>">
				<div class="wt-avatar pull-left"><?php echo get_avatar($comments_item->user_id); ?></div>
				<div class="wt-comments-text">
					<?php if($comments_item->comment_approved == 0){ ?>
					<div class="alert alert-warning">您的评论正在等待管理员审核！</div>
					<?php } ?>
					<div class="wt-comments-info">
						<?php if($comments_item->user_id){ ?>
						<span class="wt-info-model <?php if($comments_item->user_id == 1) echo 'is_single_admin'; ?>">
						<a href="<?php echo get_author_posts_url($comments_item->user_id); ?>" target="_blank"><?php echo $comments_item->comment_author; ?></a>
						<?php if($comments_item->user_id == 1){ ?>
						<span class="is_single_admin_tag">站长</span>
						<?php } ?></span>
						<?php }else{ ?>
						<span class="wt-info-model"><?php echo $comments_item->comment_author; ?></span>
						<?php } ?>
						<?php
						if($comments_item->comment_parent){
							foreach($comments as $comments_item2){
								if($comments_item->comment_parent == $comments_item2->comment_ID){
						?>
							<span class="wt-info-model"><a class="wt-comment-has-parent" href="#comment-<?php echo $comments_item->comment_parent; ?>">回复 <?php echo $comments_item2->comment_author; ?></a></span>
						<?php } } } ?>
						<span class="wt-info-model"><?php echo $comments_item->comment_date; ?></span>
						<span class="wt-comment_floor pull-right"><?php echo $comment_number - $key; ?> 楼</span>
					</div>
					<div class="wt-content wt-comment-content"><?php echo wpautop($comments_item->comment_content); ?></div>
					<div class="wt-comments-tools">
						<span class="wt-info-model"><i class="fa fa-thumbs-o-up"></i> 0</span>
						<span class="wt-info-model"><i class="fa fa-thumbs-o-down"></i> 0</span>
						<span class="wt-info-model"><a href="javascript:" class="reply_btn" this_comment_id="<?php echo $comments_item->comment_ID; ?>"><i class="fa fa-level-up"></i> 回复</a></span>
					</div>
				</div>
			</div>
		<?php } ?>
		</div>
	</div>
	</div>
<?php }else{ ?>
<div class="wt-container wt-pre-model">
	<div class="wp-info wp-info-default">
		<div class="wp-info-icon"><i class="fa fa-info"></i></div>
		<h3>还没有评论！</h3>
		<p>目前还没有任何评论，快来抢沙发吧！</p>
	</div>
</div>
	<?php } ?>
<?php }else{ ?>
<div class="wt-container wt-pre-model">
	<div class="wp-info wp-info-default">
		<div class="wp-info-icon"><i class="fa fa-info"></i></div>
		<h3>评论被关闭！</h3>
		<p>这篇文章的评论被关闭了，可能是管理员为了系统安全，亦或者是年代久远，评论已没有什么意义，如果你特别想要说点什么，请联系管理员！</p>
	</div>
</div>
<?php } ?>