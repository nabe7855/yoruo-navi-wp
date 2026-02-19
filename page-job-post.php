<?php
/**
 * Template Name: 求人投稿・編集ページ
 */

if (!is_user_logged_in() || !current_user_can('store_owner')) {
    wp_redirect(site_url('/login'));
    exit;
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;

// Security check for editing
if ($job_id > 0) {
    $post = get_post($job_id);
    if (!$post || $post->post_author != $user_id || $post->post_type != 'job') {
        wp_die('不正なアクセスです。');
    }
}

$message = '';
$error = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['job_post_nonce']) && wp_verify_nonce($_POST['job_post_nonce'], 'yoruo_job_post')) {
    // Required for media_handle_upload
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    $title = sanitize_text_field($_POST['title']);
    $content = wp_kses_post($_POST['description']);
    
    $job_data = array(
        'post_title'   => $title,
        'post_content' => $content,
        'post_type'    => 'job',
        'post_author'  => $user_id,
        'post_status'  => 'pending', // Always pending for store owners (moderation)
    );

    if ($job_id > 0) {
        $job_data['ID'] = $job_id;
        $result = wp_update_post($job_data);
    } else {
        $result = wp_insert_post($job_data);
        $job_id = $result;
    }

    if (!is_wp_error($result)) {
        // Save Meta Fields
        update_post_meta($job_id, '_employment_type', sanitize_text_field($_POST['employment_type']));
        update_post_meta($job_id, '_salary_type', sanitize_text_field($_POST['salary_type']));
        update_post_meta($job_id, '_salary_min', intval($_POST['salary_min']));
        update_post_meta($job_id, '_salary_max', intval($_POST['salary_max']));
        update_post_meta($job_id, '_qualifications', sanitize_textarea_field($_POST['qualifications']));
        update_post_meta($job_id, '_access_info', sanitize_textarea_field($_POST['access_info']));
        update_post_meta($job_id, '_salary_details', sanitize_textarea_field($_POST['salary_details']));
        update_post_meta($job_id, '_benefits', sanitize_textarea_field($_POST['benefits']));
        update_post_meta($job_id, '_insurance', sanitize_textarea_field($_POST['insurance']));
        update_post_meta($job_id, '_working_hours', sanitize_textarea_field($_POST['working_hours']));
        update_post_meta($job_id, '_holidays', sanitize_textarea_field($_POST['holidays']));
        update_post_meta($job_id, '_workplace_info', sanitize_textarea_field($_POST['workplace_info']));

        // Save Taxonomies
        if (isset($_POST['category'])) {
            wp_set_object_terms($job_id, sanitize_text_field($_POST['category']), 'job_category');
        }
        
        // Save Area (Prefecture and Municipality)
        $areas = [];
        if (!empty($_POST['area_pref'])) {
            $areas[] = sanitize_text_field($_POST['area_pref']);
        }
        if (!empty($_POST['area_city'])) {
            $areas[] = sanitize_text_field($_POST['area_city']);
        }
        if (!empty($areas)) {
            wp_set_object_terms($job_id, $areas, 'job_area');
        }

        // Save Feature Tags
        if (isset($_POST['job_tags'])) {
            $tags = array_map('sanitize_text_field', $_POST['job_tags']);
            wp_set_object_terms($job_id, $tags, 'job_tag');
        } else {
            wp_set_object_terms($job_id, array(), 'job_tag');
        }

        // Handle Image Deletions
        $existing_images = get_post_meta($job_id, '_job_images', true);
        if (!is_array($existing_images)) $existing_images = array();
        
        if (isset($_POST['delete_images'])) {
            $to_delete = array_map('intval', $_POST['delete_images']);
            foreach ($to_delete as $att_id) {
                wp_delete_attachment($att_id, true);
                if (($key = array_search($att_id, $existing_images)) !== false) {
                    unset($existing_images[$key]);
                }
            }
        }

        // Handle New Image Uploads
        if (!empty($_FILES['job_images']['name'][0])) {
            $files = $_FILES['job_images'];
            foreach ($files['name'] as $key => $value) {
                if ($files['name'][$key]) {
                    $file = array(
                        'name'     => $files['name'][$key],
                        'type'     => $files['type'][$key],
                        'tmp_name' => $files['tmp_name'][$key],
                        'error'    => $files['error'][$key],
                        'size'     => $files['size'][$key]
                    );

                    $_FILES['single_upload'] = $file;
                    $attachment_id = media_handle_upload('single_upload', $job_id);

                    if (!is_wp_error($attachment_id)) {
                        $existing_images[] = $attachment_id;
                    }
                }
            }
        }
        update_post_meta($job_id, '_job_images', array_values($existing_images));

        // Sync first image to thumbnail (Featured Image)
        if (!empty($existing_images)) {
            set_post_thumbnail($job_id, $existing_images[0]);
        } else {
            delete_post_thumbnail($job_id);
        }

        wp_redirect(site_url('/store-dashboard/?tab=jobs'));
        exit;
    } else {
        $error = '保存に失敗しました。';
    }
}

