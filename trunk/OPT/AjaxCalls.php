<?php

class OPT_AjaxCalls {
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
        add_action("wp_ajax_opt_pricing_tbl_add_new", array($this,'addNewTable'));
        //add_action("wp_ajax_nopriv_my_user_vote", "my_must_login");
        add_action("wp_ajax_opt_pricing_tbl_save_as_template", array($this,'saveAsTemplate'));
    }
    
    public function addNewTable() {
        $result['type'] = 'error';
        if(check_ajax_referer( 'opt_pricing_tbl_add_new', 'nonce', false )) {
            //nonce success
            $result['type'] = 'success';
            $title = isset($_POST['title']) ? esc_attr($_POST['title']) : '';
            $html = isset($_POST['html']) ? stripslashes($_POST['html']) : '';
            $css = isset($_POST['css']) ? stripslashes($_POST['css']) : '';
            $edit = isset($_POST['edit']) ? intval($_POST['edit']) : 0;
            $formFields = isset($_POST['formFields']) ? $_POST['formFields'] : '';
            $extras = array();
            if(is_array($formFields)):
                foreach ($formFields as $key => $data):
                    $extras[$data['name']] = $data['value'];
                endforeach;
            endif;
            global $wpdb;
            $table = $wpdb->prefix . 'optimal_pricing_tbl'; 
            
            if($edit === 0) {
                //add new
                $data = array(
                    'title' => $title,
                    'html' => $html,
                    'css'   => $css,
                    'extras' => maybe_serialize($extras),
                    'date'  => time()
                );
                $format = array('%s', '%s', '%s', '%s', '%d');

                if($wpdb->insert($table,$data,$format)) {
                    $result['message'][] = "Table saved successfully";
                }
            }
            elseif($edit > 0) {
                //update here
                $data = array(
                    'title' => $title,
                    'html' => $html,
                    'css'   => $css,
                    'extras' => maybe_serialize($extras)
                );
                $format = array('%s', '%s', '%s', '%s');
                
                $where = array('id' => $edit);
                $format_where = array('%d');
                
                if($wpdb->update($table,$data,$where,$format,$format_where)) {
                    $result['message'][] = "Table updated successfully";
                }
            }
        }
        else {
            $result['error'][] = "Nonce Error";
        }
         
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        }
        else {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
        }

         die();
    }
    
    public function saveAsTemplate() {
        $result['type'] = 'error';
        if(check_ajax_referer( 'opt_pricing_tbl_save_template', 'nonce', false )) {
            //nonce success
            $result['type'] = 'success';
            $title = isset($_POST['title']) ? esc_attr($_POST['title']) : '';
            //$html = isset($_POST['html']) ? stripslashes($_POST['html']) : '';
            //$css = isset($_POST['css']) ? stripslashes($_POST['css']) : '';
            //$edit = isset($_POST['edit']) ? intval($_POST['edit']) : 0;
            $formFields = isset($_POST['formFields']) ? $_POST['formFields'] : '';
            $extras = array();
            if(is_array($formFields)):
                foreach ($formFields as $key => $data):
                    $extras[$data['name']] = $data['value'];
                endforeach;
            endif;
            global $wpdb;
            $table = $wpdb->prefix . 'optimal_pricing_tbl_templates'; 
            
            
            //add new
            $data = array(
                'title' => $title,
                'extras' => maybe_serialize($extras),
                'date'  => time()
            );
            $format = array('%s', '%s', '%d');

            if($wpdb->insert($table,$data,$format)) {
                $result['message'][] = "Table saved successfully";
            }
            
            
        }
        else {
            $result['error'][] = "Nonce Error";
        }
         
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        }
        else {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
        }

         die();
    }
}