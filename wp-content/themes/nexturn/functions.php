
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
       wp_enqueue_script(
        'jspdf',
        'https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js',
        [],
        '2.5.1',
        true
        );

        $inline_js = sprintf(<<<'JS'
(function() {
    console.log('=== PDF Script Loaded ===');
    
    // Check jsPDF availability
    var checkJsPDF = setInterval(function() {
        if (window.jspdf && window.jspdf.jsPDF) {
            console.log('jsPDF library loaded successfully');
            clearInterval(checkJsPDF);
        }
    }, 100);
    
    setTimeout(function() {
        clearInterval(checkJsPDF);
        if (!window.jspdf || !window.jspdf.jsPDF) {
            console.warn('WARNING: jsPDF library not found after 2 seconds');
        }
    }, 2000);
    
    var ajaxurl = "%s";
    var resourceData= {
    title: '',
    id: ''
    };
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
            if (!wrapper) {
                console.error('PDF: wrapper not found');
                return;
            }

           var descFull = document.getElementById('resource-description-full');
           var description = '';

            if (resourceData.id) {
                try {
                    var response = await fetch(ajaxurl + '?action=get_resource_pdf_content&id=' + resourceData.id);
                    var data = await response.json();
                    if (data.success && data.data && data.data.content) {
                        description = data.data.content;
                    }
                } catch(e) {
                    console.error('Failed to fetch resource content', e);
                }
            } else if (descFull) {
                description = descFull.innerHTML.trim();
            }

           var title = resourceData.title || document.title;

            if (!window.jspdf || !window.jspdf.jsPDF) {
                console.error('PDF: jsPDF library not loaded');
                return;
            }

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
            console.log('PDF generated successfully:', safeName);

        } catch (e) {
            console.error('PDF generation failed:', e.message, e.stack);
        }
    }

        function handler(e){
        if (!e || !e.detail || e.detail.status !== 'mail_sent') {
            console.log('Handler: mail not sent or invalid event');
            return;
        }
        
        var wrapper = document.getElementById('resource-form');
        if (!wrapper) {
            console.error('Handler: resource-form wrapper not found');
            return;
        }
        
        console.log('Handler: mail sent successfully, processing PDF');
        
        try {
            var modalEl = document.getElementById('resource_form_modal');
            if (modalEl && window.bootstrap && window.bootstrap.Modal) {
                var modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.hide();
            }
    
            var form = wrapper.querySelector('form');
            if (form) {
                setTimeout(function(){ form.reset(); }, 150);
            }
        } catch(_) {}
    
        setTimeout(generateResourcePdf, 250);
    }
   

    // Bind CF7 events only when mail has been sent successfully
    if (document.body) {
        console.log('Attaching PDF handler to wpcf7mailsent...');
        document.body.addEventListener('wpcf7mailsent', handler, false);
        
        // Also handle mail failure - still generate PDF but log the issue
        document.body.addEventListener('wpcf7mailfailed', function(e) {
            console.warn('CF7 Mail failed for form:', e.detail);
            console.log('Mail failed detail:', e.detail);
            // Still trigger PDF but with warning
            if (e.detail) {
                console.log('Resource form mail failed - attempting PDF generation anyway');
                
                // Close modal and reset form before generating PDF
                try {
                    var modalEl = document.getElementById('resource_form_modal');
                    if (modalEl && window.bootstrap && window.bootstrap.Modal) {
                        var modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                        modal.hide();
                    }
        
                    var wrapper = document.getElementById('resource-form');
                    var form = wrapper ? wrapper.querySelector('form') : null;
                    if (form) {
                        setTimeout(function(){ form.reset(); }, 150);
                    }
                } catch(_) {}
                
                setTimeout(generateResourcePdf, 250);
            }
        });
    }

    // Also bind at document level for safety
    document.addEventListener('wpcf7mailsent', function(e) {
        console.log('wpcf7mailsent fired on document:', e.detail);
    });
    
    // Catch all CF7 events for debugging
    document.addEventListener('wpcf7submit', function(e) {
        console.log('wpcf7submit fired:', e.detail);
    });
    
    document.addEventListener('wpcf7invalid', function(e) {
        console.log('wpcf7invalid fired:', e.detail);
    });
    
    document.addEventListener('wpcf7mailfailed', function(e) {
        console.log('wpcf7mailfailed fired:', e.detail);
    });

    document.addEventListener('click', function(e){
        var btn = e.target.closest('.resource-open');
        if (!btn) return;

        resourceData.title = btn.getAttribute('data-title') || '';
        resourceData.id = btn.getAttribute('data-resource-id') || '';
    });

    // Fallback: detect form submission via CF7 form directly
    document.addEventListener('DOMContentLoaded', function() {
        var resourceForm = document.querySelector('#resource-form form.wpcf7-form');
        if (resourceForm) {
            console.log('Found CF7 resource form, attaching listeners...');
            
            resourceForm.addEventListener('submit', function(e) {
                console.log('Resource form submit event fired');
            });
            
            resourceForm.addEventListener('wpcf7submit', function(e) {
                console.log('Resource form wpcf7submit event fired:', e);
            });
        }
    });

})();
JS, admin_url('admin-ajax.php'));

        wp_add_inline_script('jspdf', $inline_js);
    }



    add_action('wp_enqueue_scripts', 'nexturn_theme_scripts');
