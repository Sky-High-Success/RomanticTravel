<?php
/**
 * Upgrade related functions.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @global object $wpdb WordPress Database object.
 */

global $wpdb;

if (get_option(GEODIRECTORY_TEXTDOMAIN . '_db_version') != GEODIRECTORY_VERSION) {
    /**
     * Include custom database table related functions.
     *
     * @since 1.0.0
     * @package GeoDirectory
     */
    include_once('geodirectory-admin/admin_db_install.php');
    add_action('plugins_loaded', 'geodirectory_upgrade_all', 10);
    if (GEODIRECTORY_VERSION <= '1.3.6') {
        add_action('plugins_loaded', 'geodir_upgrade_136', 11);
    }

    if (GEODIRECTORY_VERSION <= '1.4.6') {
        add_action('init', 'geodir_upgrade_146', 11);
    }

    if (GEODIRECTORY_VERSION <= '1.4.8') {
        add_action('init', 'geodir_upgrade_148', 11);
    }
    update_option(GEODIRECTORY_TEXTDOMAIN . '_db_version', GEODIRECTORY_VERSION);

}


/**
 * Handles upgrade for all geodirectory versions.
 *
 * @since 1.0.0
 * @package GeoDirectory
 */
function geodirectory_upgrade_all()
{
    geodir_create_tables();
    geodir_update_review_db();
    gd_install_theme_compat();
}

/**
 * Handles upgrade for geodirectory versions <= 1.3.6.
 *
 * @since 1.0.0
 * @package GeoDirectory
 */
function geodir_upgrade_136()
{
    geodir_fix_review_overall_rating();
}

/**
 * Handles upgrade for geodirectory versions <= 1.4.6.
 *
 * @since 1.0.0
 * @package GeoDirectory
 */
function geodir_upgrade_146(){
    gd_convert_virtual_pages();
}



/**
 * Handles upgrade for geodirectory versions <= 1.4.8.
 *
 * @since 1.4.8
 * @package GeoDirectory
 */
function geodir_upgrade_148(){
    /*
     * Blank the users google password if present as we now use oAuth 2.0
     */
    update_option('geodir_ga_pass','');
    update_option('geodir_ga_user','');

}



/**
 * Handles upgrade for review table.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 */
function geodir_update_review_db()
{
    global $wpdb, $plugin_prefix;
// Add columns to review table
    geodir_add_column_if_not_exist(GEODIR_REVIEW_TABLE, 'post_status', 'INT(11) DEFAULT NULL');
    geodir_add_column_if_not_exist(GEODIR_REVIEW_TABLE, 'post_date', 'DATETIME NOT NULL');
    geodir_add_column_if_not_exist(GEODIR_REVIEW_TABLE, 'post_city', 'varchar(30) NULL DEFAULT NULL');
    geodir_add_column_if_not_exist(GEODIR_REVIEW_TABLE, 'post_region', 'varchar(30) NULL DEFAULT NULL');
    geodir_add_column_if_not_exist(GEODIR_REVIEW_TABLE, 'post_country', 'varchar(30) NULL DEFAULT NULL');
    geodir_add_column_if_not_exist(GEODIR_REVIEW_TABLE, 'post_latitude', 'varchar(20) NULL DEFAULT NULL');
    geodir_add_column_if_not_exist(GEODIR_REVIEW_TABLE, 'post_longitude', 'varchar(20) NULL DEFAULT NULL');
    geodir_add_column_if_not_exist(GEODIR_REVIEW_TABLE, 'comment_content', 'TEXT NULL DEFAULT NULL');

    // this should not be needed anymore becasue of geodir_fix_review_location()
    /*$reviews = $wpdb->get_results("SELECT * FROM ".GEODIR_REVIEW_TABLE." WHERE post_city='' OR post_city IS NULL OR post_latitude='' OR post_latitude IS NULL");
    foreach($reviews as $review){
    $location = $wpdb->get_row("SELECT * FROM ".$plugin_prefix.$review->post_type."_detail WHERE post_id=".$review->post_id);
    $wpdb->query($wpdb->prepare("UPDATE ".GEODIR_REVIEW_TABLE." gdr SET gdr.post_city=%s, gdr.post_region=%s , gdr.post_country=%s , gdr.post_latitude=%s, gdr.post_longitude=%s WHERE gdr.id=%d",$location->post_city,$location->post_region,$location->post_country,$review->id,$location->post_latitude,$location->post_longitude));
    }*/

    geodir_fix_review_date();
    geodir_fix_review_post_status();
    geodir_fix_review_content();
    geodir_fix_review_location();

}

/**
 * Fixes review date.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @global object $wpdb WordPress Database object.
 */
function geodir_fix_review_date()
{
    global $wpdb;
    $wpdb->query("UPDATE " . GEODIR_REVIEW_TABLE . " gdr JOIN $wpdb->comments c ON gdr.comment_id=c.comment_ID SET gdr.post_date = c.comment_date WHERE gdr.post_date='0000-00-00 00:00:00'");
}

/**
 * Fixes review post status.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @global object $wpdb WordPress Database object.
 */
function geodir_fix_review_post_status()
{
    global $wpdb;
    $wpdb->query("UPDATE " . GEODIR_REVIEW_TABLE . " gdr JOIN $wpdb->posts p ON gdr.post_id=p.ID SET gdr.post_status = 1 WHERE gdr.post_status IS NULL AND p.post_status='publish'");
}

/**
 * Fixes review content.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @global object $wpdb WordPress Database object.
 * @return bool
 */
