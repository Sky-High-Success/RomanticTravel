<?php
/**
 * Template name: Cruise Blog
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage classiads
 * @since classiads 1.2.2
 */

?>

<?php include_once "template-travel-header.php";?>
	<div class="site-inner">
		<div class="wrap">
			<div class="content-sidebar-wrap">
				<main class="content" itemtype="http://schema.org/Blog" itemscope="itemscope" itemprop="mainContentOfPage" role="main">
					<?php 
						$posts = query_posts( array('cat' => 3, 'posts_per_page' => get_option('posts_per_page')) );
						while ( have_posts() ) : the_post();
					?>
						<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
							<header class="entry-header">
							<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
							<?php the_title( '<h2 class="entry-title">', '</h2>' ); ?>
							</a>
							</header>
							<div class="entry-content">
								<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
									<?php the_post_thumbnail('medium'); ?>
								</a>
								<p>
								<?php $content = get_the_content(); $content1 = strip_shortcodes( $content ); $content11 = strip_tags($content1); echo substr($content11, 0, 395).' <a href="'.get_permalink().'"> Read More </a>';?>
								</p>
							</div>
							<footer class="entry-footer">
								<p class="entry-meta">
									<span class="entry-categories">Filed Under: 
										<?php 
											$cate = get_the_category(); 
											foreach($cate as $cates)
											{
												$link = get_term_link( $cates->term_id, 'category' );
												echo '<a href="'.$link.'" rel="category tag">'.$cates->cat_name.'</a>';
											}
										?>
									</span> 
									<!--<span class="entry-tags">Tagged With: </span>-->
								</p>
							</footer>
						</article>
					<?php 
						endwhile;
					wp_reset_query();
					?>
                </main>
			</div>
		</div>
	</div>
<?php include_once "template-travel-footer.php";?>