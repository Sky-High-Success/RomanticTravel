<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains comments and the comment form.
 *
 * @package WordPress
 * @subpackage classiads
 * @since classiads 1.2.2
 */

/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if ( post_password_required() )
	return;

global $redux_demo;
?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
				printf( _nx( 'One review of %2$s', '%1$s reviews of %2$s', get_comments_number(), 'comments title', 'agrg' ),
					number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
			?>
		</h2>
		<div class="h3-seprator-sidebar"></div>

		<ol class="comment-list">
			<?php
				wp_list_comments( array(
					'style'       => 'ol',
					'short_ping'  => true,
					'type' => 'comment',
					'avatar_size' => 74,
					'reply_text' => 'this is',
					'per_page' => 2,
					'page' => 2
					
					
				) );
			?>
		</ol><!-- .comment-list -->

		<?php
			// Are there comments to navigate through?
			if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
		?>
		<nav class="navigation comment-navigation" role="navigation">
			<h1 class="screen-reader-text section-heading"><?php _e( 'Comment navigation', 'agrg' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'agrg' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'agrg' ) ); ?></div>
		</nav><!-- .comment-navigation -->
		<?php endif; // Check for comment navigation ?>

		<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="no-comments"><?php _e( 'Comments are closed.' , 'agrg' ); ?></p>
		<?php endif; ?>

	<?php endif; // have_comments() ?>

	<?php 
	
	global $wp;
	global $comment_rating_post_id;
	error_log($comment_rating_post_id);
	$current_url = home_url(add_query_arg(array(),$wp->request));
	
	$login_url = $redux_demo['login']."?direct=".urlencode($current_url);
	
	$comments_args = array('title_reply'=>'Write a Review',
			               'label_submit'=>'Post Review',
			               //'id_submit'=>'comment_submit',
			               'must_log_in' => '<div class="must-log-in">' .
			                                sprintf(
					                           __( 'You must be <a href="%s">logged in</a> to write a review.' ),
					                        $login_url   
			                                ) . '</div>',
			               'logged_in_as' => '<div class="logged-in-as">' .
			                                 sprintf(
					                           __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ),
					                           home_url( 'profile' ),
					                           $user_identity,
					                           wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) )
			                                ) . '</div>',
			               'comment_field' =>  '<div class="comment-form-ratings"><label class="comment-label" for="comment-ratings">' . _x( 'Ratings', 'noun' ) .
			                                   '</label>'.the_ratings('div', $comment_rating_post_id, false, true, false, false).'</div>'.
			                                   '<div class="comment-form-comment"><label class="comment-label" for="comment">' . _x( 'Review', 'noun' ) .
			                                   '</label><textarea id="comment" name="comment" cols="45" rows="8" placeholder="Is this a fantastic experience?" required="" minlength="50" aria-invalid="true" aria-required="true">' .
			                                   '</textarea></div>',
			               'comment_notes_after' => '<div class="form-allowed-tags">' .
			                                         sprintf(
					                                 __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s' ),
					                                 ' <code>' . allowed_tags() . '</code>'
			                                         ) . '</div>'
	);
	comment_form($comments_args); ?>

</div><!-- #comments -->