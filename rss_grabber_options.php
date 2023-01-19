<?php

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'rss_grabber_opt' ) ) {

  class rss_grabber_opt{


    function rss_grab_cron_adding() {
    if ( ! wp_next_scheduled( 'rss_graber_cron' ) ) {
        wp_schedule_event( time(), 'selected_minutes', 'rss_graber_cron' );
    }
  
 


  //insert all options
  $rss_grab_cat=update_option('rss_grab_cat','No');
  $rss_grab_cron=update_option('rss_grab_cron','No');
  $rss_grab_status=update_option('rss_grab_status','Pending');
  $rss_grab_photo=update_option('rss_grab_photo','No');
  $rss_grab_video=update_option('rss_grab_video','b_contents');
  }



   function rss_grab_category($rss_grab_cat_new,$cat){

                $cat_ID = get_cat_ID($rss_grab_cat_new);
             if( intval($cat_ID)==0 ) {
            $arg = array( 'description' => $rss_grab_cat_new);
            $cat_ID = wp_insert_term(sanitize_text_field($cat), "category", sanitize_text_field($rss_grab_cat_new));

            echo '<meta http-equiv="refresh" content="1">';
            
           
        }



   }

  //update all options

function rss_grab_update_options($rss_grab_name,$rss_grab_cat,$rss_grab_cron,$rss_grab_status,$rss_grab_photo,$rss_grab_video,$rss_grab_get_author){


$rss_grab_get_author=get_option('rss_grab_author',[]);


$rss_grab_name=update_option('rss_grab_name',sanitize_textarea_field($_POST['name_rss']));
if ($rss_grab_get_author=='') {
 
  $rss_grab_author=update_option('rss_grab_author',sanitize_text_field($_POST['rss_grab_author']));
}
if ($rss_grab_get_author!='') {
  
 
}


$rss_grab_cat=update_option('rss_grab_cat',sanitize_text_field($_POST['rss_grab_cat']));
$rss_grab_cat=get_option('rss_grab_cat','');
$rss_grab_cron=update_option('rss_grab_cron',sanitize_text_field($_POST['rss_grab_cron']));
$rss_grab_status=update_option('rss_grab_status',sanitize_text_field($_POST['rss_grab_status']));
$rss_grab_photo=update_option('rss_grab_photo',sanitize_text_field($_POST['rss_grab_photo']));
$rss_grab_video=update_option('rss_grab_video',sanitize_text_field($_POST['rss_grab_video']));



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







function rss_grab_media_content($feed){


     global $wpdb;


            foreach($feed->getElementsByTagName('item') as $rss_grab_info){
            array (
                               $cat=trim($rss_grab_info->getElementsByTagName('category')->item(0)->nodeValue),
                                  $title= $rss_grab_info->getElementsByTagName('title')->item(0)->nodeValue,
                                    $desc = $rss_grab_info->getElementsByTagName('description')->item(0)->nodeValue,
                                  
                                  
            );
            $rss_grab_cat_new=sanitize_text_field($cat);
             $rss_grab_title_new=sanitize_text_field($title);
             $rss_grab_desc=sanitize_text_field($desc);
             $rss_grab_title=trim($rss_grab_title_new);
             
 
              $table_name=$wpdb->prefix."posts";
            
                  $results = $wpdb->get_results(
  $wpdb->prepare("SELECT  * FROM `$table_name` 
 WHERE post_title = %s
  ",
    $rss_grab_title
  )
);

        
             
                  if($wpdb->num_rows == 0) {
              
                 
                    $cat_ID = get_cat_ID($rss_grab_cat_new);
    


            if ($rss_grab_cat=sanitize_text_field($_POST['rss_grab_cat'])=='Yes') {
      

        $this->rss_grab_category($rss_grab_cat_new,$cat);
                  }    
                   

                
                 $author=get_option('rss_grab_author','');
             $user = get_user_by( 'email', $author );
             $user_id= $user->ID;
             
               $rss_grab_cat_get=get_option('rss_grab_cat','');
           $cat_ID = get_cat_ID($rss_grab_cat_new);
           echo '<meta http-equiv="refresh" content="1">';
                    $my_post = array(
    'post_title'    => $rss_grab_title,
    'post_content'  => $rss_grab_desc,
    'post_status'   => sanitize_text_field($_POST['rss_grab_status']),
     'post_category'=>$rss_grab_cat == 'Yes' ? array( 'category' => intval($cat_ID)) :  $rss_grab_cat =! 'Yes' ? array( 'category' => 1 )  :'',
     'post_author'=> intval($user_id= $user->ID),
    
);
                    $post_id= wp_insert_post($my_post);


                                    $i=0;
                                      foreach($rss_grab_info->childNodes as $childNode) {
        if($childNode->tagName == 'media:content' ) {
        
           $rss_grab_image=$childNode->getAttribute('url');
          

      

          if (sanitize_text_field($_POST['rss_grab_photo'])=='No') {
            
            $i++;
          if ($i==1) {
           


            $this->rss_grab_image($rss_grab_image,$post_id);


          }


  



          }

          if (sanitize_text_field($_POST['rss_grab_photo'])=='Yes') {
            


                                                   
            $this->rss_grab_image($rss_grab_image,$post_id);
          }
      


        }

       

          if($childNode->tagName == 'media:content'  && $childNode->getAttribute('type')=="video/mp4") {
           
           
           
         
   
        $table_name=$wpdb->prefix."posts";
        $id = $wpdb->get_var($wpdb->prepare("SELECT * FROM `$table_name`  WHERE post_title = %s
  ",$rss_grab_title));
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
      'ID' => $id,
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

      


   


        }
         }
       


}



function rss_grab_enclosure($feed){

      global $wpdb;
         

            foreach($feed->getElementsByTagName('item') as $rss_grab_info){
                      $i=0;
            array (             
                                  $title= $rss_grab_info->getElementsByTagName('title')->item(0)->nodeValue,
                                  $cat=trim($rss_grab_info->getElementsByTagName('category')->item(0)->nodeValue),
                                    $desc = $rss_grab_info->getElementsByTagName('description')->item(0)->nodeValue,
                                   $rss_grab_image= $rss_grab_info->getElementsByTagName('enclosure')->item(0)->getAttribute('url'),
                   $img_type= $rss_grab_info->getElementsByTagName('enclosure')->item(0)->getAttribute('type')
                                  
            );
            $rss_grab_cat_new=sanitize_text_field($cat);
            $rss_grab_title_new=sanitize_text_field($title);
             $rss_grab_desc=sanitize_text_field($desc);
             $rss_grab_title=trim($rss_grab_title_new);
           
        
             $table_name=$wpdb->prefix."posts";
    	       $results = $wpdb->get_results(
  $wpdb->prepare("SELECT  * FROM `$table_name` 
 WHERE post_title = %s
  ",
    $rss_grab_title
  )
);

        
           
                  if($wpdb->num_rows == 0) {
     
        
            $cat_ID = get_cat_ID($rss_grab_cat_new);         	
    


            if ($rss_grab_cat=sanitize_text_field($_POST['rss_grab_cat'])=='Yes') {  
      

        $this->rss_grab_category($rss_grab_cat_new,$cat);
                  }    
                   



                 $rss_grab_cat_get=get_option('rss_grab_cat','');
           $cat_ID = get_cat_ID($rss_grab_cat_new);
           echo '<meta http-equiv="refresh" content="1">';
              
            $my_post = array(
   'post_title'    => $rss_grab_title,
    'post_content'  => $rss_grab_desc,
    'post_status'   => sanitize_text_field($_POST['rss_grab_status']),
    'post_category'=>$rss_grab_cat == 'Yes' ? array( 'category' => intval($cat_ID)):  $rss_grab_cat =! 'Yes' ? array( 'category' => 1 )  :'',


);
                    $post_id= wp_insert_post($my_post);
          

                    if (sanitize_text_field($_POST['rss_grab_photo'])=='No') {
                                  $i++;
            if ($i==1) {
            




          if(sanitize_text_field($img_type)=="image/jpeg"){


          $this->rss_grab_image($rss_grab_image,$post_id);
          
        
 
}
            }
                    }



if (sanitize_text_field($_POST['rss_grab_photo'])=='Yes') {

            if(sanitize_text_field($img_type=="image/jpeg")){
   
         $this->rss_grab_image($rss_grab_image,$post_id);
 
}
}

//for video

if (sanitize_text_field($img_type)=="video/mp4") {


   $table_name=$wpdb->prefix."posts";
        $id = $wpdb->get_var($wpdb->prepare("SELECT * FROM `$table_name`  WHERE post_title = %s
  ",$rss_grab_title));

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
         

          
          

         }



    
}




  function rss_grab_manuel($rss_grab_url_manuel){



       global $wpdb;
 



  
   $this->rss_grab_update_options($rss_grab_name,$rss_grab_cat,$rss_grab_cron,$rss_grab_status,$rss_grab_photo,$rss_grab_video,$rss_grab_author);


   
       $feed = new DOMDocument;
        $feed->load($rss_grab_url_manuel);
        $feed_array = array();
       $rss_grab_image_type=$feed->getElementsByTagName('enclosure');
         if($rss_grab_image_type->length == 0) {
          
         

          $this->rss_grab_media_content($feed);
         

             }
    
       
       
            else     if($rss_grab_image_type->length > 0) {
            
              $this->rss_grab_enclosure($feed);
         

             }











  }



function rss_grab_media_content_auto($feed){



$iha_cat_get=get_option('iha_cat','');
$rss_grab_cron=get_option('rss_grab_cron');
$rss_grab_status=get_option('rss_grab_status','');
$rss_grab_photo=get_option('rss_grab_photo','');
$rss_grab_video1=get_option('rss_grab_video','');
 $rss_grab_cat=get_option('rss_grab_cat','');

    global $wpdb;

            foreach($feed->getElementsByTagName('item') as $rss_grab_info){
            array (
                               $cat=trim($rss_grab_info->getElementsByTagName('category')->item(0)->nodeValue),
                                  $title= $rss_grab_info->getElementsByTagName('title')->item(0)->nodeValue,
                                    $desc = $rss_grab_info->getElementsByTagName('description')->item(0)->nodeValue,
                                 
                                  
            );
            $rss_grab_cat_new=sanitize_text_field($cat);
             $rss_grab_title_new=sanitize_text_field($title);
             $rss_grab_desc=sanitize_text_field($desc);
             $rss_grab_title=trim($rss_grab_title_new);
         
 
           
             $table_name=$wpdb->prefix."posts";
    	       $results = $wpdb->get_results(
  $wpdb->prepare("SELECT  * FROM `$table_name` 
 WHERE post_title = %s
  ",
    $rss_grab_title
  )
);

        
            
                  if($wpdb->num_rows == 0) {
              
                    $cat_ID = get_cat_ID($rss_grab_cat_new);
    


            if (sanitize_text_field($rss_grab_cat)=='Yes') {
      
       
        $this->rss_grab_category($rss_grab_cat_new,$cat);
                  }    
                   

             
               //for author ///

                  $author=get_option('rss_grab_author','');
             $user = get_user_by( 'email', $author );
             $user_id= $user->ID;
             
               $rss_grab_cat_get=get_option('rss_grab_cat','');
           $cat_ID = get_cat_ID($rss_grab_cat_new);

               ///   
           
             
             
               $rss_grab_cat_get=get_option('rss_grab_cat','');
           $cat_ID = get_cat_ID($rss_grab_cat_new);
           echo '<meta http-equiv="refresh" content="1">';



                    $my_post = array(
    'post_title'    => $rss_grab_title,
    'post_content'  => $rss_grab_desc,
    'post_status'   => $rss_grab_status,
     'post_category'=>$rss_grab_cat == 'Yes' ? array( 'category' => intval($cat_ID)) :  $rss_grab_cat =! 'Yes' ? array( 'category' => 1 )  :'',
     'post_author'=> intval($user_id= $user->ID),
);
                    $post_id= wp_insert_post($my_post);


                                    $i=0;
                                      foreach($rss_grab_info->childNodes as $childNode) {
        if($childNode->tagName == 'media:content') {
        
           $rss_grab_image=$childNode->getAttribute('url');
          

      

          if (sanitize_text_field($rss_grab_photo)=='No') {
       
            $i++;
          if ($i==1) {
            
 

$this->rss_grab_image($rss_grab_image,$post_id);
          }


  



          }

          if (sanitize_text_field($rss_grab_photo)=='Yes') {
           




$this->rss_grab_image($rss_grab_image,$post_id);
          }
      


        }

     

          if($childNode->tagName == 'media:content'  && $childNode->getAttribute('type')=="video/mp4") {
          
           
           
          
         
         
        $table_name=$wpdb->prefix."posts";
        $id = $wpdb->get_var($wpdb->prepare("SELECT * FROM `$table_name`  WHERE post_title = %s
  ",$rss_grab_title));
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
         }



}


 function rss_grab_enclosure_auto($feed){
 global $wpdb;
$rss_grab_cron=get_option('rss_grab_cron');
$rss_grab_status=get_option('rss_grab_status','');
$rss_grab_photo=get_option('rss_grab_photo','');
$rss_grab_video1=get_option('rss_grab_video','');
 $rss_grab_cat=get_option('rss_grab_cat','');


      

            foreach($feed->getElementsByTagName('item') as $rss_grab_info){
                      $i=0;
            array (               $cat=trim($rss_grab_info->getElementsByTagName('category')->item(0)->nodeValue),
                                  $title= $rss_grab_info->getElementsByTagName('title')->item(0)->nodeValue,
                                    $desc = $rss_grab_info->getElementsByTagName('description')->item(0)->nodeValue,
                                    $rss_grab_image= $rss_grab_info->getElementsByTagName('enclosure')->item(0)->getAttribute('url'),
                   $img_type= $rss_grab_info->getElementsByTagName('enclosure')->item(0)->getAttribute('type')
                                  
            );
            $rss_grab_cat_new=sanitize_text_field($cat);
            $rss_grab_title_new=sanitize_text_field($title);
             $rss_grab_desc=sanitize_text_field($desc);
          
             $rss_grab_title=trim($rss_grab_title_new);
            
          
             $table_name=$wpdb->prefix."posts";
    	       $results = $wpdb->get_results(
  $wpdb->prepare("SELECT  * FROM `$table_name` 
 WHERE post_title = %s
  ",
    $rss_grab_title
  )
);

        
             //if (!$result) {
                  if($wpdb->num_rows == 0) {


          $cat_ID = get_cat_ID($rss_grab_cat_new);
    


            if (sanitize_text_field($rss_grab_cat)=='Yes') {
      
        $this->rss_grab_category($rss_grab_cat_new,$cat);
                  } 
        
     


       $rss_grab_cat_get=get_option('rss_grab_cat','');
           $cat_ID = get_cat_ID($rss_grab_cat_new);
           echo '<meta http-equiv="refresh" content="1">';
           $author=get_option('rss_grab_author','');
             $user = get_user_by( 'email', $author );
             $user_id= $user->ID;
     
            $my_post = array(
   'post_title'    => $rss_grab_title,
    'post_content'  => $rss_grab_desc,
    'post_status'   => sanitize_text_field($rss_grab_status),
    'post_category'=>$rss_grab_cat == 'Yes' ? array( 'category' => intval($cat_ID)) :  $rss_grab_cat =! 'Yes' ? array( 'category' => 1 )  :'',

'post_author'=> intval($user_id= $user->ID),

);
                    $post_id= wp_insert_post($my_post);
          

                    if ($rss_grab_photo=='No') {
                                  $i++;
            if ($i==1) {
            




          if(sanitize_text_field($img_type)=="image/jpeg"){
                                               

         $this->rss_grab_image($rss_grab_image,$post_id); 
        
 
}
            }
                    }



if (sanitize_text_field($rss_grab_photo)=='Yes') {

            if(sanitize_text_field($img_type)=="image/jpeg"){
                                                 
          $this->rss_grab_image($rss_grab_image,$post_id);
          
        
 
}
}

//for video

if (sanitize_text_field($img_type)=="video/mp4") {


$table_name=$wpdb->prefix."posts";
        $id = $wpdb->get_var($wpdb->prepare("SELECT * FROM `$table_name`  WHERE post_title = %s
  ",$rss_grab_title));

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
         

          
          

         }
 }



}
}
 ?>