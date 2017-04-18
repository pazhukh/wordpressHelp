https://wp-kama.ru/function/get_search_form

Створюємо файл з назвою searchform.php  і запихаємо в нього

<form role="search" method="get" id="searchform" action="<?php echo home_url( '/' ) ?>" >
	<label class="screen-reader-text" for="s">Пошук: </label>
	<input type="text" value="<?php echo get_search_query() ?>" name="s" id="s" placeholder="Пошук"/>
	<input type="submit" id="searchsubmit" value="найти" />
</form>

знайдені записи показує в файлі search.php
get_header(); ?>

		<?php
		if ( have_posts() ) : ?>
			<header class="page-header">
				<h1 class="page-title"><?php printf( esc_html__( 'Search Results for: %s', 'novar' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
			</header>
			<?php
			/* Start the Loop */
			while ( have_posts() ) : the_post();
      
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

		<?php if ( 'post' === get_post_type() ) : ?>
		<div class="entry-meta">
			<?php novar_posted_on(); ?>
		</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->

	<footer class="entry-footer">
		<?php novar_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->

			endwhile;

			the_posts_navigation();

		else : /*якщо постів не знайдено*/
<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'novar' ); ?></p>
			

		endif; ?>


get_footer();


