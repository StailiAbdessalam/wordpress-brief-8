<?php

if ( ! class_exists( 'Bolby_Walker_Comment' ) ) {

	class Bolby_Walker_Comment extends Walker_Comment {

		protected function html5_comment( $comment, $depth, $args ) {

			$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';

			?>
			<<?php echo esc_attr($tag); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static output ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( $this->has_children ? 'parent' : '', $comment ); ?>>
				<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
					<footer class="comment-meta">
						<div class="comment-author vcard">
							<?php
							$comment_author_url = get_comment_author_url( $comment );
							$comment_author     = get_comment_author( $comment );
							$avatar             = get_avatar( $comment, $args['avatar_size'] );
							if ( 0 !== $args['avatar_size'] ) {
								if ( empty( $comment_author_url ) ) {
									echo wp_kses_post( $avatar );
								} else {
									printf( '<a href="%s" rel="external nofollow" class="url">', $comment_author_url ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Escaped in https://developer.wordpress.org/reference/functions/get_comment_author_url/
									echo wp_kses_post( $avatar );
								}
							}

							printf(
								'<span class="fn">%1$s</span><span class="screen-reader-text says">%2$s</span>',
								esc_html( $comment_author ),
								esc_attr__( 'says:', 'bolby' )
							);

							if ( ! empty( $comment_author_url ) ) {
								echo '</a>';
							}
							?>
						</div><!-- .comment-author -->

						<div class="comment-metadata">
							<a href="<?php echo esc_url( get_comment_link( $comment, $args ) ); ?>">
								<?php
								/* Translators: 1 = comment date, 2 = comment time */
								$comment_timestamp = sprintf( esc_attr__( '%1$s at %2$s', 'bolby' ), get_comment_date( '', $comment ), get_comment_time() );
								?>
								<time datetime="<?php comment_time( 'c' ); ?>" title="<?php echo esc_attr( $comment_timestamp ); ?>">
									<?php echo esc_html( $comment_timestamp ); ?>
								</time>
							</a>
							<?php
							if ( get_edit_comment_link() ) {
								echo ' <span aria-hidden="true">&bull;</span> <a class="comment-edit-link" href="' . esc_url( get_edit_comment_link() ) . '">' . esc_attr__( 'Edit', 'bolby' ) . '</a>';
							}
							?>
						</div><!-- .comment-metadata -->

					</footer><!-- .comment-meta -->

					<div class="comment-content entry-content">

						<?php

						comment_text();

						if ( '0' === $comment->comment_approved ) {
							?>
							<p class="comment-awaiting-moderation"><?php esc_attr_e( 'Your comment is awaiting moderation.', 'bolby' ); ?></p>
							<?php
						}

						?>

					</div><!-- .comment-content -->

					<?php

					$comment_reply_link = get_comment_reply_link(
						array_merge(
							$args,
							array(
								'add_below' => 'div-comment',
								'depth'     => $depth,
								'max_depth' => $args['max_depth'],
								'before'    => '<span class="comment-reply">',
								'after'     => '</span>',
							)
						)
                    );

					$by_post_author = bolby_is_comment_by_post_author( $comment );

					if ( $comment_reply_link || $by_post_author ) {
						?>

						<footer class="comment-footer-meta">

							<?php
							if ( $comment_reply_link ) {
								comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); 
							}
							if ( $by_post_author ) {
								echo '<span class="by-post-author">' . esc_attr__( 'By Post Author', 'bolby' ) . '</span>';
							}
							?>

						</footer>

						<?php
					}
					?>

				</article><!-- .comment-body -->

			<?php
		}
	}
}

function bolby_is_comment_by_post_author( $comment = null ) {

    if ( is_object( $comment ) && $comment->user_id > 0 ) {

        $user = get_userdata( $comment->user_id );
        $post = get_post( $comment->comment_post_ID );

        if ( ! empty( $user ) && ! empty( $post ) ) {

            return $comment->user_id === $post->post_author;

        }
    }
    return false;

}

if ( post_password_required() ) {
	return;
}

if ( $comments ) {
	?>

	<div class="comments" id="comments">

		<?php
		$comments_number = absint( get_comments_number() );
		?>

		<div class="comments-header section-inner small max-percentage">

			<h2 class="comment-reply-title">
			<?php
			if ( ! have_comments() ) {
				esc_attr_e( 'Leave a comment', 'bolby' );
			} elseif ( '1' === $comments_number ) {
				/* translators: %s: post title */
				printf( _x( 'One reply on &ldquo;%s&rdquo;', 'comments title', 'bolby' ), esc_html( get_the_title() ) );
			} else {
				echo sprintf(
					/* translators: 1: number of comments, 2: post title */
					_nx(
						'%1$s reply on &ldquo;%2$s&rdquo;',
						'%1$s replies on &ldquo;%2$s&rdquo;',
						$comments_number,
						'comments title',
						'bolby'
					),
					number_format_i18n( $comments_number ),
					esc_html( get_the_title() )
				);
			}

			?>
			</h2><!-- .comments-title -->

		</div><!-- .comments-header -->

		<div class="comments-inner section-inner thin max-percentage">

			<?php
			wp_list_comments(
				array(
					'walker'      => new Bolby_Walker_Comment(),
					'avatar_size' => 120,
					'style'       => 'div',
				)
			);

			$comment_pagination = paginate_comments_links(
				array(
					'echo'      => false,
					'end_size'  => 0,
					'mid_size'  => 0,
					'next_text' => esc_attr__( 'Newer Comments', 'bolby' ) . ' <span aria-hidden="true">&rarr;</span>',
					'prev_text' => '<span aria-hidden="true">&larr;</span> ' . esc_attr__( 'Older Comments', 'bolby' ),
				)
			);

			if ( $comment_pagination ) {
				$pagination_classes = '';

				// If we're only showing the "Next" link, add a class indicating so.
				if ( false === strpos( $comment_pagination, 'prev page-numbers' ) ) {
					$pagination_classes = ' only-next';
				}
				?>

				<nav class="comments-pagination pagination<?php echo esc_html($pagination_classes); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static output ?>" aria-label="<?php esc_attr_e( 'Comments', 'bolby' ); ?>">
					<?php echo wp_kses_post( $comment_pagination ); ?>
				</nav>

				<?php
			}
			?>

		</div><!-- .comments-inner -->

	</div><!-- comments -->

	<?php
}

if ( comments_open() || pings_open() ) {

	comment_form(
		array(
			'class_form'         => 'section-inner thin max-percentage',
			'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
			'title_reply_after'  => '</h2>',
		)
	);

} elseif ( is_single() ) {

	?>

	<div class="comment-respond" id="respond">

		<p class="comments-closed"><?php esc_attr_e( 'Comments are closed.', 'bolby' ); ?></p>

	</div><!-- #respond -->

	<?php
}
