<?php
get_header();

$countryCode = $wpdb->get_results('select * from wp_country_code');
?>

<div class="container">
  <div class="wt-container">
    <div class="breadcrumbs"><?php breadcrumbs(); ?></div>
    <h1 class="wt-single-title"><?php the_title(); ?></h1>
    <div class="wt-content">
      <table>
        <thead>
          <tr>
            <th>国家地区（EN）</th>
            <th>国家地区（CN）</th>
            <th>国际域名缩写</th>
            <th>电话代码</th>
            <th>时差</th>
          </tr>
        </thead>
        <?php foreach($countryCode as $item){ ?>
        <tr>
          <td><?php echo $item->country_en; ?></td>
          <td><?php echo $item->country_cn; ?></td>
          <td><?php echo $item->abbr; ?></td>
          <td><?php echo $item->area_code; ?></td>
          <td><?php echo $item->time_difference; ?></td>
        </tr>
        <?php } ?>
      </table>
    </div>
  </div>
</div>

<?php get_footer(); ?>