<?php
/**
 * Template Name: 応募フォームページ
 */

$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;
$job = get_post($job_id);

if (!$job || $job->post_type != 'job') {
    wp_redirect(home_url());
    exit;
}

if (!is_user_logged_in()) {
    // Redirect to login with redirect_to param
    wp_redirect(site_url('/login/?redirect_to=' . urlencode(get_permalink() . '?job_id=' . $job_id)));
    exit;
}

$current_user = wp_get_current_user();
$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_nonce']) && wp_verify_nonce($_POST['apply_nonce'], 'yoruo_apply')) {
    $name = sanitize_text_field($_POST['name']);
    $contact_type = sanitize_text_field($_POST['contact_type']);
    $contact_value = sanitize_text_field($_POST['contact_value']);
    $message = sanitize_textarea_field($_POST['message']);
    $start_date = sanitize_text_field($_POST['start_date']);

    $app_data = array(
        'post_title'   => '応募: ' . $job->post_title . ' - ' . $name,
        'post_type'    => 'application',
        'post_status'  => 'publish',
        'post_author'  => $current_user->ID,
    );

    $app_id = wp_insert_post($app_data);

    if (!is_wp_error($app_id)) {
        update_post_meta($app_id, '_job_id', $job_id);
        update_post_meta($app_id, '_applicant_id', $current_user->ID);
        update_post_meta($app_id, '_contact_type', $contact_type);
        update_post_meta($app_id, '_contact_value', $contact_value);
        update_post_meta($app_id, '_message', $message);
        update_post_meta($app_id, '_start_date', $start_date);
        update_post_meta($app_id, '_status', 'submitted');

        // Notify Store Owner
        $author_id = $job->post_author;
        $author_email = get_the_author_meta('user_email', $author_id);
        $subject = '【夜男ナビ】求人への応募がありました: ' . $job->post_title;
        $body = "求人「{$job->post_title}」に応募がありました。\n\n";
        $body .= "名前: {$name}\n";
        $body .= "連絡手段: {$contact_type}\n";
        $body .= "連絡先: {$contact_value}\n";
        $body .= "メッセージ: \n{$message}\n\n";
        $body .= "詳細は管理画面またはダッシュボードをご確認ください。";
        
        wp_mail($author_email, $subject, $body);

        $success = true;
    } else {
        $error = '応募に失敗しました。';
    }
}

get_header(); ?>

