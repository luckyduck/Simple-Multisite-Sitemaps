<?php
/*
Plugin Name: Simple Multisite Sitemaps
Plugin URI: https://github.com/luckyduck/Simple-Multisite-Sitemaps
Description: A very simple solution for generating seperate Google XML Sitemaps for each WordPress site in a multisite network, on-the-fly.
Version: 1.1
Author: Jan Brinkmann
Author URI: http://the-luckyduck.de
License: GPLv2
*/

/*
/--------------------------------------------------------------------\
|                                                                    |
| License: GPL                                                       |
|                                                                    |
| Simple Multisite Sitemaps                                          |
| Copyright (C) 2012, Jan Brinkmann                                  |
| http://the-luckyduck.de                                            |
|                                                                    |
| This program is free software; you can redistribute it and/or      |
| modify it under the terms of the GNU General Public License        |
| as published by the Free Software Foundation; either version 2     |
| of the License, or (at your option) any later version.             |
|                                                                    |
| This program is distributed in the hope that it will be useful,    |
| but WITHOUT ANY WARRANTY; without even the implied warranty of     |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the      |
| GNU General Public License for more details.                       |
|                                                                    |
| You should have received a copy of the GNU General Public License  |
| along with this program; if not, write to the                      |
| Free Software Foundation, Inc.                                     |
| 51 Franklin Street, Fifth Floor                                    |
| Boston, MA  02110-1301, USA                                        |   
|                                                                    |
\--------------------------------------------------------------------/
*/

// plugin settings
function jb_sms_settings_page()
{
    add_settings_section("section", "Options", null, "jb_sms");
    add_settings_field("jb_sms-checkbox", "Add \"/\" at the end of every sitemap entry?", "jb_sms_slash_checkbox_display", "jb_sms", "section");  
    add_settings_field("jb_sms-only_canonical", "Add only canonical domain to sitemap?", "jb_sms_only_canonical_display", "jb_sms", "section");  
    register_setting("section", "jb_sms-checkbox");
    register_setting("section", "jb_sms-only_canonical");
}

function jb_sms_slash_checkbox_display()
{
   ?>
        <input type="checkbox" name="jb_sms-checkbox" value="1" <?php checked(1, get_option('jb_sms-checkbox'), true); ?> /> 
   <?php
}

function jb_sms_only_canonical_display()
{
   ?>
        <input type="checkbox" name="jb_sms-only_canonical" value="1" <?php checked(1, get_option('jb_sms-only_canonical'), true); ?> /> 
   <?php
}

add_action("admin_init", "jb_sms_settings_page");

function jb_sms_page()
{
  ?>
      <div class="wrap">
         <h1>Simple Multisite Sitemaps</h1>
         <form method="post" action="options.php">
            <?php
               settings_fields("section");
               do_settings_sections("jb_sms");
               submit_button(); 
            ?>
         </form>
      </div>
   <?php
}

function menu_item()
{
  add_submenu_page("options-general.php", "Sitemap Settings", "Sitemap Settings", "manage_options", "jb_sms", "jb_sms_page"); 
}
 
add_action("admin_menu", "menu_item");

// flush!
function jb_sms_sitemap_flush_rules() {
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}
add_action( 'init', 'jb_sms_sitemap_flush_rules' );

// rewrite
function jb_sms_xml_feed_rewrite($wp_rewrite) {
    global $wp_rewrite;

    $feed_rules = array(
        '.*sitemap.xml$' => 'index.php?feed=sitemap'
    );
 
    $wp_rewrite->rules = $feed_rules + $wp_rewrite->rules;
}

add_filter( 'generate_rewrite_rules', 'jb_sms_xml_feed_rewrite' );

// generate sitemap.xml using the template
function jb_sms_generate_sitemap() {
    $template_dir = dirname(__FILE__);
    load_template( $template_dir . '/sitemap-template.php' );
}
add_action( 'do_feed_sitemap', 'jb_sms_generate_sitemap', 10, 1 );

// add to robots.txt
function jb_sms_add_sitemap_to_robotstxt() {
    echo "Sitemap: ".get_option('siteurl')."/sitemap.xml\n\n";
}
add_action( 'do_robotstxt', 'jb_sms_add_sitemap_to_robotstxt' );
