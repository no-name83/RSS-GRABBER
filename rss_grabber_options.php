<?php

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'rss_grabber_opt' ) ) {

  class rss_grabber_opt{
   

       function get_rss_data( $rss_id ) {
            
             global $wpdb;

             $table_name=$wpdb->prefix."rss_grabber";

             $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $rss_id ), ARRAY_A );   

             return $result;
        }


     function get_auto_rss_data() {
            
             global $wpdb;

             $table_name=$wpdb->prefix."rss_grabber";

             $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name "), ARRAY_A );   

             return $result;
        }




function rss_create_db()
{
    global $wpdb;
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    // creates my_table in database if not exists
   $rss_grabber_table = $wpdb->prefix . "rss_grabber";
    $charset_collate = $wpdb->get_charset_collate();
    $rss_grabber_sql = "CREATE TABLE IF NOT EXISTS $rss_grabber_table (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `rss_grab_name` varchar(100) NOT NULL,
        `rss_grab_url` varchar(1000) NOT NULL,
    `rss_grab_author` varchar(50) NOT NULL,
    `rss_grab_cron` varchar(5) NOT NULL,
    `rss_grab_status` varchar(15) NOT NULL,
    `rss_grab_video` varchar(50) NOT NULL,
    `rss_grab_categories` varchar(50) NOT NULL,
    `cron_active`        tinyint(1) NULL,
    `created_at`         DateTime  NOT NULL,
    `rss_grab_cron_old` varchar(2) NULL,


    UNIQUE (`id`)
    
    ) $charset_collate;";
    
   dbDelta($rss_grabber_sql);
   
}
function rss_delete_db(){

global $wpdb;
    $rss_grabber_table_name = $wpdb->prefix . 'rss_grabber';
    $rss_grabber_sql = "DROP TABLE IF EXISTS $rss_grabber_table_name";
    $wpdb->query($rss_grabber_sql);


  
}

function rss_grabber_add_rsslink($rss_grab_name,$rss_grab_url,$rss_grab_author,$rss_grab_cron,$rss_grab_status,$rss_grab_video,$rss_grab_categories,$rss_grab_cron_active,$created_at,$rss_grab_cron_old){

global $wpdb;

$rss_grabber_table=$wpdb->prefix."rss_grabber";
$wpdb->insert($rss_grabber_table,array("rss_grab_name"=>$rss_grab_name,"rss_grab_url"=>$rss_grab_url,'rss_grab_author'=>$rss_grab_author,'rss_grab_cron'=>$rss_grab_cron,'rss_grab_status'=>$rss_grab_status,'rss_grab_video'=>$rss_grab_video,'rss_grab_categories'=>$rss_grab_categories,'cron_active'=>$rss_grab_cron_active,'created_at' => $created_at,'rss_grab_cron_old' => $rss_grab_cron_old),array('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s'));

}

function rss_grabber_update_rsslink($rss_grab_name,$rss_grab_url,$rss_grab_author,$rss_grab_cron,$rss_grab_status,$rss_grab_video,$rss_grab_categories,$rss_grab_cron_active,$rss_grab_cron_old,$rss_id){

global $wpdb;

$rss_grabber_table=$wpdb->prefix."rss_grabber";


 $wpdb->query( 
                $wpdb->prepare( 
                    "
                    UPDATE $rss_grabber_table
                    SET rss_grab_name = %s,
                    rss_grab_url = %s,
                    rss_grab_author = %s,
                  
                    rss_grab_cron = %s,
                    rss_grab_status = %s,
                    
                    rss_grab_video = %s,
                    rss_grab_categories= %s,
                    cron_active=%s,
                    rss_grab_cron_old=%s

                    WHERE id = %d",
                    $rss_grab_name,
                    $rss_grab_url,
                    $rss_grab_author,
                    
                    $rss_grab_cron,
                    $rss_grab_status,
                    
                    $rss_grab_video,
                    $rss_grab_categories,
                    $rss_grab_cron_active,
                    $rss_grab_cron_old,
                    $rss_id
                    )
                );

}

  /* function rss_grab_category($rss_grab_cat_new,$cat){

                $cat_ID = get_cat_ID($rss_grab_cat_new);
             if( intval($cat_ID)==0 ) {
            $arg = array( 'description' => $rss_grab_cat_new);
            $cat_ID = wp_insert_term(sanitize_text_field($cat), "category", sanitize_text_field($rss_grab_cat_new));

            echo '<meta http-equiv="refresh" content="1">';
            
           
        }



   }*/



