<?php
/**
 * The front page template file
 */

get_header();

// Mock Slider Data
$slides = [
    [
        'image' => 'https://images.unsplash.com/photo-1566417713940-fe7c737a9ef2?auto=format&fit=crop&q=80&w=1600',
        'title' => '稼げる環境、ここにあり。',
        'subtitle' => '新宿・六本木・銀座。主要エリアの求人を網羅。',
        'badge' => 'AREA RANKING #1',
    ],
    [
        'image' => 'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?auto=format&fit=crop&q=80&w=1600',
        'title' => '未経験から、プロの黒服へ。',
        'subtitle' => 'キャリアアップを夜男ナビが徹底サポート。',
        'badge' => 'EDUCATION SUPPORT',
    ],
    [
        'image' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?auto=format&fit=crop&q=80&w=1600',
        'title' => '入社お祝い金キャンペーン',
        'subtitle' => '今なら最大50,000円を即日プレゼント中。',
        'badge' => 'LIMITED CAMPAIGN',
    ],
];

// Data from theme-data.php
$categories = get_yoruo_categories();
$guides = get_yoruo_guides();
$quick_tags = get_yoruo_quick_tags();
$salary_options = get_yoruo_salary_options();
$work_styles = get_yoruo_work_styles();
$prefectures = get_yoruo_prefectures();
?>

