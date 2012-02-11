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

define('BOKZUY_API_URL', 'http://api.bokzuy.com');

// For i10n
add_action('init', 'bokzuy_textdomain'); 
function bokzuy_textdomain() {
    if (function_exists('load_plugin_textdomain')) {
        $dir = basename(dirname(__FILE__)).'/languages';
        load_plugin_textdomain( 'bokzuy', 'wp-content/plugins/'.$dir, $dir);
    }
    // Todo: Show alert
}

// Adding style.css
add_action( 'wp_print_styles', 'bokzuy_styles' );
function bokzuy_styles(){
    if (function_exists('wp_enqueue_script')) {
       wp_enqueue_style('bokzuy', get_bloginfo('wpurl').'/wp-content/plugins/WP-Bokzuy/static/css/style.css');
    }
    // Todo: Show alert
}

/**********************************************************/
/***************** Last badges widget *********************/
/**********************************************************/

// A function to create the last badges widget
add_action( 'widgets_init', 'last_badges_widget_init' );
function last_badges_widget_init() {
    register_widget('WP_Widget_Bokzuy_Last_Badges');
}

// WP_Widget_Bokzuy_Last_Badges class definition
class WP_Widget_Bokzuy_Last_Badges extends WP_Widget {
	
	// Init
    function WP_Widget_Bokzuy_Last_Badges() {
        $widget_ops = array('classname' => 'widget_bokzuy_last_badges', 
                            'description' => __('A list of the last Bokzuy badges', 'bokzuy'));
        $this->WP_Widget('bokzuy_last_badges', __('WP-Bokzuy - Last badges', 'bokzuy'), $widget_ops);
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
			echo $before_title. __('My last badges', 'bokzuy'). $after_title;
		}
        
        // Show the badges
        $bokzuy = new Bokzuy($instance['user'], $instance['password'], $instance['lang']);
        if ($bokzuy->connect()){
            $badges = $bokzuy->get_last_badges($instance['number']);
            ?>
            <ul class="list-badges" style="list-style-type: none;">
            <?php
            foreach ($badges as $badge){ 
                ?>
                <li class="badge">
                    <a href="<?php echo $badge->bokyUrl; ?>" target="_blank">
		                <?php if($instance['show_photos']){ ?>
                            <img src="<?php echo $badge->badgeImage; ?>" alt="<?php echo $badge->name; ?>" style="width: 60px; vertical-align: middle;"/>
                        <?php } ?>
                        <p style="float: right; display: inline; text-align: right;"><?php echo $badge->name; ?></p>
                    </a>
                </li>
                <?php
            }
            ?>
            </ul>
            <?php
        }

        // Show the powered text
		if($instance['show_powered']){ 
            ?>
            <div class="bokzuy-info" style="float: right;">
                <a href="http://bokzuy.com" target="_blank">
                <span>Powered by</span> <img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/WP-Bokzuy/static/img/logo_bokzuy.png'; ?>" />
                </a>
            </div>
            <?php
        } 
        
        echo $after_widget;
    }
        
	// Save admin panel options
    function update($new_instance, $old_instance){
        $instance = $old_instance;
		$values = array('title', 'user', 'password', 'number', 'lang', 'show_photos', 'show_powered');   
        
        foreach($values as $val){
            $instance[$val] = strip_tags($new_instance[$val]);
        }
        
        return $instance;
    }

	// Show admin panel widget
    function form($instance){
        global $wp_taxonomies;
                
        $defaults = array( 
            'title' => __('My last badges', 'bokzuy'),
            'user' => '',
            'password' => '',
            'number' => 6,
            'lang' => 'en',
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
            <select id="<?php echo $this->get_field_id('number'); ?>" 
                name="<?php echo $this->get_field_name('number'); ?>" class="widefat">
                <?php 
                foreach( range(1, 20) as $option){
                    $sel = '';
                    if($instance['number'] == $option)
                        $sel = 'selected="selected"';
                    echo '<option '.$sel.' value="'.$option.'">'.$option.'</option>';
                }
                ?>
            </select>
        </p> 
        <p>
            <label for="<?php echo $this->get_field_id('lang'); ?>">
                <?php _e("Language", 'bokzuy'); ?>:</label>
            <select id="<?php echo $this->get_field_id('lang'); ?>" 
                name="<?php echo $this->get_field_name('lang'); ?>" class="widefat">
                <?php 
                foreach( array('es', 'en') as $option){
                    $sel = '';
                    if($instance['lang'] == $option)
                        $sel = 'selected="selected"';
                    echo '<option '.$sel.' value="'.$option.'">'.$option.'</option>';
                }
                ?>
            </select>
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

class Bokzuy{
    var $user_auth;
    var $user_id;
    var $lang;
   
    function Bokzuy($name, $password, $lang){
        $this->user_auth = $name.':'.$password;
        $this->lang = $lang;
    }
 
    function __GET_REQUEST($url, $options, $data){    
        $request = new HttpRequest($url, HttpRequest::METH_GET);
        $request->setOptions($options);
        $request->addQueryData($data);

        try {
            $request->send();
            //echo $request->getResponseCode();
            if ($request->getResponseCode() == 200) {
                //echo $request->getResponseBody();
                return $request->getResponseBody();
            }
        } catch (HttpException $ex) {
            return null;
        }
    }

    function __POST_REQUEST($url, $options, $fields){
        $request = new HttpRequest($url, HttpRequest::METH_POST);
        $request->setOptions($options);
        $request->addPostFields($fields);

        try {
            return  $request->send()->getBody();
        } catch (HttpException $ex) {
            return null;
        }
    }

    function connect(){  
        $url = BOKZUY_API_URL.'/user/id';
        $options = array('httpauth' => $this->user_auth);
        $data = array('lang' => $this->lang);
    
        $content = json_decode($this->__GET_REQUEST($url, $options, $data));

        if ($content && $content->success){
            $this->user_id = $content->userId;
            return $this->user_id;
        }
        return null;
    }

    function get_last_badges($count = 6){
        $url = BOKZUY_API_URL.'/user/'.$this->user_id.'/bokies';
        $options = array('httpauth' => $this->user_auth);
        $data = array('lang' => $this->lang, 
                      'max' => $count);

        $content = json_decode($this->__GET_REQUEST($url, $options, $data));

        if ($content && $content->success){
            return $content->result;
        }
        return null;
    }
}

?>
