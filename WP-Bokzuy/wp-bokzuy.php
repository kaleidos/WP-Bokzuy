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


// Load Bokzuy lib
include_once(dirname(__FILE__).'/lib/libbokzuy/libbokzuy.php');


// For i10n
add_action('init', 'bokzuy_textdomain'); 
function bokzuy_textdomain() {
    if (function_exists('load_plugin_textdomain')) {
        $dir = basename(dirname(__FILE__)).'/languages';
        load_plugin_textdomain( 'bokzuy', 'wp-content/plugins/'.$dir, $dir);
    }
}

// Adding style.css
add_action( 'wp_print_styles', 'bokzuy_styles' );
function bokzuy_styles(){
    if (function_exists('wp_enqueue_script')) {
       wp_enqueue_style('bokzuy', get_bloginfo('wpurl').'/wp-content/plugins/WP-Bokzuy/static/css/style.css');
    }
}

/**********************************************************/
/*************** Last user badges widget ******************/
/**********************************************************/

// A function to create the last user badges widget
add_action( 'widgets_init', 'last_user_badges_widget_init' );
function last_user_badges_widget_init() {
    register_widget('WP_Widget_Bokzuy_Last_User_Badges');
}

// WP_Widget_Bokzuy_Last_User_Badges class definition
class WP_Widget_Bokzuy_Last_User_Badges extends WP_Widget {
	
	// Init
    function WP_Widget_Bokzuy_Last_User_Badges() {
        $widget_ops = array('classname' => 'widget_bokzuy_last_user_badges', 
                            'description' => __('A list of the last Bokzuy badges from an user', 'bokzuy'));
        $this->WP_Widget('bokzuy_last_user_badges', __('WP-Bokzuy - Last user badges', 'bokzuy'), $widget_ops);
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
        $bokzuy = new Bokzuy($instance['user'], $instance['password']);
        if ($bokzuy->authenticate()){
            $bokies = $bokzuy->get_last_received_badges_from_me($instance['lang'], $instance['number']);
            ?>
            <div class="list-badges">
                <?php foreach ($bokies as $bokie){ ?>
                <div class="badge">
	    	        <?php if($instance['show_photos']){ ?>
                    <div class="badge-image">
                        <a href="<?php echo $bokie->bokyUrl; ?>" target="_blank">
                            <img src="<?php echo $bokie->badge->image; ?>" alt="<?php echo $bokie->badge->name; ?>"/>
                        </a>
                    </div>
                    <?php } ?>
                    <div class="badge-info">
                        <p class="badge-title"><a href="<?php echo $bokie->bokyUrl; ?>" target="_blank"><?php echo $bokie->badge->name; ?></a></p>
                        <p class="badge-description"><?php echo $bokie->badge->description; ?></p>
                        <p class="badge-sender"><?php printf(_x('from <a href="%1$s" target="_blank">%2$s</a> %3$s', '1 is sender prodile url, 2 is sender name and 3 is the date', 'bokzuy'), $bokie->sender->profile, $bokie->sender->name, since($bokie->date)); ?></p>
                    </div>
                    <div class="clear"></div>
                </div> 
                <?php } ?>
            </div>
            <?php
        }

        // Show bokzuy info
        if($instance['show_bokzuy_info']){ 
            ?>
            <div class="bokzuy-info">
                <a href="http://bokzuy.com" target="_blank">
                <span>Enjoy</span> <img src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/WP-Bokzuy/static/img/logo_bokzuy.png'; ?>" alt="<?php _e('Bokzuy web page', 'bokzuy'); ?>" class="bokzuy-logo" />
                </a>
            </div>
            <?php
        } 
        
        echo $after_widget;
    }

    // Save admin panel options
    function update($new_instance, $old_instance){
        $instance = $old_instance;
		$values = array('title', 'user', 'password', 'number', 'lang', 'show_photos', 'show_bokzuy_info');   
        
        foreach($values as $val){
            $instance[$val] = strip_tags($new_instance[$val]);
        }
        
        return $instance;
    }

    // Show admin panel widget
    function form($instance){
        global $wp_taxonomies;
                
        $defaults = array( 
            'title' => '',
            'user' => '',
            'password' => '',
            'number' => 6,
            'lang' => 'en',
            'show_photos' => True, 
            'show_bokzuy_info' => True, 
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
            <input type="password" id="<?php echo $this->get_field_id('password'); ?>" name="<?php echo $this->get_field_name('password'); ?>" 
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
            <input type="checkbox" id="<?php echo $this->get_field_id('show_bokzuy_info'); ?>" 
                name="<?php echo $this->get_field_name('show_bokzuy_info'); ?>" 
                <?php if($instance['show_bokzuy_info']){ echo 'checked="checked"'; } ?> class="checkbox"/>
            <label for="<?php echo $this->get_field_id('show_bokzuy_info'); ?>">
                <?php _e("Show Bokzuy info", 'bokzuy'); ?></label>
        </p>
        <?php
    }
}

/*
 * This function return the formatted date
 */
function since($date){
    $timestamp = strtotime($date);
    $diff = current_time('timestamp') - $timestamp;
    
    $month = array("",
        _x('Jan', 'First three letters of January', 'bokzuy'),
        _x('Feb', 'First three letters of February', 'bokzuy'),
        _x('Mar', 'First three letters of March', 'bokzuy'),
        _x('Apr', 'First three letters of April', 'bokzuy'),
        _x('May', 'First three letters of May', 'bokzuy'),
        _x('Jun', 'First three letters of June', 'bokzuy'),
        _x('Jul', 'First three letters of July', 'bokzuy'),
        _x('Aug', 'First three letters of August', 'bokzuy'),
        _x('Sep', 'First three letters of September', 'bokzuy'),
        _x('Oct', 'First three letters of October', 'bokzuy'),
        _x('Nov', 'First three letters of November', 'bokzuy'),
        _x('Dec', 'First three letters of December', 'bokzuy'));
    
    switch ($diff) {
        case $diff < 1:
            return __("now", 'bokzuy');
        case $diff < 60:
            return sprintf(_n("about a second ago", "about %d seconds ago", $diff, 'bokzuy'), $diff);
        case $diff < 3600:
            $rem = (int)($diff/60);
            return sprintf(_n("about a minute ago", "about %d minutes ago", $rem, 'bokzuy'), $rem);
        case $diff < 86400:
            $rem = (int)($diff/3600);
            return sprintf(_n("about an hour ago", "about %d hours ago", $rem, 'bokzuy'), $rem);
        case $diff < 2419200:
            $rem = (int)($diff/86400);
            return sprintf(_n("about a day ago", "about %d days ago", $rem, 'bokzuy'), $rem);
        case $diff < 29030400:
            return sprintf(_x('the %1$s %2$s', 'The day(1) and month(2)', 'bokzuy'), date(_x('jS', 'Day format', 'bokzuy'), $timestamp), $month[date('n', $timestamp)]); // 4 Apr
        default:
            return sprintf(_x('the %1$s %2$s %3$s', 'The day(1), month(2) and year(3)', 'bokzuy'), date(_x('jS', 'Day format', 'bokzuy'), $timestamp), $month[date('n', $timestamp)], date('y', $timestamp)); // 10 jun 09
    }
    return $timeago;
}

?>