<div class="animate-fade-in bg-white min-h-screen">
    <!-- Slider Section -->
    <section class="relative pt-6 pb-12 md:pt-10 md:pb-16 overflow-hidden bg-white">
        <div class="relative w-full max-w-[1600px] mx-auto overflow-hidden text-center">
            <div id="main-slider" class="relative flex items-center justify-center w-full aspect-[21/9] md:aspect-[21/7] max-h-[600px]">
                <div class="slider-track flex transition-transform duration-700 ease-out h-full items-center">
                    <?php foreach ($slides as $index => $slide) : ?>
                        <div class="slider-item relative flex-shrink-0 h-full px-1 md:px-2 transition-all duration-700 ease-out cursor-pointer" style="width: 75%;">
                            <div class="relative w-full h-full rounded-lg md:rounded-[1.5rem] overflow-hidden shadow-lg border border-slate-200">
                                <img src="<?php echo $slide['image']; ?>" alt="<?php echo $slide['title']; ?>" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex flex-col justify-end p-3 md:p-8 text-left">
                                    <div class="mb-0.5 md:mb-1">
                                        <span class="inline-block px-1.5 py-0.5 bg-amber-500 text-slate-900 text-[7px] md:text-[10px] font-black rounded uppercase tracking-widest">
                                            <?php echo $slide['badge']; ?>
                                        </span>
                                    </div>
                                    <h2 class="text-[10px] sm:text-base md:text-3xl font-black text-white leading-tight">
                                        <?php echo $slide['title']; ?>
                                    </h2>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="slider-prev absolute left-[3%] md:left-[10%] lg:left-[20%] z-30 w-8 h-8 md:w-12 md:h-12 rounded bg-black/40 backdrop-blur-sm flex items-center justify-center text-white hover:bg-black/60 transition-all active:scale-90">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="slider-next absolute right-[3%] md:right-[10%] lg:left-[20%] z-30 w-8 h-8 md:w-12 md:h-12 rounded bg-black/40 backdrop-blur-sm flex items-center justify-center text-white hover:bg-black/60 transition-all active:scale-90">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- Main Grid -->
    <div class="container mx-auto px-4 md:-mt-8 mb-12 md:mb-20 relative z-40">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8">
            <!-- Left Sidebar -->
            <div class="lg:col-span-4 space-y-6 md:space-y-8 order-1 lg:order-none">
                <!-- Recommended Categories -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-200">
                    <h3 class="text-lg md:text-xl font-black text-slate-800 mb-6 flex items-center gap-3">
                        <span class="w-1.5 h-7 bg-amber-500 rounded-full"></span>
                        おすすめカテゴリ
                    </h3>
                    <div class="grid grid-cols-1 gap-3">
                        <?php foreach (array_slice($categories, 0, 6) as $cat) : ?>
                            <a href="<?php echo esc_url(add_query_arg('category[]', $cat['id'], get_post_type_archive_link('job'))); ?>" class="flex items-center justify-between p-4 rounded-2xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-200 group active:scale-[0.98]">
                                <div class="flex items-center gap-4">
                                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition shadow-sm">
                                        <i class="fas <?php echo $cat['icon']; ?>"></i>
                                    </div>
                                    <span class="text-sm md:text-base font-bold text-slate-700"><?php echo $cat['name']; ?></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] md:text-xs text-slate-400 font-black tracking-widest"><?php echo $cat['count']; ?>件</span>
                                    <i class="fas fa-chevron-right text-slate-300"></i>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- MASTER GUIDE -->
                <div class="bg-white rounded-[2.5rem] p-6 md:p-8 border border-slate-200 shadow-xl space-y-6 overflow-hidden relative">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-cyan-100/50 rounded-full blur-3xl pointer-events-none"></div>
                    <h3 class="text-sm md:text-base font-black text-slate-800 flex items-center gap-2 justify-center md:justify-start">
                        <i class="fas fa-compass text-cyan-500"></i> MASTER GUIDE
                    </h3>
                    <div class="grid grid-cols-1 gap-4 relative z-10">
                        <?php foreach ($guides as $guide) : ?>
                            <button class="group relative p-5 rounded-3xl border border-slate-100 bg-gradient-to-br from-white to-slate-50 text-left overflow-hidden transition-all hover:scale-[1.02] hover:border-cyan-200 hover:shadow-lg active:scale-[0.98] shadow-sm flex items-center gap-5">
                                <div class="shrink-0 w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center border border-slate-200 group-hover:bg-cyan-50 group-hover:border-cyan-200 transition-all">
                                    <i class="fas <?php echo $guide['icon']; ?> text-slate-600 group-hover:text-cyan-600" style="font-size: 24px;"></i>
                                </div>
                                <div class="flex-grow">
                                    <div class="flex items-center justify-between mb-1">
                                        <h4 class="text-sm md:text-base font-black text-slate-800 group-hover:text-cyan-700 transition-colors"><?php echo $guide['title']; ?></h4>
                                        <span class="text-[7px] md:text-[8px] font-black text-amber-500 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200 animate-pulse">
                                            <?php echo $guide['micro']; ?>
                                        </span>
                                    </div>
                                    <p class="text-[10px] md:text-xs text-slate-500 group-hover:text-slate-600 transition-colors leading-snug line-clamp-1"><?php echo $guide['copy']; ?></p>
                                </div>
                                <i class="fas fa-chevron-right text-slate-400 group-hover:text-cyan-500 group-hover:translate-x-1 transition-all"></i>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Main Area -->
            <div class="lg:col-span-8 space-y-10 md:space-y-16">
                <!-- Search Section -->
                <div class="bg-white rounded-[2.5rem] p-6 md:p-10 shadow-2xl shadow-cyan-900/10 text-slate-800 relative overflow-hidden border border-slate-100 transition-all duration-500">
                    <div class="absolute -top-24 -right-24 w-64 h-64 bg-cyan-100/50 rounded-full blur-[100px] pointer-events-none"></div>
                    <form id="main-search-form" action="<?php echo get_post_type_archive_link('job'); ?>" method="get" class="relative z-10 flex flex-col">
                        <div class="flex flex-col md:flex-row gap-3">
                            <div class="relative group flex-grow">
                                <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-cyan-500 transition-colors">
                                    <i class="fas fa-search" style="font-size: 22px;"></i>
                                </div>
                                <input type="text" name="keyword" placeholder="エリア・キーワードを入力（例：新宿 日払い 30代）" class="w-full bg-slate-50 border-2 border-slate-200 hover:border-cyan-200 focus:border-cyan-500 rounded-2xl py-5 pl-14 pr-6 text-base font-bold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-cyan-500/10 transition-all shadow-inner">
                            </div>
                            <button type="button" id="search-accordion-toggle" class="flex items-center justify-center gap-2 px-6 py-5 rounded-2xl font-black bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200 transition-all active:scale-95 whitespace-nowrap">
                                <i class="fas fa-filter"></i>
                                こだわり検索
                                <i id="accordion-arrow" class="fas fa-chevron-right transition-transform duration-300"></i>
                            </button>
                        </div>

                        <!-- Accordion Content -->
                        <div id="search-accordion-content" class="max-h-0 opacity-0 overflow-hidden transition-all duration-500 ease-in-out">
                            <div class="pt-8 space-y-8">
                                <div class="space-y-3">
                                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                                        <i class="fas fa-bolt text-amber-500 animate-pulse"></i> 人気の条件から即検索
                                    </p>
                                    <div class="flex gap-2.5 overflow-x-auto pb-1 no-scrollbar">
                                        <?php foreach ($quick_tags as $tag) : ?>
                                            <label class="flex-shrink-0 cursor-pointer">
                                                <input type="checkbox" name="tags[]" value="<?php echo esc_attr($tag); ?>" class="peer sr-only">
                                                <span class="block px-5 py-2.5 bg-slate-50 text-slate-600 border border-slate-200 rounded-full text-xs font-black peer-checked:bg-cyan-500 peer-checked:text-white peer-checked:border-cyan-500 peer-checked:shadow-lg peer-checked:shadow-cyan-500/20 hover:bg-white hover:border-cyan-300 transition-all active:scale-95 whitespace-nowrap">
                                                    <?php echo esc_html($tag); ?>
                                                </span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <button type="button" class="search-modal-trigger flex flex-col items-center justify-center gap-3 p-5 bg-slate-50 border border-slate-200 hover:border-cyan-500 hover:bg-white rounded-2xl transition-all group active:scale-95 shadow-sm" data-modal="map">
                                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-cyan-500 group-hover:scale-110 transition-transform shadow-md border border-slate-100">
                                            <i class="fas fa-map-marker-alt" style="font-size: 24px;"></i>
                                        </div>
                                        <span class="text-[11px] md:text-xs font-black tracking-tighter text-slate-700">エリアで探す</span>
                                    </button>
                                    <button type="button" class="search-modal-trigger flex flex-col items-center justify-center gap-3 p-5 bg-slate-50 border border-slate-200 hover:border-indigo-500 hover:bg-white rounded-2xl transition-all group active:scale-95 shadow-sm" data-modal="job-type">
                                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-indigo-500 group-hover:scale-110 transition-transform shadow-md border border-slate-100">
                                            <i class="fas fa-briefcase" style="font-size: 24px;"></i>
                                        </div>
                                        <span class="text-[11px] md:text-xs font-black tracking-tighter text-slate-700">職種で探す</span>
                                    </button>
                                    <button type="button" class="search-modal-trigger flex flex-col items-center justify-center gap-3 p-5 bg-slate-50 border border-slate-200 hover:border-emerald-500 hover:bg-white rounded-2xl transition-all group active:scale-95 shadow-sm" data-modal="salary">
                                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-emerald-500 group-hover:scale-110 transition-transform shadow-md border border-slate-100">
                                            <i class="fas fa-wallet" style="font-size: 24px;"></i>
                                        </div>
                                        <span class="text-[11px] md:text-xs font-black tracking-tighter text-slate-700">給与で探す</span>
                                    </button>
                                    <button type="button" class="search-modal-trigger flex flex-col items-center justify-center gap-3 p-5 bg-slate-50 border border-slate-200 hover:border-blue-500 hover:bg-white rounded-2xl transition-all group active:scale-95 shadow-sm" data-modal="work-style">
                                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform shadow-md border border-slate-100">
                                            <i class="fas fa-layer-group" style="font-size: 24px;"></i>
                                        </div>
                                        <span class="text-[11px] md:text-xs font-black tracking-tighter text-slate-700">働き方で探す</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full gradient-cyan hover:brightness-110 text-white font-black py-5 rounded-2xl shadow-2xl shadow-cyan-500/30 transition-all active:scale-[0.98] text-lg flex items-center justify-center gap-3 mt-6">
                            <i class="fas fa-search"></i>
                            この条件で検索する
                        </button>
                    </form>
                </div>

                <!-- Featured Jobs -->
                <section>
                    <div class="flex items-center justify-between mb-8 px-2">
                        <div class="flex items-center gap-4">
                            <span class="w-2 h-10 gradient-cyan rounded-full"></span>
                            <div>
                                <h3 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">注目の求人</h3>
                                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Featured Opportunities</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                        <?php
                        $featured_query = new WP_Query(array(
                            'post_type' => 'job',
                            'posts_per_page' => 4,
                            'status' => 'publish'
                        ));

                        if ($featured_query->have_posts()) :
                            while ($featured_query->have_posts()) : $featured_query->the_post();
                                $area = get_post_meta(get_the_ID(), 'area', true);
                                $salary = get_post_meta(get_the_ID(), 'salary', true);
                                ?>
                                <div class="bg-white rounded-[2.5rem] overflow-hidden border border-slate-100 shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all duration-500 group">
                                    <div class="relative h-60 overflow-hidden">
                                        <?php if (has_post_thumbnail()) : the_post_thumbnail('large', array('class' => 'w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000')); else : ?>
                                            <img src="https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?auto=format&fit=crop&q=80&w=800" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                                        <?php endif; ?>
                                        <div class="absolute top-4 left-4">
                                            <span class="px-4 py-1.5 bg-white/95 backdrop-blur-md rounded-xl text-[10px] font-black text-slate-800 shadow-xl border border-white/20">
                                                <i class="fas fa-map-marker-alt text-cyan-500 mr-1"></i> <?php echo esc_html($area); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-8">
                                        <h4 class="text-xl font-black text-slate-800 mb-4 group-hover:text-cyan-600 transition-colors line-clamp-1"><?php the_title(); ?></h4>
                                        <div class="flex items-center justify-between mb-6">
                                            <div class="flex flex-col">
                                                <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">想定月収</span>
                                                <span class="text-2xl font-black text-cyan-600 italic tracking-tighter"><?php echo esc_html($salary); ?></span>
                                            </div>
                                        </div>
                                        <a href="<?php the_permalink(); ?>" class="block w-full py-4 bg-slate-900 text-white text-center rounded-2xl font-black hover:bg-cyan-600 transition-all shadow-lg shadow-slate-200 active:scale-95">詳細を見る</a>
                                    </div>
                                </div>
                            <?php endwhile; wp_reset_postdata(); endif; ?>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<!-- Modal Container (Outside Main Content) -->