function geodir_fix_review_content()
{
    global $wpdb;
    if ($wpdb->query("UPDATE " . GEODIR_REVIEW_TABLE . " gdr JOIN $wpdb->comments c ON gdr.comment_id=c.comment_ID SET gdr.comment_content = c.comment_content WHERE gdr.comment_content IS NULL")) {
        return true;
    } else {
        return false;
    }
}

/**
 * Fixes review location.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @global object $wpdb WordPress Database object.
 * @return bool
 */
function geodir_fix_review_location()
{
    global $wpdb;

    $all_postypes = geodir_get_posttypes();

    if (!empty($all_postypes)) {
        foreach ($all_postypes as $key) {
            // update each GD CTP

            $wpdb->query("UPDATE " . GEODIR_REVIEW_TABLE . " gdr JOIN " . $wpdb->prefix . "geodir_" . $key . "_detail d ON gdr.post_id=d.post_id SET gdr.post_latitude = d.post_latitude, gdr.post_longitude = d.post_longitude, gdr.post_city = d.post_city,  gdr.post_region=d.post_region, gdr.post_country=d.post_country WHERE gdr.post_latitude IS NULL OR gdr.post_city IS NULL");

        }
        return true;
    }
    return false;
}

/**
 * Fixes review overall rating.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @global object $wpdb WordPress Database object.
 */
function geodir_fix_review_overall_rating()
{
    global $wpdb;

    $all_postypes = geodir_get_posttypes();

    if (!empty($all_postypes)) {
        foreach ($all_postypes as $key) {
            // update each GD CTP
            $reviews = $wpdb->get_results("SELECT post_id FROM " . $wpdb->prefix . "geodir_" . $key . "_detail d");

            if (!empty($reviews)) {
                foreach ($reviews as $post_id) {
                    geodir_update_postrating($post_id->post_id, $key);
                }

            }

        }

    }
}


############################################
########### THEME COMPATIBILITY ############
############################################

/**
 * Inserts theme compatibility settings data for supported themes.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @global object $wpdb WordPress Database object.
 */