function rss_grab_getDatePatterns() {
    $today = new DateTime();
    $today->setTime(0, 0, 0); 

    $five_days_ago = new DateTime();
    $five_days_ago->modify('-5 days');
    $five_days_ago->setTime(0, 0, 0);

    $date_today = $today->format('Y-m-d');
    $date_five_days_ago = $five_days_ago->format('Y-m-d');

    $date_pattern = $date_five_days_ago . '%';
    $date_pattern_today = $date_today . '%';

    return [
        'date_pattern' => $date_pattern,
        'date_pattern_today' => $date_pattern_today
    ];
}


function rss_grab_reverseDomNodeList($nodeList) {
    $array = [];
    foreach ($nodeList as $node) {
        $array[] = $node;
    }
    return array_reverse($array);
}




function rss_grab_image($rss_grab_image,$post_id){


          $rss_grab_image_new= sanitize_url($rss_grab_image);                               
  $response = wp_remote_get($rss_grab_image_new);
    $filename = sanitize_text_field(uniqid()."."."jpg");


    $data=array();
       
  if( !is_wp_error( $response ) ){
  $bits = wp_remote_retrieve_body( $response );
  $upload = wp_upload_bits( $filename, null, $bits );
  $data['guid'] = $upload['url'];
  $data['post_mime_type'] = 'image/jpeg';
  $attach_id = wp_insert_attachment( $data, $upload['file'], $post_id );
  $attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
  

  add_post_meta($post_id, '_thumbnail_id', $attach_id, true);  
 
}
   
}


function add_featured_image_from_url($rss_grab_image, $post_id) {
    
    $grab_image_new = sanitize_url($rss_grab_image);

    
    $response = wp_remote_get($grab_image_new);

    
    if (!is_wp_error($response)) {
        
        $bits = wp_remote_retrieve_body($response);

        
        $filename = sanitize_file_name(uniqid() . '.jpg');

        
        $upload = wp_upload_bits($filename, null, $bits);

        
        if (!$upload['error']) {
            $file_path = $upload['file'];
            $file_url  = $upload['url'];

            
            $attachment = array(
                'guid'           => $file_url,
                'post_mime_type' => 'image/jpeg',
                'post_title'     => sanitize_text_field(pathinfo($filename, PATHINFO_FILENAME)),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );

            
            $attach_id = wp_insert_attachment($attachment, $file_path, $post_id);

            
            $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
            wp_update_attachment_metadata($attach_id, $attach_data);

            
            update_post_meta($post_id, '_thumbnail_id', $attach_id);

            return true;  
        } else {
            return false; 
        }
    } else {
        return false; 
    }
}