<div id="search-modal-container" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm modal-overlay"></div>
    <div class="relative w-full h-full flex items-center justify-center p-4">
        <div id="modal-content-wrapper" class="w-full max-w-2xl bg-white rounded-[2.5rem] shadow-2xl overflow-hidden animate-nurutto">
            <div class="p-8 md:p-10">
                <div class="flex items-center justify-between mb-8">
                    <h3 id="modal-title" class="text-xl md:text-2xl font-black text-slate-800">条件を選択</h3>
                    <button type="button" class="modal-close w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center hover:bg-slate-200 transition-all active:scale-90">
                        <i class="fas fa-times text-slate-400"></i>
                    </button>
                </div>
                
                <div id="modal-body" class="max-h-[60vh] overflow-y-auto no-scrollbar">
                    <!-- Target content -->
                </div>

                <button type="button" class="modal-close w-full mt-10 py-5 bg-slate-900 text-white font-black rounded-2xl shadow-xl shadow-slate-200 transition-all active:scale-95">
                    選択した条件を確定する
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Templates -->
<template id="template-job-type">
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        <?php foreach ($categories as $cat) : ?>
            <label class="cursor-pointer group">
                <input type="checkbox" name="category[]" value="<?php echo esc_attr($cat['id']); ?>" class="peer sr-only modal-sync-input" data-target="category[]">
                <span class="flex flex-col items-center justify-center gap-3 p-6 bg-slate-50 border border-slate-200 rounded-2xl peer-checked:bg-white peer-checked:border-indigo-500 peer-checked:shadow-lg peer-checked:shadow-indigo-500/10 group-hover:bg-white transition-all">
                    <i class="fas <?php echo $cat['icon']; ?> text-slate-400 peer-checked:text-indigo-600" style="font-size: 24px;"></i>
                    <span class="text-[10px] font-black text-slate-700 text-center leading-tight"><?php echo esc_html($cat['name']); ?></span>
                </span>
            </label>
        <?php endforeach; ?>
    </div>
