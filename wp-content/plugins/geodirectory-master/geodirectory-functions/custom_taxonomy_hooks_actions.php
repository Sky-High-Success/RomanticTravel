<?php
/**
 * Geodirectory custom post types/taxonomies related functions.
 *
 * @since 1.0.0
 * @package GeoDirectory
 */

/**
 * Register the texonomies.
 *
 * @since 1.0.0
 */
function geodir_register_taxonomies()
{
    $taxonomies = array();
    $taxonomies = get_option('geodir_taxonomies');
    // If custom taxonomies are present, register them
    if (is_array($taxonomies)) {
        // Sort taxonomies
        ksort($taxonomies);

        // Register taxonomies
        foreach ($taxonomies as $taxonomy => $args) {
            // Allow taxonomie names to be translated
            if (!empty($args['args']['labels'])) {
                foreach ($args['args']['labels'] as $key => $tax_label) {
                    $args['args']['labels'][$key] = __($tax_label, GEODIRECTORY_TEXTDOMAIN);
                }
            }

            $tax = register_taxonomy($taxonomy, $args['object_type'], $args['args']);

            if (taxonomy_exists($taxonomy)) {
                $tax = register_taxonomy_for_object_type($taxonomy, $args['object_type']);
            }
        }
    }
}


/**
 * Get available custom posttypes and taxonomies and register them.
 */
_x('places', 'URL slug', GEODIRECTORY_TEXTDOMAIN);

/**
 * Register the post types.
 *
 * @since 1.0.0
 *
 * @global array $wp_post_types List of post types.
 */
function geodir_register_post_types() 
{
    global $wp_post_types;

    $post_types = array();
    $post_types = get_option('geodir_post_types');

    // Register each post type if array of data is returned
    if (is_array($post_types)):

        foreach ($post_types as $post_type => $args):
            if ($post_type == 'gd_place' && get_option('geodir_disable_place_tax')) {
                continue;
            }
            if (!empty($args['rewrite']['slug'])) {
                $args['rewrite']['slug'] = _x($args['rewrite']['slug'], 'URL slug', GEODIRECTORY_TEXTDOMAIN);
            }
            $args = stripslashes_deep($args);

            if (!empty($args['labels'])) {
                foreach ($args['labels'] as $key => $val) {
                    $args['labels'][$key] = __($val, GEODIRECTORY_TEXTDOMAIN);// allow translation
                }
            }

            /**
             * Filter post type args.
             *
             * @since 1.0.0
             * @param string $args Post type args.
             * @param string $post_type The post type.
             */
            $args = apply_filters('geodir_post_type_args', $args, $post_type);

            $post_type = register_post_type($post_type, $args);

        endforeach;
    endif;
}

/**
 * Filters arguments array for post type.
 *
 * @since 1.0.0
 *
 * @param  array $args Array or string of arguments for registering a post type.
 * @param  string $post_type Post type name
 * @return array Array or string of arguments.
 */
