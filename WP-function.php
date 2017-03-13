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
