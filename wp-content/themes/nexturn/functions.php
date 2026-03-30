
<?php

/**
 * NextTurn website functions and definitions
 *
 * @link    https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Nexturn
 */

define("TEAM_MEMBER_PREFIX", "nexturn_team_");
define("TESTIMONIAL_PREFIX", "nexturn_testimonial_");
define("JOB_POST_PREFIX", "nexturn_job_post_");
define("IMPACT_STORY_PREFIX", "nex_impact_story_");
// define('FAQ_PREFIX', 'nexturn_faq_');

if (!function_exists('nexturn_theme_setup')):
    function nexturn_theme_setup()
    {
        add_theme_support('title-tag');

        add_theme_support('post-thumbnails');

        add_theme_support('custom-header');

        add_theme_support('custom-logo');

        register_nav_menus(array(
            'primary_menu' => esc_html__('Primary Menu', 'nexturn'),
            'footer_menu' => esc_html__('Footer Menu', 'nexturn')
        ));
        add_image_size('team_member_thumb', 300, 350, true); // Width, height, crop (true/false)

        add_theme_support('editor-styles');

        add_editor_style('assets/vendors/bootstrap/bootstrap.min.css');
        add_editor_style('assets/css/theme.css');
        add_editor_style('assets/css/admin-editor.css');
    }
    add_action('after_setup_theme', 'nexturn_theme_setup');
endif;

if (!class_exists('Redux')) {
    require_once get_template_directory() . '/_admin/theme-options.php';
}

add_filter('wp_kses_allowed_html', function ($tags) {

    $tags['svg'] = array(
        'xmlns' => array(),
        'fill' => array(),
        'viewbox' => array(),
        'role' => array(),
        'aria-hidden' => array(),
        'focusable' => array(),
    );
    $tags['path'] = array(
        'd' => array(),
        'fill' => array(),
    );
    return $tags;
}, 10, 2);

require_once get_template_directory() . '/_admin/post-types.php';  // Post-types
require_once get_template_directory() . '/_admin/metaboxes.php';  // Meta-boxes
require_once get_template_directory() . '/_admin/theme-options.php';