function geodir_post_type_args_modify($args, $post_type)
{
    $geodir_location_prefix = isset($_REQUEST['geodir_location_prefix']) ? trim($_REQUEST['geodir_location_prefix']) : get_option('geodir_location_prefix');
	if (isset($_REQUEST['geodir_listing_prefix']) && $_REQUEST['geodir_listing_prefix'] != '' && strtolower($_REQUEST['geodir_listing_prefix']) != strtolower($geodir_location_prefix)) {

        $listing_slug = htmlentities(trim($_REQUEST['geodir_listing_prefix']));

        if ($post_type == 'gd_place') {
            if (array_key_exists('has_archive', $args))
                $args['has_archive'] = $listing_slug;

            if (array_key_exists('rewrite', $args)) {
                if (array_key_exists('slug', $args['rewrite']))
                    $args['rewrite']['slug'] = $listing_slug . '/%gd_taxonomy%';
            }

            $geodir_post_types = get_option('geodir_post_types');

            if (array_key_exists($post_type, $geodir_post_types)) {

                if (array_key_exists('has_archive', $geodir_post_types[$post_type]))
                    $geodir_post_types[$post_type]['has_archive'] = $listing_slug;

                if (array_key_exists('rewrite', $geodir_post_types[$post_type]))
                    if (array_key_exists('slug', $geodir_post_types[$post_type]['rewrite']))
                        $geodir_post_types[$post_type]['rewrite']['slug'] = $listing_slug . '/%gd_taxonomy%';

                update_option('geodir_post_types', $geodir_post_types);

            }

            $geodir_post_types = get_option('geodir_post_types');

            /* --- update taxonomies (category) --- */

            $geodir_taxonomies = get_option('geodir_taxonomies');

            if (array_key_exists('listing_slug', $geodir_taxonomies[$post_type . 'category'])) {
                $geodir_taxonomies[$post_type . 'category']['listing_slug'] = $listing_slug;

                if (array_key_exists('args', $geodir_taxonomies[$post_type . 'category']))
                    if (array_key_exists('rewrite', $geodir_taxonomies[$post_type . 'category']['args']))
                        if (array_key_exists('slug', $geodir_taxonomies[$post_type . 'category']['args']['rewrite']))
                            $geodir_taxonomies[$post_type . 'category']['args']['rewrite']['slug'] = $listing_slug;

                update_option('geodir_taxonomies', $geodir_taxonomies);

            }

            /* --- update taxonomies (tags) --- */
            $geodir_taxonomies_tag = get_option('geodir_taxonomies');
            if (array_key_exists('listing_slug', $geodir_taxonomies_tag[$post_type . '_tags'])) {
                $geodir_taxonomies_tag[$post_type . '_tags']['listing_slug'] = $listing_slug . '/tags';

                if (array_key_exists('args', $geodir_taxonomies_tag[$post_type . '_tags']))
                    if (array_key_exists('rewrite', $geodir_taxonomies_tag[$post_type . '_tags']['args']))
                        if (array_key_exists('slug', $geodir_taxonomies_tag[$post_type . '_tags']['args']['rewrite']))
                            $geodir_taxonomies_tag[$post_type . '_tags']['args']['rewrite']['slug'] = $listing_slug . '/tags';

                update_option('geodir_taxonomies', $geodir_taxonomies_tag);

            }

        }

    }

    return $args;
}

/**
 * Remove rewrite rules and then recreate rewrite rules.
 *
 * @see WP_Rewrite::flush_rules()
 * @since 1.0.0
 *
 * @global WP_Rewrite $wp_rewrite Used for default feeds.
 */
function geodir_flush_rewrite_rules()
{
    global $wp_rewrite;
    $wp_rewrite->flush_rules(false);
}

/**
 * Get the full set of generated rewrite rules.
 *
 * @since 1.0.0
 *
 * @global object $wpdb WordPress Database object.
 * @param  array $rules The compiled array of rewrite rules.
 * @return array Rewrite rules.
 */
