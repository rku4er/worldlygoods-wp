<?php

namespace Roots\Sage\Shortcodes;
use Roots\Sage\Utils;

/**
 * Fullscreen slider shortcode
 */
add_shortcode( 'slider', __NAMESPACE__.'\\slider_init' );
function slider_init( $attr ){
    extract(
        shortcode_atts( array(
            "animation"  => 'slide',
            "interval"   => 5000,
            "parallax"   => false,
            "pause"      => false,
            "wrap"       => false,
            "keyboard"   => false,
            "arrows"     => false,
            "bullets"    => false,
            "fullscreen" => false,
        ), $attr )
    );


    if( isset($GLOBALS['carousel_count']) )
      $GLOBALS['carousel_count']++;
    else
      $GLOBALS['carousel_count'] = 0;

    global $wp_query;
    $page_ID = $wp_query->queried_object->ID;
    $prefix = 'sage_slider_';
    $slides = get_post_meta( $page_ID, $prefix .'group', true );

    if($slides){

        $animation   = $animation ? $animation : get_post_meta( $page_ID, $prefix .'animation', true );
        $interval    = $interval ? $interval : get_post_meta( $page_ID, $prefix .'interval', true );
        $parallax    = $parallax ? $parallax : get_post_meta( $page_ID, $prefix .'parallax', true );
        $pause       = $pause ? 'hover' : get_post_meta( $page_ID, $prefix .'pause', true );
        $wrap        = $wrap ? $wrap : get_post_meta( $page_ID, $prefix .'wrap', true );
        $keyboard    = $keyboard ? $keyboard : get_post_meta( $page_ID, $prefix .'keyboard', true );
        $arrows      = $arrows ? $arrows : get_post_meta( $page_ID, $prefix .'arrows', true );
        $bullets     = $bullets ? $bullets : get_post_meta( $page_ID, $prefix .'bullets', true );
        $fullscreen  = $fullscreen ? $fullscreen : get_post_meta( $page_ID, $prefix .'fullscreen', true );

        $div_class   = 'row carousel carousel-inline'
            . (($animation === 'fade') ? ' slide carousel-fade' : ' slide')
            . ($fullscreen ? ' carousel-fullscreen' : '')
            . ($progress ? ' carousel-progress' : '');
        $inner_class = 'carousel-inner';
        $id          = 'custom-carousel-'. $GLOBALS['carousel_count'];

        $indicators = array();
        $items = array();

        $i = -1;

        foreach($slides as $slide):
            $i++;

            $image_obj = wp_get_attachment_image_src($slide['image_id'], 'slider');
            $image_original = preg_replace("/-\d+x\d+/", "$2", $image_obj[0]);;
            $target = $slide['new_tab'] ? 'target="_blank"' : '';

            $image = sprintf('%s<img src="%s" alt="" >%s',
                $slide['link_url'] ? '<a href="' . $slide['link_url'] . '" '. $target .'>' : '',
                $image_obj[0],
                $slide['link_url'] ? '</a>' : ''
            );

            $item_style = sprintf('%s',
                sprintf('background-image: url(%s); background-attachment: %s;',
                    $image_obj[0],
                    $parallax ? 'fixed' : 'scroll'
                )
            );

            if($slide['title_text']){
                $title_style = '
                    color: '. $slide['title_color'] .';
                    -webkit-animation-delay: '. $slide['title_anim_delay'] .'s;
                    animation-delay: '. $slide['title_anim_delay'] .'s;
                    -webkit-animation-duration: '. $slide['title_anim_duration'] .'s;
                    animation-duration: '. $slide['title_anim_duration'] .'s;
                ';
                $title_html = '<h3 class="slide-title" data-animated="true" data-animation="'. $slide['title_anim'] .'" style="'
                    . $title_style .'">'
                    . $slide['title_text'] . '</h3>';
            }

            if($slide['caption_text']){
                $caption_style = '
                    color: '. $slide['caption_color'] .';
                    -webkit-animation-delay: '. $slide['caption_anim_delay'] .'s;
                    animation-delay: '. $slide['caption_anim_delay'] .'s;
                    -webkit-animation-duration: '. $slide['caption_anim_duration'] .'s;
                    animation-duration: '. $slide['caption_anim_duration'] .'s;
                ';
                $caption_html = '<div class="slide-caption hidden-xs" data-animated="true" data-animation="'. $slide['caption_anim'] .'" style="'
                    . $caption_style .'"><p>'
                    . $slide['caption_text'] . '</p></div>';
            }

            $overlay_style = '
                -webkit-animation-delay: ' . $slide['overlay_anim_delay'] . 's;
                animation-delay: ' . $slide['overlay_anim_delay'] . 's;
                -webkit-animation-duration: ' . $slide['overlay_anim_duration'] . 's;
                animation-duration: ' . $slide['overlay_anim_duration'] . 's;
            ';
            $overlay_inner_style = '
                background-color:' . $slide['overlay_color'] . ';
                opacity: ' . $slide['overlay_opacity'] . ';
            ';
            $overlay_html = '<span data-animated="true" data-animation="' . $slide['overlay_anim'] . '" style="' . $overlay_style . '"><span style="' . $overlay_inner_style . '"></span>';

            if($slide['show_caption']){
                $caption = sprintf(
                    '<div class="carousel-caption container %s %s">'
                        .'<div>'
                            .'<div>'
                                .'<div class="text-wrapper" style="%s">%s%s%s</div>'
                            .'</div>'
                        .'</div>'
                    .'</div>',
                    'align-'.$slide['align'],
                    'valign-'.$slide['valign'],
                    'max-width: '. $slide['text_width'],
                    $title_html,
                    $caption_html,
                    $overlay_html
                );
            }

            $active_class = ($i == 0) ? ' active' : '';

            $indicators[] = sprintf(
              '<li class="%s" data-target="%s" data-slide-to="%s"></li>',
              $active_class,
              esc_attr( '#' . $id ),
              esc_attr( $i )
            );

            $items[] = sprintf(
              '<div class="%s" style="%s">%s%s</div>',
              'item' . $active_class,
              $item_style,
              $image,
              $caption
          );

        endforeach;

        return sprintf(
          '<div class="%s" id="%s" data-ride="carousel" %s%s%s%s>'
              . '%s<div class="%s">%s</div>%s</div>',
          esc_attr( $div_class ),
          esc_attr( $id ),
          ( $parallax )   ? sprintf( ' data-type="%s"', 'parallax' ) : '',
          ( $interval )   ? sprintf( ' data-interval="%d"', ( $interval * 1000 )) : '',
          ( $pause )      ? sprintf( ' data-pause="%s"', esc_attr( $pause ) ) : '',
          ( $wrap )       ? sprintf( ' data-wrap="%s"', esc_attr( $wrap ) ) : '',
          ( $bullets ) ? '<ol class="carousel-indicators hidden-xs">' . implode( $indicators ) . '</ol>' : '',
          esc_attr( $inner_class ),
          implode($items),
          ( $arrows ) ? sprintf( '%s%s',
              '<a class="left carousel-control"  href="' . esc_url( '#' . $id ) . '" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>',
              '<a class="right carousel-control" href="' . esc_url( '#' . $id ) . '" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>'
          ) : ''
        );
    }
}