if (!function_exists('nexturn_theme_scripts')):
    function nexturn_theme_scripts()
    {
        wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/vendors/bootstrap/bootstrap.min.css', array(), '5.3.0-alpha1', 'all');
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css', array(), '6.0.0', 'all');
        wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css?family=Funnel Sans', array(), null, 'all');

        // wp_enqueue_style('aos', 'https://unpkg.com/aos@next/dist/aos.css', array(), null, 'all');
        //wp_enqueue_style('animate-css', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css', array(), null, 'all');
        wp_enqueue_style('owl-carousel', get_template_directory_uri() . '/assets/vendors/owlcarousel/assets/owl.carousel.min.css', array(), null, 'all');
        wp_enqueue_style('owl-carousel-theme', get_template_directory_uri() . '/assets/vendors/owlcarousel/assets/owl.theme.default.min.css', array(), null, 'all');

        wp_enqueue_style('nexturn-theme', get_template_directory_uri() . '/assets/css/theme.css', array(), null, 'all');

        wp_enqueue_style('nexturn-style', get_stylesheet_uri(), array(), null, 'all');

        //wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js', array('jquery'), '5.3.0-alpha1', true);
        wp_enqueue_script('bootstrap-bundle', get_template_directory_uri() . '/assets/vendors/bootstrap/bootstrap.bundle.min.js', array('jquery'), '5.3.0', true);
        // wp_enqueue_script('aos', 'https://unpkg.com/aos@next/dist/aos.js', array(), null, true);
        wp_enqueue_script('owl-carousel', get_template_directory_uri() . '/assets/vendors/owlcarousel/owl.carousel.min.js', array(), null, true);
        wp_enqueue_script('popper', 'https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js', array(), null, true);
        wp_enqueue_script('nexturn-theme', get_template_directory_uri() . '/assets/js/theme.js', array(), null, true);
        wp_localize_script('nexturn-theme', 'NEXTURN_AJAX', [
            'ajax_url' => admin_url('admin-ajax.php')
        ]);

     
       // Enqueue jsPDF for single resource pages
        if (is_singular('resource')) {
            wp_enqueue_script('jspdf', 'https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js', array(), '2.5.1', true);
            // Lightweight, scoped handler for resource form only (formatted for readability)
            $inline_js = <<<'JS'
(function() {
    // Helper: load image and convert to dataURL (returns { dataUrl, width, height })
    function loadImageAsDataURL(src) {
        return new Promise((resolve) => {
            try {
                var img = new Image();
                img.crossOrigin = "anonymous";
                img.onload = function() {
                    // Draw into canvas to get a dataURL (ensures jsPDF can accept it)
                    var canvas = document.createElement('canvas');
                    canvas.width = img.naturalWidth || img.width;
                    canvas.height = img.naturalHeight || img.height;
                    var ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0);
                    var dataUrl = canvas.toDataURL('image/png');
                    resolve({ dataUrl: dataUrl, width: canvas.width, height: canvas.height });
                };
                img.onerror = function() {
                    console.warn('Image failed to load:', src);
                    resolve(null); // resolve null to skip broken images
                };
                img.src = src;
            } catch (e) {
                console.error('loadImageAsDataURL error', e);
                resolve(null);
            }
        });
    }

    // Helper: detect formatting by walking up ancestors
    function detectFormatting(node, container) {
        var el = node.parentElement;
        var fmt = { bold: false, italic: false, headingLevel: 0 };
        while (el && el !== container) {
            var tag = el.tagName ? el.tagName.toLowerCase() : '';
            if (tag === 'strong' || tag === 'b') fmt.bold = true;
            if (tag === 'em' || tag === 'i') fmt.italic = true;
            var h = tag.match(/^h([1-6])$/);
            if (h && !fmt.headingLevel) fmt.headingLevel = parseInt(h[1], 10);
            el = el.parentElement;
        }
        return fmt;
    }

    function fontStyleFromFlags(bold, italic) {
        if (bold && italic) return 'bolditalic';
        if (bold) return 'bold';
        if (italic) return 'italic';
        return 'normal';
    }

    async function generateResourcePdf() {
        try {
            var wrapper = document.getElementById('resource-form');
            if (!wrapper) return;

            var descFull = document.getElementById('resource-description-full');
            var description = descFull ? descFull.innerHTML.trim() :
                              (document.getElementById('resource-description') ? document.getElementById('resource-description').innerHTML.trim() : '');

            if (!window.jspdf || !window.jspdf.jsPDF) return;

            var titleEl = document.querySelector('h1, .entry-title');
            var title = titleEl ? titleEl.textContent.trim() : document.title;

            var doc = new window.jspdf.jsPDF({ unit: 'pt', format: 'a4' });
            var margin = 40, maxWidth = 515;
            var y = margin;
            var pageHeight = doc.internal.pageSize.height;
            var lineHeight = 18;

            // Title
            doc.setFont('Helvetica', 'bold');
            doc.setFontSize(16);
            var titleLines = doc.splitTextToSize(title || 'Resource', maxWidth);
            titleLines.forEach(function(line) {
                if (y + lineHeight > pageHeight - margin) { doc.addPage(); y = margin; }
                doc.text(line, margin, y);
                y += lineHeight;
            });
            y += 10;

            // Parse HTML into temp container
            var tempDiv = document.createElement('div');
            tempDiv.innerHTML = description;

            // Recursive async renderer that preserves order
            async function renderNode(node) {
                // Text nodes: render using parent formatting
                if (node.nodeType === Node.TEXT_NODE) {
                    var text = node.textContent.replace(/\s+/g, ' ').trim();
                    if (!text) return;
                    var fmt = detectFormatting(node, tempDiv);
                    var style = fontStyleFromFlags(fmt.bold, fmt.italic);

                    // Heading size if applicable
                    var fontSize = fmt.headingLevel ? Math.max(18 - (fmt.headingLevel - 1) * 1.5, 12) : 12;
                    if (fmt.headingLevel) { style = 'bold'; }

                    // Set font and size
                    try {
                        doc.setFont('Helvetica', style);
                    } catch (e) { /* some builds require only setFontStyle later; catching to be safe */ }
                    doc.setFontSize(fontSize);

                    var lines = doc.splitTextToSize(text, maxWidth);
                    lines.forEach(function(line) {
                        if (y + lineHeight > pageHeight - margin) { doc.addPage(); y = margin; }
                        doc.text(line, margin, y);
                        y += lineHeight;
                    });
                    y += 2;
                }

                // Element nodes
                else if (node.nodeType === Node.ELEMENT_NODE) {
                    var tag = node.tagName.toLowerCase();

                    // Inline image: load synchronously (await) then draw in-place
                    if (tag === 'img') {
                        var src = node.getAttribute('src') || node.src || '';
                        if (!src) return;
                        var imgInfo = await loadImageAsDataURL(src);
                        if (!imgInfo) return; // skip if failed

                        // Calculate scaled dimensions
                        var imgWidth = Math.min(imgInfo.width, maxWidth);
                        var imgHeight = imgInfo.height * (imgWidth / imgInfo.width);

                        if (y + imgHeight > pageHeight - margin) { doc.addPage(); y = margin; }

                        // addImage expects a dataURL (we pass PNG)
                        try {
                            doc.addImage(imgInfo.dataUrl, 'PNG', margin, y, imgWidth, imgHeight);
                        } catch (errAdd) {
                            // fallback: attempt JPEG
                            try { doc.addImage(imgInfo.dataUrl, 'JPEG', margin, y, imgWidth, imgHeight); }
                            catch(e){ console.warn('addImage failed for', src, e); }
                        }

                        y += imgHeight + 10;
                    }

                    // Block elements that should cause spacing: <p>, <div>, <li>, <blockquote>, headings, etc.
                    else if (['p','div','li','blockquote','section','article','pre'].indexOf(tag) !== -1 ||
                             (/^h[1-6]$/.test(tag))) {
                        // If element is a heading tag, we will let text nodes inside detect heading level.
                        for (var i = 0; i < node.childNodes.length; i++) {
                            await renderNode(node.childNodes[i]);
                        }
                        y += 6; // space after block
                    }

                    // Inline elements (strong, em, a, span, etc.): just recurse in order
                    else {
                        for (var j = 0; j < node.childNodes.length; j++) {
                            await renderNode(node.childNodes[j]);
                        }
                    }
                }
            }

            // Render each top-level child in order
            for (var k = 0; k < tempDiv.childNodes.length; k++) {
                await renderNode(tempDiv.childNodes[k]);
            }

            var safeName = (title || 'resource').replace(/[^\w\-\s\.]/g, '_') + '.pdf';
            doc.save(safeName);

        } catch (e) {
            console.error('PDF generation failed', e);
        }
    }

    // Handler to be run on CF7 events — closes modal, resets form, and triggers generation
    function handler(e){
        var wrapper = document.getElementById('resource-form');
        if (!wrapper) return;
        var form = e && e.target ? e.target : null;
        if (form && !wrapper.contains(form)) return;

        try {
            var modalEl = document.getElementById('resource_form_modal');
            if (modalEl && window.bootstrap && window.bootstrap.Modal) {
                var modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.hide();
            }
            var frm = form || wrapper.querySelector('form');
            if (frm) setTimeout(function(){ try { frm.reset(); } catch(_){} }, 100);
        } catch(_) {}

        // Slight delay to let CF7 DOM updates finish
        setTimeout(generateResourcePdf, 100);
    }

    // Bind CF7 events
    document.addEventListener('wpcf7mailsent', handler);
    document.addEventListener('wpcf7submit', handler);

})();
JS;

        wp_add_inline_script('jspdf', $inline_js);
    }


}
    add_action('wp_enqueue_scripts', 'nexturn_theme_scripts');
endif;

add_filter('run_wptexturize', '__return_false');

add_action('wp_head', function () {
    if (is_front_page() || is_home()) {
        $banner_url = get_template_directory_uri() . '/assets/images/home/home-banner.jpg';
        echo '<link rel="preload" as="image" href="' . esc_url($banner_url) . '" />' . "\n";
    }
}, 1);

// function change_title_dash($sep){
//     return '-';
// }
// add_filter ('document_title_separator', 'change_title_dash');

function add_file_types_to_uploads($file_types)
{
    $new_filetypes = array();
    $new_filetypes['svg'] = 'image/svg+xml';
    $file_types = array_merge($file_types, $new_filetypes);
    return $file_types;
}
add_filter('upload_mimes', 'add_file_types_to_uploads');

