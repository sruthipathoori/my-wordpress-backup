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
    <section class="resource-single-banner" style="background-image: url('http://localhost/wordpress/wp-content/uploads/2026/04/single-resource_BG.png');">
        <div class="resource-banner-overlay"></div>
        <div class="container resource-banner-content">
            <div class="resource-single-breadcrumb">
            <div class="container" >
                <nav>
                    <a href="<?php echo esc_url(site_url('/')); ?>">Home</a>
                    <span class="sep">›</span>
                    <?php if ($group): ?>
                    <span><?php echo esc_html($group); ?></span>
                    <?php endif; ?>
                </nav>
            </div>
            <h1 class="resource-banner-title"><?php the_title(); ?></h1>
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

    <div class="resource-navigation container">

        <div class="nav-left" style="margin-left:60px;">
            <?php
            $prev_post = get_previous_post(true, '', 'resource_group');
            if ($prev_post):
            ?>
            <a href="<?php echo get_permalink($prev_post->ID); ?>">
                ← Previous Post
            </a>
            <?php endif; ?>
        </div>

        <div class="nav-right" style="margin-right:70px;" >
            <?php
            $next_post = get_next_post(true, '', 'resource_group');
            if ($next_post):
            ?>
            <a href="<?php echo get_permalink($next_post->ID); ?>">
                Next Post →
            </a>
            <?php endif; ?>
        </div>

    </div>

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
    $image_url = ($image && is_array($image)) ? reset($image)['url'] : get_the_post_thumbnail_url(get_the_ID(), 'medium');

    ?>
    
    <div class="col-md-4 mb-4">
    <a href="<?php the_permalink(); ?>" class="related-card">
    <div class="related-img">
    <img src="<?php echo esc_url($image_url); ?>" alt="<?php the_title(); ?>">
    </div>
    <h4 class="related-heading" style="color: #ffffff;"><?php the_title(); ?></h4>
    </a>
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