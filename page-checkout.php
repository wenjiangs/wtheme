<?php
if(!isset($_POST['goods_id'])){
	wp_die('系统错误！');
	exit;
}
$goods = get_post($_POST['goods_id']);
$img_id = get_post_thumbnail_id($goods->ID);
$img_arr = wp_get_attachment_image_src($img_id);
get_header();
?>

<div class="container">
	<div class="wt-container" style="margin-bottom:20px;">
		<div class="wt-nav-tabs">
			<h2>收货地址</h2>
		</div>
		<ul class="receiving_address">
			<li>
			<div class="receiving_address_item">
				<div>文江 <span class="pull-right">15828424300</span></div>
				<p>四川 成都市 青羊区 培风小区8栋 三单元5楼18号</p>
			</div>
			</li>
			<li>
			<div class="receiving_address_item">
				<div>文江 <span class="pull-right">15828424300</span></div>
				<p>四川 成都市 青羊区 培风小区8栋 三单元5楼18号</p>
			</div>
			</li>
			<li>
			<div class="receiving_address_item">
				<div>文江 <span class="pull-right">15828424300</span></div>
				<p>四川 成都市 青羊区 培风小区8栋 三单元5楼18号</p>
			</div>
			</li>
			<li>
			<div class="receiving_address_item">
				<div>文江 <span class="pull-right">15828424300</span></div>
				<p>四川 成都市 青羊区 培风小区8栋 三单元5楼18号</p>
			</div>
			</li>
			<li>
			<div class="receiving_address_item">
				<div>文江 <span class="pull-right">15828424300</span></div>
				<p>四川 成都市 青羊区 培风小区8栋 三单元5楼18号</p>
			</div>
			</li>
			<li>
			<div class="receiving_address_item">
				<div>文江 <span class="pull-right">15828424300</span></div>
				<p>四川 成都市 青羊区 培风小区8栋 三单元5楼18号</p>
			</div>
			</li>
		</ul>
	</div>
	<div class="wt-container" style="margin-bottom:20px;">
		<div class="wt-nav-tabs">
			<h2>产品信息</h2>
		</div>
		<div class="goods_form">
			<table class="table table-bordered">
				<tr>
					<th style="text-align:left;">产品</th>
					<th>积分</th>
					<th>购买数量</th>
					<th>总计积分</th>
				</tr>
				<tr>
					<td style="text-align:left;"><img src="<?php echo $img_arr[0]; ?>"/> <?php echo $goods->post_title; ?></td>
					<td><?php echo get_post_meta($goods->ID, 'goods_prices', true); ?></td>
					<td>
					<div id="quantityInput" class="quantity-input">
						<button type="button" class="reduce">
						  <i class="fa fa-minus"></i>
						</button>
						<input name="goods_num" class="quantity-input-tx" value="1" maxlength="4" type="text">
						<button type="button" class="add">
						  <i class="fa fa-plus"></i>
						</button>
					</div>
					</td>
					<td><?php echo get_post_meta($goods->ID, 'goods_prices', true); ?></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="wt-container" style="margin-bottom:20px;">
		<div class="wt-nav-tabs">
			<h2>附加信息</h2>
		</div>
		<div class=""></div>
	</div>
	<div class="goods_form_btn"><button type="submit" class="btn btn-danger btn-lg">去结算</button></div>
</div>

<?php get_footer(); ?>