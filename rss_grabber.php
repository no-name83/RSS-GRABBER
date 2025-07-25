<?php
/**
Plugin Name: RSS GRABBER 
Description: RSS GRABBER is plugin  for importing, and displaying RSS feeds.With RSS GRABBER you can download the contents with video and pictures
Tags:rss,rss import,video rss,feeds, rss with video,rss grabber
Tested up to: 6.8.2
Requires PHP: 5.5
Version: 1.1
Stable tag: 1.1
License: GPL2
**/
ob_start(); 

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'rss_grabber_main' ) ) {

  class rss_grabber_main{


public static function rssgrabber_once(){

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once __DIR__ . '/rss_grabber_options.php';
require_once __DIR__. '/rss_grabber_table.php';



 } 



 

 



   
public static function rss_grab_cron_time( $schedules ) {
  
          
          
    $schedules['selected_minutes'] = array(
        'interval' => '60',
        'display'  => esc_html('Once Every  Selected Minutes'),
    );

    return $schedules;
}

public static  function rss_grab_cron_adding() {
    if ( ! wp_next_scheduled( 'rss_graber_cron' ) ) {
        wp_schedule_event( time(), 'selected_minutes', 'rss_graber_cron' );
    }

}

 public static function rss_grab_cron_remove() {
    wp_unschedule_event( wp_next_scheduled( 'rss_graber_cron' ), 'rss_graber_cron' );
}











 public static function rss_graber_admin_menu_option()
  {
     
     rss_grabber_main::rssgrabber_once();
    add_menu_page('rss-grabber-settings','RSS-GRABBER','manage_options','rssgrabber-admin-menu',array('rss_grabber_main','rssgrabber_scripts_page'),'dashicons-rss',200);
    add_submenu_page('rssgrabber-admin-menu', __('Panel','menu-test'), __('EDIT RSS','menu-test'), 'manage_options', 'edit-rss', array('rss_grabber_main','rss_grab_edit'));
       add_submenu_page('rssgrabber-admin-menu', __('Panel','menu-test'), __('RSS TABLE','menu-test'), 'manage_options', 'rss-tables', array('rss_grabber_main','rss_grab_table'));
     
  }





 public static function rss_grab_edit(){
 $rss_grabber_opt = new   rss_grabber_opt;  




 
  $rss_id = absint( $_GET['rss_id'] );



   if ( empty(intval($rss_id)) ) {
            
             wp_redirect(admin_url('admin.php?page=rss-tables'));
             exit;
        }




       
    $rss_details = $rss_grabber_opt->get_rss_data(intval($rss_id ));
  
  $rss_id=intval($rss_details['id']);
  $rss_grab_name=sanitize_text_field($rss_details['rss_grab_name']);
  $rss_grab_url=esc_url_raw($rss_details['rss_grab_url']);
  
  $rss_grab_cron=sanitize_text_field($rss_details['rss_grab_cron']); 
   
  $rss_grab_video=sanitize_text_field($rss_details['rss_grab_video']); 
  $rss_grab_categories=sanitize_text_field($rss_details['rss_grab_categories']); 
   $actions = ( isset( $_GET['action'] ) ) ? $_GET['action'] : '';

   if (is_user_logged_in()) {

      switch ( $actions ) {
               
                case 'runjob':
                     
                  
                   $rss_grabber_opt = new   rss_grabber_opt;  
                   
                   
                    $rss_grabber_opt->rss_grab_manuel($rss_grab_url,intval($rss_id));
                    
                   if (headers_sent()) {
    
}
wp_redirect(admin_url('admin.php?page=rss-tables'));
exit;


    
                   

                     break;
      
            } 

          }

    ?>

  <?php

  


  ?>


    <div class="">
      
      <form method="post" action="">
      <?php wp_nonce_field('rssgrab-nonce'); ?>
        <h2>RSS NAME :</h2>
    <textarea name="name_rss" class="large-text" ><?php print esc_attr($rss_grab_name);?></textarea>
         <h2> RSS URL:</h2>
    <textarea name="url_rss" class="large-text" ><?php print esc_url($rss_grab_url); ?></textarea>

    
      <label for=""><h1>SETTINGS:</h1></label>
      <label for=""><h3>Categories:</h3></label>
      <?php
       $terms="";
       $rt = explode(',',$rss_grab_categories);
      $terms = get_terms(array('category'), array('hide_empty' => false));
							foreach($terms as $tm) {
								echo '<input type="checkbox" name="rss_grabber_categories[]" id="rss-grab'.esc_attr($tm->term_id).'" value="'.esc_attr($tm->term_id).'"';
								if(in_array($tm->term_id,$rt)) {
									echo ' checked ';
								}
								echo '>'.esc_attr($tm->name).' ';
							}
  
      ?>

      <label for=""><h3>Publish Posts?</h3></label>
      <select  name="rss_grab_status">
        
    <option value="Publish" selected>Yes</option>'
     <option value="Pending">No</option>';
     
 

  

   
?>
  </select>
  <br><br>
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




  <br>
<label for=""><h3>Auto Download ?<br><h4></h4></br></h3></label>
                <select  name="rss_grab_cron">
         
         <?php if(esc_attr($rss_grab_cron)=='15'){
    echo '<option value="15" selected>Every 15 Minutes</option>';
    echo '<option value="30">Every 30 Minutes</option>';
     echo '<option value="60">Every 60 Minutes</option>';
     echo '<option value="No">No</option>'; 
     
  
}
  
else if(esc_attr($rss_grab_cron)=='30'){
    echo '<option value="30" selected>Every 30 Minutes</option>'; 
    echo '<option value="15">Every 15 Minutes</option>';
    echo '<option value="60">Every 60 Minutes</option>';
    echo '<option value="No" >No</option>'; 
  }
  else if(esc_attr($rss_grab_cron)=='60'){
    echo '<option value="60" selected>Every 60 Minutes</option>'; 
    echo '<option value="30">Every 30 Minutes</option>';
    echo '<option value="15">Every 15 Minutes</option>';
    echo '<option value="No" >No</option>'; 
  }


  else if(esc_attr($rss_grab_cron)=='No'){
    echo '<option value="No" selected>No</option>'; 
    echo '<option value="15">Every 15 Minutes</option>';
     echo '<option value="30">Every 30 Minutes</option>';
      echo '<option value="60">Every 60 Minutes</option>';
  }
    
?>
     
  </select>
  <br>

<br>
  <label for=""><h3>Download Video?</h3></label>
               <select  name="rss_grab_video">
        <?php if(esc_attr($rss_grab_video)=='b_contents'){
    echo '<option value="b_contents" selected>Yes-Before Contents</option>';
     echo '<option value="a_contents">Yes-After Contents</option>'; 
     echo '<option value="No">No</option>'; 
  
}
  
  else if(esc_attr($rss_grab_video)=='a_contents'){
    echo '<option value="a_contents" selected>Yes-After Contents</option>'; 
    echo '<option value="b_contents">Yes-Before Contents</option>'; 
     echo '<option value="No">No</option>'; 
  
  }
  elseif (esc_attr($rss_grab_video)=='No') {
    
    echo '<option value="No" selected>No</option>';
    echo '<option value="b_contents">Yes-Before Contents</option>';
     echo '<option value="a_contents" >Yes-After Contents</option>'; 
     
     
  }
 
  
  
?>
     
  </select>
  <br><br>
      <input type="submit" name="submit_rssgrab_update_new" class="button button-primary" value="Save">
       <?php wp_nonce_field('rss_grab_update', '_wpnonce'); ?>
      </form>
    </div>  

  <?php
//
  if (isset($_POST['submit_rssgrab_update_new'])  && isset($_POST['_wpnonce']) && check_admin_referer('rss_grab_update', '_wpnonce')) {
  	
    $rss_grab_cron_active;
  	  
  	 $rss_grab_name=sanitize_text_field($_POST['name_rss']);
   $rss_grab_url=esc_url_raw($_POST['url_rss']);
   $rss_grab_author = sanitize_text_field($_POST['rss_grab_author']);
    
    $rss_grab_cron = sanitize_text_field($_POST['rss_grab_cron']);
    
    $rss_grab_status = sanitize_text_field($_POST['rss_grab_status']);
    
    $rss_grab_video = sanitize_text_field($_POST['rss_grab_video']);
    
    $rss_grab_categories = implode(',', array_map('sanitize_text_field', $_POST['rss_grabber_categories']?? []));
    
       $rss_grabber_opt = new   rss_grabber_opt;  

     

       if($rss_grab_cron =='No'){

        $rss_grab_cron_active='0';
        
       }
       if ($rss_grab_cron !='No') {
        
        $rss_grab_cron_active='1';
         

       }
 

     $rss_grab_cron_old=$rss_grab_cron;
       

     if($rss_grab_cron!='No'){

     	$rss_grab_cron_old="";
     }

     else if($rss_grab_cron=='No'){


     	$rss_grab_cron=15;
     }

  

     
      
  


 $rss_grabber_opt->rss_grabber_update_rsslink($rss_grab_name,$rss_grab_url,$rss_grab_author,$rss_grab_cron,$rss_grab_status,$rss_grab_video,$rss_grab_categories,$rss_grab_cron_active,$rss_grab_cron_old,$rss_id);
     


 wp_redirect(admin_url('admin.php?page=rss-tables'));
exit;
  }
   

 
  }

public static function rss_grab_table(){


 	$table = new rss_grabber_table();
    $table->prepare_items();

    
?>

<div class="wrap">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>

    <form id="persons-table" method="GET">
        <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']) ?>"/>
        <?php $table->display() ?>
    </form>



<br><br>

<?php

 }




 public static function rssgrabber_scripts_page()
  {
                 $rss_grab_name = get_option('rss_grab_name','');
                 $rss_grab_url = get_option('rss_grab_url','');
                 $current_user = wp_get_current_user();

                 $users = get_users( array( 'fields' => array( 'ID','display_name','user_email') ) );
                
   


    ?>

  <?php

  


  ?>


    <div class="">
      
      <form method="post" action="">
      <?php wp_nonce_field('rssgrab-nonce'); ?>
        <h2>RSS NAME :</h2>
    <textarea name="name_rss" class="large-text" ><?php print esc_attr($rss_grab_name);?></textarea>
         <h2> RSS URL:</h2>
    <textarea name="url_rss" class="large-text" ><?php print esc_url($rss_grab_url); ?></textarea>

    
      <label for=""><h1>SETTINGS:</h1></label>
          <label for=""><h1>Categories:</h1></label>
      <?php

       	$terms = get_terms(array('category'), array('hide_empty' => false));
							foreach($terms as $tm) {
								echo '<input type="checkbox" name="rss_grabber_categories[]" id="logics-rss-taxitem-'.esc_attr($tm->term_id).'" value="'.esc_attr($tm->term_id).'" >'.esc_attr($tm->name).' ';
							}



   
  
      ?>

      <label for=""><h3>Publish Posts?</h3></label>
      <select  name="rss_grab_status">
       
    <option value="Publish" selected>Yes</option>
     <option value="Pending">No</option>
     

  
 
   
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




  <br>
<label for=""><h3>Auto Download ?<br><h4></h4></br></h3></label>
                <select  name="rss_grab_cron">
         
         
    <option value="15" selected>Every 15 Minutes</option>
    <option value="30">Every 30 Minutes</option>
     <option value="60">Every 60 Minutes</option>
     <option value="No">No</option> 
     
  

  

   
 
    
?>
     
  </select>
  <br>

<br>

  <label for=""><h3>Download Video?</h3></label>
               <select  name="rss_grab_video">
        

    
   <option value="No" selected>No</option>
    <option value="b_contents">Yes-Before Contents</option>
     <option value="a_contents" >Yes-After Contents</option>
     
     

 
  
  
?>
     
  </select>
  <br><br>
      <input type="submit" name="submit_rssgrab_update" class="button button-primary" value="Save">
      <?php wp_nonce_field('rssgrab-nonce', '_wpnonce'); ?>
      </form>
    </div>  
    <?php
  }


  




public static function  rss_grab_manuel_new()
  {
   


     if (is_user_logged_in()) {
      
rss_grabber_main::rssgrabber_once();
      if (isset($_POST['submit_rssgrab_update']) && isset($_POST['_wpnonce']) && check_admin_referer('rssgrab-nonce', '_wpnonce')) {
          
         

 



   $rss_grab_url_manuel=get_option('rss_grab_url');


    

          $rss_grabber_opt = new   rss_grabber_opt;  

  
   $rss_grab_cron_active;       

   $rss_grab_name=sanitize_text_field($_POST['name_rss']);
   $rss_grab_url=sanitize_text_field($_POST['url_rss']);
   $rss_grab_author = sanitize_text_field($_POST['rss_grab_author']);
    
    $rss_grab_cron = sanitize_text_field($_POST['rss_grab_cron']);
    $rss_grab_status = sanitize_text_field($_POST['rss_grab_status']);
    
    $rss_grab_video = sanitize_text_field($_POST['rss_grab_video']);
   
    $rss_grab_categories = implode(',', array_map('sanitize_text_field', $_POST['rss_grabber_categories']?? []));
    

    if($rss_grab_cron == 'No'){

      $rss_grab_cron_active='0';
      $rss_grab_cron_old = 15;

    }
    if($rss_grab_cron !='No'){

      $rss_grab_cron_active='1';

    }

  $current_timestamp = current_time('timestamp');  

    
    $timezone = get_option('timezone_string');
    if ($timezone) {
        date_default_timezone_set($timezone);
    } else {
        date_default_timezone_set('UTC');
    }
    $created_at = date('Y-m-d H:i:s',$current_timestamp);
    
    

   $rss_grabber_opt->rss_grabber_add_rsslink($rss_grab_name,$rss_grab_url,$rss_grab_author,$rss_grab_cron,$rss_grab_status,$rss_grab_video,$rss_grab_categories,$rss_grab_cron_active,$created_at,$rss_grab_cron_old);



wp_redirect(admin_url('admin.php?page=rss-tables'));
exit;











       
       
        }
     }

       
      
   
  }
  


 public static function rss_grab_auto_new($rss_grab_url,$rss_id){




 rss_grabber_main::rssgrabber_once();
 
  $rss_grabber_opt = new   rss_grabber_opt;  

 
  $rss_details = $rss_grabber_opt->get_rss_data(intval($rss_id ));
  
  
  $rss_grab_url1=sanitize_text_field($rss_details['rss_grab_url']);
  $rss_grab_cron = sanitize_text_field($rss_details['rss_grab_cron']);



if ($rss_grab_cron !="No") {
 
 
    
         

        
       $feed = new DOMDocument;
        $feed->load($rss_grab_url);
        $feed_array = array();
       $rss_grab_image_type=$feed->getElementsByTagName('enclosure');
         if($rss_grab_image_type->length == 0) {
          

       
           $rss_grabber_opt->rss_grab_media_content_auto($feed,intval($rss_id));



         

             }
    
       
       
            else     if($rss_grab_image_type->length > 0) {
          
        
       
            
              $rss_grabber_opt->rss_grab_enclosure_auto($feed,intval($rss_id));
         

             }

          


 


}
  }


  public static function ccm_toggle_cron() {
  

    if (isset($_GET['action'], $_GET['record_id'], $_GET['_wpnonce']) && $_GET['action'] === 'toggle_cron') {
        $id = intval($_GET['record_id']);
        if (wp_verify_nonce($_GET['_wpnonce'], 'toggle_cron_' . $id)) {
            global $wpdb;
            $table = $wpdb->prefix . 'rss_grabber';
            $current = $wpdb->get_var($wpdb->prepare("SELECT cron_active FROM $table WHERE id = %d", $id));
            $wpdb->update($table, ['cron_active' => !$current], ['id' => $id]);
        }
        wp_redirect(remove_query_arg(['action', 'record_id', '_wpnonce']));
        exit;
    }
}



public static function ccm_handle_cron() {
    global $wpdb;
    $table = $wpdb->prefix . 'rss_grabber';
    $records = $wpdb->get_results("SELECT * FROM $table WHERE cron_active = 1", ARRAY_A);

    
    $current_timestamp = current_time('timestamp');  

    
    $timezone = get_option('timezone_string');
    if ($timezone) {
        date_default_timezone_set($timezone);
    } else {
        date_default_timezone_set('UTC');
    }

    foreach ($records as $record) {
        
        $created = strtotime($record['created_at']);
        $interval = intval($record['rss_grab_cron']);  
        $rss_id = $record['id'];
        $rss_grab_url=$record['rss_grab_url'];

        
        $created_update = $created + ($interval * 60);  

        
        $created_update_mysql = date('Y-m-d H:i:s',$current_timestamp);



        
        if ($current_timestamp >= $created_update) {
            
            

            
            $wpdb->update(
                $table,
                ['created_at' => $created_update_mysql],  
                ['id' => $rss_id]  
            );


            

             

            $rss_grabber_opt = new   rss_grabber_main;

             
             $rss_grabber_opt->rss_grab_auto_new($rss_grab_url,intval($rss_id));
        }

        



        

        
    }
}


}
}







