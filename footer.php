</div> <!-- pjax-container end-->
<footer>
<ul class="flink">
<?php echo get_footer_menu(); ?>
</ul>
<p><?php echo get_option('footer_copyright'); ?> / <a href="http://beian.miit.gov.cn" target="blank"><?php echo get_option('zh_cn_l10n_icp_num'); ?></a><p>
</footer>
<div class="side-tool">
	<a href="/group/feedback"><i class="fa fa-send"></i></a>
	<a href="" class="backup"><i class="fa fa-angle-up"></i></a>
</div>
<script>
$(function(){
  
	function reply(){
		$(".reply_btn").each(function(){
			$(this).click(function(){
				$(this).parent().parent().after($('#comment_form'));
				$('#comment_form').addClass('wt-comments-text-reply')
				$('#comment_form').find('#comment_parent').val($(this).attr('this_comment_id'));
				$('<a href="javascript:" class="cancel_reply">取消回复</a>').appendTo('.wt-comments-submit').click(function(){
					$('.wt-comments-title').eq(0).after($('#comment_form'));
					$('#comment_form').removeClass('wt-comments-text-reply')
					$('.cancel_reply').remove();
				})
			})
		})
	}
	reply();

	//延迟加载
	function lazyload_img(){
		$("img").lazyload({ 
			placeholder : "/wp-content/themes/wtheme/images/placeholder.png",
			effect : "fadeIn",
		});
	}
	lazyload_img();
	
	//返回顶部
	$(".backup").click(function(){
		$("html,body").animate({scrollTop:0}, 1000);
		return false;
	})
	
  var following = false;
	
  function follow(){
		//关注
		$('.follow_btn').click(function(){
			follow_btn = $(this);
      <?php if(is_user_logged_in()){ ?>
      if(following) return;
      following = true;
			follow_btn.html('<i class="fa fa-spinner fa-pulse"></i> 提交中');
      $.ajax({
        type: 'post',
        url: '/wjson/',
        data: {
          model: 'collection',
          action: JSON.stringify({
            user_id: <?php global $current_user; echo $current_user->ID; ?>,
            item_id: follow_btn.attr('item_id'),
            item_type: follow_btn.attr('item_type')
          })
        },
        cache:false,
        dataType:'json',
        success: (res)=>{
          following = false;
          console.log(res);
          if(res.success){
            var follow_count = follow_btn.attr('follow_count') * 1
            if(res.data==1){
              follow_btn.html('<i class="fa fa-heart"></i> <span>已关注</span> ' +
              (follow_count + 1).toString());
              follow_btn.attr('follow_count', follow_count + 1)
            }else{
              follow_btn.html('<i class="fa fa-heart"></i> <span>关注</span> ' +
              (follow_count - 1).toString());
              follow_btn.attr('follow_count', follow_count - 1)
            }
          }else{
            alert(res.message);
          }
        }
      })
      <?php }else{ ?>
      follow_btn.html('请先登录');
      <?php } ?>
		})
	}
	
	function sumit_link(){
		var bp = document.createElement('script');
		var curProtocol = window.location.protocol.split(':')[0];
		if (curProtocol === 'https') {
			bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';        
		}
		else {
			bp.src = 'http://push.zhanzhang.baidu.com/push.js';
		}
		var s = document.getElementsByTagName("script")[0];
		s.parentNode.insertBefore(bp, s);
	}
	
	function tongji(){
		var _hmt = _hmt || [];
		(function() {
		  var hm = document.createElement("script");
		  hm.src = "https://hm.baidu.com/hm.js?6a11d57bf1c555a6598b7e72026ac1bc";
		  var s = document.getElementsByTagName("script")[0]; 
		  s.parentNode.insertBefore(hm, s);
		})();
	}
  
  // 文章侧边目录
  function sideToc(){
		if($('.side-toc').length==0) return;
    var tocTop = $('.side-toc').offset().top;
    let allH = [];
    $('.wt-content :header').each(function(){
      allH.push($(this).offset().top)
    });
    $('.singleToc').css('max-height', $(window).height()-160);
    $(window).scroll(function(){
      if($(window).scrollTop()>(tocTop + $(window).height())){
        $('.side-toc').addClass('side-toc-fixed');
      }else{
        $('.side-toc').removeClass('side-toc-fixed');
      }
      let thisLiIndex = 0;
      for(let i=0; i<allH.length; i++){
        //console.log($(window).scrollTop(), allH[i]);
        if($(window).scrollTop()<allH[i]){
          thisLiIndex = i;
          break;
        }
      }
      //console.log('--------------------');
      $('.singleToc li').removeClass('active');
      $('.singleToc li').eq(thisLiIndex).addClass('active');
    })
  }
  
  // 初始化
  follow();
  reply();
  lazyload_img();
  tongji();
  sumit_link();
  sideToc();

  $(document).pjax('a[pjax!="exclude"]', '#pjax-container', {
    fragment: '#pjax-container',
    timeout: 10000
  })
  $(document).on('pjax:send', function() {
    NProgress.start();
  })
  
  // 加载完毕
  $(document).on('pjax:complete', function() {
    NProgress.done();
    follow();
    reply();
    lazyload_img();
    tongji();
    sumit_link();
    window.MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
		sideToc();
  })

})
</script>
<?php wp_footer(); ?>
<style>
#pjax-container .MathJax_Display{
  display: inline !important;
}
</style>
</body>
</html>