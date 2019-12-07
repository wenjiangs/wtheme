<?php include('header.php'); ?>

<section class="py-7 bg-light aloneWorks" id="works">
	<div class="container">
		<div class="row">
			<div class="col-md-10 mx-auto">
				<h2 class="dot-circle">优秀案例</h2>
				<p class="text-muted lead">每一个客户案例都是我们的名片，用一个个我们原创设计的案例体现我们的专注与水平。</p>
			</div>
		</div>
		<div class="row mt-5">
      <?php if(have_posts()):?>
      <?php while (have_posts()) : the_post();?>
			<div class="col-lg-3 col-md-6 col-sm-6 mb-5">
				<div class="card works-img">
					<a href="<?php the_permalink();?>" title="<?php the_title(); ?>">
						<?php the_post_thumbnail(); ?>
					</a>
					<div class="card-body">
						<a href="<?php the_permalink();?>" title="<?php the_title(); ?>">
							<h5 class="card-title"><?php the_title(); ?></h5>
							<p class="card-text"><?php echo wp_trim_words(get_the_excerpt(), 30); ?></p>
						</a>
					</div>
				</div>
			</div>
      <?php endwhile; ?>
      <?php endif; ?>
		</div>
	</div>
</section>

<?php include('footer.php'); ?>