<div class="col-md-4 pageSidebar">

  <?php if('topic'==get_post_type()){ ?>
  <div class="sideTopicPub">
    <a href="/publish?type=topic"><i class="fa fa-pencil"></i>发布话题</a>
    <a class="sideTickBtn2" href="/publish?type=topic"><i class="fa fa-calendar-check-o"></i>每日签到</a>
  </div>
  <?php } ?>

	<?php
	if(is_author()){
		global $au_description, $comment_count, $curauth, $curauth_views;
	?>
	<div class="side-term-info">
		<div class="wt-container">
			<div class="mod-tit">
				<h3>简介</h3>
			</div>
			<div class="mod-con">
				<div class="side-term-description"><?php echo wpautop($au_description); ?></div>
			</div>
		</div>
	</div>
	<div class="page-side-count">
		<div class="wt-container">
			<div class="author-tatol-item">
			<span class="item-num"><?php echo count_user_posts($curauth->ID, 'post'); ?></span>
			<span class="item-name">文章</span>
			</div>
			<div class="author-tatol-item">
			<span class="item-num"><?php echo $comment_count; ?></span>
			<span class="item-name">评论</span>
			</div>
			<div class="author-tatol-item">
			<span class="item-num"><?php echo $curauth_views+1; ?></span>
			<span class="item-name">人气</span>
			</div>
		</div>
	</div>
	<?php } ?>
	<?php
	if(is_tag() || is_category()){
	global $term;
	if(!empty($term->description)){
	?>
	<div class="side-term-info">
		<div class="wt-container">
			<div class="mod-tit">
				<h3>简介</h3>
			</div>
			<div class="mod-con">
				<div class="side-term-description"><?php echo wpautop($term->description); ?></div>
			</div>
		</div>
	</div>
	<?php }} ?>
	<div class="side-img"><img src="/wp-content/themes/wtheme/images/sidebar.png"></div>
	<?php if(!is_page()){ ?>
	<div class="side-tags">
		<div class="wt-container">
			<div class="mod-tit">
				<a href="/tags" class="pull-right">更多 <i class="fa fa-angle-right"></i></a>
				<h3>热门标签</h3>
			</div>
			<div class="mod-con">
				<?php
				$side_tags = get_tags(array('orderby'=>'count', 'order'=>'DESC', 'number'=>16));
				foreach($side_tags as $side_tags_item){
				?>
				<a href="<?php echo get_tag_link($side_tags_item->term_id); ?>"><?php echo $side_tags_item->name; ?></a>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php } ?>
  <?php if(!is_page()){ ?>
	<div class="side-group">
	<div class="wt-container">
	<div class="mod-tit">
		<a href="/people" class="pull-right">更多 <i class="fa fa-angle-right"></i></a>
		<h3>推荐作者</h3>
	</div>
	<div class="mod-con">
		<?php
		$side_user = wp_cache_get('side_user');
		if(!$side_user){
			$side_user_sql = "select * from wp_users order by rand() LIMIT 5";
			global $wpdb;
			$side_user = $wpdb->get_results($side_user_sql);
			wp_cache_add('side_user', $side_user);
		}
		?>
		<div class="row wt-group">
			<?php
			foreach($side_user as $user_item){
				$follow_text = '关注';
				$follow_action = 'follow';
				$follow_count = collection_count($user_item->ID, 'user');
				if(is_user_logged_in()){
					//查询用户是否喜欢过
					$user_is_follow = is_follow_item($current_user->ID, $user_item->ID, 'user');
					if($user_is_follow > 0){
						$follow_text = '已关注';
						$follow_action = 'unfollow';
					}
				}
			?>
			<div class="col-md-12">
				<div class="wt-group-avatar pull-left"><a href="<?php echo get_author_posts_url($user_item->ID);?>"><?php echo get_avatar($user_item->ID);?></a></div>
				<a follow_action = "<?php echo $follow_action; ?>"
          item_id="<?php echo $user_item->ID; ?>"
          follow_count="<?php echo $follow_count; ?>"
          item_type="user"
          href="javascript:"
          class="follow_btn pull-right">
					<i class="fa fa-heart"></i> <span><?php echo $follow_text; ?></span> <?php echo $follow_count; ?>
				</a>
				<div class="wt-group-text">
					<h3 class="wt-group-tit"><a href="<?php echo get_author_posts_url($user_item->ID);?>"><?php echo $user_item->display_name?></a></h3>
					<p>
					<span class="wt-info-model">文章 <?php echo count_user_posts($user_item->ID, 'post'); ?></span>
					<span class="wt-info-model">评论 <?php echo count_user_comments($user_item->ID); ?></span>
					</p>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
	</div>
</div>
  <?php } ?>
<div class="side-friendship">
	<div class="wt-container">
		<div class="mod-tit">
			<a href="/friendship" class="pull-right">更多 <i class="fa fa-angle-right"></i></a>
			<h3>友情链接</h3>
		</div>
		<div class="mod-con">
			<?php
			$friend_link = get_bookmarks( array('category'=>82) );
			foreach($friend_link as $friend_link_item):
			?>
			<a href="<?php echo $friend_link_item->link_url;?>" target="_blank" title="<?php echo $friend_link_item->link_description;?>"><?php echo $friend_link_item->link_name;?></a>
			<?php endforeach;?>
		</div>
	</div>
</div>
<?php $toc = singleToc($post->post_content); ?>
<?php if(is_single() && !empty($toc)){?>
<div class="side-toc">
  <div class="wt-container">
		<div class="mod-tit">
			<h3>文章目录</h3>
		</div>
		<div class="mod-con">
    <?php echo $toc; ?>
    </div>
	</div>
</div>
<?php } ?>
</div>