// Prepare Data for Fields
$edit_data = array(
    'title' => '',
    'description' => '',
    'category' => '',
    'employment_type' => 'アルバイト',
    'area_region' => '',
    'area_pref' => '',
    'area_city' => '',
    'salary_type' => 'hourly',
    'salary_min' => 1200,
    'salary_max' => 1500,
    'qualifications' => '',
    'access_info' => '',
    'salary_details' => '',
    'benefits' => '',
    'insurance' => '',
    'working_hours' => '',
    'holidays' => '',
    'workplace_info' => '',
    'job_tags' => array(),
    'job_images' => array(),
);

if ($job_id > 0) {
    $post = get_post($job_id);
    $edit_data['title'] = $post->post_title;
    $edit_data['description'] = $post->post_content;
    $edit_data['employment_type'] = get_post_meta($job_id, '_employment_type', true);
    $edit_data['salary_type'] = get_post_meta($job_id, '_salary_type', true);
    $edit_data['salary_min'] = get_post_meta($job_id, '_salary_min', true);
    $edit_data['salary_max'] = get_post_meta($job_id, '_salary_max', true);
    $edit_data['qualifications'] = get_post_meta($job_id, '_qualifications', true);
    $edit_data['access_info'] = get_post_meta($job_id, '_access_info', true);
    $edit_data['salary_details'] = get_post_meta($job_id, '_salary_details', true);
    $edit_data['benefits'] = get_post_meta($job_id, '_benefits', true);
    $edit_data['insurance'] = get_post_meta($job_id, '_insurance', true);
    $edit_data['working_hours'] = get_post_meta($job_id, '_working_hours', true);
    $edit_data['holidays'] = get_post_meta($job_id, '_holidays', true);
    $edit_data['workplace_info'] = get_post_meta($job_id, '_workplace_info', true);
    
    $cats = wp_get_object_terms($job_id, 'job_category');
    if (!empty($cats)) $edit_data['category'] = $cats[0]->name;
    
    $areas = wp_get_object_terms($job_id, 'job_area');
    if (!empty($areas)) {
        foreach ($areas as $area) {
            // Simple heuristic: if it ends with a prefecture suffix, it's a pref
            if (preg_match('/.{2,3}[都道府県]$/', $area->name)) {
                $edit_data['area_pref'] = $area->name;
            } else {
                $edit_data['area_city'] = $area->name;
            }
        }
    }

    $existing_tags = wp_get_object_terms($job_id, 'job_tag');
    if (!empty($existing_tags)) {
        $edit_data['job_tags'] = wp_list_pluck($existing_tags, 'name');
    }

    $edit_data['job_images'] = get_post_meta($job_id, '_job_images', true);
    if (!is_array($edit_data['job_images'])) $edit_data['job_images'] = array();
}

get_header(); ?>