function geodir_listing_rewrite_rules($rules)
{
    $newrules = array();
    $taxonomies = array();
    $taxonomies = get_option('geodir_taxonomies');
    $detail_url_seprator = get_option('geodir_detailurl_separator');
    //create rules for post listing
    if (is_array($taxonomies)):
        foreach ($taxonomies as $taxonomy => $args):

            $post_type = $args['object_type'];
            $listing_slug = $args['listing_slug'];

            if (strpos($taxonomy, 'tags')) {
                $newrules[$listing_slug . '/(.+?)/page/?([0-9]{1,})/?$'] = 'index.php?' . $taxonomy . '=$matches[1]&paged=$matches[2]';
                $newrules[$listing_slug . '/(.+?)/?$'] = 'index.php?' . $taxonomy . '=$matches[1]';

            }

            /*	$newrules[$listing_slug.'/'.$detail_url_seprator.'/([^/]+)/?$'] = 'index.php?'.$post_type.'=$matches[1]';
                $newrules[$listing_slug.'/(.+?)/'.$detail_url_seprator.'/([^/]+)/?$'] = 'index.php?'.$taxonomy.'=$matches[1]&'.$post_type.'=$matches[2]';
                */


        endforeach;
    endif;

    //create rules for location listing
    $location_page = get_option('geodir_location_page');
    //$location_prefix = get_option('geodir_location_prefix');
    if($location_page) {
        global $wpdb;
        $location_prefix = $wpdb->get_var($wpdb->prepare("SELECT post_name FROM $wpdb->posts WHERE post_type='page' AND ID=%d", $location_page));
    }
    if ($location_prefix == '')
        $location_prefix = 'location';


    $newrules[$location_prefix . '/([^/]+)/([^/]+)/([^/]+)/?$'] = 'index.php?page_id=' . $location_page . '&gd_country=$matches[1]&gd_region=$matches[2]&gd_city=$matches[3]';
    $newrules[$location_prefix . '/([^/]+)/([^/]+)/?$'] = 'index.php?page_id=' . $location_page . '&gd_country=$matches[1]&gd_region=$matches[2]';
    $newrules[$location_prefix . '/([^/]+)/?$'] = 'index.php?page_id=' . $location_page . '&gd_country=$matches[1]';

    if ($location_page && function_exists('icl_object_id')) {

        foreach(icl_get_languages('skip_missing=N') as $lang){
            $alt_page_id = '';
            $alt_page_id = icl_object_id($location_page, 'page', false,$lang['language_code']);
            if($alt_page_id){
                $location_prefix = $wpdb->get_var($wpdb->prepare("SELECT post_name FROM $wpdb->posts WHERE post_type='page' AND ID=%d", $alt_page_id));

                $newrules[$location_prefix . '/([^/]+)/([^/]+)/([^/]+)/?$'] = 'index.php?page_id=' . $alt_page_id . '&gd_country=$matches[1]&gd_region=$matches[2]&gd_city=$matches[3]';
                $newrules[$location_prefix . '/([^/]+)/([^/]+)/?$'] = 'index.php?page_id=' . $alt_page_id . '&gd_country=$matches[1]&gd_region=$matches[2]';
                $newrules[$location_prefix . '/([^/]+)/?$'] = 'index.php?page_id=' . $alt_page_id . '&gd_country=$matches[1]';

            }
        }

    }

    $newrules[$location_prefix . '/?$'] = 'index.php?page_id=' . $location_page;


    $rules = array_merge($newrules, $rules);
    return $rules;
}

/**
 * Get the list of rewrite rules formatted for output to an .htaccess file.
 *
 * @since 1.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $rules mod_rewrite Rewrite rules formatted for .htaccess.
 * @return array Rewrite rules.
 */
function geodir_htaccess_contents($rules)
{
    global $wpdb;
    $location_prefix = get_option('geodir_location_prefix');
    // if location page slug changed then add redirect
    if ($location_prefix == 'location') {
        return $rules;
    }
    $my_content = <<<EOD
\n# BEGIN GeoDirectory Rules
Redirect 301 /location/ /$location_prefix/
# END GeoDirectory Rules\n\n
EOD;
    return $my_content . $rules;
}
add_filter('mod_rewrite_rules', 'geodir_htaccess_contents');

/**
 * Add the location variables to the query variables.
 *
 * @since 1.0.0
 *
 * @param array $public_query_vars The array of query variables.
 * @return array Query variables.
 */
function geodir_add_location_var($public_query_vars)
{
    $public_query_vars[] = 'gd_country';
    $public_query_vars[] = 'gd_region';
    $public_query_vars[] = 'gd_city';
    return $public_query_vars;
}

/**
 * Add the variable to the query variables to indentify geodir page.
 *
 * @since 1.0.0
 *
 * @param array $public_query_vars The array of query variables.
 * @return array Query variables.
 */
function geodir_add_geodir_page_var($public_query_vars)
{
    $public_query_vars[] = 'gd_is_geodir_page';
    return $public_query_vars;
}

