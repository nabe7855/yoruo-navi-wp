<?php
/**
 * Admin Customizations for yoruo-navi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Account Status column to User List
 */
function yoruo_navi_add_user_status_column($columns) {
    if (!current_user_can('administrator')) return $columns;
    $columns['account_status'] = '店舗ステータス';
    return $columns;
}
add_filter('manage_users_columns', 'yoruo_navi_add_user_status_column');

/**
 * Display User Status in Column
 */
function yoruo_navi_show_user_status_column_content($value, $column_name, $user_id) {
    if ($column_name !== 'account_status') return $value;

    $user = get_userdata($user_id);
    if (!in_array('store_owner', $user->roles)) return '-';

    $status = get_user_meta($user_id, '_account_status', true);
    $output = '';

    switch ($status) {
        case 'approved':
            $output = '<span style="color: #2e7d32; font-weight: bold;">承認済</span>';
            break;
        case 'pending':
            $output = '<span style="color: #ed6c02; font-weight: bold;">承認待ち</span>';
            $output .= '<br><a href="' . wp_nonce_url(admin_url('users.php?action=approve_store&user=' . $user_id), 'approve_store_' . $user_id) . '" style="color: #1976d2;">[承認する]</a>';
            break;
        case 'rejected':
            $output = '<span style="color: #d32f2f; font-weight: bold;">拒否済</span>';
            break;
        default:
            $output = '未設定';
    }

    return $output;
}
add_filter('manage_users_custom_column', 'yoruo_navi_show_user_status_column_content', 10, 3);

/**
 * Handle Approve Action
 */
function yoruo_navi_handle_user_actions() {
    if (isset($_GET['action']) && $_GET['action'] === 'approve_store' && isset($_GET['user']) && isset($_GET['_wpnonce'])) {
        $user_id = intval($_GET['user']);
        if (wp_verify_nonce($_GET['_wpnonce'], 'approve_store_' . $user_id)) {
            update_user_meta($user_id, '_account_status', 'approved');
            wp_redirect(admin_url('users.php?approved=1'));
            exit;
        }
    }
}
add_action('admin_init', 'yoruo_navi_handle_user_actions');

/**
 * Add Job Columns
 */
function yoruo_navi_add_job_columns($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['store_name'] = '投稿店舗';
        }
    }
    return $new_columns;
}
add_filter('manage_job_posts_columns', 'yoruo_navi_add_job_columns');

function yoruo_navi_show_job_columns_content($column, $post_id) {
    if ($column === 'store_name') {
        $author_id = get_post_field('post_author', $post_id);
        $store_name = get_user_meta($author_id, '_store_name', true);
        echo esc_html($store_name ? $store_name : '不明');
    }
}
add_action('manage_job_posts_custom_column', 'yoruo_navi_show_job_columns_content', 10, 2);
/**
 * Add Site Settings Page
 */
function yoruo_navi_add_site_settings_menu() {
    add_menu_page(
        'サイト設定',
        'サイト設定',
        'manage_options',
        'yoruo-navi-settings',
        'yoruo_navi_site_settings_page',
        'dashicons-admin-generic',
        60
    );
}
add_action('admin_menu', 'yoruo_navi_add_site_settings_menu');

