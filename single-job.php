<?php
/**
 * The template for displaying all single Job posts
 */

get_header();

while ( have_posts() ) :
    the_post();
    
    // Get Job meta
    $area = get_post_meta(get_the_ID(), 'area', true);
    $city = get_post_meta(get_the_ID(), 'city', true);
    $salary = get_post_meta(get_the_ID(), 'salary', true);
    $employer = get_post_meta(get_the_ID(), 'employer_name', true);
    $qualifications = get_post_meta(get_the_ID(), 'qualifications', true);
    $access = get_post_meta(get_the_ID(), 'access_info', true);
    $salary_details = get_post_meta(get_the_ID(), 'salary_details', true);
    $hours = get_post_meta(get_the_ID(), 'working_hours', true);
    $holidays = get_post_meta(get_the_ID(), 'holidays', true);
    $benefits = get_post_meta(get_the_ID(), 'benefits', true);
    $employment_type = get_post_meta(get_the_ID(), 'employment_type', true);
    
    $categories = wp_get_post_terms(get_the_ID(), 'job_category', array('fields' => 'names'));
?>

<div class="bg-slate-50 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <a href="<?php echo get_post_type_archive_link('job'); ?>" class="mb-8 inline-flex items-center gap-2 text-slate-500 hover:text-indigo-600 transition font-black text-xs md:text-sm bg-white px-6 py-3 rounded-2xl shadow-sm border border-slate-100 active:scale-95">
            <i class="fas fa-arrow-left"></i> 検索結果一覧へ戻る
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-12">
            <div class="lg:col-span-8 space-y-10 md:space-y-16">
                <!-- Main Header Card -->
                <article class="bg-white rounded-[2.5rem] p-6 md:p-12 border border-slate-100 shadow-2xl shadow-indigo-900/5 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50 rounded-full -mr-32 -mt-32 blur-[80px] pointer-events-none opacity-50"></div>

                    <div class="relative z-10">
                        <div class="flex flex-wrap gap-2 mb-8">
                            <?php if (!empty($categories)) : ?>
                                <span class="px-4 py-1.5 bg-indigo-600 text-white text-[10px] md:text-xs font-black rounded-lg tracking-widest uppercase shadow-lg shadow-indigo-200">
                                    <?php echo esc_html($categories[0]); ?>
                                </span>
                            <?php endif; ?>
                            <span class="px-4 py-1.5 bg-slate-100 text-slate-500 text-[10px] md:text-xs font-black rounded-lg tracking-widest uppercase">
                                <?php echo esc_html($employment_type ?: '正社員'); ?>
                            </span>
                        </div>

                        <h1 class="text-2xl md:text-5xl font-black text-slate-900 mb-10 leading-[1.2] tracking-tight">
                            <?php the_title(); ?>
                        </h1>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6 mb-12">
                            <div class="flex items-center p-6 bg-slate-900 rounded-3xl border border-slate-800 shadow-xl group hover:border-amber-500/30 transition-all">
                                <div class="w-14 h-14 bg-amber-500 rounded-2xl flex items-center justify-center mr-5 shrink-0 shadow-lg shadow-amber-500/20 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-money-bill-wave text-white" style="font-size: 28px;"></i>
                                </div>
                                <div>
                                    <span class="block text-[9px] md:text-[10px] text-slate-500 font-black uppercase mb-1 tracking-[0.2em]">想定給与</span>
                                    <span class="text-xl md:text-3xl font-black text-white italic"><?php echo esc_html($salary); ?></span>
                                </div>
                            </div>
                            <div class="flex items-center p-6 bg-white rounded-3xl border border-slate-100 shadow-sm group hover:border-indigo-100 transition-all">
                                <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center mr-5 shrink-0 border border-slate-100 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-map-marker-alt text-slate-400 group-hover:text-indigo-500 transition-colors" style="font-size: 28px;"></i>
                                </div>
                                <div>
                                    <span class="block text-[9px] md:text-[10px] text-slate-400 font-black uppercase mb-1 tracking-[0.2em]">勤務地</span>
                                    <span class="text-sm md:text-lg font-black text-slate-700"><?php echo esc_html($area . ' ' . $city); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="relative mb-12 group rounded-[2rem] overflow-hidden shadow-2xl h-[300px] md:h-[500px]">
                            <?php if (has_post_thumbnail()) : the_post_thumbnail('full', ['class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-[2000ms]']); else : ?>
                                <img src="https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?auto=format&fit=crop&q=80&w=1600" class="w-full h-full object-cover">
                            <?php endif; ?>
                            <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-black/60 to-transparent flex items-end p-8">
                                <p class="text-white font-black text-sm md:text-lg drop-shadow-md"><?php echo esc_html($employer); ?> の現場写真</p>
                            </div>
                        </div>

                        <div class="prose prose-slate max-w-none">
                            <h3 class="text-xl md:text-3xl font-black text-slate-900 flex items-center mb-8 gap-4">
                                <div class="w-2 h-10 bg-indigo-600 rounded-full"></div>
                                仕事内容・メッセージ
                            </h3>
                            <div class="bg-slate-50 rounded-3xl p-8 md:p-10 border border-slate-100">
                                <div class="text-slate-700 leading-[2] whitespace-pre-wrap text-base md:text-xl font-medium tracking-wide">
                                    <?php the_content(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Recruitment Table -->
                <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl overflow-hidden">
                    <div class="px-8 py-10 bg-slate-900 flex items-center justify-between">
                        <h2 class="text-xl md:text-2xl font-black text-white flex items-center gap-4">
                            <i class="fas fa-file-alt text-indigo-400"></i> 募集要項詳細
                        </h2>
                    </div>

                    <div class="divide-y divide-slate-100">
                        <?php 
                        $details = [
                            ['icon' => 'fa-star', 'label' => '経験・資格', 'content' => $qualifications],
                            ['icon' => 'fa-map-marker-alt', 'label' => '交通アクセス', 'content' => $access],
                            ['icon' => 'fa-money-bill-wave', 'label' => '給与・報酬', 'content' => $salary_details],
                            ['icon' => 'fa-clock', 'label' => '勤務時間', 'content' => $hours],
                            ['icon' => 'fa-calendar-alt', 'label' => '休日・休暇', 'content' => $holidays],
                            ['icon' => 'fa-briefcase', 'label' => '雇用形態', 'content' => $employment_type ?: '正社員'],
                            ['icon' => 'fa-shield-alt', 'label' => '待遇・福利厚生', 'content' => $benefits],
                        ];
                        foreach ($details as $row) : if (!$row['content']) continue; ?>
                            <div class="flex flex-col md:flex-row border-b border-slate-100 last:border-0 hover:bg-slate-50/50 transition-colors">
                                <div class="w-full md:w-64 bg-slate-50/50 p-6 md:p-8 shrink-0 flex items-center gap-4">
                                    <div class="text-indigo-500 bg-white p-2 w-10 h-10 rounded-lg shadow-sm flex items-center justify-center">
                                        <i class="fas <?php echo $row['icon']; ?>"></i>
                                    </div>
                                    <span class="text-xs md:text-sm font-black text-slate-600 uppercase tracking-widest"><?php echo $row['label']; ?></span>
                                </div>
                                <div class="flex-grow p-6 md:p-8 bg-white">
                                    <p class="text-sm md:text-base text-slate-700 leading-loose whitespace-pre-wrap font-medium"><?php echo nl2br(esc_html($row['content'])); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Right Floating Sidebar -->
            <div class="lg:col-span-4 lg:relative">
                <div class="sticky top-24 space-y-8">
                    <div class="bg-white rounded-[2.5rem] p-8 md:p-10 border border-slate-100 shadow-2xl shadow-indigo-900/10">
                        <div class="flex items-center gap-5 mb-10 pb-8 border-b border-slate-50">
                            <div class="w-16 h-16 bg-slate-900 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl shadow-slate-200 shrink-0">
                                <i class="fas fa-building"></i>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em] mb-1 block">掲載ストア</span>
                                <h4 class="font-black text-slate-900 text-lg leading-tight"><?php echo esc_html($employer); ?></h4>
                            </div>
                        </div>

                        <div class="space-y-4 mb-10">
                            <button class="w-full py-6 gradient-gold hover:brightness-110 text-slate-900 text-xl font-black rounded-3xl transition transform active:scale-[0.97] shadow-2xl shadow-amber-500/30 flex items-center justify-center group">
                                <span>応募画面へ進む</span>
                                <i class="fas fa-chevron-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                            </button>
                            <button class="w-full py-5 bg-emerald-500 hover:bg-emerald-600 text-white text-lg font-black rounded-3xl transition transform active:scale-[0.97] shadow-xl shadow-emerald-500/20 flex items-center justify-center gap-3">
                                <i class="fab fa-line text-2xl"></i>
                                <span>LINEで質問する</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
endwhile;
get_footer();
