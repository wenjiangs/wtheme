<?php
if(isset($_GET['id'])){  //存在ID则更新文章
	$edit_post = get_post($_GET['id']);
	if(!($current_user->ID == $edit_post->post_author)){
		wp_die('没有权限修改！');
	}
}

global $current_user;

get_header();

$catArgs = array(
  'taxonomy'=>"category",
  'hide_empty' => false,
  'orderby' => 'id',
  'order' => 'DESC',
);
if(isset($_GET['type']) && $_GET['type'] == 'doc'){
  $catArgs['taxonomy'] = 'group';
}
$terms = get_terms($catArgs);
$jsTerms = array();
foreach($terms as $key => $terms_item){
  $jsTerms[] = array(
    'label' => $terms_item->name,
    'value' => $terms_item->term_id,
  );
}  

?>
<div class="container" id="app">
  <div class="wt-container">
    <div class="publishBox">
      <div class="page-public">
        <?php if(isset($err_msg)){?>
        <div class="alert alert-danger"><?php echo $err_msg;?></div>
        <?php }?>
        <div class="editor_title">
          <input type="text" placeholder="这里输入文章标题" name="post_title" class="form-control post_title" value="<?php echo $edit_post->post_title;?>">
        </div>
        <div class="editor_main">
        <?php
        $arrs = array(
          'tinymce'=>1,
          'textarea_rows'=>20,
          'media_buttons' => false,
          'quicktags' => false,
          'teeny' => true,
        );
        wp_editor($edit_post->post_content, 'post_content', $arrs);
        ?>
        </div>
      </div>
    
    </div>
  </div>
  <div class="postPublishInfo">感谢您投稿到本博客，我们会认真对待你输入的每一个文字，并尽快给您答复，为了更好的管理投稿的文章和营造良好的网络环境，所有投稿的文章都会由管理员审核，内容必须健康向上，具体请先参阅我们的 <a href="/specification" target="_blank">发布规范</a> 。
  </div>
  <div class="postBox">
    <div class="wt-container">
      <div class="container postBoxFlex">
        <div class="postBoxSection">
        <template>
          <el-select v-model="statusValue" placeholder="公开度">
            <el-option
              v-for="item in statusOptions"
              :key="item.value"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
          </template>
        </div>
        <div class="postBoxSection">
          <el-select v-model="catgoryValue" placeholder="文章分类">
            <el-option
              v-for="item in catgoryOptions"
              :key="item.value"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
        </div>
        <div class="postBoxSection">
          <el-row>
            <el-button type="primary" round @click="checkForm" size="small">提交审核</el-button>
            <el-button type="info" round size="small" @click="saveDraft">保存草稿</el-button>
          </el-row>
        </div>
      </div>
    </div>
  </div>
  
  <el-dialog width="800px" custom-class="bannerTypeDia" :close-on-click-modal="false" title="媒体空间" :visible.sync="dialogVisible2">
    <el-tabs v-model="mediaType" type="card" @tab-click="mediaTabClick">
      <el-tab-pane :label="'图片管理 ' + mediaTotal" name="imageManege">
        <div class="bannerSearch">
          <div class="bsLeft">
            <el-input size="mini" v-model="mediaSearch" placeholder="请输入内容"></el-input>
          </div>
          <div class="bsBtn">
            <el-button type="success" size="mini" @click="doMediaSearch">搜索</el-button>
          </div>
        </div>
        <ul class="mediaList">
          <li v-for="(item, index) in mediaList" :class="{active:item.active}"
            @click="selectMedia(index, $event)"><img :src="item.guid"/></li>
        </ul>
        <div class="mediaDialogNav">
          <el-pagination
            background
            layout="pager"
            :current-page="mediaPage"
            :page-size="mediaRows"
            @current-change="getMedias"
            :total="mediaTotal">
          </el-pagination>
        </div>
        <div class="mediaDialogFooter">
          <el-button @click="dialogVisible2 = false" size="mini">取 消</el-button>
          <el-button type="primary" @click="insertImage" size="mini">确 定</el-button>
        </div>
      </el-tab-pane>
      <el-tab-pane label="上传图片" name="uploadImage">
        <div class="uploadDiv" @click="selectFile">
          <div><i class="el-icon-upload"></i></div>
          <p>点击选择图片</p>
        </div>
      </el-tab-pane>
      <el-tab-pane label="外部图片" name="externalImage">
        <div class="extImageBox">
          <div class="extILeft">图片地址</div>
          <div class="extIRight">
            <el-input size="mini" v-model="extImage" placeholder="请输入图片地址"></el-input>
          </div>
        </div>
        <div class="extImageBox">
          <div class="extIRight">
            <div class="hbImg">
              <span v-if="extImage==''">请输入图片地址</span>
              <img v-else :src="extImage">
            </div>
          </div>
        </div>
        <div class="mediaDialogFooter">
          <el-button @click="dialogVisible2 = false" size="mini">取 消</el-button>
          <el-button type="primary" @click="insertExtImage" size="mini">确 定</el-button>
        </div>
      </el-tab-pane>
    </el-tabs>
  </el-dialog>
  <input type="file" class="upFileInput hide"/>
  
