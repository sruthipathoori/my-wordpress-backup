<?php /* Template Name: About Us */
get_header();
?>
<section class="innerHero-section"
   style="background-image: url('http://localhost/wordpress/wp-content/uploads/2025/12/Clip-path-group-4.png');">
   <div class="container">
      <div class="innerBanner-text">
         <h1 class="display-4 fw-bold mb-4">Engineering Impact for the Future</h1>
         <p class="lead mb-0">At NextTurn, we are dedicated to harnessing the power of Data & AI to drive innovation,
            efficiency, and transformation for businesses worldwide. Our journey is about creating measurable impact
            with technology that redefines possibilities.</p>
      </div>
   </div>
</section>


<!--  middle comtent -->

<section class="pt-5">
   <div class="container">
      <div class="row">
         <div class="col-12">
            <p class="lead">NexTurn harnesses the power of cloud technologies and AI responsibly, delivering
               industry-specific, sustainable, and impactful solutions that drive growth, efficiency, and a better
               tomorrow.</p>
            <p class="lead mt-4">Unlike traditional system integrators, we embrace a technology-first approach, blending
               deep expertise in AI, cloud engineering, and analytics to deliver transformative results faster. Our
               AI-driven delivery ecosystem, paired with agile advisory models, ensures cost-effective, scalable
               solutions that consistently outperform industry benchmarks.</p>
            <p class="lead mt-4">Our commitment to a "people-first culture" shines through in personalized client
               engagements and end-to-end support. From enhancing customer experiences to modernizing legacy
               infrastructures, NexTurn combines innovation, agility, and expertise to unlock measurable value.</p>
         </div>
      </div>
   </div>
</section>

<section class="team">
   <div class="container">
      <h2 class="text-left display-4">Team</h2>
      <div class="team-section">
         <div class="row g-4">
            <?php echo do_shortcode('[put_team_thumbs]'); ?>
         </div>
      </div>
</section>
<?php get_template_part('template', 'partner-contact') ?>
<?php get_footer(); ?>