<?php
include('header.php');

// 获取分类
$lang = get_the_terms($post->ID, 'mywoks_language');
$industry = get_the_terms($post->ID, 'mywoks_industry');
$client = get_the_terms($post->ID, 'mywoks_client');
$type = get_the_terms($post->ID, 'mywoks_type');
$stack = get_the_terms($post->ID, 'mywoks_stack');

// 切割内容
$contentArray = explode('<!--more-->', $post->post_content);
$temArr = array();
foreach($contentArray as $cao){
  preg_match('#<h1>(.+?)</h1>#i', $cao, $match);
  $temArr[] = array(
    'title'=>$match[1],
    'content'=>trim(str_replace($match[0], '', $cao)),
  );
}
//print_r($temArr);
?>

<section id="works-single">
  <div class="container">
    <div class="row">
      <div class="col-lg-6">
        <div class="works-desc">
          <h2>项目介绍</h2>
          <div class="works-desc-content">
            <h3><?php echo $post->post_title; ?></h3>
            <p><?php echo $post->post_excerpt; ?></p>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="works-meta">
          <h2>服务类型</h2>
          <div class="works-meta-box">
            <dl>
              <dt>开发语言</dt>
              <dd>
                <?php foreach($lang as $item){ ?>
                <span><?php echo $item->name; ?></span>
                <?php } ?>
              </dd>
            </dl>
            <dl>
              <dt>所属行业</dt>
              <dd>
                <?php foreach($client as $item){ ?>
                <span><?php echo $item->name; ?></span>
                <?php } ?>
              </dd>
            </dl>
            <dl>
              <dt>用户端</dt>
              <dd>
                <?php foreach($industry as $item){ ?>
                <span><?php echo $item->name; ?></span>
                <?php } ?>
              </dd>
            </dl>
            <dl>
              <dt>服务类型</dt>
              <dd>
                <?php foreach($type as $item){ ?>
                <span><?php echo $item->name; ?></span>
                <?php } ?>
              </dd>
            </dl>
            <dl>
              <dt>技术栈</dt>
              <dd>
                <?php foreach($stack as $item){ ?>
                <span><?php echo $item->name; ?></span>
                <?php } ?>
              </dd>
            </dl>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="works-content">
  <div class="container">
    <div class="works-tabs">
      <?php foreach($temArr as $key => $item){ ?>
      <div <?php if($key==0) echo 'class="active"'; ?>><?php echo $item['title']; ?></div>
      <?php } ?>
    </div>
    <?php foreach($temArr as $key => $item){ ?>
    <div class="works-tab-content" <?php if($key==0) echo 'style="display:block"'; ?>>
      <?php
      if(strpos($item['content'], '[gallery') !== false){
        echo '<div class="gallery-box"><div class="gallery-wrap">';
        echo str_replace(array('<br style="clear: both" />', '<a'),
          array('', '<a rel="gallery_group"'), do_shortcode($item['content']));
        echo '</div></div>';
      }else{
        echo wpautop($item['content']);
      }
      ?>
    </div>
    <?php } ?>
  </div>
</section>

<?php include('footer.php'); ?>