</div>

<script>
var app = new Vue({
  el: '#app',
  data: {
    loadingObj: {},
    postID: <?php echo isset($_GET['id'])?$_GET['id']:0; ?>,
    postType: "<?php echo isset($_GET['type'])?'doc':'post'; ?>",
    statusValue: '',
    statusOptions: [
      {label: '公开', value: 'pending'},
      {label: '私密', value: 'private'},
    ],
    catgoryValue: '',
    catgoryOptions: JSON.parse('<?php echo json_encode($jsTerms); ?>'),
    user_id: <?php echo $current_user->ID; ?>,
    dialogVisible2: false,
    
    mediaList: [],
    mediaPage: 1,
    mediaTotal: 0,
    mediaRows: 21,
    mediaSearch: '',
    mediaType: 'imageManege',
    extImage: '',
    ajaxurl: '<?php echo admin_url('admin-ajax.php')?>',
    lastClickIndex: -1,
    
  },
  methods: {
    saveDraft(){
      this.statusValue = 'draft';
      this.checkForm();
    },
    checkForm(){
      if($('.post_title').val() == ''){
        this.$message.error('文章标题不能为空！');
        $('.post_title').focus();
        return false;
      }
      
      if(tinyMCE.activeEditor.getContent() == ''){
        this.$message.error('文章内容不能为空！');
        return false;
      }

      if(this.catgoryValue == ''){
        this.$message.error('请选择分类！');
        return false;
      }
      this.submitData();
    },
    submitData(){
      this.loadingObj = this.$loading({
        text: '提交中',
      });
      $.ajax({
        method: 'post',
        url: '/wjson/',
        data:{
          model: 'publishPost',
          action: JSON.stringify({
            post_title: $('.post_title').val(),
            post_content: tinyMCE.activeEditor.getContent(),
            post_type: this.postType,
            post_category: this.catgoryValue,
            post_status: this.statusValue,
            ID: this.postID,
          })
        },
        success:(res)=>{
          this.loadingObj.close();
          if(res.success){
            this.$confirm('你的文章已提交成功，需管理员审核后才能在显示！', '提交成功', {
              confirmButtonText: '查看文章',
              cancelButtonText: '再写一篇',
              center: true,
              type: 'warning'
            }).then(() => {
              tinymce.activeEditor.setContent("");
              $('.post_title').val("");
              window.location.href = res.data.link;
            }).catch(() => {
              tinymce.activeEditor.setContent("");
              $('.post_title').val("");
              window.location.href = '/publish';
            });
          }else{
            this.$alert(res.message, '提示');
          }
        }
      })
    },
    insertCon(txt) {
      tinyMCE.execCommand('mceInsertContent', false, txt);
    },
    
    // 图片管理
    mediaTabClick(e){
    },
    mediaShow(){
      this.dialogVisible2 = true;
      if(this.mediaList.length==0){
        setTimeout(()=>{
          this.getMedias();
        }, 300)
      }
    },
    selectFile(){
      $('.upFileInput').unbind();
      $('.upFileInput').trigger('click').change(()=>{
        this.uploadFile();
      });
    },
    uploadFile(){
      this.loadingObj = this.$loading();
      var formData = new FormData();
      formData.append("file", $(".upFileInput")[0].files[0]);
      $.ajax({
        url: '/wp-content/plugins/wj-tools/upload.php',
        type: 'post',
        data: formData,
        contentType: false,
        processData: false,
        success: (res)=>{
          console.log(res);
          this.loadingObj.close();
          res = JSON.parse(res);
          if(res.success){
            this.$message({
              showClose: true,
              message: res.message,
              type: 'success'
            });
            // 上传完成以后
            this.mediaType = 'imageManege';
            this.mediaPage = 1;
            this.getMedias();
          }else{
            this.$message({
              showClose: true,
              message: res.message,
              type: 'error'
            });
          }
          this.vueToHtml();
        }
      })
    },
    getMedias(e){
      this.loadingObj = this.$loading();
      if(!(typeof e == 'underfined') && e){
        this.mediaPage = e;
      }
      $.ajax({
        type: 'post',
        url: this.ajaxurl,
        data:{
          action: 'wjtGetMedias',
          search: this.mediaSearch,
          page: this.mediaPage,
          rows: this.mediaRows,
        },
        cache:false,
        dataType:'json',
        success: (res)=>{
          this.loadingObj.close();
          this.mediaList = res.data;
          this.mediaTotal = res.total;
        }
      });
    },
    doMediaSearch(){
      this.mediaPage = 1;
      this.getMedias();
    },
    insertImage(){
      var isFound = false;
      this.mediaList.map((item, index)=>{
        if(item.active){
          this.insertCon('<p><img src="'+item.guid+'" alt=""/></p>');
          this.dialogVisible2 = false;
          isFound = true;
        }
      })
      if(!isFound){
        this.$message({
          showClose: true,
          message: '请选择图片',
          type: 'error'
        });
      }
    },
    insertExtImage(){
      if(this.extImage==''){
        this.$message({
          showClose: true,
          message: '请输入图片地址',
          type: 'error'
        });
        return;
      }
      this.insertCon('<p><img src="'+this.extImage+'" alt=""/></p>');
      this.extImage = '';
      this.dialogVisible2 = false;
    },
    selectMedia(e, event){
      // 重复点击取消旋转
      if(this.lastClickIndex == e){
        this.mediaList[e].active = false;
        this.$set(this.mediaList, e, this.mediaList[e]);
        this.lastClickIndex = -1;
        return;
      }
      
      console.log(event);
      // 按下了 shift
      if(event.shiftKey){
        if(this.lastClickIndex>-1){
          if(e>this.lastClickIndex){
            this.mediaList.map((item, index)=>{
              if(index>=this.lastClickIndex && index<=e)
              this.mediaList[index].active = true;
              this.mediaList = [...this.mediaList];
            })
          }else{
            this.mediaList.map((item, index)=>{
              if(index<=this.lastClickIndex && index>=e)
              this.mediaList[index].active = true;
              this.mediaList = [...this.mediaList];
            })
          }
          return;
        }
      }
      
      // 安装了ctrl
      if(event.ctrlKey){
        this.mediaList[e].active = !this.mediaList[e].active;
        this.$set(this.mediaList, e, this.mediaList[e]);
        return;
      }
      
      this.lastClickIndex = e;
      this.mediaList.map((item, index)=>{
        this.mediaList[index].active = false;
      })
      this.mediaList[e].active = true;
      this.$set(this.mediaList, e, this.mediaList[e]);
    },
    // 图片管理 end
    
  },
  mounted(){
    var that = this;
    
    // 修改过页面，询问是否更改
    // 阻止关闭网页
    window.onbeforeunload = function () {
      if(!($('.post_title').val() == '') || !(tinyMCE.activeEditor.getContent() == '')){
        return true;
      }
    };
    
    $(function(){
      let upimgbtn = $('<div id="mceu_999" class="mce-widget mce-btn" tabindex="-1" aria-pressed="false" role="button" aria-label="upimg"><button id="mceu_999-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-image"></i></button></div>')
      setTimeout(()=>{
        $('.mce-i-link').parents('div.mce-btn').after(upimgbtn)
      }, 500)
      upimgbtn.click(function(){
        that.mediaShow();
      })
    })
    
  }
})
</script>
<?php get_footer();?>