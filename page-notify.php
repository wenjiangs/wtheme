<?php
global $current_user;
get_currentuserinfo();
@$notiy = get_notiys($current_user->ID, 1, 20);
get_header();
?>
<div class="container">
  <div class="row">
    <?php get_sidebar('user'); ?>
    <div class="col-md-9">
      <div class="wt-container">
        <h1 class="wt-setting-tit"><?php echo $post->post_title; ?></h1>
        <div class="notiyList">
          <?php foreach($notiy as $item){ ?>
          <?php if($item->id==0){ ?>
          <div class="notiyTime"><?php echo $item->time; ?></div>
          <?php }else{ ?>
          <div class="notiyItem">
            <div class="notiyAvatar">
              <img src="<?php echo $item->user->user_avatar?>"/>
            </div>
            <div class="notiyTxt <?php echo $item->type=="collection" && $item->content->item_type=="user"?'notiyTxtAlone':''?>">
              <h3>
              <span class="pull-right"><?php echo $item->send_data; ?></span>
              <?php echo $item->user->display_name; ?> <?php echo $item->title; ?>
              </h3>
              <?php if(isset($item->post)){ ?>
              <p><?php echo $item->post['title']; ?></p>
              <?php } ?>
              <?php if(isset($item->reply)){ ?>
              <p><?php echo $item->reply->comment_content; ?></p>
              <?php } ?>
            </div>
          </div>
          <?php } ?>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php get_footer();?>