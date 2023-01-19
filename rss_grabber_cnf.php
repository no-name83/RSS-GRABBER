<?php

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'rss_grabber_cnf' ) ) {
    class rss_grabber_cnf{
function rss_grabber_saving(){


	
$rss_grab_name1 = get_option('rss_grab_name','');
                 $rss_grab_url = get_option('rss_grab_url','');

	echo '<div class="">
      
      <form method="post" action="">
     
        <h2>RSS NAME :</h2>
    <textarea name="name_rss" class="large-text">'.esc_attr($rss_grab_name1).'</textarea>
         <h2> RSS URL:</h2>
    <textarea name="url_rss" class="large-text">'.esc_url($rss_grab_url).'</textarea>

   <input type="submit" name="submit_rssgrab_update" class="button button-primary" value="Save">
      </form>'

    ;


   if (isset($_POST['submit_rssgrab_update'])) {
  
   	$rss_grab_url1=sanitize_url($_POST['url_rss']);
      $rss_grab_name1=sanitize_text_field($_POST['name_rss']);
         $rss_grab_url_update=update_option('rss_grab_url',$rss_grab_url1);
         $rss_grab_name1 = update_option('rss_grab_name',$rss_grab_name1);
          echo '<meta http-equiv="refresh" content="1">';
   }


}
}
}

?>