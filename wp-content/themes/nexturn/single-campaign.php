<?php get_header(); ?>
<div class="container-fluid campaign-full" style="padding-top:60px;">
    <?php
       $url = rwmb_meta('campaign_external_url');

        $raw_html = wp_external_page_fetch($url);

        if ($raw_html) {

            $clean_html = extract_clean_body_content($raw_html);
            $final_html = fix_relative_urls($clean_html, $url);

            echo '<div class="external-content">';
            echo $final_html;
            echo '</div>';

        } else {
            echo '<p>Unable to load content.</p>';
        }

    ?> 
    <?php        
        echo do_shortcode('[campaign-float-form form_id="1c318af" form_title="Campaign Form"]');      
    ?>
     
</div>
<?php get_footer(); ?>