/**
 * Add the page id to the query variables.
 *
 * @since 1.0.0
 * @global object $wp_query WordPress Query object.
 * @return array WordPress Query object.
 */
function geodir_add_page_id_in_query_var()
{
    global $wp_query;

    $page_id = $wp_query->get_queried_object_id();

    if (!get_query_var('page_id')) {
        // fix for WP tags conflict with enfold theme
        $theme_name = strtolower(wp_get_theme());
        if (!geodir_is_geodir_page() && strpos($theme_name, 'enfold') !== false) {
            return $wp_query;
        }
        $wp_query->set('page_id', $page_id);
    }

    return $wp_query;
}

/**
 * Add the location variables in session.
 *
 * @since 1.0.0
 *
 * @param object $wp The WordPress object.
 */
function geodir_set_location_var_in_session_in_core($wp)
{
	//$wp->geodir_query_vars = $wp->query_vars ;
	// this code will determine when a user wants to switch location 
	
	// Query Vars will have page_id parameter
	// check if query var has page_id and that page id is location page
    geodir_set_is_geodir_page($wp);
    if (!get_option('geodir_set_as_home')) {

        if (empty($wp->query_vars) || !array_diff(array_keys($wp->query_vars), array('preview', 'page', 'paged', 'cpage'))) {
            if ('page' == get_option('show_on_front'))
                $wp->query_vars['page_id'] = get_option('page_on_front');
        }

    }


    if (isset($wp->query_vars['page_id']) && $wp->query_vars['page_id'] == geodir_location_page_id()) {
        $gd_country = '';
        $gd_region = '';
        $gd_city = '';
        if (isset($wp->query_vars['gd_country']) && $wp->query_vars['gd_country'] != '')
            $gd_country = urldecode($wp->query_vars['gd_country']);

        if (isset($wp->query_vars['gd_region']) && $wp->query_vars['gd_region'] != '')
            $gd_region = urldecode($wp->query_vars['gd_region']);

        if (isset($wp->query_vars['gd_city']) && $wp->query_vars['gd_city'] != '')
            $gd_city = urldecode($wp->query_vars['gd_city']);

        if (!($gd_country == '' && $gd_region == '' && $gd_city == '')) {
            $default_location = geodir_get_default_location();

            if (get_option('geodir_add_location_url')) {
                if (get_option('geodir_show_location_url') != 'all') {
                    if ($gd_region == '') {
                        if (isset($_SESSION['gd_region']))
                            $gd_region = $_SESSION['gd_region'];
                        else
                            $gd_region = $default_location->region_slug;
                    }

                    if ($gd_city == '') {
                        if (isset($_SESSION['gd_city']))
                            $gd_city = $_SESSION['gd_city'];
                        else
                            $gd_city = $default_location->city_slug;

                        $base_location_link = geodir_get_location_link('base');
                        wp_redirect($base_location_link . '/' . $gd_country . '/' . $gd_region . '/' . $gd_city);
                        exit();
                    }

                }
            }

            $args = array(
                'what' => 'city',
                'city_val' => $gd_city,
                'region_val' => $gd_region,
                'country_val' => $gd_country,
                'country_column_name' => 'country_slug',
                'region_column_name' => 'region_slug',
                'city_column_name' => 'city_slug',
                'location_link_part' => false,
                'compare_operator' => ''
            );

            $location_array = function_exists('geodir_get_location_array') ? geodir_get_location_array($args) : array();
            if (!empty($location_array)) {
                $_SESSION['gd_multi_location'] = 1;
                $_SESSION['gd_country'] = $gd_country;
                $_SESSION['gd_region'] = $gd_region;
                $_SESSION['gd_city'] = $gd_city;
                $wp->query_vars['gd_country'] = $gd_country;
                $wp->query_vars['gd_region'] = $gd_region;
                $wp->query_vars['gd_city'] = $gd_city;
            } else {

                unset($_SESSION['gd_multi_location'],
                    $_SESSION['gd_city'],
                    $_SESSION['gd_region'],
                    $_SESSION['gd_country']);
            }

        } else {
            unset($_SESSION['gd_multi_location'],
                $_SESSION['gd_city'],
                $_SESSION['gd_region'],
                $_SESSION['gd_country']);
        }

    } else if (isset($wp->query_vars['post_type']) && $wp->query_vars['post_type'] != '') {

        if (!is_admin()) {
            $requested_post_type = $wp->query_vars['post_type'];
            // check if this post type is geodirectory post types
            $post_type_array = geodir_get_posttypes();
            if (in_array($requested_post_type, $post_type_array)) {
                // now u can apply geodirectory related manipulation.
            }
        }
    } else {

        // check if a geodirectory taxonomy is set
        $gd_country = '';
        $gd_region = '';
        $gd_city = '';
        $is_geodir_taxonomy = false;
        $is_geodir_taxonomy_term = false; // the last term is real geodirectory taxonomy term or not
        $is_geodir_location_found = false;
        $geodir_taxonomy = '';
        $geodir_post_type = '';
        $geodir_term = '';
        $geodir_set_location_session = true;
        $geodir_taxonomis = geodir_get_taxonomies('', true);
        foreach ($geodir_taxonomis as $taxonomy) {
            if (array_key_exists($taxonomy, $wp->query_vars)) {
                $is_geodir_taxonomy = true;
                $geodir_taxonomy = $taxonomy;
                $geodir_post_type = str_replace('category', '', $taxonomy);
                $geodir_post_type = str_replace('_tags', '', $geodir_post_type);
                $geodir_term = $wp->query_vars[$geodir_taxonomy];

                break;
            }
        }
        // now get an array of all terms seperated by '/'
        $geodir_terms = explode('/', $geodir_term);
        $geodir_last_term = end($geodir_terms);

        if ($is_geodir_taxonomy) // do all these only when it is a geodirectory taxonomy
        {

            $wp->query_vars['post_type'] = $geodir_post_type;

                // now check if last term is a post of geodirectory post types
                $geodir_post = get_posts(array(
                    'name' => $geodir_last_term,
                    'posts_per_page' => 1,
                    'post_type' => $geodir_post_type,


                ));

                if (empty($geodir_post)) {
                    $geodir_post = get_posts(array(
                        'name' => $geodir_last_term,
                        'posts_per_page' => 1,
                        'post_type' => $geodir_post_type,
                        'post_status' => 'draft',
                        'suppress_filters' => false,

                    ));
                }

                if (!empty($geodir_post)) {

                    if ($geodir_post[0]->post_status != 'publish') {
                        foreach ($wp->query_vars as $key => $vars) {
                            unset($wp->query_vars[$key]);
                        }
                        $wp->query_vars['error'] = '404';
                        // set it as 404 if post exists but its status is not published yet

                    } else {
                        //$wp->query_vars[$geodir_taxonomy] = str_replace( '/'.$geodir_last_term , ''  , $geodir_term);
                        $wp->query_vars[$geodir_post_type] = $geodir_last_term;
                        $wp->query_vars['name'] = $geodir_last_term;

                    }


                    $geodir_term = str_replace('/' . $geodir_last_term, '', $geodir_term, $post_title_replace_count);
                    if (!$post_title_replace_count)
                        $geodir_term = str_replace($geodir_last_term, '', $geodir_term, $post_title_replace_count);
                    $geodir_terms = explode('/', $geodir_term);
                    $geodir_last_term = end($geodir_terms);

                    $geodir_set_location_session = false;
                    //return ;
                }

            $geodir_location_terms = '';
            // if last term is not a post then check if last term is a term of the specific texonomy or not
            if (geodir_term_exists($geodir_last_term, $geodir_taxonomy)) {
                $is_geodir_taxonomy_term = true;

                $geodir_set_location_session = false;
            }


            // now check if there is location parts in the url or not
            if (get_option('geodir_add_location_url')) {
                $default_location = geodir_get_default_location();
                if (get_option('geodir_show_location_url') == 'all') {
                    if (count($geodir_terms) >= 3) {
                        $gd_country = urldecode($geodir_terms[0]);
                        $gd_region = urldecode($geodir_terms[1]);
                        $gd_city = urldecode($geodir_terms[2]);
                    } else if (count($geodir_terms) >= 2) {
                        $gd_country = urldecode($geodir_terms[0]);
                        $gd_region = urldecode($geodir_terms[1]);
                    } else if (count($geodir_terms) >= 1) {
                        $gd_country = urldecode($geodir_terms[0]);
                    }

                    if (strtolower($default_location->country_slug) == strtolower($gd_country) &&
                        strtolower($default_location->region_slug) == strtolower($gd_region) &&
                        strtolower($default_location->city_slug) == strtolower($gd_city)
                    )
                        $is_geodir_location_found = true;

                    // if location has not been found for country , region and city then search for country and region only

                    if (!$is_geodir_location_found) {
                        $gd_city = '';
                        if (strtolower($default_location->country_slug) == strtolower($gd_country) &&
                            strtolower($default_location->region_slug) == strtolower($gd_region)
                        )
                            $is_geodir_location_found = true;

                    }

                    // if location has not been found for country , region  then search for country only
                    if (!$is_geodir_location_found) {
                        $gd_city = '';
                        $gd_region = '';
                        if (strtolower($default_location->country_slug) == strtolower($gd_country))
                            $is_geodir_location_found = true;
                    }
                } else {
                    $gd_city = $geodir_terms[0];

                    if (strtolower($default_location->city_slug) == strtolower($gd_city)) {
                        $is_geodir_location_found = true;
                        $gd_region = $default_location->region_slug;
                        $gd_country = $default_location->country_slug;
                    }


                }
                // if locaton still not found then clear location related session variables
                if ($is_geodir_location_found && $geodir_set_location_session) {
                    $_SESSION['gd_multi_location'] = 1;
                    $_SESSION['gd_country'] = $gd_country;
                    $_SESSION['gd_region'] = $gd_region;
                    $_SESSION['gd_city'] = $gd_city;
                }

                if (get_option('geodir_show_location_url') != 'all') {
                    $gd_country = '';
                    $gd_region = '';
                }

                if ($is_geodir_location_found) {
                    $wp->query_vars['gd_country'] = $gd_country;
                    $wp->query_vars['gd_region'] = $gd_region;
                    $wp->query_vars['gd_city'] = $gd_city;
                } else {
                    $gd_country = '';
                    $gd_region = '';
                    $gd_city = '';
                }
            }

            $wp->query_vars[$geodir_taxonomy] = $geodir_term;
            // eliminate location related terms from taxonomy term
            if ($gd_country != '')
                $wp->query_vars[$geodir_taxonomy] = preg_replace('/' . urlencode($gd_country) . '/', '', $wp->query_vars[$geodir_taxonomy], 1);

            if ($gd_region != '')
                $wp->query_vars[$geodir_taxonomy] = preg_replace('/' . urlencode($gd_region) . '/', '', $wp->query_vars[$geodir_taxonomy], 1);

            if ($gd_city != '')
                $wp->query_vars[$geodir_taxonomy] = preg_replace('/' . urlencode($gd_city) . '/', '', $wp->query_vars[$geodir_taxonomy], 1);


            $wp->query_vars[$geodir_taxonomy] = str_replace('///', '', $wp->query_vars[$geodir_taxonomy]);
            $wp->query_vars[$geodir_taxonomy] = str_replace('//', '', $wp->query_vars[$geodir_taxonomy]);

            $wp->query_vars[$geodir_taxonomy] = trim($wp->query_vars[$geodir_taxonomy], '/');

            if ($wp->query_vars[$geodir_taxonomy] == '') {
                unset($wp->query_vars[$geodir_taxonomy]);
            } else {
                if (!$is_geodir_taxonomy_term) {
                    foreach ($wp->query_vars as $key => $vars) {
                        unset($wp->query_vars[$key]);
                    }
                    $wp->query_vars['error'] = '404';
                }
            }
        }
    }

    if (isset($_SESSION['gd_multi_location']) && $_SESSION['gd_multi_location'] == 1) {
        $wp->query_vars['gd_country'] = $_SESSION['gd_country'];
        $wp->query_vars['gd_region'] = $_SESSION['gd_region'];
        $wp->query_vars['gd_city'] = $_SESSION['gd_city'];
    }

    // now check if there is location parts in the url or not
    if (get_option('geodir_add_location_url')) {
        if (get_option('geodir_show_location_url') != 'all') {
            if (isset($wp->query_vars['gd_country']))
                $wp->query_vars['gd_country'] = '';

            if (isset($wp->query_vars['gd_region']))
                $wp->query_vars['gd_region'] = '';
        }
    } else {
        if (isset($wp->query_vars['gd_country']))
            $wp->query_vars['gd_country'] = '';

        if (isset($wp->query_vars['gd_region']))
            $wp->query_vars['gd_region'] = '';

        if (isset($wp->query_vars['gd_city']))
            $wp->query_vars['gd_city'] = '';
    }
}