function yoruo_navi_site_settings_page() {
    ?>
    <div class="wrap">
        <h1>サイト共通設定</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('yoruo-navi-settings-group');
            do_settings_sections('yoruo-navi-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function yoruo_navi_site_settings_init() {
    register_setting('yoruo-navi-settings-group', 'yoruo_navi_phone');
    register_setting('yoruo-navi-settings-group', 'yoruo_navi_line_url');

    add_settings_section(
        'yoruo_navi_contact_section',
        '連絡先設定',
        '__return_empty_string',
        'yoruo-navi-settings'
    );

    add_settings_field(
        'yoruo_navi_phone',
        '電話番号',
        function() {
            $val = get_option('yoruo_navi_phone');
            echo '<input type="text" name="yoruo_navi_phone" value="' . esc_attr($val) . '" class="regular-text" placeholder="03-1234-5678">';
        },
        'yoruo-navi-settings',
        'yoruo_navi_contact_section'
    );

    add_settings_field(
        'yoruo_navi_line_url',
        'LINE登録URL',
        function() {
            $val = get_option('yoruo_navi_line_url');
            echo '<input type="url" name="yoruo_navi_line_url" value="' . esc_attr($val) . '" class="regular-text" placeholder="https://line.me/R/ti/p/...">';
        },
        'yoruo-navi-settings',
        'yoruo_navi_contact_section'
    );
}
add_action('admin_init', 'yoruo_navi_site_settings_init');
/**
 * Add Meta Boxes for Slider
 */
function yoruo_navi_add_slider_meta_boxes() {
    add_meta_box(
        'slider_details',
        'バナー詳細設定',
        'yoruo_navi_render_slider_meta_box',
        'slider',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'yoruo_navi_add_slider_meta_boxes');

function yoruo_navi_render_slider_meta_box($post) {
    $subtitle = get_post_meta($post->ID, '_slider_subtitle', true);
    $badge = get_post_meta($post->ID, '_slider_badge', true);
    $link = get_post_meta($post->ID, '_slider_link', true);

    wp_nonce_field('slider_meta_box_nonce', 'slider_meta_box_nonce_field');
    ?>
    <table class="form-table">
        <tr>
            <th><label for="slider_subtitle">サブタイトル</label></th>
            <td>
                <input type="text" id="slider_subtitle" name="slider_subtitle" value="<?php echo esc_attr($subtitle); ?>" class="large-text" placeholder="例：新宿・六本木・銀座。主要エリアの求人を網羅。">
            </td>
        </tr>
        <tr>
            <th><label for="slider_badge">バッジテキスト</label></th>
            <td>
                <input type="text" id="slider_badge" name="slider_badge" value="<?php echo esc_attr($badge); ?>" class="regular-text" placeholder="例：AREA RANKING #1">
                <p class="description">画像の上に表示される小さなラベルです。</p>
            </td>
        </tr>
        <tr>
            <th><label for="slider_link">リンク先URL</label></th>
            <td>
                <input type="url" id="slider_link" name="slider_link" value="<?php echo esc_attr($link); ?>" class="large-text" placeholder="https://...">
                <p class="description">バナーをクリックした時の飛び先です。求人詳細や特集ページなどのURLを入力してください。</p>
            </td>
        </tr>
    </table>
    <?php
}

function yoruo_navi_save_slider_meta($post_id) {
    if (!isset($_POST['slider_meta_box_nonce_field']) || !wp_verify_nonce($_POST['slider_meta_box_nonce_field'], 'slider_meta_box_nonce')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['slider_subtitle'])) {
        update_post_meta($post_id, '_slider_subtitle', sanitize_text_field($_POST['slider_subtitle']));
    }
    if (isset($_POST['slider_badge'])) {
        update_post_meta($post_id, '_slider_badge', sanitize_text_field($_POST['slider_badge']));
    }
    if (isset($_POST['slider_link'])) {
        update_post_meta($post_id, '_slider_link', esc_url_raw($_POST['slider_link']));
    }
}
add_action('save_post_slider', 'yoruo_navi_save_slider_meta');

/**
 * Add Thumbnail Column to Slider List
 */
function yoruo_navi_add_slider_columns($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        if ($key === 'title') {
            $new_columns['slider_image'] = '画像';
        }
        $new_columns[$key] = $value;
    }
    return $new_columns;
}
add_filter('manage_slider_posts_columns', 'yoruo_navi_add_slider_columns');

function yoruo_navi_show_slider_columns_content($column, $post_id) {
    if ($column === 'slider_image') {
        if (has_post_thumbnail($post_id)) {
            echo get_the_post_thumbnail($post_id, array(100, 50));
        } else {
            echo 'なし';
        }
    }
}
add_action('manage_slider_posts_custom_column', 'yoruo_navi_show_slider_columns_content', 10, 2);
/**
 * Add Meta Boxes for Quick Menu and Menu Banners
 */