function rss_grab_media_content($feed,$rss_id){







     global $wpdb;

    $rss_details= $this->get_rss_data(intval($rss_id));

    $rss_photo =  sanitize_text_field($rss_details['rss_grab_categories']);

    

     $rss_grab_status=sanitize_text_field($rss_details['rss_grab_photo']);
    $rt=sanitize_text_field($rss_details['rss_grab_categories']);
    $rss_grab_author=sanitize_text_field($rss_details['rss_grab_author']);

    $video1 =sanitize_text_field($rss_details['rss_grab_video']);
    
   $termArray = array();
      foreach(explode(',',$rt) as $r) {
        $term = get_term( $r, 'category' );
        $termArray[] = $term->name;
      }
   
   

   $items = $feed->getElementsByTagName('item');
$itemArray = $this->rss_grab_reverseDomNodeList($items);

       

    


            foreach($itemArray as $rss_grab_info){
            array (
                              
            	               
                                  $title= $rss_grab_info->getElementsByTagName('title')->item(0)->nodeValue,
                                    $desc = $rss_grab_info->getElementsByTagName('description')->item(0)->nodeValue,
                                   
                                   
                                  
                                  
            );
            $rss_grab_cat_new=sanitize_text_field($cat);
             $rss_grab_title_new=sanitize_text_field($title);
             $rss_grab_desc=sanitize_text_field($desc);
             $rss_grab_title=trim($rss_grab_title_new);


              $timezone = get_option('timezone_string');
date_default_timezone_set($timezone);





$datePatterns = $this->rss_grab_getDatePatterns();

$start_date = $datePatterns['date_pattern'];
$end_date = $datePatterns['date_pattern_today'];  


$table_name = $wpdb->prefix . "posts";


/*$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM `$table_name` 
        WHERE post_title = %s
        AND (post_date LIKE %s OR post_date LIKE %s)", 
        $rss_grab_title,  
        $date_pattern,    
        $date_pattern_today  
    )
);*/


$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM `$table_name` 
        WHERE post_title = %s 
        AND DATE(post_date) BETWEEN %s AND %s", 
        $rss_grab_title,  
        $start_date, 
        $end_date     
    )
);


        
             
                  if($wpdb->num_rows == 0) {
              
                 
                    $cat_ID = get_cat_ID($rss_grab_cat_new);
    


            if ($rss_grab_cat=sanitize_text_field($_POST['rss_grab_cat'])=='Yes') {
      

        $this->rss_grab_category($rss_grab_cat_new,$cat);
                  }    
                   

                
                 $author=$rss_grab_author;
             $user = get_user_by( 'email', $author );
             $user_id= $user->ID;
             
            
           echo '<meta http-equiv="refresh" content="1">';
                    $my_post = array(
    'post_title'    => $rss_grab_title,
    'post_content'  => $rss_grab_desc,
    'post_status'   => $rss_details['rss_grab_status'],
    
     'post_author'=> intval($user_id= $user->ID),
    
);
                    $post_id= wp_insert_post($my_post);

                    wp_set_object_terms( $post_id, $termArray, 'category' );


                                    $i=0;
                                      foreach($rss_grab_info->childNodes as $childNode) {
        /*if($childNode->tagName == 'media:content' || $childNode->tagName == 'media:thumbnail' && $childNode->getAttribute('type')!="video/mp4") {




                  $post_id=wp_update_post($my_post1);

       
          $rss_grab_image=$childNode->getAttribute('url');
          
         


             $i++;
          if ($i==1) {
           
             //add_featured_image_from_url

            //$this->rss_grab_image($rss_grab_image,$post_id);
            //$this->add_featured_image_from_url($rss_grab_image,$post_id);


          }



     

        }*/

     

        

       

          if($video1!='No' && $childNode->tagName == 'media:content'  && $childNode->getAttribute('type')=="video/mp4") {
           
           
           
         
   
        $table_name=$wpdb->prefix."posts";
        /*$id = $wpdb->get_var($wpdb->prepare("SELECT * FROM `$table_name`  WHERE post_title = %s
  ",$rss_grab_title));*/

    $id = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT * FROM `$table_name` 
        WHERE post_title = %s 
        AND DATE(post_date) BETWEEN %s AND %s", 
        $rss_grab_title,  
        $start_date,  
        $end_date     
    )
);
             if (isset($id)) {
            
          $video = $childNode->getAttribute('url');
$rss_grab_video=sanitize_url($video);
$response = wp_remote_get($rss_grab_video);
  if( !is_wp_error( $response ) ){
  $bits = wp_remote_retrieve_body( $response );
  $filename = sanitize_text_field(uniqid().'.mp4');
  $upload = wp_upload_bits( $filename, null, $bits );
 
}
          }
        $rss_grab_upload_dir = wp_upload_dir();

$after_contents='
<!-- wp:video  -->
<figure class="wp-block-video"><video controls controlsList="nodownload" src="'.$rss_grab_upload_dir['url'].'/'.sanitize_text_field($filename).'"></video></figure>
<!-- /wp:video -->'."<p>".$desc."</p>";



$before_contents=$desc.'<!-- wp:video  -->
<figure class="wp-block-video"><video controls controlsList="nodownload" src="'.$rss_grab_upload_dir['url'].'/'.sanitize_text_field($filename).'"></video></figure>
<!-- /wp:video -->'
;

  
if (sanitize_text_field($video1)!="No"  && sanitize_text_field($video1)!="b_contents" ) {
        
             
               $rss_grab_video1=$video;
                  $my_post1 = array(
      'ID' => $id,
      'post_content'  => $before_contents,
  
    
);
 
        }
          
        if (sanitize_text_field($video1)!="No"  && sanitize_text_field($video1)!="a_contents" ) {
             
             $rss_grab_video1=$video;
                  $my_post1 = array(
      'ID' => intval($id),
      'post_content'  => $after_contents,
    
);
 
        }
  $post_id=wp_update_post($my_post1);
        }
     

                   
        }

      



       

        }
		

    
		
		if($wpdb->num_rows > 0) {
			  
      
					 
		  /*$id = $wpdb->get_var($wpdb->prepare("SELECT * FROM `$table_name`  WHERE post_title = %s
  ",$rss_grab_title));*/


  $id = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT * FROM `$table_name` 
        WHERE post_title = %s 
        AND DATE(post_date) BETWEEN %s AND %s", 
        $rss_grab_title,  
        $start_date,  
        $end_date     
    )
);
		 $parent_id = $id; 
