// clear head
if ( ! function_exists('cleanup_head') ) {
  function cleanup_head() {
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'index_rel_link');
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
    remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_theme_support ('automatic-feed-links');
  }
}
add_action('init', 'cleanup_head');

// Remove Query Strings From Static Resources
if ( ! function_exists('progenitor_remove_script_version') ) {
  function progenitor_remove_script_version( $src ) {
    $parts = explode( '?', $src );
    return $parts[0];
  }
}
add_filter( 'script_loader_src', 'progenitor_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', 'progenitor_remove_script_version', 15, 1 );


//remove WP version from RSS
if ( ! function_exists('remove_wp_version') ) {
  function remove_wp_version() {
	return '';
  }
}
add_filter( 'the_generator', 'remove_wp_version' );


// Show less info to users on failed login for security.
if ( ! function_exists('show_less_login_info') ) {
  function show_less_login_info() {
      return "<strong>ERROR</strong>: Stop guessing!";
  }
}
add_filter( 'login_errors', 'show_less_login_info' );



show_admin_bar(false);

********************************************************************  

// відміняємо показ вибраного терміну наверху в списку термінів
add_filter( 'wp_terms_checklist_args', 'set_checked_ontop_default', 10 );
function set_checked_ontop_default( $args ) {
	if( ! isset($args['checked_ontop']) )
		$args['checked_ontop'] = false;

	return $args;
}

******************************************************************** 

//Відключаєм можливітсь редагувати файли в адмінці для тем, плагінів
define('DISALLOW_FILE_EDIT', true);

******************************************************************** 

// Header menu
register_nav_menus( array(
		'main' => esc_html__( 'Main', 'Theme' ),
		'submain' => esc_html__( 'Submain', 'Theme' ),
		'footerMenu' => esc_html__( 'FooterMenu', 'Theme' ),
   ) );
<?php wp_nav_menu( array( 'theme_location' => 'footerMenu', 'menu_class' => 'footerMenu', 'container' => 'ul' ) ); ?>

********************************************************************  
  
