<?php
global $wpdb, $paged;

if(isset($_GET['del'])){
	$wpdb->query('delete from wp_submit_baidu where id = '.$_GET['del']);
}

$paged = $_GET['paged'];
if(empty($paged) || $paged == 0){
	$paged = 1;
}
$break_page = 20;

//统计
$submit_baidu_count = $wpdb->get_var('select count(*) from wp_submit_baidu');
$max_page = ceil($submit_baidu_count/$break_page);

$submit_baidu = $wpdb->get_results("select * from wp_submit_baidu order by submit_date desc limit ".(($paged-1)*$break_page).", ".$break_page);
?>
<div class="wrap">
	<h1 class="wrap-h1">主动提交到百度</h1>
	<table class="wp-list-table widefat fixed striped posts baidu_submit">
		<thead>
		<tr>
			<td width="32">ID</td>
			<td>类型</td>
			<td>文章</td>
			<td>剩余额度</td>
			<td width="96">成功与否</td>
			<td width="130">时间</td>
			<td width="32"></td>
		</tr>
		</thead>
		<tbody>
			<?php foreach($submit_baidu as $submit_baidu_item){ $result = json_decode($submit_baidu_item->result); ?>
			<tr>
			<td><?php echo $submit_baidu_item->id; ?></td>
			<td><?php if(isset($result->remain)) echo '百度网页'; else echo '熊掌号'; ?></td>
			<td><a href="<?php echo get_permalink($submit_baidu_item->post_id); ?>"><?php echo get_the_title($submit_baidu_item->post_id); ?></a></td>
			<td><?php if(isset($result->remain)) echo $result->remain; else echo $result->remain_batch; ?></td>
			<td><?php if(isset($result->success)) echo $result->success; else echo $result->success_batch; ?></td>
			<td><?php echo date('Y-m-d H:i:s', ($submit_baidu_item->submit_date + (8*60*60))); ?></td>
			<td><a href="/wp-admin/edit.php?page=wt_submit_baidu&del=<?php echo $submit_baidu_item->id; ?>">删除</a></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<ul class="pagenavi">
		<?php pagenavi(8, $max_page);?>
	</ul>
</div>
<style>
.pagenavi{ text-align: center; margin: 0; padding: 15px 0; }
.pagenavi li {
    display: inline-block;
    margin: 0 3px;
    font-size: 14px;
}
.pagenavi a {
    display: block;
    padding: 3px 10px;
    color: #666;
	border-radius:3px;
}
.pagenavi a:hover{ background:#EFEFEF; }
.pagenavi .active a {
    background: #337AB7;
    color: #FFF;
}
.pagenavi .disabled{ margin-right:10px;}
</style>