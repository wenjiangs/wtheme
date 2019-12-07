<?php
global $curauth, $wpdb, $count_love_category, $leaf;
$break_page = 20;
$categorys = $wpdb->get_results('select * from wp_user_item_taxonomy, wp_terms where (wp_user_item_taxonomy.item_type = "category" or wp_user_item_taxonomy.item_type = "docs" or wp_user_item_taxonomy.item_type = "tag") and wp_user_item_taxonomy.item_id = wp_terms.term_id and wp_user_item_taxonomy.user_id = '.$curauth->ID.' order by wp_user_item_taxonomy.id desc limit '.($leaf-1)*$break_page.', '.$break_page);
$max_page = ceil($count_love_category/$break_page);
?>

<div class="row no_pama">
<?php
foreach($categorys as $key => $tags_item){
	$tag_thumb = get_term_meta($tags_item->term_id, 'thumb', true);
	if(!$tag_thumb){
		$tag_thumb = '/wp-content/themes/wtheme/images/placeholder.png';
	}
	$follow_text = '关注';
	$follow_action = 'follow';
	$follow_count = $wpdb->get_var('select count(*) from wp_user_item_taxonomy where item_id = '.$tags_item->term_id.' and item_type = "tag"');
	if(is_user_logged_in()){
		//查询用户是否喜欢过
		$user_is_follow = $wpdb->get_var('select count(*) from wp_user_item_taxonomy where item_id = '.$tags_item->term_id.' and item_type = "tag" and user_id = '.$current_user->ID);
		if($user_is_follow > 0){
			$follow_text = '已关注';
			$follow_action = 'unfollow';
		}
	}
	
	$term_taxonomy = $wpdb->get_results('select description, count from wp_term_taxonomy where term_id = '.$tags_item->term_id);
	$term_taxonomy = $term_taxonomy[0];
?>
	<div class="col-md-6 no_pama">
	<div class="collection-wrap">
		<div class="collection-img"><a href="<?php echo get_tag_link($tags_item->term_id); ?>" target="blank"><img src="<?php echo $tag_thumb; ?>" alt="<?php echo $tags_item->name; ?>" /></a></div>
		<div class="collection-text">
			<h3>
			<a follow_action = "<?php echo $follow_action; ?>" item_id="<?php echo $tags_item->term_id; ?>" item_type="tag" href="javascript:" class="follow_btn pull-right">
			<i class="fa fa-heart"></i> <span><?php echo $follow_text; ?></span> <?php echo $follow_count; ?>
			</a>
			<a href="<?php echo get_tag_link($tags_item->term_id); ?>" class="item_name" target="blank"><?php echo $tags_item->name; ?></a>
			</h3>
			<div>文章 <?php echo $term_taxonomy->count; ?> · 浏览 <?php echo get_term_meta($tags_item->term_id, 'views', true);?></div>
			<p><?php if(empty($term_taxonomy->description)) echo '还没有描述！'; else echo wp_trim_words($term_taxonomy->description, 36);?></p>
		</div>
	</div>
	</div>
<?php } ?>
</div>
<ul class="pagenavi">
	<?php author_pagenavi(5, $max_page);?>
</ul>


