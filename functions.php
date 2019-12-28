<?php
date_default_timezone_set("Asia/Shanghai");

include( 'Parsedown.php' );
include( 'common/verify.php' );
$Parsedown = new Parsedown();

function count_user_comments( $user_id ) {
	global $wpdb;
	return $wpdb->get_var( 'select count(*) from wp_comments where user_id = '.$user_id );
}

// 后台添加样式
add_action('admin_init', 'admin_wthteme_style');
function admin_wthteme_style(){
  wp_enqueue_style("wthteme", get_stylesheet_directory_uri().'/admin/style.css');
}

//面包屑导航
function breadcrumbs( $conscat = '' ) {
	global $post, $cat;
	echo '<a href="'.get_bloginfo( 'url' ).'">'.get_bloginfo( 'name' ).'</a> <small>></small> ';
	if ( is_singular() ) {
		the_category( ' <small>&gt;</small> ' );
		echo ' <small>&gt;</small> 文章详情';
	} elseif ( is_category() ) {
		$top_cat_info = get_category( $cat );
		if ( $top_cat_info->category_parent ) {
			$top_cat_info = get_category( $top_cat_info->category_parent );
			echo '<a href="/'.$top_cat_info->slug.'">'.$top_cat_info->name.'</a> <small>></small> ';
		}
		single_cat_title();
	} elseif ( is_tag() ) {
		single_tag_title();
	} elseif ( is_day() ) {
		the_time( 'Y年Fj日' );
	} elseif ( is_month() ) {
		the_time( 'Y年F' );
	} elseif ( is_year() ) {
		the_time( 'Y年' );
	}
}

//禁止非管理员访问后台
function wj_restrict_admin() {
	if ( ! current_user_can( 'manage_options' )
	     && $_SERVER['PHP_SELF'] != '/wp-admin/admin-ajax.php'
	     && $_SERVER['PHP_SELF'] != '/wp-admin/async-upload.php'
	     && $_SERVER['PHP_SELF'] != '/wp-admin/media-upload.php'
	) {
		global $current_user;
		get_currentuserinfo();
		wp_redirect( get_author_posts_url( $current_user->id ) );
	}
}

add_action( 'admin_init', 'wj_restrict_admin', 1 );

function performance() {
  $stat = sprintf('%d queries in %.3f seconds, using %.2fMB memory',
    get_num_queries(),
    timer_stop( 0, 3 ),
    memory_get_peak_usage() / 1024 / 1024
  );
  echo "<!-- {$stat} -->";
}
add_action( 'wp_footer', 'performance', 20 );

//禁止全英文评论
function scp_comment_post( $incoming_comment ) {
	$pattern = '/[一-龥]/u';
	if ( ! preg_match( $pattern, $incoming_comment['comment_content'] ) ) {
		wp_die( "I'm sorry，But you should type some Chinese word (like \"喜欢\") in your comment to pass the spam-check, thanks for your patience!  这是天朝，至少要有一个我们天朝的文字，纯表情也不行，<span style='color:red'>刷评论的烂JJ。</span>" );
	}

	//评论随机用户
	if ( isset( $_POST['is_sys_user'] ) && $_POST['is_sys_user'] == 1 ) {
		global $wpdb;
		$rand_user                                = $wpdb->get_var( 'SELECT * FROM wp_sys_user ORDER BY RAND() LIMIT 1' );
		$rand_user                                = get_user_by( 'id', $rand_user );
		$incoming_comment['user_ID']              = $rand_user->ID;
		$incoming_comment['user_id']              = $rand_user->ID;
		$incoming_comment['comment_author']       = $rand_user->data->display_name;
		$incoming_comment['comment_author_email'] = $rand_user->data->user_email;
	}
	//print_r($incoming_comment);
	//exit;
	return $incoming_comment;
}

add_filter( 'preprocess_comment', 'scp_comment_post' );

//建立菜单
function wt_register_nav_menus() {
	register_nav_menus( array(
		'header_menu' => '顶部菜单',
		'footer_menu' => '底部菜单'
	) );
}

add_action( 'init', 'wt_register_nav_menus' );

//分页函数
if ( ! function_exists( 'pagenavi' ) ) {
	function pagenavi( $p = 7, $max_page = '' ) {
		global $wp_query, $paged;
		if ( $max_page == '' ) {
			$max_page = $wp_query->max_num_pages;
		}
		echo '<li class="disabled">共 '.$max_page.' 页</li>';
		if ( $max_page == 1 ) {
			return;
		}
		if ( empty( $paged ) ) {
			$paged = 1;
		}
		if ( $paged > 1 ) {
			p_link( $paged - 1, '上一页', '&laquo; 上一页' );
		}
		for ( $i = $paged - $p + 3; $i <= $paged + $p - 3; $i ++ ) {
			if ( $i > 0 && $i <= $max_page ) {
				$i == $paged ? print "<li class='active'><a href='javascript:'>{$i}</a></li>" : p_link( $i, '', '' );
			}
		}
		if ( $paged < $max_page ) {
			p_link( $paged + 1, '下一页', '下一页 &raquo;' );
		}
	}

	function p_link( $i, $title = '', $linktype = '' ) {
		if ( $title == '' ) {
			$title = "第 {$i} 页";
		}
		if ( $linktype == '' ) {
			$linktext = $i;
      echo "<li class='naviNum'>";
		} else {
			$linktext = $linktype;
      echo '<li class="naviWord">';
		}
		echo "<a href='", esc_html( get_pagenum_link( $i ) ), "' title='{$title}'>{$linktext}</a></li>";
	}
}

//分页函数
if ( ! function_exists( 'searchnavi' ) ) {
	function searchnavi( $p = 7, $max_page = '' ) {
		global $wp_query, $spage;
		if ( $max_page == '' ) {
			$max_page = $wp_query->max_num_pages;
		}
		echo '<li class="disabled">共 '.$max_page.' 页</li>';
		if ( $max_page == 1 ) {
			return;
		}
		if ( empty( $spage ) ) {
			$spage = 1;
		}
		if ( $spage > 1 ) {
			search_link( $spage - 1, '上一页', '&laquo; 上一页' );
		}
		for ( $i = $spage - $p + 3; $i <= $spage + $p - 3; $i ++ ) {
			if ( $i > 0 && $i <= $max_page ) {
				$i == $spage ? print "<li class='active'><a href='javascript:'>{$i}</a></li>" : search_link( $i, '', '' );
			}
		}
		if ( $spage < $max_page ) {
			search_link( $spage + 1, '下一页', '下一页 &raquo;' );
		}
	}

	function search_link( $i, $title = '', $linktype = '' ) {
		if ( $title == '' ) {
			$title = "第 {$i} 页";
		}
		if ( $linktype == '' ) {
			$linktext = $i;
      echo "<li class='naviNum'>";
		} else {
			$linktext = $linktype;
      echo '<li class="naviWord">';
		}
    
    $qsArr = array();
    parse_str($_SERVER["QUERY_STRING"], $qsArr);
    $qsArr['spage'] = $i;
    $qs = http_build_query($qsArr);
    
		echo "<a href='".get_bloginfo('url').$_SERVER['REDIRECT_URL'].'?'.$qs."' title='{$title}'>{$linktext}</a></li>";
	}
}

//作者主页分页函数
if ( ! function_exists( 'author_pagenavi' ) ) {
	function author_pagenavi( $p = 7, $max_page = '' ) {
		global $wp_query, $leaf;
		if ( $max_page == '' ) {
			$max_page = $wp_query->max_num_pages;
		}
		echo '<li class="disabled">共 '.$max_page.' 页</li>';
		if ( $max_page == 1 ) {
			return;
		}
		if ( empty( $leaf ) ) {
			$leaf = 1;
		}
		if ( $leaf > 1 ) {
			author_link( $leaf - 1, '上一页', '&laquo; 上一页' );
		}
		for ( $i = $leaf - $p + 3; $i <= $leaf + $p - 3; $i ++ ) {
			if ( $i > 0 && $i <= $max_page ) {
				$i == $leaf ? print "<li class='active'><a href='javascript:'>{$i}</a></li>" : author_link( $i, '', '' );
			}
		}
		if ( $leaf < $max_page ) {
			author_link( $leaf + 1, '下一页', '下一页 &raquo;' );
		}
	}

	function author_link( $i, $title = '', $linktype = '' ) {
		$author_page = '';
		if ( isset( $_GET['a'] ) ) {
			$author_page = $_GET['a'];
		}
		if ( $linktype == '' ) {
			$linktext = $i;
      echo "<li class='naviNum'>";
		} else {
			$linktext = $linktype;
      echo '<li class="naviWord">';
		}
		if ( isset( $_GET['a'] ) ) {
			echo "<a href='?a=".$_GET['a']."&leaf=".$i."' title='{$title}'>{$linktext}</a>";
		} else {
			echo "<a href='?leaf=".$i."' title='{$title}'>{$linktext}</a>";
		}
    echo '</li>';
	}
}

//获取内容里面的图片
if(!function_exists('catch_that_image')){
  function catch_that_image($pt=array()) {
    if(empty($pt)){
      global $post;
    }else{
      $post = $pt;
    }
    $match_num = preg_match_all( '/<img(.+?)src=[\'"](.+?)[\'"](.+?)>/im', $post->post_content, $matche );
    if ( $match_num == 0 ) {
      $match_num = preg_match_all( '/<img(.+?)src=(.+?) alt(.+?)>/im', $post->post_content, $matche );
    }
    if ( $matche[2] ) {
      return $matche[2];
    }

    if ( $match_num == 0 ) {
      $match_num = preg_match_all( '/<img src="(.+?)">/im', $post->post_content, $matche );
    }
    if ( $matche[1] ) {
      return $matche[1];
    }
  }
}

//创建小组功能
function custom_post_topic() {
	$labels = array(
		'name'               => '话题',
		'singular_name'      => 'topic',
		'add_new'            => '创建话题',
		'add_new_item'       => '添加话题',
		'edit_item'          => '编辑',
		'new_item'           => '创建新的',
		'all_items'          => '所有话题',
		'view_item'          => '浏览话题',
		'search_items'       => '搜索话题',
		'not_found'          => '没有找到',
		'not_found_in_trash' => '回收站中没有找到',
		'parent_item_colon'  => '',
		'menu_name'          => '小组'
	);
	$args   = array(
		'labels'        => $labels,
		'hierarchical'  => true,
		'description'   => '话题',
		'public'        => true,
		'menu_position' => 5,
		'supports'      => array( 'title', 'editor', 'category', 'thumbnail', 'excerpt', 'comments' ),
		'has_archive'   => true,
		'menu_icon'     => 'dashicons-schedule'
	);
	register_post_type( 'topic', $args );

	//创建分类
	$labels = array(
		'name'                       => '小组',
		'singular_name'              => 'country',
		'search_items'               => '搜索',
		'popular_items'              => '热门',
		'all_items'                  => '所有',
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => '编辑',
		'update_item'                => '更新',
		'add_new_item'               => '添加',
		'new_item_name'              => '小组名称',
		'separate_items_with_commas' => '按逗号分开',
		'add_or_remove_items'        => '添加或删除',
		'choose_from_most_used'      => '从经常使用的类型中选择',
		'menu_name'                  => '小组分类',
	);
	$args   = array(
		'labels'        => $labels,
		'hierarchical'  => true,
		'description'   => '小组',
		'public'        => true,
		'menu_position' => 5,
		'supports'      => array( 'title', 'editor', 'category', 'thumbnail', 'excerpt', 'comments' ),
		'has_archive'   => true
	);
	register_taxonomy( 'group', 'topic', $args );
}

