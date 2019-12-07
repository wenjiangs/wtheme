<?php
global $curauth, $wpdb, $count_love_user, $leaf;
$break_page = 20;
$users = $wpdb->get_results('select * from wp_user_item_taxonomy, wp_users where wp_user_item_taxonomy.item_type = "user" and wp_user_item_taxonomy.item_id = wp_users.ID and wp_user_item_taxonomy.user_id = '.$curauth->ID.' order by wp_user_item_taxonomy.id desc limit '.($leaf-1)*$break_page.', '.$break_page);
$max_page = ceil($count_love_user/$break_page);
?>
<div class="row no_pama">
<?php
foreach($users as $key => $user_item){
	$follow_text = '关注';
	$follow_action = 'follow';
	$follow_count = $wpdb->get_var('select count(*) from wp_user_item_taxonomy where item_id = '.$user_item->ID.' and item_type = "user"');
	if(is_user_logged_in()){
		//查询用户是否喜欢过
		$user_is_follow = $wpdb->get_var('select count(*) from wp_user_item_taxonomy where item_id = '.$user_item->ID.' and item_type = "user" and user_id = '.$current_user->ID);
		if($user_is_follow > 0){
			$follow_text = '已关注';
			$follow_action = 'unfollow';
		}
	}
?>
<div class="col-md-6 no_pama">
	<div class="collection-wrap">
		<div class="collection-img"><a href="<?php echo get_author_posts_url($user_item->ID);?>"><?php echo get_avatar($user_item->ID);?></a>	</div>
		<div class="collection-text">
			<h3>
			<a follow_action = "<?php echo $follow_action; ?>" item_id="<?php echo $user_item->ID; ?>" item_type="user" href="javascript:" class="follow_btn pull-right">
				<i class="fa fa-heart"></i> <span><?php echo $follow_text; ?></span> <?php echo $follow_count; ?>
			</a>
			<a href="<?php echo get_author_posts_url($user_item->ID);?>" class="item_name"><?php echo $user_item->display_name?></a></h3>
			<div class=""><span><?php echo get_user_meta($user_item->ID, 'views', true);?></span> 人气 <span class="gap-point">·</span> <?php echo count_user_posts($user_item->ID, 'post');?> 文章</div>
			<p><?php if(get_user_meta($user_item->ID, 'description', true)){
			echo wp_trim_words(get_user_meta($user_item->ID, 'description', true), 36);
			}else{
				echo '这个人比较懒，什么都没有填写！';
			}
			?></p>
		</div>
	</div>
</div>
<?php } ?>
</div>
		<ul class="pagenavi">
			<?php author_pagenavi(5, $max_page);?>
		</ul>