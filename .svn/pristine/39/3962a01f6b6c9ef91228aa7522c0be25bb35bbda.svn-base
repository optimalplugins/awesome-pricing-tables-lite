<?php

if(!defined('WPINC')) {
    die();
}

class OPT_Admin_Pages_Main {
    private static $instance;
    
    public static function getInstance(){
        if(self::$instance==null){
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    private function __construct() {
        
    }
    
    public function onLoad() {
        //load css and js here
    }
    
    public function mainDiv() {
        if(isset($_GET['page']) && $_GET['page'] === 'optimal-pricing-tbl') {
            ob_start();
            $this->listTable();
            $data = ob_get_clean();
            
            $this->getHeader();
            echo $data;
            $this->getFooter();
        }
    }
    
    private function listTable(){
        $table = new OPT_Admin_Libs_ListTables();
        //Fetch, prepare, sort, and filter our data...
        $table->prepare_items();
        ?>    
        <form id="movies-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
            <?php $table->display() ?>
        </form>
        <?php    
    }
    
    private function getHeader() {
        $title = __('Awesome Pricing Tables',  OptimalPricingTable::getTD());
        $link = add_query_arg(array('page' => 'optimal-pricing-tbl-new'),  admin_url('admin.php'));
       ?>
        <div class="wrap">
            <div id="icon-edit" class="icon32 icon32-posts-post">&nbsp;</div>
            <h2><?php echo $title; ?> <a class="add-new-h2" href="<?php echo $link; ?>">Add New</a></h2>
            <br class="clear">
        <?php
    }
    
    private function getFooter() {
        echo '</div>';
    }
}