add_action( 'init', 'custom_post_topic' );

// 自定义文章类型伪静态
add_filter( 'post_type_link', 'custom_topic_link', 1, 3 );
function custom_topic_link( $link, $post = 0 ) {
	if ( $post->post_type == 'topic' ) {
		return home_url( 'group/topic-'.$post->ID.'.html' );
	} else if ( $post->post_type == 'product' ) {
		return home_url( 'product/item-'.$post->ID.'.html' );
	} else {
		return $link;
	}
}

add_action( 'init', 'custom_item_rewrites_init' );
function custom_item_rewrites_init() {
	add_rewrite_rule(
		'group/topic-([0-9]+)?.html$',
		'index.php?post_type=topic&p=$matches[1]',
		'top' );
	add_rewrite_rule(
		'product/item-([0-9]+)?.html$',
		'index.php?post_type=product&p=$matches[1]',
		'top' );
}

//创建文档中心功能
function custom_post_doc() {
	$labels = array(
		'name'               => '手册',
		'singular_name'      => 'doc',
		'add_new'            => '创建手册',
		'add_new_item'       => '添加手册',
		'edit_item'          => '编辑',
		'new_item'           => '创建新的',
		'all_items'          => '所有手册',
		'view_item'          => '浏览手册',
		'search_items'       => '搜索手册',
		'not_found'          => '没有找到',
		'not_found_in_trash' => '回收站中没有找到',
		'parent_item_colon'  => '',
		'menu_name'          => '文档'
	);
	$args   = array(
		'labels'        => $labels,
		'hierarchical'  => true,
		'description'   => '手册',
		'public'        => true,
		'menu_position' => 5,
		'supports'      => array( 'title', 'editor', 'category', 'thumbnail', 'excerpt', 'comments', 'custom-fields' ),
		'has_archive'   => true,
		'menu_icon'     => 'dashicons-editor-justify'
	);
	register_post_type( 'doc', $args );

	//创建分类
	$labels = array(
		'name'                       => '文档中心',
		'singular_name'              => 'country',
		'search_items'               => '搜索',
		'popular_items'              => '热门',
		'all_items'                  => '所有',
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => '编辑',
		'update_item'                => '更新',
		'add_new_item'               => '添加',
		'new_item_name'              => '小组名称',
		'separate_items_with_commas' => '按逗号分开',
		'add_or_remove_items'        => '添加或删除',
		'choose_from_most_used'      => '从经常使用的类型中选择',
		'menu_name'                  => '文档分类',
	);

	$args = array(
		'labels'        => $labels,
		'hierarchical'  => true,
		'description'   => '文档中心',
		'public'        => true,
		'menu_position' => 5,
		'supports'      => array( 'title', 'editor', 'category', 'thumbnail', 'excerpt', 'comments' ),
		'has_archive'   => true,
	);
	register_taxonomy( 'docs', 'doc', $args );
}

add_action( 'init', 'custom_post_doc' );

// 添加一个新栏目 上次登录
function add_last_login_column( $columns ) {
	$columns['last_activity'] = '上次登录';
	$columns['user_source'] = '来源';
	$columns['user_comment'] = '评论';
	return $columns;
}

add_filter( 'manage_users_columns', 'add_last_login_column' );

// 显示登录时间到新增栏目
function add_last_login_column_value( $value, $column_name, $user_id ) {
  global $wpdb;
	if ( 'last_activity' == $column_name ) {
		return $value = get_user_meta( $user_id, 'last_activity', true );
	}
	if ( 'user_source' == $column_name ) {
    $wxsp_openid = get_user_meta( $user_id, 'wxsp_openid', true );
    if($wxsp_openid){
      return '微信小程序';
    }
    $weibo_id = get_user_meta( $user_id, 'weibo_id', true );
    if($weibo_id){
      return '新浪微博';
    }
    $alipaysp_userid = get_user_meta( $user_id, 'alipaysp_userid', true );
    if($alipaysp_userid){
      return '支付宝小程序';
    }
    $unionid = get_user_meta( $user_id, 'unionid', true );
    $openid = get_user_meta( $user_id, 'openid', true );
    if($unionid || $openid){
      return 'QQ开放平台';
    }
    $qqsp_unionid = get_user_meta( $user_id, 'unionid', true );
    $qqsp_openid = get_user_meta( $user_id, 'openid', true );
    if($qqsp_unionid || $qqsp_openid){
      return 'QQ小程序';
    }
	}
  if ( 'user_comment' == $column_name ) {
    return $wpdb->get_var('select count(*) from wp_posts, wp_comments where wp_comments.user_id = '.$user_id.' and wp_posts.post_status = "publish" and wp_posts.post_type = "post" and wp_comments.comment_post_ID = wp_posts.ID');
  }
}

add_action( 'manage_users_custom_column', 'add_last_login_column_value', 10, 3 );


//发布时间显示为"几小时"前
if(!function_exists('time_since')){
  function time_since( $older_date, $newer_date = false ) {
    $chunks = array(
      array( 60 * 60 * 24 * 365, '年' ),
      array( 60 * 60 * 24 * 30, '月' ),
      array( 60 * 60 * 24 * 7, '周' ),
      array( 60 * 60 * 24, '天' ),
      array( 60 * 60, '小时' ),
      array( 60, '分钟' ),
    );

    $newer_date = ( $newer_date == false ) ? ( time() + ( 60 * 60 * get_option( "gmt_offset" ) ) ) : $newer_date;
    $since      = $newer_date - abs( strtotime( $older_date ) );

    //根据自己的需要调整时间段，下面的24则表示小时，根据需要调整吧
    if ( $since < 60 * 60 * 24 * 7 ) {
      for ( $i = 0, $j = count( $chunks ); $i < $j; $i ++ ) {
        $seconds = $chunks[ $i ][0];
        $name    = $chunks[ $i ][1];

        if ( ( $count = floor( $since / $seconds ) ) != 0 ) {
          break;
        }
      }

      $out = ( $count == 1 ) ? '1'.$name : "$count{$name}";

      return $out."以前";
    } else {
      return date( 'Y-m-d', strtotime($older_date) );
    }
  }
}

//获取某个小组的所有回复数量
function get_group_reply_number( $grpup_id ) {
	global $wpdb;
	$sql = "select count(*) from wp_term_relationships,wp_term_taxonomy,wp_posts,wp_comments where 
    wp_term_taxonomy.term_id = ".$grpup_id." and
    wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id and
    wp_term_relationships.object_id = wp_posts.ID and
    wp_comments.comment_post_ID = wp_posts.ID and
    wp_comments.comment_approved = 1";
	$reply_number = $wpdb->get_var( $sql );
	return $reply_number;
}

//登陆用户的评论无需审核
function loggedin_approved_comment( $approved ) {
	if ( ! $approved ) {
		if ( is_user_logged_in() ) {
			$approved = 1;
		}
	}

	return $approved;
}

add_action( 'pre_comment_approved', 'loggedin_approved_comment' );

//话题页面无需审核
function page_approved_comment( $approved, $commentdata ) {
    global $post;
	$post = get_post( $post );
	if ( ! empty( $post->ID ) && $post->post_type == 'topic' ) {
		$approved = 0;
		if ( $user_id = $commentdata['user_id'] ) {
			$user = get_userdata( $user_id );
			if ( $user_id == $post->post_author || $user->has_cap( 'moderate_comments' ) ) {
				$approved = 1;
			}
		}
	}

	return $approved;
}

add_action( 'pre_comment_approved', 'page_approved_comment', 10, 2 );

//在[媒体库]只显示用户上传的文件
function only_youself_upload_media_library( $wp_query ) {
	if ( $_SERVER['PHP_SELF'] == '/wp-admin/async-upload.php' || $_SERVER['PHP_SELF'] == '/wp-admin/media-upload.php' ) {
		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'manage_media_library' ) ) {
			global $current_user;
			$wp_query->set( 'author', $current_user->id );
		}
	}
}

add_filter( 'parse_query', 'only_youself_upload_media_library' );

//常规页面添加自定义字段
add_filter( 'admin_init', 'register_fields' );
function register_fields() {
	register_setting( 'general', 'footer_copyright', 'footer_copyright' );
	add_settings_field( 'fav_color', '<label for="footer_copyright">底部版权</label>', 'fields_html', 'general' );
}

function fields_html() {
	$footer_copyright = get_option( 'footer_copyright', '' );
	echo '<input type="text" id="footer_copyright" name="footer_copyright" value="'.$footer_copyright.'" />';
}

// 主动推送文章到百度
function post_baidu( $ID, $post, $api ) {
	global $wpdb;
	$urls[] = get_permalink( $ID );
	$ch      = curl_init();
	$options = array(
		CURLOPT_URL            => $api,
		CURLOPT_POST           => true,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POSTFIELDS     => implode( "\n", $urls ),
		CURLOPT_HTTPHEADER     => array( 'Content-Type: text/plain' ),
	);
	curl_setopt_array( $ch, $options );
	$result = curl_exec( $ch );
  // 将推送的结果保存到数据库中
	$wpdb->query( "insert into wp_submit_baidu values (NULL, ".$ID.", '".json_encode( $urls )."', '".$result."', ".time().", '')" );
	curl_close( $ch );
}

function publish_baidu($ID){
  $api = 'http://data.zz.baidu.com/urls?site=www.wenjiangs.com&token=x33AJZVnADCXKKmt';
  $post = get_post($ID);
  post_baidu($ID, $post, $api);
  
  // 文章审核通过加金币
  $integralCount = getUserIntegralList($post->post_author, 1, 10, 'publish_'.$post->post_type, date('Y-m-d'));
  $post_type = array(
    'post' => '发布文章',
    'topic' => '发布话题',
    'doc' => '发布专栏文章',
  );
  if(count($integralCount)<1){
    addUserIntegral($post->post_author, $post->ID, 'publish_'.$post->post_type, 20, $post_type[$post->post_type]);
  }
  // 文章审核通过邮件通知
  // newPostNotify($ID, $post);
}

add_action( 'publish_post', 'publish_baidu', 10, 2 );
// add_action( 'publish_topic', 'publish_baidu', 10, 2 );
// add_action( 'publish_doc', 'publish_baidu', 10, 2 );

function newPostNotify($ID, $post) {
  if( wp_is_post_revision($ID) ) return;
  global $wpdb;
  if ( $post->post_status == 'publish' && $_POST['original_post_status'] != 'publish' ) {
    //获取管理员、编辑
    $editors = $wpdb->get_col("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'wp_user_level' AND meta_value >= 7 ORDER BY user_id");
    if(in_array($post->post_author, $editors)){
      //编辑和管理员自己发布的文章除外
      return ;
    }
    // 读数据库，获取所有用户的email
    $wp_user_email = $wpdb->get_results("SELECT DISTINCT user_email FROM $wpdb->users WHERE id=".$post->post_author);
    // 依次给每个Email发邮件
    foreach ( $wp_user_email as $email ) {
      $subject = '文江博客文章通过审核邮件';
      // 邮件内容：新文章网址：+ URL
      $message = '通过审核的文章地址是：' . get_permalink($post_ID);
      // 发邮件
      wp_mail($email->user_email, $subject, $message);
    }
  }
}

