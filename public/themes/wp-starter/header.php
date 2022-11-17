<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>
<body class="antialiased">
  <header>
    <nav role="navigation">
      <?php wp_nav_menu(['theme_location' => 'main']); ?>
    </nav>
  </header>