$mime_type = 'image/jpeg'; 



$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM `$table_name` 
         WHERE post_parent = %d 
           AND post_mime_type = %s",
        $parent_id,
        $mime_type
    )
);

if (  empty($results) ) {
    
    


       $i=0;
   foreach($rss_grab_info->childNodes as $childNode) {
        if($childNode->tagName == 'media:content' || $childNode->tagName == 'media:thumbnail') {
			
			$rss_grab_image=$childNode->getAttribute('url');
      if($childNode->getAttribute('type')!="video/mp4"){
			
			   $i++;
          if ($i==1) {
           


            $this->add_featured_image_from_url($rss_grab_image,$id);


          }
		  
		  
			
		}
		
		}
   }
   

        


  
           
	
	
	
	
	
	
	
	
} 
			
			
			
		}

		 
		 
	
		
         }


      


}



function rss_grab_enclosure($feed,$rss_id){

      global $wpdb;




    $rss_details= $this->get_rss_data($rss_id);

    $rss_photo =  $rss_details['rss_grab_categories'];

    

     $rss_grab_status=$rss_details['rss_grab_status'];
    $rt=$rss_details['rss_grab_categories'];
    $rss_grab_author=sanitize_text_field($rss_details['rss_grab_author']);

    $video1 = $rss_details['rss_grab_video'];
    echo $video1;
   $termArray = array();
      foreach(explode(',',$rt) as $r) {
        $term = get_term( $r, 'category' );
        $termArray[] = $term->name;
      }
   
   




          $items = $feed->getElementsByTagName('item');
$itemArray = $this->rss_grab_reverseDomNodeList($items);
         

            foreach($itemArray as $rss_grab_info){
                      $i=0;
            array (             
                                  $title= $rss_grab_info->getElementsByTagName('title')->item(0)->nodeValue,
                                 
                                    $desc = $rss_grab_info->getElementsByTagName('description')->item(0)->nodeValue,
                                   $rss_grab_image= $rss_grab_info->getElementsByTagName('enclosure')->item(0)->getAttribute('url'),
                   $img_type= $rss_grab_info->getElementsByTagName('enclosure')->item(0)->getAttribute('type')
                                  
            );
            
            $rss_grab_title_new=sanitize_text_field($title);
             $rss_grab_desc=sanitize_text_field($desc);
             $rss_grab_title=trim($rss_grab_title_new);
              $timezone = get_option('timezone_string');
date_default_timezone_set($timezone);




$datePatterns = $this->rss_grab_getDatePatterns();

/*$date_pattern = $datePatterns['date_pattern'];
$date_pattern_today = $datePatterns['date_pattern_today']; */
$start_date = $datePatterns['date_pattern'];
$end_date = $datePatterns['date_pattern_today'];  




$table_name = $wpdb->prefix . "posts";


/*$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM `$table_name` 
        WHERE post_title = %s
        AND (post_date LIKE %s OR post_date LIKE %s)", 
        $rss_grab_title,  
        $date_pattern,    
        $date_pattern_today  
    )
);*/


$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM `$table_name` 
        WHERE post_title = %s 
        AND DATE(post_date) BETWEEN %s AND %s", 
        $rss_grab_title,  
        $start_date,  
        $end_date     
    )
);

        
           
                  if($wpdb->num_rows == 0) {
     
        
         
           echo '<meta http-equiv="refresh" content="1">';
              
            $my_post = array(
   'post_title'    => $rss_grab_title,
    'post_content'  => $rss_grab_desc,
    
    'post_status'   => $rss_grab_status,
   


);
                    $post_id= wp_insert_post($my_post);
                    wp_set_object_terms( $post_id, $termArray, 'category' );

                    

                    
          


//for video

if (sanitize_text_field($img_type)=="video/mp4") {


   $table_name=$wpdb->prefix."posts";
        /*$id = $wpdb->get_var($wpdb->prepare("SELECT * FROM `$table_name`  WHERE post_title = %s
  ",$rss_grab_title));*/
    $id = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT * FROM `$table_name` 
        WHERE post_title = %s 
        AND DATE(post_date) BETWEEN %s AND %s", 
        $rss_grab_title,  
        $start_date,  // '2025-07-01' gibi bir tarih
        $end_date     // '2025-07-18' gibi bir tarih
    )
);

         if (isset($id)) {
            
          $video = $childNode->getAttribute('url');
$rss_grab_video=sanitize_url($video);
$response = wp_remote_get($rss_grab_video);
  if( !is_wp_error( $response ) ){
  $bits = wp_remote_retrieve_body( $response );
  $filename = sanitize_text_field(uniqid().'.mp4');
  $upload = wp_upload_bits( $filename, null, $bits );
 
}
          }


  $rss_grab_upload_dir = wp_upload_dir();