function yoruo_navi_add_menu_meta_boxes() {
    // Quick Menu (Icons)
    add_meta_box(
        'quick_menu_details',
        'アイコン・リンク設定',
        'yoruo_navi_render_quick_menu_meta_box',
        'quick_menu',
        'normal',
        'high'
    );

    // Menu Banner
    add_meta_box(
        'menu_banner_details',
        'バナー内容設定',
        'yoruo_navi_render_menu_banner_meta_box',
        'menu_banner',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'yoruo_navi_add_menu_meta_boxes');

// Render Quick Menu Meta Box
function yoruo_navi_render_quick_menu_meta_box($post) {
    $icon = get_post_meta($post->ID, '_icon', true);
    $link = get_post_meta($post->ID, '_link', true);
    $color = get_post_meta($post->ID, '_color', true);
    wp_nonce_field('quick_menu_nonce', 'quick_menu_nonce_field');
    ?>
    <table class="form-table">
        <tr>
            <th><label>アイコン (Font Awesome)</label></th>
            <td>
                <input type="text" name="quick_icon" value="<?php echo esc_attr($icon); ?>" class="regular-text" placeholder="例: fa-music">
                <p class="description"><a href="https://fontawesome.com/search?o=r&m=free" target="_blank">Font Awesome 6 Free</a> のクラス名を入力してください。</p>
            </td>
        </tr>
        <tr>
            <th><label>リンク先URL</label></th>
            <td>
                <input type="url" name="quick_link" value="<?php echo esc_url($link); ?>" class="large-text" placeholder="https://...">
            </td>
        </tr>
        <tr>
            <th><label>背景グラデーション色</label></th>
            <td>
                <input type="text" name="quick_color" value="<?php echo esc_attr($color); ?>" class="regular-text" placeholder="例: from-cyan-500 to-blue-500">
                <p class="description">Tailwind CSSのグラデーションクラスを指定してください。</p>
            </td>
        </tr>
    </table>
    <?php
}

// Render Menu Banner Meta Box
function yoruo_navi_render_menu_banner_meta_box($post) {
    $subtitle = get_post_meta($post->ID, '_subtitle', true);
    $link = get_post_meta($post->ID, '_link', true);
    $gradient = get_post_meta($post->ID, '_gradient', true);
    wp_nonce_field('menu_banner_nonce', 'menu_banner_nonce_field');
    ?>
    <table class="form-table">
        <tr>
            <th><label>サブタイトル</label></th>
            <td>
                <input type="text" name="banner_subtitle" value="<?php echo esc_attr($subtitle); ?>" class="large-text" placeholder="例: 3ヶ月で月収50万円を目指す">
            </td>
        </tr>
        <tr>
            <th><label>リンク先URL</label></th>
            <td>
                <input type="url" name="banner_link" value="<?php echo esc_url($link); ?>" class="large-text" placeholder="https://...">
            </td>
        </tr>
        <tr>
            <th><label>バナー色 (Tailwind)</label></th>
            <td>
                <input type="text" name="banner_gradient" value="<?php echo esc_attr($gradient); ?>" class="regular-text" placeholder="例: from-indigo-600 to-purple-600">
                <p class="description">Tailwindのグラデーションクラス（不透明度含む）を指定してください。</p>
            </td>
        </tr>
    </table>
    <?php
}

// Save Logic
function yoruo_navi_save_menu_meta($post_id) {
    // Quick Menu Save
    if (isset($_POST['quick_menu_nonce_field']) && wp_verify_nonce($_POST['quick_menu_nonce_field'], 'quick_menu_nonce')) {
        update_post_meta($post_id, '_icon', sanitize_text_field($_POST['quick_icon']));
        update_post_meta($post_id, '_link', esc_url_raw($_POST['quick_link']));
        update_post_meta($post_id, '_color', sanitize_text_field($_POST['quick_color']));
    }

    // Menu Banner Save
    if (isset($_POST['menu_banner_nonce_field']) && wp_verify_nonce($_POST['menu_banner_nonce_field'], 'menu_banner_nonce')) {
        update_post_meta($post_id, '_subtitle', sanitize_text_field($_POST['banner_subtitle']));
        update_post_meta($post_id, '_link', esc_url_raw($_POST['banner_link']));
        update_post_meta($post_id, '_gradient', sanitize_text_field($_POST['banner_gradient']));
    }
}
add_action('save_post', 'yoruo_navi_save_menu_meta');
