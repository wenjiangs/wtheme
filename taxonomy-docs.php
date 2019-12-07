<?php
get_header();
$docs = get_term_by('slug', $term, 'docs');
$docs_info = get_term_meta($docs->term_id, 'docs_info', true);
$docs_views = get_term_meta($docs->term_id, 'views', true);
update_term_meta($docs->term_id, 'views', $docs_views+1);
$salong_posts = new WP_Query(
	array(
		'post_type' => 'doc',//自定义文章类型
		'ignore_sticky_posts' => 1,//忽略置顶文章
		'posts_per_page' => 10000,//显示的文章数量
		'order'		=> 'ASC',
		'tax_query' => array(
			array(
				'taxonomy' => 'docs',//分类法名称
				'field'    => 'id',//根据分类法条款的什么字段查询，这里设置为ID
				'terms'    => $docs->term_id,//分类法条款，输入分类的ID，多个ID使用数组：array(1,2)
			)
		),
	)
);

//评论处理
if(isset($_POST['comment_term_ID'])){
	
	//删除非法HTML标签
	$term_comment = strip_tags($_POST['term_comment']);
	
	$user_id = $current_user->ID;
	if(isset($_POST['is_sys_user']) && $_POST['is_sys_user'] == 1){
		$user_id = $wpdb->get_var('SELECT * FROM wp_sys_user ORDER BY RAND() LIMIT 1');
	}
	
	$publish_date = time();
	if(isset($_POST['is_rand_time']) && $_POST['is_rand_time'] == 1){
		$publish_date = rand(1491235200, time());
	}
	
	if(!($_POST['term_comment'] == '')){
		$sql = 'insert into wp_term_comment values (NULL, '.$_POST['comment_term_ID'].', '.$user_id.', '.$_POST['parent_id'].', "'.$term_comment.'", 0, '.$publish_date.')';
		$is_sql = $wpdb->query($sql);
		if($is_sql){
			$success_message = '提交成功！';
		}
	}else{
		$error_message = '评论内容不能为空！';
	}
	
}

$docs_cover = get_term_meta($docs->term_id, 'docs_cover', true);
if(empty($docs_cover)){
	$docs_cover = '/wp-content/themes/wtheme/images/placeholder.png';
}

$follow_text = '关注';
$follow_action = 'follow';
$item_type = 'docs';

$this_term_id = $docs->term_id;

$follow_count = collection_count($this_term_id, 'docs');
if(is_user_logged_in()){
	//查询用户是否喜欢过
	$user_is_follow = is_follow_item($current_user->ID, $this_term_id, 'docs');
	if($user_is_follow > 0){
		$follow_text = '已关注';
		$follow_action = 'unfollow';
	}
}

//查询评论
if($paged == 0){
	$paged = 1;
}
$break_page = 20;
$term_comment = $wpdb->get_results('select * from wp_term_comment where term_id = '.$docs->term_id.' order by publish_date desc limit '.($paged-1)*$break_page.','.$break_page);
$term_comment_count = $wpdb->get_var('select count(*) from wp_term_comment where term_id = '.$docs->term_id);
$max_page = ceil($term_comment_count/$break_page);

$comment_count = $wpdb->get_var('select count(*) from wp_term_comment where term_id = '.$docs->term_id);

/* 查询子分类 2019年4月27日 21:41:50 */
$child = get_terms(array(
	'number'		=> 100,
	'taxonomy'		=> "docs",
  'parent'    => $docs->term_id,
	'hide_empty' 	=> false,
));

//print_r($child);