$after_contents='
<!-- wp:video  -->
<figure class="wp-block-video"><video controls controlsList="nodownload" src="'.$rss_grab_upload_dir['url'].'/'.sanitize_text_field($filename).'"></video></figure>
<!-- /wp:video -->'."<p>".$desc."</p>";

$before_contents=$desc.'<!-- wp:video  -->
<figure class="wp-block-video"><video controls controlsList="nodownload" src="'.$rss_grab_upload_dir['url'].'/'.sanitize_text_field($filename).'"></video></figure>
<!-- /wp:video -->'
;





  if (sanitize_text_field($_POST["rss_grab_video"])!="No"  && sanitize_text_field($_POST["rss_grab_video"])!="b_contents" ) {
          
             $rss_grab_video1=get_option('rss_grab_video','');
                  $my_post1 = array(
      'ID' => intval($id),
      'post_content'  => $before_contents,
    
);
 
        }


                if (sanitize_text_field($_POST["rss_grab_video"])!="No"  && sanitize_text_field($_POST["rss_grab_video"])!="a_contents" ) {
          
             $rss_grab_video1=get_option('rss_grab_video','');
                  $my_post1 = array(
      'ID' => intval($id),

      'post_content'  => $after_contents,
    
);
 
        }
        $post_id=wp_update_post($my_post1);


}


  

   }

   

  
/* $id = $wpdb->get_var($wpdb->prepare("SELECT * FROM `$table_name`  WHERE post_title = %s
  ",$rss_grab_title));*/


    $id = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT * FROM `$table_name` 
        WHERE post_title = %s 
        AND DATE(post_date) BETWEEN %s AND %s", 
        $rss_grab_title,  
        $start_date,  
        $end_date     
    )
);




		 $parent_id = $id; 
$mime_type = 'image/jpeg'; 



$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM `$table_name` 
         WHERE post_parent = %d 
           AND post_mime_type = %s",
        $parent_id,
        $mime_type
    )
);

if (  empty($results) ) {
  

           
	$this->add_featured_image_from_url($rss_grab_image, $parent_id);


  
       

          }
          

         }



    
}




  function rss_grab_manuel($rss_grab_url,$rss_id){



       global $wpdb;
     

$rss_grab_cat = sanitize_text_field($_POST['rss_grab_cat']) ?? null;

$rss_grab_cron=sanitize_text_field($_POST['rss_grab_cron']) ?? null;
$rss_grab_status= sanitize_text_field($_POST['rss_grab_status']) ?? null;
$rss_grab_photo = sanitize_text_field($_POST['rss_grab_photo']) ?? null;
$rss_grab_video = sanitize_text_field($_POST['rss_grab_video']) ?? null;
$rss_grab_author = sanitize_text_field($_POST['rss_grab_author']) ?? null;

       $rss_details = $this->get_rss_data( intval($rss_id ));





  
   

    
   
       $feed = new DOMDocument;
      $feed->load(sanitize_text_field($rss_grab_url));
        $feed_array = array();
       $rss_grab_image_type=$feed->getElementsByTagName('enclosure');
         if($rss_grab_image_type->length == 0) {
          
         

          $this->rss_grab_media_content($feed,intval($rss_id));
          
         

             }
    
       
       
            else     if($rss_grab_image_type->length > 0) {
            
              $this->rss_grab_enclosure($feed,$rss_id);
         

             }











  }



