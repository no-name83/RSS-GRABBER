<?php
/**
Plugin Name: RSS GRABBER 
Description: RSS GRABBER is plugin  for importing, and displaying RSS feeds.With RSS GRABBER you can download the contents with video and pictures
Tags:rss,rss import,feed,feeds, rss with video
Tested up to: 6.1.1
Requires PHP: 5.5
Version: 1.0
Stable tag: 1.0
License: GPL2
**/


if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'rss_grabber_main' ) ) {

  class rss_grabber_main{


 function rssgrabber_once(){

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once __DIR__ . '/rss_grabber_options.php';
require_once __DIR__ . '/rss_grabber_cnf.php';


 } 



 

 



   
function rss_grab_cron_time( $schedules ) {
  $options = get_option( 'rss_grab_cron');
          
          $interval = ( absint( $options ) * 60 );
    $schedules['selected_minutes'] = array(
        'interval' => $interval,
        'display'  => esc_html('Once Every  Selected Minutes'),
    );

    return $schedules;
}



/*function db_crontest_deactivate() {
    wp_unschedule_event( wp_next_scheduled( 'rss_graber_cron' ), 'rss_graber_cron' );
}*/

function rss_grab_cron_remove() {
    wp_unschedule_event( wp_next_scheduled( 'rss_graber_cron' ), 'rss_graber_cron' );
}











  function rss_graber_admin_menu_option()
  {
  
    add_menu_page('rss-grabber-settings','RSS-GRABBER','manage_options','rssgrabber-admin-menu',array('rss_grabber_cnf','rss_grabber_saving'),'dashicons-rss',200);
    add_submenu_page('rssgrabber-admin-menu', __('Panel','menu-test'), __('SETTINGS','menu-test'), 'manage_options', 'sub-page', array('rss_grabber_main','rssgrabber_scripts_page'));
     
  }

  

 

  function rssgrabber_scripts_page()
  {
                 $rss_grab_name = get_option('rss_grab_name','');
                 $rss_grab_url = get_option('rss_grab_url','');
                 $current_user = wp_get_current_user();

                 $users = get_users( array( 'fields' => array( 'ID','display_name','user_email') ) );
                
   


    ?>




    <div class="">
      
      <form method="post" action="">
      <?php wp_nonce_field('rssgrab-nonce'); ?>
        <h2>RSS NAME :</h2>
    <textarea name="name_rss" class="large-text" readonly><?php print esc_attr($rss_grab_name);?></textarea>
         <h2> RSS URL:</h2>
    <textarea name="url_rss" class="large-text" readonly><?php print esc_url($rss_grab_url); ?></textarea>

    
      <label for=""><h1>SETTINGS:</h1></label>
      <label for=""><h3>Publish Posts?</h3></label>
      <select  name="rss_grab_status">
        <?php if(get_option('rss_grab_status')=='Publish'){
    echo '<option value="Publish" selected>Yes</option>';
     echo '<option value="Pending">No</option>'; 
     
 
}
  
  else if(get_option('rss_grab_status')=='Pending'){
    echo '<option value="Pending" selected>No</option>'; 
    echo '<option value="Publish" >Yes</option>'; 
  }
   
?>
  </select>
  <br>
 <label for=""><h3>Author?</h3></label>
 <select  name="rss_grab_author">
        <?php if(get_option('rss_grab_author')!=''){
  

      
         $author=get_option('rss_grab_author','');
             $user = get_user_by( 'email', $author );
             $user_id1= $user->display_name;
             
    sanitize_text_field($user_email=$user_id->user_email);
     sanitize_text_field($user= $user_id->display_name);
    
    echo '<option value="'.esc_attr($user_email).'">'.esc_attr($user_id1).'</option>';
 
}

else{


  $users = get_users( array( 'fields' => array( 'ID','display_name','user_email') ) );

 foreach($users as $user_id){

     sanitize_text_field($mail= $user_id->user_email);
    sanitize_text_field($name =  $user_id->display_name);


       echo '<option value="'.esc_attr($mail).'" >'.esc_attr($name).'</option>';


 }
}
  
 
   
?>
  </select>
  <br>



                    <label for=""><h3>Save Categories?</h3><h4>If tag name is category</h4></label>
                    <select  name="rss_grab_cat">
  
    <?php if(get_option('rss_grab_cat')=='Yes'){
    echo '<option value="Yes" selected>Yes</option>';
     echo '<option value="No">No</option>'; 
     
  
}
  
  else if(get_option('rss_grab_cat')=='No'){
    echo '<option value="No" selected>No</option>'; 
    echo '<option value="Yes" >Yes</option>'; 
  }
  
?>
  </select>
  <br>
<label for=""><h3>Auto Download ?<br><h4></h4></br></h3></label>
                <select  name="rss_grab_cron">
         
         <?php if(get_option('rss_grab_cron')=='15'){
    echo '<option value="15" selected>Every 15 Minutes</option>';
    echo '<option value="30">Every 30 Minutes</option>';
     echo '<option value="60">Every 60 Minutes</option>';
     echo '<option value="No">No</option>'; 
     
  
}
  
else if(get_option('rss_grab_cron')=='30'){
    echo '<option value="30" selected>Every 30 Minutes</option>'; 
    echo '<option value="15">Every 15 Minutes</option>';
    echo '<option value="60">Every 60 Minutes</option>';
    echo '<option value="No" >No</option>'; 
  }
  else if(get_option('rss_grab_cron')=='60'){
    echo '<option value="60" selected>Every 60 Minutes</option>'; 
    echo '<option value="30">Every 30 Minutes</option>';
    echo '<option value="15">Every 15 Minutes</option>';
    echo '<option value="No" >No</option>'; 
  }


  else if(get_option('rss_grab_cron')=='No'){
    echo '<option value="No" selected>No</option>'; 
    echo '<option value="15">Every 15 Minutes</option>';
     echo '<option value="30">Every 30 Minutes</option>';
      echo '<option value="60">Every 60 Minutes</option>';
  }
    
?>
     
  </select>
  <br>
  <label for=""><h3>Download All Images?</h3></label>
           <select  name="rss_grab_photo">
      <?php if(get_option('rss_grab_photo')=='Yes'){
    echo '<option value="Yes" selected>Yes</option>';
     echo '<option value="No">No</option>'; 
     
 
}
  
  else if(get_option('rss_grab_photo')=='No'){
    echo '<option value="No" selected>No</option>'; 
    echo '<option value="Yes" >Yes</option>'; 
  }
  
?>
     
  </select>
<br><br>
 <br>
  <label for=""><h3>Download Video?</h3></label>
               <select  name="rss_grab_video">
        <?php if(get_option('rss_grab_video')=='b_contents'){
    echo '<option value="b_contents" selected>Yes-Before Contents</option>';
     echo '<option value="a_contents">Yes-After Contents</option>'; 
     echo '<option value="No">No</option>'; 
  
}
  
  else if(get_option('rss_grab_video')=='a_contents'){
    echo '<option value="a_contents" selected>Yes-After Contents</option>'; 
    echo '<option value="b_contents">Yes-Before Contents</option>'; 
     echo '<option value="No">No</option>'; 
  
  }
  elseif (get_option('rss_grab_video')=='No') {
    
    echo '<option value="No" selected>No</option>';
    echo '<option value="b_contents">Yes-Before Contents</option>';
     echo '<option value="a_contents" >Yes-After Contents</option>'; 
     
     
  }
 
  
  
?>
     
  </select>
  <br><br>
      <input type="submit" name="submit_rssgrab_update" class="button button-primary" value="Save And Download">
      </form>
    </div>  
    <?php
  }


  




  function rss_grab_manuel_new()
  {
   


     if (is_user_logged_in()) {
      
rss_grabber_main::rssgrabber_once();
       if (isset($_POST['submit_rssgrab_update']) && wp_verify_nonce($_POST['_wpnonce'], 'rssgrab-nonce')) {
          
         

 



   $rss_grab_url_manuel=get_option('rss_grab_url');




          $rss_grabber_opt = new   rss_grabber_opt;  

   $rss_grabber_opt->rss_grab_manuel($rss_grab_url_manuel);




       
       
        }
     }

       
      
   
  }
  


  function rss_grab_auto_new(){

  rss_grabber_main::rssgrabber_once();
 
  $rss_grabber_opt = new   rss_grabber_opt;  

$rss_grab_cron=get_option('rss_grab_cron');
$rss_grab_status=get_option('rss_grab_status','');
$rss_grab_photo=get_option('rss_grab_photo','');
$rss_grab_video=get_option('rss_grab_video','');
 $rss_grab_cat=get_option('rss_grab_cat','');
if ($rss_grab_cron !="No") {
 
 
          
  $rss_grab_url1 = get_option('rss_grab_url');
         

        
       $feed = new DOMDocument;
        $feed->load($rss_grab_url1);
        $feed_array = array();
       $rss_grab_image_type=$feed->getElementsByTagName('enclosure');
         if($rss_grab_image_type->length == 0) {
          

       
           $rss_grabber_opt->rss_grab_media_content_auto($feed);
         

             }
    
       
       
            else     if($rss_grab_image_type->length > 0) {
          
        
       
            
              $rss_grabber_opt->rss_grab_enclosure_auto($feed);
         

             }

          


 


}
  }


}
}







add_filter( 'cron_schedules', array('rss_grabber_main','rss_grab_cron_time'), 10, 1 );
add_action('admin_menu',array('rss_grabber_main','rss_graber_admin_menu_option'));
require_once __DIR__ . '/rss_grabber_options.php';
register_activation_hook( __FILE__, 'rss_grabber_opt::rss_grab_cron_adding' );
register_deactivation_hook( __FILE__, 'rss_grab_cron_remove' );


add_action('init',array('rss_grabber_main','rss_grab_manuel_new'), 10,2);
   add_action( 'rss_graber_cron', array('rss_grabber_main','rss_grab_auto_new' ));



 
   
  

