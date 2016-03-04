<?php

if(!defined('WPINC')) {
    die();
}

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class OPT_Admin_Libs_ListTables extends WP_List_Table{
    private $table;
    function __construct(){
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'table',     //singular name of the listed records
            'plural'    => 'tables',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
        global $wpdb;
        $this->table = $wpdb->prefix . 'optimal_pricing_tbl'; 
    }
    
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'title1'     => 'Title',
            'sc'        => 'Shortcode',
            'date1'      => 'Date',
            'active'    => 'Active',
        );
        return $columns;
    }
    
    function column_cb($item){
        //echo $item['id'];
        //echo $column_name;
        return sprintf('<input type="checkbox" name="%s[]" value="%s" />',$this->_args['singular'],$item->id );
    }
    
    function column_default($item, $column_name){
        switch($column_name){
            case 'title1':
                //Build row actions
                $actions = array(
                    'edit'      => sprintf('<a href="?page=optimal-pricing-tbl-new&edit=%s" class="edit">Edit</a>',$item->id),
                    'delete'    => sprintf('<a href="?page=optimal-pricing-tbl-new&delete=%s" class="delete">Delete</a>',$item->id),
                );

                //Return the title contents
                return sprintf('%1$s %2$s',
                    /*$1%s*/ $item->title,
                    /*$2%s*/ $this->row_actions($actions)
                );
                
            case 'sc':
                return sprintf("%s", '[awesome_pricing_tbl id=' . $item->id . ']');
             
            case 'date1':
                return sprintf('%s', date('Y-m-d',  $item->date));
                
            case 'active':
                $str = "";
                if($item->active === '1')
                    $str = __("Active", OptimalPricingTable::getTD());
                elseif($item->active === '0')
                    $str = __('Inactive', OptimalPricingTable::getTD());
                return sprintf('%s',$str);
        }
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'date'     => array('date',true),     //true means it's already sorted
            'title'    => array('title',false),
        );
        return $sortable_columns;
    }
    
    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }
    
    function process_bulk_action() {
        
        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
            echo('Items deleted (or they would be if we had items to delete)!');
        }
        
    }
    
    function extra_tablenav( $which ) {
	if ( $which == "top" ){
           
	}
	if ( $which == "bottom" ){
		//The code that goes after the table is there
		//echo"Hi, I'm after the table";
	}
    }
    
    function prepare_items() {
	global $wpdb;
        $this->process_bulk_action();

        /* -- Preparing your query -- */
        $query = "SELECT id, title, date, active FROM {$this->table}";

        
        /* -- Pagination parameters -- */
        $totalitems = $wpdb->query($query); //return the total number of affected rows
        $perpage = 10;
        $paged = !empty($_GET["paged"]) ? stripslashes($_GET["paged"]) : '';
        if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
        $totalpages = ceil($totalitems/$perpage);
        if(!empty($paged) && !empty($perpage)){
                $offset=($paged-1)*$perpage;
            $query.=' LIMIT '.(int)$offset.','.(int)$perpage;
        }
        
        /* -- Register the Columns -- */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        
	/* -- Register the pagination -- */
        $this->set_pagination_args( array(
                "total_items" => $totalitems,
                "total_pages" => $totalpages,
                "per_page" => $perpage,
        ) );
        //echo $query;
	/* -- Fetch the items -- */
        $this->items = $wpdb->get_results($query);
    }
}