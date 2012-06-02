<?php
/*
Plugin Name: Verelo Blog Monitoring Plugin
Plugin URI: https://github.com/FastTrackIT/WPPlugin_VereloMonitoring
Description: This plugin allows you to automatically enable Uptime, Response time, Malware and Virus monitoring on your blog using the remote Verelo monitoring system. If something goes wrong, you can be notified by Email, SMS or a phone call automatically.
Version: 1.1
Author: Verelo Inc.
Author URI: http://www.verelo.com
*/

/*  Copyright 2012  Verelo Inc.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );


define( 'VERELO_DIR', WP_PLUGIN_DIR . '/verelo-monitoring-plugin' );
define( 'VERELO_URL', WP_PLUGIN_URL . '/verelo-monitoring-plugin' );


if (!class_exists("Verelo")) :

class Verelo {
	var $addpage;
	
	function Verelo() {	
		add_action('admin_init', array(&$this,'init_admin') );
		add_action('init', array(&$this,'init') );
		add_action('admin_menu', array(&$this,'add_pages') );
		
		register_activation_hook( __FILE__, array(&$this,'activate') );
		register_deactivation_hook( __FILE__, array(&$this,'deactivate') );
	}
	

	function activate() {
		global $wpdb;

		if (function_exists('is_multisite') && is_multisite()) {
			// check if it is a network activation - if so, run the activation function for each blog id
			if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
				$old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
				die();
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					$this->_activate();
				}
				switch_to_blog($old_blog);
				return;
			}	
		} 
		$this->_activate();		
	}

	function deactivate() {
		global $wpdb;

		if (function_exists('is_multisite') && is_multisite()) {
			// check if it is a network activation - if so, run the activation function for each blog id
			if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
				$old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					$this->_deactivate();
				}
				switch_to_blog($old_blog);
				return;
			}	
		} 
		$this->_deactivate();		
	}	
	
	function _activate() {
		//get the required variables to setup an account for the user
	        $siteurl = urlencode(get_option( 'siteurl'));
		$adminemail = urlencode(get_option('admin_email'));
		$blogname = urlencode(get_option('blogname'));
		$provider = "wordpress-plugin";

		//api url to activate the user
		$url = "https://app.verelo.com/provision?act=user_create&user_email=$adminemail&user_zone=$blogname&test_url=$siteurl&provider=$provider";
		$response = file_get_contents($url);
		$obj = json_decode($response);
		if($obj->result != "success")
			die("Plugin installation failed, please try again later!");
		else
		{
			add_option( "verelo_autologin_service_url", $obj->response->service_url, "", true );
		}
	}
	
	function _deactivate() {}
	
	function init_admin() {
	}

	function init() {
		load_plugin_textdomain( 'verelo', VERELO_DIR . '/lang', basename( dirname( __FILE__ ) ) . '/lang' );
	}

	function add_pages() {
	
		// Add a new submenu
		$this->addpage = add_utility_page(	__('Verelo Blog Monitoring', 'verelo'), __('Verelo Blog Monitoring', 'verelo'), 
											'administrator', 'verelo', 
											array(&$this,'add_verelo_page') );
		add_action("admin_head-$this->addpage", array(&$this,'add_verelo_admin_head'));
		add_action("load-$this->addpage", array(&$this, 'on_load_verelo_page'));
		add_action("admin_print_styles-$this->addpage", array(&$this,'add_verelo_admin_styles'));
		add_action("admin_print_scripts-$this->addpage", array(&$this,'add_verelo_admin_scripts'));
	}

	function add_verelo_admin_head() {
	}
	
	
	function add_verelo_admin_styles() {
	}
	
	function add_verelo_admin_scripts() {
	}
	
	function on_load_verelo_page() {	
	}
	
	
	function add_verelo_page() {
		include('verelo.php');
	
	}

	function print_example($str, $print_info=TRUE) {
		if (!$print_info) return;
		__($str . "<br/><br/>\n", 'verelo' );
	}

} // end class
endif;

global $verelo;
if (class_exists("Verelo") && !$verelo) {
    $verelo = new Verelo();	
}	
?>