</template>

<template id="template-salary">
    <div class="grid grid-cols-1 gap-3">
        <?php foreach ($salary_options as $option) : ?>
            <label class="cursor-pointer group block">
                <input type="radio" name="salary" value="<?php echo esc_attr($option['id']); ?>" class="peer sr-only modal-sync-input" data-target="salary">
                <span class="flex items-center justify-between p-6 bg-slate-50 border border-slate-200 rounded-2xl peer-checked:bg-white peer-checked:border-emerald-500 peer-checked:shadow-lg peer-checked:shadow-emerald-500/10 group-hover:bg-white transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 peer-checked:bg-emerald-50 peer-checked:text-emerald-500 transition-colors">
                            <i class="fas <?php echo $option['icon']; ?>"></i>
                        </div>
                        <span class="text-sm font-black text-slate-700"><?php echo esc_html($option['name']); ?></span>
                    </div>
                    <i class="fas fa-check text-emerald-500 opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                </span>
            </label>
        <?php endforeach; ?>
    </div>
</template>

<template id="template-work-style">
    <div class="grid grid-cols-2 gap-3">
        <?php foreach ($work_styles as $style) : ?>
            <label class="cursor-pointer group block">
                <input type="checkbox" name="style[]" value="<?php echo esc_attr($style['id']); ?>" class="peer sr-only modal-sync-input" data-target="style[]">
                <span class="flex flex-col items-center justify-center gap-4 p-6 bg-slate-50 border border-slate-200 rounded-2xl peer-checked:bg-white peer-checked:border-blue-500 peer-checked:shadow-lg peer-checked:shadow-blue-500/10 group-hover:bg-white transition-all text-center">
                    <i class="fas <?php echo $style['icon']; ?> text-slate-400 peer-checked:text-blue-600" style="font-size: 24px;"></i>
                    <span class="text-xs font-black text-slate-700"><?php echo esc_html($style['name']); ?></span>
                </span>
            </label>
        <?php endforeach; ?>
    </div>
</template>

<template id="template-map">
    <div class="w-full flex flex-col bg-slate-50">
        <!-- Map Container -->
        <div id="japan-map-container" class="w-full relative overflow-hidden bg-slate-50/50 rounded-3xl">
            <!-- Japan Map will be rendered here -->
        </div>
        
        <!-- Instructions -->
        <div class="p-4 bg-white border-t border-slate-100">
            <p class="text-sm text-slate-600 text-center">
                <i class="fas fa-info-circle text-indigo-600 mr-2"></i>
                地図上の地域をクリックして都道府県を選択してください
            </p>
        </div>
    </div>
</template>

<?php
get_footer();
