<?php
include_once('sphinxapi.php');
get_header();
$rows = 10;
$spage = isset($_GET['spage'])?$_GET['spage']:1;

// 搜索类型
$searchType = array(
  'all' => '全部',
  'post_article' => '文章',
  'post_topic' => '话题',
  'post_doc' => '手册',
  'user' => '用户',
);
$type = isset($_GET['type'])?$_GET['type']:'all';
$w = isset($_GET['w'])?$_GET['w']:'';

$cl = new SphinxClient ();
$cl->SetServer ( '127.0.0.1', 9312);
$cl->SetArrayResult ( true );
$cl->SetLimits(($spage-1)*$rows, $rows);
$res = $cl->Query ( $w, $type=='all'?'*':$type );

if($res['total']>0){
  $dataLoop = array();
  foreach($res['matches'] as $key => $item){
    if(in_array($item['attrs']['post_type'], array('post', 'doc', 'topic'))){
      $dataLoop[$key] = get_post($item['id']);
      $dataLoop[$key]->type = "post";
    }
    if($item['attrs']['post_type']=='user'){
      $dataLoop[$key] = get_user_by('id', $item['id']);
      $dataLoop[$key]->type = "user";
    }
  }
}

?>

<div class="container">
  <form action="/search">
  <div class="searchForm">
    <div class="row">
      <div class="col-md-2">
        <div class="searchLogo"></div>
      </div>
      <div class="col-md-8">
        <div class="searchInput">
          <input type="text" placeholder="搜索关键词" name="w" <?php if(isset($_GET['w'])) echo 'value="'.$_GET['w'].'"';?> class="form-control"/>
        </div>
      </div>
      <div class="col-md-2">
        <div class="searchBtn">
          <button type="submit" class="btn btn-danger btn-block">搜 索</button>
        </div>
      </div>
    </div>
  </div>
  </form>
</div>

<div class="container">
  <div class="searchTabs">
    <ul>
      <?php foreach($searchType as $key => $st){ ?>
      <li <?php if($type==$key) echo 'class="active"'; ?>>
      <a href="/search?type=<?php echo $key; ?>&w=<?php echo $_GET['w']; ?>"><?php echo $st; ?></a>
      </li>
      <?php } ?>
    </ul>
  </div>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-8">
			<div class="wt-container">
			<div class="wt-nav-tabs">
				<h2 class="pull-left">搜索结果</h2>
			</div>
			<div class="search-alert alert alert-warning">为你找到约 <?php echo $res['total']?> 条结果 （用时 <?php echo $res['time'];?> 秒）</div>
      
      <?php if(isset($res['words'])){ ?>
      <div class="search-alert alert alert-success">分词搜索：
        <?php
        $wfArr = array();
        foreach($res['words'] as $key => $wf){
          $wfArr[] = '<code>'.$key.'</code> '.$wf['docs'].'个结果';
        }
        echo implode('，', $wfArr);
        ?>
      </div>
      <?php } ?>
      
      <?php if($res['total']>0){ ?>
      <?php foreach($dataLoop as $post){ ?>
      <?php if($post->type=='post'){ ?>
      <?php setup_postdata($post); get_template_part('loop'); ?>
      <?php }else{ $user = $post; ?>
      <div class="userLoopItem">
        <div class="ULIAvatar pull-left">
          <?php echo get_avatar($user->ID); ?>
        </div>
        <div class="ULIText">
          <h3><a href="<?php echo get_author_posts_url($user->ID); ?>"><?php echo $user->data->display_name; ?></a></h3>
          <div class="ULIAdress">
            <?php echo get_user_meta($user->ID, 'location', true);?>
            <?php echo get_user_meta($user->ID, 'location2', true);?>
          </div>
          <ul class="ULITotal">
            <li>关注 <span><?php echo follow_count($user->ID, 'user'); ?></span></li>
            <li>粉丝 <span><?php echo collection_count($user->ID, 'user'); ?></span></li>
            <li>文章 <span><?php echo count_user_posts($user->ID, 'post', true); ?></span></li>
            <li>评论 <span><?php echo count_user_comments($user->ID);?></span></li>
            <li>专辑 <span>0</span></li>
          </ul>
          <?php
          $des = get_user_meta($user->ID, 'location', true);
          if(empty($des)) $des = '这个人比较懒，什么都没有填写！';
          ?>
          <div class="ULIDes">简介：<?php echo $des; ?></div>
          <div class="">注册时间：<?php echo $user->data->user_registered; ?></div>
          <?php
          if(!empty($user->data->user_url)){
          ?>
          <div class="">网址：<?php echo $user->data->user_url; ?></div>
          <?php } ?>
        </div>
      </div>
      <?php } ?>
      <?php } ?>
      <?php }else{ ?>
      <div class="wp-info wp-info-default">
				<div class="wp-info-icon"><i class="fa fa-info"></i></div>
				<h3>这个页面没有内容！</h3>
				<p>这个页面的内容为空，可能是刚刚添加的分类或者新创建的标签页，管理员还没有添加内容，请过段时间再来访问。</p>
			</div>
      <?php } ?>

			<ul class="pagenavi">
				<?php searchnavi(5, ceil($res['total']/$rows)); ?>
			</ul>
		</div>
		</div>
		<?php get_sidebar(); ?>
	</div>
</div>
<?php get_footer();?>