//判断是否为蜘蛛
function is_spider() {
	//定义搜索引擎蜘蛛
	$robots = array(
		"Baiduspider",
		"googlebot",
		"sosospider",
		"360spider",
		"slurp",
		"yodaobot",
		"sogou",
		"msnbot",
		"bingbot"
	);
	$agent  = strtolower( $_SERVER["HTTP_USER_AGENT"] );//获取访问者浏览器相关参数

	//搜索引擎蜘蛛爬虫判断
	foreach ( $robots as $user_agent ) {
		if ( strripos( $agent, $user_agent ) > 0 ) {
			return true;
		}
	}
}

//提取图片
function extract_image( $con ) {
	$matches   = array();
	$match_num = preg_match_all( '/<img(.+?)src=[\'"](.+?)[\'"](.+?)>/im', $con, $matches );
	if ( $match_num == 0 ) {
		$match_num = preg_match_all( '/<img(.+?)src=(.+?) alt(.+?)>/im', $con, $matches );
	}
	if ( $match_num == 0 ) {
		$match_num = preg_match_all( '/<img src="(.+?)">/im', $con, $matches );
	}

	return $matches;
}

function ismobile() {
	// 如果有HTTP_X_WAP_PROFILE则一定是移动设备
	if ( isset ( $_SERVER['HTTP_X_WAP_PROFILE'] ) ) {
		return true;
	}

	//此条摘自TPM智能切换模板引擎，适合TPM开发
	if ( isset ( $_SERVER['HTTP_CLIENT'] ) && 'PhoneClient' == $_SERVER['HTTP_CLIENT'] ) {
		return true;
	}
	//如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
	if ( isset ( $_SERVER['HTTP_VIA'] ) ) //找不到为flase,否则为true
	{
		return stristr( $_SERVER['HTTP_VIA'], 'wap' ) ? true : false;
	}
	//判断手机发送的客户端标志,兼容性有待提高
	if ( isset ( $_SERVER['HTTP_USER_AGENT'] ) ) {
		$clientkeywords = array(
			'nokia',
			'sony',
			'ericsson',
			'mot',
			'samsung',
			'htc',
			'sgh',
			'lg',
			'sharp',
			'sie-',
			'philips',
			'panasonic',
			'alcatel',
			'lenovo',
			'iphone',
			'ipod',
			'blackberry',
			'meizu',
			'android',
			'netfront',
			'symbian',
			'ucweb',
			'windowsce',
			'palm',
			'operamini',
			'operamobi',
			'openwave',
			'nexusone',
			'cldc',
			'midp',
			'wap',
			'mobile'
		);
		//从HTTP_USER_AGENT中查找手机浏览器的关键字
		if ( preg_match( "/(".implode( '|', $clientkeywords ).")/i", strtolower( $_SERVER['HTTP_USER_AGENT'] ) ) ) {
			return true;
		}
	}
	//协议法，因为有可能不准确，放到最后判断
	if ( isset ( $_SERVER['HTTP_ACCEPT'] ) ) {
		// 如果只支持wml并且不支持html那一定是移动设备
		// 如果支持wml和html但是wml在html之前则是移动设备
		if ( ( strpos( $_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml' ) !== false ) && ( strpos( $_SERVER['HTTP_ACCEPT'], 'text/html' ) === false || ( strpos( $_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml' ) < strpos( $_SERVER['HTTP_ACCEPT'], 'text/html' ) ) ) ) {
			return true;
		}
	}

	return false;
}

function my_do_content( $content ) {
	//搜索引擎就不替换图片地址
	if ( ! is_spider() ) {
		if ( ! ismobile() ) {
			/*替换图片地址懒加载*/
			$matches   = array();
			$match_num = preg_match_all( '/<img(.+?)src=[\'"](.+?)[\'"](.+?)>/im', $content, $matches );
			if ( $match_num == 0 ) {
				$match_num = preg_match_all( '/<img(.+?)src=(.+?) alt(.+?)>/im', $content, $matches );
			}
			if ( $match_num == 0 ) {
				$match_num = preg_match_all( '/<img src="(.+?)">/im', $content, $matches );
			}

			if ( is_single() && $match_num ) {
				foreach ( $matches[2] as $num => $title ) {
					$content_url = $matches[2][ $num ];
					if ( ! ( stripos( $content_url, 'static.wenjiangs.com' ) === false ) ) {
						$thumb_file_info = pathinfo( $content_url );
						if ( stripos( $content_url, '-wjcontent' ) === false ) {
							$content_url = $content_url.'-wjcontent';
						}
						//else{
						//$thumb_file_extension = explode('-', $thumb_file_info['extension']);
						//$content_url = $thumb_file_info['dirname'].'/'.$thumb_file_info['filename'].'.'.$thumb_file_extension[0].'-wjcontent';
						//}
					}
					$content = str_replace( $matches[0][ $num ], '<img'.$matches[1][ $num ].'data-original="'.$content_url.'"'.$matches[3][ $num ].'>', $content );
				}
			}
		}
	}

	//查找外链
	$host = $_SERVER['HTTP_HOST'];
	$reg  = '/href=[\'"]http(?:s?):\/\/((?:[A-za-z0-9-]+\.)+[A-za-z]{2,4})(.+?)[\'"]/';
	preg_match_all( $reg, $content, $match );
	foreach ( $match[0] as $key => $value ) {
		if ( $match[1][ $key ] != $host ) {
			$content = str_replace( $value, 'external-link="true" '.$value, $content );
		}
	}
  
  // 如果图片的 alt 为空，就替换为文章标题
  $content = str_replace('alt=""', 'alt="'.$GLOBALS['post']->post_title.'"', $content);
  
  // 文章内容里面的链接不适用pjax加载
  $content = str_replace('<a', '<a pjax="exclude"', $content);
  
  // 文章内目录
  $content = singleToc($content, true);

	return $content;
}

add_filter( 'the_content', 'my_do_content' );

//更换登录logo链接
add_filter( "login_headerurl", create_function( false, "return get_bloginfo('url');" ) );

//更换登录logo描述
add_filter( "login_headertitle", create_function( false, "return get_bloginfo('description');" ) );

// 自定义logo css样式
function custom_login_style() {
	echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo( 'template_directory' ).'/style.css" />
	<link href="'.get_bloginfo( 'template_directory' ).'/css/font-awesome.min.css" rel="stylesheet" type="text/css">';
}

add_action( 'login_head', 'custom_login_style' );

//定制登录页面底部文字
add_action( 'login_footer', 'wt_login_footer' );
function wt_login_footer() {
	?>
    <div class="login-sns">
        <p>您还可以通过以下方式创建账号</p>
        <div>
          <a href="/oauth/qq/" class="button"><i class="fa fa-qq"></i> QQ</a>
          <a href="/oauth/weibo/" class="button"><i class="fa fa-weibo"></i> 微博</a>
          <a href="/scanning-login" class="button"><i class="fa fa-weixin"></i> 小程序</a>
        </div>
        <b>如果你继续使用社交账号，并且还没有 Wenjiangs.com 账户，则需要创建一个，并同意我们的
        <a href="http://www.wenjiangs.com/agreement" target="blank">用户协议</a>。</b>
    </div>
	<?php
}

//添加后台管理页面
add_action( 'admin_menu', 'register_my_custom_submenu_page' );
function register_my_custom_submenu_page() {
	add_submenu_page( 'users.php', '登录日志', '登录日志', 'manage_options', 'wt_loginlog', 'wt_loginlog' );
	add_submenu_page( 'users.php', '通知消息', '通知消息', 'manage_options', 'wjnotiy', 'wjnotiy' );
	add_submenu_page( 'edit.php', '百度提交', '百度提交', 'manage_options', 'wt_submit_baidu', 'wt_submit_baidu' );
	add_submenu_page( 'tools.php', '内容举报', '内容举报', 'manage_options', 'accusation', 'accusation' );
}

function wt_loginlog() {
	include( 'admin/loginlog.php' );
}

function wt_submit_baidu() {
	include( 'admin/submit_baidu.php' );
}

function accusation() {
	include( 'admin/accusation.php' );
}

function wjnotiy() {
	include( 'admin/wjnotiy.php' );
}

//获取用户IP
function get_user_ip() {
	$unknown = 'unknown';
	if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp( $_SERVER['HTTP_X_FORWARDED_FOR'], $unknown ) ) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) && $_SERVER['REMOTE_ADDR'] && strcasecmp( $_SERVER['REMOTE_ADDR'], $unknown ) ) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	/**
	 * 处理多层代理的情况
	 * 或者使用正则方式：$ip = preg_match("/[\d\.]{7,15}/", $ip, $matches) ? $matches[0] : $unknown;
	 */
	if ( false !== strpos( $ip, ',' ) ) {
		$ip = reset( explode( ',', $ip ) );
	}

	return $ip;
}

//根据IP返回物理地址
function get_address( $user_ip ) {
	$address_json = file_get_contents( 'http://ip.taobao.com/service/getIpInfo.php?ip='.$user_ip );
	$address_arr  = json_decode( $address_json );

	return $address_arr->data->region.' '.$address_arr->data->city;
}

//记录登录日志
add_action( 'wp_login_failed', 'LoginFailed', 10, 1 );
add_action( 'wp_login', 'LoginSuccess', 10, 1 );
function LoginFailed( $user_login ) {
	global $wpdb;
	$insert = "INSERT INTO wp_loginlog VALUES (NULL, '".$user_login."', '".( time() + 3600 * 8 )."', '".get_user_ip()."', '".$_SERVER['HTTP_USER_AGENT']."', '0')";
	$wpdb->query( $insert );
}

function LoginSuccess( $user_login ) {
	global $wpdb;
	$insert = "INSERT INTO wp_loginlog VALUES (NULL, '".$user_login."', '".( time() + 3600 * 8 )."', '".get_user_ip()."', '".$_SERVER['HTTP_USER_AGENT']."', '1')";
	$wpdb->query( $insert );
}

//自定义标签字段
add_action( 'post_tag_edit_form_fields', 'categorykeywordsedit' );
function categorykeywordsedit( $taxonomy ) {
	wp_enqueue_script( 'media-upload' );
	wp_enqueue_script( 'thickbox' );
	wp_enqueue_script( 'my-upload' );
	wp_enqueue_style( 'thickbox' );
	?>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="tag-keywords">关键词</label></th>
    <td><input type="text" name="tag-keywords" id="tag-keywords"
               value="<?php echo get_term_meta( $taxonomy->term_id, 'keywords', true ); ?>"/></td>
  </tr>
  <tr class="form-field">
    <th scope="row" valign="top"><label for="tag-keywords">缩略图</label></th>
    <td><?php
  $tag_thumb = get_term_meta( $taxonomy->term_id, 'thumb', true );
  if ( $tag_thumb ) {
    echo '<img src="'.$tag_thumb.'" /> <br/>';
  }
  ?><input id="tag-thumb" name="tag-thumb" type="text" value="<?php echo $tag_thumb; ?>"/>
      <p><input id="upload_image_button" class="button button-primary" type="button" value="上传图片"/></p>
      <script>
        jQuery(document).ready(function () {
          jQuery('#upload_image_button').click(function () {
            tb_show('', '<?php echo admin_url(); ?>media-upload.php?type=image&amp;TB_iframe=true');
            return false;
          });
          window.send_to_editor = function (html) {
            imgurl = jQuery('img', html).attr('src');
            jQuery('#tag-thumb').val(imgurl);
            tb_remove();
          }
        });
      </script>
    </td>
  </tr>
	<?php
}

