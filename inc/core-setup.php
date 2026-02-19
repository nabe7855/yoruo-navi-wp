<?php
/**
 * Core Setup for yoruo-navi
 * Registers Roles, Custom Post Types, and Taxonomies.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Custom Roles
 */
function yoruo_navi_register_roles() {
    // Store Owner Role
    add_role( 'store_owner', '店舗オーナー', array(
        'read'         => true,
        'edit_posts'   => true,
        'upload_files' => true,
        'publish_posts' => false, // Initially false, can be updated via custom logic if needed
    ) );

    // Job Seeker Role
    add_role( 'job_seeker', '求職者', array(
        'read'         => true,
    ) );
}
add_action( 'init', 'yoruo_navi_register_roles' );

/**
 * Register Custom Post Types
 */
function yoruo_navi_register_post_types() {
    // 1. Job CPT (Moved from functions.php)
    $job_labels = array(
        'name'                  => _x( '求人', 'Post Type General Name', 'yoruo-navi' ),
        'singular_name'         => _x( '求人', 'Post Type Singular Name', 'yoruo-navi' ),
        'menu_name'             => __( '求人管理', 'yoruo-navi' ),
        'all_items'             => __( '求人一覧', 'yoruo-navi' ),
        'add_new_item'          => __( '新規求人追加', 'yoruo-navi' ),
        'add_new'               => __( '新規追加', 'yoruo-navi' ),
        'edit_item'             => __( '求人を編集', 'yoruo-navi' ),
        'update_item'           => __( '求人を更新', 'yoruo-navi' ),
        'view_item'             => __( '求人を表示', 'yoruo-navi' ),
        'search_items'          => __( '求人を検索', 'yoruo-navi' ),
    );
    $job_args = array(
        'label'                 => __( '求人', 'yoruo-navi' ),
        'labels'                => $job_labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
        'taxonomies'            => array( 'job_category', 'job_area', 'job_tag' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-businessman',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
    );
    register_post_type( 'job', $job_args );

    // 2. Application CPT (New)
    $app_labels = array(
        'name'                  => _x( '応募', 'Post Type General Name', 'yoruo-navi' ),
        'singular_name'         => _x( '応募', 'Post Type Singular Name', 'yoruo-navi' ),
        'menu_name'             => __( '応募管理', 'yoruo-navi' ),
        'all_items'             => __( '応募一覧', 'yoruo-navi' ),
        'view_item'             => __( '応募を表示', 'yoruo-navi' ),
        'search_items'          => __( '応募を検索', 'yoruo-navi' ),
    );
    $app_args = array(
        'label'                 => __( '応募', 'yoruo-navi' ),
        'labels'                => $app_labels,
        'supports'              => array( 'title', 'editor', 'custom-fields' ),
        'hierarchical'          => false,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 6,
        'menu_icon'             => 'dashicons-email-alt',
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'show_in_rest'          => true,
    );
    // 3. Slider CPT (New)
    $slider_labels = array(
        'name'                  => _x( 'バナー', 'Post Type General Name', 'yoruo-navi' ),
        'singular_name'         => _x( 'バナー', 'Post Type Singular Name', 'yoruo-navi' ),
        'menu_name'             => __( 'トップバナー管理', 'yoruo-navi' ),
        'all_items'             => __( 'バナー一覧', 'yoruo-navi' ),
        'add_new_item'          => __( '新規バナー追加', 'yoruo-navi' ),
        'add_new'               => __( '新規追加', 'yoruo-navi' ),
        'edit_item'             => __( 'バナーを編集', 'yoruo-navi' ),
        'update_item'           => __( 'バナーを更新', 'yoruo-navi' ),
        'view_item'             => __( 'バナーを表示', 'yoruo-navi' ),
        'search_items'          => __( 'バナーを検索', 'yoruo-navi' ),
    );
    $slider_args = array(
        'label'                 => __( 'バナー', 'yoruo-navi' ),
        'labels'                => $slider_labels,
        'supports'              => array( 'title', 'thumbnail', 'page-attributes' ),
        'hierarchical'          => false,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 7,
        'menu_icon'             => 'dashicons-images-alt2',
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'show_in_rest'          => true,
    );
    register_post_type( 'slider', $slider_args );

    // 4. Quick Menu CPT (Icons in Hamburger Menu)
    $quick_menu_labels = array(
        'name'                  => 'アイコン管理',
        'singular_name'         => 'メニューアイコン',
        'menu_name'             => 'メニューアイコン管理',
    );
    $quick_menu_args = array(
        'label'                 => 'アイコン管理',
        'labels'                => $quick_menu_labels,
        'supports'              => array( 'title', 'page-attributes' ),
        'hierarchical'          => false,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 8,
        'menu_icon'             => 'dashicons-grid-view',
        'capability_type'       => 'post',
        'capabilities' => array(
            'edit_post'          => 'manage_options',
            'read_post'          => 'manage_options',
            'delete_post'        => 'manage_options',
            'edit_posts'         => 'manage_options',
            'edit_others_posts'  => 'manage_options',
            'publish_posts'      => 'manage_options',
            'read_private_posts' => 'manage_options',
            'create_posts'       => 'manage_options',
        ),
    );
    register_post_type( 'quick_menu', $quick_menu_args );

    // 5. Menu Banner CPT (Banners in Hamburger Menu)
    $menu_banner_labels = array(
        'name'                  => 'メニュー内バナー',
        'singular_name'         => 'メニューバナー',
        'menu_name'             => 'メニューバナー管理',
    );
    $menu_banner_args = array(
        'label'                 => 'メニュー内バナー',
        'labels'                => $menu_banner_labels,
        'supports'              => array( 'title', 'thumbnail' ),
        'hierarchical'          => false,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 9,
        'menu_icon'             => 'dashicons-format-image',
        'capability_type'       => 'post',
        'capabilities' => array(
            'edit_post'          => 'manage_options',
            'read_post'          => 'manage_options',
            'delete_post'        => 'manage_options',
            'edit_posts'         => 'manage_options',
            'edit_others_posts'  => 'manage_options',
            'publish_posts'      => 'manage_options',
            'read_private_posts' => 'manage_options',
            'create_posts'       => 'manage_options',
        ),
    );
    register_post_type( 'menu_banner', $menu_banner_args );
}
add_action( 'init', 'yoruo_navi_register_post_types', 0 );

/**
 * Register Taxonomies (Moved from functions.php)
 */
function yoruo_navi_register_taxonomies() {
    // Job Category
    register_taxonomy( 'job_category', array( 'job' ), array(
        'hierarchical'      => true,
        'labels'            => array( 'name' => '職種' ),
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
    ) );

    // Job Area (Area/Prefecture)
    register_taxonomy( 'job_area', array( 'job' ), array(
        'hierarchical'      => true,
        'labels'            => array( 'name' => 'エリア' ),
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
    ) );

    // Job Tags
    register_taxonomy( 'job_tag', array( 'job' ), array(
        'hierarchical'      => false,
        'labels'            => array( 'name' => '特徴・タグ' ),
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
    ) );
}
add_action( 'init', 'yoruo_navi_register_taxonomies', 0 );

/**
 * Hide Admin Bar for non-administrators
 */
function yoruo_navi_hide_admin_bar() {
    if (!current_user_can('administrator')) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'yoruo_navi_hide_admin_bar');

/**
 * Handle Matched Jobs Search (AJAX)
 */
function yoruo_navi_get_matched_jobs() {
    $pref = isset($_GET['pref']) ? sanitize_text_field($_GET['pref']) : (isset($_GET['area']) ? sanitize_text_field($_GET['area']) : '');
    $munis = isset($_GET['muni']) ? (array) $_GET['muni'] : array();
    
    $args = array(
        'post_type' => 'job',
        'posts_per_page' => 5,
        'post_status' => 'publish',
    );

    // Filter by Area (Prefecture or Municipality)
    $tax_query = array();

    if (!empty($munis)) {
        $tax_query[] = array(
            'taxonomy' => 'job_area',
            'field'    => 'name',
            'terms'    => array_map('sanitize_text_field', $munis),
            'operator' => 'IN',
        );
    } elseif ($pref) {
        $tax_query[] = array(
            'taxonomy' => 'job_area',
            'field'    => 'name',
            'terms'    => $pref,
        );
    }

    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }

    $query = new WP_Query($args);
    $results = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $job_id = get_the_ID();
            
            $salary_min = get_post_meta($job_id, '_salary_min', true);
            $salary_type = get_post_meta($job_id, '_salary_type', true);
            $salary_label = ($salary_type === 'hourly' ? '時給' : ($salary_type === 'daily' ? '日給' : '月給'));

            $results[] = array(
                'id' => $job_id,
                'title' => get_the_title(),
                'link' => get_permalink(),
                'thumbnail' => get_the_post_thumbnail_url($job_id, 'medium') ?: 'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?auto=format&fit=crop&q=80&w=400',
                'category' => wp_get_post_terms($job_id, 'job_category', array('fields' => 'names'))[0] ?? '求人',
                'area' => wp_get_post_terms($job_id, 'job_area', array('fields' => 'names'))[0] ?? 'エリア未定',
                'salary' => $salary_label . ' ' . number_format($salary_min) . '円〜',
            );
        }
    }

    wp_reset_postdata();
    wp_send_json($results);
}
add_action('wp_ajax_get_matched_jobs', 'yoruo_navi_get_matched_jobs');
add_action('wp_ajax_nopriv_get_matched_jobs', 'yoruo_navi_get_matched_jobs');