endif;

function resource_enqueue_scripts() {
    // Only localize the existing nexturn-theme script instead of double-loading it incorrectly
    wp_localize_script('nexturn-theme', 'ajax_object', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);
}
add_action('wp_enqueue_scripts', 'resource_enqueue_scripts');

// AJAX endpoint to fetch resource content securely for PDF generation
add_action('wp_ajax_get_resource_pdf_content', 'nexturn_get_resource_pdf_content');
add_action('wp_ajax_nopriv_get_resource_pdf_content', 'nexturn_get_resource_pdf_content');
function nexturn_get_resource_pdf_content() {
    $resource_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($resource_id) {
        $content = rwmb_meta('resource_text', [], $resource_id);
        if (empty($content)) {
            $post = get_post($resource_id);
            if ($post) {
                $content = apply_filters('the_content', $post->post_content);
            }
        }
        // Return full HTML so jsPDF can parse headings
        wp_send_json_success(array('content' => $content));
    }
    wp_send_json_error();
}
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
            'excerpt' => wp_trim_words(strip_tags($post->post_content), 20, '')
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
 
 

//resource card shortcode 
//new shortcode for latest resource
function nexturn_latest_resource_shortcode($atts) {

    $atts = shortcode_atts([
        'group' => ''
    ], $atts);

    // QUERY
    $args = [
        'post_type'      => 'resource',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];

    // FILTER BY GROUP
    if (!empty($atts['group'])) {
        $args['tax_query'] = [[
            'taxonomy' => 'resource_group',
            'field'    => 'slug',
            'terms'    => sanitize_title($atts['group']),
        ]];
    }

    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();

        // IMAGE
        $image_meta = rwmb_meta('resource_image', ['size' => 'large'], $post_id);
        $image_url  = ($image_meta && is_array($image_meta))
            ? reset($image_meta)['url']
            : get_the_post_thumbnail_url($post_id, 'large');

        // CONTENT
        $summary = rwmb_meta('resource_summary', [], $post_id);
        $full    = rwmb_meta('resource_text', [], $post_id);

        $clean_summary = wp_kses_post($summary);

        if (!empty(trim(strip_tags($clean_summary)))) {
            $desc = wp_trim_words(strip_tags($clean_summary), 35, '...');
            $display_content = wpautop($clean_summary);
        } else {
            $desc = wp_trim_words(strip_tags($full), 35, '...');
            $display_content = wpautop($desc);
        }
        // DATE
        $date = rwmb_meta('resource_date', [], $post_id);
        $display_date = $date
            ? date('Y-m-d', strtotime($date))
            : get_the_date('Y-m-d', $post_id);
        ?>

        <div class="latest-resource-card">
            <div class="latest-resource-image" >
                <?php if ($image_url): ?>
                    <img src="<?php echo esc_url($image_url); ?>" style="width:100%;">
                <?php endif; ?>
            </div>
            <div class="latest-resource-content" >

                <div class="story-title"><?php echo esc_html(get_the_title()); ?></div>

                <div class="resource-date" style="margin:10px 0;">
                    <?php echo esc_html($display_date); ?>
                </div>

                <div class="resource-desc" style="color:#000">
                    <?php echo wp_kses_post($display_content); ?>
                </div>
                <!-- DOWNLOAD BUTTON -->
                <a href="javascript:void(0);"
                   class="svg-container download-btn resource-open"
                   data-bs-toggle="modal"
                   data-bs-target="#resource_form_modal"
                   data-resource-id="<?php echo esc_attr($post_id); ?>"
                   data-title="<?php echo esc_attr(get_the_title()); ?>">
                   
                    <span>Download</span>

                    <svg class="home-bn-arrow" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <g data-name="Layer 2">
                            <path d="M1 16a15 15 0 1 1 15 15A15 15 0 0 1 1 16Zm28 0a13 13 0 1 0-13 13 13 13 0 0 0 13-13Z"></path>
                            <path d="M12.13 21.59 17.71 16l-5.58-5.59a1 1 0 0 1 0-1.41 1 1 0 0 1 1.41 0l6.36 6.36a.91.91 0 0 1 0 1.28L13.54 23a1 1 0 0 1-1.41 0 1 1 0 0 1 0-1.41Z"></path>
                        </g>
                    </svg>

                </a>

            </div>

        </div>

        <?php
    } else {
        echo '<p>No resource found.</p>';
    }

    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode('latest_resource', 'nexturn_latest_resource_shortcode');