//реєструємо наші скріпти в футер та стилі
function theme_name_scripts() {
        //реєструємо наш jquery (відміняємо jquery worpdress)
  	wp_deregister_script( 'jquery' );
	wp_register_script('jquery', get_template_directory_uri() . '/js/jquery-3.1.1.min.js', '','', true);
	wp_enqueue_script( 'jquery' );
  	
	//підключаємо jquery який є в wordpress
	wp_enqueue_script( 'jquery' );

	wp_enqueue_script('googleMap', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAowdFNkUGZ-b6WvxwkyII2Lry2hjO6vcY', '', '', true);
	wp_enqueue_script('script', get_template_directory_uri() . '/js/common.js', '','', true);
  
	wp_enqueue_style('lightboxCSS', get_template_directory_uri() . '/css/lightbox.css');
	wp_enqueue_style('style', get_stylesheet_uri());
}
add_action( 'wp_enqueue_scripts', 'theme_name_scripts' );

або 

// style & scripts
add_action('wp_enqueue_scripts', 'load_style_scripts');
function load_style_scripts()
{
    $source_path = get_template_directory_uri() . '/design/css/';

    $styles = [
        'style',
        'font-awesome',
    ];

    foreach($styles as $file_name)
        wp_enqueue_style($file_name, $source_path . $file_name . '.css');
}

// style & scripts
add_action('wp_enqueue_scripts', 'my_scripts_method');
function my_scripts_method()
{
    $source_path = get_template_directory_uri() . '/design/js/';

    $scripts = [
        'jquery-1.11.2.min',
        'helium.parallax',
        'custom',
    ];

    foreach($scripts as $file_name)
        wp_enqueue_script($file_name, $source_path . $file_name . '.js');
}

********************************************************************

jQuery(function ($) {}

************************************************************************

//thumbnails in posts
add_theme_support('post-thumbnails'); 
add_theme_support( 'post-thumbnails', array( 'post' ) );
//logo через адмінку
add_theme_support( 'custom-header' ); //додає картинку та color
//додаємо можливіість WP самому генерувати <title></title> (тег вже не потрібно давати в хедер)
add_theme_support( 'title-tag' );
//віджети
add_theme_support( 'customize-selective-refresh-widgets' );

*********************************************************************************

//задаємо щоб різало картинки під такі розміри як треба
add_image_size( 'prod-prew', 180, 120, true );
//в шаблоні пишемо наступне
the_post_thumbnail( 'custom-size' );
//другий варіант
    if ( has_post_thumbnail() ) {
the_post_thumbnail(array(1024, 512, true), array('class' => 'img-responsive')); // add post thumbnail
    }

*********************************************************************************

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
pll_register_string( 'Заголовок', 'Текст', 'theme', false );
//вивід рядка в файлі
<?php pll_e('Текст'); ?>

*********************************************************************************

//вставляємо інші файли в function.php
require get_template_directory() . '/inc/ajax-posts.php';

*********************************************************************************

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
function projects_post_type (){
	$labels = array(
		'name'                => __( 'Проекти', 'mebus' ),
		'singular_name'       => __( 'Проект', 'mebus' ),
		'search_items'        => __( 'Пошук', 'mebus' ),
		'all_items'           => __( 'Всі проекти', 'mebus' ),
		'edit_item'           => __( 'Редагувати проект', 'mebus' ),
		'update_item'         => __( 'Оновити проект', 'mebus' ),
		'add_new_item'        => __( 'Додати проект', 'mebus' ),
		'new_item_name'       => __( 'Заголовок', 'mebus' ),
		'menu_name'           => __( 'Проекти', 'mebus' )
		);
	$args = array(
		'labels'                => $labels,
		'public'                => true,
		'publicly_queryable'    => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_icon'             => _('dashicons-hammer'),
		'query_var'             => true,
		'capability_type'       => 'post',
		'has_archive'           => false,
		'hierarchical'          => false,
		'supports'              => array('title', 'author', 'editor', 'page-attributes'),
		'rewrite'               => array('slug'=>'proekty')
		);
	register_post_type( 'projects_cpt', $args );
}
add_action( 'init', 'projects_post_type', 0 );

*********************************************************************

//видалення slug з url в custom post type (коли використовуємо плагіни Custom Post Type UI, Toolset Types)
Налаштування постійних посилань повинно бути обовязково "Назва запису"
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

//створюємо нову таксономію (детальніше https://wp-kama.ru/function/register_taxonomy)
register_taxonomy()
//створюємо кастомний тип поста (детальніше https://wp-kama.ru/function/register_post_type)
register_post_type()



//функція яка вставляє поширення в соцмережі http://sharelinkgenerator.com/ https://simplesharebuttons.com/html-share-buttons/
function share_this(){
    $content .= '<div class="share_post">';
        
    $title = get_the_title();
    $permalink = get_permalink();

    $facebook = 'https://www.facebook.com/sharer/sharer.php?u=' . $permalink;
    $vk = 'http://vk.com/share.php?url=' .$permalink . '';
    $google = 'https://plus.google.com/share?url=' . $permalink;
    $twitter = 'https://twitter.com/intent/tweet?text=Great post: ' . $title . '&amp;url=' . $permalink . '';
      
    $content .= '<a href="' . $facebook . '" target="_blank" rel="nofollow" class="share_img"><img src="../../wp-content/themes/bedrock-theme/assets/icons/face_white.png" class="social-icons white_icon"><img src="../../wp-content/themes/bedrock-theme/assets/icons/face_red.png" class="social-icons red_icon"></a>';
    $content .= '<a href="' . $vk . '" target="_blank" rel="nofollow" class="share_img"><img src="../../wp-content/themes/bedrock-theme/assets/icons/vk_white.png" class="social-icons white_icon"><img src="../../wp-content/themes/bedrock-theme/assets/icons/vk_red.png" class="social-icons red_icon"></a>';
    $content .= '</div>';
    
    return $content;  
}


//Хлібні крихте Yoast seo
https://sltaylor.co.uk/blog/adding-custom-post-type-landing-pages-yoast-breadcrumbs/
додати в Function.php ось це, що показувало нормальну ієрархію хлібних крихт
add_filter( 'wpseo_breadcrumb_links', 'my_wpseo_breadcrumb_links' );
function my_wpseo_breadcrumb_links( $links ) {
    if ( is_single() ) {
        $cpt_object = get_post_type_object( get_post_type() );
        if ( ! $cpt_object->_builtin ) {
            $landing_page = get_page_by_path( $cpt_object->rewrite['slug'] );
            array_splice( $links, -1, 0, array(
                array(
                    'id'    => $landing_page->ID
                )
            ));
        }
    }
    return $links;
}

****************************************************************************************************
//реєструємо сайд бар
register_sidebar([
    'id'          => 'header_main_text',
    'name'        => 'Шапка',
    'description' => 'Текст в шапке на главной',
    'before_widget' => '',
    'after_widget'  => '',
]);
//виводимо в адмінці (записуємо в шаблоні)
<?php dynamic_sidebar('header_contacts'); ?>

*********************************************************************************************

// Фикс блока навигации темы, когда активирован admin-bar
add_theme_support( 'admin-bar', array( 'callback' => 'customAdminBarStyles' ) );
function customAdminBarStyles()
{
 ?>
<style type="text/css" media="screen">
    html { margin-top: 32px !important; }
    * html body { margin-top: 32px !important; }
    header.fixed .nav { top: 62px; }
    header.fixed .infoBar { top: 32px; }
    @media screen and ( max-width: 782px ) {
        html { margin-top: 46px !important; }
        * html body { margin-top: 46px !important; }
        header.fixed .nav { top: 76px; }
        header.fixed .infoBar { top: 46px; }
    }
</style>
<?php
}

************************************************************************************************************
	
//instal and show excerpt length
function excerpt_count_js(){
	if ('page' != get_post_type()) {
		echo '<script>jQuery(document).ready(function(){
			jQuery("#postexcerpt .handlediv").after("<div style=\"position:absolute;top:12px;right:34px;color:#666;\"><small>Довжина уривка: </small><span id=\"excerpt_counter\"></span><span style=\"font-weight:bold; padding-left:7px;\">/ 175</span><small><span style=\"font-weight:bold; padding-left:7px;\">character(s).</span></small></div>");
			jQuery("span#excerpt_counter").text(jQuery("#excerpt").val().length);
			jQuery("#excerpt").keyup( function() {
				if(jQuery(this).val().length > 175){
					jQuery(this).val(jQuery(this).val().substr(0, 175));
				}
				jQuery("span#excerpt_counter").text(jQuery("#excerpt").val().length);
			});
		});</script>';
	}
}
add_action( 'admin_head-post.php', 'excerpt_count_js');
add_action( 'admin_head-post-new.php', 'excerpt_count_js');

