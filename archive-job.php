<?php
/**
 * The template for displaying Job archive pages
 */

get_header();

// Parse filters from URL
$keyword = isset($_GET['keyword']) ? sanitize_text_field($_GET['keyword']) : '';
$selected_pref = isset($_GET['pref']) ? sanitize_text_field($_GET['pref']) : '';
$selected_cats = isset($_GET['category']) ? (array)$_GET['category'] : array();
$selected_tags = isset($_GET['tags']) ? (array)$_GET['tags'] : array();

// Build Breadcrumbs or Title
$title = $keyword ? '"' . $keyword . '" の検索結果' : '求人一覧';
?>

<div class="container mx-auto px-4 py-8 pb-24">
    <!-- Mobile Filter Toggle -->
    <div class="lg:hidden mb-6">
        <button id="mobile-filter-toggle" class="w-full py-4 bg-white border border-slate-200 rounded-2xl font-black text-slate-700 shadow-sm flex items-center justify-center gap-2">
            <i class="fas fa-filter text-indigo-600"></i> 絞り込み条件を表示
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Sidebar Filters -->
        <aside id="sidebar-filters" class="hidden lg:block lg:col-span-3">
            <div class="bg-white rounded-[2.5rem] border border-slate-200 p-8 sticky top-24 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="font-black text-slate-800 text-lg">絞り込み条件</h3>
                    <a href="<?php echo get_post_type_archive_link('job'); ?>" class="text-[10px] font-black text-slate-400 hover:text-red-500 transition tracking-widest">RESET</a>
                </div>

                <form action="<?php echo get_post_type_archive_link('job'); ?>" method="get" class="space-y-8">
                    <?php if ($keyword) : ?><input type="hidden" name="keyword" value="<?php echo esc_attr($keyword); ?>"><?php endif; ?>

                    <!-- Area Filter -->
                    <div>
                        <h4 class="text-sm font-black text-slate-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-indigo-600"></i> エリアで絞る
                        </h4>
                        <select name="pref" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition">
                            <option value="">全てのエリア</option>
                            <?php
                            $pref_list = ["東京都", "神奈川県", "埼玉県", "千葉県", "大阪府", "京都府", "兵庫県", "愛知県", "福岡県", "北海道", "沖縄県"];
                            foreach ($pref_list as $pref) :
                                echo '<option value="' . esc_attr($pref) . '" ' . selected($selected_pref, $pref, false) . '>' . esc_html($pref) . '</option>';
                            endforeach;
                            ?>
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <h4 class="text-sm font-black text-slate-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-list text-indigo-600"></i> 職種で絞る
                        </h4>
                        <div class="space-y-3">
                            <?php
                            $cat_list = ["キャバクラ", "ガールズバー", "スナック", "ラウンジ", "ホストクラブ", "バー"];
                            foreach ($cat_list as $cat) :
                                $checked = in_array($cat, $selected_cats) ? 'checked' : '';
                                ?>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="category[]" value="<?php echo esc_attr($cat); ?>" <?php echo $checked; ?> class="w-5 h-5 border-2 border-slate-300 rounded-md checked:bg-indigo-600 transition-all">
                                    <span class="text-sm font-bold text-slate-600 group-hover:text-indigo-600 transition-colors"><?php echo esc_html($cat); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black shadow-lg shadow-indigo-200 hover:brightness-110 active:scale-95 transition-all">条件を適用する</button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="lg:col-span-9">
            <!-- Search Results Header -->
            <div class="bg-white rounded-[2rem] border border-slate-200 p-8 shadow-sm mb-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-baseline gap-2">
                        <h2 class="text-xl font-black text-slate-900"><?php echo esc_html($title); ?></h2>
                        <span class="text-sm font-bold text-slate-500"><?php echo $wp_query->found_posts; ?>件</span>
                    </div>
                </div>
            </div>

            <!-- Job List -->
            <div class="space-y-6">
                <?php if ( have_posts() ) : ?>
                    <?php while ( have_posts() ) : the_post(); 
                        $area = get_post_meta(get_the_ID(), 'area', true);
                        $salary = get_post_meta(get_the_ID(), 'salary', true);
                        $category = wp_get_post_terms(get_the_ID(), 'job_category', array('fields' => 'names'));
                        ?>
                        <div class="bg-white rounded-[2rem] border border-slate-200 p-6 md:p-8 shadow-sm hover:shadow-xl transition-all group relative">
                            <div class="flex flex-col md:flex-row gap-8">
                                <div class="w-full md:w-64 h-48 rounded-2xl overflow-hidden shrink-0">
                                    <?php if (has_post_thumbnail()) : the_post_thumbnail('large', ['class' => 'w-full h-full object-cover group-hover:scale-110 transition-transform duration-700']); else : ?>
                                        <img src="https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?auto=format&fit=crop&q=80&w=800" class="w-full h-full object-cover">
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow flex flex-col">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[10px] font-black tracking-widest uppercase"><?php echo !empty($category) ? esc_html($category[0]) : '未設定'; ?></span>
                                        <span class="flex items-center gap-1 text-[10px] font-bold text-slate-400">
                                            <i class="fas fa-map-marker-alt"></i> <?php echo esc_html($area); ?>
                                        </span>
                                    </div>
                                    <h3 class="text-xl md:text-2xl font-black text-slate-800 mb-4 group-hover:text-indigo-600 transition-colors">
                                        <?php the_title(); ?>
                                    </h3>
                                    <div class="mt-auto flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-sm font-bold text-slate-400">給与:</span>
                                            <span class="text-2xl font-black text-indigo-600 tracking-tight"><?php echo esc_html($salary); ?></span>
                                        </div>
                                        <a href="<?php the_permalink(); ?>" class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-black hover:bg-slate-800 transition-all active:scale-95 shadow-lg shadow-slate-200">詳細を見る</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    
                    <!-- Pagination -->
                    <div class="pt-8">
                        <?php the_posts_pagination(array(
                            'mid_size'  => 2,
                            'prev_text' => '<i class="fas fa-chevron-left"></i>',
                            'next_text' => '<i class="fas fa-chevron-right"></i>',
                        )); ?>
                    </div>
                <?php else : ?>
                    <div class="py-24 text-center bg-white rounded-[3rem] border-2 border-dashed border-slate-200 flex flex-col items-center gap-6">
                        <i class="fas fa-search text-slate-200" style="font-size: 64px;"></i>
                        <p class="text-slate-500 font-black text-lg">条件に合う求人が見つかりませんでした</p>
                        <a href="<?php echo get_post_type_archive_link('job'); ?>" class="text-indigo-600 font-black hover:underline px-6 py-2 bg-indigo-50 rounded-full transition">すべての求人を表示する</a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php
get_footer();