/**
 * Ragister a custom post status.
 *
 * This will add a new post status in the system called: Virtual.
 *
 * @since 1.0.0
 *
 * @param object $wp The WordPress object.
 */
function geodir_custom_post_status()
{
    // Vertual Page Status
    register_post_status('virtual', array(
        'label' => _x('Virtual', 'page', GEODIRECTORY_TEXTDOMAIN),
        'public' => true,
        'exclude_from_search' => true,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop('Virtual <span class="count">(%s)</span>', 'Virtual <span class="count">(%s)</span>', GEODIRECTORY_TEXTDOMAIN),
    ));

    /**
     * Called after we register the custom post status 'Virtual'.
     *
     * Can be use to add more post statuses.
     *
     * @since 1.0.0
     */
    do_action('geodir_custom_post_status');
}

/**
 * Retrieve the term link.
 *
 * @since 1.0.0
 *
 * @param string $termlink Term link URL.
 * @param object $term Term object.
 * @param string $taxonomy Taxonomy slug.
 * @return string The term link
 */
function geodir_get_term_link($termlink, $term, $taxonomy)
{
    return geodir_term_link($termlink, $term, $taxonomy); // taxonomy_functions.php
}

/**
 * Retrieve the post type archive permalink.
 *
 * @since 1.0.0
 *
 * @param string $link The post type archive permalink.
 * @param string $post_type Post type name.
 * @return string The post type archive permalink.
 */
