<?php
/**
 * Template Name: ログインページ
 */

if (is_user_logged_in()) {
    wp_redirect(home_url());
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_nonce']) && wp_verify_nonce($_POST['login_nonce'], 'yoruo_login')) {
    $creds = array(
        'user_login'    => sanitize_email($_POST['email']),
        'user_password' => $_POST['password'],
        'remember'      => true
    );

    $user = wp_signon($creds, false);

    if (is_wp_error($user)) {
        $error = 'メールアドレスまたはパスワードが正しくありません。';
    } else {
        // Redirect store owners to dashboard
        if (in_array('store_owner', (array) $user->roles)) {
            wp_redirect(home_url('/dashboard/'));
        } else {
            wp_redirect(home_url());
        }
        exit;
    }
}

get_header(); ?>

<div class="container mx-auto px-4 py-20 flex flex-col items-center">
    <div class="bg-white p-10 rounded-3xl shadow-xl w-full max-w-md border border-gray-100">
        <h2 class="text-3xl font-black mb-8 text-center text-gray-900">
            ログイン
        </h2>

        <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-50 text-red-600 rounded-xl text-sm font-bold">
                <?php echo esc_html($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-6">
            <?php wp_nonce_field('yoruo_login', 'login_nonce'); ?>
            
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                    メールアドレス
                </label>
                <input
                    type="email"
                    name="email"
                    value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>"
                    class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-2xl focus:bg-white focus:border-indigo-600 outline-none transition font-bold"
                    placeholder="example@example.com"
                    required
                />
            </div>

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                    パスワード
                </label>
                <input
                    type="password"
                    name="password"
                    class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-2xl focus:bg-white focus:border-indigo-600 outline-none transition font-bold"
                    placeholder="••••••••"
                    required
                />
            </div>

            <button
                type="submit"
                class="w-full py-5 rounded-2xl font-black transition shadow-lg shadow-indigo-100 bg-indigo-600 text-white hover:bg-indigo-700 active:scale-95"
            >
                ログイン
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-gray-400 text-sm font-bold">
                アカウントをお持ちでないですか？
            </p>
            <a
                href="<?php echo esc_url(site_url('/signup')); ?>"
                class="text-indigo-600 font-black hover:underline mt-2 inline-block"
            >
                新規登録はこちら
            </a>
        </div>
    </div>
</div>

<?php get_footer(); ?>
