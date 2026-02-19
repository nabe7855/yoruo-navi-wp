<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class('antialiased bg-slate-50'); ?>>
<?php wp_body_open(); ?>

<div id="page" class="min-h-screen flex flex-col">
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-cyan-100 text-slate-800 shadow-sm h-14 md:h-20">
        <div class="container mx-auto px-4 h-full flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-2 group">
                    <span class="text-xl md:text-3xl font-black bg-gradient-to-r from-cyan-500 to-blue-500 bg-clip-text text-transparent group-hover:brightness-110 transition tracking-tighter">
                        夜男ナビ <span class="text-sm md:text-base font-bold bg-clip-text text-transparent bg-gradient-to-r from-cyan-400 to-blue-300">-ヨルオナビ-</span>
                    </span>
                </a>
            </div>

            <nav class="hidden lg:flex items-center gap-10 text-sm font-bold">
                <a href="<?php echo esc_url( home_url( '/jobs/' ) ); ?>" class="hover:text-cyan-500 transition-colors text-slate-600">求人を探す</a>
                <a href="<?php echo esc_url( home_url( '/features/' ) ); ?>" class="hover:text-cyan-500 transition-colors text-slate-600">特集</a>
                <a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>" class="hover:text-cyan-500 transition-colors text-slate-600">よくある質問</a>
                <a href="<?php echo esc_url( home_url( '/matcher/' ) ); ?>" class="hover:text-cyan-500 transition-colors text-slate-600">30秒診断</a>
                <a href="<?php echo esc_url( home_url( '/business/' ) ); ?>" class="hover:text-cyan-500 transition-colors border-l border-slate-200 pl-10 text-slate-500">店舗様向け</a>
            </nav>

            <div class="flex items-center gap-3 md:gap-4">
                <?php if ( is_user_logged_in() ) : ?>
                    <div class="flex items-center gap-3">
                        <?php if (current_user_can('store_owner')) : ?>
                            <a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>" class="flex items-center gap-2 px-4 py-2 rounded-full bg-indigo-600 text-white hover:bg-indigo-700 transition text-xs font-bold shadow-lg shadow-indigo-200">
                                <i class="fas fa-store" style="font-size: 14px;"></i>
                                <span class="hidden sm:inline">店舗管理</span>
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo esc_url( home_url( '/profile/' ) ); ?>" class="flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 hover:bg-slate-50 transition text-xs font-bold text-slate-700">
                            <i class="fas fa-user text-cyan-500" style="font-size: 14px;"></i>
                            <span class="hidden sm:inline">マイページ</span>
                        </a>
                        <a href="<?php echo wp_logout_url( home_url() ); ?>" class="text-xs font-bold text-slate-400 hover:text-red-400 transition px-2">
                            <i class="fas fa-sign-out-alt" style="font-size: 16px;"></i>
                        </a>
                    </div>
                <?php else : ?>
                    <a href="<?php echo esc_url( home_url( '/login/' ) ); ?>" class="hidden sm:flex items-center gap-1.5 px-4 py-2 rounded-full border border-slate-200 hover:bg-slate-50 transition text-xs font-bold text-slate-600">
                        <i class="fas fa-sign-in-alt" style="font-size: 14px;"></i>
                        <span>ログイン</span>
                    </a>
                    <a href="<?php echo esc_url( home_url( '/signup/' ) ); ?>" class="flex items-center gap-1.5 px-4 md:px-6 py-2 md:py-3 rounded-full gradient-cyan text-white font-black hover:brightness-110 transition text-xs md:text-sm shadow-lg shadow-cyan-500/20">
                        <i class="fas fa-user-plus" style="font-size: 16px;"></i>
                        <span>新規登録</span>
                    </a>
                <?php endif; ?>
                
                <button id="menu-toggle" class="lg:hidden p-2 text-slate-600 hover:text-cyan-500 transition">
                    <i class="fas fa-bars" style="font-size: 24px;"></i>
                </button>
            </div>
        </div>
    </header>

    <main id="primary" class="flex-grow pb-28 md:pb-0">