function geodir_get_posttype_link($link, $post_type)
{
    return geodir_posttype_link($link, $post_type); // taxonomy_functions.php
}

/**
 * Retrieve the array of pages to exclude from the pages list.
 *
 * This will exclude pages from the pages list which has post_status virtual.
 *
 * @since 1.0.0
 *
 * @param array $exclude_array An array of page IDs to exclude.
 * @return array $An array of page IDs.
 */
function exclude_from_wp_list_pages($exclude_array)
{
    $pages_ids = array();
    $pages_array = get_posts(array('post_type' => 'page', 'post_status' => 'virtual'));
    foreach ($pages_array as $page) {
        $pages_ids[] = $page->ID;
    }
    $exclude_array = $exclude_array + $pages_ids;
    return $exclude_array;
}

/**
 * Exclude the virtual pages from the admin list.
 *
 * @since 1.0.0
 *
 * @param WP_Query $query The WP_Query instance.
 * @return WP_Query instance.
 */
function geodir_exclude_page($query)
{
    add_filter('posts_where', 'geodir_exclude_page_where', 100);
    return $query;
}

/**
 * Exclude the virtual pages from page list.
 *
 * @since 1.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $where The SQL query where clause.
 * @return string Query where clause.
 */
function geodir_exclude_page_where($where)
{
    global $wpdb;
    if (is_admin())
        $where .= " AND $wpdb->posts.post_status != 'virtual'";

    return $where;
}
