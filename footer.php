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
                        <li><a href="<?php echo esc_url( home_url( '/matcher/' ) ); ?>" class="hover:text-cyan-500 transition-colors">30秒診断</a></li>
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
        <a href="<?php echo esc_url( home_url( '/matcher/' ) ); ?>" class="block w-full bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 transition-all active:scale-[0.98]">
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
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
