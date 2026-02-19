<?php
/**
 * Template Name: 店舗ダッシュボード
 */

if (!is_user_logged_in() || !current_user_can('store_owner')) {
    wp_redirect(site_url('/login'));
    exit;
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// Check if store info is registered
$store_name = get_user_meta($user_id, '_store_name', true);
$is_onboarding = empty($store_name);

// Handle Store Info Registration
if (isset($_POST['register_store_nonce']) && wp_verify_nonce($_POST['register_store_nonce'], 'yoruo_register_store')) {
    update_user_meta($user_id, '_store_name', sanitize_text_field($_POST['store_name']));
    update_user_meta($user_id, '_store_business_type', sanitize_text_field($_POST['business_type']));
    update_user_meta($user_id, '_store_area_pref', sanitize_text_field($_POST['area_pref']));
    update_user_meta($user_id, '_store_area_city', sanitize_text_field($_POST['area_city']));
    update_user_meta($user_id, '_store_contact_email', sanitize_email($_POST['contact_email']));
    update_user_meta($user_id, '_store_contact_phone', sanitize_text_field($_POST['contact_phone']));
    
    // Refresh page
    wp_redirect(get_permalink());
    exit;
}

get_header(); ?>

<div class="container mx-auto px-4 py-12 max-w-6xl">
    <?php if ($is_onboarding) : ?>
        <!-- Onboarding Form -->
        <div class="max-w-2xl mx-auto">
            <div class="bg-white p-10 rounded-3xl shadow-xl border border-gray-100">
                <h2 class="text-3xl font-black mb-4 text-gray-900 text-center">店舗情報の登録</h2>
                <p class="text-gray-500 text-center mb-10">求人を投稿するために、まずは店舗情報を登録しましょう。</p>

                <form method="POST" action="" class="space-y-6">
                    <?php wp_nonce_field('yoruo_register_store', 'register_store_nonce'); ?>
                    
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 text-left">店舗名</label>
                        <input type="text" name="store_name" required class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-2xl focus:bg-white focus:border-indigo-600 outline-none transition font-bold" placeholder="店舗名を入力">
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 text-left">業態</label>
                        <select name="business_type" required class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-2xl focus:bg-white focus:border-indigo-600 outline-none transition font-bold appearance-none">
                            <option value="" disabled selected>選択してください</option>
                            <?php
                            $types = ['キャバクラ', 'ガールズバー', 'スナック', 'ラウンジ', 'ホストクラブ', 'バー', 'クラブ', 'ニュークラブ'];
                            foreach ($types as $type) {
                                echo '<option value="' . esc_attr($type) . '">' . esc_html($type) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 text-left">都道府県</label>
                            <select id="pref-select" name="area_pref" required class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-2xl focus:bg-white focus:border-indigo-600 outline-none transition font-bold appearance-none">
                                <option value="" disabled selected>選択</option>
                                <?php
                                $prefs = [
                                    '01' => '北海道', '02' => '青森県', '03' => '岩手県', '04' => '宮城県', '05' => '秋田県',
                                    '06' => '山形県', '07' => '福島県', '08' => '茨城県', '09' => '栃木県', '10' => '群馬県',
                                    '11' => '埼玉県', '12' => '千葉県', '13' => '東京都', '14' => '神奈川県', '15' => '新潟県',
                                    '16' => '富山県', '17' => '石川県', '18' => '福井県', '19' => '山梨県', '20' => '長野県',
                                    '21' => '岐阜県', '22' => '静岡県', '23' => '愛知県', '24' => '三重県', '25' => '滋賀県',
                                    '26' => '京都府', '27' => '大阪府', '28' => '兵庫県', '29' => '奈良県', '30' => '和歌山県',
                                    '31' => '鳥取県', '32' => '島根県', '33' => '岡山県', '34' => '広島県', '35' => '山口県',
                                    '36' => '徳島県', '37' => '香川県', '38' => '愛媛県', '39' => '高知県', '40' => '福岡県',
                                    '41' => '佐賀県', '42' => '長崎県', '43' => '熊本県', '44' => '大分県', '45' => '宮崎県',
                                    '46' => '鹿児島県', '47' => '沖縄県'
                                ];
                                foreach ($prefs as $code => $name) {
                                    echo '<option value="' . esc_attr($name) . '" data-code="' . esc_attr($code) . '">' . esc_html($name) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 text-left">市区町村</label>
                            <select id="city-select" name="area_city" required class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-2xl focus:bg-white focus:border-indigo-600 outline-none transition font-bold appearance-none">
                                <option value="" disabled selected>都道府県を選択</option>
                            </select>
                        </div>
                    </div>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const prefSelect = document.getElementById('pref-select');
                        const citySelect = document.getElementById('city-select');

                        prefSelect.addEventListener('change', function() {
                            const selectedOption = this.options[this.selectedIndex];
                            const prefCode = selectedOption.getAttribute('data-code');
                            
                            // Reset city select
                            citySelect.innerHTML = '<option value="" disabled selected>選択してください</option>';
                            
                            if (prefCode && window.ALL_MUNICIPALITIES_DATA && window.ALL_MUNICIPALITIES_DATA[prefCode]) {
                                const cities = window.ALL_MUNICIPALITIES_DATA[prefCode];
                                cities.forEach(function(city) {
                                    const option = document.createElement('option');
                                    option.value = city;
                                    option.textContent = city;
                                    citySelect.appendChild(option);
                                });
                            }
                        });
                    });
                    </script>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 text-left">連絡用メール</label>
                            <input type="email" name="contact_email" value="<?php echo esc_attr($current_user->user_email); ?>" required class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-2xl focus:bg-white focus:border-indigo-600 outline-none transition font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 text-left">電話番号</label>
                            <input type="text" name="contact_phone" required class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-2xl focus:bg-white focus:border-indigo-600 outline-none transition font-bold" placeholder="03-xxxx-xxxx">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-5 bg-indigo-600 text-white rounded-2xl font-black transition shadow-lg shadow-indigo-100 hover:bg-indigo-700 active:scale-95">
                        店舗情報を登録して開始する
                    </button>
                </form>
            </div>
        </div>
    <?php else : ?>
        <!-- Main Dashboard -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-black text-gray-900"><?php echo esc_html($store_name); ?></h1>
                <p class="text-gray-500 font-bold">店舗情報管理ダッシュボード</p>
                <a href="<?php echo wp_logout_url(home_url()); ?>" class="mt-2 text-xs font-bold text-gray-400 hover:text-rose-500 flex items-center gap-1 transition">
                    <i class="fas fa-sign-out-alt"></i> ログアウト
                </a>
            </div>
            <a href="<?php echo esc_url(site_url('/job-post')); ?>" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-100 flex items-center justify-center space-x-2 transition active:scale-95">
                <i class="fas fa-plus mr-2"></i>
                <span>新規求人を投稿</span>
            </a>
        </div>

        <!-- Dashboard Content (Tabs Logic) -->
        <?php
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'jobs';
        ?>
        <div class="flex overflow-x-auto space-x-1 bg-gray-100 p-1 rounded-xl mb-8 w-full md:w-fit">
            <a href="?tab=jobs" class="px-6 py-3 rounded-lg text-sm font-bold transition whitespace-nowrap <?php echo $active_tab === 'jobs' ? 'bg-white shadow-sm text-indigo-600' : 'text-gray-500 hover:text-gray-700'; ?>">
                <i class="fas fa-briefcase mr-2"></i>求人管理
            </a>
            <a href="?tab=applications" class="px-6 py-3 rounded-lg text-sm font-bold transition whitespace-nowrap <?php echo $active_tab === 'applications' ? 'bg-white shadow-sm text-indigo-600' : 'text-gray-500 hover:text-gray-700'; ?>">
                <i class="fas fa-users mr-2"></i>応募管理
            </a>
        </div>

        <div class="min-h-[400px]">
            <?php
            // Get all jobs for this user once
            $jobs_args = array(
                'post_type' => 'job',
                'author'    => $user_id,
                'posts_per_page' => -1,
                'post_status' => array('publish', 'pending', 'draft')
            );
            $jobs_query = new WP_Query($jobs_args);
            $my_job_ids = !empty($jobs_query->posts) ? wp_list_pluck($jobs_query->posts, 'ID') : array();

            if ($active_tab === 'jobs') : ?>
                <!-- Jobs List -->
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left whitespace-nowrap">
                            <thead>
                                <tr class="bg-gray-50 text-gray-400 text-[10px] font-bold uppercase tracking-wider border-b border-gray-100">
                                    <th class="px-6 py-4">求人タイトル</th>
                                    <th class="px-6 py-4">ステータス</th>
                                    <th class="px-6 py-4">給与</th>
                                    <th class="px-6 py-4">更新日</th>
                                    <th class="px-6 py-4 text-right">操作</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php if ($jobs_query->have_posts()) : while ($jobs_query->have_posts()) : $jobs_query->the_post(); 
                                    $salary_min = get_post_meta(get_the_ID(), '_salary_min', true);
                                    ?>
                                    <tr class="text-sm hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-4 font-bold text-gray-800"><?php the_title(); ?></td>
                                        <td class="px-6 py-4">
                                            <?php
                                            $status = get_post_status();
                                            $status_label = '下書き';
                                            $status_class = 'bg-gray-100 text-gray-500 border border-gray-200';
                                            if ($status === 'publish') {
                                                $status_label = '公開中';
                                                $status_class = 'bg-emerald-50 text-emerald-600 border border-emerald-100';
                                            } elseif ($status === 'pending') {
                                                $status_label = '審査中';
                                                $status_class = 'bg-amber-50 text-amber-600 border border-amber-100';
                                            }
                                            ?>
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider <?php echo $status_class; ?>">
                                                <?php echo $status_label; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 font-medium">
                                            <?php echo number_format($salary_min); ?>円〜
                                        </td>
                                        <td class="px-6 py-4 text-gray-400 font-medium"><?php echo get_the_modified_date(); ?></td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="<?php echo esc_url(add_query_arg('job_id', get_the_ID(), site_url('/job-post'))); ?>" class="text-indigo-600 hover:text-indigo-800 font-bold px-3 py-1 hover:bg-indigo-50 rounded-lg transition">
                                                <i class="fas fa-edit mr-1"></i>編集
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; else : ?>
                                    <tr>
                                        <td colspan="5" class="py-20 text-center text-gray-400">まだ求人がありません</td>
                                    </tr>
                                <?php endif; wp_reset_postdata(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif ($active_tab === 'applications') : ?>
                <!-- Applications List -->
                <?php
                $apps_query = null;
                if (!empty($my_job_ids)) {
                    $app_args = array(
                        'post_type' => 'application',
                        'meta_query' => array(
                            array(
                                'key' => '_job_id',
                                'value' => $my_job_ids,
                                'compare' => 'IN'
                            )
                        ),
                        'posts_per_page' => -1
                    );
                    $apps_query = new WP_Query($app_args);
                }
                ?>
                <div class="space-y-4 text-left">
                    <?php if ($apps_query && $apps_query->have_posts()) : while ($apps_query->have_posts()) : $apps_query->the_post(); 
                        $job_id = get_post_meta(get_the_ID(), '_job_id', true);
                        $message = get_post_meta(get_the_ID(), '_message', true);
                        $contact_type = get_post_meta(get_the_ID(), '_contact_type', true);
                        $contact_value = get_post_meta(get_the_ID(), '_contact_value', true);
                        $status = get_post_meta(get_the_ID(), '_status', true);
                        ?>
                        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h3 class="font-black text-xl text-gray-800"><?php the_title(); ?></h3>
                                        <span class="px-2.5 py-1 bg-gray-100 text-gray-600 text-[10px] font-black rounded uppercase border border-gray-200">
                                            <?php echo esc_html($contact_type); ?>
                                        </span>
                                        <span class="text-xs text-slate-400 font-bold">
                                            <?php echo get_the_date(); ?>
                                        </span>
                                    </div>
                                    <p class="text-sm text-indigo-600 font-bold mb-3 flex items-center gap-1">
                                        <i class="fas fa-briefcase text-[14px] mr-1"></i>
                                        対象求人: <?php echo get_the_title($job_id); ?>
                                    </p>
                                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                        <p class="text-sm text-gray-700 font-medium leading-relaxed">
                                            <?php echo nl2br(esc_html($message)); ?>
                                        </p>
                                        <div class="mt-3 pt-3 border-t border-slate-200 text-xs text-gray-500 font-bold">
                                            連絡先: <?php echo esc_html($contact_value); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; else : ?>
                        <div class="py-20 text-center text-gray-400 bg-white rounded-2xl border border-gray-100">
                            まだ応募がありません
                        </div>
                    <?php endif; wp_reset_postdata(); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
