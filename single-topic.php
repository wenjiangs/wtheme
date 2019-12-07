<?php
get_header();
//获取话题的分类信息
$group = get_the_terms($post->ID, 'group');
$comment_number = get_comments_number();
?>
<div class="container">
<div class="row">
<div class="col-md-8">
<div class="wt-container">
<?php if(!empty($group)){ ?>
<div class="wt-group-top">
	<span class="wt-info-model"><img  src="/wp-content/themes/wtheme/images/placeholder.png" data-original="/wp-content/themes/wtheme/images/group-<?php echo $group[0]->term_id; ?>.png" alt="<?php echo $group[0]->name; ?>" /></span>
	<span class="wt-info-model"><a href="<?php echo get_term_link($group[0]->term_id); ?>"><?php echo $group[0]->name; ?></a></span>
	<span class="wt-info-model wt-group-top-info">主题：<?php echo $group[0]->count; ?></span>
	<span class="wt-info-model wt-group-top-info">回复：<?php echo get_group_reply_number($group[0]->term_id); ?></span>
</div>
<?php } ?>
<?php if(have_posts()){ ?>
<?php while (have_posts()){the_post();
$this_post_id = $post->ID; //记录当前postID
$this_post_author = $post->post_author; //记录当前作者ID
$this_views = get_post_meta($this_post_id, 'views', true);
if($this_views < 500){
	$rand_views = rand(300, 500);
	$this_views = $this_views + $rand_views;
}else{
	$this_views = $this_views + 1;
}
update_post_meta($this_post_id, 'views', $this_views); //更新浏览量
$word_num = mb_strwidth($post->post_content);
$comment_args = array(
	'post_id' => $post->ID, // use post_id, not post_ID
	'count' => true //return only the count
);
$comment_count = get_comments($comment_args);

//更新评论数
global $wpdb;
$this_topic_comment_count = $wpdb->get_var('select count(*) from wp_comments where comment_post_ID = '.$post->ID);
$wpdb->query('update wp_posts set comment_count = '.$this_topic_comment_count.' where ID = '.$post->ID);
?>
<h1 class="wt-single-title"><?php the_title(); ?></h1>
<div class="wt-single-meta">
	<span class="wt-info-model"><a href="<?php echo get_author_posts_url($this_post_author); ?>"><?php the_author(); ?></a> 发布于 <?php echo the_time('Y-m-d');?></span>
	<span class="wt-info-model">字数<?php echo $word_num; ?></span>
	<span class="wt-info-model">浏览 <?php echo $this_views;?></span>
	<span class="wt-info-model">回复 <?php echo $post->comment_count; ?></span>
</div>
<div class="wt-content">
<?php echo $Parsedown->text(get_the_content()); ?>
<?php
$imgList = get_post_meta($post->ID, 'imgList', true);
if($imgList){
  foreach($imgList as $img){
?>
<p><img src="<?php echo $img; ?>" class="aligncenter" alt="<?php the_title(); ?>"/></p>
<?php
  }
}
?>
</div>
</div>
<div class="wt-container wt-topic-reply-wrap">
<?php if(comments_open()){ ?>
	<?php if(get_comments_number()){ $comments = get_comments(array('post_id'=>$this_post_id));?>
	<div class="wt-comments">
		<h3 class="wt-comments-title">回复（<?php echo $comment_number = get_comments_number(); ?>）</h3>
		<div class="wt-comments-list">
		<?php foreach($comments as $key => $comments_item){ ?>
			<div class="wt-comments-item" id="comment-<?php echo $comments_item->comment_ID; ?>">
				<div class="wt-avatar pull-left"><?php echo get_avatar($comments_item->user_id); ?></div>
				<div class="wt-comments-text">
					<?php if($comments_item->comment_approved == 0){ ?>
					<div class="alert alert-warning">您的回复正在等待管理员审核！</div>
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
						<?php if($comments_item->comment_parent){ foreach($comments as $comments_item2){ if($comments_item->comment_parent == $comments_item2->comment_ID){ ?>
							<span class="wt-info-model"><a class="wt-comment-has-parent" href="#comment-<?php echo $comments_item->comment_parent; ?>">回复 <?php echo $comments_item2->comment_author; ?></a></span>
						<?php } } } ?>
						<span class="wt-info-model"><?php echo $comments_item->comment_date; ?></span>
						<span class="wt-comment_floor pull-right"><?php echo $comment_number - $key; ?> 楼</span>
					</div>
					<div class="wt-content wt-comment-content"><?php echo wpautop($Parsedown->text($comments_item->comment_content)); ?></div>
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
	<?php }else{ ?>
	<div class="wp-info wp-info-default">
		<div class="wp-info-icon"><i class="fa fa-info"></i></div>
		<h3>还没有回复！</h3>
		<p>目前还没有任何回复，快来抢沙发吧！</p>
	</div>
	<?php } ?>
	</div>
	<div class="wt-container wt-reply-form">
	<?php if(is_user_logged_in()){ global $current_user; get_currentuserinfo(); ?>
	<h3 class="wt-comments-title">发布回复</h3>
	<div class="wt-comments-item" id="comment_form">
		<form action="<?php echo bloginfo('url'); ?>/wp-comments-post.php" method="post">
		<div class="wt-avatar pull-left"><?php echo get_avatar($current_user->ID); ?></div>
		<div class="wt-comments-text">
			<div class="wt-textarea"><textarea rows="4" name="comment" placeholder="你的回复，随便说点什么吧！" class="form-control"></textarea></div>
			<div class="wt-comments-submit">
				<button type="submit" class="btn btn-primary">发布回复</button>
				<input name="comment_post_ID" value="<?php echo $this_post_id; ?>" id="comment_post_ID" type="hidden">
				<input name="comment_parent" id="comment_parent" value="0" type="hidden">
				<?php if($current_user->ID == 1){ ?>
				<span class="is_sys_user"><label for="is_sys_user"><input type="checkbox" id="is_sys_user" value="1" name="is_sys_user" /> 伪装成随机用户回复文章</label></span>
				<?php } ?>
			</div>
		</div>
		</form>
	</div>
	<?php }else{ ?>
	<?php if(get_option('comment_registration')){ //只有登录以后才能回复 ?>
	<div class="wp-info wp-info-warning">
		<div class="wp-info-icon"><i class="fa fa-info"></i></div>
		<h3>您暂时不能回复！</h3>
		<p>管理员开启了需要登录才能够回复，你可以免费注册一个本站的账号。</p>
	</div>
	<?php }else{ ?>
	<h3 class="wt-comments-title">发布回复</h3>
	<div class="wt-comments-item" id="comment_form">
		<form action="<?php echo bloginfo('url'); ?>/wp-comments-post.php" method="post">
		<div class="row wt-other-input">
		<div class="col-md-4"><input class="form-control" placeholder="你的昵称（必填）" name="author"/></div>
		<div class="col-md-4"><input class="form-control" placeholder="你的邮箱，不会被公开（必填）" name="email"/></div>
		<div class="col-md-4"><input class="form-control" placeholder="你的个人站点" name="url"/></div>
		</div>
		<div class="wt-textarea"><textarea rows="4" name="comment" placeholder="你的回复，随便说点什么吧！" class="form-control"></textarea></div>
			<div class="wt-comments-submit">
				<button type="submit" class="btn btn-primary">发布回复</button>
				<input name="comment_post_ID" value="<?php echo $this_post_id; ?>" id="comment_post_ID" type="hidden">
				<input name="comment_parent" id="comment_parent" value="0" type="hidden">
			</div>
		</form>
	</div>
	<?php } ?>
	<?php } ?>
<?php }else{ ?>
<div class="wp-info wp-info-default">
	<div class="wp-info-icon"><i class="fa fa-info"></i></div>
	<h3>回复被关闭！</h3>
	<p>这篇文章的回复被关闭了，可能是管理员为了系统安全，亦或者是年代久远，回复已没有什么意义，如果你特别想要说点什么，请联系管理员！</p>
</div>
<?php } ?>
<?php } ?>
<?php }?>
</div>
</div>
<div class="col-md-4">
	<div class="side-topic-author">
		<div class="wt-container">
			<div class="mod-tit">
			<?php
			$user = get_user_by('id', $post->post_author);
			?>
				<h3>关于作者</h3>
			</div>
			<div class="side-single-author-avatar">
				<a class="side-single-author-avatar-img pull-left" href="<?php echo get_author_posts_url($post->post_author);?>"><?php echo get_avatar($post->post_author, 48);?></a>
				<a class="side-single-author-avatar-txt" href="<?php echo get_author_posts_url($post->post_author);?>"><?php echo get_the_author();?></a>
				<p><?php
				$au_description = get_user_meta($post->post_author, 'description', true);
				if($au_description){
					echo $au_description;
				}else{
					echo '这个人比较懒，什么都没有填写！';
				}
				?></p>
			</div>
			<div class="side-single-author-count author-tatol">
				<div class="author-tatol-item">
					<span class="item-num"><?php echo count_user_posts($post->post_author, 'topic'); ?></span>
					<span class="item-name">主题</span>
				</div>
				<div class="author-tatol-item">
					<span class="item-num"><?php echo $comment_count; ?></span>
					<span class="item-name">回复</span>
				</div>
				<div class="author-tatol-item">
					<span class="item-num"><?php echo get_user_meta($post->post_author, 'views', true); ?></span>
					<span class="item-name">人气</span>
				</div>
			</div>
			<div class="side-single-author-do">
				<a href="javascript:" class="btn btn-success"><i class="fa fa-plus"></i> 加关注</a>
				<a href="javascript:" class="btn btn-info"><i class="fa fa-comment-o"></i> 发私信</a>
			</div>
		</div>
	</div>
	<div class="side-topic-author">
		<div class="wt-container">
			<div class="mod-tit">
				<h3>相关话题</h3>
			</div>
			<div class="mod-con">
				<ul class="side-topic-rand-list">
				<?php query_posts("orderby=rand&post_type=topic"); ?>
				<?php if(have_posts()):?>
				<?php while (have_posts()) : the_post(); ?>
					<li><a href="<?php the_permalink();?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></li>
				<?php endwhile; ?>
				<?php endif; ?>
				</ul>
			</div>
		</div>
	</div>
</div>
</div>
</div>
<?php
get_footer();
?>