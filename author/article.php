<?php
global $curauth;
$arg = array(
	'post_type'		=> 'post',
	'page'			=> $paged,
	'author'	=> $curauth->ID
);
if(isset($_GET['leaf'])){$arg['paged'] = $_GET['leaf']; }
query_posts($arg);
?>
<?php if(have_posts()): ?>
<?php while (have_posts()) : the_post(); $ws_has_thumb = false; ?>
<?php get_template_part('loop'); ?>
<?php endwhile; ?>
<ul class="pagenavi">
	<?php author_pagenavi(); ?>
</ul>
<?php else: ?>
<div class="wp-info wp-info-default">
	<div class="wp-info-icon"><i class="fa fa-info"></i></div>
	<h3>这个页面没有内容！</h3>
	<p>这个页面的内容为空，可能是刚刚添加的分类或者新创建的标签页，管理员还没有添加内容，请过段时间再来访问。</p>
</div>
<?php endif;?>