function rss_grab_media_content_auto($feed,$rss_id){





    global $wpdb;




    $rss_details= $this->get_rss_data($rss_id);

    $rss_photo =  $rss_details['rss_grab_categories'];

    

     $rss_grab_status=sanitize_text_field($rss_details['rss_grab_status']);
    $rt=sanitize_text_field($rss_details['rss_grab_categories']);
    $rss_grab_author=sanitize_text_field($rss_details['rss_grab_author']);

    
    $rss_grab_video1=sanitize_text_field($rss_details['rss_grab_video']);
   
   $termArray = array();
      foreach(explode(',',$rt) as $r) {
        $term = get_term( $r, 'category' );
        $termArray[] = $term->name;
      }
    
    
        $items = $feed->getElementsByTagName('item');
$itemArray = $this->rss_grab_reverseDomNodeList($items);

   

            foreach($itemArray as $rss_grab_info){
            array (
                             
                                  $title= $rss_grab_info->getElementsByTagName('title')->item(0)->nodeValue,
                                    $desc = $rss_grab_info->getElementsByTagName('description')->item(0)->nodeValue,
                                 
                                  
            );
            $rss_grab_cat_new=sanitize_text_field($cat);
             $rss_grab_title_new=sanitize_text_field($title);
             $rss_grab_desc=sanitize_text_field($desc);
             $rss_grab_title=trim($rss_grab_title_new);
         
              $timezone = get_option('timezone_string');
date_default_timezone_set($timezone);




$datePatterns = $this->rss_grab_getDatePatterns();

/*$date_pattern = $datePatterns['date_pattern'];
$date_pattern_today = $datePatterns['date_pattern_today']; */
$start_date = $datePatterns['date_pattern'];
$end_date = $datePatterns['date_pattern_today'];  


$table_name = $wpdb->prefix . "posts";


/*$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM `$table_name` 
        WHERE post_title = %s
        AND (post_date LIKE %s OR post_date LIKE %s)", 
        $rss_grab_title,  
        $date_pattern,    
        $date_pattern_today  
    )
);*/

$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM `$table_name` 
        WHERE post_title = %s 
        AND DATE(post_date) BETWEEN %s AND %s", 
        $rss_grab_title,  
        $start_date,  
        $end_date     
    )
);



        
            
                  if($wpdb->num_rows == 0) {
              
                    $cat_ID = get_cat_ID($rss_grab_cat_new);
    


            if (sanitize_text_field($rss_grab_cat)=='Yes') {
      
       
        $this->rss_grab_category($rss_grab_cat_new,$cat);
                  }    
                   

             
               //for author ///

                  $author=$rss_grab_author;
             $user = get_user_by( 'email', $author );
             $user_id= $user->ID;
             
             
           
             
             
            



                    $my_post = array(
    'post_title'    => $rss_grab_title,
    'post_content'  => $rss_grab_desc,
    'post_status'   => $rss_details['rss_grab_status'],
     
     'post_author'=> intval($user_id= $user->ID),
    
);
                    $post_id= wp_insert_post($my_post);

                    wp_set_object_terms( $post_id, $termArray, 'category' );


                                    $i=0;
                                      foreach($rss_grab_info->childNodes as $childNode) {
        
       /* if($childNode->tagName == 'media:content' || $childNode->tagName == 'media:thumbnail' && $childNode->getAttribute('type')!="video/mp4"){                                
        
           $rss_grab_image=$childNode->getAttribute('url');
          //$this->rss_grab_image($rss_grab_image,$post_id);

      

    
      


        }*/

     

          if($rss_grab_video1!='No' && $childNode->tagName == 'media:content'  && $childNode->getAttribute('type')=="video/mp4") {
          
           
           
          
         
         
        $table_name=$wpdb->prefix."posts";
        /*$id = $wpdb->get_var($wpdb->prepare("SELECT * FROM `$table_name`  WHERE post_title = %s
  ",$rss_grab_title));*/

    $id = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT * FROM `$table_name` 
        WHERE post_title = %s 
        AND DATE(post_date) BETWEEN %s AND %s", 
        $rss_grab_title,  
        $start_date,  // '2025-07-01' gibi bir tarih
        $end_date     // '2025-07-18' gibi bir tarih
    )
);
             if (isset($id)) {
          
          $video = $childNode->getAttribute('url');
$rss_grab_video=sanitize_url($video);
$response = wp_remote_get($rss_grab_video);
  if( !is_wp_error( $response ) ){
  $bits = wp_remote_retrieve_body( $response );
  $filename = sanitize_text_field(uniqid().'.mp4');
  $upload = wp_upload_bits( $filename, null, $bits );
 
}
          }
        
  $rss_grab_upload_dir = wp_upload_dir();
$after_contents='
<!-- wp:video  -->
<figure class="wp-block-video"><video controls controlsList="nodownload" src="'.$rss_grab_upload_dir['url'].'/'.sanitize_text_field($filename).'"></video></figure>
<!-- /wp:video -->'."<p>".$desc."</p>";

$before_contents=$desc.'<!-- wp:video  -->
<figure class="wp-block-video"><video controls controlsList="nodownload" src="'.$rss_grab_upload_dir['url'].'/'.sanitize_text_field($filename).'"></video></figure>
<!-- /wp:video -->'
;

  if (sanitize_text_field($rss_grab_video1) !="No"  && sanitize_text_field($rss_grab_video1) !="b_contents" ) {
        
            
                  $my_post1 = array(
      'ID' => intval($id),
      'post_content'  => $before_contents,
  
    
);
 
        }
          if (sanitize_text_field($rss_grab_video1) !="No"  && sanitize_text_field($rss_grab_video1) !="a_contents" ) {
        
          
                  $my_post1 = array(
      'ID' => intval($id),
      'post_content'  => $after_contents,
    
);
 
        }
  $post_id=wp_update_post($my_post1);
        }


                   
        }

      


   


        }
		
			
				if($wpdb->num_rows > 0) {
					
		 /* $id = $wpdb->get_var($wpdb->prepare("SELECT * FROM `$table_name`  WHERE post_title = %s
  ",$rss_grab_title));*/

    $id = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT * FROM `$table_name` 
        WHERE post_title = %s 
        AND DATE(post_date) BETWEEN %s AND %s", 
        $rss_grab_title,  
        $start_date, 
        $end_date     
    )
);
		 $parent_id = $id; 
