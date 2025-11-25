<?php get_header(); ?>

<div class="container-fluid my-5 px-4"  style="padding-top:60px;">
    <div class="row">
        <div class="col-12 p-0">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <?php
                // --- CUSTOM FIELD FETCHING ---
                $resource_image = rwmb_meta('resource_image', ['size' => 'full'], get_the_ID());
                $img_url = '';
                if ($resource_image && is_array($resource_image)) {
                    $img = reset($resource_image);
                    $img_url = $img['url'];
                }

                // Fetch both Summary (for display) and Description (for PDF)
                $resource_summary = rwmb_meta('resource_summary', [], get_the_ID());
                $resource_description_full = rwmb_meta('resource_text', [], get_the_ID());

                // Fallback: If resource_text is empty, use the main post content
                if (empty($resource_description_full)) {
                    $resource_description_full = get_the_content();
                }

                // Determine which text to display on the page: Summary if available, otherwise trimmed description
                $display_text = !empty($resource_summary)
                    ? $resource_summary
                    : wp_kses_post(wp_trim_words($resource_description_full, 100, '...'));
                ?>

                <section class="resource-hero d-flex flex-column flex-lg-row">
                    <!-- IMAGE FIRST (LEFT SIDE) -->
                    <?php if ($img_url): ?>
                        <div class="col-lg-6 position-relative p-0 order-1">
                            <img src="<?php echo esc_url($img_url); ?>"
                                 alt="<?php echo esc_attr(get_the_title()); ?>"
                                 class="w-100 h-100 object-fit-cover">
                        </div>
                    <?php endif; ?>

                    <!-- TEXT SECOND (RIGHT SIDE) -->
                    <div class="col-lg-6 d-flex align-items-center p-4 order-2">
                        <div class="text-white">
                            <h1 class="mb-4 fs-2 fw-bold" id="resource-title">
                                <?php the_title(); ?>
                            </h1>

                            <div id="resource-summary" class="fs-5 preview-text">
                                <?php echo wp_kses_post($display_text); ?>
                            </div>

                            <div id="resource-description-full" style="display:none;">
                                <?php echo wp_kses_post($resource_description_full); ?>
                            </div>

                            <!-- ORIGINAL GET RESOURCE BUTTON (kept same styling, now below text) -->
                            <div class="mt-4">
                                <a class="svg-container action-click cta-btn" data-bs-toggle="modal" data-bs-target="#resource_form_modal">
                                    <span class="pe-3">Download</span>
                                    <svg class="home-bn-arrow home-bn-arrow-sec" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                                        <g data-name="Layer 2">
                                            <path d="M1 16a15 15 0 1 1 15 15A15 15 0 0 1 1 16Zm28 0a13 13 0 1 0-13 13 13 13 0 0 0 13-13Z" fill="#ffffff"></path>
                                            <path d="M12.13 21.59 17.71 16l-5.58-5.59a1 1 0 0 1 0-1.41 1 1 0 0 1 1.41 0l6.36 6.36a.91.91 0 0 1 0 1.28L13.54 23a1 1 0 0 1-1.41 0 1 1 0 0 1 0-1.41Z" fill="#ffffff"></path>
                                        </g>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- MODAL FORM -->
                <div class="modal fade findanwser" id="resource_form_modal" tabindex="-1" aria-labelledby="resourceFormLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content p-3">
                            <div class="modal-header border-bottom-0 pb-0">
                                <h2 class="modal-title section-title m-0 pb-5" id="resourceFormLabel">Download</h2>
                                <button type="button" class="close close-btn" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body pt-0">
                                <div class="row">
                                    <div class="col-lg-12" id="resource-form">
                                        <?php echo do_shortcode('[contact-form-7 id="0e81076" title="Resource form"]'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endwhile; endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
