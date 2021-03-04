<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package wcn
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

remove_filter ('the_content', 'wpautop');

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * @since 1.0.0
 */
function wcn_block_assets() {
	// Styles.
	wp_enqueue_style( 'wcn-style-css', plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ) );

	wp_enqueue_style( 'font-awesome', plugins_url( 'lib/css/fontawesome.min.css', dirname( __FILE__ ) ) );
	wp_enqueue_style( 'bootstrap-style', plugins_url( 'lib/css/bootstrap.min.css', dirname( __FILE__ ) ) );

	// Scripts.
	wp_enqueue_script( 'bootstrap-script', plugins_url( 'lib/js/bootstrap.min.js', dirname( __FILE__ ) ), array('jquery') );

	// <!-- Required dependencies -->
	// wp_enqueue_script('prop-types-script', '//cdnjs.cloudflare.com/ajax/libs/prop-types/15.6.2/prop-types.min.js', array());
	// wp_enqueue_script('react-script', '//cdnjs.cloudflare.com/ajax/libs/react/16.7.0/umd/react.production.min.js', array());
	// wp_enqueue_script('react-dom-script', '//cdnjs.cloudflare.com/ajax/libs/react-dom/16.7.0/umd/react-dom.production.min.js', array());
	// wp_enqueue_script('reactstrap-script', '//cdnjs.cloudflare.com/ajax/libs/reactstrap/6.5.0/reactstrap.full.min.js', array());
    // <!-- Optional dependencies -->
	// wp_enqueue_script('react-transition-group-script', '//cdnjs.cloudflare.com/ajax/libs/react-transition-group/2.2.1/react-transition-group.min.js', array());
	// wp_enqueue_script('popper-script', '//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', array());
	// wp_enqueue_script('react-popper-script', '//cdnjs.cloudflare.com/ajax/libs/react-popper/0.10.4/umd/react-popper.min.js', array());
    // <!-- Lastly, include your app's bundle -->
	// <script type="text/javascript" src="/assets/bundle.js"></script>
} 
// Hook: Frontend assets.
add_action( 'enqueue_block_assets', 'wcn_block_assets' );

/**
 * Enqueue Gutenberg block assets for backend editor.
 *
 * `wp-blocks`: includes block type registration and related functions.
 * `wp-element`: includes the WordPress Element abstraction for describing the structure of your blocks.
 * `wp-i18n`: To internationalize the block's text.
 *
 * @since 1.0.0
 */
function wcn_editor_assets() {
	// Scripts.
	wp_enqueue_script(
		'wcn-block-js', // Handle.
		plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ), // Dependencies, defined above.
		// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
		true // Enqueue the script in the footer.
	);

	// Styles.
	wp_enqueue_style(
		'wcn-block-editor-css', // Handle.
		plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ), // Block editor CSS.
		array( 'wp-edit-blocks' ) // Dependency to include the CSS after it.
		// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: filemtime — Gets file modification time.
	);
}
// Hook: Editor assets.
add_action( 'enqueue_block_editor_assets', 'wcn_editor_assets' );


