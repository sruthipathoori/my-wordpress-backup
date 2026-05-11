<?php get_header(); ?>

<div class="container-fluid resource-single-page p-0">
    <?php if (have_posts()): while (have_posts()): the_post(); 
        $resource_image = rwmb_meta('resource_image', ['size'=>'full'], get_the_ID());
        $img_url = ($resource_image && is_array($resource_image)) ? reset($resource_image)['url'] : get_the_post_thumbnail_url(get_the_ID(), 'large');
      
        $full_desc = rwmb_meta('resource_text', [], get_the_ID());
        if (empty($full_desc)) { $full_desc = apply_filters('the_content', get_the_content()); }

        $terms = get_the_terms(get_the_ID(), 'resource_group');
        $group = ($terms && !is_wp_error($terms)) ? reset($terms)->name : '';
        $group_link = ($terms && !is_wp_error($terms)) ? get_term_link(reset($terms)) : '#';
    ?>

    <!-- 1. BANNER WITH TITLE OVERLAY -->
    <section class="resource-hero-banner" style="background-image: url('http://localhost/wordpress/wp-content/uploads/2026/04/Resource-Center-Hero-image.jpg');">       <div class="resource-banner-overlay"></div>
        <div class="container resource-banner-content">
            <div class="resource-single-breadcrumb" style="margin-top: 80px; ">
            <div class="container" >
                <nav>
                    <a href="<?php echo esc_url(site_url('/')); ?>">Home</a>
                    <span class="sep">›</span>
                    <?php if ($group): ?>
                    <span><?php echo esc_html($group); ?></span>
                    <?php endif; ?>
                </nav>
            </div>
            <h2 class="resource-banner-title"><?php the_title(); ?></h2>
            <p class="resource-date">
            <?php echo get_the_date('F j, Y'); ?>
            </p>
        </div>
        </div>
    </section>
   

    <!-- 4. FULL DESCRIPTION — centered with side padding -->
    <section class="resource-single-body">
        <div class="container" style="margin-top:40px;">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10 col-12">

                    <div class="resource-single-content" id="resource-description-full">
                        <?php echo wp_kses_post($full_desc); ?>
                    </div>

                </div>
            </div>
        </div>
    </section>

    

   <?php
    $terms = get_the_terms(get_the_ID(), 'resource_group');
    $term_ids = (!empty($terms) && !is_wp_error($terms)) ? wp_list_pluck($terms, 'term_id') : [];

    if (!empty($term_ids)) {

    $related = new WP_Query([
    'post_type'      => 'resource',
    'posts_per_page' => 3,
    'post__not_in'   => [get_the_ID()],
    'tax_query'      => [[
        'taxonomy' => 'resource_group',
        'field'    => 'term_id',
        'terms'    => $term_ids,
    ]],
    ]);

    if ($related->have_posts()):
    ?>
<section class="related-resources-section">

    <div class="container">

        <h2 class="related-title">Related Posts</h2>

        <div class="row">

            <?php while ($related->have_posts()): $related->the_post();

                $image = rwmb_meta('resource_image', ['size' => 'medium'], get_the_ID());

                $image_url = ($image && is_array($image))
                    ? reset($image)['url']
                    : get_the_post_thumbnail_url(get_the_ID(), 'medium');

                $related_terms = get_the_terms(get_the_ID(), 'resource_group');

                $related_group = (!empty($related_terms) && !is_wp_error($related_terms))
                    ? $related_terms[0]->name
                    : '';

            ?>

            <div class="col-lg-4 col-md-6 mb-4">

                <div class="related-resource-card">

                    <!-- IMAGE -->
                    <div class="related-resource-image">

                        <a href="<?php the_permalink(); ?>">

                            <img src="<?php echo esc_url($image_url); ?>"
                                 alt="<?php the_title_attribute(); ?>">

                        </a>

                    </div>

                    <!-- CONTENT -->
                    <div class="related-resource-content">

                        <!-- GROUP -->
                        <!--  -->

                        <!-- TITLE -->
                        <div class="resource-card-content">
                        <h5><?php echo esc_html(get_the_title()); ?></h5>

                        </div>

                        <!-- READ MORE -->
                        <a href="<?php the_permalink(); ?>"
                           class="svg-container download-btn cta-btn related-read-more">
                            <span class="pe-3">Read More</span>
                            <svg class="home-bn-arrow home-bn-arrow-sec"
                                 viewBox="0 0 32 32"
                                 xmlns="http://www.w3.org/2000/svg">
                                <g data-name="Layer 2">
                                    <path d="M1 16a15 15 0 1 1 15 15A15 15 0 0 1 1 16Zm28 0a13 13 0 1 0-13 13 13 13 0 0 0 13-13Z"></path>
                                    <path d="M12.13 21.59 17.71 16l-5.58-5.59a1 1 0 0 1 0-1.41 1 1 0 0 1 1.41 0l6.36 6.36a.91.91 0 0 1 0 1.28L13.54 23a1 1 0 0 1-1.41 0 1 1 0 0 1 0-1.41Z"></path>                                </g>

                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
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