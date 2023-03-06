<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since Twenty Nineteen 1.0
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<?php wp_head(); ?>
	<style>
		html {
			font-size: 16px;
		}
	</style>
</head>

<body <?php body_class(); ?>>

	<div id="page" class="site">

		<header id="masthead" class="site-header">
			<div class="site-branding-container">
				<div class="site-branding navbar navbar-expand navbar-dark container">

					<?php if ( has_custom_logo() ) : ?>
						<div class="site-logo"><?php the_custom_logo(); ?></div>
					<?php endif; ?>
					<?php $blog_info = get_bloginfo( 'name' ); ?>
					<?php if ( ! empty( $blog_info ) ) : ?>
						<?php if ( is_front_page() && is_home() ) : ?>
							<h1 class="site-title"><a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
						<?php else : ?>
							<p class="site-title"><a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
						<?php endif; ?>
					<?php endif; ?>

					<?php
					$description = get_bloginfo( 'description', 'display' );
					if ( $description || is_customize_preview() ) :
						?>
							<p class="site-description align-self-end">
								<?php echo $description; ?>
							</p>
					<?php endif; ?>
					<?php if ( has_nav_menu( 'menu-1' ) ) : ?>
						<nav id="site-navigation" class="main-navigation collapse navbar-collapse" aria-label="<?php esc_attr_e( 'Top Menu', 'twentynineteen' ); ?>">
							<?php
							wp_nav_menu(
								array(
									'theme_location' => 'menu-1',
									'menu_class'     => 'main-menu navbar-nav',
									'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
								)
							);
							?>
						</nav><!-- #site-navigation -->
					<?php endif; ?>
				</div><!-- .site-branding -->
			</div><!-- .site-branding-container -->
		</header>

		<div id="content" class="site-content">
			<div id="primary" class="content-area">
				<main id="aic-main" class="aic-site-main">

					<?php
					require_once plugin_dir_path(  __FILE__  ) . 'chat-window.php';
					?>

				</main><!-- #main -->
			</div><!-- #primary -->
		</div><!-- #content -->
	</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
