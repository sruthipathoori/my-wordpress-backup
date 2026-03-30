<?php get_header(); ?>

<div class="container-fluid resource-single-page">
    <?php if (have_posts()): while (have_posts()): the_post(); 
        $resource_image = rwmb_meta('resource_image', ['size'=>'full'], get_the_ID());
        $img_url = ($resource_image && is_array($resource_image)) ? reset($resource_image)['url'] : get_the_post_thumbnail_url(get_the_ID(), 'large');
        $summary = rwmb_meta('resource_summary', [], get_the_ID());
        $full_desc = rwmb_meta('resource_text', [], get_the_ID());
        if (empty($full_desc)) { $full_desc = apply_filters('the_content', get_the_content()); }

        $terms = get_the_terms(get_the_ID(), 'resource_group');
        $group = ($terms && !is_wp_error($terms)) ? reset($terms)->name : '';
    ?>
    <section class="resource-hero-banner" style="background-image:url('<?php echo esc_url($img_url ?: get_template_directory_uri().'/assets/images/default-resource-banner.jpg'); ?>');">
        <div class="hero-content container">
            <div class="hero-breadcrumb"><a href="<?php echo esc_url(site_url('/')); ?>">Home</a> / <a href="<?php echo esc_url(site_url('/resources')); ?>">Resources</a> / <?php echo esc_html($group); ?></div>
            <h1 class="hero-title"><?php the_title(); ?></h1>
            <div class="hero-subtitle"><?php echo esc_html(get_the_date('M j, Y')); ?></div>
        </div>
    </section>

    <section class="container my-5 resource-single-content">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="resource-summary">
                    <?php echo wp_kses_post($summary); ?>
                </div>
                <div id="resource-description-full">
                    <?php echo wp_kses_post($full_desc); ?>
                </div>
            </div>
            <div class="col-lg-4">
                <a class="btn resource-cta" data-bs-toggle="modal" data-bs-target="#resource_form_modal">Download Resource</a>
            </div>
        </div>
    </section>

    <?php
        $term_ids = (!empty($terms) && !is_wp_error($terms)) ? wp_list_pluck($terms,'term_id') : [];
        if (!empty($term_ids)) {
            $related = new WP_Query([
                'post_type' => 'resource',
                'posts_per_page' => 3,
                'post__not_in' => [get_the_ID()],
                'tax_query' => [[ 'taxonomy'=>'resource_group','field'=>'term_id','terms'=>$term_ids ]],
            ]);
            if ($related->have_posts()):
    ?>
    <section class="container my-5">
        <h3>Read Next</h3>
        <div class="row">
        <?php while ($related->have_posts()): $related->the_post(); ?>
            <div class="col-md-4 mb-3">
                <a href="<?php the_permalink(); ?>" class="related-card">
                    <h5><?php the_title(); ?></h5>
                    <p><?php echo wp_trim_words(get_the_excerpt() ?: get_the_content(), 20, '...'); ?></p>
                </a>
            </div>
        <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </section>
    <?php endif; } ?>

    <div class="modal fade" id="resource_form_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content p-4">
                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title">Download resource</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
               <div class="modal-body">
                    <div id="resource-form"> 
                        <?php echo do_shortcode('[contact-form-7 id="0e81076" title="Resource form"]'); ?>
                    </div>
                   
                </div>
                 
            </div>
        </div>
    </div>

    <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>