function disable_wp_auto_p($content)
{
    remove_filter('the_content', 'wpautop');
    remove_filter('the_excerpt', 'wpautop');
    return $content;
}
add_filter('the_content', 'disable_wp_auto_p', 0);

function menu_add_class_on_anchor($classes, $item, $args)
{
    if (isset($args->add_anchor_class)) {
        $classes['class'] = $args->add_anchor_class;
    }
    return $classes;
}
add_filter('nav_menu_link_attributes', 'menu_add_class_on_anchor', 1, 3);

function menu_add_class_on_li($classes, $item, $args)
{
    if (isset($args->add_li_class)) {
        $classes[] = $args->add_li_class;
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'menu_add_class_on_li', 1, 3);



remove_action('wpcf7_init', 'wpcf7_add_form_tag_submit');
add_action('wpcf7_init', 'my_add_form_tag_submit', 10, 0);

function my_add_form_tag_submit()
{
    wpcf7_add_form_tag('submit', 'my_submit_form_tag_handler');
}

function my_submit_form_tag_handler($tag): string
{
    return '<button class="wpcf7-form-control wpcf7-submit has-spinner btn btn-submit" type="submit">  <span class="ms-2">→</span></button>';
}

add_filter('shortcode_atts_wpcf7', 'custom_shortcode_atts_wpcf7_filter', 10, 3);

function custom_shortcode_atts_wpcf7_filter($out, $pairs, $atts)
{
    $my_attr = 'cand_role';

    if (isset($atts[$my_attr])) {
        $out[$my_attr] = $atts[$my_attr];
    }

    return $out;
}

// Custom validation function for email field (including DNS verification)
function custom_validate_email_dns_and_letters($result, $tag)
{

    // Apply only for email fields (both required and optional)
    if ('email' === $tag->basetype) {

        // Get the submitted email value
        $email = isset($_POST[$tag->name]) ? trim($_POST[$tag->name]) : '';

        // Check for more than three consecutive identical alphabetical letters (case-insensitive)
        if (preg_match('/([A-Za-z])\1{3,}/', $email)) {
            //$result->invalidate( $tag, "Email cannot contain more than 3 consecutive identical letters." );
            $result->invalidate($tag, "Please enter a valid email address.");
            return $result;
        }

        // Extract the domain part after the @ symbol
        $parts = explode('@', $email);
        if (count($parts) < 2) {
            // Invalid email format; let CF7's built-in rules handle this
            return $result;
        }
        $domain = array_pop($parts);

        // Check for a valid DNS record on the domain (preferably MX, fallback to A)
        if (!checkdnsrr($domain, "MX") && !checkdnsrr($domain, "A")) {
            //$result->invalidate( $tag, "The email domain does not appear to be valid." );
            $result->invalidate($tag, "Please enter a valid email address.");
        }
    }
    return $result;
}

// Apply the filter for both optional and required email fields
add_filter('wpcf7_validate_email', 'custom_validate_email_dns_and_letters', 20, 2);
add_filter('wpcf7_validate_email*', 'custom_validate_email_dns_and_letters', 20, 2);


add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/validate-email', [
        'methods' => 'GET',
        'callback' => 'validate_email_domain',
        'permission_callback' => '__return_true'
    ]);
});

function validate_email_domain(WP_REST_Request $request)
{
    $domain = sanitize_text_field($request->get_param('domain'));

    if ($domain != '') {
        // Validate domain DNS (MX records)
        $is_valid = checkdnsrr($domain, 'MX');
    } else {
        $is_valid = false;
    }
    return rest_ensure_response(['valid' => $is_valid]);
}



add_shortcode('put_team_thumbs', 'put_team_thumbs');
function put_team_thumbs()
{
    global $post;
    $args = array(
        'posts_per_page' => -1,
        'offset' => 0,
        'orderby' => 'date',
        'order' => 'ASC',
        'include' => '',
        'exclude' => '',
        'meta_key' => '',
        'meta_value' => '',
        'post_type' => 'nexturn_team',
        'post_mime_type' => '',
        'post_parent' => '',
        'author' => '',
        'post_status' => 'publish',
        'suppress_filters' => true
    );
    $team_members = get_posts($args);

    if (!empty($team_members) && count($team_members) != 0):
        foreach ($team_members as $post):
            setup_postdata($post);
            $role = get_post_meta(get_the_ID(), TEAM_MEMBER_PREFIX . 'role', TRUE);
            $picture_id = get_post_meta(get_the_ID(), TEAM_MEMBER_PREFIX . 'picture', TRUE);
            $picture = get_the_guid($picture_id);
            $picture_meta = rwmb_meta(TEAM_MEMBER_PREFIX . 'picture', array('size' => 'team_member_thumb'));
            $picture_1 = reset($picture_meta);
            //echo '<pre>';
            //print_r($picture_1['url']);
            //echo '</pre>';
            //print_r($picture);
            $lnkdn_link = get_post_meta(get_the_ID(), TEAM_MEMBER_PREFIX . 'linkdn_link', TRUE);
            if (!empty(get_the_title()) && $picture_1['url'] != ''):
?>
                <div class="col-md-3">
                    <div class="founders-card">
                        <img src="<?php echo $picture_1['url']; ?>" alt="<?php the_title(); ?>" class="founders-img img-fluid">
                        <h4 class="mt-3"><?php the_title(); ?></h4>
                        <div class="d-flex justify-content-between">
                            <p><?php echo $role; ?></p>
                            <?php if ($lnkdn_link != ''): ?>
                                <a href="<?php echo $lnkdn_link; ?>" class="social-link" target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                        class="bi bi-linkedin" viewBox="0 0 24 24">
                                        <path
                                            d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z">
                                        </path>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
        <?php
            endif;
        endforeach;
    else:
        return '<div class="col-12"><p class="lead">No Team Members Found</p></div>';
    endif;
    wp_reset_postdata();
}

