<!--footer / contact-->
<footer class="py-6 bg-light">
	<div class="container">
		<div class="row">
			<div class="col-md-6 mx-auto text-center">
				<ul class="list-inline webpro-foot-menu">
					<?php echo get_footer_menu(); ?>
				</ul>
				<ul class="list-inline social social-dark social-sm">
					<li class="list-inline-item">
						<a href=""><i class="fa fa-facebook"></i></a>
					</li>
					<li class="list-inline-item">
						<a href=""><i class="fa fa-twitter"></i></a>
					</li>
					<li class="list-inline-item">
						<a href=""><i class="fa fa-google-plus"></i></a>
					</li>
					<li class="list-inline-item">
						<a href=""><i class="fa fa-dribbble"></i></a>
					</li>
				</ul>
			</div>
		</div>
		<div class="row copyRight">
			<div class="col-12 text-muted text-center small-xl">
        <?php echo get_option('footer_copyright'); ?> / <?php echo get_option('zh_cn_l10n_icp_num'); ?>
      </div>
		</div>
	</div>
</footer>

<!--scroll to top-->
<div class="scroll-top">
	<i class="fa fa-angle-up" aria-hidden="true"></i>
</div>

<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="/wp-content/themes/wtheme/js/jquery.min.js"></script>
<script src="/wp-content/themes/wtheme/website-development/js/browser.js"></script>
<script src="/wp-content/themes/wtheme/website-development/js/popper.min.js"></script>
<script src="/wp-content/themes/wtheme/website-development/js/bootstrap.min.js"></script>
<script src="/wp-content/themes/wtheme/website-development/js/feather.min.js"></script>
<script src="/wp-content/themes/wtheme/website-development/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script src="/wp-content/themes/wtheme/website-development/fancybox/jquery.fancybox-1.3.4.js"></script>
<script src="/wp-content/themes/wtheme/website-development/js/scripts.js"></script>
</body>
</html>