function gd_install_theme_compat()
{
    global $wpdb;

    $theme_compat = array();
    $theme_compat = get_option('gd_theme_compats');
//GDF
    $theme_compat['GeoDirectory_Framework'] = array(
        'geodir_wrapper_open_id' => 'geodir_wrapper',
        'geodir_wrapper_open_class' => '',
        'geodir_wrapper_open_replace' => '',
        'geodir_wrapper_close_replace' => '</div></div><!-- content ends here-->',
        'geodir_wrapper_content_open_id' => 'geodir_content',
        'geodir_wrapper_content_open_class' => '',
        'geodir_wrapper_content_open_replace' => '',
        'geodir_wrapper_content_close_replace' => '',
        'geodir_article_open_id' => '',
        'geodir_article_open_class' => '',
        'geodir_article_open_replace' => '',
        'geodir_article_close_replace' => '',
        'geodir_sidebar_right_open_id' => '',
        'geodir_sidebar_right_open_class' => '',
        'geodir_sidebar_right_open_replace' => '<aside id="gd-sidebar-wrapper" class="sidebar [class]" role="complementary" itemscope itemtype="[itemtype]" [width_css]>',
        'geodir_sidebar_right_close_replace' => '',
        'geodir_sidebar_left_open_id' => '',
        'geodir_sidebar_left_open_class' => '',
        'geodir_sidebar_left_open_replace' => '<aside  id="gd-sidebar-wrapper" class="sidebar [class]" role="complementary" itemscope itemtype="[itemtype]" [width_css]>',
        'geodir_sidebar_left_close_replace' => '',
        'geodir_main_content_open_id' => '',
        'geodir_main_content_open_class' => '',
        'geodir_main_content_open_replace' => '<!-- removed -->',
        'geodir_main_content_close_replace' => '<!-- removed -->',
        'geodir_top_content_add' => '',
        'geodir_before_main_content_add' => '<div class="clearfix geodir-common">',
        'geodir_before_widget_filter' => '',
        'geodir_after_widget_filter' => '',
        'geodir_theme_compat_css' => '',
        'geodir_theme_compat_js' => '',
        'geodir_theme_compat_default_options' => '',
        'geodir_theme_compat_code' => ''
    );

//Avada
    $theme_compat['Avada'] = array(
        'geodir_wrapper_open_id' => '',
        'geodir_wrapper_open_class' => '',
        'geodir_wrapper_open_replace' => '<!-- removed -->',
        'geodir_wrapper_close_replace' => '<!-- removed -->',
        'geodir_wrapper_content_open_id' => 'content',
        'geodir_wrapper_content_open_class' => '',
        'geodir_wrapper_content_open_replace' => '',
        'geodir_wrapper_content_close_replace' => '',
        'geodir_article_open_id' => '',
        'geodir_article_open_class' => '',
        'geodir_article_open_replace' => '',
        'geodir_article_close_replace' => '',
        'geodir_sidebar_right_open_id' => '',
        'geodir_sidebar_right_open_class' => '',
        'geodir_sidebar_right_open_replace' => '<div id="sidebar" class="sidebar [class]" role="complementary" itemscope itemtype="[itemtype]" [width_css]>',
        'geodir_sidebar_right_close_replace' => '</div><!-- end sidebar -->',
        'geodir_sidebar_left_open_id' => '',
        'geodir_sidebar_left_open_class' => '',
        'geodir_sidebar_left_open_replace' => '<div id="sidebar" class="sidebar [class]" role="complementary" itemscope itemtype="[itemtype]" [width_css]>',
        'geodir_sidebar_left_close_replace' => '</div><!-- end sidebar -->',
        'geodir_main_content_open_id' => '',
        'geodir_main_content_open_class' => '',
        'geodir_main_content_open_replace' => '<!-- removed -->',
        'geodir_main_content_close_replace' => '<!-- removed -->',
        'geodir_top_content_add' => '',
        'geodir_before_main_content_add' => '',
        'geodir_before_widget_filter' => '',
        'geodir_after_widget_filter' => '',
        'geodir_theme_compat_css' => stripslashes('.geodir-sidebar-left{float:left}select,textarea{border-style:solid;border-width:1px}.top-menu li > div{visibility:visible}.geodir-chosen-container-single .chosen-single{height:auto}ul li#menu-item-gd-location-switcher ul{width:222px}ul li#menu-item-gd-location-switcher ul li{padding-right:0!important}#mobile-nav li#mobile-menu-item-gd-location-switcher li a{padding-left:10px;padding-right:10px}#menu-item-gd-location-switcher dd,#mobile-menu-item-gd-location-switcher{margin-left:0}#menu-item-gd-location-switcher dd a{display:block}.geodir-chosen-container .chosen-results li.highlighted{background-color:#eee;background-image:none;color:#444}#mobile-nav li.mobile-nav-item li a:before{content:\'\';margin:0}#mobile-nav li.mobile-nav-item li a{padding:10px;width:auto}.geodir-listing-search{text-align:center}.geodir-search{float:none;margin:0}.geodir-search select,.geodir-search .search_by_post,.geodir-search input[type="text"],.geodir-search input[type="button"],.geodir-search input[type="submit"]{display:inline-block;float:none}.geodir-cat-list ul li,.map_category ul li{list-style-type:none}.wpgeo-avada .page-title ul li:after{content:\'\'}.top_banner_section{margin-bottom:0}.geodir-category-list-in{margin:0;padding:15px}.geodir_full_page .geodir-cat-list .widget-title{margin-top:0}.geodir_full_page .geodir-cat-list ul li{padding-left:0}.geodir-loc-bar{border:none;margin:0;padding:0}.geodir-loc-bar-in{padding:15px 0}.geodir_full_page section.widget{margin-bottom:20px}.sidebar .geodir-loginbox-list li{margin-bottom:10px;padding-bottom:10px}.sidebar .geodir-loginbox-list li a{display:block}.sidebar .geodir-chosen-container .chosen-results li{margin:0;padding:5px 6px}.sidebar .geodir-chosen-container .chosen-results li.highlighted{background:#eee;background-image:none;color:#000}.sidebar .geodir_category_list_view li.geodir-gridview{display:inline-block;margin-bottom:15px}.wpgeo-avada.double-sidebars #main #sidebar{margin-left:3%}.wpgeo-avada.double-sidebars #main #sidebar-2{margin-left:-100%}.wpgeo-avada.double-sidebars #content{float:left;margin-left:0}.geodir_full_page section.widget{margin-bottom: 0px;} .sidebar .widget .geodir-hide {display: none;}'),
        'geodir_theme_compat_js' => '',
        'geodir_theme_compat_default_options' => '',
        'geodir_theme_compat_code' => 'Avada'
    );

//Enfold
    $theme_compat['Enfold'] = array(
        'geodir_wrapper_open_id' => '',
        'geodir_wrapper_open_class' => '',
        'geodir_wrapper_open_replace' => '',
        'geodir_wrapper_close_replace' => '</div></div><!-- content ends here-->',
        'geodir_wrapper_content_open_id' => '',
        'geodir_wrapper_content_open_class' => '',
        'geodir_wrapper_content_open_replace' => '',
        'geodir_wrapper_content_close_replace' => '</div></main>',
        'geodir_article_open_id' => '',
        'geodir_article_open_class' => '',
        'geodir_article_open_replace' => '',
        'geodir_article_close_replace' => '',
        'geodir_sidebar_right_open_id' => '',
        'geodir_sidebar_right_open_class' => '',
        'geodir_sidebar_right_open_replace' => '',
        'geodir_sidebar_right_close_replace' => '</div></aside><!-- sidebar ends here-->',
        'geodir_sidebar_left_open_id' => '',
        'geodir_sidebar_left_open_class' => '',
        'geodir_sidebar_left_open_replace' => '',
        'geodir_sidebar_left_close_replace' => '</div></aside><!-- sidebar ends here-->',
        'geodir_main_content_open_id' => '',
        'geodir_main_content_open_class' => '',
        'geodir_main_content_open_replace' => '',
        'geodir_main_content_close_replace' => '',
        'geodir_top_content_add' => '',
        'geodir_before_main_content_add' => '',
        'geodir_before_widget_filter' => '',
        'geodir_after_widget_filter' => '',
        'geodir_theme_compat_css' => stripslashes('.geodir_full_page .top_banner_section{margin-bottom:0}.widget .geodir-cat-list ul li{clear:none}.wpgeo-enfold .av-main-nav ul{width:222px}.geodir-listing-search .geodir-loc-bar{border-top:none;padding:0}#main .geodir-listing-search,.geodir-listing-search .geodir-loc-bar{margin-bottom:0}#main .geodir-loc-bar-in,#main .geodir-category-list-in{background-color:#fcfcfc;margin:20px 0;padding:20px}#main .geodir_full_page .geodir-loc-bar-in,#main .geodir_full_page .geodir-loc-bar,#main .geodir_full_page .geodir-category-list-in{margin-top:0;margin-bottom:0}#main .geodir-loc-bar-in{padding:20px}#main .geodir-search{margin:0;width:100%}#main .geodir-search select{margin:0 3% 0 0;padding:8px 10px;width:13%}#main .geodir-search input[type="text"]{margin:0 3% 0 0;padding:10px;width:32.4%}#main .geodir-search input[type="button"],#main .geodir-search input[type="submit"]{font-size:inherit;line-height:2.25;margin:0;padding:7px;width:13%}.enfold-home-top section.widget{margin:0;padding:0}.enfold-home-top .top_banner_section{margin-bottom:0}.enfold-home-top .geodir-loc-bar{background:#fcfcfc;border:none;margin:0;padding:0}#main .enfold-home-top .geodir-loc-bar-in{background:none;border:none;margin:0 auto;padding:20px 0}#main .geodir-breadcrumb{border-bottom-style:solid;border-bottom-width:1px}#gd-tabs dt{clear:none}#geodir_slider ul li{list-style-type:none;margin:0;padding:0}#respond{clear:both}#comments .comments-title span{display:inline;font-size:inherit;font-weight:700}#reviewsTab .comments-area .bypostauthor cite span{display:inline}#top #comments .commentlist .comment,#top #comments .commentlist .comment > div{min-height:0}.commentlist .commenttext{padding-top:15px}#comment_imagesdropbox{margin-bottom:20px}.wpgeo-enfold .geodir_category_list_view li{margin-left:0;padding:0}.widget ul.geodir-loginbox-list{overflow:visible}.geodir_category_list_view li .geodir-post-img{display:block}.wpgeo-enfold .geodir_event_listing_calendar tr.title{background:#ccc}@media only screen and (max-width:480px){.geodir_category_list_view li .geodir-content,.geodir_category_list_view li .geodir-post-img,.geodir_category_list_view li .geodir-addinfo{float:none;width:100%;margin:10px 0}#main .geodir-search input[type="text"],#main .geodir-search input[type="button"],#main .geodir-search input[type="submit"],#main .geodir-search select{margin:10px 0;width:100%}}#main .geodir_full_page section:last-child .geodir-loc-bar{margin-bottom: -1px;border-bottom: none;}'),
        'geodir_theme_compat_js' => '',
        'geodir_theme_compat_default_options' => '',
        'geodir_theme_compat_code' => 'Enfold'
    );

// X
    $theme_compat['X'] = array(
        'geodir_wrapper_open_id' => '',
        'geodir_wrapper_open_class' => '',
        'geodir_wrapper_open_replace' => '',
        'geodir_wrapper_close_replace' => '',
        'geodir_wrapper_content_open_id' => '',
        'geodir_wrapper_content_open_class' => '',
        'geodir_wrapper_content_open_replace' => '',
        'geodir_wrapper_content_close_replace' => '',
        'geodir_article_open_id' => '',
        'geodir_article_open_class' => '',
        'geodir_article_open_replace' => '',
        'geodir_article_close_replace' => '',
        'geodir_sidebar_right_open_id' => '',
        'geodir_sidebar_right_open_class' => '',
        'geodir_sidebar_right_open_replace' => '',
        'geodir_sidebar_right_close_replace' => '',
        'geodir_sidebar_left_open_id' => '',
        'geodir_sidebar_left_open_class' => '',
        'geodir_sidebar_left_open_replace' => '',
        'geodir_sidebar_left_close_replace' => '',
        'geodir_main_content_open_id' => '',
        'geodir_main_content_open_class' => '',
        'geodir_main_content_open_replace' => '',
        'geodir_main_content_close_replace' => '',
        'geodir_top_content_add' => '',
        'geodir_before_main_content_add' => '',
        'geodir_before_widget_filter' => '',
        'geodir_after_widget_filter' => '',
        'geodir_theme_compat_css' => stripslashes('.x-colophon.bottom{clear:both}#geodir-main-content,.geodir_flex-container{margin-top:16px}.geodir-x ul{list-style:none}.widget ul.geodir_category_list_view{border:none}.geodir_category_list_view li.geodir-gridview:last-child{border-bottom:1px solid #e1e1e1}.home .x-header-landmark{display:none}.geodir-x .x-main .geodir_advance_search_widget{margin:0}.geodir-x .top_banner_section{margin-bottom:0}.geodir-loc-bar{background:rgba(0,0,0,0.05);margin:0;padding:0}.geodir-loc-bar-in{background:none;border:none;padding:10px}.geodir-search{margin:0;width:100%}.widget .geodir-search select,.geodir-search input[type="text"],.geodir-search input[type="button"],.geodir-search input[type="submit"]{border:1px solid #ccc;box-shadow:none;height:auto;line-height:21px;margin:0 1% 0 0;padding:5px 10px}.widget .geodir-search select,.geodir-search input[type="text"]{width:28%}.geodir-search input[type="submit"],.geodir-search input[type="button"]{line-height:19px;margin-right:0;width:11%}.geodir-search input:hover[type="submit"],.geodir-search input:hover[type="button"]{background:#333;color:#fff}.geodir-cat-list .widget-title{margin-top:0}.geodir-x .geodir-category-list-in{background:rgba(0,0,0,0.05);border:none}.widget .geodir-cat-list ul.geodir-popular-cat-list{border:none;border-radius:0;box-shadow:none}.geodir_full_page .geodir-cat-list ul li{border:none}.geodir_full_page .geodir-cat-list ul li a{border:none}.post-type-archive .geodir-loc-bar{border:none;margin-top:20px}#menu-item-gd-location-switcher dd{margin-left:0}.geodir-chosen-container-single .chosen-single{height:auto}.widget ul.geodir-loginbox-list{overflow:visible}.geodir_full_page section.widget{clear:both}.x-ethos .entry-title{margin-bottom:20px}.x-ethos .geodir-chosen-container-single .chosen-single{padding:0 0 0 8px}.x-ethos .widget ul li a,.x-ethos .geodir_category_list_view li{color:#333}@media only screen and (max-width:767px){.widget .geodir-search select,.geodir-search input[type="text"],.geodir-search input[type="button"],.geodir-search input[type="submit"]{margin:0 0 10px;width:100%}}.geodir_full_page .geodir-loc-bar-in,.geodir_full_page .geodir-loc-bar,.geodir_full_page .geodir-category-list-in{margin-top:0;margin-bottom:0}.geodir_full_page .geodir-loc-bar-in,.geodir_full_page .geodir-category-list-in{border-bottom:1px solid rgba(0,0,0,0.1)}.geodir_full_page .geodir-listing-search{text-align:center}.geodir_full_page .geodir-search{float:none;margin:0}.geodir_full_page .geodir-search select,.geodir_full_page .geodir-search .search_by_post,.geodir_full_page .geodir-search input[type="text"],.geodir_full_page .geodir-search input[type="button"],.geodir_full_page .geodir-search input[type="submit"]{display:inline-block;float:none}'),
        'geodir_theme_compat_js' => '',
        'geodir_theme_compat_default_options' => '',
        'geodir_theme_compat_code' => 'X'
    );

// Divi
    $theme_compat['Divi'] = array(
        'geodir_wrapper_open_id' => 'main-content',
        'geodir_wrapper_open_class' => '',
        'geodir_wrapper_open_replace' => '',
        'geodir_wrapper_close_replace' => '',
        'geodir_wrapper_content_open_id' => 'left-area',
        'geodir_wrapper_content_open_class' => '',
        'geodir_wrapper_content_open_replace' => '<div class="container"><div id="content-area" class="clearfix"><div id="[id]" class="[class]" role="main" >',
        'geodir_wrapper_content_close_replace' => '',
        'geodir_article_open_id' => '',
        'geodir_article_open_class' => '',
        'geodir_article_open_replace' => '',
        'geodir_article_close_replace' => '',
        'geodir_sidebar_right_open_id' => 'sidebar',
        'geodir_sidebar_right_open_class' => '',
        'geodir_sidebar_right_open_replace' => '<aside  id="[id]" class="" role="complementary" itemscope itemtype="[itemtype]" >',
        'geodir_sidebar_right_close_replace' => '</aside><!-- sidebar ends here--></div></div>',
        'geodir_sidebar_left_open_id' => 'sidebar',
        'geodir_sidebar_left_open_class' => '',
        'geodir_sidebar_left_open_replace' => '<aside  id="[id]" class="" role="complementary" itemscope itemtype="[itemtype]" >',
        'geodir_sidebar_left_close_replace' => '</aside><!-- sidebar ends here--></div></div>',
        'geodir_main_content_open_id' => '',
        'geodir_main_content_open_class' => '',
        'geodir_main_content_open_replace' => '',
        'geodir_main_content_close_replace' => '',
        'geodir_top_content_add' => '',
        'geodir_before_main_content_add' => '',
        'geodir_before_widget_filter' => '',
        'geodir_after_widget_filter' => '',
        'geodir_theme_compat_css' => stripslashes('#left-area ul.geodir-direction-nav{list-style-type:none}#sidebar .geodir-company_info{margin-left:30px}#sidebar .geodir-widget{float:none;margin:0 0 30px 30px}.geodir_full_page .geodir-loc-bar{padding:0;margin:0;border:none}.geodir_full_page .geodir-category-list-in{margin-top:0}.geodir_full_page .top_banner_section{margin-bottom:0}.archive .entry-header,.geodir-breadcrumb{border-bottom:1px solid #e2e2e2}.archive .entry-header h1,ul#breadcrumbs{margin:0 auto;width:1080px}#left-area ul.geodir_category_list_view{padding:10px 0}.nav li#menu-item-gd-location-switcher ul{width:222px}#menu-item-gd-location-switcher li.gd-location-switcher-menu-item{padding-right:0}#menu-item-gd-location-switcher dd{margin-left:0}#menu-item-gd-location-switcher .geodir_location_tab_container dd a{padding:5px;width:auto}@media only screen and ( max-width: 980px ){.geodir-loc-bar-in,.geodir-cat-list,ul#breadcrumbs{width:690px}}@media only screen and ( max-width: 767px ){.geodir-loc-bar-in,.geodir-cat-list,ul#breadcrumbs{width:400px}}@media only screen and ( max-width: 479px ){.geodir-loc-bar-in,.geodir-cat-list,ul#breadcrumbs{width:280px}}.geodir_full_page .geodir-listing-search{text-align:center}.geodir_full_page .geodir-search{float:none;margin:0}.geodir_full_page .geodir-search select,.geodir_full_page .geodir-search .search_by_post,.geodir_full_page .geodir-search input[type="text"],.geodir_full_page .geodir-search input[type="button"],.geodir_full_page .geodir-search input[type="submit"]{display:inline-block;float:none}'),
        'geodir_theme_compat_js' => '',
        'geodir_theme_compat_default_options' => '',
        'geodir_theme_compat_code' => 'Divi'
    );

// Genesis
    $theme_compat['Genesis'] = array(
        'geodir_wrapper_open_id' => '',
        'geodir_wrapper_open_class' => 'content-sidebar-wrap',
        'geodir_wrapper_open_replace' => '',
        'geodir_wrapper_close_replace' => '',
        'geodir_wrapper_content_open_id' => '',
        'geodir_wrapper_content_open_class' => 'content',
        'geodir_wrapper_content_open_replace' => '<div class="[class]" role="main" >',
        'geodir_wrapper_content_close_replace' => '',
        'geodir_article_open_id' => '',
        'geodir_article_open_class' => '',
        'geodir_article_open_replace' => '',
        'geodir_article_close_replace' => '',
        'geodir_sidebar_right_open_id' => '',
        'geodir_sidebar_right_open_class' => 'sidebar sidebar-primary widget-area',
        'geodir_sidebar_right_open_replace' => '<aside  id="[id]" class="[class]" role="complementary" itemscope itemtype="[itemtype]">',
        'geodir_sidebar_right_close_replace' => '',
        'geodir_sidebar_left_open_id' => '',
        'geodir_sidebar_left_open_class' => 'sidebar sidebar-secondary widget-area',
        'geodir_sidebar_left_open_replace' => '<aside  id="[id]" class="[class]" role="complementary" itemscope itemtype="[itemtype]">',
        'geodir_sidebar_left_close_replace' => '',
        'geodir_main_content_open_id' => '',
        'geodir_main_content_open_class' => '',
        'geodir_main_content_open_replace' => '<main  id="[id]" class="entry [class]"  role="main">',
        'geodir_main_content_close_replace' => '',
        'geodir_top_content_add' => '',
        'geodir_before_main_content_add' => '',
        'geodir_before_widget_filter' => '',
        'geodir_after_widget_filter' => '',
        'geodir_theme_compat_css' => stripslashes('.full-width-content #geodir-wrapper-content{width:100%}.geodir_full_page .geodir-listing-search{text-align:center}.geodir_full_page .geodir-search{float:none;margin:0}.geodir_full_page .geodir-search select,.geodir_full_page .geodir-search .search_by_post,.geodir_full_page .geodir-search input[type="text"],.geodir_full_page .geodir-search input[type="button"],.geodir_full_page .geodir-search input[type="submit"]{display:inline-block;float:none}.content{float:left}.sidebar-content .content,.sidebar-content #geodir-wrapper-content{float:right}.sidebar .geodir-company_info{background-color:#fff;border:none}.geodir_full_page .geodir-loc-bar{padding:0;margin:0;border:none}.geodir_full_page .geodir-category-list-in{margin-top:0}.geodir_full_page .top_banner_section{margin-bottom:0}.geodir-breadcrumb-bar{margin-bottom:-35px} .search-page .entry-title,.listings-page .entry-title{font-size: 20px;}.site-inner .geodir-breadcrumb-bar{margin-bottom:0px}'),
        'geodir_theme_compat_js' => '',
        'geodir_theme_compat_default_options' => '',
        'geodir_theme_compat_code' => 'Genesis'
    );

// Jupiter
    $theme_compat['Jupiter'] = array(
        'geodir_wrapper_open_id' => '',
        'geodir_wrapper_open_class' => '',
        'geodir_wrapper_open_replace' => '<div id="theme-page"><div class="mk-main-wrapper-holder"><div  class="theme-page-wrapper mk-main-wrapper  mk-grid vc_row-fluid">',
        'geodir_wrapper_close_replace' => '</div></div></div>',
        'geodir_wrapper_content_open_id' => '',
        'geodir_wrapper_content_open_class' => '',
        'geodir_wrapper_content_open_replace' => '',
        'geodir_wrapper_content_close_replace' => '',
        'geodir_article_open_id' => '',
        'geodir_article_open_class' => '',
        'geodir_article_open_replace' => '',
        'geodir_article_close_replace' => '',
        'geodir_sidebar_right_open_id' => 'mk-sidebar',
        'geodir_sidebar_right_open_class' => 'mk-builtin geodir-sidebar-right geodir-listings-sidebar-right',
        'geodir_sidebar_right_open_replace' => '',
        'geodir_sidebar_right_close_replace' => '',
        'geodir_sidebar_left_open_id' => 'mk-sidebar',
        'geodir_sidebar_left_open_class' => 'mk-builtin geodir-sidebar-right geodir-listings-sidebar-right',
        'geodir_sidebar_left_open_replace' => '',
        'geodir_sidebar_left_close_replace' => '',
        'geodir_main_content_open_id' => '',
        'geodir_main_content_open_class' => '',
        'geodir_main_content_open_replace' => '',
        'geodir_main_content_close_replace' => '',
        'geodir_top_content_add' => '',
        'geodir_before_main_content_add' => '',
        'geodir_before_widget_filter' => '',
        'geodir_after_widget_filter' => '',
        'geodir_before_title_filter' => '<h3 class="widgettitle geodir-widget-title">',
        'geodir_after_title_filter' => '',
        'geodir_menu_li_class_filter' => 'menu-item menu-item-has-children no-mega-menu',
        'geodir_sub_menu_ul_class_filter' => '',
        'geodir_sub_menu_li_class_filter' => '',
        'geodir_menu_a_class_filter' => 'menu-item-link',
        'geodir_sub_menu_a_class_filter' => 'menu-item-link one-page-nav-item',
        'geodir_location_switcher_menu_li_class_filter' => 'menu-item menu-item-type-social menu-item-type-social gd-location-switcher menu-item-has-children no-mega-menu',
        'geodir_location_switcher_menu_a_class_filter' => 'menu-item-link',
        'geodir_location_switcher_menu_sub_ul_class_filter' => '',
        'geodir_location_switcher_menu_sub_li_class_filter' => '',
        'geodir_theme_compat_css' => stripslashes('.geodir-widget li,.geodir_category_list_view li{margin:0}#theme-page h3.geodir-entry-title{font-size:14px}#menu-item-gd-location-switcher dd{line-height:44px}#menu-item-gd-location-switcher .geodir_location_sugestion{line-height:20px}.geodir_loginbox{overflow:visible}.geodir_full_page .geodir-listing-search{text-align:center}.geodir_full_page .geodir-search{float:none;margin:0}.geodir_full_page .geodir-search select,.geodir_full_page .geodir-search .search_by_post,.geodir_full_page .geodir-search input[type="text"],.geodir_full_page .geodir-search input[type="button"],.geodir_full_page .geodir-search input[type="submit"]{display:inline-block;float:none}'),
        'geodir_theme_compat_js' => '',
        'geodir_theme_compat_default_options' => '',
        'geodir_theme_compat_code' => 'Jupiter'
    );

// Multi News
    $theme_compat['Multi_News'] = array(
        'geodir_wrapper_open_id' => '',
        'geodir_wrapper_open_class' => 'main-container clearfix',
        'geodir_wrapper_open_replace' => '',
        'geodir_wrapper_close_replace' => '',
        'geodir_wrapper_content_open_id' => '',
        'geodir_wrapper_content_open_class' => '',
        'geodir_wrapper_content_open_replace' => '<div class="main-left" ><div class="main-content  ">',
        'geodir_wrapper_content_close_replace' => '</div></div>',
        'geodir_article_open_id' => '',
        'geodir_article_open_class' => '',
        'geodir_article_open_replace' => '',
        'geodir_article_close_replace' => '',
        'geodir_sidebar_right_open_id' => '',
        'geodir_sidebar_right_open_class' => '',
        'geodir_sidebar_right_open_replace' => '<aside  class="sidebar" role="complementary" itemscope itemtype="[itemtype]" >',
        'geodir_sidebar_right_close_replace' => '',
        'geodir_sidebar_left_open_id' => '',
        'geodir_sidebar_left_open_class' => '',
        'geodir_sidebar_left_open_replace' => '<aside  class="secondary-sidebar" role="complementary" itemscope itemtype="[itemtype]" >',
        'geodir_sidebar_left_close_replace' => '',
        'geodir_main_content_open_id' => '',
        'geodir_main_content_open_class' => '',
        'geodir_main_content_open_replace' => '<div class="site-content page-wrap">',
        'geodir_main_content_close_replace' => '</div>',
        'geodir_top_content_add' => '',
        'geodir_before_main_content_add' => '',
        'geodir_full_page_class_filter' => 'section full-width-section',
        'geodir_before_widget_filter' => '',
        'geodir_after_widget_filter' => '',
        'geodir_before_title_filter' => '<div class="widget-title"><h2>',
        'geodir_after_title_filter' => '</h2></div>',
        'geodir_menu_li_class_filter' => '',
        'geodir_sub_menu_ul_class_filter' => '',
        'geodir_sub_menu_li_class_filter' => '',
        'geodir_menu_a_class_filter' => '',
        'geodir_sub_menu_a_class_filter' => '',
        'geodir_location_switcher_menu_li_class_filter' => '',
        'geodir_location_switcher_menu_a_class_filter' => '',
        'geodir_location_switcher_menu_sub_ul_class_filter' => '',
        'geodir_location_switcher_menu_sub_li_class_filter' => '',
        'geodir_theme_compat_css' => stripslashes('.full-width-section .geodir-search{margin:0;width:100%}.geodir_full_page .geodir-search{margin:0 auto;float:none}.geodir-search input[type=button],.geodir-search input[type=submit]{width:13%}.geodir-search input[type=text]{border:1px solid #ddd;border-radius:0;padding:0 8px}.geodir-category-list-in,.geodir-loc-bar-in{background:#f2f2f2;border-color:#dbdbdb}.geodir-category-list-in{margin-top:0}.geodir-cat-list .widget-title h2{margin:-13px -13px 13px}.widget .geodir-cat-list ul li.geodir-pcat-show a:before{display:none!important}.widget .geodir-cat-list ul li.geodir-pcat-show i{margin-right:5px}.container .geodir-search select{margin:0 3% 0 0;padding:8px 10px;width:13%}#geodir_carousel,#geodir_slider{border-radius:0;-webkit-border-radius:0;-moz-border-radius:0;margin-bottom:20px!important;border:1px solid #e1e1e1;box-shadow:none}#geodir_carousel{padding:10px}.geodir-tabs-content ol.commentlist{margin:40px 0;padding:0}li#post_mapTab{min-height:400px}#reviewsTab ol.commentlist li{border-bottom:none}#reviewsTab ol.commentlist li article.comment{border-bottom:1px solid #e1e1e1;padding-bottom:10px}.comment-content .rating{display:none}.comment-respond .gd_rating{margin-bottom:20px}div.geodir-rating{width:85px!important}.comment-respond .comment-notes{margin-bottom:10px}.average-review span,.comment-form label,.dtreviewed,.geodir-details-sidebar-user-links a,.geodir-viewall,.geodir_more_info span,.reviewer,dl.geodir-tab-head dd a{font-family:"Archivo Narrow",sans-serif}section.comment-content{margin:0 0 0 12%}#reviewsTab .comments-area .comment-content{width:auto}section.comment-content .description,section.comment-content p{margin:15px 0}dl.geodir-tab-head dd a{background:#f3f3f3;margin-top:-1px;font-size:14px;padding:0 15px}dl.geodir-tab-head dd.geodir-tab-active a{padding-bottom:1px}.geodir-widget .geodir_list_heading,.geodir-widget h3.widget-title{padding:0 15px;background:#e9e9e9;border:1px solid #dbdbdb;height:38px;line-height:38px;color:#2d2d2d}.geodir-widget .geodir_list_heading h3{background:0 0;border:none}.geodir-widget .geodir_list_heading{margin:-13px -14px 13px}.geodir-map-listing-page{border-width:1px 0 0;border-style:solid;border-color:#dbdbdb}.geodir-sidebar-wrap .geodir-company_info{margin:15px}.geodir-details-sidebar-social-sharing iframe{float:left}.geodir-details-sidebar-rating{overflow:hidden}.geodir-details-sidebar-rating .gd_rating_show,.geodir-details-sidebar-rating .geodir-rating{float:left;margin-right:15px}.geodir-details-sidebar-rating span.item{float:left;margin-top:5px}.geodir-details-sidebar-rating .average-review{top:-4px;position:relative}.geodir-details-sidebar-rating span.item img{margin-top:5px}.geodir_full_page{background:#fff;border:1px solid #e1e1e1;-webkit-box-shadow:0 1px 0 #e5e5e5;box-shadow:0 1px 0 #e5e5e5;padding:15px;margin-bottom:20px;clear:both}.geodir_map_container .main_list img{margin:0 5px}.geodir_category_list_view li.geodir-gridview .geodir-post-img .geodir_thumbnail{margin-bottom:10px}.geodir-addinfo .geodir-pinpoint,.geodir-addinfo a i{margin-right:5px}.geodir_category_list_view li.geodir-gridview h3{font-size:18px;margin-bottom:10px}#related_listingTab ul.geodir_category_list_view{padding:0!important}#reviewsTab #comments .gd_rating{margin-top:5px}.widget .geodir_category_list_view li .geodir-entry-content,.widget .geodir_category_list_view li a:before{display:none!important}.geodir_category_list_view li .geodir-entry-title{margin-bottom:10px}.widget ul.geodir_category_list_view{padding:15px}.sidebar .widget .geodir_category_list_view li{width:calc(100% - 25px)}.widget .geodir-loginbox-list li{overflow:visible!important}.widget ul.chosen-results{margin:0!important}.main_list_selecter{margin-right:5px}.geodir-viewall{float:right;width:auto!important}.widget-title h2{padding:0 15px;background:#e9e9e9;border:1px solid #dbdbdb;height:38px;line-height:38px}.widget:first-child .geodir_list_heading .widget-title{margin-top:0}.geodir_list_heading .widget-title{float:left;width:80%;margin-top:0}.geodir_list_heading .widget-title h2{padding:0 px;background:0 0;border:none;height:auto;line-height:auto}.chosen-default:before{content:none;display:none;position:absolute;margin-left:-1000000px;float:left}#geodir-wrapper .entry-crumbs{margin-bottom:20px}'),
        'geodir_theme_compat_js' => 'jQuery(document).ready(function(e){e(".geodir_full_page").length&&""===e.trim(e(".geodir_full_page").html())&&e(".geodir_full_page").css({display:"none"})});',
        'geodir_theme_compat_default_options' => '',
        'geodir_theme_compat_code' => 'Multi_News'
    );

    update_option('gd_theme_compats', $theme_compat);

    gd_set_theme_compat();// set the compat pack if avail
}