add_shortcode('put_job_thumbs', 'put_job_thumbs');
function put_job_thumbs()
{
    global $post;
    $args = array(
        'posts_per_page' => -1,
        'offset' => 0,
        'orderby' => 'date',
        'order' => 'ASC',
        'include' => '',
        'exclude' => '',
        'meta_key' => '',
        'meta_value' => '',
        'post_type' => 'nexturn_job_posts',
        'post_mime_type' => '',
        'post_parent' => '',
        'author' => '',
        'post_status' => 'publish',
        'suppress_filters' => true
    );
    $job_posts = get_posts($args);
    //print_r($job_posts);
    if (!empty($job_posts) && count($job_posts) > 0):
        ob_start();
        ?>
        <div class="row g-4">
            <!-- Job Card 1 -->
            <?php
            $count = 0;
            foreach ($job_posts as $post):
                setup_postdata($post);
            ?>
                <div class="col-12 col-md-6 job_post_thumb <?php echo ++$count > 2 ? 'show_hide' : ''; ?>"
                    id="<?php echo 'job_post_thumb_' . $count; ?>" <?php echo $count > 2 ? 'style="display:none;"' : ''; ?>>
                    <div class="job-card">
                        <h3 class="whitecard-heading"><?php the_title(); ?></h3>
                        <p class="job-subtitle"><?php echo get_post_meta(get_the_ID(), JOB_POST_PREFIX . 'location', TRUE); ?></p>

                        <div class="mb-3 card-text-medium">
                            <strong class="job-subtitle">Work Experience:</strong>
                            <?php echo get_post_meta(get_the_ID(), JOB_POST_PREFIX . 'work_exp', TRUE); ?>
                        </div>
                        <div class="mb-3 card-text-medium">
                            <strong class="job-subtitle">Qualifications:</strong>
                            <?php echo get_post_meta(get_the_ID(), JOB_POST_PREFIX . 'qual', TRUE); ?>
                        </div>
                        <!-- <div class="mb-3 card-text-medium">
                            <strong class="job-subtitle">Reports To:</strong>
                            <?php echo get_post_meta(get_the_ID(), JOB_POST_PREFIX . 'reports_to', TRUE); ?>
                        </div> -->
                        <div class="my-4 ">
                            <strong class="job-subtitle">Job Description:</strong>
                            <p class="card-text-medium">
                                <?php echo substr(strip_tags(get_post_meta(get_the_ID(), JOB_POST_PREFIX . 'desc', TRUE)), 0, 100) . ' [...]'; ?>
                            </p>
                        </div>

                        <a href="<?php echo get_the_permalink(); ?>" class="job-apply-btn mb-4">
                            <span class="pe-2">Apply</span> <svg width="14" height="14" viewBox="0 0 14 14" fill="#000"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.079 7.5L6.286 13.292L7 14L14 7L7 0L6.286 0.708L12.08 6.5H0V7.5H12.079Z" fill="#000">
                                </path>
                            </svg>
                            </svg>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (count($job_posts) > 2): ?>
                <div class="text-center mt-4">
                    <button class="btn btn-link text-white text-decoration-none innersec-white" id="show_more_jobs" data-count=2
                        data-total="<?php echo count($job_posts); ?>">Show
                        more</button>
                    <button class="btn btn-link text-white text-decoration-none innersec-white" id="hide_more_jobs"
                        style="display:none;">Hide all</button>
                </div>
            <?php endif; ?>

        </div>
    <?php
    endif;
    wp_reset_postdata();
    return ob_get_clean();
}

add_action('wp_ajax_nopriv_live_search', 'handle_live_search');
add_action('wp_ajax_live_search', 'handle_live_search');