?>
<div class="container">
	<div class="row">
		<div class="col-md-8">
			<div class="wt-container">
				<div class="category-head">
					<div class="category-img pull-left"><img src="<?php echo $docs_cover; ?>" alt="<?php echo $docs->name; ?>"></div>
					<div class="category-btn pull-right">
						<a href="<?php echo get_permalink($salong_posts->posts[0]->ID); ?>" target="_blank" class="btn btn-info">开始阅读</a>
						<a follow_action = "<?php echo $follow_action; ?>"
              item_id="<?php echo $this_term_id; ?>"
              follow_count="<?php echo $follow_count; ?>"
              item_type="<?php echo $item_type; ?>"
              href="javascript:" class="follow_btn btn btn-success category-fllow-btn">
							<i class="fa fa-heart"></i> <span><?php echo $follow_text; ?></span> <?php echo $follow_count; ?>
						</a>
					</div>
					<div class="category-text">
						<h3><?php echo $docs->name; ?></h3>
						<p>
							<span class="wt-info-model"><i class="fa fa-file-text-o"></i> 文档 <?php echo $docs->count; ?></span>
							<span class="wt-info-model"><i class="fa fa-eye"></i> 浏览 <?php echo $docs_views; ?></span>
							<span class="wt-info-model"><i class="fa fa-comment-o"></i> 评论 <?php echo $comment_count; ?></span>
						</p>
					</div>
				</div>
				<div class="wt-content">
					<?php echo wpautop($docs_info); ?>
				</div>
			</div>
			
			<div class="wt-container books_wrap">
				<div class="wt-nav-tabs">
					<h2 class="pull-left">目录</h2>
				</div>
				<div class="doc_term_toc">
					<?php
					$docKey = 0;
					if ($salong_posts->have_posts()): while ($salong_posts->have_posts()): $salong_posts->the_post(); $docKey++;
					?>
					<div><a href="<?php the_permalink(); ?>"><?php echo $docKey.'、'; the_title(); ?></a></div>
					<?php endwhile; ?>
					<?php endif; wp_reset_query(); ?>
				</div>
			</div>
			
			<div class="wt-container books_wrap">
				<div class="wt-nav-tabs">
					<h2 class="pull-left">评论</h2>
				</div>
				<?php if(count($term_comment) > 0){ ?>
				<div class="wt-comments-list">
				<?php foreach($term_comment as $key => $term_comment_item){ ?>
				<div class="wt-comments-item" id="term_comment-<?php echo $term_comment_item->id; ?>">
					<div class="wt-avatar pull-left"><?php echo get_avatar($term_comment_item->user_id); ?></div>
					<div class="wt-comments-text">
						<div class="wt-comments-info">
							<span class="wt-info-model"><a href="<?php echo get_author_posts_url($term_comment_item->user_id); ?>" target="_blank"><?php echo get_user_by('id', $term_comment_item->user_id)->data->display_name; ?></a></span>
							<?php if(!($term_comment_item->parent_id == 0)){ ?>
							<span class="wt-info-model">回复 <a href="#term_comment-<?php echo $term_comment_item->parent_id; ?>">
							<?php
							$parent_user_id = $wpdb->get_var('select user_id from wp_term_comment where id = '.$term_comment_item->parent_id);
							echo get_user_by('id', $parent_user_id)->data->display_name;
							?>
							</a></span>
							<?php } ?>
							<span class="wt-info-model"><?php echo date('Y-m-d H:i:s', $term_comment_item->publish_date); ?></span>
							<span class="wt-comment_floor pull-right"><?php echo count($term_comment)-$key; ?> 楼</span>
						</div>
						<div class="wt-content wt-comment-content"><?php echo wpautop($term_comment_item->content); ?></div>
						<div class="wt-comments-tools">
							<span class="wt-info-model"><i class="fa fa-thumbs-o-up"></i> 0</span>
							<span class="wt-info-model"><i class="fa fa-thumbs-o-down"></i> 0</span>
							<span class="wt-info-model"><a href="javascript:" class="reply_btn" this_comment_id="<?php echo $term_comment_item->id; ?>"><i class="fa fa-level-up"></i> 回复</a></span>
						</div>
					</div>
				</div>
				<?php } ?>
				</div>
				<?php }else{ ?>
				<div class="wp-info wp-info-default">
					<div class="wp-info-icon"><i class="fa fa-info"></i></div>
					<h3>还没有评论！</h3>
					<p>目前还没有任何评论，快来抢沙发吧！</p>
				</div>
				<?php } ?>
			</div>
			<div class="wt-container books_wrap">
				<div class="wt-nav-tabs">
					<h2 class="pull-left">发表评论</h2>
				</div>
				<?php if(is_user_logged_in()){ ?>
				<div class="wt-comments-item" id="comment_form">
					<form action="" method="post">
					<div class="wt-avatar pull-left"><?php echo get_avatar($current_user->ID); ?></div>
					<div class="wt-comments-text">
						<div class="wt-textarea"><textarea rows="4" name="term_comment" placeholder="你的评论，随便说点什么吧！" class="form-control"></textarea></div>
						<div class="wt-comments-submit">
							<button type="submit" class="btn btn-primary">发布评论</button>
							<input name="comment_term_ID" value="<?php echo $docs->term_id; ?>" id="comment_term_ID" type="hidden">
							<input name="parent_id" id="parent_id" value="0" type="hidden">
							<?php if($current_user->ID == 1){ ?>
							<span class="is_sys_user"><label for="is_sys_user"><input type="checkbox" id="is_sys_user" value="1" name="is_sys_user" /> 随机用户</label></span>
							<span class="is_sys_user"><label for="is_rand_time"><input type="checkbox" id="is_rand_time" value="1" name="is_rand_time" /> 随机时间</label></span>
							<?php } ?>
						</div>
					</div>
					</form>
				</div>
				<?php }else{ ?>
				<div class="wp-info wp-info-warning">
					<div class="wp-info-icon"><i class="fa fa-info"></i></div>
					<h3>您暂时不能评论！</h3>
					<p>管理员开启了需要登录才能够评论，你可以免费注册一个本站的账号。</p>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php get_sidebar(); ?>
	</div>
</div>
<?php get_footer(); ?>