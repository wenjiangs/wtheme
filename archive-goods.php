<?php
$goodsCat = get_terms(array('taxonomy'=>"goods-classify", 'hide_empty' => false));
// 查询
$args = array(
  'post_type' => 'goods',
  'showposts' => 200,
);
$goods = new WP_Query( $args );
get_header();
?>

  <div class="container">
    <div class="row">
      <div class="col-md-8 goodsLeft">
        <div class="agUser">
          <div class="agAvatar pull-left">
            <img src="/wp-content/themes/wtheme/images/avatars.png"/>
          </div>
          <div class="agText">
            <h2>先天的风</h2>
            <ul class="agLink">
              <li><a href="">我的积分</a></li>
              <li><a href="">收货地址</a></li>
              <li><a href="">我的兑换</a></li>
              <li><a href="">消息通知</a></li>
            </ul>
            <p>上次登录：2019年3月4日18:53:36</p>
          </div>
        </div>
        <div class="agGame">
          <div class="agGameItem">
            <img src="/wp-content/themes/wtheme/images/zhuan.png"/>
            <h3>积分大转盘</h3>
          </div>
          <div class="agGameItem">
            <img src="/wp-content/themes/wtheme/images/wei.png"/>
            <h3>积分大转盘</h3>
          </div>
          <div class="agGameItem">
            <img src="/wp-content/themes/wtheme/images/wei.png"/>
            <h3>积分大转盘</h3>
          </div>
          <div class="agGameItem">
            <img src="/wp-content/themes/wtheme/images/wei.png"/>
            <h3>积分大转盘</h3>
          </div>
        </div>
        <div class="wt-container">
          <div class="wt-nav-tabs">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#all">热门推荐</a></li>
              <?php foreach($goodsCat as $gci){ ?>
              <li><a href="#<?php echo $gci->slug; ?>"><?php echo $gci->name; ?></a></li>
              <?php } ?>
            </ul>
          </div>
          <div class="goodsHomeList">
            <?php foreach($goods->posts as $goodsItem){
              $goodsMeta = get_goods_meta($goodsItem->ID);
              $thisThumb = '/wp-content/themes/wtheme/images/placeholder.png';
              if(!empty($goodsMeta['thumb'])){
                $thisThumb = $goodsMeta['thumb'][0];
              }
            ?>
            <div class="ghlItem">
              <div class="listCollection"><i class="fa fa-heart"></i></div>
              <div class="ghlImg"><a href="<?php echo get_permalink($goodsItem->ID); ?>"><img src="<?php echo $thisThumb; ?>"/></a></div>
              <div class="prices">
                <span class="sale-prices"><i>￥</i><?php echo $goodsMeta['sale_prices']; ?></span>
                <span class="market-prices"><i>￥</i><?php echo $goodsMeta['market_prices']; ?></span>
                <span class="slaeNum pull-right">已兑 52</span>
              </div>
              <h3><a href="<?php echo get_permalink($goodsItem->ID); ?>"><?php echo $goodsItem->post_title; ?></a></h3>
            </div>
            <?php } ?>
          </div>
        </div>
      </div>
      <div class="col-md-4 goodsRight">
        <div class="side-tick-btn">
          <div class="side-tick-top">
            <span class="pull-left">我的积分 <i>0</i></span>
            <a class="pull-right" href="">积分明细</a>
          </div>
          <div class="sideTickBtn">
            <i class="fa fa-calendar-check-o pull-left"></i>
            <div>
              <h3>签到领积分</h3>
              <p>今日签到可以获得 7 积分</p>
            </div>
          </div>
        </div>
        <div class="side-get-tick">
          <div class="wt-container">
            <div class="mod-tit">
              <a href="/people" class="pull-right">更多 <i class="fa fa-angle-right"></i></a>
              <h3>赚积分</h3>
            </div>
            <div class="mod-con">
              <ul class="side-get-tick-list">
                <li>
                  <h3>每日签到</h3>
                  <p>连续签到积分越多，断签从头开始。</p>
                </li>
                <li>
                  <h3>文章分享（1/5）</h3>
                  <p>连续签到积分越多，断签从头开始。</p>
                </li>
                <li>
                  <h3>评论文章（1/10）</h3>
                  <p>连续签到积分越多，断签从头开始。</p>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php get_footer(); ?>