function handle_live_search()
{
    $term = isset($_GET['term']) ? sanitize_text_field($_GET['term']) : '';

    if (!$term) {
        wp_send_json([]);
    }

    global $wpdb;

    // Search posts with full-word match using REGEXP
    $escaped_term = esc_sql($term);
    $pattern = '[[:<:]]' . $escaped_term . '(-[a-zA-Z0-9]+)?[[:>:]]'; // MySQL word boundary match

    $results = $wpdb->get_results("
    SELECT ID
    FROM $wpdb->posts
    WHERE post_status = 'publish'
      AND post_type IN ('post', 'page', 'nexturn_job_posts')
      AND (
        post_title REGEXP '{$pattern}'
        OR post_content REGEXP '{$pattern}'
      )
    LIMIT 10
  ");

    $data = [];

    foreach ($results as $row) {
        $post = get_post($row->ID);
        $data[] = [
            'title' => get_the_title($post),
            'link' => get_permalink($post),
            'excerpt' => wp_trim_words(strip_tags($post->post_content), 20, '...')
        ];
    }

    wp_send_json($data);
}

add_shortcode('partner-with-us', function () {
    ob_start(); ?>
    <a class="svg-container action-click cta-btn" data-bs-target="#partner_contact_modal" data-bs-toggle="modal">
        <span class="pe-3">Talk To Us</span>
        <svg class="home-bn-arrow home-bn-arrow-sec" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
            <g data-name="Layer 2">
                <path d="M1 16a15 15 0 1 1 15 15A15 15 0 0 1 1 16Zm28 0a13 13 0 1 0-13 13 13 13 0 0 0 13-13Z"
                    class="fill-000000"></path>
                <path
                    d="M12.13 21.59 17.71 16l-5.58-5.59a1 1 0 0 1 0-1.41 1 1 0 0 1 1.41 0l6.36 6.36a.91.91 0 0 1 0 1.28L13.54 23a1 1 0 0 1-1.41 0 1 1 0 0 1 0-1.41Z"
                     class="fill-000000"></path>
            </g>
        </svg>
    </a>
    <?php
    //$partner_contact_modal = get_template_part('template','partner-contact');
    add_action('wp_footer', function () {
        get_template_part('template', 'partner-contact');
    });
    return ob_get_clean();
});

add_shortcode('partners-btn', function ($atts = []) {
    ob_start();
    ?>
    <a class="svg-container partner-btn" data-bs-toggle="modal" data-bs-target="#partner_contact_modal">
        <span class="pe-3">Talk To Us</span>
        <svg class="home-bn-arrow home-bn-arrow-sec" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
            <g data-name="Layer 2">
                <path d="M1 16a15 15 0 1 1 15 15A15 15 0 0 1 1 16Zm28 0a13 13 0 1 0-13 13 13 13 0 0 0 13-13Z"
                    class="circle"></path>
                <path d="M12.13 21.59 17.71 16l-5.58-5.59a1 1 0 0 1 0-1.41 1 1 0 0 1 1.41 0l6.36 6.36a.91.91 0 0 1 0 1.28L13.54 23a1 1 0 0 1-1.41 0 1 1 0 0 1 0-1.41Z"
                    class="arrow"></path>
            </g>
        </svg>
    </a>
    <?php
    return ob_get_clean();
});

add_shortcode('home-partner-with-us', function () {
    ob_start(); ?>
   <a class="svg-container action-click cta-btn" data-bs-target="#partner_contact_modal" data-bs-toggle="modal">
        <svg class="home-bn-arrow" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
            <g data-name="Layer 2">
                <path d="M1 16a15 15 0 1 1 15 15A15 15 0 0 1 1 16Zm28 0a13 13 0 1 0-13 13 13 13 0 0 0 13-13Z" 
                    class="fill-000000"></path>
                <path
                    d="M12.13 21.59 17.71 16l-5.58-5.59a1 1 0 0 1 0-1.41 1 1 0 0 1 1.41 0l6.36 6.36a.91.91 0 0 1 0 1.28L13.54 23a1 1 0 0 1-1.41 0 1 1 0 0 1 0-1.41Z"
                    class="fill-000000"></path>
            </g>
        </svg>
    </a>
    <?php
    add_action('wp_footer', function () {
        get_template_part('template', 'partner-contact');
    });
    return ob_get_clean();
});

add_shortcode('know-more', function ($atts = []) {
    ob_start();
    $atts = array_change_key_case((array) $atts, CASE_LOWER);
    $atts = shortcode_atts(array(
        'slug' => '#',
        'text' => 'Know More',
    ), $atts);
    ?>
    <a class="svg-container know-more" href="<?php echo site_url($atts['slug']) ?>"><span
            class="pe-3"><?php echo $atts['text'] ?></span>
        <?php
        global $nexturn_opt;
        $final_cta_svg = substr_replace($nexturn_opt['know_more_svg'], 'class="' . $nexturn_opt['know_more_svg_class'] . '"', 5, 0);
        echo $final_cta_svg;
        ?>
    </a>
    <?php
    return ob_get_clean();
});

add_shortcode('impact-stories', function ($atts = []) {
    ob_start();
    global $post;
    $atts = array_change_key_case((array) $atts, CASE_LOWER);
?>
    <div class="row">
        <div class="col-12">
            <?php
            if (isset($atts['group-slug']) && trim($atts['group-slug']) != '') {
                $group_slug = $atts['group-slug'];
                $args = array(
                    'post_type' => 'nex_impact_stories',
                    'posts_per_page' => -1,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'nex_impact_group',
                            'field' => 'slug',
                            'terms' => $group_slug
                        )
                    )
                );
                $posts = get_posts($args);
            ?>
                <div class="owl-carousel owl-theme">
                    <!-- Item -->
                    <?php foreach ($posts as $post):
                        setup_postdata($post); ?>
                        <div class="item">
                            <div class="story-card">
                                <div class="story-title"><?php the_title(); ?></div>
                                <div class="innersec-white grey-text">
                                    <?php echo get_post_meta(get_the_ID(), IMPACT_STORY_PREFIX . 'sub_title', TRUE) ?>
                                </div>
                                <div class="innersec-white mt-3 pb-5">
                                    <?php echo get_post_meta(get_the_ID(), IMPACT_STORY_PREFIX . 'desc', TRUE); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach;
                    wp_reset_postdata(); ?>
                </div>
            <?php } else { ?>
                <span>- No Impact Stories Found -</span>
            <?php } ?>
        </div>
    </div>
<?php
    return ob_get_clean();
});

add_shortcode('join-our-team', function ($atts = []) {
    ob_start();
    $atts = array_change_key_case((array) $atts, CASE_LOWER);
    $atts = shortcode_atts(array(
        'text' => 'Join Our Team',
    ), $atts);
?>
    <a class="svg-container action-click cta-btn" data-bs-toggle="modal" data-bs-target="#join_our_team"><span
            class="pe-3">Join our team</span>
        <svg class="home-bn-arrow home-bn-arrow-sec" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
            <g data-name="Layer 2">
                <path d="M1 16a15 15 0 1 1 15 15A15 15 0 0 1 1 16Zm28 0a13 13 0 1 0-13 13 13 13 0 0 0 13-13Z" fill="#ffffff"
                    class="fill-000000"></path>
                <path
                    d="M12.13 21.59 17.71 16l-5.58-5.59a1 1 0 0 1 0-1.41 1 1 0 0 1 1.41 0l6.36 6.36a.91.91 0 0 1 0 1.28L13.54 23a1 1 0 0 1-1.41 0 1 1 0 0 1 0-1.41Z"
                    fill="#ffffff" class="fill-000000"></path>
            </g>
        </svg>
    </a>
    <?php
    add_action('wp_footer', function () { ?>
        <div class="modal fade findanwser" id="join_our_team" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content p-3">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h2 class="modal-title section-title m-0 pb-5" id="exampleModalLabel">Job Application Form</h2>
                        <button type="button" class="close close-btn" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body pt-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <?php echo do_shortcode('[contact-form-7 id="4abff73" title="Join Team Form"]'); ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    <?php get_template_part('template', 'contact-popup');
    });
    return ob_get_clean();
});

add_shortcode('careers-faq', function ($atts = []) {
    ob_start();
    ?>
    <a class="svg-container action-click cta-btn" data-bs-toggle="modal" data-bs-target="#faqModal"><span class="pe-3">Find
            the answers</span>
        <svg class="home-bn-arrow home-bn-arrow-sec" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
            <g data-name="Layer 2">
                <path d="M1 16a15 15 0 1 1 15 15A15 15 0 0 1 1 16Zm28 0a13 13 0 1 0-13 13 13 13 0 0 0 13-13Z" fill="#ffffff"
                    class="fill-000000"></path>
                <path
                    d="M12.13 21.59 17.71 16l-5.58-5.59a1 1 0 0 1 0-1.41 1 1 0 0 1 1.41 0l6.36 6.36a.91.91 0 0 1 0 1.28L13.54 23a1 1 0 0 1-1.41 0 1 1 0 0 1 0-1.41Z"
                    fill="#ffffff" class="fill-000000"></path>
            </g>
        </svg>
    </a>
    <?php
    add_action('wp_footer', function () {
        get_template_part('template', 'careers-faq');
    });
    return ob_get_clean();
});