<div class="container mx-auto px-4 py-12 max-w-5xl">
    <div class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-50 overflow-hidden">
        <div class="bg-slate-900 px-10 py-12 text-white relative overflow-hidden">
            <div class="relative z-10">
                <h1 class="text-4xl font-black mb-2 tracking-tight">
                    <?php echo $job_id > 0 ? '求人を編集する' : '求人を掲載する'; ?>
                </h1>
                <p class="text-slate-400">求人情報を入力して、新しい募集を作成しましょう。</p>
            </div>
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl"></div>
        </div>

        <?php if ($error): ?>
            <div class="m-10 p-4 bg-red-50 text-red-600 rounded-xl font-bold"><?php echo esc_html($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data" class="p-10 space-y-12">
            <?php wp_nonce_field('yoruo_job_post', 'job_post_nonce'); ?>
            
            <section class="space-y-8">
                <h3 class="text-xl font-black text-gray-800 flex items-center">
                    <span class="w-1.5 h-6 bg-blue-600 mr-4 rounded-full"></span>
                    基本設定
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="col-span-full">
                        <label class="block text-sm font-bold text-gray-500 mb-3 uppercase tracking-widest text-left">求人タイトル</label>
                        <input required type="text" name="title" value="<?php echo esc_attr($edit_data['title']); ?>" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none font-bold text-gray-800" placeholder="例: 【安平町】時給1200円！日払いOKの工場スタッフ">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-500 mb-3 uppercase tracking-widest text-left">職種</label>
                        <select name="category" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none font-bold">
                            <?php 
                            $categories = array("キャバクラ", "ガールズバー", "スナック", "ラウンジ", "ホストクラブ", "バー", "クラブ", "ニュークラブ", "ショークラブ");
                            foreach ($categories as $cat) : ?>
                                <option value="<?php echo esc_attr($cat); ?>" <?php selected($edit_data['category'], $cat); ?>><?php echo esc_html($cat); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-500 mb-3 uppercase tracking-widest text-left">雇用形態</label>
                        <select name="employment_type" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none font-bold">
                            <?php 
                            $types = array("正社員", "アルバイト", "業務委託", "体験入店");
                            foreach ($types as $type) : ?>
                                <option value="<?php echo esc_attr($type); ?>" <?php selected($edit_data['employment_type'], $type); ?>><?php echo esc_html($type); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-span-full">
                        <label class="block text-sm font-bold text-gray-500 mb-6 uppercase tracking-widest text-left">求人画像（複数選択可）</label>
                        
                        <div id="image-upload-container" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            <!-- Existing Images -->
                            <?php $idx = 0; foreach ($edit_data['job_images'] as $img_id) : 
                                $img_url = wp_get_attachment_image_url($img_id, 'medium');
                                if ($img_url) : ?>
                                <div class="relative aspect-square rounded-2xl overflow-hidden border border-slate-200 group <?php echo $idx === 0 ? 'border-indigo-500 ring-2 ring-indigo-500/20' : ''; ?>">
                                    <img src="<?php echo esc_url($img_url); ?>" class="w-full h-full object-cover">
                                    <?php if ($idx === 0) : ?>
                                        <div class="absolute top-2 left-2 px-2 py-1 bg-indigo-600 text-white text-[8px] font-black rounded-lg uppercase shadow-lg">Main Photo</div>
                                    <?php endif; ?>
                                    <label class="absolute top-2 right-2 cursor-pointer">
                                        <input type="checkbox" name="delete_images[]" value="<?php echo esc_attr($img_id); ?>" class="hidden peer">
                                        <div class="w-8 h-8 bg-black/40 text-white rounded-full flex items-center justify-center shadow-lg transition peer-checked:bg-red-500 group-hover:bg-black/60">
                                            <i class="fas fa-times"></i>
                                        </div>
                                        <div class="absolute inset-0 bg-red-500/20 hidden peer-checked:block"></div>
                                    </label>
                                    <div class="absolute bottom-2 left-2 px-2 py-0.5 bg-black/50 text-[10px] text-white rounded">削除対象</div>
                                </div>
                            <?php $idx++; endif; endforeach; ?>

                            <!-- Upload Button / Placeholder -->
                            <label class="relative aspect-square rounded-2xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition group">
                                <input type="file" name="job_images[]" multiple accept="image/*" class="hidden" id="job-image-input">
                                <i class="fas fa-plus text-slate-300 text-2xl group-hover:text-blue-500 mb-2 transition"></i>
                                <span class="text-[10px] font-black text-slate-400 group-hover:text-blue-500 uppercase tracking-widest">アップロード</span>
                            </label>
                        </div>
                        <p class="mt-4 text-[10px] font-bold text-slate-400">※最大5枚まで。形式: JPG, PNG, WEBP。</p>
                        
                        <div id="preview-grid" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 mt-4"></div>
                    </div>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const fileInput = document.getElementById('job-image-input');
                        const previewGrid = document.getElementById('preview-grid');

                        fileInput.addEventListener('change', function() {
                            previewGrid.innerHTML = '';
                            if (this.files) {
                                Array.from(this.files).forEach(file => {
                                    const reader = new FileReader();
                                    reader.onload = function(e) {
                                        const isFirst = previewGrid.children.length === 0 && <?php echo empty($edit_data['job_images']) ? 'true' : 'false'; ?>;
                                        const div = document.createElement('div');
                                        div.className = `relative aspect-square rounded-2xl overflow-hidden border-2 shadow-xl ${isFirst ? 'border-indigo-500' : 'border-blue-500'}`;
                                        div.innerHTML = `
                                            <img src="${e.target.result}" class="w-full h-full object-cover">
                                            <div class="absolute top-2 left-2 px-2 py-1 ${isFirst ? 'bg-indigo-600' : 'bg-blue-600'} text-white text-[8px] font-black rounded-lg uppercase shadow-lg">
                                                ${isFirst ? 'New Main Photo' : 'New'}
                                            </div>
                                        `;
                                        previewGrid.appendChild(div);
                                    }
                                    reader.readAsDataURL(file);
                                });
                            }
                        });
                    });
                    </script>

                    <div class="col-span-full grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-500 mb-3 uppercase tracking-widest text-left">地方</label>
                            <select id="region-select" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none font-bold">
                                <option value="">地方を選択</option>
                                <?php 
                                $regions = array("北海道", "東北", "関東", "中部", "関西", "中国", "四国", "九州・沖縄");
                                foreach ($regions as $region) : ?>
                                    <option value="<?php echo esc_attr($region); ?>"><?php echo esc_html($region); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-500 mb-3 uppercase tracking-widest text-left">都道府県</label>
                            <select id="pref-select" name="area_pref" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none font-bold">
                                <option value="">都道府県を選択</option>
                                <?php 
                                // All prefectures for initial hydration if editing
                                $all_prefs = array(
                                    '北海道' => '01', '青森県' => '02', '岩手県' => '03', '宮城県' => '04', '秋田県' => '05', 
                                    '山形県' => '06', '福島県' => '07', '茨城県' => '08', '栃木県' => '09', '群馬県' => '10', 
                                    '埼玉県' => '11', '千葉県' => '12', '東京都' => '13', '神奈川県' => '14', '新潟県' => '15', 
                                    '富山県' => '16', '石川県' => '17', '福井県' => '18', '山梨県' => '19', '長野県' => '20', 
                                    '岐阜県' => '21', '静岡県' => '22', '愛知県' => '23', '三重県' => '24', '滋賀県' => '25', 
                                    '京都府' => '26', '大阪府' => '27', '兵庫県' => '28', '奈良県' => '29', '和歌山県' => '30', 
                                    '鳥取県' => '31', '島根県' => '32', '岡山県' => '33', '広島県' => '34', '山口県' => '35', 
                                    '徳島県' => '36', '香川県' => '37', '愛媛県' => '38', '高知県' => '39', '福岡県' => '40', 
                                    '佐賀県' => '41', '長崎県' => '42', '熊本県' => '43', '大分県' => '44', '宮崎県' => '45', 
                                    '鹿児島県' => '46', '沖縄県' => '47'
                                );
                                foreach ($all_prefs as $pref_name => $pref_code) : ?>
                                    <option value="<?php echo esc_attr($pref_name); ?>" data-code="<?php echo esc_attr($pref_code); ?>" <?php selected($edit_data['area_pref'], $pref_name); ?>>
                                        <?php echo esc_html($pref_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-500 mb-3 uppercase tracking-widest text-left">市区町村</label>
                            <select id="city-select" name="area_city" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none font-bold">
                                <option value="">市区町村を選択</option>
                                <?php if ($edit_data['area_city']) : ?>
                                    <option value="<?php echo esc_attr($edit_data['area_city']); ?>" selected><?php echo esc_html($edit_data['area_city']); ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const regionSelect = document.getElementById('region-select');
                        const prefSelect = document.getElementById('pref-select');
                        const citySelect = document.getElementById('city-select');

                        const REGIONS_DATA = {
                            "北海道": ["北海道"],
                            "東北": ["青森県", "岩手県", "宮城県", "秋田県", "山形県", "福島県"],
                            "関東": ["東京都", "神奈川県", "埼玉県", "千葉県", "茨城県", "栃木県", "群馬県"],
                            "中部": ["愛知県", "静岡県", "岐阜県", "三重県", "新潟県", "富山県", "石川県", "福井県", "山梨県", "長野県"],
                            "関西": ["大阪府", "兵庫県", "京都府", "滋賀県", "奈良県", "和歌山県"],
                            "中国": ["鳥取県", "島根県", "岡山県", "広島県", "山口県"],
                            "四国": ["徳島県", "香川県", "愛媛県", "高知県"],
                            "九州・沖縄": ["福岡県", "佐賀県", "長崎県", "熊本県", "大分県", "宮崎県", "鹿児島県", "沖縄県"]
                        };

                        const PREF_NAME_TO_CODE = {
                            "北海道": "01", "青森県": "02", "岩手県": "03", "宮城県": "04", "秋田県": "05", "山形県": "06", "福島県": "07",
                            "茨城県": "08", "栃木県": "09", "群馬県": "10", "埼玉県": "11", "千葉県": "12", "東京都": "13", "神奈川県": "14",
                            "新潟県": "15", "富山県": "16", "石川県": "17", "福井県": "18", "山梨県": "19", "長野県": "20", "岐阜県": "21",
                            "静岡県": "22", "愛知県": "23", "三重県": "24", "滋賀県": "25", "京都府": "26", "大阪府": "27", "兵庫県": "28",
                            "奈良県": "29", "和歌山県": "30", "鳥取県": "31", "島根県": "32", "岡山県": "33", "広島県": "34", "山口県": "35",
                            "徳島県": "36", "香川県": "37", "愛媛県": "38", "高知県": "39", "福岡県": "40", "佐賀県": "41", "長崎県": "42",
                            "熊本県": "43", "大分県": "44", "宮崎県": "45", "鹿児島県": "46", "沖縄県": "47"
                        };

                        // Region -> Prefecture
                        regionSelect.addEventListener('change', function() {
                            const region = this.value;
                            const currentPref = prefSelect.value;
                            prefSelect.innerHTML = '<option value="">都道府県を選択</option>';
                            
                            if (region && REGIONS_DATA[region]) {
                                REGIONS_DATA[region].forEach(pref => {
                                    const option = document.createElement('option');
                                    option.value = pref;
                                    option.textContent = pref;
                                    option.dataset.code = PREF_NAME_TO_CODE[pref];
                                    if (pref === currentPref) option.selected = true;
                                    prefSelect.appendChild(option);
                                });
                            }
                            prefSelect.dispatchEvent(new Event('change'));
                        });

                        // Prefecture -> City
                        prefSelect.addEventListener('change', function() {
                            const selectedOption = this.options[this.selectedIndex];
                            const prefCode = selectedOption ? selectedOption.getAttribute('data-code') : null;
                            const currentCity = citySelect.value;
                            
                            citySelect.innerHTML = '<option value="">市区町村を選択</option>';
                            
                            if (prefCode && window.ALL_MUNICIPALITIES_DATA && window.ALL_MUNICIPALITIES_DATA[prefCode]) {
                                window.ALL_MUNICIPALITIES_DATA[prefCode].forEach(city => {
                                    const option = document.createElement('option');
                                    option.value = city;
                                    option.textContent = city;
                                    if (city === currentCity) option.selected = true;
                                    citySelect.appendChild(option);
                                });
                            }
                        });

                        // Hydrate Region based on initial Prefecture
                        if (prefSelect.value) {
                            for (const [region, prefs] of Object.entries(REGIONS_DATA)) {
                                if (prefs.includes(prefSelect.value)) {
                                    regionSelect.value = region;
                                    // Trigger prefSelect update to filter list but keep selection
                                    const currentPref = prefSelect.value;
                                    prefSelect.innerHTML = '<option value="">都道府県を選択</option>';
                                    prefs.forEach(pref => {
                                        const option = document.createElement('option');
                                        option.value = pref;
                                        option.textContent = pref;
                                        option.dataset.code = PREF_NAME_TO_CODE[pref];
                                        if (pref === currentPref) option.selected = true;
                                        prefSelect.appendChild(option);
                                    });
                                    break;
                                }
                            }
                            // Trigger citySelect update
                            prefSelect.dispatchEvent(new Event('change'));
                        }
                    });
                    </script>
            </section>

            <section class="space-y-8">
                <h3 class="text-xl font-black text-gray-800 flex items-center">
                    <span class="w-1.5 h-6 bg-blue-600 mr-4 rounded-full"></span>
                    給与設定
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <label class="block text-sm font-bold text-gray-500 mb-3 uppercase tracking-widest text-left">給与タイプ</label>
                        <select name="salary_type" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none font-bold">
                            <option value="hourly" <?php selected($edit_data['salary_type'], 'hourly'); ?>>時給</option>
                            <option value="daily" <?php selected($edit_data['salary_type'], 'daily'); ?>>日給</option>
                            <option value="monthly" <?php selected($edit_data['salary_type'], 'monthly'); ?>>月給</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-500 mb-3 uppercase tracking-widest text-left">最小給与</label>
                        <input type="number" name="salary_min" value="<?php echo esc_attr($edit_data['salary_min']); ?>" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none font-bold">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-500 mb-3 uppercase tracking-widest text-left">最大給与</label>
                        <input type="number" name="salary_max" value="<?php echo esc_attr($edit_data['salary_max']); ?>" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none font-bold">
                    </div>
                </div>
            </section>

            <section class="space-y-8">
                <h3 class="text-xl font-black text-gray-800 flex items-center">
                    <span class="w-1.5 h-6 bg-blue-600 mr-4 rounded-full"></span>
                    こだわり条件（検索タグ）
                </h3>
                <div class="space-y-8">
                    <?php 
                    $feature_groups = get_yoruo_feature_tags();
                    foreach ($feature_groups as $group_label => $tags) : ?>
                        <div>
                            <label class="block text-sm font-bold text-gray-500 mb-4 uppercase tracking-widest text-left"><?php echo esc_html($group_label); ?></label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <?php foreach ($tags as $tag) : ?>
                                    <label class="flex items-center gap-3 p-4 bg-slate-50 border border-slate-100 rounded-2xl cursor-pointer hover:bg-white hover:border-blue-500 transition-all group">
                                        <input type="checkbox" name="job_tags[]" value="<?php echo esc_attr($tag); ?>" <?php checked(in_array($tag, $edit_data['job_tags'])); ?> class="w-5 h-5 border-2 border-slate-200 rounded-md checked:bg-blue-600 transition-all">
                                        <span class="text-sm font-bold text-slate-600 group-hover:text-blue-600 transition-colors"><?php echo esc_html($tag); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="space-y-8">
                <h3 class="text-xl font-black text-gray-800 flex items-center">
                    <span class="w-1.5 h-6 bg-blue-600 mr-4 rounded-full"></span>
                    詳細情報
                </h3>
                <div class="grid grid-cols-1 gap-8">
                    <?php
                    $fields = array(
                        'access_info'    => array('label' => '勤務地・交通手段', 'placeholder' => '北海道勇払郡安平町、植苗駅より車9分など'),
                        'salary_details' => array('label' => '給与・報酬の詳細', 'placeholder' => '時給1200円、月収例20.8万円など'),
                        'insurance'      => array('label' => '加入保険', 'placeholder' => '社会保険あり（健康・厚生・雇用・労災）など'),
                        'working_hours'  => array('label' => '勤務時間', 'placeholder' => '8:30～17:00（実働7.5h）など'),
                        'holidays'       => array('label' => '休日・休暇', 'placeholder' => '土日祝（工場カレンダー）など'),
                        'workplace_info' => array('label' => '職場情報・PR', 'placeholder' => '2020～2022年度、全国277名が社員登用！など'),
                        'qualifications' => array('label' => '経験・資格（補足）', 'placeholder' => '特になし、既卒・第二新卒も歓迎など'),
                        'benefits'       => array('label' => '待遇・福利厚生（補足）', 'placeholder' => '社内割引あり、研修制度充実など'),
                        'description'    => array('label' => '仕事内容の補足', 'placeholder' => '具体的な仕事の流れなど自由に入力してください'),
                    );
                    foreach ($fields as $key => $f) : ?>
                        <div class="group">
                            <label class="block text-sm font-bold text-gray-500 mb-3 uppercase tracking-widest group-focus-within:text-blue-600 transition text-left"><?php echo esc_html($f['label']); ?></label>
                            <textarea name="<?php echo esc_attr($key); ?>" rows="3" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-blue-100 outline-none text-sm leading-relaxed" placeholder="<?php echo esc_attr($f['placeholder']); ?>"><?php echo esc_textarea($edit_data[$key]); ?></textarea>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <div class="flex gap-4 pt-12">
                <a href="<?php echo esc_url(site_url('/store-dashboard')); ?>" class="px-10 py-5 text-gray-400 font-black hover:text-gray-600 transition">キャンセル</a>
                <button type="submit" class="flex-grow py-5 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-3xl shadow-2xl shadow-blue-900/20 flex items-center justify-center transition active:scale-95">
                    <i class="fas fa-paper-plane mr-3"></i>
                    <span><?php echo $job_id > 0 ? '求人を更新申請する' : '求人を公開申請する'; ?></span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php get_footer(); ?>
