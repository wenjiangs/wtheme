<?php
get_header();
$sql = 'select *, GET_FIRST_PINYIN_CHAR(wp_terms.name) as initials from wp_terms,
wp_term_taxonomy where wp_terms.term_id = wp_term_taxonomy.term_id and wp_term_taxonomy.taxonomy = "docs" and 
wp_term_taxonomy.parent = 0 ORDER BY GET_FIRST_PINYIN_CHAR(wp_terms.name) asc;';
$docs = $wpdb->get_results($sql);
$importIndex = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
$importIndex = str_split($importIndex);
$temA = array();
foreach($docs as $key=>$doc){
  if(!array_key_exists($doc->initials, $importIndex)){
    if(!array_key_exists($doc->initials, $temA)){
      $temA[$doc->initials] = array();
    }
    $temA[$doc->initials][] = $doc;
  }else{
    $temA['#'][] = $doc;
  }
}
?>
<div class="container">
  <div class="diHead">
    <h3>快速查找需要的帮助文档</h3>
    <p>共收录 <?php echo count($docs); ?> 个文档手册，更多文档还在不断完善中…</p>
  </div>
</div>
<div class="container">
  <div class="wt-container diMain">
    <?php foreach($temA as $key => $item){ ?>
    <div class="diRow">
      <div class="row">
        <div class="col-md-2">
          <div class="diLetter">
            <?php echo $key; ?>
          </div>
        </div>
        <div class="col-md-10">
          <ul class="diList">
            <?php foreach($item as $key2 => $item2){ $temrID = $item2->term_id * 1; ?>
            <li><a href="<?php echo get_term_link($temrID, 'docs'); ?>">
            <span class="diName"><?php echo $item2->name; ?></span>
            <span class="diCount"><?php echo $item2->count; ?></span>
            </a></li>
            <?php } ?>
          </ul>
        </div>
      </div>
    </div>
    <?php } ?>
  </div>
</div>

<?php get_footer(); ?>