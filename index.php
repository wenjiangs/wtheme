<?php
$home_banner = get_option('home_banner');

$terms = get_terms(array(
	'number'		=> 10,
	'offset'		=> 0,
	'taxonomy'		=> "docs",
  'parent'    => 0,
	'hide_empty' 	=> false,
  'orderby' => 'id',
  'order' => 'desc'
));

get_header();
?>
<div class="container">
	<div class="row">
		<div class="col-md-8">
		<?php if($home_banner){ ?>
		<div class="wt-banner">
			<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
			  <ol class="carousel-indicators">
				<?php foreach($home_banner as $key=> $home_banner_item){ if(empty($home_banner_item)) continue; ?>
				<li data-target="#carousel-example-generic" data-slide-to="<?php echo $key; ?>" <?php if($key == 0) echo 'class="active"'; ?>></li>
				<?php } ?>
			  </ol>
			  <div class="carousel-inner" role="listbox">
				<?php foreach($home_banner as $key => $home_banner_item){ if(empty($home_banner_item)) continue; ?>
				<div class="item <?php if($key == 0) echo 'active'; ?>">
				  <a href="<?php echo get_url_by_banner_type($home_banner_item); ?>"><img src="<?php echo $home_banner_item['image']; ?>" /></a>
				</div>
				<?php } ?>
			  </div>
			  <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
				<span class="glyphicon glyphicon-chevron-left fa fa-angle-left"></span>
			  </a>
			  <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
				<span class="glyphicon glyphicon-chevron-left fa fa-angle-right"></span>
			  </a>
			</div>
		</div>
		<?php } ?>
    
    <div class="wt-container indexDocs">
      <div class="wt-nav-tabs">
        <a href="/doc" class="pull-right">更多 <i class="fa fa-angle-right"></i></a>
        <h2 class="pull-left">最新手册</h2>
      </div>
      <div class="indexDocSwiper swiper-container">
        <div class="swiper-wrapper">
        <?php foreach($terms as $item){
          $docs_cover = get_term_meta($item->term_id, 'docs_cover', true);
          if(empty($docs_cover)){
            $docs_cover = '/wp-content/themes/wtheme/images/placeholder.png';
          }
        ?>
          <div class="swiper-slide">
            <a href="<?php echo get_term_link($item->term_id, 'docs'); ?>"><img src="<?php echo $docs_cover; ?>"/></a>
            <h3><a href="<?php echo get_term_link($item->term_id, 'docs'); ?>"><?php echo $item->name; ?></a></h3>
          </div>
        <?php } ?>
        </div>
      </div>
    </div>
    
		<div class="wt-container">
		<div class="wt-nav-tabs">
			<h2 class="pull-left">最新文章</h2>
		</div>
		<?php if(have_posts()): ?>
		<?php while (have_posts()) : the_post(); $ws_has_thumb = false;?>
		<?php get_template_part('loop'); ?>
		<?php endwhile;?>
		<?php else:?>
		<div class="wp-info wp-info-default">
			<div class="wp-info-icon"><i class="fa fa-info"></i></div>
			<h3>这个页面没有内容！</h3>
			<p>这个页面的内容为空，可能是刚刚添加的分类或者新创建的标签页，管理员还没有添加内容，请过段时间再来访问。</p>
		</div>
		<?php endif;?>

		<?php if(have_posts()):?>
		<ul class="pagenavi">
			<?php pagenavi();?>
		</ul>
		<?php endif; ?>
		</div>
		</div>
		<?php get_sidebar(); ?>
	</div>
</div>
<?php
get_footer();
?>