/**
 * Socials
 */

add_shortcode( 'socials', __NAMESPACE__.'\\socials_init' );
function socials_init( $attr ){
    extract( shortcode_atts( array(
        'label' => false
    ), $attr ));

    global $redux_demo;
    $options = $redux_demo;

    $socials = $options['socials'];

    if($socials){
        $buffer = '<span class="socials">';
        $buffer .= $label ? '<span>'.$label.'</span>' : '';

        foreach( $socials as $key => $value ){
            $buffer .= $value ? '<a href="'. $value . '" target="_blank"><i class="fa fa-'. strtolower($key) .'"></i></a>' : '';
        }
        $buffer .= '</span>';
        return $buffer;
    }
}

/**
  *
  * bs_tabs
  *
  * @author Filip Stefansson
  * @since 1.0
  * Modified by TwItCh twitch@designweapon.com
  * Now acts a whole nav/tab/pill shortcode solution!
  */
add_shortcode( 'tabs_vertical', __NAMESPACE__.'\\bs_tabs_vertical' );
function bs_tabs_vertical( $atts, $content = null ) {

  if( isset( $GLOBALS['tabs_count'] ) )
    $GLOBALS['tabs_count']++;
  else
    $GLOBALS['tabs_count'] = 0;

  $GLOBALS['tabs_default_count'] = 0;

  $atts = shortcode_atts( array(
    "type"   => false,
    "xclass" => false,
    "data"   => false
  ), $atts );

  $ul_class  = 'nav';
  $ul_class .= ( $atts['type'] )     ? ' nav-' . $atts['type'] : ' nav-tabs';
  $ul_class .= ( $atts['xclass'] )   ? ' ' . $atts['xclass'] : '';
  $ul_class .= ' tabs-vertical tabs-left';

  $div_class = 'tab-content';

  $id = 'custom-tabs-'. $GLOBALS['tabs_count'];

  $data_props = $atts['data'];

  $atts_map = Utils\attribute_map( $content );

  // Extract the tab titles for use in the tab widget.
  if ( $atts_map ) {
    $tabs = array();
    $GLOBALS['tabs_default_active'] = true;
    foreach( $atts_map as $check ) {
        if( !empty($check["tab"]["active"]) ) {
            $GLOBALS['tabs_default_active'] = false;
        }
    }
    $i = 0;
    foreach( $atts_map as $tab ) {

      $class  ='';
      $class .= ( !empty($tab["tab"]["active"]) || ($GLOBALS['tabs_default_active'] && $i == 0) ) ? 'active' : '';
      $class .= ( !empty($tab["tab"]["xclass"]) ) ? ' ' . $tab["tab"]["xclass"] : '';

      $tabs[] = sprintf(
        '<li%s><a href="#%s" data-toggle="tab">%s</a></li>',
        ( !empty($class) ) ? ' class="' . $class . '"' : '',
        'custom-tab-' . $GLOBALS['tabs_count'] . '-' . md5($tab["tab"]["title"]),
        $tab["tab"]["title"]
      );
      $i++;
    }
  }
  return sprintf(
    '<div class="col-sm-3"><ul class="%s" id="%s"%s>%s</ul></div><div class="col-sm-9"><div class="%s">%s</div></div>',
    esc_attr( $ul_class ),
    esc_attr( $id ),
    ( $data_props ) ? ' ' . $data_props : '',
    ( $tabs )  ? implode( $tabs ) : '',
    esc_attr( $div_class ),
    do_shortcode( $content )
  );
}
