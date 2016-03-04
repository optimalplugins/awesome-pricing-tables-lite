<?php

class OPT_Activate {
    private static $instance;
    
    public static function getInstance(){
        if(self::$instance==null){
            self::$instance = new self;
            self::$instance->actions();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        ;
    }
    
    private function actions(){
        global $wpdb;
        $sqls = array();
        
        if (!empty ($wpdb->charset))
            $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
        if (!empty ($wpdb->collate))
            $charset_collate .= " COLLATE {$wpdb->collate}";
        
        $sqls[] = "CREATE TABLE `{$wpdb->prefix}optimal_pricing_tbl` (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `title` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
                    `html` longtext COLLATE utf8_unicode_ci NOT NULL,
                    `css` longtext COLLATE utf8_unicode_ci NOT NULL,
                    `extra_css` longtext COLLATE utf8_unicode_ci NOT NULL,
                    `date` int(11) NOT NULL,
                    `active` tinyint(4) NOT NULL DEFAULT '1',
                    UNIQUE KEY `id` (`id`)
                  ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        foreach ($sqls as $sql){
            dbDelta($sql);
        }
        
    }
}