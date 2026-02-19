<?php
/**
 * Template Name: 新規登録ページ
 */

if (is_user_logged_in()) {
    wp_redirect(home_url());
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup_nonce']) && wp_verify_nonce($_POST['signup_nonce'], 'yoruo_signup')) {
    $display_name = sanitize_text_field($_POST['display_name']);
    $email        = sanitize_email($_POST['email']);
    $password     = $_POST['password'];
    $role         = sanitize_text_field($_POST['role']);

    if (empty($display_name) || empty($email) || empty($password)) {
        $error = 'すべてのフィールドを入力してください。';
    } elseif (!is_email($email)) {
        $error = '有効なメールアドレスを入力してください。';
    } elseif (email_exists($email)) {
        $error = 'このメールアドレスは既に登録されています。';
    } else {
        $user_id = wp_create_user($email, $password, $email);
        if (is_wp_error($user_id)) {
            $error = $user_id->get_error_message();
        } else {
            // Update display name
            wp_update_user(array(
                'ID'           => $user_id,
                'display_name' => $display_name,
                'role'         => ($role === 'employer') ? 'store_owner' : 'job_seeker'
            ));

            // Set account status for stores
            if ($role === 'employer') {
                update_user_meta($user_id, '_account_status', 'pending');
            }

            // Auto login and redirect
            wp_set_auth_cookie($user_id);
            wp_redirect(home_url());
            exit;
        }
    }
}

get_header(); ?>

<div class="container mx-auto px-4 py-20 flex flex-col items-center">
    <div class="bg-white p-10 rounded-3xl shadow-xl w-full max-w-md border border-gray-100">
        <h2 class="text-3xl font-black mb-8 text-center text-gray-900">
            新規登録
        </h2>

        <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-50 text-red-600 rounded-xl text-sm font-bold">
                <?php echo esc_html($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-6" id="signup-form">
            <?php wp_nonce_field('yoruo_signup', 'signup_nonce'); ?>
            
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                    表示名
                </label>
                <input
                    type="text"
                    name="display_name"
                    value="<?php echo isset($_POST['display_name']) ? esc_attr($_POST['display_name']) : ''; ?>"
                    class="w-full px-5 py-4 bg-gray-50 border border-transparent rounded-2xl focus:bg-white focus:border-indigo-600 outline-none transition font-bold"
                    placeholder="山田 太郎"
                    required
                />
            </div>

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

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                    あなたはどっち？
                </label>
                <div class="flex gap-4" id="role-selector">
                    <input type="hidden" name="role" id="role-input" value="jobseeker">
                    <button
                        type="button"
                        onclick="setSignupRole('jobseeker')"
                        id="role-btn-jobseeker"
                        class="role-btn flex-1 py-4 px-2 rounded-2xl font-bold text-xs transition border-2 bg-indigo-600 border-indigo-600 text-white"
                    >
                        求職者
                    </button>
                    <button
                        type="button"
                        onclick="setSignupRole('employer')"
                        id="role-btn-employer"
                        class="role-btn flex-1 py-4 px-2 rounded-2xl font-bold text-xs transition border-2 bg-white border-gray-100 text-gray-400"
                    >
                        店舗
                    </button>
                </div>
            </div>

            <button
                type="submit"
                class="w-full py-5 rounded-2xl font-black transition shadow-lg shadow-indigo-100 bg-indigo-600 text-white hover:bg-indigo-700 active:scale-95"
            >
                登録する
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-gray-400 text-sm font-bold">
                すでにアカウントをお持ちですか？
            </p>
            <a
                href="<?php echo esc_url(site_url('/login')); ?>"
                class="text-indigo-600 font-black hover:underline mt-2 inline-block"
            >
                ログインはこちら
            </a>
        </div>
    </div>
</div>

<script>
function setSignupRole(role) {
    const roleInput = document.getElementById('role-input');
    const jobseekerBtn = document.getElementById('role-btn-jobseeker');
    const employerBtn = document.getElementById('role-btn-employer');

    roleInput.value = role;

    if (role === 'jobseeker') {
        jobseekerBtn.classList.add('bg-indigo-600', 'border-indigo-600', 'text-white');
        jobseekerBtn.classList.remove('bg-white', 'border-gray-100', 'text-gray-400');
        
        employerBtn.classList.remove('bg-purple-600', 'border-purple-600', 'text-white');
        employerBtn.classList.add('bg-white', 'border-gray-100', 'text-gray-400');
    } else {
        employerBtn.classList.add('bg-purple-600', 'border-purple-600', 'text-white');
        employerBtn.classList.remove('bg-white', 'border-gray-100', 'text-gray-400');
        
        jobseekerBtn.classList.remove('bg-indigo-600', 'border-indigo-600', 'text-white');
        jobseekerBtn.classList.add('bg-white', 'border-gray-100', 'text-gray-400');
    }
}
</script>

<?php get_footer(); ?>