add_action( 'edit_term', 'categorykeywordssave' );
add_action( 'create_term', 'categorykeywordssave' );
function categorykeywordssave( $term_id ) {
	if ( isset( $_POST['tag-keywords'] ) ) {
		update_term_meta( $term_id, 'keywords', $_POST['tag-keywords'] );
	}
	if ( isset( $_POST['tag-thumb'] ) ) {
		update_term_meta( $term_id, 'thumb', $_POST['tag-thumb'] );
	}
}

//分类添加自定义字段
add_action( 'category_edit_form_fields', 'categorykeywordsedit' );


//WordPress 个人资料添加额外的字段
add_action( 'show_user_profile', 'extra_user_profile_fields' );
add_action( 'edit_user_profile', 'extra_user_profile_fields' );

function extra_user_profile_fields( $user ) {

	wp_enqueue_script( 'media-upload' );
	wp_enqueue_script( 'thickbox' );
	wp_enqueue_script( 'my-upload' );
	wp_enqueue_style( 'thickbox' );

	?>
  <h3>用户头像</h3>

  <table class="form-table">
    <tr>
        <th><label for="twitter">用户头像</label></th>
        <td><?php
    $user_avatar = get_usermeta( $user->ID, 'user_avatar', true );
    if ( $user_avatar ) {
      echo '<img src="'.$user_avatar.'" /> <br/>';
    }
    ?><input id="user_avatar" class="regular-text code" name="user_avatar" type="text"
                 value="<?php echo $user_avatar; ?>"/>
        <p><input id="upload_image_button" class="button button-primary" type="button" value="上传图片"/></p>
        <script>
          jQuery(document).ready(function () {
            jQuery('#upload_image_button').click(function () {
              tb_show('', '<?php echo admin_url(); ?>media-upload.php?type=image&amp;TB_iframe=true');
              return false;
            });
            window.send_to_editor = function (html) {
              imgurl = jQuery('img', html).attr('src');
              jQuery('#user_avatar').val(imgurl);
              tb_remove();
            }
          });
        </script>
      </td>
    </tr>
  </table>
<?php }

add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );

function save_extra_user_profile_fields( $user_id ) {

	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}
	update_usermeta( $user_id, 'user_avatar', $_POST['user_avatar'] );
}

//更改默认头像
if(!function_exists('wt_get_avatar')){
  function wt_get_avatar( $avatar, $user_id ) {
    $user_avatar = get_user_meta( $user_id, 'user_avatar', true );
    if ( $user_avatar ) {
      return '<img src="'.$user_avatar.'" width="96" height="96"/>';
    } else {
      return '<img src="'.get_bloginfo('url').'/wp-content/themes/wtheme/images/avatars.png" width="96" height="96"/>';
    }
  }
}

add_filter( 'get_avatar', 'wt_get_avatar', 1, 5 );

//增强用户搜索
if(!function_exists('wt_enhance_user_query')){
  add_action( 'pre_user_query', 'wt_enhance_user_query', 9 );
  function wt_enhance_user_query( $query ) {

    if ( ! empty( $query->query_vars['search'] ) ) {
      global $wpdb;
      $keyword             = $query->query_vars['search'];
      $keyword             = str_replace( '*', '', $keyword );
      $query->query_where  = $wpdb->prepare( " WHERE 1=1 AND (user_login LIKE %s OR user_email LIKE %s OR user_nicename LIKE %s OR display_name LIKE %s OR UM.meta_value LIKE  %s) AND UM.meta_key='nickname'", "%".$keyword."%", "%".$keyword."%", "%".$keyword."%", "%".$keyword."%", "%".$keyword."%" );
      $query->query_fields .= " ,$wpdb->users.display_name, UM.meta_value as nickname";
      $query->query_from   .= " left join $wpdb->usermeta UM on ($wpdb->users.ID=UM.user_id) ";
    }
  }
}

//判断用户是否关注某个项目
function is_follow_item( $user_id, $item_id, $item_type ) {
  $sql = 'select id from wp_user_item_taxonomy where user_id = '.$user_id.' and item_id = '.$item_id.' and item_type = "'.$item_type.'"';
  global $wpdb;
  $isFollow = $wpdb->get_var($sql);
  if(empty($isFollow)){
    return false;
  }else{
    return true;
  }
}

//文章类型对应的名称
$post_type_name = array(
	'post'    => '文章',
	'topic'   => '社区话题',
	'doc'     => '手册',
);

if ( ! function_exists( 'size2mb' ) ) {
  //计算文件大小，字节到 mb
  function size2mb( $size, $digits = 2 ) { //digits，要保留几位小数
    $unit = array( '', 'K', 'M', 'G', 'T', 'P' ); //单位数组，是必须1024进制依次的哦。
    $base = 1024; //对数的基数
    $i    = floor( log( $size, $base ) ); //字节数对1024取对数，值向下取整。
    return round( $size / pow( $base, $i ), $digits ).' '.$unit[ $i ].'B';
  }
}

function get_custom_post_type_tags( $id, $taxonomy ) {
	global $wpdb;
	$sql      = 'select * from wp_term_relationships, wp_term_taxonomy where wp_term_relationships.object_id = '.$id.' and wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id and taxonomy = "'.$taxonomy.'"';
	$pre_term = $wpdb->get_results( $sql );
	$terms    = array();
	foreach ( $pre_term as $item ) {
		$terms[] = get_term( $item->term_id, $taxonomy );
	}

	return $terms;
}

/**
 * 获取客户端浏览器信息 添加win10 edge浏览器判断
 * @param  user agent
 * @return string
 */
function get_broswer( $sys ) {
	if ( stripos( $sys, "Firefox/" ) > 0 ) {
		preg_match( "/Firefox\/([^;)]+)+/i", $sys, $b );
		$exp[0] = "Firefox";
		$exp[1] = $b[1];  //获取火狐浏览器的版本号
	} elseif ( stripos( $sys, "Maxthon" ) > 0 ) {
		preg_match( "/Maxthon\/([\d\.]+)/", $sys, $aoyou );
		$exp[0] = "傲游";
		$exp[1] = $aoyou[1];
	} elseif ( stripos( $sys, "MSIE" ) > 0 ) {
		preg_match( "/MSIE\s+([^;)]+)+/i", $sys, $ie );
		$exp[0] = "IE";
		$exp[1] = $ie[1];  //获取IE的版本号
	} elseif ( stripos( $sys, "OPR" ) > 0 ) {
		preg_match( "/OPR\/([\d\.]+)/", $sys, $opera );
		$exp[0] = "Opera";
		$exp[1] = $opera[1];
	} elseif ( stripos( $sys, "Edge" ) > 0 ) {
		//win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
		preg_match( "/Edge\/([\d\.]+)/", $sys, $Edge );
		$exp[0] = "Edge";
		$exp[1] = $Edge[1];
	} elseif ( stripos( $sys, "Chrome" ) > 0 ) {
		preg_match( "/Chrome\/([\d\.]+)/", $sys, $google );
		$exp[0] = "Chrome";
		$exp[1] = $google[1];  //获取google chrome的版本号
	} elseif ( stripos( $sys, 'rv:' ) > 0 && stripos( $sys, 'Gecko' ) > 0 ) {
		preg_match( "/rv:([\d\.]+)/", $sys, $IE );
		$exp[0] = "IE";
		$exp[1] = $IE[1];
	} else {
		return '-';
	}

	return $exp[0].' '.$exp[1];
}

/**
 * 获取客户端操作系统信息包括 win10
 * @param  user agent
 * @return string
 */
function get_os( $agent ) {
	$os = false;
	if ( preg_match( '/win/i', $agent ) && strpos( $agent, '95' ) ) {
		$os = 'Windows 95';
	} else if ( preg_match( '/win 9x/i', $agent ) && strpos( $agent, '4.90' ) ) {
		$os = 'Windows ME';
	} else if ( preg_match( '/win/i', $agent ) && preg_match( '/98/i', $agent ) ) {
		$os = 'Windows 98';
	} else if ( preg_match( '/win/i', $agent ) && preg_match( '/nt 6.0/i', $agent ) ) {
		$os = 'Windows Vista';
	} else if ( preg_match( '/win/i', $agent ) && preg_match( '/nt 6.1/i', $agent ) ) {
		$os = 'Windows 7';
	} else if ( preg_match( '/win/i', $agent ) && preg_match( '/nt 6.2/i', $agent ) ) {
		$os = 'Windows 8';
	} else if ( preg_match( '/win/i', $agent ) && preg_match( '/nt 10.0/i', $agent ) ) {
		$os = 'Windows 10';#添加win10判断
	} else if ( preg_match( '/win/i', $agent ) && preg_match( '/nt 5.1/i', $agent ) ) {
		$os = 'Windows XP';
	} else if ( preg_match( '/win/i', $agent ) && preg_match( '/nt 5/i', $agent ) ) {
		$os = 'Windows 2000';
	} else if ( preg_match( '/win/i', $agent ) && preg_match( '/nt/i', $agent ) ) {
		$os = 'Windows NT';
	} else if ( preg_match( '/win/i', $agent ) && preg_match( '/32/i', $agent ) ) {
		$os = 'Windows 32';
	} else if ( preg_match( '/linux/i', $agent ) ) {
		$os = 'Linux';
	} else if ( preg_match( '/unix/i', $agent ) ) {
		$os = 'Unix';
	} else if ( preg_match( '/sun/i', $agent ) && preg_match( '/os/i', $agent ) ) {
		$os = 'SunOS';
	} else if ( preg_match( '/ibm/i', $agent ) && preg_match( '/os/i', $agent ) ) {
		$os = 'IBM OS/2';
	} else if ( preg_match( '/Mac/i', $agent ) && preg_match( '/PC/i', $agent ) ) {
		$os = 'Macintosh';
	} else if ( preg_match( '/PowerPC/i', $agent ) ) {
		$os = 'PowerPC';
	} else if ( preg_match( '/AIX/i', $agent ) ) {
		$os = 'AIX';
	} else if ( preg_match( '/HPUX/i', $agent ) ) {
		$os = 'HPUX';
	} else if ( preg_match( '/NetBSD/i', $agent ) ) {
		$os = 'NetBSD';
	} else if ( preg_match( '/BSD/i', $agent ) ) {
		$os = 'BSD';
	} else if ( preg_match( '/OSF1/i', $agent ) ) {
		$os = 'OSF1';
	} else if ( preg_match( '/IRIX/i', $agent ) ) {
		$os = 'IRIX';
	} else if ( preg_match( '/FreeBSD/i', $agent ) ) {
		$os = 'FreeBSD';
	} else if ( preg_match( '/teleport/i', $agent ) ) {
		$os = 'teleport';
	} else if ( preg_match( '/flashget/i', $agent ) ) {
		$os = 'flashget';
	} else if ( preg_match( '/webzip/i', $agent ) ) {
		$os = 'webzip';
	} else if ( preg_match( '/offline/i', $agent ) ) {
		$os = 'offline';
	} else {
		$os = '-';
	}

	return $os;
}