********************************************************************************************************
//pagination
// <?php  the_posts_pagination( $args );?>

//remove h2 tag from pagination
function sanitize_pagination($content) {
// Remove role attribute
$content = str_replace('role="navigation"', '', $content);

// Remove h2 tag
$content = preg_replace('#<h2.*?>(.*?)<\/h2>#si', '', $content);

return $content;
}
add_action('navigation_markup_template', 'sanitize_pagination');
	
*****************************************************************************************************
	
//hide Wysiwyg Editor on page ID 2024
add_action( 'admin_init', 'hide_editor' );
function hide_editor() {
  // Get the Post ID.
  $post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'] ;
  if( !isset( $post_id ) ) return;
  // Hide the editor on the page titled 'Homepage'
  $homepgname = $post_id;
  if($homepgname == 2024){ 
    remove_post_type_support('page', 'editor');
  }
  // Hide the editor on a page with a specific page template
  // Get the name of the Page Template file.
  $template_file = get_post_meta($post_id, '_wp_page_template', true);
  if($template_file == 'my-page-template.php'){ // the filename of the page template
    remove_post_type_support('page', 'editor');
  }
}

*********************************************************************************************************************
	
//add widget to pages Site Editing Notes
function custom_meta_box_markup(){
    echo '<a href="' . get_site_url() . '/wp-content/uploads/Site-Editing-Notes.pdf" target="_blank">Open notes</a>';
}
function add_custom_meta_box(){
    add_meta_box("demo-meta-box", "Site Editing Notes", "custom_meta_box_markup", "page", "side", "default", null);
}
add_action("add_meta_boxes", "add_custom_meta_box");