add_shortcode('team-testimonial', function () {
    global $post;
    $args = array(
        'post_type' => 'nexturn_testimonial',
        'post_status' => 'publish',
        'posts_per_page' => -1
    );
    $posts = get_posts($args);
    if (count($posts) > 0):
        ob_start();
    ?>
        <div id="testimonialCarousel" class="carousel">
            <div class="carousel-inner">
                <?php
                foreach ($posts as $post):
                    //print_r($post);
                    setup_postdata($post);
                    // $picture_id = get_post_meta(get_the_ID(), TESTIMONIAL_PREFIX . 'picture', true);
                    // print_r($picture);
                    $picture_meta = rwmb_meta(TESTIMONIAL_PREFIX . 'picture', array('size' => 'team_member_thumb'));
                    $picture = reset($picture_meta);
                ?>
                    <div class="carousel-item">
                        <div class="card shadow-sm rounded-3">
                            <div class="quotes display-2 text-body-tertiary">
                                <i class="bi bi-quote"></i>
                            </div>
                            <div class="card-body">
                                <p class="card-text"><?php echo get_post_meta(get_the_ID(), TESTIMONIAL_PREFIX . 'message', true) ?>
                                </p>
                                <div class="d-flex align-items-center pt-2 testimonials-bottom">
                                    <img src="<?php echo $picture['url']; ?>" alt="bootstrap testimonial carousel slider 2">
                                    <div>
                                        <h5 class="card-title fw-bold text-dark"><?php the_title(); ?> </h5>
                                        <span class="text-secondary"><?php echo get_post_meta(get_the_ID(), TESTIMONIAL_PREFIX . 'desg', true) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    <?php
    else:
        echo '- No Testimonial Found -';
    endif;
    return ob_get_clean();
});
 
// Helper function to render resource HTML
function render_resource_html($resource, $counter)
{
    $resource_summary = rwmb_meta('resource_summary', [], $resource->ID);
    $resource_text = rwmb_meta('resource_text', [], $resource->ID);
    $resource_card_text = $resource_summary !== '' ? $resource_summary : $resource_text;
   
    // Limit description length
    $resource_excerpt = wp_trim_words(strip_tags($resource_card_text), 60, '...');

    $resource_image = rwmb_meta('resource_image', ['size' => 'large'], $resource->ID);
    $resource_image_url = '';

    if ($resource_image && is_array($resource_image)) {
        $img = reset($resource_image);
        $resource_image_url = $img['url'];
    }

    $image_first = ($counter % 2 == 0);
    $column_reverse = $image_first ? '' : 'column-reverse';
    $terms = get_the_terms($resource->ID, 'resource_group');
    $group_name = !empty($terms) && !is_wp_error($terms) ? $terms[0]->name : '';

    ob_start();
    ?>
    <div class="row resource-item <?php echo $column_reverse; ?>">
        <?php if (!$image_first): ?>          
    <!-- Text First on Desktop, but Image First on Mobile -->
    <div class="col-lg-6 order-2 order-lg-1 p-0">
        <!--TEXT CONTENT -->
        <div class="service-card small-card">
            <div class="col-12 px-4">
                <?php if ($group_name): ?>
                    <span class="resource-group-label mb-4 d-block text-uppercase" style="font-size:28px; line-height:1.15; font-weight:700; margin-bottom:12px; color:#111;">
                        <?php echo esc_html($group_name); ?>
                    </span>
                <?php endif; ?>

                <h4 class="animate-on-scroll resource-title mb-3" style="color: #000 !important;">
                    <?php echo esc_html(get_the_title($resource)); ?>
                </h4>

                <?php if ($resource_excerpt): ?>
                    <p class="description animate-on-scroll innersec-white"><?php echo wp_kses_post($resource_excerpt); ?></p>
                <?php endif; ?>

                <a href="<?php echo esc_url(get_permalink($resource)); ?>" class="animate-on-scroll svg-container know-more" target="_blank">
                    <span class="pe-2">Know More</span>
                    <svg class="home-bn-arrow home-bn-arrow-sec" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <g data-name="Layer 2">
                            <path d="M1 16a15 15 0 1 1 15 15A15 15 0 0 1 1 16Zm28 0a13 13 0 1 0-13 13 13 13 0 0 0 13-13Z" fill="#ffffff"></path>
                            <path d="M12.13 21.59 17.71 16l-5.58-5.59a1 1 0 0 1 1.41-1.41l6.36 6.36a.91.91 0 0 1 0 1.28L13.54 23a1 1 0 0 1-1.41 0 1 1 0 0 1 0-1.41Z" fill="#ffffff"></path>
                        </g>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- IMAGE Second on Desktop, but First on Mobile -->
    <div class="col-lg-6 order-1 order-lg-2 p-0 cloud-image"
        style="background-image: url('<?php echo esc_url($resource_image_url ?: get_template_directory_uri() . '/assets/images/default-resource.jpg'); ?>');">
    </div>
    <?php else: ?>
    <!-- IMAGE First -->
    <div class="col-lg-6 order-1 p-0 cloud-image"
        style="background-image: url('<?php echo esc_url($resource_image_url ?: get_template_directory_uri() . '/assets/images/default-resource.jpg'); ?>');">
    </div>

    <!-- TEXT Second -->
    <div class="col-lg-6 order-2 p-0">
        <div class="service-card small-card">
            <div class="col-12 px-4">
                <?php if ($group_name): ?>
                    <span class="resource-group-label mb-4 d-block text-uppercase" style="font-size:28px; line-height:1.15; font-weight:700; margin-bottom:12px; color:#111;">
                        <?php echo esc_html($group_name); ?>
                    </span>
                <?php endif; ?>

                <h4 class="animate-on-scroll resource-title mb-3" style="color: #000 !important;">
                    <?php echo esc_html(get_the_title($resource)); ?>
                </h4>

                <?php if ($resource_excerpt): ?>
                    <p class="animate-on-scroll innersec-white"><?php echo wp_kses_post($resource_excerpt); ?></p>
                <?php endif; ?>

                <a href="<?php echo esc_url(get_permalink($resource)); ?>" class="animate-on-scroll svg-container know-more" target="_blank">
                    <span class="pe-2">Know More</span>
                    <svg class="home-bn-arrow home-bn-arrow-sec" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <g data-name="Layer 2">
                            <path d="M1 16a15 15 0 1 1 15 15A15 15 0 0 1 1 16Zm28 0a13 13 0 1 0-13 13 13 13 0 0 0 13-13Z" fill="#ffffff"></path>
                            <path d="M12.13 21.59 17.71 16l-5.58-5.59a1 1 0 0 1 1.41-1.41l6.36 6.36a.91.91 0 0 1 0 1.28L13.54 23a1 1 0 0 1-1.41 0 1 1 0 0 1 0-1.41Z" fill="#ffffff"></path>
                        </g>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    </div>
    <?php
    return ob_get_clean();
}

