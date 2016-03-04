<?php
/**
 * Plugin Name: Awesome Pricing Tables Lite
 * Plugin URI: http://optimalplugins.com
 * Description: The most AWESOME Visual CSS3 Responsive Pricing Comparison Tables for WordPress. Visual editing right where you see them.
 * Version: 1.0.0
 * Author: OptimalPlugins
 * Author URI: http://www.optimalplugins.com
 * Requires at least: 3.8
 * Tested up to: 4.1
 */

if (!defined('WPINC')) {
    die();
}

class OptimalPricingTable
{
    public static $DS = '/';
    private static $instance;
    private $dir, $url, $td, $menu_slugs;

    private function __construct()
    {
        ;
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self;
            self::$instance->dir = dirname(__FILE__);
            self::$instance->url = WP_PLUGIN_URL . self::$DS . basename(self::$instance->dir);
            self::$instance->td = 'optimal-pricing-tbl';
            self::$instance->menu_slugs = array();
            self::$instance->actions();
        }
        return self::$instance;
    }

    private function actions()
    {
        // Add the Autoloader
        if (!class_exists('OPT_Autoloader_LoaderClass')) {
            $path = $this->dir . self::$DS . 'OPT' . self::$DS
                . 'Autoloader' . self::$DS . 'LoaderClass.php';
            require_once $path;
        }
        $loader = new OPT_Autoloader_LoaderClass("OPT", $this->dir);
        $loader->register();
        //register activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        //admin hook
        if (is_admin()) {
            add_action('admin_menu', array($this, 'adminMenu'));
            add_action('admin_init', array($this, 'actionAdminInit'));
        }
        if (!is_admin()) {
            add_shortcode('awesome_pricing_tbl', array($this, 'tableShortCode'));
        }
        //ajax hooks
        if (defined('DOING_AJAX') && DOING_AJAX) {
            OPT_AjaxCalls::getInstance();
        }
    }

    public static function getTD()
    {
        return self::$instance->td;
    }

    public static function getDir()
    {
        return self::$instance->dir;
    }

    public static function getUrl()
    {
        return self::$instance->url;
    }

    function tableShortCode($atts)
    {
        $a = shortcode_atts(array(
            'id' => '0',
        ), $atts);
        $str = "";
        if (is_numeric($a['id']) && $a['id'] > 0) {
            global $wpdb;
            $table = $wpdb->prefix . 'optimal_pricing_tbl';
            $query = $wpdb->prepare(
                'SELECT * FROM `' . $table . '` WHERE id=%d AND active=1',
                array($a['id'])
            );
            $row = $wpdb->get_row($query, ARRAY_A);
            if (!empty($row)):
                $row['html'] = html_entity_decode($row['html']);
                $row['css'] = html_entity_decode($row['css']);
                // get global css
                $path = $this->dir . self::$DS . 'OPT' . self::$DS
                    . 'Admin' . self::$DS . 'Pages' . self::$DS . 'GlobalCSS.php';
                ob_start();
                include $path;
                $global_css = ob_get_clean();

                $css = str_replace('opt-pricing-table', 'opt-pricing-table-'. $row[id] ,$row['css']);
                $global_css = str_replace('opt-pricing-table', 'opt-pricing-table-'. $row[id], $global_css);
                $html = str_replace('opt-pricing-table', 'opt-pricing-table-'. $row[id] ,$row['html']);

                $str .= <<<EOD
<style type='text/css'>
    {$css}
    {$global_css}
</style>
<div class="opt-pricing-table-{$row[id]}-wrapper">
{$html}
    <div class="opt-pricing-table-{$row[id]}-footer"><a href="http://www.optimalplugins.com/" target="_blank">Powered by Optimal Plugins</a></div>
</div>
EOD;
            endif;
        }
        return $str;
    }

    public function adminMenu()
    {
        $this->menu_slugs['main'] = add_menu_page(__('Optimal Pricing Table', $this->td),
            __('Pricing Table', $this->td), 'manage_options', 'optimal-pricing-tbl',
            array($this, 'pricingTableMenu'), 'dashicons-forms', 27.123);
        add_action('load-' . $this->menu_slugs['main'], array($this, 'loadMainPage'));
        $this->menu_slugs['new_table'] = add_submenu_page('optimal-pricing-tbl', __('Add New Pricing Tables',
            $this->td), __('Add New', $this->td), 'manage_options', 'optimal-pricing-tbl-new',
            array($this, 'pricingTableNew'));
        add_action('load-' . $this->menu_slugs['new_table'], array($this, 'loadNewTable'));
//        $this->menu_slugs['settings'] = add_submenu_page('optimal-pricing-tbl', __('Settings',
//            $this->td), __('Settings', $this->td), 'manage_options', 'optimal-pricing-tbl-settings',
//            'OptimalLicenseChecker::init');
    }

    public function pricingTableMenu()
    {
        $obj = OPT_Admin_Pages_Main::getInstance();
        $obj->mainDiv();
    }

    public function loadMainPage()
    {
        //load css and js for Pricing Table main page
        $obj = OPT_Admin_Pages_Main::getInstance();
        $obj->onLoad();
    }

    public function pricingTableNew()
    {
        $obj = OPT_Admin_Pages_AddNewTable::getInstance();
        $obj->mainDiv();
    }

    public function loadNewTable()
    {
        //load css and js for pricing table new table
        $obj = OPT_Admin_Pages_AddNewTable::getInstance();
        $obj->onLoad();
    }

    public function actionAdminInit()
    {
        $this->enqueueScript();
    }

    private function enqueueScript()
    {
        $ds = self::$DS;
        wp_register_script('opt-pricing-tbl-main', "{$this->url}{$ds}js{$ds}main.js", array('jquery'), false, false);
        wp_register_script('opt-pricing-tbl', "{$this->url}{$ds}js{$ds}pricing-table.js", array('jquery'), false, false);
        wp_register_script('js-color', "{$this->url}{$ds}js{$ds}jscolor{$ds}jscolor.js", array('jquery'));
        wp_register_script('select2', "{$this->url}{$ds}select2{$ds}js{$ds}select2.min.js", array('jquery'));
        //wp_register_script('opt-pricing-tbl-template', "{$this->url}{$ds}OPT{$ds}Admin{$ds}Pages{$ds}template.php",array('jquery','opt-pricing-tbl-main','opt-pricing-tbl'),false, true);
        wp_register_style('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.css');
        wp_register_style('select2', "{$this->url}{$ds}select2{$ds}css{$ds}select2.min.css");
        wp_register_style('opt-pricing-tbl', "{$this->url}{$ds}css{$ds}opt-pricing-tbl.css");
        wp_register_style('font-awesome', "{$this->url}{$ds}css{$ds}font-awesome.min.css");
    }

    public function activate()
    {
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
                    `extras` longtext COLLATE utf8_unicode_ci NOT NULL,
                    `date` int(11) NOT NULL,
                    `active` tinyint(4) NOT NULL DEFAULT '1',
                    UNIQUE KEY `id` (`id`)
                  )";
        $sqls[] = "CREATE TABLE `{$wpdb->prefix}optimal_pricing_tbl_templates` (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `title` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
                    `extras` longtext COLLATE utf8_unicode_ci NOT NULL,
                    `date` int(11) NOT NULL,
                    `active` tinyint(4) NOT NULL DEFAULT '1',
                    UNIQUE KEY `id` (`id`)
                  )";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        foreach ($sqls as $sql) {
            dbDelta($sql);
        }
        $result = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}optimal_pricing_tbl_templates`");
        if (empty($result)) {
            $query2 = <<<EOD
INSERT INTO `{$wpdb->prefix}optimal_pricing_tbl_templates` (`title`, `extras`, `date`, `active`) VALUES
('Template 1', 'a:46:{s:6:"preset";s:7:"preset0";s:7:"preset2";s:0:"";s:11:"columnWidth";s:3:"232";s:7:"padding";s:1:"0";s:11:"cellPadding";s:2:"10";s:6:"margin";s:1:"0";s:12:"priceOptions";s:1:"2";s:13:"featureColumn";s:1:"2";s:11:"hoverEffect";s:3:"yes";s:11:"borderWidth";s:1:"0";s:11:"borderStyle";s:5:"solid";s:12:"borderColour";s:7:"#CCCCCC";s:12:"borderRadius";s:1:"0";s:15:"cellBorderWidth";s:1:"0";s:21:"cellBottomBorderWidth";s:1:"0";s:15:"cellBorderStyle";s:0:"";s:16:"cellBorderColour";s:7:"#B83737";s:16:"cellBorderRadius";s:1:"0";s:25:"removeTopCellBorderBottom";s:3:"yes";s:28:"removeBottomCellBorderBottom";s:3:"yes";s:17:"headingTextColour";s:7:"#808080";s:17:"backgroundColour1";s:7:"#424242";s:17:"backgroundColour2";s:7:"#2B2B2B";s:17:"pricingTextColour";s:7:"#5BC2D4";s:23:"pricingBackgroundColour1";s:7:"#363636";s:16:"actionTextColour";s:7:"#2A6496";s:22:"actionBackgroundColour";s:0:"";s:19:"rowBackgroundColour";s:7:"#F6F6F6";s:28:"alternateRowBackgroundColour";s:7:"#E3E3E3";s:22:"columnBackgroundColour";s:7:"#FFFFFF";s:10:"fontFamily";s:7:"Georgia";s:15:"headingFontSize";s:2:"22";s:11:"rowFontSize";s:2:"16";s:15:"pricingFontSize";s:2:"20";s:14:"actionFontSize";s:2:"14";s:9:"textAlign";s:6:"center";s:12:"actionButton";s:3:"yes";s:22:"buttonBackgroundColour";s:7:"#EEEEEE";s:28:"buttonBorderPaddingTopBottom";s:1:"7";s:28:"buttonBorderPaddingLeftRight";s:2:"73";s:17:"buttonBorderStyle";s:5:"solid";s:17:"buttonBorderWidth";s:1:"0";s:18:"buttonBorderColour";s:7:"#B83737";s:16:"buttonTextColour";s:7:"#000000";s:18:"buttonBorderRadius";s:2:"42";s:9:"extra_css";s:0:"";}', 1425325134, 1),
('Template 2', 'a:43:{s:6:"preset";s:7:"preset0";s:7:"preset2";s:0:"";s:11:"columnWidth";s:3:"272";s:7:"padding";s:1:"0";s:11:"cellPadding";s:1:"7";s:6:"margin";s:1:"0";s:12:"priceOptions";s:1:"2";s:13:"featureColumn";s:1:"2";s:11:"borderWidth";s:1:"0";s:11:"borderStyle";s:5:"solid";s:12:"borderColour";s:7:"#CCCCCC";s:12:"borderRadius";s:1:"0";s:15:"cellBorderWidth";s:1:"0";s:21:"cellBottomBorderWidth";s:1:"2";s:15:"cellBorderStyle";s:0:"";s:16:"cellBorderColour";s:7:"#B83737";s:16:"cellBorderRadius";s:1:"0";s:17:"headingTextColour";s:7:"#DA4300";s:17:"backgroundColour1";s:7:"#FBD601";s:17:"backgroundColour2";s:7:"#FDAE00";s:17:"pricingTextColour";s:7:"#DA4300";s:23:"pricingBackgroundColour1";s:7:"#FDB200";s:16:"actionTextColour";s:7:"#2A6496";s:22:"actionBackgroundColour";s:0:"";s:19:"rowBackgroundColour";s:7:"#F6F6F6";s:28:"alternateRowBackgroundColour";s:7:"#E3E3E3";s:22:"columnBackgroundColour";s:7:"#FFFFFF";s:10:"fontFamily";s:17:"Yanone Kaffeesatz";s:15:"headingFontSize";s:2:"21";s:11:"rowFontSize";s:2:"17";s:15:"pricingFontSize";s:2:"24";s:14:"actionFontSize";s:2:"15";s:9:"textAlign";s:6:"center";s:12:"actionButton";s:3:"yes";s:22:"buttonBackgroundColour";s:7:"#EFEFEF";s:28:"buttonBorderPaddingTopBottom";s:1:"5";s:28:"buttonBorderPaddingLeftRight";s:2:"15";s:17:"buttonBorderStyle";s:5:"solid";s:17:"buttonBorderWidth";s:1:"1";s:18:"buttonBorderColour";s:7:"#B5B5B5";s:16:"buttonTextColour";s:7:"#444444";s:18:"buttonBorderRadius";s:1:"2";s:9:"extra_css";s:0:"";}', 1425327481, 1),
('Template 3', 'a:44:{s:6:"preset";s:7:"preset1";s:7:"preset2";s:8:"preset_2";s:11:"columnWidth";s:3:"272";s:7:"padding";s:0:"";s:11:"cellPadding";s:1:"7";s:6:"margin";s:1:"0";s:12:"priceOptions";s:1:"2";s:13:"featureColumn";s:1:"2";s:11:"borderWidth";s:1:"0";s:11:"borderStyle";s:5:"solid";s:12:"borderColour";s:7:"#CCCCCC";s:12:"borderRadius";s:1:"0";s:15:"cellBorderWidth";s:1:"0";s:21:"cellBottomBorderWidth";s:1:"2";s:15:"cellBorderStyle";s:0:"";s:16:"cellBorderColour";s:7:"#B83737";s:16:"cellBorderRadius";s:1:"0";s:25:"removeTopCellBorderBottom";s:0:"";s:28:"removeBottomCellBorderBottom";s:0:"";s:17:"headingTextColour";s:7:"#FFFFFF";s:17:"backgroundColour1";s:7:"#B83636";s:17:"backgroundColour2";s:7:"#A72323";s:17:"pricingTextColour";s:7:"#FFFFFF";s:23:"pricingBackgroundColour1";s:7:"#A62222";s:16:"actionTextColour";s:7:"#2A6496";s:22:"actionBackgroundColour";s:0:"";s:19:"rowBackgroundColour";s:7:"#F6F6F6";s:28:"alternateRowBackgroundColour";s:7:"#E3E3E3";s:22:"columnBackgroundColour";s:7:"#FFFFFF";s:10:"fontFamily";s:17:"Yanone Kaffeesatz";s:15:"headingFontSize";s:2:"21";s:11:"rowFontSize";s:2:"17";s:15:"pricingFontSize";s:2:"24";s:14:"actionFontSize";s:2:"15";s:9:"textAlign";s:6:"center";s:22:"buttonBackgroundColour";s:7:"#EFEFEF";s:28:"buttonBorderPaddingTopBottom";s:1:"5";s:28:"buttonBorderPaddingLeftRight";s:2:"15";s:17:"buttonBorderStyle";s:5:"solid";s:17:"buttonBorderWidth";s:1:"1";s:18:"buttonBorderColour";s:7:"#B5B5B5";s:16:"buttonTextColour";s:7:"#444444";s:18:"buttonBorderRadius";s:1:"2";s:9:"extra_css";s:0:"";}', 1425329032, 1),
('Template 4', 'a:42:{s:6:"preset";s:7:"preset0";s:7:"preset2";s:8:"preset_3";s:11:"columnWidth";s:3:"272";s:7:"padding";s:0:"";s:11:"cellPadding";s:1:"8";s:6:"margin";s:1:"0";s:12:"priceOptions";s:1:"2";s:13:"featureColumn";s:1:"2";s:11:"borderWidth";s:1:"0";s:11:"borderStyle";s:5:"solid";s:12:"borderColour";s:7:"#CCCCCC";s:12:"borderRadius";s:1:"0";s:15:"cellBorderWidth";s:1:"0";s:21:"cellBottomBorderWidth";s:1:"2";s:15:"cellBorderStyle";s:0:"";s:16:"cellBorderColour";s:7:"#B83737";s:16:"cellBorderRadius";s:1:"0";s:17:"headingTextColour";s:7:"#878787";s:17:"backgroundColour1";s:7:"#FFFFFF";s:17:"backgroundColour2";s:7:"#D2D2D2";s:17:"pricingTextColour";s:7:"#FFBA58";s:23:"pricingBackgroundColour1";s:7:"#AE3A3A";s:16:"actionTextColour";s:7:"#2A6496";s:22:"actionBackgroundColour";s:0:"";s:19:"rowBackgroundColour";s:7:"#F6F6F6";s:28:"alternateRowBackgroundColour";s:7:"#E3E3E3";s:22:"columnBackgroundColour";s:7:"#FFFFFF";s:10:"fontFamily";s:7:"Lobster";s:15:"headingFontSize";s:2:"21";s:11:"rowFontSize";s:2:"17";s:15:"pricingFontSize";s:2:"24";s:14:"actionFontSize";s:2:"15";s:9:"textAlign";s:6:"center";s:22:"buttonBackgroundColour";s:7:"#EFEFEF";s:28:"buttonBorderPaddingTopBottom";s:1:"5";s:28:"buttonBorderPaddingLeftRight";s:2:"15";s:17:"buttonBorderStyle";s:5:"solid";s:17:"buttonBorderWidth";s:1:"1";s:18:"buttonBorderColour";s:7:"#B5B5B5";s:16:"buttonTextColour";s:7:"#444444";s:18:"buttonBorderRadius";s:1:"2";s:9:"extra_css";s:0:"";}', 1425329346, 1),
('Template 5', 'a:42:{s:6:"preset";s:7:"preset0";s:7:"preset2";s:8:"preset_3";s:11:"columnWidth";s:3:"272";s:7:"padding";s:0:"";s:11:"cellPadding";s:1:"7";s:6:"margin";s:1:"0";s:12:"priceOptions";s:1:"2";s:13:"featureColumn";s:1:"2";s:11:"borderWidth";s:1:"0";s:11:"borderStyle";s:5:"solid";s:12:"borderColour";s:7:"#CCCCCC";s:12:"borderRadius";s:1:"0";s:15:"cellBorderWidth";s:1:"0";s:21:"cellBottomBorderWidth";s:1:"2";s:15:"cellBorderStyle";s:0:"";s:16:"cellBorderColour";s:7:"#B83737";s:16:"cellBorderRadius";s:1:"0";s:17:"headingTextColour";s:7:"#FFFFFF";s:17:"backgroundColour1";s:7:"#E18416";s:17:"backgroundColour2";s:0:"";s:17:"pricingTextColour";s:7:"#FFFFFF";s:23:"pricingBackgroundColour1";s:7:"#EF9A36";s:16:"actionTextColour";s:7:"#2A6496";s:22:"actionBackgroundColour";s:0:"";s:19:"rowBackgroundColour";s:7:"#F6F6F6";s:28:"alternateRowBackgroundColour";s:7:"#E3E3E3";s:22:"columnBackgroundColour";s:7:"#FFFFFF";s:10:"fontFamily";s:14:"PT Sans Narrow";s:15:"headingFontSize";s:2:"21";s:11:"rowFontSize";s:2:"17";s:15:"pricingFontSize";s:2:"24";s:14:"actionFontSize";s:2:"15";s:9:"textAlign";s:6:"center";s:22:"buttonBackgroundColour";s:7:"#EFEFEF";s:28:"buttonBorderPaddingTopBottom";s:1:"5";s:28:"buttonBorderPaddingLeftRight";s:2:"15";s:17:"buttonBorderStyle";s:5:"solid";s:17:"buttonBorderWidth";s:1:"1";s:18:"buttonBorderColour";s:7:"#B5B5B5";s:16:"buttonTextColour";s:7:"#444444";s:18:"buttonBorderRadius";s:1:"2";s:9:"extra_css";s:0:"";}', 1425329748, 1),
('Template 6', 'a:44:{s:6:"preset";s:7:"preset2";s:7:"preset2";s:8:"preset_5";s:11:"columnWidth";s:3:"272";s:7:"padding";s:0:"";s:11:"cellPadding";s:1:"7";s:6:"margin";s:1:"0";s:12:"priceOptions";s:1:"2";s:13:"featureColumn";s:1:"2";s:11:"borderWidth";s:1:"0";s:11:"borderStyle";s:5:"solid";s:12:"borderColour";s:7:"#CCCCCC";s:12:"borderRadius";s:1:"0";s:15:"cellBorderWidth";s:1:"0";s:21:"cellBottomBorderWidth";s:1:"2";s:15:"cellBorderStyle";s:0:"";s:16:"cellBorderColour";s:7:"#B83737";s:16:"cellBorderRadius";s:1:"0";s:25:"removeTopCellBorderBottom";s:0:"";s:28:"removeBottomCellBorderBottom";s:0:"";s:17:"headingTextColour";s:7:"#FFFFFF";s:17:"backgroundColour1";s:7:"#E9AA00";s:17:"backgroundColour2";s:0:"";s:17:"pricingTextColour";s:7:"#FFFFFF";s:23:"pricingBackgroundColour1";s:7:"#F6BA18";s:16:"actionTextColour";s:7:"#2A6496";s:22:"actionBackgroundColour";s:0:"";s:19:"rowBackgroundColour";s:7:"#F6F6F6";s:28:"alternateRowBackgroundColour";s:7:"#E3E3E3";s:22:"columnBackgroundColour";s:7:"#FFFFFF";s:10:"fontFamily";s:7:"Rokkitt";s:15:"headingFontSize";s:2:"21";s:11:"rowFontSize";s:2:"17";s:15:"pricingFontSize";s:2:"24";s:14:"actionFontSize";s:2:"15";s:9:"textAlign";s:6:"center";s:22:"buttonBackgroundColour";s:7:"#EFEFEF";s:28:"buttonBorderPaddingTopBottom";s:1:"5";s:28:"buttonBorderPaddingLeftRight";s:2:"15";s:17:"buttonBorderStyle";s:5:"solid";s:17:"buttonBorderWidth";s:1:"1";s:18:"buttonBorderColour";s:7:"#B5B5B5";s:16:"buttonTextColour";s:7:"#444444";s:18:"buttonBorderRadius";s:1:"2";s:9:"extra_css";s:0:"";}', 1425329858, 1),
('Template 7', 'a:43:{s:6:"preset";s:7:"preset0";s:7:"preset2";s:8:"preset_6";s:11:"columnWidth";s:3:"272";s:7:"padding";s:0:"";s:11:"cellPadding";s:1:"9";s:6:"margin";s:1:"0";s:12:"priceOptions";s:1:"2";s:13:"featureColumn";s:1:"2";s:11:"borderWidth";s:1:"0";s:11:"borderStyle";s:5:"solid";s:12:"borderColour";s:7:"#CCCCCC";s:12:"borderRadius";s:1:"0";s:15:"cellBorderWidth";s:1:"0";s:21:"cellBottomBorderWidth";s:1:"2";s:15:"cellBorderStyle";s:0:"";s:16:"cellBorderColour";s:7:"#B83737";s:16:"cellBorderRadius";s:1:"0";s:17:"headingTextColour";s:7:"#FFFFFF";s:17:"backgroundColour1";s:7:"#A18DCB";s:17:"backgroundColour2";s:0:"";s:17:"pricingTextColour";s:7:"#FFFFFF";s:23:"pricingBackgroundColour1";s:7:"#AC99D2";s:16:"actionTextColour";s:7:"#2A6496";s:22:"actionBackgroundColour";s:0:"";s:19:"rowBackgroundColour";s:7:"#F6F6F6";s:28:"alternateRowBackgroundColour";s:7:"#E3E3E3";s:22:"columnBackgroundColour";s:7:"#FFFFFF";s:10:"fontFamily";s:8:"PT Serif";s:15:"headingFontSize";s:2:"18";s:11:"rowFontSize";s:2:"15";s:15:"pricingFontSize";s:2:"20";s:14:"actionFontSize";s:2:"13";s:9:"textAlign";s:6:"center";s:12:"actionButton";s:0:"";s:22:"buttonBackgroundColour";s:7:"#AC99D2";s:28:"buttonBorderPaddingTopBottom";s:1:"5";s:28:"buttonBorderPaddingLeftRight";s:2:"15";s:17:"buttonBorderStyle";s:5:"solid";s:17:"buttonBorderWidth";s:1:"1";s:18:"buttonBorderColour";s:7:"#AC99D2";s:16:"buttonTextColour";s:7:"#FFFFFF";s:18:"buttonBorderRadius";s:1:"2";s:9:"extra_css";s:0:"";}', 1425330242, 1),
('Template 8', 'a:43:{s:6:"preset";s:8:"preset11";s:7:"preset2";s:8:"preset_7";s:11:"columnWidth";s:3:"272";s:7:"padding";s:0:"";s:11:"cellPadding";s:1:"9";s:6:"margin";s:1:"0";s:12:"priceOptions";s:1:"2";s:13:"featureColumn";s:1:"2";s:11:"borderWidth";s:1:"0";s:11:"borderStyle";s:5:"solid";s:12:"borderColour";s:7:"#CCCCCC";s:12:"borderRadius";s:1:"0";s:15:"cellBorderWidth";s:1:"0";s:21:"cellBottomBorderWidth";s:1:"2";s:15:"cellBorderStyle";s:0:"";s:16:"cellBorderColour";s:7:"#B83737";s:16:"cellBorderRadius";s:1:"0";s:17:"headingTextColour";s:7:"#FFFFFF";s:17:"backgroundColour1";s:7:"#35BED0";s:17:"backgroundColour2";s:0:"";s:17:"pricingTextColour";s:7:"#FFFFFF";s:23:"pricingBackgroundColour1";s:7:"#3EC7D7";s:16:"actionTextColour";s:7:"#2A6496";s:22:"actionBackgroundColour";s:0:"";s:19:"rowBackgroundColour";s:7:"#F6F6F6";s:28:"alternateRowBackgroundColour";s:7:"#E3E3E3";s:22:"columnBackgroundColour";s:7:"#FFFFFF";s:10:"fontFamily";s:5:"Cabin";s:15:"headingFontSize";s:2:"18";s:11:"rowFontSize";s:2:"15";s:15:"pricingFontSize";s:2:"20";s:14:"actionFontSize";s:2:"13";s:9:"textAlign";s:6:"center";s:12:"actionButton";s:0:"";s:22:"buttonBackgroundColour";s:7:"#3EC7D7";s:28:"buttonBorderPaddingTopBottom";s:1:"5";s:28:"buttonBorderPaddingLeftRight";s:2:"15";s:17:"buttonBorderStyle";s:5:"solid";s:17:"buttonBorderWidth";s:1:"1";s:18:"buttonBorderColour";s:7:"#3EC7D7";s:16:"buttonTextColour";s:7:"#FFFFFF";s:18:"buttonBorderRadius";s:1:"2";s:9:"extra_css";s:0:"";}', 1425330501, 1),
('Template 9', 'a:43:{s:6:"preset";s:7:"preset0";s:7:"preset2";s:8:"preset_8";s:11:"columnWidth";s:3:"272";s:7:"padding";s:0:"";s:11:"cellPadding";s:1:"9";s:6:"margin";s:1:"0";s:12:"priceOptions";s:1:"2";s:13:"featureColumn";s:1:"2";s:11:"borderWidth";s:1:"0";s:11:"borderStyle";s:5:"solid";s:12:"borderColour";s:7:"#CCCCCC";s:12:"borderRadius";s:1:"0";s:15:"cellBorderWidth";s:1:"0";s:21:"cellBottomBorderWidth";s:1:"2";s:15:"cellBorderStyle";s:0:"";s:16:"cellBorderColour";s:7:"#B83737";s:16:"cellBorderRadius";s:1:"0";s:17:"headingTextColour";s:7:"#FFFFFF";s:17:"backgroundColour1";s:7:"#5EC2EA";s:17:"backgroundColour2";s:0:"";s:17:"pricingTextColour";s:7:"#FFFFFF";s:23:"pricingBackgroundColour1";s:7:"#6ACAED";s:16:"actionTextColour";s:7:"#2A6496";s:22:"actionBackgroundColour";s:0:"";s:19:"rowBackgroundColour";s:7:"#F6F6F6";s:28:"alternateRowBackgroundColour";s:7:"#E3E3E3";s:22:"columnBackgroundColour";s:7:"#FFFFFF";s:10:"fontFamily";s:12:"Crafty Girls";s:15:"headingFontSize";s:2:"18";s:11:"rowFontSize";s:2:"15";s:15:"pricingFontSize";s:2:"20";s:14:"actionFontSize";s:2:"13";s:9:"textAlign";s:6:"center";s:12:"actionButton";s:0:"";s:22:"buttonBackgroundColour";s:7:"#6ACAED";s:28:"buttonBorderPaddingTopBottom";s:1:"5";s:28:"buttonBorderPaddingLeftRight";s:2:"15";s:17:"buttonBorderStyle";s:5:"solid";s:17:"buttonBorderWidth";s:1:"1";s:18:"buttonBorderColour";s:7:"#6ACAED";s:16:"buttonTextColour";s:7:"#FFFFFF";s:18:"buttonBorderRadius";s:1:"2";s:9:"extra_css";s:0:"";}', 1425330629, 1),
('Template 10', 'a:43:{s:6:"preset";s:7:"preset0";s:7:"preset2";s:8:"preset_9";s:11:"columnWidth";s:3:"272";s:7:"padding";s:0:"";s:11:"cellPadding";s:1:"9";s:6:"margin";s:1:"0";s:12:"priceOptions";s:1:"2";s:13:"featureColumn";s:1:"2";s:11:"borderWidth";s:1:"0";s:11:"borderStyle";s:5:"solid";s:12:"borderColour";s:7:"#CCCCCC";s:12:"borderRadius";s:1:"0";s:15:"cellBorderWidth";s:1:"0";s:21:"cellBottomBorderWidth";s:1:"2";s:15:"cellBorderStyle";s:0:"";s:16:"cellBorderColour";s:7:"#B83737";s:16:"cellBorderRadius";s:1:"0";s:17:"headingTextColour";s:7:"#FFFFFF";s:17:"backgroundColour1";s:7:"#36A6DF";s:17:"backgroundColour2";s:0:"";s:17:"pricingTextColour";s:7:"#FFFFFF";s:23:"pricingBackgroundColour1";s:7:"#42B3E5";s:16:"actionTextColour";s:7:"#2A6496";s:22:"actionBackgroundColour";s:0:"";s:19:"rowBackgroundColour";s:7:"#F6F6F6";s:28:"alternateRowBackgroundColour";s:7:"#E3E3E3";s:22:"columnBackgroundColour";s:7:"#FFFFFF";s:10:"fontFamily";s:4:"Abel";s:15:"headingFontSize";s:2:"20";s:11:"rowFontSize";s:2:"17";s:15:"pricingFontSize";s:2:"23";s:14:"actionFontSize";s:2:"14";s:9:"textAlign";s:6:"center";s:12:"actionButton";s:0:"";s:22:"buttonBackgroundColour";s:7:"#42B3E5";s:28:"buttonBorderPaddingTopBottom";s:1:"5";s:28:"buttonBorderPaddingLeftRight";s:2:"15";s:17:"buttonBorderStyle";s:5:"solid";s:17:"buttonBorderWidth";s:1:"1";s:18:"buttonBorderColour";s:7:"#42B3E5";s:16:"buttonTextColour";s:7:"#FFFFFF";s:18:"buttonBorderRadius";s:1:"2";s:9:"extra_css";s:0:"";}', 1425330770, 1),
('Template 11', 'a:43:{s:6:"preset";s:7:"preset0";s:7:"preset2";s:8:"preset_2";s:11:"columnWidth";s:3:"272";s:7:"padding";s:0:"";s:11:"cellPadding";s:1:"7";s:6:"margin";s:1:"0";s:12:"priceOptions";s:1:"2";s:13:"featureColumn";s:1:"2";s:11:"borderWidth";s:1:"0";s:11:"borderStyle";s:5:"solid";s:12:"borderColour";s:7:"#CCCCCC";s:12:"borderRadius";s:1:"0";s:15:"cellBorderWidth";s:1:"0";s:21:"cellBottomBorderWidth";s:1:"2";s:15:"cellBorderStyle";s:0:"";s:16:"cellBorderColour";s:7:"#B83737";s:16:"cellBorderRadius";s:1:"0";s:17:"headingTextColour";s:7:"#FFFFFF";s:17:"backgroundColour1";s:7:"#7CB746";s:17:"backgroundColour2";s:7:"#5B9433";s:17:"pricingTextColour";s:7:"#FFFFFF";s:23:"pricingBackgroundColour1";s:7:"#7EB548";s:16:"actionTextColour";s:7:"#2A6496";s:22:"actionBackgroundColour";s:0:"";s:19:"rowBackgroundColour";s:7:"#F6F6F6";s:28:"alternateRowBackgroundColour";s:7:"#E3E3E3";s:22:"columnBackgroundColour";s:7:"#FFFFFF";s:10:"fontFamily";s:17:"Yanone Kaffeesatz";s:15:"headingFontSize";s:2:"21";s:11:"rowFontSize";s:2:"17";s:15:"pricingFontSize";s:2:"24";s:14:"actionFontSize";s:2:"15";s:9:"textAlign";s:6:"center";s:12:"actionButton";s:3:"yes";s:22:"buttonBackgroundColour";s:0:"";s:28:"buttonBorderPaddingTopBottom";s:1:"0";s:28:"buttonBorderPaddingLeftRight";s:1:"0";s:17:"buttonBorderStyle";s:0:"";s:17:"buttonBorderWidth";s:1:"1";s:18:"buttonBorderColour";s:7:"#B5B5B5";s:16:"buttonTextColour";s:7:"#303030";s:18:"buttonBorderRadius";s:1:"0";s:9:"extra_css";s:0:"";}', 1425331067, 1),
('Template 12', 'a:43:{s:6:"preset";s:7:"preset0";s:7:"preset2";s:9:"preset_11";s:11:"columnWidth";s:3:"272";s:7:"padding";s:0:"";s:11:"cellPadding";s:1:"7";s:6:"margin";s:1:"0";s:12:"priceOptions";s:1:"2";s:13:"featureColumn";s:1:"2";s:11:"borderWidth";s:1:"0";s:11:"borderStyle";s:5:"solid";s:12:"borderColour";s:7:"#CCCCCC";s:12:"borderRadius";s:1:"0";s:15:"cellBorderWidth";s:1:"0";s:21:"cellBottomBorderWidth";s:1:"2";s:15:"cellBorderStyle";s:0:"";s:16:"cellBorderColour";s:7:"#B83737";s:16:"cellBorderRadius";s:1:"0";s:17:"headingTextColour";s:7:"#FFFFFF";s:17:"backgroundColour1";s:7:"#70BBD4";s:17:"backgroundColour2";s:7:"#5399C0";s:17:"pricingTextColour";s:7:"#FFFFFF";s:23:"pricingBackgroundColour1";s:7:"#5399C0";s:16:"actionTextColour";s:7:"#2A6496";s:22:"actionBackgroundColour";s:0:"";s:19:"rowBackgroundColour";s:7:"#F6F6F6";s:28:"alternateRowBackgroundColour";s:7:"#E3E3E3";s:22:"columnBackgroundColour";s:7:"#FFFFFF";s:10:"fontFamily";s:17:"Yanone Kaffeesatz";s:15:"headingFontSize";s:2:"21";s:11:"rowFontSize";s:2:"17";s:15:"pricingFontSize";s:2:"24";s:14:"actionFontSize";s:2:"15";s:9:"textAlign";s:6:"center";s:12:"actionButton";s:3:"yes";s:22:"buttonBackgroundColour";s:0:"";s:28:"buttonBorderPaddingTopBottom";s:1:"0";s:28:"buttonBorderPaddingLeftRight";s:1:"0";s:17:"buttonBorderStyle";s:0:"";s:17:"buttonBorderWidth";s:1:"1";s:18:"buttonBorderColour";s:7:"#B5B5B5";s:16:"buttonTextColour";s:7:"#303030";s:18:"buttonBorderRadius";s:1:"0";s:9:"extra_css";s:0:"";}', 1425331211, 1),
('Template 13', 'a:42:{s:6:"preset";s:7:"preset0";s:7:"preset2";s:9:"preset_12";s:11:"columnWidth";s:3:"272";s:7:"padding";s:0:"";s:11:"cellPadding";s:1:"7";s:6:"margin";s:1:"0";s:12:"priceOptions";s:1:"2";s:13:"featureColumn";s:1:"2";s:11:"borderWidth";s:1:"0";s:11:"borderStyle";s:5:"solid";s:12:"borderColour";s:7:"#CCCCCC";s:12:"borderRadius";s:1:"0";s:15:"cellBorderWidth";s:1:"0";s:21:"cellBottomBorderWidth";s:1:"2";s:15:"cellBorderStyle";s:0:"";s:16:"cellBorderColour";s:7:"#B83737";s:16:"cellBorderRadius";s:1:"0";s:17:"headingTextColour";s:7:"#FFFFFF";s:17:"backgroundColour1";s:7:"#C5B474";s:17:"backgroundColour2";s:7:"#A98E55";s:17:"pricingTextColour";s:7:"#FFFFFF";s:23:"pricingBackgroundColour1";s:7:"#C0AE72";s:16:"actionTextColour";s:7:"#2A6496";s:22:"actionBackgroundColour";s:0:"";s:19:"rowBackgroundColour";s:7:"#F6F6F6";s:28:"alternateRowBackgroundColour";s:7:"#FFFFFF";s:22:"columnBackgroundColour";s:7:"#FFFFFF";s:10:"fontFamily";s:17:"Yanone Kaffeesatz";s:15:"headingFontSize";s:2:"21";s:11:"rowFontSize";s:2:"17";s:15:"pricingFontSize";s:2:"24";s:14:"actionFontSize";s:2:"15";s:9:"textAlign";s:6:"center";s:22:"buttonBackgroundColour";s:0:"";s:28:"buttonBorderPaddingTopBottom";s:1:"0";s:28:"buttonBorderPaddingLeftRight";s:1:"0";s:17:"buttonBorderStyle";s:0:"";s:17:"buttonBorderWidth";s:1:"1";s:18:"buttonBorderColour";s:7:"#B5B5B5";s:16:"buttonTextColour";s:7:"#303030";s:18:"buttonBorderRadius";s:1:"0";s:9:"extra_css";s:0:"";}', 1425331677, 1),
('Template 14', 'a:42:{s:6:"preset";s:7:"preset0";s:7:"preset2";s:9:"preset_13";s:11:"columnWidth";s:3:"272";s:7:"padding";s:0:"";s:11:"cellPadding";s:1:"7";s:6:"margin";s:1:"0";s:12:"priceOptions";s:1:"2";s:13:"featureColumn";s:1:"2";s:11:"borderWidth";s:1:"0";s:11:"borderStyle";s:5:"solid";s:12:"borderColour";s:7:"#CCCCCC";s:12:"borderRadius";s:1:"0";s:15:"cellBorderWidth";s:1:"0";s:21:"cellBottomBorderWidth";s:1:"2";s:15:"cellBorderStyle";s:0:"";s:16:"cellBorderColour";s:7:"#B83737";s:16:"cellBorderRadius";s:1:"0";s:17:"headingTextColour";s:7:"#FFFFFF";s:17:"backgroundColour1";s:7:"#A88142";s:17:"backgroundColour2";s:7:"#7E5F30";s:17:"pricingTextColour";s:7:"#FFFFFF";s:23:"pricingBackgroundColour1";s:7:"#A88142";s:16:"actionTextColour";s:7:"#2A6496";s:22:"actionBackgroundColour";s:0:"";s:19:"rowBackgroundColour";s:7:"#F6F6F6";s:28:"alternateRowBackgroundColour";s:7:"#FFFFFF";s:22:"columnBackgroundColour";s:7:"#FFFFFF";s:10:"fontFamily";s:7:"Lobster";s:15:"headingFontSize";s:2:"21";s:11:"rowFontSize";s:2:"17";s:15:"pricingFontSize";s:2:"24";s:14:"actionFontSize";s:2:"15";s:9:"textAlign";s:6:"center";s:22:"buttonBackgroundColour";s:0:"";s:28:"buttonBorderPaddingTopBottom";s:1:"0";s:28:"buttonBorderPaddingLeftRight";s:1:"0";s:17:"buttonBorderStyle";s:0:"";s:17:"buttonBorderWidth";s:1:"1";s:18:"buttonBorderColour";s:7:"#B5B5B5";s:16:"buttonTextColour";s:7:"#303030";s:18:"buttonBorderRadius";s:1:"0";s:9:"extra_css";s:0:"";}', 1425331773, 1),
('Template 15', 'a:42:{s:6:"preset";s:7:"preset0";s:7:"preset2";s:9:"preset_14";s:11:"columnWidth";s:3:"272";s:7:"padding";s:0:"";s:11:"cellPadding";s:1:"7";s:6:"margin";s:1:"0";s:12:"priceOptions";s:1:"2";s:13:"featureColumn";s:1:"2";s:11:"borderWidth";s:1:"0";s:11:"borderStyle";s:5:"solid";s:12:"borderColour";s:7:"#CCCCCC";s:12:"borderRadius";s:1:"0";s:15:"cellBorderWidth";s:1:"0";s:21:"cellBottomBorderWidth";s:1:"2";s:15:"cellBorderStyle";s:0:"";s:16:"cellBorderColour";s:7:"#B83737";s:16:"cellBorderRadius";s:1:"0";s:17:"headingTextColour";s:7:"#FFFFFF";s:17:"backgroundColour1";s:7:"#684B24";s:17:"backgroundColour2";s:0:"";s:17:"pricingTextColour";s:7:"#FFFFFF";s:23:"pricingBackgroundColour1";s:7:"#7B5D2F";s:16:"actionTextColour";s:7:"#2A6496";s:22:"actionBackgroundColour";s:0:"";s:19:"rowBackgroundColour";s:7:"#F6F6F6";s:28:"alternateRowBackgroundColour";s:7:"#FFFFFF";s:22:"columnBackgroundColour";s:7:"#FFFFFF";s:10:"fontFamily";s:7:"Lobster";s:15:"headingFontSize";s:2:"21";s:11:"rowFontSize";s:2:"17";s:15:"pricingFontSize";s:2:"24";s:14:"actionFontSize";s:2:"15";s:9:"textAlign";s:6:"center";s:22:"buttonBackgroundColour";s:0:"";s:28:"buttonBorderPaddingTopBottom";s:1:"0";s:28:"buttonBorderPaddingLeftRight";s:1:"0";s:17:"buttonBorderStyle";s:0:"";s:17:"buttonBorderWidth";s:1:"1";s:18:"buttonBorderColour";s:7:"#B5B5B5";s:16:"buttonTextColour";s:7:"#303030";s:18:"buttonBorderRadius";s:1:"0";s:9:"extra_css";s:0:"";}', 1425331931, 1);
EOD;
            $wpdb->query($query2);
        }
    }

    public function deactivate()
    {
    }
}

OptimalPricingTable::getInstance();