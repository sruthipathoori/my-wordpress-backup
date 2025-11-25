<?php
if (!class_exists('Redux')) {
    return;
}

$opt_name = 'nexturn_opt';

$theme = wp_get_theme(); // For use with some settings. Not necessary.

$args = array(
    // This is where your data is stored in the database and also becomes your global variable name.
    'opt_name' => $opt_name,

    // Name that appears at the top of your panel.
    'display_name' => $theme->get('Name'),

    // Version that appears at the top of your panel.
    'display_version' => $theme->get('Version'),

    // Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only).
    'menu_type' => 'menu',

    // Show the sections below the admin menu item or not.
    'allow_sub_menu' => true,

    // The text to appear in the admin menu.
    'menu_title' => esc_html__('Theme Options', 'your-textdomain-here'),

    // The text to appear on the page title.
    'page_title' => esc_html__('Theme Options', 'your-textdomain-here'),

    // Disable to create your own Google fonts loader.
    'disable_google_fonts_link' => false,

    // Show the panel pages on the admin bar.
    'admin_bar' => true,

    // Icon for the admin bar menu.
    'admin_bar_icon' => 'dashicons-admin-generic',

    // Priority for the admin bar menu.
    'admin_bar_priority' => 50,

    // Sets a different name for your global variable other than the opt_name.
    'global_variable' => $opt_name,

    // Show the time the page took to load, etc. (forced on while on localhost or when WP_DEBUG is enabled).
    'dev_mode' => false,

    // Enable basic customizer support.
    'customizer' => true,

    // Allow the panel to open expanded.
    'open_expanded' => false,

    // Disable the save warning when a user changes a field.
    'disable_save_warn' => false,

    // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
    'page_priority' => 60,

    // For a full list of options, visit: https://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters.
    'page_parent' => 'themes.php',

    // Permissions needed to access the options panel.
    'page_permissions' => 'manage_options',

    // Specify a custom URL to an icon.
    'menu_icon' => '',

    // Force your panel to always open to a specific tab (by id).
    'last_tab' => '',

    // Icon displayed in the admin panel next to your menu_title.
    'page_icon' => 'icon-themes',

    // Page slug used to denote the panel, will be based off page title, then menu title, then opt_name if not provided.
    'page_slug' => $opt_name,

    // On load save the defaults to DB before user clicks save.
    'save_defaults' => true,

    // Display the default value next to each field when not set to the default value.
    'default_show' => false,

    // What to print by the field's title if the value shown is default.
    'default_mark' => '*',

    // Shows the Import/Export panel when not used as a field.
    'show_import_export' => true,

    // Shows the Options Object for debugging purposes. Show be set to false before deploying.
    'show_options_object' => false,

    // The time transients will expire when the 'database' arg is set.
    'transient_time' => 60 * MINUTE_IN_SECONDS,

    // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output.
    'output' => true,

    // Allows dynamic CSS to be generated for customizer and google fonts,
    // but stops the dynamic CSS from going to the page head.
    'output_tag' => true,

    // Disable the footer credit of Redux. Please leave if you can help it.
    'footer_credit' => '',

    // If you prefer not to use the CDN for ACE Editor.
    // You may download the Redux Vendor Support plugin to run locally or embed it in your code.
    'use_cdn' => true,

    // Set the theme of the option panel.  Use 'wp' to use a more modern style, default is classic.
    'admin_theme' => 'wp',

    // Enable or disable flyout menus when hovering over a menu with submenus.
    'flyout_submenus' => true,

    // Mode to display fonts (auto|block|swap|fallback|optional)
    // See: https://developer.mozilla.org/en-US/docs/Web/CSS/@font-face/font-display.
    'font_display' => 'swap',

    // HINTS.
    'hints' => array(
        'icon' => 'el el-question-sign',
        'icon_position' => 'right',
        'icon_color' => 'lightgray',
        'icon_size' => 'normal',
        'tip_style' => array(
            'color' => 'red',
            'shadow' => true,
            'rounded' => false,
            'style' => '',
        ),
        'tip_position' => array(
            'my' => 'top left',
            'at' => 'bottom right',
        ),
        'tip_effect' => array(
            'show' => array(
                'effect' => 'slide',
                'duration' => '500',
                'event' => 'mouseover',
            ),
            'hide' => array(
                'effect' => 'slide',
                'duration' => '500',
                'event' => 'click mouseleave',
            ),
        ),
    ),

    // FUTURE → Not in use yet, but reserved or partially implemented.
    // Use at your own risk.
    // Possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
    'database' => '',
    'network_admin' => true,
    'search' => true,
);

