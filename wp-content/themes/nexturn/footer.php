<?php
global $nexturn_opt; ?>
<footer class="bg-dark text-white pt-5">
  <div class="container">
    <div class="row">
      <!-- Left column - Offices information -->
      <div class="col-md-6">
        <h2 class="mb-4">Offices</h2>
        <?php
        for ($i = 0; $i <= count($nexturn_opt['office_location']) - 1; $i++):
          if ($nexturn_opt['show_hide'][$i] == '1'):
            ?>
            <div class="mb-4">
              <h5><span class="fw-bold"><?php echo $nexturn_opt['office_location'][$i]; ?></span></h5>
              <p class="mb-0"><?php echo $nexturn_opt['office_address_1'][$i]; ?></p>
              <p><?php echo $nexturn_opt['office_address_2'][$i]; ?></p>
            </div>
          <?php endif; endfor; ?>
      </div>

      <!-- Right column - Contact info -->
      <div class="col-md-6 text-md-end">
        <img 
        src="<?php echo esc_url('http://localhost/wordpress/wp-content/uploads/2025/11/ISO-Badge.png'); ?>"
        alt="ISO Certified" 
        class="iso-badge"

        />

        <div class="pb-4"> <a href="<?php echo site_url(); ?>" class="">
            <img src="<?php echo $nexturn_opt['site_footer_logo']['url'] ?>" alt="Nexturn Logo" width="50">
          </a></div>
        <div class="pb-4">
          <?php foreach ($nexturn_opt['contact_emails'] as $email_addr): ?>
            <p class="mb-0">
              <a href="mailto:<?php echo $email_addr; ?>" target="_blank"><?php echo $email_addr; ?></a>
            </p>
          <?php endforeach; ?>
        </div>
        <div class="">
          <p>Follow us on</p>

          <div class="social-icons">
            <?php
            for ($i = 0; $i <= count($nexturn_opt['social_link_title']) - 1; $i++):
              if ($nexturn_opt['social_show_hide'][$i] == 1):
                ?>
                <a href="<?php echo $nexturn_opt['social_link_url'][$i]; ?>" target="_blank" class="<?php echo $nexturn_opt['social_link_class'][$i];
                   echo $i > 0 ? ' ms-3' : ''; ?>">
                  <?php echo $nexturn_opt['social_link_svg_code'][$i]; ?>
                </a>
              <?php endif; endfor; ?>
          </div>
        </div>
      </div>
    </div>


  </div>
  <div class="bg-black">
    <div class="container end-footer">
      <div class="d-lg-flex align-items-center justify-content-between mt-3 py-3   mt-3 pt-4  ">

        <div class="text-lg-start text-center align-items-center ">

          <p class="small mb-0"><?php echo $nexturn_opt['footer_copyright_text']; ?></p>

        </div>
        <div class="">
          <?php if (count($nexturn_opt['footer_link_title']) > 0): ?>
            <ul class="nav justify-content-md-end justify-content-center small pt-lg-0 pt-2">
              <?php
              for ($i = 0; $i <= count($nexturn_opt['footer_link_title']) - 1; $i++):
                $footer_link_slug_url = $nexturn_opt['footer_link_slug_url'][$i];

                if (filter_var($footer_link_slug_url, FILTER_VALIDATE_URL) === FALSE) {
                  $footer_link = site_url($footer_link_slug_url);

                } else {
                  $footer_link = sanitize_url($footer_link_slug_url);
                }

                ?>
                <li class="nav-item"><span class="px-2"><a href="<?php echo $footer_link; ?>"
                      target="_blank"><?php echo $nexturn_opt['footer_link_title'][$i]; ?></a></span></li>
              <?php endfor; ?>
              <!-- <li class="nav-item"><a href="#" class="nav-link text-white small px-2">Cookie Settings</a></li> -->
            </ul>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <?php echo get_template_part('template', 'contact-popup'); ?>
  <?php wp_footer(); ?>
</footer>


        