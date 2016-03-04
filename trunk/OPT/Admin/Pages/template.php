<?php
global $wpdb;
$query = $wpdb->prepare('SELECT * FROM `' . $this->table .'` WHERE id=%d', array(2));
            $row = $wpdb->get_row($query,ARRAY_A);
            if(isset($row['extras']))
                $extras = maybe_unserialize($row['extras']);
            print_r($extras);
            echo "test";
