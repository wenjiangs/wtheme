<?php
get_header();

$leaf = 1;
if(isset($_GET['leaf'])){
	$leaf = $_GET['leaf'];
}

global $current_user;
$curauth = $wp_query->get_queried_object();
$curauth_views = get_user_meta($curauth->ID, 'views', true);
update_user_meta($curauth->ID, 'views', $curauth_views+1);
$comment_args = array(
	'user_id' => $curauth->ID, // use post_id, not post_ID
	'post_type'		=> 'post',
	'count' => true //return only the count
);
$comment_count = get_comments($comment_args);
$au_description = get_user_meta($curauth->ID, 'description', true);
if(empty($au_description)) $au_description = '这家伙很懒，什么都还没有填写！';

$count_user_post_all = $wpdb->get_var('select count(*) from wp_posts where post_author = '.$curauth->ID.' and post_status = "publish" and (post_type = "post" or post_type = "topic")');
$count_user_post_topic = $wpdb->get_var('select count(*) from wp_posts where post_author = '.$curauth->ID.' and post_status = "publish" and post_type = "topic"');
$count_user_reply = $wpdb->get_var('select count(*) from wp_posts, wp_comments where wp_comments.user_id = '.$curauth->ID.' and wp_posts.post_status = "publish" and wp_posts.post_type = "topic" and wp_comments.comment_post_ID = wp_posts.ID');
$count_user_love = $wpdb->get_var('select count(*) from wp_user_item_taxonomy where user_id = '.$curauth->ID);

$count_love = $wpdb->get_var('select count(*) from wp_user_item_taxonomy where user_id = '.$curauth->ID);
$count_love_post = $wpdb->get_var('select count(*) from wp_user_item_taxonomy where user_id = '.$curauth->ID.' and item_type = "post"');
$count_love_category = $wpdb->get_var('select count(*) from wp_user_item_taxonomy where user_id = '.$curauth->ID.' and (item_type = "category" or item_type = "docs" or item_type = "tag")');
$count_love_user = $wpdb->get_var('select count(*) from wp_user_item_taxonomy where user_id = '.$curauth->ID.' and item_type = "user"');
$count_love_fans = $wpdb->get_var('select count(*) from wp_user_item_taxonomy where item_id = '.$curauth->ID.' and item_type = "user"');

$follow_text = '关注';
$follow_action = 'follow';
$item_type = 'user';

$follow_count = collection_count($curauth->ID, $item_type);
if(is_user_logged_in()){
	//查询用户是否喜欢过
	$user_is_follow = is_follow_item($current_user->ID, $curauth->ID, $item_type);
	if($user_is_follow > 0){
		$follow_text = '已关注';
		$follow_action = 'unfollow';
	}
}
?>
<div class="container">
	<div class="row">
		<div class="col-md-8">
		<div class="wt-container">
		<div class="category-head">
			<div class="category-img pull-left"><?php echo get_avatar($curauth->ID); ?></div>
			<div class="category-btn pull-right">
				<a href="/message?user=<?php echo $curauth->ID; ?>" target="_blank" class="btn btn-info">发私信</a>
				<a follow_action = "<?php echo $follow_action; ?>"
          item_id="<?php echo $curauth->ID; ?>"
          item_type="<?php echo $item_type; ?>"
          follow_count="<?php echo $follow_count; ?>"
          href="javascript:" class="follow_btn btn btn-success category-fllow-btn">
					<i class="fa fa-heart"></i> <span><?php echo $follow_text; ?></span> <?php echo $follow_count; ?>
				</a>
			</div>
			<div class="category-text">
				<h3><?php echo $curauth->data->display_name; ?></h3>
				<p>
					<span class="wt-info-model"><?php echo count_user_posts($curauth->ID, 'post'); ?> 文章</span>
					<span class="wt-info-model"><?php echo $curauth_views+1; ?> 浏览</span>
				</p>
			</div>
		</div>
		<div class="wt-nav-tabs">
			<ul class="nav nav-tabs author-nav">
				<li <?php if(!isset($_GET['a'])) echo ' class="active"'; ?>><a href="<?php echo get_author_posts_url($curauth->ID); ?>">全部 <?php echo $count_user_post_all; ?></a></li>
				<li <?php if(isset($_GET['a']) && $_GET['a'] == 'article') echo ' class="active"'; ?>><a href="<?php echo get_author_posts_url($curauth->ID); ?>?a=article">文章 <?php echo count_user_posts($curauth->ID, 'post'); ?></a></li>
				<li <?php if(isset($_GET['a']) && $_GET['a'] == 'comment') echo ' class="active"'; ?>><a href="<?php echo get_author_posts_url($curauth->ID); ?>?a=comment">评论 <?php echo $comment_count; ?></a></li>
				<li <?php if(isset($_GET['a']) && $_GET['a'] == 'topic') echo ' class="active"'; ?>><a href="<?php echo get_author_posts_url($curauth->ID); ?>?a=topic">话题 <?php echo $count_user_post_topic; ?></a></li>
				<li <?php if(isset($_GET['a']) && $_GET['a'] == 'reply') echo ' class="active"'; ?>><a href="<?php echo get_author_posts_url($curauth->ID); ?>?a=reply">回复 <?php echo $count_user_reply; ?></a></li>
				<li <?php if(isset($_GET['a']) && ($_GET['a'] == 'love' || $_GET['a'] == 'love-term' || $_GET['a'] == 'love-user' || $_GET['a'] == 'fans')) echo ' class="active"'; ?>><a href="<?php echo get_author_posts_url($curauth->ID); ?>?a=love">喜欢 <?php echo $count_love; ?></a></li>
				<li <?php if(isset($_GET['a']) && $_GET['a'] == 'profile') echo ' class="active"'; ?>><a href="<?php echo get_author_posts_url($curauth->ID); ?>?a=profile">资料</a></li>
			</ul>
		</div>
		<?php if(isset($_GET['a']) && ($_GET['a'] == 'love' || $_GET['a'] == 'love-term' || $_GET['a'] == 'love-user' || $_GET['a'] == 'fans' || $_GET['a'] == 'love-category')){ ?>
		<div class="sub_author_nav">
			<a href="<?php echo get_author_posts_url($curauth->ID); ?>?a=love" <?php if(isset($_GET['a']) && $_GET['a'] == 'love') echo ' class="active"'; ?>>关注的文章 <?php echo $count_love_post; ?></a>
			<a href="<?php echo get_author_posts_url($curauth->ID); ?>?a=love-category" <?php if(isset($_GET['a']) && $_GET['a'] == 'love-category') echo ' class="active"'; ?>>关注的分类 <?php echo $count_love_category; ?></a>
			<a href="<?php echo get_author_posts_url($curauth->ID); ?>?a=love-user"<?php if(isset($_GET['a']) && $_GET['a'] == 'love-user') echo ' class="active"'; ?>>关注的人 <?php echo $count_love_user; ?></a>
			<a href="<?php echo get_author_posts_url($curauth->ID); ?>?a=fans"<?php if(isset($_GET['a']) && $_GET['a'] == 'fans') echo ' class="active"'; ?>>被关注 <?php echo $count_love_fans; ?></a>
		</div>
		<?php } ?>
		<?php
		if(isset($_GET['a'])){
			include('author/'.$_GET['a'].'.php');
		}else{
			include('author/index.php');
		}
		?>
		</div>
		</div>
	<?php get_sidebar(); ?>
	</div>
</div>
<?php
get_footer();
?>