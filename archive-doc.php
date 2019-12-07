<?php
get_header();
$breakpage = 10;
if($paged == 0){
	$paged = 1;
}
$terms = get_terms(array(
	'number'		=> $breakpage,
	'offset'		=> ($paged-1)*$breakpage,
	'taxonomy'		=> "docs",
  'parent'    => 0,
	'hide_empty' 	=> false,
  'orderby' => 'id',
  'order' => 'desc'
));
$count_terms = $wpdb->get_var('select count(*) from wp_term_taxonomy where taxonomy = "docs"');
$max_page = ceil($count_terms/$breakpage);
?>
<div class="container">
	<div class="row">
	<div class="col-md-8">
	<div class="wt-container">
		<div class="wt-nav-tabs">
			<h2 class="pull-left">开发文档</h2>
		</div>
		<div class="doc_list">
			<?php
			foreach($terms as $terms_item){
			$docs_cover = get_term_meta($terms_item->term_id, 'docs_cover', true);
			if(empty($docs_cover)){
				$docs_cover = '/wp-content/themes/wtheme/images/placeholder.png';
			}
      if(empty($terms_item->description)){
        $terms_item->description = get_term_meta($terms_item->term_id, 'docs_info', true);
      }
			$comment_count = $wpdb->get_var('select count(*) from wp_term_comment where term_id = '.$terms_item->term_id);
			$docs_views = get_term_meta($terms_item->term_id, 'views', true);
			?>
			<div class="doc_list_item">
				<div class="wt-group-avatar pull-left"><a href="<?php echo get_term_link($terms_item->term_id, 'docs'); ?>"><img  src="<?php echo $docs_cover; ?>" alt="<?php echo $terms_item->name; ?>" /></a></div>
				<div class="wt-group-text">
					<h3 class="wt-group-tit"><a href="<?php echo get_term_link($terms_item->term_id, 'docs'); ?>"><?php echo $terms_item->name; ?></a></h3>
					<div class="doc_list_info"><?php echo wp_trim_words($terms_item->description, 60); ?></div>
					<p>
					<span class="wt-info-model"><i class="fa fa-file-text-o"></i> 文档 <?php echo $terms_item->count; ?></span>
					<span class="wt-info-model"><i class="fa fa-comment-o"></i> 评论 <?php echo $comment_count; ?></span>
					<span class="wt-info-model"><i class="fa fa-eye"></i> 浏览 <?php echo $docs_views; ?></span>
					</p>
				</div>
			</div>
			<?php } ?>
			<ul class="pagenavi">
				<?php pagenavi(5, $max_page); ?>
			</ul>
		</div>
	</div>
	</div>
	<?php get_sidebar(); ?>
	</div>
</div>
<?php get_footer(); ?>