add_shortcode('resources-section', function ($atts) {

    $atts = shortcode_atts([
        'group' => '',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC'
    ], $atts);

    // Fetch all resource groups
    $terms = get_terms([
        'taxonomy' => 'resource_group',
        'hide_empty' => false,
    ]);

    // Preload resources per group
    $all_group_resources = [];
    foreach ($terms as $term) {
        $resources = get_posts([
            'post_type' => 'resource',
            'posts_per_page' => -1,
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
            'post_status' => 'publish',
            'tax_query' => [[
                'taxonomy' => 'resource_group',
                'field' => 'term_id',
                'terms' => $term->term_id,
            ]],
        ]);
        $all_group_resources[$term->term_id] = $resources;
    }
    
    // Latest one per group (render section cards)
    $resources_to_render = [];
    foreach ($terms as $grp) {
        if (!empty($all_group_resources[$grp->term_id])) {
            $resources_to_render[] = $all_group_resources[$grp->term_id][0];
        }
    }
    ob_start();
?>
<section class="resources-section home">
    <div class="container-fluid p-0">

        <!-- DESKTOP VIEW -->
        <?php if (!empty($terms)): ?>
            <div class="d-flex flex-row flex-wrap gap-3 justify-content-start mb-4">

                <?php foreach ($terms as $term): ?>
                    <div class="resource-group-tile"
                         data-group="<?php echo esc_attr($term->term_id); ?>"
                         data-group-name="<?php echo esc_attr($term->name); ?>">

                        <span class="resource-group-name">
                            <?php echo esc_html($term->name); ?>
                        </span>

                        <!-- Hidden popup list -->
                        <ul class="hidden-resource-list" style="display:none;">
                            <?php if (!empty($all_group_resources[$term->term_id])): ?>
                                <?php foreach ($all_group_resources[$term->term_id] as $res): ?>
                                    <?php
                                        // Get resource image (Meta Box field or featured image fallback)
                                        $thumb_url = '';
                                        $resource_image = rwmb_meta('resource_image', ['size' => 'medium'], $res->ID);
                                        if ($resource_image && is_array($resource_image)) {
                                            $img = reset($resource_image);
                                            $thumb_url = esc_url($img['url']);
                                        } elseif (has_post_thumbnail($res->ID)) {
                                            $thumb_url = get_the_post_thumbnail_url($res->ID, 'medium');
                                        } else {
                                            $thumb_url = get_template_directory_uri() . '/assets/images/default-thumb.jpg';
                                        }
                                    ?>
                                    <li data-thumb="<?php echo esc_url($thumb_url); ?>">
                                        <a href="<?php echo esc_url(get_permalink($res)); ?>" target="_blank">
                                            <?php echo esc_html(get_the_title($res)); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>No resources in this group.</li>
                            <?php endif; ?>
                        </ul>

                    </div>
                <?php endforeach; ?>

            </div> <!-- CLOSE DESKTOP ROW -->
        <?php endif; ?>


        <!-- MOBILE CAROUSEL -->
        <div id="resourceCarousel" class="carousel slide d-md-none" data-bs-ride="false">
            <div class="carousel-inner">

                <?php $active = "active"; ?>
                <?php foreach ($terms as $group): ?>
                    <div class="carousel-item <?php echo $active; ?>">
                        <span class="resource-group-name"
                              data-group="<?php echo esc_attr($group->term_id); ?>"
                              data-group-name="<?php echo esc_attr($group->name); ?>">
                            <?php echo esc_html($group->name); ?>
                        </span>

                        <!-- Hidden list for popup -->
                        <ul class="hidden-resource-list" style="display:none;">
                            <?php if (!empty($all_group_resources[$group->term_id])): ?>
                                <?php foreach ($all_group_resources[$group->term_id] as $res): ?>
                                    <li data-thumb="<?php echo esc_url(get_the_post_thumbnail_url($res, 'medium')); ?>">
                                        <a href="<?php echo esc_url(get_permalink($res)); ?>" target="_blank">
                                            <?php echo esc_html(get_the_title($res)); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>No resources in this group.</li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <?php $active = ""; ?>
                <?php endforeach; ?>

            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#resourceCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#resourceCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>

        
        <!-- RESOURCE CARDS (Existing Section Boxes) -->
        <?php if (!empty($resources_to_render)): ?>
            <div id="resources-container" class="h-100 resources-container-inner">
                <?php
                $counter = 0;
                foreach ($resources_to_render as $resource):
                    setup_postdata($resource);
                    $counter++;
                    echo render_resource_html($resource, $counter);
                endforeach;
                wp_reset_postdata();
                ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- POPUP HTML -->
<div id="resource-popup" class="resource-popup">
    <div class="popup-content">
        <span class="popup-close">&times;</span>
        <h3 id="popup-title">Resources</h3>

        <div id="popup-grid"></div>
    </div>
</div>

<?php
return ob_get_clean();
});

add_action('wp_enqueue_scripts', function () {
    // Adjust paths based on your theme structure
    wp_enqueue_style('resources-section', get_template_directory_uri() . '/assets/css/theme.css', [], '1.0.0');
    wp_enqueue_script('resources-section', get_template_directory_uri() . '/assets/js/resources-section.js', ['jquery'], '1.0.0', true);
});