Redux::set_args($opt_name, $args);

Redux::set_section(
    $opt_name,
    array(
        'title' => esc_html__('Global Settings', 'your-textdomain-here'),
        'id' => 'global_settings',
        'desc' => esc_html__('These settings will be applicable through out the website', 'your-textdomain-here'),
        'icon' => 'el el-globe-alt',
        'fields' => array(
            array(
                'id' => 'site_logo',
                'type' => 'media',
                'url' => true,
                'title' => esc_html__('Site Logo', 'your-textdomain-here'),
                'compiler' => 'true',
                'subtitle' => esc_html__('Logo to appear at the header', 'your-textdomain-here'),
                'preview_size' => 'medium',
            ),
            /*array(
                'id' => 'font_family',
                'type' => 'typography',
                'title' => esc_html__('Font Family', 'your-textdomain-here'),
                'subtitle' => esc_html__('Specify the font family for website.', 'your-textdomain-here'),
                'google' => true,
                'font_family_clear' => false,
                'font_style' => false,
                'font-weight' => false,
                'font-size' => false,
                'line-height' => false,
                'text-align' => false,
                'color' => false,
                'output' => array('body')
            ),*/
            array(
                'id' => 'cta_btn_color',
                'type' => 'color',
                'title' => esc_html__('Call-to-action Button Color', 'your-textdomain-here'),
                //'subtitle' => esc_html__('Pick a color for call-to-action buttons ', 'your-textdomain-here'),
                'desc' => esc_html__('Color will be applied for all CTA like Partner With Us, Join Our Team etc.', 'your-textdomain-here'),
                'default' => '#3dcbff',
                'validate' => 'color',
                'output' => array(
                    'color' => '.cta-btn .home-bn-arrow, .cta-btn .home-bn-arrow path, .cta-btn .home-bn-arrow plygon, .cta-btn .home-bn-arrow circle',
                    'fill' => '.cta-btn .home-bn-arrow, .cta-btn .home-bn-arrow path, .cta-btn .home-bn-arrow plygon, .cta-btn .home-bn-arrow circle'
                )
            ),
            array(
                'id' => 'cta_btn_svg',
                'type' => 'textarea',
                'title' => esc_html__('Call-to-action Button SVG Code', 'your-textdomain-here'),
                'desc' => esc_html__('SVG Code for the Icon of the call-to-action button.', 'your-textdomain-here'),
                'rows' => 10
            ),
            array(
                'id' => 'cta_btn_svg_class',
                'type' => 'text',
                'title' => esc_html__('Call-to-action Button SVG CSS Class', 'your-textdomain-here'),
                'data' => array(
                    'home_page' => 'Home Page',
                    'other_page' => 'Other page',
                )
            ),
            array(
                'id' => 'cta_partner_modal_title',
                'type' => 'text',
                'title' => esc_html__('Modal Title for [home-partner-with-us] & [partner-with-us]', 'your-textdomain-here')
            ),
            array(
                'id' => 'cta_partner_contact_code',
                'type' => 'text',
                'title' => esc_html__('CF7 Shortcode for [home-partner-with-us] & [partner-with-us]', 'your-textdomain-here'),
                'class'=>'large-text'
            ),
            array(
                'id' => 'anchor_color',
                'type' => 'color',
                'title' => esc_html__('Anchor Tag Color', 'your-textdomain-here'),
                //'subtitle' => esc_html__('Pick a color for call-to-action buttons ', 'your-textdomain-here'),
                'desc' => esc_html__('Color will be applied for all <a> tag & Know More buttons throughout the website.', 'your-textdomain-here'),
                'default' => '#2251ff',
                'validate' => 'color',
                'output' => array(
                    'color' => 'a, .text-info, a:active, .home-bn-arrow, .home-bn-arrow path, .home-bn-arrow plygon, .home-bn-arrow circle',
                    'fill' => '.home-bn-arrow, .home-bn-arrow path, .home-bn-arrow plygon, .home-bn-arrow circle'
                )
            ),
            array(
                'id' => 'know_more_svg',
                'type' => 'textarea',
                'title' => esc_html__('Know More Button SVG Code', 'your-textdomain-here'),
                'desc' => esc_html__('SVG Code for the Icon of the Know More button.', 'your-textdomain-here'),
                'rows' => 10
            ),
            array(
                'id' => 'know_more_svg_class',
                'type' => 'text',
                'title' => esc_html__('Know More Button SVG CSS Class', 'your-textdomain-here')
            ),
            array(
                'id' => 'ga_section_start',
                'type' => 'section',
                'title' => esc_html__('Google Analytics', 'your-textdomain-here'),
                'subtitle' => esc_html__('Manage settings for Google Analytics', 'your-textdomain-here'),
                'indent' => false
            ),
            array(
                'id' => 'ga_switch',
                'type' => 'switch',
                'title' => 'Status',
                'default' => true,
                'on' => 'Enable',
                'off' => 'Disable'
            ),
            array(
                'id' => 'ga_measure_id',
                'type' => 'text',
                'title' => esc_html__('Google Analytics Measurement ID: ', 'your-textdomain-here')
            ),
            array(
                'id' => 'ga_section_end',
                'type' => 'section',
                'indent' => false
            )
        )
    )
);
Redux::set_section(
    $opt_name,
    array(
        'title' => esc_html__('Footer Settings', 'your-textdomain-here'),
        'id' => 'footer_settings',
        'desc' => esc_html__('All the settings to manage the footer content', 'your-textdomain-here'),
        'icon' => 'el  el-circle-arrow-down',
        'fields' => array(
            array(
                'id' => 'site_footer_logo',
                'type' => 'media',
                'url' => true,
                'title' => esc_html__('Site Footer Logo', 'your-textdomain-here'),
                'compiler' => 'true',
                'subtitle' => esc_html__('Logo to appear at the footer', 'your-textdomain-here'),
                'preview_size' => 'medium',
            ),
            array(
                'id' => 'offices_info',
                'type' => 'repeater',
                'title' => esc_html__('Office Information', 'your-textdomain-here'),
                'full_width' => true,
                'subtitle' => esc_html__('Manage the office information in footer section.', 'your-textdomain-here'),
                'item_name' => '',
                'sortable' => true,
                'active' => false,
                'collapsible' => false,
                'fields' => array(
                    array(
                        'id' => 'office_location',
                        'title' => esc_html__('Office Location', 'your-domain-here'),
                        'type' => 'text',
                        'placeholder' => esc_html__('Office Location', 'your-textdomain-here'),
                    ),
                    array(
                        'id' => 'office_address_1',
                        'type' => 'text',
                        //'placeholder' => esc_html__( 'Address Line 1', 'your-textdomain-here' ),
                        'title' => esc_html__('Address Line 1', 'your-domain-here'),
                        'class' => 'large-text'
                    ),
                    array(
                        'id' => 'office_address_2',
                        'type' => 'text',
                        //'placeholder' => esc_html__( 'Address Line 2', 'your-textdomain-here' ),
                        'title' => esc_html__('Address Line 2', 'your-domain-here'),
                        'class' => 'large-text'
                    ),
                    array(
                        'id' => 'show_hide',
                        'type' => 'switch',
                        'placeholder' => esc_html__('Switch Field', 'your-textdomain-here'),
                        'default' => true,
                        'on' => 'Show',
                        'off' => 'Hide'
                    )
                ),
            ),
            array(
                'id' => 'contact_emails',
                'type' => 'multi_text',
                'title' => __('Contact Email Addresses', 'redux_docs_generator'),
                'subtitle' => __('Manage email addresses for footer section', 'redux_docs_generator')
            ),
            array(
                'id' => 'social_icons',
                'type' => 'repeater',
                'title' => esc_html__('Social Icons', 'your-textdomain-here'),
                'full_width' => true,
                'subtitle' => esc_html__('Manage social icons for footer section', 'your-textdomain-here'),
                'item_name' => '',
                'sortable' => true,
                'active' => false,
                'collapsible' => false,
                'fields' => array(
                    array(
                        'id' => 'social_link_title',
                        'title' => esc_html__('Social Link Title', 'your-domain-here'),
                        'type' => 'text',
                        //'placeholder' => esc_html__('Office Location', 'your-textdomain-here'),
                    ),
                    array(
                        'id' => 'social_link_url',
                        'title' => esc_html__('Social Link URL', 'your-domain-here'),
                        'type' => 'text',
                        //'placeholder' => esc_html__('Office Location', 'your-textdomain-here'),
                    ),
                    array(
                        'id' => 'social_link_class',
                        'type' => 'text',
                        //'placeholder' => esc_html__( 'Address Line 1', 'your-textdomain-here' ),
                        'title' => esc_html__('CSS Class', 'your-domain-here')
                    ),
                    array(
                        'id' => 'social_link_svg_code',
                        'type' => 'textarea',
                        //'placeholder' => esc_html__( 'Address Line 2', 'your-textdomain-here' ),
                        'title' => esc_html__('SVG Code for icon', 'your-domain-here'),
                        'rows' => 5
                    ),
                    array(
                        'id' => 'social_link_hover_color',
                        'type' => 'color',
                        'title' => esc_html__('Hover Color', 'your-textdomain-here'),
                        //'subtitle' => esc_html__('Pick a color for call-to-action buttons ', 'your-textdomain-here'),
                        //'desc' => esc_html__('Color will be applied for all CTA like Partner With Us, Join Our Team etc.', 'your-textdomain-here'),
                        //'default' => '#3dcbff',
                        'validate' => 'color',
                        'output' => array(
                            'color' => '.cta-btn .home-bn-arrow, .cta-btn .home-bn-arrow path, .cta-btn .home-bn-arrow plygon, .cta-btn .home-bn-arrow circle',
                            'fill' => '.cta-btn .home-bn-arrow, .cta-btn .home-bn-arrow path, .cta-btn .home-bn-arrow plygon, .cta-btn .home-bn-arrow circle'
                        )
                    ),
                    array(
                        'id' => 'social_show_hide',
                        'type' => 'switch',
                        'placeholder' => esc_html__('Switch Field', 'your-textdomain-here'),
                        'default' => true,
                        'on' => 'Show',
                        'off' => 'Hide'
                    )
                ),
            ),
            array(
                'id' => 'footer_copyright_text',
                'type' => 'text',
                'title' => esc_html__('Copyright Text', 'your-textdomain-here'),
                'class'=>'large-text'
            ),
            array(
                'id' => 'footer_links',
                'type' => 'repeater',
                'title' => esc_html__('Footer Links', 'your-textdomain-here'),
                'full_width' => true,
                'subtitle' => esc_html__('Create links to display at the footer.', 'your-textdomain-here'),
                'item_name' => '',
                'sortable' => true,
                'active' => false,
                'collapsible' => false,
                'fields' => array(
                    array(
                        'id' => 'footer_link_title',
                        'title' => esc_html__('Title', 'your-domain-here'),
                        'type' => 'text'
                    ),
                    array(
                        'id' => 'footer_link_slug_url',
                        'type' => 'text',
                        //'placeholder' => esc_html__( 'Address Line 1', 'your-textdomain-here' ),
                        'title' => esc_html__('Page Slug/URL', 'your-domain-here'),
                        'desc' => 'Enter page slug to link with any internal pages or enter complete URL for any external or media libary resources(Ex: PDF files stored in Media library).',
                        'class' => 'large-text'
                    ),
                    array(
                        'id' => 'footer_link_show_hide',
                        'type' => 'switch',
                        'placeholder' => esc_html__('Switch Field', 'your-textdomain-here'),
                        'default' => true,
                        'on' => 'Show',
                        'off' => 'Hide'
                    )
                ),
            ),
        )
    )
);