add_filter( 'cron_schedules', array('rss_grabber_main','rss_grab_cron_time'), 10, 1 );
add_action('admin_menu',array('rss_grabber_main','rss_graber_admin_menu_option'));
require_once __DIR__ . '/rss_grabber_options.php';
  $rss_grabber_opt = new   rss_grabber_opt;  

register_activation_hook( __FILE__, array(rss_grabber_main::class,'rss_grab_cron_adding') );


register_activation_hook( __FILE__, array($rss_grabber_opt,'rss_create_db') );

register_deactivation_hook( __FILE__, array(rss_grabber_main::class,'rss_grab_cron_remove') );
register_deactivation_hook( __FILE__, array($rss_grabber_opt,'rss_delete_db') );

//register_uninstall_hook(__FILE__, array($rss_grabber_opt,'rss_delete_db'));


add_action('init', array(rss_grabber_main::class, 'rss_grab_manuel_new'), 10, 2);
add_action('admin_init', array(rss_grabber_main::class, 'ccm_toggle_cron'));
  
 add_action( 'rss_graber_cron', array(rss_grabber_main::class, 'ccm_handle_cron'));
 


add_action('admin_enqueue_scripts', 'rss_grab_style');

function rss_grab_style() {
    wp_enqueue_style(
        'rss_grabber_styles', 
        plugin_dir_url(__FILE__) . 'rss_grab_style.css',
        array(), 
        '1.0.0'  
    );
}



 
   
  