$mime_type = 'image/jpeg';



$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM `$table_name` 
         WHERE post_parent = %d 
           AND post_mime_type = %s",
        $parent_id,
        $mime_type
    )
);
$i=0;
if (  empty($results) ) {
 foreach($rss_grab_info->childNodes as $childNode) {
	 if($childNode->tagName == 'media:content' || $childNode->tagName == 'media:thumbnail' && $childNode->getAttribute('type')!="video/mp4") {
		 
		 
		 
		 		$rss_grab_image=$childNode->getAttribute('url');
		 		if($childNode->getAttribute('type')!="video/mp4"){
			
			   $i++;
          if ($i==1) {
           


            $this->add_featured_image_from_url($rss_grab_image,$parent_id);


          }
	 }
	 
	 }
	 
 }
				}			

		  }
		  
			
		
		
         }



}


 function rss_grab_enclosure_auto($feed,$rss_id){
 global $wpdb;



 $rss_details= $this->get_rss_data(intval($rss_id));

    $rss_photo =  $rss_details['rss_grab_categories'];

    

     $rss_grab_status=sanitize_text_field($rss_details['rss_grab_status']);
     $rss_grab_author=sanitize_text_field($rss_details['rss_grab_author']);
    $rt=sanitize_text_field($rss_details['rss_grab_categories']);

    
    $rss_grab_video1=$rss_details['rss_grab_video'];
   
   $termArray = array();
      foreach(explode(',',$rt) as $r) {
        $term = get_term( $r, 'category' );
        $termArray[] = $term->name;
      }


      $items = $feed->getElementsByTagName('item');
$itemArray = $this->rss_grab_reverseDomNodeList($items);

            foreach($itemArray as $rss_grab_info){
                      $i=0;
            array (               //$cat=trim($rss_grab_info->getElementsByTagName('category')->item(0)->nodeValue),
            	                  $cat=trim($rss_grab_info->getElementsByTagName('Kategori')->item(0)->nodeValue),
                                  $title= $rss_grab_info->getElementsByTagName('title')->item(0)->nodeValue,
                                    $desc = $rss_grab_info->getElementsByTagName('description')->item(0)->nodeValue,
                                    $rss_grab_image= $rss_grab_info->getElementsByTagName('enclosure')->item(0)->getAttribute('url'),
                   $img_type= $rss_grab_info->getElementsByTagName('enclosure')->item(0)->getAttribute('type')
                                  
            );
            $rss_grab_cat_new=sanitize_text_field($cat);
            $rss_grab_title_new=sanitize_text_field($title);
             $rss_grab_desc=sanitize_text_field($desc);
          
             $rss_grab_title=trim($rss_grab_title_new);
              $timezone = get_option('timezone_string');
date_default_timezone_set($timezone);




$datePatterns = $this->rss_grab_getDatePatterns();

/*$date_pattern = $datePatterns['date_pattern'];
$date_pattern_today = $datePatterns['date_pattern_today']; */

$start_date = $datePatterns['date_pattern'];
$end_date = $datePatterns['date_pattern_today'];   


$table_name = $wpdb->prefix . "posts";


/*$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM `$table_name` 
        WHERE post_title = %s
        AND (post_date LIKE %s OR post_date LIKE %s)", 
        $rss_grab_title,  
        $date_pattern,    
        $date_pattern_today  
    )
);*/


$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM `$table_name` 
        WHERE post_title = %s 
        AND DATE(post_date) BETWEEN %s AND %s", 
        $rss_grab_title,  
        $start_date,  
        $end_date     
    )
);



        
             
                  if($wpdb->num_rows == 0) {


          $cat_ID = get_cat_ID($rss_grab_cat_new);
    


            if (sanitize_text_field($rss_grab_cat)=='Yes') {
      
        $this->rss_grab_category($rss_grab_cat_new,$cat);
                  } 
        
       //for author ///

                  $author=$rss_grab_author;
             $user = get_user_by( 'email', $author );
             $user_id= $user->ID;

       
           
     
            $my_post = array(
   'post_title'    => $rss_grab_title,
    'post_content'  => $rss_grab_desc,
    'post_status'   => sanitize_text_field($rss_grab_status),
    //'post_category'=>$rss_grab_cat == 'Yes' ? array( 'category' => intval($cat_ID)) :  $rss_grab_cat =! 'Yes' ? array( 'category' => 1 )  :'',

'post_author'=> intval($user_id= $user->ID),

);
                    $post_id= wp_insert_post($my_post);
                    wp_set_object_terms( $post_id, $termArray, 'category' );
                    // $this->rss_grab_image($rss_grab_image,$post_id); 

          



//for video

if (sanitize_text_field($img_type)=="video/mp4") {


$table_name=$wpdb->prefix."posts";
       /* $id = $wpdb->get_var($wpdb->prepare("SELECT * FROM `$table_name`  WHERE post_title = %s
  ",$rss_grab_title));*/

    $id = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT * FROM `$table_name` 
        WHERE post_title = %s 
        AND DATE(post_date) BETWEEN %s AND %s", 
        $rss_grab_title,  
        $start_date,  
        $end_date     
    )
);

         if (isset($id)) {
         
          $video = $childNode->getAttribute('url');
$rss_grab_video=sanitize_url($video);
$response = wp_remote_get($rss_grab_video);
  if( !is_wp_error( $response ) ){
  $bits = wp_remote_retrieve_body( $response );
  $filename = sanitize_text_field(uniqid().'.mp4');
  $upload = wp_upload_bits( $filename, null, $bits );
 
}
          }


  $rss_grab_upload_dir = wp_upload_dir();

