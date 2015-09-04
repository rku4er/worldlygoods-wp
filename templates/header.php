<?php
    use Roots\Sage\Nav\NavWalker;
?>
<?php
    global $redux_demo;
    $options = $redux_demo;
    $page_ID = $wp_query->queried_object->ID;
    $prefix = 'sage_page_options_';
    $navbar_position = get_post_meta( $page_ID, $prefix .'navbar_position', true );
    $navbar_position = $navbar_position ? 'navbar-' . $navbar_position : 'navbar-fixed-top';
    $navbar_logo = $options['navbar-logo'];
    $logo_image = $navbar_logo['url'] ? '<img src="' . esc_url($navbar_logo['url']) . '" alt="' . get_bloginfo('name') . '">' : '';
    $navbar_brand = sprintf(
        '<a class="%s" href="%s">%s</a>',
        'navbar-brand withoutripple',
        esc_url(home_url('/')),
        $logo_image ? $logo_image  : '<strong>'.get_bloginfo('name').'</strong><span>'.get_bloginfo('description') . '</span>'
    );
    if($options['header_layout'] === '1'){
        $container_class = 'container';
    }else if($options['header_layout'] === '2'){
        $container_class = 'container-fluid';
    }
?>
<header class="banner navbar navbar-default <?php echo $navbar_position; ?>" role="banner">
    <div class="<?php echo $container_class; ?>">
        <div class="navbar-header">
          <?php echo $navbar_brand; ?>
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only"><?php echo __('Toggle navigation', 'sage'); ?></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>

        <nav class="collapse navbar-collapse" role="navigation">
          <?php
          if (has_nav_menu('primary_navigation')) :
            wp_nav_menu(['theme_location' => 'primary_navigation', 'walker' => new NavWalker(), 'menu_class' => 'nav navbar-nav']);
          endif;
          ?>
          <?php echo do_shortcode('[socials]'); ?>
        </nav>

  </div>
</header>