Redux::set_section(
    $opt_name,
    array(
        'title' => esc_html__('Careers', 'your-textdomain-here'),
        'id' => 'careers_faq_section',
        'desc' => esc_html__('Manage Settings related to Careers', 'your-textdomain-here'),
        'icon' => 'el  el-edit',
        'fields' => array(
            array(
                'id' => 'career_cta_title',
                'title' => esc_html__('Career CTA Popup Title', 'your-domain-here'),
                'desc' => esc_html__('Ex: Title for Join Our Team popup', 'your-domain-here'),
                'type' => 'text'
            ),
            array(
                'id' => 'career_cta_cf7_shortcode',
                'title' => esc_html__('Career CTA CF7 Shortcode', 'your-domain-here'),
                'desc' => esc_html__('Ex: CF7 shortcode for Join Our Team form', 'your-domain-here'),
                'type' => 'text',
                'class' => 'large-text'
            ),
            array(
                'id' => 'career_apply_title',
                'title' => esc_html__('Apply CTA Popup Title', 'your-domain-here'),
                'desc' => esc_html__('Ex: Title for Apply button popup in Job details page', 'your-domain-here'),
                'type' => 'text',
                'class' => 'large-text'
            ),
            array(
                'id' => 'career_apply_cf7_shortcode',
                'title' => esc_html__('Apply CTA CF7 Shortcode', 'your-domain-here'),
                'desc' => esc_html__('Ex: CF7 shortcode for Apply button popup form in Job details page', 'your-domain-here'),
                'type' => 'text',
                'class' => 'large-text'
            ),
            array(
                'id' => 'faq_section_start',
                'type' => 'section',
                'title' => esc_html__('Careers FAQ', 'your-textdomain-here'),
                'subtitle' => esc_html__('Manage settings for FAQ popup for Careers page.', 'your-textdomain-here'),
                'indent' => true
            ),
            array(
                'id' => 'faq_section_title',
                'title' => esc_html__('FAQ Section Title', 'your-domain-here'),
                'type' => 'text'
            ),
            array(
                'id' => 'careers_faq',
                'type' => 'repeater',
                'title' => esc_html__('FAQ Information', 'your-textdomain-here'),
                'full_width' => true,
                'subtitle' => esc_html__('Manage the FAQ information for the career section.', 'your-textdomain-here'),
                'item_name' => '',
                'sortable' => true,
                'active' => false,
                'collapsible' => false,
                'fields' => array(
                    array(
                        'id' => 'faq_title',
                        'title' => esc_html__('FAQ Title', 'your-domain-here'),
                        'type' => 'text',
                        'class' => 'large-text'
                    ),
                    array(
                        'id' => 'faq_content',
                        'title' => esc_html__('FAQ Content', 'your-domain-here'),
                        'type' => 'textarea',
                        'rows' => 10
                    ),
                    array(
                        'id' => 'faq_show_hide',
                        'type' => 'switch',
                        'placeholder' => esc_html__('Switch Field', 'your-textdomain-here'),
                        'default' => true,
                        'on' => 'Show',
                        'off' => 'Hide'
                    )
                ),
            ),
            array(
                'id' => 'faq_section_end',
                'type' => 'section',
                'indent' => false
            ),
        )
    )
);
