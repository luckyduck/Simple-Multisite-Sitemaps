<?php
/*
 * XML Sitemap Template
 */

/*
/--------------------------------------------------------------------\
|                                                                    |
| License: GPL                                                       |
|                                                                    |
| Simple Multisite Sitemaps                                         |
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
header( 'HTTP/1.0 200 OK' );
header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );
header( 'Cache-Control: public, must-revalidate, proxy-revalidate, max-age=0' );

echo '<?xml version="1.0" encoding="'.get_option( 'blog_charset' ).'"?'.'>'; 
?>

<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
        xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

<?php 
global $blog_id;

$query_args = array(
    'post_type'   => array( 'post', 'page' ),
    'post_status' => 'publish',
    'orderby'     => 'date',
    'posts_per_page' => -1
);
query_posts( $query_args );

function get_custom_blog_permalink( $blog_id, $post_id ) {
    $page_permalink = get_blog_permalink( $blog_id, $post_id );
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        $page_permalink = 'https:' . preg_replace('#^https?.#', '', rtrim($page_permalink, '/'));
    }
    if (get_option('jb_sms-checkbox') == 1) {
        if (substr($page_permalink, -1) != '/') {
            $page_permalink = $page_permalink . '/';
        }
    }

    return $page_permalink;
}

if (get_option('jb_sms-only_canonical') == 1):
    $permalink = get_custom_blog_permalink($blog_id, $post->ID);
    $canonical_domain = parse_url($permalink, PHP_URL_SCHEME) . '://' . parse_url($permalink, PHP_URL_HOST);
?>
    <url>
        <loc><?php echo $canonical_domain; ?></loc>
        <lastmod><?php echo mysql2date( 'Y-m-d\TH:i:s+00:00', get_post_modified_time('Y-m-d H:i:s', true), false ); ?></lastmod> 
        <changefreq>weekly</changefreq> 
        <priority>1.0</priority>
    </url>
<?php
else:
    if ( have_posts()) : while (have_posts() ) : the_post();
?>
    <url>
        <loc><?php echo get_custom_blog_permalink( $blog_id, $post->ID ); ?></loc> 
        <lastmod><?php echo mysql2date( 'Y-m-d\TH:i:s+00:00', get_post_modified_time('Y-m-d H:i:s', true), false ); ?></lastmod> 
        <changefreq>weekly</changefreq> 
        <priority>1.0</priority>
    </url>
<?php 
endwhile; endif;
endif;
?> 
</urlset>
