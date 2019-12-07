<?php
global $wpdb, $paged;

if(isset($_GET['del'])){
	$wpdb->query('delete from wp_loginlog where id = '.$_GET['del']);
}

if(isset($_GET['paged'])){
	$paged = $_GET['paged'];
}else{
	$paged = 0;
}
$break_page = 20;
$this_page = $paged == 0 ? 1 : $paged;

//统计
$loginlog_error_count = $wpdb->get_var('select count(*) from wp_loginlog where success = 0');
$loginlog_success_count = $wpdb->get_var('select count(*) from wp_loginlog where success = 1');
$loginlog_count = $wpdb->get_var('select count(*) from wp_loginlog');
$max_page = ceil($loginlog_count/$break_page);

if(isset($_GET['success'])){
	$loginlog = $wpdb->get_results("select * from wp_loginlog where success = ".$_GET['success']." order by time desc limit ".(($this_page-1)*$break_page).", ".$break_page);
}elseif(isset($_GET['username'])){
	$loginlog = $wpdb->get_results("select * from wp_loginlog where username = '".$_GET['username']."' order by time desc limit ".(($this_page-1)*$break_page).", ".$break_page);
}else{
	$loginlog = $wpdb->get_results("select * from wp_loginlog order by time desc limit ".(($this_page-1)*$break_page).", ".$break_page);
}

?>
<style>
.pagenavi{
    text-align: center;
    margin: 0;
    padding: 15px 0;
}
.pagenavi li {
    display: inline-block;
    margin: 0 3px;
    font-size: 12px;
}
.pagenavi a {
    display: block;
    border-radius: 3px;
    padding: 3px 10px 1px;
    color: #666;
}
.pagenavi .active a {
    background: #337AB7;
    color: #FFF;
}
</style>
<div class="wrap">
	<h1>登录日志</h1>
	<ul class="subsubsub">
<li class="all">
<a href="/wp-admin/users.php?page=wt_loginlog" class="current">全部<span class="count">（<?php echo $loginlog_count; ?>）</span>
</a> |</li>
<li class="publish">
<a href="/wp-admin/users.php?page=wt_loginlog&success=1">登录成功<span class="count">（<?php echo $loginlog_success_count; ?>）</span>
</a> |</li>
<li class="trash">
<a href="/wp-admin/users.php?page=wt_loginlog&success=0">登录失败<span class="count">（<?php echo $loginlog_error_count; ?>）</span>
</a></li>
</ul>
	<table class="wp-list-table widefat fixed striped posts">
		<thead>
		<tr>
			<td>ID</td>
			<td>用户名</td>
			<td>时间</td>
			<td>IP</td>
			<td>登录状态</td>
			<td width="64"></td>
		</tr>
		</thead>
		<tbody>
			<?php foreach($loginlog as $loginlog_item){ ?>
			<tr>
			<td><?php echo $loginlog_item->id; ?></td>
			<td><a href="/wp-admin/users.php?page=ws_loginlog&username=<?php echo $loginlog_item->username; ?>"><?php echo $loginlog_item->username; ?></a></td>
			<td><?php echo date('Y-m-d H:i:s', $loginlog_item->time); ?></td>
			<td><?php echo $loginlog_item->ip; ?></td>
			<td><?php echo $loginlog_item->success; ?></td>
			<td><a href="/wp-admin/users.php?page=ws_loginlog&del=<?php echo $loginlog_item->id; ?>">删除</a></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<ul class="pagenavi">
		<?php pagenavi(5, $max_page);?>
	</ul>
</div>