//new shortcode for case study and article
function nexturn_resource_cards_shortcode($atts) {

    $atts = shortcode_atts([
        'group' => '', // optional
        'posts_per_page' => 10
    ], $atts);

    //GET LATEST POST ID
    $latest_args = [
        'post_type' => 'resource',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'orderby' => 'date',
        'order' => 'DESC',
    ];

    if (!empty($atts['group'])) {
        $latest_args['tax_query'] = [[
            'taxonomy' => 'resource_group',
            'field' => 'slug',
            'terms' => sanitize_title($atts['group']),
        ]];
    }

    $latest_query = new WP_Query($latest_args);
    $latest_id = ($latest_query->have_posts()) ? $latest_query->posts[0]->ID : 0;
    wp_reset_postdata();

    //GET ALL EXCEPT LATEST
    $args = [
        'post_type' => 'resource',
        'post_status' => 'publish',
        'posts_per_page' => intval($atts['posts_per_page']),
        'orderby' => 'date',
        'order' => 'DESC',
        'post__not_in' => [$latest_id],
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
    

    if ($query->have_posts()) {

        echo '<div class="resource-carousel owl-carousel owl-theme">';

        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();

            // IMAGE
            $image_meta = rwmb_meta('resource_image', ['size' => 'medium'], $post_id);
            $image_url  = ($image_meta && is_array($image_meta))
                ? reset($image_meta)['url']
                : get_the_post_thumbnail_url($post_id, 'medium');

            // CONTENT
            $summary = rwmb_meta('resource_summary', [], $post_id);
            $full    = rwmb_meta('resource_text', [], $post_id);

            $clean_summary = wp_kses_post($summary);

            if (!empty(trim(strip_tags($clean_summary)))) {
                $desc = wp_trim_words(strip_tags($clean_summary), 35, '...');
                $display_content = wpautop($clean_summary);
            } else {
                $desc = wp_trim_words(strip_tags($full), 35, '...');
                $display_content = wpautop($desc);
            }
            // DATE
            $date = rwmb_meta('resource_date', [], $post_id);
            $display_date = $date
                ? date('Y-m-d', strtotime($date))
                : get_the_date('Y-m-d', $post_id);

            ?>
            <div class="item">
                <div class="resource-card">
                    <!-- IMAGE -->
                    <div class="resource-card-img">
                        <img src="<?php echo esc_url($image_url); ?>">
                    </div>
                    <!-- CONTENT -->
                    <div class="resource-card-content">
                        <h5><?php echo esc_html(get_the_title()); ?></h5>

                        <div class="resource-date">
                            <?php echo esc_html($display_date); ?>
                        </div>

                        <div class="resource-desc">
                            <?php echo wp_kses_post($display_content); ?>
                        </div>
                        <!-- DOWNLOAD BUTTON -->
                        <a href="javascript:void(0);"
                           class="svg-container download-btn resource-open"
                           data-bs-toggle="modal"
                           data-bs-target="#resource_form_modal"
                           data-resource-id="<?php echo esc_attr($post_id); ?>"
                           data-title="<?php echo esc_attr(get_the_title()); ?>">
                            <span>Download</span>
                            <svg class="home-bn-arrow" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                                <g data-name="Layer 2">
                                    <path d="M1 16a15 15 0 1 1 15 15A15 15 0 0 1 1 16Zm28 0a13 13 0 1 0-13 13 13 13 0 0 0 13-13Z"></path>
                                    <path d="M12.13 21.59 17.71 16l-5.58-5.59a1 1 0 0 1 0-1.41 1 1 0 0 1 1.41 0l6.36 6.36a.91.91 0 0 1 0 1.28L13.54 23a1 1 0 0 1-1.41 0 1 1 0 0 1 0-1.41Z"></path>
                                </g>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            <?php
        }
        echo '</div>';
    } else {
        echo '<p>No resources found.</p>';
    }
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('resource_cards', 'nexturn_resource_cards_shortcode');


//download model for resources
add_action('wp_footer', 'nexturn_global_resource_modal');

function nexturn_global_resource_modal() {
?>
<div class="modal fade" id="resource_form_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content p-3">

            <div class="modal-header border-0 pb-0">
                <h2 class="modal-title">Download Resource</h2>
                <button type="button" class="close close-btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
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



//Keyword search for resources
add_action('wp_ajax_resource_keyword_search', 'resource_keyword_search');
add_action('wp_ajax_nopriv_resource_keyword_search', 'resource_keyword_search');

function resource_keyword_search() {

    $keyword = isset($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '';

    $group = isset($_POST['group']) ? sanitize_text_field($_POST['group']) : '';

    $args = [
        'post_type' => 'resource',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    ];

    if (!empty($group)) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'resource_group',
                'field'    => 'slug',
                'terms'    => $group,
            ]
        ];
    }

    if (!empty($keyword)) {
        $args['meta_query'] = [
            [
                'key'     => 'resource_keywords',
                'value'   => $keyword,
                'compare' => 'LIKE'
            ]
        ];
    }

    $query = new WP_Query($args);

    ob_start();

    echo '<div class="resource-carousel owl-carousel owl-theme">';

    if ($query->have_posts()) {

        while ($query->have_posts()) {
        $query->the_post();
        global $post;

        echo '<div class="item">';
        echo nexturn_render_resource_card($post); // remove the nexturn_render_resource_card function
   }
    } else {
        echo '<p>No matching resources found</p>';
    }

    echo '</div>';

    wp_reset_postdata();

    echo ob_get_clean();
    wp_die();
}