<div class="container mx-auto px-4 py-8 md:py-16 flex justify-center bg-slate-50 min-h-screen">
    <div class="w-full max-w-2xl text-left">
        <a href="<?php echo get_permalink($job_id); ?>" class="mb-8 inline-flex items-center gap-2 text-slate-500 hover:text-indigo-600 transition font-black text-xs md:text-sm bg-white px-6 py-3 rounded-2xl shadow-sm border border-slate-100 active:scale-95">
            <i class="fas fa-arrow-left"></i> 戻る
        </a>

        <div class="bg-white rounded-[2.5rem] p-8 md:p-12 border border-slate-100 shadow-2xl shadow-indigo-900/5 relative overflow-hidden text-left">
            <?php if ($success) : ?>
                <div class="text-center py-10">
                    <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-check text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900 mb-4">応募が完了しました！</h2>
                    <p class="text-slate-500 font-bold mb-10 text-center">店舗からの連絡をお待ちください。</p>
                    <a href="<?php echo home_url(); ?>" class="inline-block px-10 py-4 bg-indigo-600 text-white font-black rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition">トップページへ戻る</a>
                </div>
            <?php else : ?>
                <div class="relative z-10 mb-12 text-center md:text-left">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-indigo-50 text-indigo-600 text-[10px] md:text-xs font-black rounded-full uppercase tracking-widest mb-6">
                        <i class="fas fa-paper-plane"></i> Entry Form
                    </div>
                    <h1 class="text-2xl md:text-4xl font-black text-slate-900 leading-tight">
                        求人に応募する
                    </h1>
                    <div class="flex items-center gap-3 mt-4 justify-center md:justify-start">
                        <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400">
                            <i class="fas fa-building"></i>
                        </div>
                        <p class="text-slate-500 font-black text-sm">
                            <?php echo esc_html(get_user_meta($job->post_author, '_store_name', true) ?: get_the_author_meta('display_name', $job->post_author)); ?>
                        </p>
                    </div>
                </div>

                <?php if ($error) : ?>
                    <div class="mb-6 p-4 bg-red-50 text-red-600 rounded-xl font-bold"><?php echo esc_html($error); ?></div>
                <?php endif; ?>

                <form method="POST" action="" class="relative z-10 space-y-10">
                    <?php wp_nonce_field('yoruo_apply', 'apply_nonce'); ?>
                    
                    <div class="space-y-4">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700 uppercase tracking-widest text-left">
                            <div class="w-1.5 h-4 bg-indigo-500 rounded-full"></div>
                            お名前 <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input required type="text" name="name" value="<?php echo esc_attr($current_user->display_name); ?>" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:border-indigo-500 outline-none transition font-bold" placeholder="例: 山田 太郎">
                    </div>

                    <div class="space-y-4">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700 uppercase tracking-widest text-left">
                            <div class="w-1.5 h-4 bg-indigo-500 rounded-full"></div>
                            希望の連絡手段 <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-4">
                            <input type="hidden" name="contact_type" id="contact-type-input" value="line">
                            <button type="button" onclick="setContactType('line')" id="btn-line" class="contact-btn py-5 rounded-3xl border-2 flex flex-col items-center justify-center gap-3 transition-all border-indigo-600 bg-indigo-50 text-indigo-600 shadow-lg shadow-indigo-100">
                                <i class="fab fa-line text-2xl"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest text-center">LINE</span>
                            </button>
                            <button type="button" onclick="setContactType('phone')" id="btn-phone" class="contact-btn py-5 rounded-3xl border-2 flex flex-col items-center justify-center gap-3 transition-all border-slate-50 bg-white text-slate-400">
                                <i class="fas fa-phone text-2xl"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest text-center">電話</span>
                            </button>
                            <button type="button" onclick="setContactType('email')" id="btn-email" class="contact-btn py-5 rounded-3xl border-2 flex flex-col items-center justify-center gap-3 transition-all border-slate-50 bg-white text-slate-400">
                                <i class="fas fa-envelope text-2xl"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest text-center">メール</span>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700 uppercase tracking-widest text-left">
                            <div class="w-1.5 h-4 bg-indigo-500 rounded-full"></div>
                            連絡先情報 <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input required type="text" name="contact_value" id="contact-value-input" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:border-indigo-500 outline-none transition font-bold" placeholder="LINE IDを入力">
                    </div>

                    <div class="space-y-4">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700 uppercase tracking-widest text-left">
                            <div class="w-1.5 h-4 bg-indigo-500 rounded-full"></div>
                            勤務開始可能日
                        </label>
                        <input type="date" name="start_date" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:border-indigo-500 outline-none transition font-bold">
                    </div>

                    <div class="space-y-4">
                        <label class="flex items-center gap-2 text-sm font-black text-slate-700 uppercase tracking-widest text-left">
                            <div class="w-1.5 h-4 bg-indigo-500 rounded-full"></div>
                            店舗へのメッセージ
                        </label>
                        <textarea name="message" rows="4" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:border-indigo-500 outline-none transition font-bold" placeholder="勤務時間の希望や、質問などがあれば自由に入力してください。"></textarea>
                    </div>

                    <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100 flex gap-4 text-left">
                        <i class="fas fa-exclamation-circle text-amber-500 shrink-0 mt-1"></i>
                        <p class="text-xs text-amber-700 font-bold leading-relaxed">
                            応募完了後、お店の担当者より選択した連絡手段へ24時間以内にご連絡いたします。迷惑メール設定やブロック解除等をご確認ください。
                        </p>
                    </div>

                    <button type="submit" class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl transition shadow-2xl shadow-indigo-200 active:scale-[0.98] flex items-center justify-center gap-3">
                        <i class="fas fa-check-circle"></i>
                        応募を完了する
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function setContactType(type) {
    document.getElementById('contact-type-input').value = type;
    const btns = document.querySelectorAll('.contact-btn');
    btns.forEach(b => {
        b.classList.remove('border-indigo-600', 'bg-indigo-50', 'text-indigo-600', 'shadow-lg', 'shadow-indigo-100');
        b.classList.add('border-slate-50', 'bg-white', 'text-slate-400');
    });
    
    const activeBtn = document.getElementById('btn-' + type);
    activeBtn.classList.add('border-indigo-600', 'bg-indigo-50', 'text-indigo-600', 'shadow-lg', 'shadow-indigo-100');
    activeBtn.classList.remove('border-slate-50', 'bg-white', 'text-slate-400');

    const input = document.getElementById('contact-value-input');
    if (type === 'line') input.placeholder = 'LINE IDを入力';
    else if (type === 'phone') input.placeholder = '電話番号を入力';
    else input.placeholder = 'メールアドレスを入力';
}
</script>

<?php get_footer(); ?>
