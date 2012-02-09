<?php

/*
Plugin Name: WP-Bokzuy
Plugin Script: wp-bokzuy.php
Plugin URI: 
Description:   
Version: 0.1
License: GPL
Author: David Barragán Merino
Author URI: http://kaleidos.net/FFF8E7/
*/

/*
 * === RELEASE NOTES ===
 *    2012-02-08 - v0.1 - first version
 */

/* 
 * Copyright 2012  David Barragán Merino (david.barragan@kaleidos.net)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 * Online: http://www.gnu.org/licenses/gpl.txt
 */

// For i10n
add_action('init', 'bokzuy_textdomain'); 
function bokzuy_textdomain() {
    $dir = basename(dirname(__FILE__))."/languages";
    load_plugin_textdomain( 'bokzuy', 'wp-content/plugins/'.$dir, $dir);
}


/**********************************************************/
/***************** Llatest badges widget ********************/
/**********************************************************/

// A function to create the latest badges widget
add_action( 'widgets_init', 'latest_badges_widget_init' );
function latest_badges_widget_init() {
        register_widget('WP_Widget_Bokzuy_Latest_Badges');
}

// WP_Widget_Bokzuy_Latest_Badges class definition
class WP_Widget_Bokzuy_Latest_Badges extends WP_Widget {
	
	// Init
    function WP_Widget_Bokzuy_Latest_Badges() {
        $widget_ops = array('classname' => 'widget_bokzuy_latest_badges', 
                            'description' => __('A list of the latest Bokzuy badges', 'bokzuy'));
        $this->WP_Widget('bokzuy_latest_badges', __('Bokzuy - Latest badges', 'bokzuy'), $widget_ops);
	}
        
	// Show widget 
    function widget($args, $instance){
        extract($args);
		
        echo $before_widget;
		
		// Show the widget title
        $title = apply_filters('widget_title', $instance['title']);
        if($title){
            echo $before_title . $title . $after_title;
        }
		else{
			echo $before_title. __('My latest badges', 'bokzuy'). $after_title;
		}

        // Show the badges
		$instance['user']
		$instance['password']
		$instance['number']
		if($instance['show_photos']){ } 

        // Show the powered text
		if($instance['show_powered']){ } 

        echo $after_widget;
    }
        
	// Save admin panel options
    function update($new_instance, $old_instance){
        $instance = $old_instance;
		$values = array('title', 'user', 'password', 'number', 'show_photos', 'show_powered');   
        
        foreach($values as $val){
            $instance[$val] = strip_tags($new_instance[$val]);
        }
        
        return $instance;
    }

	// Show admin panel widget
    function form($instance){
        global $wp_taxonomies;
                
        $defaults = array( 
            'title' => __('My latest badges', 'bokzuy'),
            'user' => '',
            'password' => '',
            'number' => 6,
            'show_photos' => True, 
            'show_powered' => True, 
        );
        $instance = wp_parse_args((array)$instance, $defaults); 

	    ?>
       
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e("Title", 'bokzuy'); ?>:</label>
            <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" 
	            value="<?php echo $instance['title']; ?>" class="widefat" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('user'); ?>"><?php _e("Bokzuy user", 'bokzuy'); ?>:</label>
            <input id="<?php echo $this->get_field_id('user'); ?>" name="<?php echo $this->get_field_name('user'); ?>" 
	            value="<?php echo $instance['user']; ?>" class="widefat" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('password'); ?>"><?php _e("Bokzuy password", 'bokzuy'); ?>:</label>
            <input id="<?php echo $this->get_field_id('password'); ?>" name="<?php echo $this->get_field_name('password'); ?>" 
	            value="<?php echo $instance['password']; ?>" class="widefat" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>">
                <?php _e("Number of badges to show", 'bokzuy'); ?>:</label>
            <input id="<?php echo $this->get_field_id('number'); ?>" 
                name="<?php echo $this->get_field_name('number'); ?>" 
                value="<?php echo $instance['number']; ?>" size="3" class="widefat" />
        </p>	
        <p>
            <input type="checkbox" id="<?php echo $this->get_field_id('show_photos'); ?>" 
                name="<?php echo $this->get_field_name('show_photos'); ?>" 
                <?php if($instance['show_photos']){ echo 'checked="checked"'; } ?> class="checkbox"/>
            <label for="<?php echo $this->get_field_id('show_photos'); ?>">
                <?php _e("Show the budges images", 'bokzuy'); ?></label>
        </p>
        <p>
            <input type="checkbox" id="<?php echo $this->get_field_id('show_powered'); ?>" 
                name="<?php echo $this->get_field_name('show_powered'); ?>" 
                <?php if($instance['show_powered']){ echo 'checked="checked"'; } ?> class="checkbox"/>
            <label for="<?php echo $this->get_field_id('show_powered'); ?>">
                <?php _e("Show Bokzuy info", 'bokzuy'); ?></label>
        </p>
        <?php
        }
}


?>
