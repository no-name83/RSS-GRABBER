<?php


if ( ! defined( 'ABSPATH' ) ) exit;


if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

if ( ! class_exists( 'rss_grabber_table' ) ) {

class rss_grabber_table extends WP_List_Table
{
  
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
           
            'singular' => 'rss_grab_url', 
                'plural'   => 'rss_grab_urls',
        ));
    }


    function column_default($item, $column_name)
    {
        

        switch( $column_name ) { 
            case 'id':
            case 'rss_grab_name':
            case 'rss_grab_url':
           
            return $item[$column_name];            
            
            case 'action':
                $rtn = '<a href="' . admin_url( 'admin.php?page=edit-rss&action=rss_grabber_edit&rss_id=' . $item['id'] ) . '">' . __( 'Edit', 'logics' ) . '</a>'; 
                $rtn .= ' | <a href="' . admin_url( 'admin.php?page=edit-rss&action=runjob&rss_id=' . $item['id'] ) . '">' . __( 'Run', 'logics' ) . '</a>'; 
                return $rtn;
            default:
                return 'id';
        }
    }

  
    function column_age($item)
    {
        return '<em>' . $item['list_status'] . '</em>';
    }

 
    function column_name($item)
    {
        //this function is disable
    }

   
  
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->
            _args['singular'], 
            $item['id'] 
        );
    
    }


    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'rss_grab_name' => __('Name', 'crw_bot'),
            'rss_grab_url' => __('Url', 'crw_bot'),
            //aşağıdaki satır sonradan eklendi
            'action' => __('Action', 'crw_bot'),
            'cron_active' => 'Cron Status',
        
        );
        return $columns;
    }


    function get_sortable_columns()
    {
        $sortable_columns = array(
            'rss_grab_url' => array('rss_grab_url', true),
            'rss_grab_name' => array('rss_grab_cat', false),
        );
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = array(
            
            'delete' => 'Delete'
        );
        return $actions;
    }



    function process_bulk_action()
    {
        global $wpdb;
       $table_name = $wpdb->prefix . 'rss_grabber'; 

        if ( 'delete' === $this->current_action() ) {
            if ( isset( $_GET['rss_grab_url'] ) ) {
                $crw_i = 0;
                foreach ( $_GET['rss_grab_url'] as $bot_id ) {
                    $crw_i++;
                    $result = $wpdb->delete(
                        $table_name,
                       
                        array( 'id' => sanitize_text_field( $bot_id ) )
                    );
                 
                    
                }
              
            }
        }
    }


    public function column_cron_active($item) {

        global $wpdb;
        $table = $wpdb->prefix . 'rss_grabber';
    $is_active = intval($item['cron_active']) === 1;
    $rss_grab_cron=$item['rss_grab_cron'];
    $rss_grab_cron_old = $item['rss_grab_cron_old'];
    $created = strtotime($item['created_at']);
    $interval = intval($item['rss_grab_cron']); 


    $toggle_url = add_query_arg([
        'action'    => 'toggle_cron',
        'record_id' => $item['id'],
        '_wpnonce'  => wp_create_nonce('toggle_cron_' . $item['id']),
    ]);

    $status_label = $is_active ? __('Active', 'custom-cron-manager') : __('Inactive', 'custom-cron-manager');

    $next_run_info = '';
    if ($is_active) {


    	$current_timestamp = current_time('timestamp');  

    
    $timezone = get_option('timezone_string');
    if ($timezone) {
        date_default_timezone_set($timezone);
    } else {
        date_default_timezone_set('UTC');
    }
       $created_update = $created + ($interval * 60);  

        
        $created_update_mysql = date('Y-m-d H:i:s',$current_timestamp); 
    if ($current_timestamp >= $created_update) {

 $wpdb->update($table, ['created_at' => $created_update_mysql], ['id' => $item['id']]);

    }

        if($rss_grab_cron=='No'){

           $wpdb->update($table, ['rss_grab_cron' => $rss_grab_cron_old], ['id' => $item['id']]);

        }

       
        $created_at = strtotime($item['created_at']);
        
        $interval = intval($item['rss_grab_cron']);
        $next_run = $created_at + $interval * 60;
        $next_run_iso = date('c', $next_run);

        $next_run_info = sprintf(
            '<br><small>%s <span class="ccm-next-run" data-timestamp="%s" data-interval="%d">%s</span></small>',
            __('Next Run:', 'custom-cron-manager'),
            esc_attr($next_run_iso),
            esc_attr($interval),
            esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $next_run))
        );
    }

    if(!$is_active){

        if($rss_grab_cron!='No'){


        $wpdb->update($table, ['rss_grab_cron' => 'No','rss_grab_cron_old'=>$rss_grab_cron], ['id' => $item['id']]);
       
    }




    }

    return sprintf(
        '<label class="ccm-switch">
            <input type="checkbox" onchange="window.location.href=\'%s\'" %s>
            <span class="ccm-slider"></span>
        </label>
        <span class="ccm-status-label">%s</span>%s',
        esc_url($toggle_url),
        $is_active ? 'checked' : '',
        esc_html($status_label),
        $next_run_info
    );
    if ($is_active) {
    $created_at = strtotime($item['created_at']);
    //$interval = intval($item['interval_minutes']);
    $interval = intval($item['rss_grab_cron']);
    $next_run = $created_at + $interval * 60;
    $next_run_iso = date('c', $next_run);

    $next_run_info = sprintf(
        '<br><small>' . __('Next Run:', 'custom-cron-manager') . ' <span class="ccm-next-run" data-timestamp="%s" data-interval="%d">%s</span></small>',
        esc_attr($next_run_iso),
        esc_attr($interval),
        esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $next_run))
    );
}

}
 
    function prepare_items()
    {
        global $wpdb;
       
         $table_name = $wpdb->prefix . 'rss_grabber'; 

        $per_page = 10; 

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $columns               = $this->get_columns();
        $hidden                = array();
        $sortable              = $this->get_sortable_columns();
        $this->_column_headers = array(
            $columns,
            $hidden,
            $sortable,
        );
        $this->process_bulk_action();
        
        if ( isset( $_GET['order'] ) ) {
            $order = sanitize_text_field( $_GET['order'] );
        } else {
            $order = 'asc';
        }
        if ( isset( $_GET['orderby'] ) ) {
            $orderby = sanitize_text_field( $_GET['orderby'] );
        } else {
            
            $orderby = 'id';
        }

      
         $order   = sanitize_sql_orderby( $order );
         $orderby = str_replace( ' ', '', $orderby );

        

          

          

          

              

                   $results = $wpdb->get_results(
                "SELECT * FROM `$table_name`  order by " . $orderby .
                ' ' . $order
            );

      

        $data = array();
        
        foreach ( $results as $crw_query ) {
            array_push( $data, (array) $crw_query );
        }
        $current_page = $this->get_pagenum();
        $total_items  = count( $data );
        $data         = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
        $this->items  = $data;
        $this->set_pagination_args(
            array(
                'total_items' => $total_items, 
                'per_page'    => $per_page, 
                'total_pages' => ceil( $total_items / $per_page ), 
            )
        );
    }
}

}


