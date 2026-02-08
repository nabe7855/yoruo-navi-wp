<?php
/**
 * yoruo-navi functions and definitions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function yoruo_navi_setup() {
    // Add default posts and comments RSS feed links to head.
    add_theme_support( 'automatic-feed-links' );

    // Let WordPress manage the document title.
    add_theme_support( 'title-tag' );

    // Enable support for Post Thumbnails on posts and pages.
    add_theme_support( 'post-thumbnails' );

    // Register Navigation Menu
    register_nav_menus( array(
        'primary' => esc_html__( 'Primary Menu', 'yoruo-navi' ),
        'footer'  => esc_html__( 'Footer Menu', 'yoruo-navi' ),
    ) );

    // Switch default core markup for search form, comment form, and comments to output valid HTML5.
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ) );
}
add_action( 'after_setup_theme', 'yoruo_navi_setup' );

// Include Theme Data
require_once get_template_directory() . '/theme-data.php';

/**
 * Enqueue scripts and styles.
 */
function yoruo_navi_scripts() {
    // Tailwind CDN (Script)
    wp_enqueue_script( 'yoruo-navi-tailwind', 'https://cdn.tailwindcss.com', array(), '3.4.1', false );
    
    // Google Fonts
    wp_enqueue_style( 'yoruo-navi-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=Noto+Sans+JP:wght@400;700;900&display=swap', array(), null );

    // Font Awesome
    wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css', array(), '6.0.0' );

    // Theme main stylesheet
    wp_enqueue_style( 'yoruo-navi-style', get_stylesheet_uri(), array(), '1.0.0' );

    // Global JS
    wp_enqueue_script( 'yoruo-navi-main', get_template_directory_uri() . '/js/main.js', array(), '1.0.0', true );
    wp_enqueue_script( 'yoruo-navi-map-data', get_template_directory_uri() . '/js/map-data.js', array(), '1.0.0', true );
    wp_enqueue_script( 'yoruo-navi-map', get_template_directory_uri() . '/js/japan-map.js', array( 'yoruo-navi-map-data' ), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'yoruo_navi_scripts' );

/**
 * Configure Tailwind (Inline script)
 */
function yoruo_navi_tailwind_config() {
    ?>
    <script>
        window.tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gold: {
                            start: '#fbbf24',
                            end: '#d97706',
                        },
                        cyan: {
                            50: '#ecfeff',
                            100: '#cffafe',
                            200: '#a5f3fc',
                            300: '#67e8f9',
                            400: '#22d3ee',
                            500: '#06b6d4',
                            600: '#0891b2',
                            700: '#0e7490',
                            800: '#155e75',
                            900: '#164e63',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'Noto Sans JP', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.8s ease-out forwards',
                        'map-entrance': 'mapFluffyEntrance 1.2s cubic-bezier(0.22, 1, 0.36, 1) forwards',
                        'label-in': 'labelSlideIn 1s cubic-bezier(0.22, 1, 0.36, 1) forwards',
                        'nurutto': 'nuruttoFadeIn 1s cubic-bezier(0.22, 1, 0.36, 1) forwards',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        mapFluffyEntrance: {
                            '0%': { opacity: '0', transform: 'scale(0.9) translateY(20px)', filter: 'blur(10px)' },
                            '100%': { opacity: '1', transform: 'scale(1) translateY(0)', filter: 'blur(0)' },
                        },
                        labelSlideIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        nuruttoFadeIn: {
                            '0%': { opacity: '0', transform: 'scale(0.98) translateY(10px)' },
                            '100%': { opacity: '1', transform: 'scale(1) translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer base {
            body {
                @apply bg-slate-50 text-slate-900 font-sans antialiased;
            }
        }
        @layer utilities {
            .gradient-gold {
                background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #d97706 100%);
            }
            .gradient-cyan {
                background: linear-gradient(135deg, #06b6d4 0%, #22d3ee 50%, #3b82f6 100%);
            }
            .no-scrollbar::-webkit-scrollbar {
                display: none;
            }
            .no-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
            .text-glow-amber {
                text-shadow: 0 0 10px rgba(251, 191, 36, 0.5);
            }
        }
    </style>
    <?php
}
add_action( 'wp_head', 'yoruo_navi_tailwind_config' );

/**
 * Register Custom Post Type: Job
 */
function yoruo_navi_register_job_cpt() {
    $labels = array(
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
    $args = array(
        'label'                 => __( '求人', 'yoruo-navi' ),
        'labels'                => $labels,
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
        'capability_type'       => 'page',
        'show_in_rest'          => true,
    );
    register_post_type( 'job', $args );
}
add_action( 'init', 'yoruo_navi_register_job_cpt', 0 );

/**
 * Register Taxonomies
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