//add widget to dashboard Site Editing Notes
add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');
function my_custom_dashboard_widgets() {
global $wp_meta_boxes;
wp_add_dashboard_widget('custom_help_widget', 'Site Editing Notes', 'custom_dashboard_help');
}
function custom_dashboard_help() {
echo '<p><a href="' . get_site_url() . '/wp-content/uploads/Site-Editing-Notes.pdf" target="_blank">Open notes</a></p>';
}
	
*********************************************************************************************************************

//remove WYSIWYG on pages
add_action('init', 'init_remove_support',100);
function init_remove_support(){
    $post_type = 'your post type';
    remove_post_type_support( $post_type, 'editor');
}
		
*********************************************************************************************************************

//Поміняти лого на сторінці входу в адмінку wp-login.php
https://wp-kama.ru/id_7675/frontend-15-hukov-dlya-functions-php.html#izmenenie-kartinki-na-stranitse-vhoda---wp-login.php
add_action( 'login_head', 'wp_login_logo_img_url' );
function wp_login_logo_img_url() {
	if( function_exists('get_custom_header') ){
		$width = get_custom_header()->width;
		$height = get_custom_header()->height;
	}
	else {
		$width = HEADER_IMAGE_WIDTH;
		$height = HEADER_IMAGE_HEIGHT;
	}

	echo '
	<style>
	.login h1 a {
		background-image: url('. get_header_image() .') !important;
		background-size: '. $width .'px '. $height .'px !important;
		width: '. $width .'px !important;
		height: '. $height .'px !important;
	}
	</style>';
}

***************************************************
	
//thumbnail ріжемо картинки
add_image_size( 'blog-prev', 350, 320, true );

******************************************************************************************************
	
//query only in title
//пошук лише в заголовках
	function ni_search_by_title_only( $search, $wp_query )
{
global $wpdb;
if ( empty( $search ) )
    return $search; // skip processing - no search term in query
$q = $wp_query->query_vars;
$n = ! empty( $q['exact'] ) ? '' : '%';
$search =
$searchand = '';
foreach ( (array) $q['search_terms'] as $term ) {
    $term = esc_sql( like_escape( $term ) );
    $search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
    $searchand = ' AND ';
}
if ( ! empty( $search ) ) {
    $search = " AND ({$search}) ";
    if ( ! is_user_logged_in() )
        $search .= " AND ($wpdb->posts.post_password = '') ";
}
return $search;
}
add_filter( 'posts_search', 'ni_search_by_title_only', 500, 2 );

******************************************************************************************************
	
//додаємо title атрибут до картинок загружені через редактор
// set the title attribute on images inserted via the editor
function featured_image_titles($attr, $attachment = null){
	$attr['title'] = get_post($attachment->ID)->post_title;
	$attr['alt'] = get_post($attachment->ID)->post_title;
	return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'featured_image_titles', 10, 2);

//remove srcset attribute from images
add_filter( 'wp_calculate_image_srcset_meta', '__return_null' );
	
******************************************************************************************************

// міняєм кількість потів на сторінці archive-custom.php
function set_posts_per_page_for_towns_cpt( $query ) {
 if ( !is_admin() && $query->is_main_query() && is_post_type_archive( 'custom' ) ) {
   $query->set( 'posts_per_page', '10' );
 }
}
add_action( 'pre_get_posts', 'set_posts_per_page_for_towns_cpt' );
	
