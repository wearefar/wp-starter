<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>
<article class="px-4 py-12">
  <header>
    <h1><?php the_title(); ?></h1>
  </header>

  <?php the_content(); ?>
</article>
<?php endwhile; ?>

<?php get_footer(); ?>