$after_contents='
<!-- wp:video  -->
<figure class="wp-block-video"><video controls controlsList="nodownload" src="'.$rss_grab_upload_dir['url'].'/'.sanitize_text_field($filename).'"></video></figure>
<!-- /wp:video -->'."<p>".$desc."</p>";



$before_contents=$desc.'<!-- wp:video  -->
<figure class="wp-block-video"><video controls controlsList="nodownload" src="'.$rss_grab_upload_dir['url'].'/'.sanitize_text_field($filename).'"></video></figure>
<!-- /wp:video -->'
;




  if (sanitize_text_field($rss_grab_video1)!="No"  && sanitize_text_field($rss_grab_video1)!="b_contents" ) {
          
             $rss_grab_video1=get_option('rss_grab_video','');
                  $my_post1 = array(
      'ID' => intval($id),
      'post_content'  => $before_contents,
    
);
 
        }


                if (sanitize_text_field($rss_grab_video1)!="No"  && sanitize_text_field($rss_grab_video1)!="a_contents" ) {
         
             $rss_grab_video1=get_option('rss_grab_video','');
                  $my_post1 = array(
      'ID' => intval($id),

      'post_content'  => $after_contents,
    
);
 
        }
        $post_id=wp_update_post($my_post1);


}


  

   }
         
/*$id = $wpdb->get_var($wpdb->prepare("SELECT * FROM `$table_name`  WHERE post_title = %s
  ",$rss_grab_title));*/

    $id = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT * FROM `$table_name` 
        WHERE post_title = %s 
        AND DATE(post_date) BETWEEN %s AND %s", 
        $rss_grab_title,  
        $start_date,  
        $end_date     
    )
);




		 $parent_id = $id; 
$mime_type = 'image/jpeg'; 



$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM `$table_name` 
         WHERE post_parent = %d 
           AND post_mime_type = %s",
        $parent_id,
        $mime_type
    )
);

if (  empty($results) ) {
  

           
	$this->add_featured_image_from_url($rss_grab_image, $parent_id);


  
       

          }
          
          

         }
 }



}
}
 ?>