//FAQ Shortcode for all pages
function nexturn_faq_shortcode($atts){

$atts = shortcode_atts(array(
'id' => 0
), $atts);

$post_id = intval($atts['id']);

$faq_items = rwmb_meta('nexturn_faq_items', '', $post_id);

if(empty($faq_items)) return '';

$left = [];
$right = [];

foreach($faq_items as $i => $item){
if($i % 2 == 0){
$left[] = $item;
}else{
$right[] = $item;
}
}

ob_start();
?>

<div class="nexturn-faq-wrapper">

<div class="faq-column">

<?php foreach($left as $item): ?>

<div class="nexturn-faq-item">

<div class="nexturn-faq-question">
<span><?php echo esc_html($item['question']); ?></span>
<span class="nexturn-faq-icon">⌄</span>
</div>

<div class="nexturn-faq-answer">
<?php echo wp_kses_post($item['answer']); ?>
</div>

</div>

<?php endforeach; ?>

</div>


<div class="faq-column">

<?php foreach($right as $item): ?>

<div class="nexturn-faq-item">

<div class="nexturn-faq-question">
<span><?php echo esc_html($item['question']); ?></span>
<span class="nexturn-faq-icon">⌄</span>
</div>

<div class="nexturn-faq-answer">
<?php echo wp_kses_post($item['answer']); ?>
</div>

</div>

<?php endforeach; ?>

</div>

</div>

<?php
return ob_get_clean();
}
add_shortcode('faq','nexturn_faq_shortcode');


//Resource Cards Shortcode for all pages
function nexturn_render_resource_card($resource) {
    $group_terms = get_the_terms($resource->ID, 'resource_group');
    $group_slug = !empty($group_terms) && !is_wp_error($group_terms) ? $group_terms[0]->slug : '';
    $group_name = !empty($group_terms) && !is_wp_error($group_terms) ? $group_terms[0]->name : '';

    $summary = rwmb_meta('resource_summary', [], $resource->ID);
    $full = rwmb_meta('resource_text', [], $resource->ID);
    $card_text = !empty($summary) ? wp_trim_words(strip_tags($summary), 30, '...') : wp_trim_words(strip_tags($full), 30, '...');
    $image_meta = rwmb_meta('resource_image', ['size' => 'large'], $resource->ID);
    $image_url = ($image_meta && is_array($image_meta)) ? reset($image_meta)['url'] : get_the_post_thumbnail_url($resource->ID, 'large');

    $date = rwmb_meta('resource_date', [], $resource->ID);
    $display_date = !empty($date) ? date('M j, Y', strtotime($date)) : get_the_date('M j, Y', $resource->ID);

   $is_read = in_array($group_slug, ['announcement','announcements','blog','blogs']);

$cta_label = $is_read ? 'Read More' : 'Download';

$cta_link = $is_read ? get_permalink($resource->ID) : '#';

$cta_attrs = $is_read 
    ? '' 
    : 'data-bs-toggle="modal" data-bs-target="#resource_form_modal"';
    $cta_link = in_array($group_slug, ['announcement','blogs']) ? get_permalink($resource->ID) : 'javascript:void(0)';
    $cta_attrs = in_array($group_slug, ['announcement','blogs']) 
        ? ''
        : 'data-bs-toggle="modal" data-bs-target="#resource_form_modal" data-resource-id="' . esc_attr($resource->ID) . '"';

    ob_start(); ?>
    <div class="resource-card">
        <div class="resource-card-img"><img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title($resource)); ?>"></div>
        <div class="resource-card-content">
            <?php if ($group_name): ?><div class="resource-meta"><?php echo esc_html($group_name); ?></div><?php endif; ?>
            <h4 class="resource-title"><?php echo esc_html(get_the_title($resource)); ?></h4>
            <div class="resource-date"><?php echo esc_html($display_date); ?></div>
            <p class="resource-desc"><?php echo esc_html($card_text); ?></p>
            <div class="resource-actions">
                <a class="btn resource-cta" href="<?php echo esc_url($cta_link); ?>" <?php echo $cta_attrs; ?>>
                    <?php echo esc_html($cta_label); ?>
                </a>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function nexturn_resource_cards($atts) {
    $atts = shortcode_atts([
        'group' => '',
        'posts_per_page' => 9,
        'orderby' => 'date',
        'order' => 'DESC',
    ], $atts);

    $args = [
        'post_type' => 'resource',
        'post_status' => 'publish',
        'posts_per_page' => intval($atts['posts_per_page']),
        'orderby' => $atts['orderby'],
        'order' => $atts['order'],
    ];

    if (!empty($atts['group'])) {
        $args['tax_query'] = [[
            'taxonomy' => 'resource_group',
            'field' => 'slug',
            'terms' => sanitize_title($atts['group']),
        ]];
    }

    $query = new WP_Query($args);
    ob_start();

    echo '<div class="resource-card-grid">';
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            global $post;
            echo nexturn_render_resource_card($post);
        }
    } else {
        echo '<div class="no-resource">No resources found.</div>';
    }
    echo '</div>';

    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('resource_cards', 'nexturn_resource_cards');

function nexturn_resource_category_page($atts) {
    $atts = shortcode_atts([
        'group' => 'announcement',
        'title' => '',
        'banner' => '',
    ], $atts);

    $group_name = ucfirst(str_replace('-', ' ', $atts['group']));
    $title = !empty($atts['title']) ? $atts['title'] : $group_name;
    $banner = !empty($atts['banner']) ? esc_url($atts['banner']) : get_template_directory_uri() . '/assets/images/default-resource-banner.jpg';

    ob_start(); ?>
    <section class="resource-hero-banner" style="background-image:url('<?php echo esc_url($banner); ?>')">
        <div class="hero-content container">
            <div class="hero-breadcrumb"><a href="<?php echo esc_url(site_url('/')); ?>">Home</a> / <span>Resource Center</span> / <span><?php echo esc_html($group_name); ?></span></div>
            <div class="hero-title"><?php echo esc_html($title); ?></div>
        </div>
    </section>
    <section class="container my-5">
        <?php echo do_shortcode('[resource_cards group="' . esc_attr($atts['group']) . '" posts_per_page="12"]'); ?>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('resource_category_page', 'nexturn_resource_category_page');
add_action('wp_footer', 'nexturn_global_resource_modal');

function nexturn_global_resource_modal() {
?>
<div class="modal fade" id="resource_form_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content p-4">

            <div class="modal-header border-0 pb-0">
                <h2 class="modal-title">Download Resource</h2>
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
<?php
}