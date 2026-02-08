<?php
/**
 * The main template file
 */

get_header();
?>

<div class="container mx-auto px-4 py-12">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('mb-12 bg-white p-8 rounded-3xl shadow-sm border border-slate-100'); ?>>
                <header class="mb-6">
                    <h2 class="text-2xl font-black text-slate-800">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>
                </header>
                <div class="prose max-w-none text-slate-600">
                    <?php the_excerpt(); ?>
                </div>
            </article>
            <?php
        endwhile;
        the_posts_navigation();
    else :
        echo '<p>記事が見つかりませんでした。</p>';
    endif;
    ?>
</div>

<?php
get_footer();
