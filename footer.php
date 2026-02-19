<?php
/**
 * The template for displaying the footer
 */
?>
    </main><!-- #primary -->

    <footer class="bg-slate-50 text-slate-600 py-16 lg:py-20 border-t border-slate-200 pb-28 lg:pb-16 relative z-10">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                <div class="sm:col-span-2">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-block mb-6">
                        <span class="text-3xl font-black bg-gradient-to-r from-cyan-500 to-blue-500 bg-clip-text text-transparent">
                            夜男ナビ <span class="text-lg font-bold bg-clip-text text-transparent bg-gradient-to-r from-cyan-400 to-blue-300">-ヨルオナビ-</span>
                        </span>
                    </a>
                    <p class="text-sm leading-relaxed max-w-sm font-medium text-slate-500">
                        夜男ナビ（よるおナビ）は、男性ナイトワークに特化した日本最大級の求人プラットフォームです。
                        全国の厳選された優良店舗のみを掲載し、あなたの「稼ぎたい」を全力でサポートします。
                    </p>
                </div>
                <div>
                    <h4 class="text-slate-900 font-black mb-6 tracking-widest uppercase text-sm">サイトマップ</h4>
                    <ul class="text-sm space-y-4 font-bold text-slate-600">
                        <li><a href="<?php echo esc_url( home_url( '/jobs/' ) ); ?>" class="hover:text-cyan-500 transition-colors">求人を検索</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/features/' ) ); ?>" class="hover:text-cyan-500 transition-colors">特集</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>" class="hover:text-cyan-500 transition-colors">よくある質問</a></li>
                        <li><a href="#" class="matcher-trigger hover:text-cyan-500 transition-colors">30秒診断</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/business/' ) ); ?>" class="hover:text-cyan-500 transition-colors">店舗掲載について</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-slate-900 font-black mb-6 tracking-widest uppercase text-sm">法的情報</h4>
                    <ul class="text-sm space-y-4 font-bold text-slate-600">
                        <li><a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>" class="hover:text-cyan-500 transition-colors">利用規約</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>" class="hover:text-cyan-500 transition-colors">プライバシーポリシー</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/legal/' ) ); ?>" class="hover:text-cyan-500 transition-colors">特定商取引法に基づく表記</a></li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 border-t border-slate-200 text-[10px] md:text-xs text-center font-black tracking-widest text-slate-600">
                &copy; <?php echo date('Y'); ?> YORUO NAVI. ALL RIGHTS RESERVED.
            </div>
        </div>
    </footer>

    <!-- Mobile Navigation -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-[55] shadow-[0_-5px_15px_rgba(0,0,0,0.05)]">
        <!-- 30秒診断バナー -->
        <a href="#" class="matcher-trigger block w-full bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 transition-all active:scale-[0.98]">
            <div class="relative w-full h-12 flex items-center justify-center overflow-hidden">
                <span class="text-white font-black text-sm tracking-widest">30秒診断でぴったりの職場を！</span>
            </div>
        </a>

        <!-- メインナビ -->
        <div class="bg-white/90 backdrop-blur-md border-t border-slate-200 h-16 flex items-center justify-around px-2">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex flex-col items-center gap-1 transition-colors <?php echo is_front_page() ? 'text-indigo-600' : 'text-slate-400'; ?>">
                <i class="fas fa-home" style="font-size: 20px;"></i>
                <span class="text-[10px] font-black uppercase tracking-tighter">ホーム</span>
            </a>
            <a href="<?php echo esc_url( home_url( '/jobs/' ) ); ?>" class="flex flex-col items-center gap-1 transition-colors <?php echo is_post_type_archive('job') ? 'text-indigo-600' : 'text-slate-400'; ?>">
                <i class="fas fa-search" style="font-size: 20px;"></i>
                <span class="text-[10px] font-black uppercase tracking-tighter">求人探す</span>
            </a>
            <a href="<?php echo esc_url( home_url( '/features/' ) ); ?>" class="flex flex-col items-center gap-1 transition-colors text-slate-400">
                <i class="fas fa-star" style="font-size: 20px;"></i>
                <span class="text-[10px] font-black uppercase tracking-tighter">特集</span>
            </a>
            <a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>" class="flex flex-col items-center gap-1 transition-colors text-slate-400">
                <i class="fas fa-question-circle" style="font-size: 20px;"></i>
                <span class="text-[10px] font-black uppercase tracking-tighter">FAQ</span>
            </a>
            <a href="<?php echo esc_url( home_url( '/profile/' ) ); ?>" class="flex flex-col items-center gap-1 transition-colors text-slate-400">
                <i class="fas fa-user" style="font-size: 20px;"></i>
                <span class="text-[10px] font-black uppercase tracking-tighter">マイページ</span>
            </a>
        </div>
    </nav>

    <!-- Rich Hamburger Menu -->
    <div id="rich-menu-container" class="fixed inset-0 z-[120] hidden overflow-hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md rich-menu-overlay opacity-0 transition-opacity duration-300"></div>
        <div id="rich-menu-panel" class="absolute top-0 right-0 bottom-0 w-full max-w-md bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 shadow-2xl translate-x-full transition-transform duration-500 ease-out flex flex-col overflow-y-auto no-scrollbar">
            <!-- Close Button -->
            <button type="button" class="rich-menu-close absolute top-6 right-6 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition z-10 backdrop-blur-md border border-white/10 active:scale-95">
                <i class="fas fa-times text-xl"></i>
            </button>

            <div class="p-8 pt-20 space-y-10">
                <!-- 1. Account / Actions -->
                <section>
                    <?php if ( is_user_logged_in() ) : 
                        $current_user = wp_get_current_user();
                    ?>
                        <div class="flex items-center gap-5 mb-8">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white font-black text-2xl shadow-lg rotate-3 overflow-hidden">
                                <?php echo esc_html( substr($current_user->display_name, 0, 1) ); ?>
                            </div>
                            <div>
                                <p class="text-white font-black text-lg"><?php echo esc_html($current_user->display_name); ?></p>
                                <p class="text-slate-400 text-xs font-medium"><?php echo esc_html($current_user->user_email); ?></p>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="flex gap-4 mb-8">
                            <a href="<?php echo esc_url( home_url( '/login/' ) ); ?>" class="flex-1 px-6 py-4 rounded-2xl border-2 border-white/10 text-white font-bold text-center hover:bg-white/5 transition active:scale-95">ログイン</a>
                            <a href="<?php echo esc_url( home_url( '/signup/' ) ); ?>" class="flex-1 px-6 py-4 rounded-2xl gradient-cyan text-white font-black text-center hover:brightness-110 transition shadow-lg shadow-cyan-500/20 active:scale-95">新規登録</a>
                        </div>
                    <?php endif; ?>

                    <!-- LINE Promo -->
                    <div class="relative group">
                        <div class="absolute -top-3 left-6 bg-amber-400 text-slate-900 text-[10px] font-black px-4 py-1 rounded-full shadow-lg z-10 animate-bounce">
                            PayPayマネーが当たる！
                        </div>
                        <a href="<?php echo esc_url(get_option('yoruo_navi_line_url', '#')); ?>" target="_blank" class="w-full bg-[#06C755] text-white font-black py-5 rounded-[1.5rem] shadow-xl hover:brightness-110 transition flex items-center justify-center gap-4 border border-white/10">
                            <i class="fab fa-line text-2xl group-hover:rotate-12 transition-transform"></i>
                            <span class="text-lg">LINE無料登録で特典GET</span>
                        </a>
                    </div>
                </section>

                <!-- 2. Quick Menu -->
                <section>
                    <h3 class="text-slate-400 font-black text-xs mb-6 flex items-center gap-3 uppercase tracking-[0.2em]">
                        <span class="w-8 h-[2px] bg-gradient-to-r from-cyan-500 to-transparent"></span>
                        Quick Menu
                    </h3>
                    <div class="grid grid-cols-3 gap-4">
                        <?php
                        // Fetch Dynamic Quick Menu
                        $quick_query = new WP_Query(array(
                            'post_type'      => 'quick_menu',
                            'posts_per_page' => 9,
                            'orderby'        => 'menu_order',
                            'order'          => 'ASC',
                        ));

                        $quick_items = array();
                        if ($quick_query->have_posts()) {
                            while ($quick_query->have_posts()) {
                                $quick_query->the_post();
                                $quick_items[] = array(
                                    'label' => get_the_title(),
                                    'icon'  => get_post_meta(get_the_ID(), '_icon', true) ?: 'fa-tag',
                                    'link'  => get_post_meta(get_the_ID(), '_link', true) ?: '#',
                                    'color' => get_post_meta(get_the_ID(), '_color', true) ?: 'from-slate-500 to-slate-700',
                                );
                            }
                            wp_reset_postdata();
                        } else {
                            // Mock Fallback
                            $quick_items = [
                                ['label' => '日払いOK', 'icon' => 'fa-money-bill-wave', 'link' => '/jobs/?tags[]=日払いOK', 'color' => 'from-emerald-500 to-teal-500'],
                                ['label' => '体験バイト', 'icon' => 'fa-user-check', 'link' => '/jobs/?tags[]=未経験歓迎', 'color' => 'from-blue-500 to-indigo-500'],
                                ['label' => '幹部候補', 'icon' => 'fa-trophy', 'link' => '/jobs/?category[]=幹部候補', 'color' => 'from-amber-500 to-orange-500'],
                                ['label' => '寮・社宅', 'icon' => 'fa-home', 'link' => '/jobs/?tags[]=寮・社宅あり', 'color' => 'from-purple-500 to-pink-500'],
                                ['label' => '30代歓迎', 'icon' => 'fa-arrow-trend-up', 'link' => '/jobs/?tags[]=30代歓迎', 'color' => 'from-rose-500 to-red-500'],
                                ['label' => 'ドライバー', 'icon' => 'fa-car', 'link' => '/jobs/?category[]=送りドライバー', 'color' => 'from-cyan-500 to-blue-500'],
                                ['label' => '30秒診断', 'icon' => 'fa-robot', 'link' => '#', 'color' => 'from-indigo-500 to-purple-500', 'class' => 'matcher-trigger'],
                                ['label' => 'ノウハウ', 'icon' => 'fa-book-open', 'link' => '/#master-guides', 'color' => 'from-yellow-500 to-amber-500'],
                                ['label' => 'マイページ', 'icon' => 'fa-user', 'link' => '/profile/', 'color' => 'from-slate-500 to-slate-700'],
                            ];
                        }

                        foreach ($quick_items as $item) : ?>
                            <a href="<?php echo esc_url($item['link']); ?>" class="<?php echo $item['class'] ?? ''; ?> group relative aspect-square rounded-[2rem] bg-slate-800/50 border border-white/5 hover:border-cyan-500/30 transition-all overflow-hidden backdrop-blur-sm active:scale-95">
                                <div class="absolute inset-0 bg-gradient-to-br <?php echo $item['color']; ?> opacity-0 group-hover:opacity-20 transition-opacity"></div>
                                <div class="relative h-full flex flex-col items-center justify-center gap-3 p-2">
                                    <div class="p-3 rounded-2xl bg-white/5 group-hover:bg-white/10 transition-colors">
                                        <i class="fas <?php echo $item['icon']; ?> text-cyan-400 text-xl"></i>
                                    </div>
                                    <span class="text-white text-[10px] font-black text-center leading-tight tracking-tighter"><?php echo $item['label']; ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- 3. Banners -->
                <section>
                    <h3 class="text-slate-400 font-black text-xs mb-6 flex items-center gap-3 uppercase tracking-[0.2em]">
                        <span class="w-8 h-[2px] bg-gradient-to-r from-blue-500 to-transparent"></span>
                        Discover Features
                    </h3>
                    <div class="space-y-5">
                        <?php
                        // Fetch Dynamic Menu Banners
                        $banner_query = new WP_Query(array(
                            'post_type'      => 'menu_banner',
                            'posts_per_page' => 2,
                        ));

                        $banners = array();
                        if ($banner_query->have_posts()) {
                            while ($banner_query->have_posts()) {
                                $banner_query->the_post();
                                $banners[] = array(
                                    'title'    => get_the_title(),
                                    'subtitle' => get_post_meta(get_the_ID(), '_subtitle', true),
                                    'link'     => get_post_meta(get_the_ID(), '_link', true) ?: '#',
                                    'gradient' => get_post_meta(get_the_ID(), '_gradient', true) ?: 'from-slate-600 to-slate-800',
                                    'image'    => get_the_post_thumbnail_url(get_the_ID(), 'large') ?: 'https://images.unsplash.com/photo-1552581234-26160f608093?auto=format&fit=crop&q=80&w=800',
                                );
                            }
                            wp_reset_postdata();
                        } else {
                            // Mock Fallback
                            $banners = [
                                [
                                    'title'    => '成功ロードマップ',
                                    'subtitle' => '3ヶ月で月収50万円を目指す',
                                    'link'     => '#',
                                    'gradient' => 'from-indigo-600 to-purple-600',
                                    'image'    => 'https://images.unsplash.com/photo-1552581234-26160f608093?auto=format&fit=crop&q=80&w=800',
                                ],
                                [
                                    'title'    => '入社お祝い金増額中',
                                    'subtitle' => '最大10万円プレゼント',
                                    'link'     => '#',
                                    'gradient' => 'from-amber-600 to-orange-600',
                                    'image'    => 'https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?auto=format&fit=crop&q=80&w=800',
                                ]
                            ];
                        }

                        foreach ($banners as $banner) : ?>
                            <a href="<?php echo esc_url($banner['link']); ?>" class="group relative block w-full h-36 rounded-[2rem] overflow-hidden border border-white/10 shadow-xl active:scale-[0.98]">
                                <img src="<?php echo esc_url($banner['image']); ?>" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-[2000ms]">
                                <div class="absolute inset-0 bg-gradient-to-r <?php echo esc_attr($banner['gradient']); ?> opacity-80 group-hover:opacity-60 transition-opacity"></div>
                                <div class="absolute inset-0 flex flex-col items-start justify-center p-8">
                                    <h4 class="text-white font-black text-xl mb-1 drop-shadow-lg"><?php echo esc_html($banner['title']); ?></h4>
                                    <p class="text-white/80 text-xs font-bold uppercase tracking-widest"><?php echo esc_html($banner['subtitle']); ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- 4. Contact -->
                <section>
                    <h3 class="text-slate-400 font-black text-xs mb-6 flex items-center gap-3 uppercase tracking-[0.2em]">
                        <span class="w-8 h-[2px] bg-gradient-to-r from-emerald-500 to-transparent"></span>
                        Contact Us
                    </h3>
                    <div class="space-y-4">
                        <a href="<?php echo esc_url(get_option('yoruo_navi_line_url', '#')); ?>" target="_blank" class="w-full bg-slate-100 text-slate-900 font-black py-5 rounded-[1.5rem] hover:brightness-110 transition flex items-center justify-center gap-3 shadow-lg active:scale-95">
                            <i class="fab fa-line text-2xl text-[#06C755]"></i>
                            <span>LINEでお問い合わせ</span>
                        </a>
                        <?php 
                        $phone = get_option('yoruo_navi_phone');
                        if ($phone) : 
                            $tel_link = 'tel:' . str_replace('-', '', $phone);
                        ?>
                            <a href="<?php echo esc_url($tel_link); ?>" class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-black py-5 rounded-[1.5rem] hover:brightness-110 transition flex items-center justify-center gap-4 shadow-xl shadow-blue-900/40 active:scale-95">
                                <div class="p-2 bg-white/10 rounded-xl">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div class="text-left">
                                    <div class="text-base">電話で相談する</div>
                                    <div class="text-[10px] opacity-70 font-bold uppercase tracking-widest">Available 12:00〜22:00</div>
                                </div>
                            </a>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- 5. Footer Links -->
                <section class="pt-6 border-t border-white/5">
                    <div class="grid grid-cols-2 gap-x-8 gap-y-4">
                        <a href="<?php echo esc_url( home_url('/terms/') ); ?>" class="text-slate-500 hover:text-white transition-colors text-xs font-bold uppercase tracking-widest">Terms</a>
                        <a href="<?php echo esc_url( home_url('/privacy/') ); ?>" class="text-slate-500 hover:text-white transition-colors text-xs font-bold uppercase tracking-widest">Privacy</a>
                        <a href="<?php echo esc_url( home_url('/about/') ); ?>" class="text-slate-500 hover:text-white transition-colors text-xs font-bold uppercase tracking-widest">Company</a>
                        <?php if ( is_user_logged_in() ) : ?>
                            <a href="<?php echo wp_logout_url( home_url() ); ?>" class="flex items-center gap-2 text-red-500 hover:text-red-400 transition-colors text-xs font-black uppercase tracking-widest">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Final Footer -->
                <div class="text-center text-slate-600 text-[10px] font-black uppercase tracking-[0.3em] pb-12">
                    &copy; <?php echo date('Y'); ?> YORUO NAVI
                </div>
            </div>
        </div>
    </div>

    <!-- Matcher Modal Container -->
    <div id="matcher-modal-container" class="fixed inset-0 z-[110] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md matcher-modal-overlay"></div>
        <div class="relative w-full h-full flex flex-col md:items-center md:justify-center">
            <div id="matcher-content-wrapper" class="w-full md:max-w-2xl h-full md:h-[80vh] bg-slate-50 md:rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col animate-nurutto">
                <!-- Header -->
                <div class="bg-white p-4 md:p-6 border-b border-slate-200 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/20">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-slate-800 text-sm md:text-base leading-tight">30秒適職診断</h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Job Matching AI</p>
                        </div>
                    </div>
                    <button type="button" class="matcher-modal-close w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center hover:bg-slate-200 transition-all active:scale-90">
                        <i class="fas fa-times text-slate-400"></i>
                    </button>
                </div>

                <!-- Chat History -->
                <div id="matcher-chat-history" class="flex-grow overflow-y-auto p-4 md:p-8 space-y-4 no-scrollbar">
                    <!-- Messages will appear here -->
                </div>

                <!-- Input Area (Options) -->
                <div id="matcher-input-area" class="bg-white border-t border-slate-100 p-4 md:p-8 pb-10 md:pb-8 shadow-[0_-4px_20px_rgba(0,0,0,0.05)] shrink-0">
                    <div id="matcher-options-grid" class="grid grid-cols-2 gap-3 mb-4">
                        <!-- Options will be rendered here -->
                    </div>
                    <div id="matcher-progress-bar" class="flex justify-between items-center px-1">
                        <span id="matcher-step-info" class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Question 1 / 5</span>
                        <div id="matcher-dots" class="flex gap-1.5">
                            <!-- Progress dots -->
                        </div>
                    </div>
                </div>

                <!-- Results View (Hidden initially) -->
                <div id="matcher-results-view" class="absolute inset-0 bg-white z-[120] hidden overflow-y-auto p-4 md:p-12 pb-24">
                    <div class="text-center mb-10">
                        <div class="inline-block px-4 py-1 bg-indigo-600 text-white text-[10px] font-black rounded-full mb-4 tracking-widest uppercase">MATCHING RESULT</div>
                        <h2 class="text-3xl font-black text-slate-900 mb-2 italic">あなたへの提案</h2>
                        <p class="text-slate-400 text-xs font-bold">条件に最も近い求人を解析しました</p>
                    </div>

                    <div id="matcher-results-list" class="space-y-4 mb-10">
                        <!-- Result Job Cards or Summary -->
                        <div class="p-8 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200 text-center">
                            <i class="fas fa-search text-slate-300 mb-4" style="font-size: 48px;"></i>
                            <p class="text-slate-500 font-black">AIが分析した結果、あなたにぴったりの求人が見つかりました！</p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        <button id="matcher-go-results" class="w-full py-5 bg-indigo-600 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95 text-lg">
                            診断結果の一覧を見る
                        </button>
                        <button id="matcher-restart" class="w-full py-4 bg-slate-100 text-slate-500 font-black rounded-2xl hover:bg-slate-200 transition-all active:scale-95 text-sm">
                            もう一度診断する
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