/* 将base64代码转换成图片
 * @param  [Base64] $base64_image_content [要保存的Base64]
 * @param  [目录] $path [要保存的路径，包含图片名，不包含扩展名] uploads/avatar2/1
 */
function base64_image_content( $base64_image_content, $path ) {
	if ( preg_match( '/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result ) ) {
		$type = $result[2];
		$path = $path.'.'.$type;
		if ( file_put_contents( $path, base64_decode( str_replace( $result[1], '', $base64_image_content ) ) ) ) {
			return $type;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

// 随机字符串
if(!function_exists('randStr')){
  function randStr( $len ) {
    $chars  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
    $string = '';
    for ( $i = 0; $i < $len; $i ++ ) {
      $string .= $chars[ rand( 1, strlen( $chars ) ) ];
    }

    return $string;
  }
}

// 随机数字
function randNum($len){
  $chars = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
  $charsLen = count($chars) - 1;
  shuffle($chars);
  $output = "";
  for ($i=0; $i<$len; $i++){
    $output .= $chars[mt_rand(0, $charsLen)];
  }
  return $output;
}

//排序
add_action( 'load-edit.php', 'custom_topic_sort_load' );
function custom_topic_sort_load() {
	add_filter( 'request', 'custom_topic_sort' );
}

function custom_topic_sort( $vars ) {
	if ( isset( $vars['post_type'] ) && ! ( 'post' == $vars['post_type'] ) ) {
		$vars['order']   = 'desc';
		$vars['orderby'] = 'menu_order title';
	}

	return $vars;
}

function get_top_menu() {
  // 是否有缓存
  $header_menu = wp_cache_get('header_menu', 'wj_cache');
  if($header_menu){
    return $header_menu;
  }
	$defaults = array(
		'theme_location'  => 'header_menu',
		'container'       => '',
		'container_class' => '',
		'container_id'    => '',
		'menu_class'      => 'menu',
		'menu_id'         => '',
		'echo'            => false,
		'fallback_cb'     => 'wp_page_menu',
		'before'          => '',
		'after'           => '',
		'link_before'     => '',
		'link_after'      => '',
		'items_wrap'      => '%3$s',
		'depth'           => 0,
		'walker'          => ''
	);
	$topnav   = wp_nav_menu( $defaults );
	//替换一些内容

	$topnav = str_replace( array( 'current-menu-item', "<a>", 'sub-menu', 'menu-item-has-children' ), array(
		'active',
		'<a href="javascript:" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">',
		'dropdown-menu navbar-dropdown',
		'dropdown'
	), $topnav );

  // 缓存数据
  wp_cache_set('header_menu', $topnav, 'wj_cache');
	return $topnav;
}

function get_footer_menu() {
  // 是否有缓存
  $footer_menu = wp_cache_get('footer_menu', 'wj_cache');
  if($footer_menu){
    return $footer_menu;
  }
	$defaults = array(
		'theme_location'  => 'footer_menu',
		'container'       => '',
		'container_class' => '',
		'container_id'    => '',
		'menu_class'      => 'menu',
		'menu_id'         => '',
		'echo'            => false,
		'fallback_cb'     => 'wp_page_menu',
		'before'          => '',
		'after'           => '',
		'link_before'     => '',
		'link_after'      => '',
		'items_wrap'      => '%3$s',
		'depth'           => 0,
		'walker'          => ''
	);
	$menu = wp_nav_menu( $defaults );

  // 缓存数据
  wp_cache_set('footer_menu', $menu, 'wj_cache');
	return $menu;
}

function get_shop_menu() {
	$defaults = array(
		'theme_location'  => 'shop_menu',
		'container'       => '',
		'container_class' => '',
		'container_id'    => '',
		'menu_class'      => 'menu',
		'menu_id'         => '',
		'echo'            => false,
		'fallback_cb'     => 'wp_page_menu',
		'before'          => '',
		'after'           => '',
		'link_before'     => '',
		'link_after'      => '',
		'items_wrap'      => '%3$s',
		'depth'           => 0,
		'walker'          => ''
	);
	$menu = wp_nav_menu( $defaults );

	return $menu;
}

function creat_thumb( $imageUrl, $w = 200, $h = 150 ) {

  // 七牛云直接使用缩略图
	if(strpos($imageUrl, 'static.wenjiangs.com') !== false){
	  $imageUrl = str_replace('-wjcontent', '', $imageUrl);
	  return $imageUrl.'-wjthumb';
  }

  // 外链图片跳过
	if(!(strpos($imageUrl, str_replace(array('http://', 'https://'), '', get_bloginfo('url'))) !== false)){
	  return;
  }

  $pathInfo = pathinfo($imageUrl);
  
  $url = get_bloginfo('url');
  $url2 = str_replace('https://', 'http://', $url);
  $localPath = str_replace($url, '', $pathInfo['dirname']);
  $localPath = str_replace($url2, '', $localPath);
  $localPath = $_SERVER['DOCUMENT_ROOT'].$localPath;
  $localPathOld = $localPath;
	$localPath .= '/'.$pathInfo['filename'].'_'.$w.'x'.$h.'.'.$pathInfo['extension'];
	$localPathOld .= '/'.$pathInfo['filename'].'.'.$pathInfo['extension'];

  $imagePath = $pathInfo['dirname'].'/'.$pathInfo['filename'].'_'.$w.'x'.$h.'.'.$pathInfo['extension'];
  
  if ( file_exists( $localPath ) ) {
		$imageUrl = $imagePath.'?exists='.file_exists( $localPath );
	} else {
    $image = wp_get_image_editor( $localPathOld );
    if ( ! is_wp_error( $image ) ) {
      $image->resize( $w, $h, true );
      $image->set_quality( 100 );
      $image->save( $localPath );
      $imageUrl = $imagePath;
    }else{
      $imageUrl = '';
    }
	}
	return $imageUrl;
}

function get_chat_list($user_id, $type="private"){
  global $wpdb;
  $sql = 'select * from wp_messages where 
  (accept_id='.$user_id.' or send_id='.$user_id.') and
  type = "'.$type.'"
  order by id desc';
  $msgList = $wpdb->get_results($sql);
  
  // 合并相同用户
  $temUserID = array();
  foreach($msgList as $key => $msgItem){
    if($msgItem->send_id == $user_id){
      if(!in_array($msgItem->accept_id, $temUserID)){
        $temUserID[$key] = $msgItem->accept_id;
      }else{
        unset($msgList[$key]);
      }
    }else{
      if(!in_array($msgItem->send_id, $temUserID)){
        $temUserID[$key] = $msgItem->send_id;
      }else{
        unset($msgList[$key]);
      }
    }
  }
  
  $temCL = array();
  foreach($msgList as $key => $msgItem){
    // 查询未读消息
    $temUserF = filter_chat($msgItem, $user_id);
    $sql = 'select count(*) from wp_messages where accept_id = '.$user_id.' and send_id = '.$temUserF->user_id.' and status = 1 and type = "'.$type.'"';
    $temUserF->noRead = $wpdb->get_var($sql);
    $temCL[] = $temUserF;
  }
  return $temCL;
}

// 过滤聊天内容
function filter_chat($chatItem, $user_id){
  if($chatItem->send_id == $user_id){
    $id = $chatItem->accept_id;
  }else{
    $id = $chatItem->send_id;
  }
  unset($chatItem->send_id, $chatItem->accept_id);
  $chatItem->user_id = $id;
  $chatItem->display_name = get_user_by('id', $id)->data->display_name;
  $chatItem->user_avatar = preg_replace( '/<img(.+?)src=[\'"](.+?)[\'"](.+?)>/im', "$2", get_avatar($id) );
  $chatItem->send_time_stamp = $chatItem->send_time;
  $chatItem->send_date = date('Y-m-d H:i:s', $chatItem->send_time);
  $chatItem->send_time = date('m-d', $chatItem->send_time);
  return $chatItem;
}

// 获取某个用户和别人的聊天信息
function get_messages($send_id, $accept_id){
  global $wpdb;
  $sql = 'select * from wp_messages where 
  (accept_id='.$accept_id.' and send_id='.$send_id.') or
  (accept_id='.$send_id.' and send_id='.$accept_id.')
  order by id asc';
  $msgList = $wpdb->get_results($sql);
  
  $tempMsg = array();
  foreach($msgList as $key => $msgItem){
    
    if($key==0){
      $temTime = ceil($msgItem->send_time/600);
      $temTimeArray = array(
        'user_id' => 0,
        'time' => date('Y-m-d H:i', $msgItem->send_time),
        'temTime' => $temTime,
        'key' => $key,
      );
      $tempMsg[] = (object)$temTimeArray;
    }
    
    $temTime2 = ceil($msgItem->send_time/600);
    
    if(!($temTime==$temTime2)){
      $temTime = ceil($msgItem->send_time/600);
      $temTimeArray = array(
        'user_id' => 0,
        'time' => date('Y-m-d H:i', $msgItem->send_time),
        'temTime' => $temTime,
        'key' => $key,
      );
      $tempMsg[] = (object)$temTimeArray;
    }
    
    $tempMsg[] = filter_chat($msgItem, $user_id);
  }
  
  return $tempMsg;
}

// 获取单条信息
function get_message($id, $user_id){
  global $wpdb;
  $data = $wpdb->get_results('select * from wp_messages where id = '.$id);
  if(empty($data)){
    return false;
  }else{
    return filter_chat($data[0], $user_id);
  }
}

// 发送消息
function send_message($send_id, $accept_id, $title, $content, $type, $source){
  global $wpdb;
  $sql = 'insert into wp_messages values (NULL, '.$send_id.', '.$accept_id.', "'.$title.'", 
    "'.$content.'", "'.$type.'", "'.$source.'", 1, '.time().')';
  $isInsert = $wpdb->query($sql);
  return $isInsert?$wpdb->insert_id:$isInsert;
}

function filter_user($user, $user_id = 0){
  unset($user->user_pass);
  unset($user->user_activation_key);
  unset($user->user_login);
  unset($user->user_nicename);
  unset($user->id);
  unset($user->item_id);
  
  $user->user_id = $user->ID;
  
  $user->description = get_user_meta($user->user_id, "description", true);
  if(empty($user->description)){
    $user->description = "这个人比较懒，什么都没有填写！";
  }
  if(empty($user->display_name)){
    $user->display_name = '未填写';
  }
  $user->user_avatar = preg_replace( '/<img(.+?)src=[\'"](.+?)[\'"](.+?)>/im', "$2", get_avatar($user->user_id) );
  
  // 登录用户和当前用户的关系
  if($user_id){
    $user->collection_user = is_follow_item($user_id, $user->user_id, 'user');
  }else{
    $user->collection_user = false;
  }
  
  return $user;
}

function get_item_relationships($user_id, $item_type, $page, $rows){
  global $wpdb;
  if($item_type == 'user'){
    $sql = 'select * from wp_users, wp_user_item_taxonomy where
    wp_user_item_taxonomy.item_id = wp_users.ID and
    wp_user_item_taxonomy.user_id = '.$user_id.' and
    wp_user_item_taxonomy.item_type = "user" limit '.($page-1)*$rows.','.$rows;
    $data = $wpdb->get_results($sql);
    if(!empty($data)){
      foreach($data as $key => $item){
        $data[$key] = filter_user($item, $user_id);
      }
    }
    return $data;
  }
  if($item_type == 'fans'){
    $sql = 'select * from wp_users, wp_user_item_taxonomy where
    wp_user_item_taxonomy.user_id = wp_users.ID and
    wp_user_item_taxonomy.item_id = '.$user_id.' and
    wp_user_item_taxonomy.item_type = "user" limit '.($page-1)*$rows.','.$rows;
    $data = $wpdb->get_results($sql);
    if(!empty($data)){
      foreach($data as $key => $item){
        $data[$key] = filter_user($item, $user_id);
      }
    }
    return $data;
  }
}

function search_user($s, $rows, $page){
  $args = array(
    'search' => $s,
    'number' => $rows,
    'offset' => ($page-1)*$rows,
  );
  $user_search = new WP_User_Query($args);
  $user_total = $user_search->get_total();
  $users = $user_search->get_results();
  $temU = array();
  foreach($users as $user){
    $temU[] = filter_user($user->data);
  }
  return array('data'=>$temU, 'total'=>$user_total);
}

function badge_message_read($send_id, $accept_id){
  global $wpdb;
  $sql = 'update wp_messages set status = 2 where send_id = '.$send_id.' and accept_id = '.$accept_id;
  $changeRows = $wpdb->query($sql);
  return $changeRows;
}

function post_relation_term($termIDs, $post_id){
  global $wpdb;
  $termArr = array();
  if(is_array($termIDs)){
    $termArr = $termIDs;
  }else{
    $termArr[] = $termIDs;
  }
  foreach($termArr as $termID){
    $taxonomy_id = $wpdb->get_var('select term_taxonomy_id from wp_term_taxonomy where term_id = '.$termID);
    if($taxonomy_id){
      $wpdb->query('insert into wp_term_relationships values ('.$post_id.', '.$taxonomy_id.', 0)');
    }
  }
}

// 今天是否签到
function is_userSign($user_id){
  global $wpdb;
  $sql = 'select count(*) from wp_usertick where user_id = '.$user_id.' and date_format(tick_date, "%Y-%m-%d") = "'.date('Y-m-d').'"';
  return $wpdb->get_var($sql)*1;
}

// 昨天是否签到
function is_yesUserSign($user_id){
  global $wpdb;
  $sql = 'select count(*) from wp_usertick where user_id = '.$user_id.' and 
  date_format(tick_date, "%Y-%m-%d") = "'.date('Y-m-d', strtotime("-1 day")).'"';
  return $wpdb->get_var($sql)*1;
}

// 执行签到
function userSign($user_id){
  global $wpdb;
  // 先判断是否签到
  if(!is_userSign($user_id)){
    $isInsert = $wpdb->query('insert into wp_usertick values (NULL, '.$user_id.', "'.date('Y-m-d H:i:s').'")');
    if($isInsert){
      return $wpdb->insert_id;
    }else{
      return false;
    }
  }else{
    return false;
  }
}

// 加积分
function addUserIntegral($user_id, $item_id, $integral_type, $integral_value, $integral_remarks){
  global $wpdb;
  return $wpdb->query('insert into wp_integral values (NULL, '.$user_id.', '.$item_id.', "'.$integral_type.'", "'.$integral_value.'", "'.date('Y-m-d H:i:s').'", "'.$integral_remarks.'")');
}

function getUserIntegral($user_id){
  global $wpdb;
  $userIntegral = $wpdb->get_var('select sum(integral_value) as integral from wp_integral where user_id = '.$user_id);
  return $userIntegral?$userIntegral:0;
}

if(!function_exists('collection_count')){
  // 获取文章话题的关注量
  function collection_count($item_id, $item_type){
    global $wpdb;
    $sql = 'select count(*) from wp_user_item_taxonomy where
    item_id = '.$item_id.' and item_type = "'.$item_type.'"';
    return $wpdb->get_var($sql);
  }
}

// 获取用户的关注量
function follow_count($item_id, $item_type){
  global $wpdb;
  $sql = 'select count(*) from wp_user_item_taxonomy where
  user_id = '.$item_id.' and item_type = "'.$item_type.'"';
  return $wpdb->get_var($sql);
}

if(!function_exists('comment_count')){
  function comment_count($type, $ID){
    global $wpdb;
    if($type == 'post'){
      return $wpdb->get_var('select comment_count from wp_posts where ID = '.$ID);
    }elseif($type == 'user'){
      return $wpdb->get_var('select count(*) from wp_comments where user_id = '.$ID);
    }
  }
}

// 过滤分类的评论
function wj_filter_category_comment($catComment){
  $user = get_user_by('id', $catComment->user_id);
  $catComment->comment_author = $user->data->display_name;
  $catComment->avatar = preg_replace( '/<img(.+?)src=[\'"](.+?)[\'"](.+?)>/im', "$2", get_avatar($catComment->user_id ));
  $catComment->publish_date = time_since(date('Y-m-d H:i:s', $catComment->publish_date + 8*3600));
  $catComment->content = wpautop($catComment->content);
  return $catComment;
}

// 获取分类的评论列表
if(!function_exists('getCategoryComments')){
  function getCategoryComments($catID, $page = 1, $rows = 10){
    global $wpdb;
    $sql = 'select * from wp_term_comment where term_id = '.$catID.' order by id desc limit '.($page-1)*$rows.', '.$rows;
    $catComments = $wpdb->get_results($sql);
    $temCatCom = array();
    foreach($catComments as $key => $catCommentItem){
      $temCatCom[] = wj_filter_category_comment($catCommentItem);
    }
    return $temCatCom;
  }
}

// 获取单条分类的评论
function getCategoryComment($catComID){
  global $wpdb;
  $catComment = $wpdb->get_results('select * from wp_term_comment where id = '.$catComID);
  return wj_filter_category_comment($catComment[0]);
}

// 验证用户token
function tokenVerification($token, $user_id){
  if(empty($token) || empty($user_id)) return false;
  global $wpdb;
  $user_id = $wpdb->get_var('select user_id from wp_usermeta where 
  meta_value = "'.$token.'" and user_id = '.$user_id.' and meta_key = "token"');
  return $user_id;
}

// 自定义分类页面添加自定义字段
// 新建分类页面添加自定义字段输入框
add_action( 'docs_add_form_fields', 'docs_add_fields', 10, 1);
// 编辑分类页面添加自定义字段输入框
add_action( 'docs_edit_form_fields', 'docs_edit_fields', 10, 1 );

// 保存自定义字段数据
add_action( 'edited_docs', 'docs_save_fields', 10, 2 );
add_action( 'created_docs', 'docs_save_fields', 10, 2 );

function docs_add_fields(){
?>
<div class="form-field term-cover-wrap">
	<label for="tag-cover">缩略图</label>
	<input id="docs_cover" name="docs_cover" class="regular-text" type="text" value="" />
</div>
<div class="form-field term-info-wrap">
	<label for="tag-info">介绍</label>
  <?php
  $arrs = array(
    'tinymce'=>1,
    'textarea_rows'=>5,
  );
  wp_editor($docs_info, 'docs_info', $arrs);
  ?>
</div>
<script>
var $ = jQuery;
$(function(){
  $('#submit').click(function(){
    var wp_editor_id = $(".wp-editor-area").attr('id');
    var content;
    var editor = tinyMCE.get(wp_editor_id);
    if (editor) {
        content = editor.getContent();
    } else {
        content = $('#' + wp_editor_id).val();
    }
    $('#docs_info').val(content)
  })
})
</script>
<?php
}

function docs_edit_fields($params){
  $docs_cover = get_term_meta($params->term_id, 'docs_cover', true);
  $docs_info = get_term_meta($params->term_id, 'docs_info', true);
?>
  <tr class="form-field term-cover-wrap">
    <th scope="row"><label for="cover">缩略图</label></th>
    <td>
      <?php
			if($docs_cover){
				echo '<img width="80" src="'.$docs_cover.'" /> <br/>';
			}
			?>
			<input id="docs_cover" name="docs_cover" class="regular-text" type="text" value="<?php echo $docs_cover; ?>" />
    </td>
  </tr>
  <tr class="form-field term-info-wrap">
    <th scope="row"><label for="info">介绍</label></th>
    <td>
      <?php
      $arrs = array(
        'tinymce'=>1,
        'textarea_rows'=>10,
      );
      wp_editor($docs_info, 'docs_info', $arrs);
      ?>
    </td>
  </tr>
<?php
}
function docs_save_fields($term_id){
  update_term_meta($term_id, 'docs_cover', $_POST['docs_cover']);
  update_term_meta($term_id, 'docs_info', $_POST['docs_info']);
}

function filter_topic_list( $query ) {
  if ( ($query->query['post_type'] == 'topic' || isset($query->query['group'])) &&
    $query->is_main_query() ) {
    $query->set( 'posts_per_page', '20' );
  }
}
add_action( 'pre_get_posts', 'filter_topic_list' );

function get_url_by_banner_type($item){
  if($item['linkType']==1){
    if($item['type']=='page' || $item['type']=='post'){
      return get_permalink($item['ID']*1);
    }elseif($item['type']=="docs"){
      return get_term_link($item['ID']*1, 'docs');
    }
  }else{
    return $item['link'];
  }
}

//禁用WordPress前台搜索功能
function disable_search( $query, $error = true ) {
  if (is_search() && !is_admin()) {
    $query->is_search = false;
    $query->query_vars['s'] = false;
    $query->query['s'] = false;
    if ( $error == true )
    // 执行搜索后显示的错误页面
    // $query->is_home = true; //跳转到首页
    $query->is_404 = true;//跳转到404页
  }
}
add_action( 'parse_query', 'disable_search' );
add_filter( 'get_search_form', create_function( '$a', "return null;" ) );


// 生成token
function wj_encode($string = "", $skey = 'wenjiangs') {
  $strArr = str_split(base64_encode($string));
  $strCount = count($strArr);
  foreach (str_split($skey) as $key => $value)
  $key < $strCount && $strArr[$key].=$value;
  return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join($strArr));
}

// 解密token
function wj_decode($string = "", $skey = 'wenjiangs') {
  $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
  $strCount = count($strArr);
  foreach (str_split($skey) as $key => $value)
  $key <= $strCount && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
  return base64_decode(join($strArr));
}

/*
 * @description 根据资源ID获取资源作者ID
 */
 
//文章类型
$wjPostType = array(
  'topic' => '话题',
  'post' => '文章',
);


function getUserIDByItemType($item_id, $item_type){
  if(in_array($item_type, array('topic', 'post'))){
    $tPost = get_post($item_id);
    return $tPost->post_author;
  }
  if($item_type=='user'){
    return $item_id;
  }
  return 0;
}

// 获取系统消息
function get_system_message($user_id){
  $msg_collection = get_chat_list($user_id, 'collection');
  $msg_comment = get_chat_list($user_id, 'comment');
  $msg_system = get_chat_list($user_id, 'system');
  return array(
    'collection' => $msg_collection,
    'comment' => $msg_comment,
    'system' => $msg_system,
  );
}

function get_system_message_by_type($user_id, $type, $page, $rows){
  global $wpdb;
  //stripslashes 反转义
  $sql = 'select * from wp_messages where type = "'.$type.'" and accept_id = '.$user_id.'
  order by id desc limit '.($page-1)*$rows.','.$rows;
  $sysmsg = $wpdb->get_results($sql);
  
  // 编辑为已读
  $sql = 'update wp_messages set status = 2 where type = "'.$type.'" and accept_id = '.$user_id;
  $wpdb->query($sql);
  
  foreach($sysmsg as $key => $sgItem){
    $sysmsg[$key]->send_time = time_since(date('Y-m-d H:i:s', $sysmsg[$key]->send_time));
    $sysmsg[$key]->content = json_decode($sysmsg[$key]->content);
    
    $user = get_user_by('id', $sgItem->content->user_id);
    $user = wj_filter_user($user);
    $sysmsg[$key]->user = $user;
    
    // 收藏
    if($sgItem->type == "collection"){
      if(!($sgItem->content->item_type == 'user')){
        $post = get_post($sgItem->content->item_id);
        $sysmsg[$key]->post = wj_filter_post($post);
      }
    }
    
    // 评论
    if($sgItem->type == "comment"){
      if($sgItem->content->item_type == 'comment'){
        $comment = get_comment($sgItem->content->item_object_id);
        $sysmsg[$key]->comment = wj_filter_comment($comment);
        $post = get_post($sgItem->content->item_id);
        $sysmsg[$key]->post = wj_filter_post($post);
      }else{
        $comment = get_comment($sgItem->content->item_id);
        $sysmsg[$key]->comment = wj_filter_comment($comment);
        $reply = get_comment($sgItem->content->item_object_id);
        $sysmsg[$key]->reply = wj_filter_comment($reply);
      }
    }
    
  }
  
  return $sysmsg;
}

// 附加用户信息
function wj_filter_user($user){
  global $wpdb, $params;
  unset($user->data->user_activation_key);
  $user->data->location = get_user_meta($user->ID, 'location', true);
  $user->data->location2 = get_user_meta($user->ID, 'location2', true);
  $user->data->gender = get_user_meta($user->ID, 'gender', true);
  if(!$user->data->location) $user->data->location = '';
	$user->data->user_avatar = preg_replace( '/<img(.+?)src=[\'"](.+?)[\'"](.+?)>/im', "$2", get_avatar($user->ID) );
	unset($user->data->user_pass);
  $user->data->description = get_user_meta($user->ID, 'description', true);
  if(!$user->data->description) $user->data->description = '';
  $user->data->mobile_phone = get_user_meta($user->ID, 'mobile_phone', true);
  if(!$user->data->mobile_phone) $user->data->mobile_phone = '';
  $user->data->comment_count = $wpdb->get_var('select count(*) from wp_posts, wp_comments where wp_comments.user_id = '.$user->ID.' and wp_posts.post_status = "publish" and wp_posts.post_type = "post" and wp_comments.comment_post_ID = wp_posts.ID');
  if(!$user->data->comment_count) $user->data->comment_count = 0;
  $user->data->post_count = $wpdb->get_var('select count(*) from wp_posts where post_author = '.$user->ID.' and post_status = "publish" and post_type = "post"');
  if(!$user->data->post_count) $user->data->post_count = 0;
  $user->data->topic_count = $wpdb->get_var('select count(*) from wp_posts where post_author = '.$user->ID.' and post_status = "publish" and post_type = "topic"');
  if(!$user->data->topic_count) $user->data->topic_count = 0;
  $user->data->reply_count = $wpdb->get_var('select count(*) from wp_posts, wp_comments where wp_comments.user_id = '.$user->ID.' and wp_posts.post_status = "publish" and wp_posts.post_type = "topic" and wp_comments.comment_post_ID = wp_posts.ID');
  if(!$user->data->reply_count) $user->data->reply_count = 0;
  $user->data->collection_count = $wpdb->get_var('select count(*) from wp_user_item_taxonomy where user_id = '.$user->ID);
  if(!$user->data->collection_count) $user->data->collection_count = 0;
  $user->data->fans_count = $wpdb->get_var('select count(*) from wp_user_item_taxonomy where item_id = '.$user->ID);
  if(!$user->data->fans_count) $user->data->fans_count = 0;
  $user->data->views = get_user_meta($user->ID, 'views', true);
  if(!$user->data->views) $user->data->views = 0;
  
  // 当前用户和查询的用户关系
  if(isset($params['user_id']) && !empty($params['user_id'])){
    $user->data->collection_user = is_follow_item($params['user_id'], $user->ID, 'user');
  }
  
  // 修正 display_name;
  $user->data->display_name = $wpdb->get_var('select display_name from wp_users where ID = '.$user->ID);
  
  // 金币
  $user->data->user_coin = getUserIntegral($user->ID)*1;
  
  return $user->data;
}

function get_user_by_post_id($postID){
  global $wpdb;
  $user_id = $wpdb->get_var('select post_author from wp_posts where ID = '.$postID);
  return get_user_by('id', $user_id);
}

// 删除链接地址
function doOuterChain($str){
  $str = preg_replace( '/<a(.+?)>/im', '', $str );
  $str = preg_replace( '/<\/a>/im', '', $str );
  return $str;
}

// 重新整理 post 数据
function wj_filter_post( $post, $single = false ) {
  global $wpdb, $params, $Parsedown;
	$pure_post          = array();
	$pure_post['ID']    = $post->ID;
	$pure_post['title'] = $post->post_title;
	$pure_post['post_type'] = $post->post_type;
  
	// 详情页显示内容
	if ( $single ) {
    $pure_post['content'] = $post->post_content;
    $pure_post['content'] = preg_replace('#\[emoji:(.*?)\]#si', '', $pure_post['content']);
    $pure_post['content'] = wpautop($Parsedown->text($pure_post['content']));
	}
  
  // 话题列表单独处理
  if(isset($params['sp']) && $params['sp']){
    $limitWords = 70;
    $pure_post['content'] = $post->post_content;
    $pure_post['content'] = preg_replace('#\[emoji:(.*?)\]#si', '', $pure_post['content']);
    $pure_post['content'] = wpautop($Parsedown->text($pure_post['content']));
    $pure_post['content'] = wp_trim_words($pure_post['content'], $limitWords);
    $pure_post['content'] = strip_tags($pure_post['content']);
    $pure_post['content'] = str_replace('&hellip;', '…', $pure_post['content']);
  }

	// 特色图片
  $pure_post['thumbnail'] = array();
  if(has_post_thumbnail() && !isset($params['sp'])){
    $img_id                 = get_post_thumbnail_id( $post->ID );
    $img_arr                = wp_get_attachment_image_src( $img_id, 'full' );
    $pure_post['thumbnail'] = $img_arr[0];
  }

  // 图片尺寸
  $tw = $twb = 200; $th = $thb = 150;
  if(isset($params['tp'])){
    $tw = $th = 120;
    $twb = 380; $thb = 285;
  }
  
	// 获取文章中的图片集合
	$imageList = catch_that_image();
	if(!empty($imageList)){
		$outList = array();
		$outLists = array();
		if ( count( $imageList ) >= 3 ) {
      for($i=0; $i<3; $i++){
        $outList[] = $imageList[$i];
        $outLists[] = creat_thumb( $imageList[$i], $tw, $th );
      }
		} else {
			if(empty($pure_post['thumbnail'])){
				$outList[] = $imageList[0];
				$outLists[] = creat_thumb( $imageList[0] );
			}
		}
		$pure_post['thumbnail'] = $outList;
		$pure_post['thumbnails'] = $outLists;
  }else{
    $imgList = get_post_meta($post->ID, 'imgList', true);
    if(!empty($imgList)){
      if(count($imgList) == 1){
        $pure_post['thumbnail'][] = $imgList[0];
        $pure_post['thumbnails'][] = creat_thumb($imgList[0], 380, 285);
      }else{
        foreach($imgList as $imgItem){
          $pure_post['thumbnail'][] = $imgItem;
          $pure_post['thumbnails'][] = creat_thumb($imgItem, 120, 120);
        }
      }
    }
  }

	// 查询作者
  if(isset($post->post_author)){
    $author = get_user_by('id', $post->post_author);
  }else{
    $author = get_user_by_post_id($post->ID);
  }
  $pure_post['author_name'] = $author->data->display_name;
  $pure_post['post_author'] = $post->post_author;
  
  $avatar = get_avatar($author->ID);
  $avatar = preg_replace( '/<img(.+?)src=[\'"](.+?)[\'"](.+?)>/im', "$2", $avatar );
  $pure_post['author_avatar'] = $avatar;
  
  // 用户是否关注作者
  $pure_post['collection_author'] = false;
  if(isset($params['user_id']) && !empty($params['user_id'])){
    $pure_post['collection_author'] = is_follow_item($params['user_id'], $post->post_author, 'user');
  }

	// 统计数据
	$pure_post['views'] = get_post_meta($post->ID, 'views', true)*1;
  
  // 收藏数
	$pure_post['collection'] = $wpdb->get_var('select count(*) from wp_user_item_taxonomy
    where item_id = '.$post->ID.' and item_type = "'.$post->post_type.'"')*1;

  // 用户是否收藏这篇文章
  if(isset($params['user_id']) && $params['user_id']){
    $sql = 'select count(*) from wp_user_item_taxonomy
      where item_id = '.$post->ID.' and item_type = "'.$post->post_type.'" and user_id = '.$params['user_id'];
    $pure_post['collection_current'] = $wpdb->get_var($sql)*1;
  }else{
    $pure_post['collection_current'] = 0;
  }
  
	$pure_post['word_count'] = mb_strwidth($post->post_content);
	$pure_post['comment_count'] = comment_count('post', $post->ID);
	$pure_post['comment_status'] = $post->comment_status;

	// 发表时间
	$pure_post['date'] = time_since($post->post_date);
  
  // 如果有 app 这个参数，处理里面链接地址
  if(isset($params['app']) && !empty($params['app'])){
    $pure_post['content'] = doOuterChain($pure_post['content']);
  }

	return $pure_post;
}

// 过滤分类
function wj_filter_category($term){
  global $wpdb, $params;
	unset($term->term_group);
	unset($term->filter);
	unset($term->taxonomy);
	unset($term->parent);
	unset($term->term_taxonomy_id);
	$term->cover = get_term_meta($term->term_id, 'docs_cover', true);
	if(empty($term->cover)){
		$term->cover = get_bloginfo('url').'/wp-content/themes/wtheme/images/placeholder.png';
	}
	$term->comment_count = $wpdb->get_var('select count(*) from wp_term_comment where term_id = '.$term->term_id);
	$term->views = get_term_meta($term->term_id, 'views', true);
  $term->summary = str_replace('&hellip;', "…", wp_trim_words($term->description, 40));
  $term->details = doOuterChain(wpautop(get_term_meta($term->term_id, 'docs_info', true)));
  $term->details = preg_replace('/ style="(.+?)"/ims', "", $term->details);
  
  // 当前用户是否关注该分类
  $term->collection_current = false;
  if(isset($params['user_id']) && !empty($params['user_id'])){
    $term->collection_current = is_follow_item($params['user_id'], $term->term_id, 'docs');
  }
  
  $term->collection = collection_count($term->term_id, 'docs');
  
  // 如果是小组，返回图标
  if($params['taxonomy']=='group'){
    $term->cover = 'https://www.wenjiangs.com/wp-content/themes/wtheme/images/group-'.$term->term_id.'.png';
    $term->comment_count = get_group_reply_number($term->term_id);
  }
  
	return $term;
}

// 评论
function wj_filter_comment($comment){
  @$comment->comment_date = time_since($comment->comment_date);
  unset($comment->comment_date_gmt);
  $comment->comment_content = wpautop($comment->comment_content);
  $comment->avatar = preg_replace( '/<img(.+?)src=[\'"](.+?)[\'"](.+?)>/im', "$2", get_avatar($comment->user_id ));
  return $comment;
}

function countryCodeToName($code, $type="cn"){
  global $wpdb;
  $type_text = "country_cn";
  if($type == "en"){
    $type_text = "country_en";
  }
  return $wpdb->get_var('select '.$type_text.' from wp_country_code where area_code = "'.$code.'"');
}

/**
* @description 获取系统通知列表
* @param {Number} user_id 用户ID
* @param {Number} page 页码
* @param {Number} rows 分页大小
* @return {Array} result 结果
*/
function get_notiys($user_id, $page, $rows){
  global $wpdb;
  //stripslashes 反转义
  $sql = 'select * from wp_messages where type IN ("comment", "collection", "system") and accept_id = '.$user_id.'
  order by id desc limit '.($page-1)*$rows.','.$rows;
  $sysmsg = $wpdb->get_results($sql);
  
  // 编辑为已读
  $sql = 'update wp_messages set status = 2 where type = "'.$type.'" and accept_id = '.$user_id;
  $wpdb->query($sql);
  
  $temArray = array();
  
  foreach($sysmsg as $key => $sgItem){
    
    if($key==0){
      $temTime = date('Y-m-d', $sgItem->send_time);
      $temTimeArray = array(
        'id' => 0,
        'time' => date('Y-m-d', $sgItem->send_time),
        'temTime' => $temTime,
        'key' => $key,
      );
      $temArray[] = (object)$temTimeArray;
    }
    
    $temTime2 = date('Y-m-d', $sgItem->send_time);
    
    if(!($temTime==$temTime2)){
      $temTime = date('Y-m-d', $sgItem->send_time);
      $temTimeArray = array(
        'id' => 0,
        'time' => date('Y-m-d', $sgItem->send_time),
        'temTime' => $temTime,
        'key' => $key,
      );
      $temArray[] = (object)$temTimeArray;
    }
    
    $sgItem->send_data = date('Y-m-d H:i:s', $sgItem->send_time);
    $sgItem->send_time = time_since(date('Y-m-d H:i:s', $sgItem->send_time));
    $sgItem->content = json_decode($sgItem->content);
    
    $user = get_user_by('id', $sgItem->content->user_id);
    $user = wj_filter_user($user);
    $sgItem->user = $user;
    
    // 收藏
    if($sgItem->type == "collection"){
      if(!($sgItem->content->item_type == 'user')){
        $post = get_post($sgItem->content->item_id);
        $sgItem->post = wj_filter_post($post);
      }
    }
    
    // 评论
    if($sgItem->type == "comment"){
      if($sgItem->content->item_type == 'comment'){
        $comment = get_comment($sgItem->content->item_object_id);
        $sgItem->comment = wj_filter_comment($comment);
        $post = get_post($sgItem->content->item_id);
        $sgItem->post = wj_filter_post($post);
      }else{
        $comment = get_comment($sgItem->content->item_id);
        $sgItem->comment = wj_filter_comment($comment);
        $reply = get_comment($sgItem->content->item_object_id);
        $sgItem->reply = wj_filter_comment($reply);
      }
    }
    
    $temArray[] = $sgItem;
    
  }
  
  return $temArray;
}


/**
 * 文章内目录
 */
function singleToc($content, $only = false){
  $matches = array();
  $toc = '';
  $match = preg_match_all('/<h([2-6]).*?\>(.*?)<\/h[2-6]>/is', $content, $matches);
	foreach($matches[1] as $key => $value) {
		$title = trim(strip_tags($matches[2][$key]));
		$content = str_replace($matches[0][$key], '<h' . $value . ' id="title-' . $key . '">'.$title.'</h2>', $content);
		$toc .= '<li class="toc-'.$value.'"><a href="#title-'.$key.'" title="'.$title.'">'.$title."</a></li>\n";
	}
	if($match){
		$toc = "\n<div class=\"singleToc\"><ul>\n" . $toc . "</ul></div>\n";
	}
	return $only?$content:$toc;
}

function getUserIntegralList($user_id, $page, $rows, $type = '', $date = ''){
  global $wpdb;
  $sql = 'select * from wp_integral where user_id = '.$user_id;
  if(!empty($type)){
    $sql .= ' and integral_type = "'.$type.'" ';
  }
  if(!empty($date)){
    $sql .= ' and date_format(integral_date, "%Y-%m-%d") = "'.$date.'" ';
  }
  $sql .= ' order by id desc limit '.($page-1)*$rows.','.$rows;
  $res = $wpdb->get_results($sql);
  return $res;
}

function addUserBrowse($user_id, $item_id, $item_type){
  global $wpdb;
  // 先查询是否存在
  $sql = 'select id from wp_user_browse where user_id = '.$user_id.' and item_id = '.$item_id.' and item_type = "'.$item_type.'"';
  $isExist = $wpdb->get_var($sql);
  if($isExist){
    $sql = 'update wp_user_browse set view_date = "'.date('Y-m-d H:i:s').'" where id = '.$isExist;
  }else{
    $sql = 'insert into wp_user_browse values (NULL, '.$user_id.', '.$item_id.', "'.$item_type.'", "'.date('Y-m-d H:i:s').'", "")';
  }
  return $isInsert = $wpdb->query($sql);
}

function addUserLoginCoin($user_id){
  $isGive = isGiveLoginCoin($user_id);
  if(!$isGive){
    addUserIntegral($user_id, 0, 'login', 5, '每日登录');
  }
}

function isGiveLoginCoin($user_id){
  global $wpdb;
  // 今日是否赠送
  $sql = 'select count(*) from wp_integral where user_id = '.$user_id.' and integral_type = "login" and date_format(integral_date, "%Y-%m-%d") = "'.date('Y-m-d').'"';
  return $isGive = $wpdb->get_var($sql)*1;
}

function getUserBrowseList($user_id, $page, $rows, $type = '', $date = ''){
  global $wpdb;
  $sql = 'select * from wp_user_browse where user_id = '.$user_id;
  if(!empty($type)){
    $sql .= ' and item_type = "'.$type.'" ';
  }
  if(!empty($date)){
    $sql .= ' and date_format(view_date, "%Y-%m-%d") = "'.$date.'" ';
  }
  $sql .= ' order by id desc limit '.($page-1)*$rows.','.$rows;
  return $res = $wpdb->get_results($sql);
}

// 今日发布的 评论/回复
function getTodaySubmitCommentCount($user_id, $type){
  global $wpdb;
  $sql = 'select count(*) from wp_comments, wp_posts where wp_comments.comment_post_ID = wp_posts.ID and wp_posts.post_type = "'.$type.'" 
  and user_id = '.$user_id.' and date_format(comment_date, "%Y-%m-%d") = "'.date('Y-m-d').'"';
  return $wpdb->get_var($sql)*1;
}

// 今日发布的 文章/话题
function getTodaySubmitPostCount($user_id, $type){
  global $wpdb;
  $sql = 'select count(*) from wp_posts where post_type = "'.$type.'" and post_author = '.$user_id.' and 
  post_status = "publish" and date_format(comment_date, "%Y-%m-%d") = "'.date('Y-m-d').'"';
  return $wpdb->get_var($sql)*1;
}

// 评论审核通过发送邮件
add_action('comment_unapproved_to_approved', 'wj_comment_approved');
function wj_comment_approved($comment){
  if (is_email($comment->comment_author_email)){
    $post_link = get_permalink($comment->comment_post_ID);
    $title = '您在【' . get_bloginfo('name') . '】的评论已通过审核';
    $body = '您在《<a href="' . $post_link . '" target="_blank" >' . get_the_title($comment->comment_post_ID) . '</a>》中发表的评论已通过审核！<br /><br />';
    $body .= '<strong>您的评论：</strong><br />';
    $body .= strip_tags($comment->comment_content) . '<br /><br />';
    $body .= '您可以：<a href="' . get_comment_link($comment->comment_ID) . '" target="_blank">查看您的评论</a>  |  <a href="' . $post_link . '#comments" target="_blank">查看其他评论</a>  |  <a href="' . $post_link . '" target="_blank">再次阅读文章</a><br /><br />';
    $body .= '欢迎再次光临【<a href="' . get_bloginfo('url') . '" target="_blank" title="' . get_bloginfo('description') . '">' . get_bloginfo('name') . '</a>】。';
    $body .= '<br /><br />注：此邮件为系统自动发送，请勿直接回复';
    @wp_mail($comment->comment_author_email, $title, $body, "Content-Type: text/html; charset=UTF-8");
  }
  // 通过加金币
  $post = get_post($comment->comment_post_ID);
  $integralCount = getUserIntegralList($comment->user_id, 1, 10, 'comment_'.$post->post_type, date('Y-m-d'));
  $post_type = array(
    'post' => '评论文章',
    'topic' => '回复话题',
    'doc' => '评论专栏文章',
  );
  if(count($integralCount)<3){
    addUserIntegral($comment->user_id, $comment->comment_ID, 'comment_'.$post->post_type, 5, $post_type[$post['post_type']]);
  }
}

### Function Show Post Views Column in WP-Admin
add_action('manage_posts_custom_column', 'add_postviews_column_content');
add_filter('manage_posts_columns', 'add_postviews_column');
add_action('manage_pages_custom_column', 'add_postviews_column_content');
add_filter('manage_pages_columns', 'add_postviews_column');
function add_postviews_column($defaults) {
  $defaults['views'] = '浏览';
  $defaults['collection'] = '收藏';
  return $defaults;
}


### Functions Fill In The Views Count
function add_postviews_column_content($column_name) {
  global $post, $wpdb;
  switch($column_name){
    case 'views':
      echo get_post_meta($post->ID, 'views', true);
    case 'collection':
      echo $wpdb->get_var('select count(*) from wp_user_item_taxonomy
      where item_id = '.$post->ID.' and item_type = "'.$post->post_type.'"')*1;
  }
}

### Function Sort Columns
add_filter('manage_edit-post_sortable_columns', 'sort_postviews_column');
add_filter('manage_edit-page_sortable_columns', 'sort_postviews_column');
function sort_postviews_column($defaults){
  $defaults['views'] = '浏览';
  $defaults['collection'] = '收藏';
  return $defaults;
}
add_action('pre_get_posts', 'sort_postviews');
function sort_postviews($query) {
  $orderby = $query->get('orderby');
  switch($orderby){
    case 'views':
      $query->set('meta_key', 'views');
    case 'collection':
      $query->set('meta_key', 'collection');
  }
  if(in_array($orderby, array('views', 'collection'))) {
    $query->set('orderby', 'meta_value_num');
  }
}