// block.php
function wcn_block_two_render_block_query_builder( $attributes, $content ) {
	global $post;

	ob_start();
	
	// echo '<pre>';
	// var_dump($attributes);
	// echo '</pre>';
	// echo '<pre>';
	// var_dump($content);
	// echo '</pre>';

	// set attributes
	$att = [];
	$att['postTypesArray'] = isset($attributes["selectedPostTypes"]) ? $attributes['selectedPostTypes'] : [];
	$att['postTypesList'] = isset($attributes["selectedPostTypes"]) ? $attributes['selectedPostTypes'] : '';
	$att['categoriesList'] = isset($attributes["selectedCategories"]) ? implode(', ', $attributes['selectedCategories']) : '';
	$att['tagsList'] = isset($attributes["selectedTags"]) ? implode(', ', $attributes['selectedTags']) : '';

	$att['orderBy'] = isset($attributes['orderBy']) ? $attributes['orderBy'] : 'post_date';
	$att['order'] = isset($attributes['order']) ? $attributes['order'] : 'desc';
	$att['numToShow'] = isset($attributes['numToShow']) ? $attributes['numToShow'] : 12;
	$att['excludeIDs'] = isset($attributes['excludeIDs']) ? $attributes['excludeIDs'] : '';
	$att['excludeIDs'] = explode(',', $att['excludeIDs']);
	//var_dump($att['excludeIDs']);

	$att['hideTitle'] = isset($attributes['hideTitle']) ? $attributes['hideTitle'] : false;
	$att['hideDate'] = isset($attributes['hideDate']) ? $attributes['hideDate'] : false;
	$att['hideFeaturedMedia'] = isset($attributes['hideFeaturedMedia']) ? $attributes['hideFeaturedMedia'] : false;
	$att['hideExcerpt'] = isset($attributes['hideExcerpt']) ? $attributes['hideExcerpt'] : false;
	$att['excerptWordCount'] = isset($attributes['excerptWordCount']) ? $attributes['excerptWordCount'] : 30;
	$att['hideCTA'] = isset($attributes['hideCTA']) ? $attributes['hideCTA'] : false;
	$att['ctaContent'] = isset($attributes['ctaContent']) ? $attributes['ctaContent'] : 'Call-To-Action';
	$att['columnsToShow'] = isset($attributes['columnsToShow']) ? $attributes['columnsToShow'] : 4;

	// default and custom CSS class settings
	$att['customClassName'] = isset($attributes['className']) ? $attributes['className'] : '';
	$att['className'] = trim('wp-block-wcn-query-builder row ' . $att['customClassName']);

	$args = array(
		'post_type' => $att['postTypesArray'],
		'post__not_in' => array(get_the_ID()),
		'post__not_in' => $att['excludeIDs'],
		'orderby' => $att['orderBy'],
		'order' => $att['order'],
		'posts_per_page' => $att['numToShow'],
	);
	if ($att['categoriesList'] !== '' && $att['postTypesList'] === 'post') { // in_array("post", $att['postTypesArray'])
		$args['cat'] = $att['categoriesList'];
	}
	if ($att['tagsList'] !== '' && $att['postTypesList'] === 'post') { // in_array("post", $att['postTypesArray'])
		$args['tag'] = $att['tagsList'];
	}
	//var_dump($att['postTypesList']);
	//var_dump($args);

	// Custom query.
	$query = new WP_Query( $args );
	//var_dump($query);

	// Check that we have query results.
	if ( $query->have_posts() ) {
	 
		echo '<div class="' . $att['className'] . '">';

			// Start looping over the query results.
			while ( $query->have_posts() ) {
		
				$query->the_post();
				//var_dump(get_the_ID());

				$att['featuredMediaArray'] = !$att['hideFeaturedMedia'] ? wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()),"full") : [];
				//var_dump($att['featuredMediaArray']);
				$att['featuredMedia'] = $att['featuredMediaArray'][0];
		
				// DO ACTION HOOK AND RUN DEFAULT
				// Contents of the queried post results go here..
				add_action('output_one_result', 'wcn_block_two_render_block_query_builder_default_action', 10, 2);
				do_action('output_one_result', $att, $post);

			}
	 
		echo '</div>';

	}
	 
	// Restore original post data.
	wp_reset_postdata();
	
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}
// register block save (return null) callback for php handling
register_block_type( 'wcn/query-builder', array(
    'render_callback' => 'wcn_block_two_render_block_query_builder',
) );

// default action
function wcn_block_two_render_block_query_builder_default_action($att, $post) {
	//var_dump($att);
	//var_dump($post);
	setup_postdata($post);
	//
	?>
		<div class="col">
			<div class="card">
				<?php /* var_dump($post); */ ?>
				<?php if ( !$att['hideFeaturedMedia'] && $att['featuredMedia'] != false ) : ?>
					<a href="<?php the_permalink() ?>" title="<?php the_title() ?>">
						<img width="100%" 
							src="<?php echo $att['featuredMedia'] ?>" 
							alt="Card Image Top for <?php the_title() ?>" 
							class="card-img-top" 
						/>
					</a>
				<?php endif; ?>
				<div class="card-body">
					<?php if ( !$att['hideTitle'] ) : ?>
						<h4 class="card-title">
							<a href="<?php the_permalink() ?>" title="<?php the_title() ?>">
								<?php the_title() ?>
							</a>
						</h4>
					<?php endif; ?>
					<?php if ( !$att['hideDate'] ) : ?>
						<h5 class="card-subtitle">
							<?php echo date('F d, Y', strtotime(get_the_date())); ?>
						</h5>
					<?php endif; ?>
					<?php if ( !$att['hideExcerpt'] ) : ?>
						<p class="card-text">
							<?php echo wp_trim_words( get_the_excerpt(), $att['excerptWordCount'], '...' ); ?>
						</p>
					<?php endif; ?>
					<?php if ( !$att['hideCTA'] ) : ?>
						<a href="<?php the_permalink() ?>" title="<?php the_title() ?>">
							<button class="btn btn-secondary">
								<?php echo $att['ctaContent'] ?>
							</button>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php
	//
	wp_reset_postdata();
}




class all_terms
{
    public function __construct()
    {
        $version = '2';
        $namespace = 'wp/v' . $version;
        $base = 'all-terms';
        register_rest_route($namespace, '/' . $base, array(
            'methods' => 'GET',
            'callback' => array($this, 'get_all_terms'),
        ));
    }

    public function get_all_terms($object)
    {
        $return = array();
        // $return['categories'] = get_terms('category');
		// $return['tags'] = get_terms('post_tag');
        // Get taxonomies
        $args = array(
            'public' => true,
            '_builtin' => false
        );
        $output = 'names'; // or objects
        $operator = 'and'; // 'and' or 'or'
        $taxonomies = get_taxonomies($args, $output, $operator);
        foreach ($taxonomies as $key => $taxonomy_name) {
            if ($taxonomy_name = $_GET['taxonomy']) {
            	$return = get_terms($taxonomy_name);
        	}
        }
        return new WP_REST_Response($return, 200);
    }
}
add_action('rest_api_init', function () {
    $all_terms = new all_terms;
});