/**
 * Converts virtual pages to normal pages.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @global object $wpdb WordPress Database object.
 */
function gd_convert_virtual_pages(){
    global $wpdb;

    // Update the add listing page settings
    $add_listing_page = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT ID FROM " . $wpdb->posts . " WHERE post_name = %s AND post_status='virtual' LIMIT 1;",
            array('add-listing')
        )
    );

    if($add_listing_page){
        wp_update_post( array('ID' => $add_listing_page, 'post_status' => 'publish') );
        update_option( 'geodir_add_listing_page', $add_listing_page);
    }

    // Update the listing preview page settings
    $listing_preview_page = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT ID FROM " . $wpdb->posts . " WHERE post_name = %s AND post_status='virtual' LIMIT 1;",
            array('listing-preview')
        )
    );

    if($listing_preview_page){
        wp_update_post( array('ID' => $listing_preview_page, 'post_status' => 'publish') );
        update_option( 'geodir_preview_page', $listing_preview_page);
    }

    // Update the listing success page settings
    $listing_success_page = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT ID FROM " . $wpdb->posts . " WHERE post_name = %s AND post_status='virtual' LIMIT 1;",
            array('listing-success')
        )
    );

    if($listing_success_page){
        wp_update_post( array('ID' => $listing_success_page, 'post_status' => 'publish') );
        update_option( 'geodir_success_page', $listing_success_page);
    }

    // Update the listing success page settings
    $location_page = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT ID FROM " . $wpdb->posts . " WHERE post_name = %s AND post_status='virtual' LIMIT 1;",
            array('location')
        )
    );

    if($location_page){
        $location_slug = get_option('geodir_location_prefix');
        if(!$location_slug ){$location_slug  = 'location';}
        wp_update_post( array('ID' => $location_page, 'post_status' => 'publish','post_name' => $location_slug) );
        update_option( 'geodir_location_page', $location_page);
    }

}








