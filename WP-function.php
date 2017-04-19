// delete trash
remove_action ('wp_head', 'rsd_link');
remove_theme_support ('automatic-feed-links');
remove_action ('wp_head', 'wlwmanifest_link');
remove_action ('wp_head', 'wp_generator');
show_admin_bar(false);


// Header menu
register_nav_menus( array(
	'menu'));
  
  
//реєструємо наші скріпти в футер та стилі
function theme_name_scripts() {
        //реєструємо наш jquery (відміняємо jquery worpdress)
  wp_deregister_script( 'jquery' );
	wp_register_script('jquery', get_template_directory_uri() . '/js/jquery-3.1.1.min.js', '','', true);
	wp_enqueue_script( 'jquery' );
  
	wp_enqueue_script('googleMap', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAowdFNkUGZ-b6WvxwkyII2Lry2hjO6vcY', '', '', true);
	wp_enqueue_script('script', get_template_directory_uri() . '/js/common.js', '','', true);
  
	wp_enqueue_style('lightboxCSS', get_template_directory_uri() . '/css/lightbox.css');
	wp_enqueue_style('style', get_stylesheet_uri());
}
add_action( 'wp_enqueue_scripts', 'theme_name_scripts' );



//thumbnails in posts
add_theme_support('post-thumbnails'); 
//logo через адмінку
add_theme_support( 'custom-header' );
//додаємо можливіість WP самому генерувати <title></title> (тег вже не потрібно давати в хедер)
add_theme_support( 'title-tag' );


//register polylang menu widget
function wpb_widgets_init() {
	register_sidebar( array(
		'name'          => 'Lang switcher',
		'id'            => 'custom-header-widget',
		'before_widget' => '<div class="langWidget">',
		'after_widget'  => '</div>',
		) );
}
add_action( 'widgets_init', 'wpb_widgets_init' );

//register polylang fields
pll_register_string( 'Заголовок', 'Відгуки наших клієнтів', 'theme', false );


//дочірні категорії беруть шаблон батьківської.
//актуально для категорій та single.php
//http://n-wp.ru/17536
add_action('template_redirect', 'use_parent_category_template');
function use_parent_category_template() {
global $cat, $post;
$category = get_category($cat);
if (is_category()):
while ($category->cat_ID) {
        if ( file_exists(TEMPLATEPATH . "/category-" . $category->cat_ID . '.php') ) {
            include(TEMPLATEPATH . "/category-" . $category->cat_ID . '.php');
            exit;
        }
$category = get_category($category->category_parent);
    }
elseif (is_single()) :
 
    $categories = get_the_category($post->ID);
    
    if (count($categories)) foreach ( $categories as $category ) {
while ($category->cat_ID) {
    	    if ( file_exists(TEMPLATEPATH . "/single-" . $category->cat_ID . '.php') ) {
        	    include(TEMPLATEPATH . "/single-" . $category->cat_ID . '.php');
            	exit;
        }
    	    
$category = get_category($category->category_parent);
    }
    
    }
 
endif;
}



//додаємо можливість додавати ACF на панель в wordpress
//в ACF вибираємо сторінка опцій = загальні дані
//виводим поля <?php the_field('number1', 'option'); ?>
if( function_exists('acf_add_options_page') ) {
	acf_add_options_page(array(
		'page_title' 	=> 'Загальні дані (поля які потрібно заповнити знаходяться в плагіні ACF)',
		'menu_title'	=> 'Загальні дані',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
		));
}


//реєстрація Custom Post Types (щоб розділяти записи)
add_action( 'init', 'create_post_type' );
function create_post_type() {
  register_post_type( 'acme_product',
    array(
      'labels' => array(
        'name' => __( 'Products' ),
        'singular_name' => __( 'Product' )
      ),
      'public' => true,
      'has_archive' => true,
    )
  );
}

//видалення slug з url в custom post type (коли використовуємо плагіни Custom Post Type UI, Toolset Types)
//https://kellenmace.com/remove-custom-post-type-slug-from-permalinks/
function custom_remove_course_slug( $post_link, $post, $leavename ) {
    if ( 'course' != $post->post_type || 'publish' != $post->post_status ) {
        return $post_link;
    }
    $post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );
    return $post_link;
}
add_filter( 'post_type_link', 'custom_remove_course_slug', 10, 3 );

function gp_parse_request_trick( $query ) {
    // Only noop the main query
    if ( ! $query->is_main_query() )
        return;
    // Only noop our very specific rewrite rule match
    if ( 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
        return;
    }
    // 'name' will be set if post permalinks are just post_name, otherwise the page rule will match
    if ( ! empty( $query->query['name'] ) ) {
        $query->set( 'post_type', array( 'course', 'bloging', 'university' ) );
    }
}
add_action( 'pre_get_posts', 'gp_parse_request_trick' );

//віджети
function polylangWidg() {
	register_sidebar( array(
		'name'          => 'Lang switcher',
		'id'            => 'lang-switcher',
		'before_widget' => '<div class="langSwitcher">',
		'after_widget'  => '</div>',
		) );
}
add_action( 'widgets_init', 'polylangWidg' );
//місце в шаблоні куда вставляємо
<?php dynamic_sidebar( 'lang-switcher' ); ?>

//додавати посилання на іншу сторінку нашого сайта в редакторі
function permalink_thingy($atts) {
	extract(shortcode_atts(array('id'=>1, 'text'=>''), $atts));
	if($text) {
		$url = get_permalink($id);
		return "<a href='$url'>$text</a>";
	} else {
		return get_permalink($id);
	}
}
add_shortcode('link', 'permalink_thingy');
//приклад коду який вставляємо в редактор
<a href="[link id=233]">Blog</a>
