<?php
global $curauth, $wpdb, $count_love_post, $leaf;
$break_page = 10;
$sql = 'select * from wp_posts, wp_user_item_taxonomy where wp_user_item_taxonomy.item_id = wp_posts.ID and wp_user_item_taxonomy.user_id = '.$curauth->ID.' and wp_posts.post_type = "post" order by wp_user_item_taxonomy.id desc limit '.($leaf-1)*$break_page.', '.$break_page;
$posts = $wpdb->get_results($sql);
$max_page = ceil($count_love_post/$break_page);
?>
<?php if(!empty($posts)){ ?>
<?php foreach($posts as $post){ setup_postdata($post); the_post(); $ws_has_thumb = false; ?>
<?php get_template_part('loop'); ?>
<?php } ?>
<ul class="pagenavi">
	<?php author_pagenavi(5, $max_page); ?>
</ul>
<?php }else{ ?>
<div class="wp-info wp-info-default">
	<div class="wp-info-icon"><i class="fa fa-info"></i></div>
	<h3>这个页面没有内容！</h3>
	<p>这个页面的内容为空，可能是刚刚添加的分类或者新创建的标签页，管理员还没有添加内容，请过段时间再来访问。</p>
</div>
<?php } ?>