<?php
/*
Plugin Name: Post series
Plugin URI: 
Description: Create a series of post or pages and show the navigation in a widget.
Version: 1.0
Author: Thomas Lhotta
Author URI: github.com/thomaslhotta
Author Email: th.lhotta@gmail.com
Text Domain: post-series
Domain Path: /lang/
Network: false
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Copyright 2013 Thomas Lhoota (th.lhotta@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


define( 'POST_SERIES_DIR', ABSPATH . 'wp-content/plugins/' . ( basename( dirname( __FILE__ ) ) ) );
define( 'POST_SERIES_FILE', POST_SERIES_DIR . '/' . ( basename( __FILE__ ) ) );

class Post_Series extends WP_Widget {

    protected $text_domain = 'post-series';
    
    protected $defaults = array(
    	'before_widget' => '',
        'after_widget' => '',
    );
    
    /*--------------------------------------------------*/
	/* Constructor
	/*--------------------------------------------------*/

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		// load plugin text domain
		add_action( 'init', array( $this, 'widget_textdomain' ) );
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		
		add_shortcode( 'post_series', array( $this, 'shortcode' ) );
		
		if ( is_admin() ) {
		    add_action( 'admin_menu' , array( $this, 'add_admin_box' ) );
		}
		
		
		// Hooks fired when the Widget is activated and deactivated
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		parent::__construct(
			'post-series',
			__( 'Post Series Navigation', $this->text_domain ),
			array(
				'classname'		=>	'post-series',
				'description'	=>	__( 'Shows the navigation for a series of posts.', $this->text_domain )
			)
		);


	} // end constructor

	/*--------------------------------------------------*/
	/* Widget API Functions
	/*--------------------------------------------------*/

	/**
	 * Outputs the content of the widget.
	 *
	 * @param	array	args		The array of form elements
	 * @param	array	instance	The current instance of the widget
	 */
	public function widget( $args = '', $instance = array() ) {
		$args = wp_parse_args( $args, $this->defaults );
		 
		extract( $args, EXTR_SKIP );

		$current_post_id = get_the_ID();
		
		$series = wp_get_post_terms( get_the_ID(), 'series', array( 'fields' => 'all' ) );
		
		// Don't show anything if the current post is not part of a series
		if ( count( $series ) <= 0 ) {
		    return;
		}
		
		// Init vars
		$series = reset( $series );
		$posts = array();
		$loop_prev_post = array();
		$prev_post = array();
		$next_post = array();

		// Find other posts of series
		$query_args = array(
		    'post_type' => get_post_type(),
		    'order'   => 'ASC', 
		    'orderby' => 'date',
		    'tax_query' => array(
		        array(
		            'taxonomy' => 'series',
		            'field' => 'id',
		            'terms' => $series->term_id,
		        ),
		    )
		);
		
		$query = new WP_Query( $query_args );
		
		// Find previous and next post, transform posts to array.
		while ( $query->have_posts() ) {
		    $query->the_post();
		    $post = array(
		        'ID'     => get_the_ID(),
		        'title'  => get_the_title(),
		        'active' => false,
		        'permalink' => get_permalink(),
		    );
		    
		    if ( get_the_ID() == $current_post_id ) {
		        $post['active'] = true;
		        $prev_post = $loop_prev_post;
		    }
		    
		    if ( !empty( $loop_prev_post ) && $loop_prev_post['ID'] == $current_post_id ) {
		        $next_post = $post;
		    }
		    
		    $posts[] = $post;
		    $loop_prev_post = $post;
		}
        wp_reset_postdata();
		
		echo $before_widget;

		include( POST_SERIES_DIR . '/views/widget.php' );

		echo $after_widget;

	} // end widget

	/**
	 * Short code version of widget.
	 * 
	 * @return string
	 */
	public function shortcode()
	{
		ob_start();
		$this->widget();
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	
	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param	array	new_instance	The previous instance of values before the update.
	 * @param	array	old_instance	The new instance of values to be generated via the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		

		return $instance;

	} // end widget

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param	array	instance	The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		$instance = wp_parse_args(
			(array) $instance
		);

		
		// Display the admin form
		include( POST_SERIES_DIR . '/views/admin.php' );

	} // end form

	/*--------------------------------------------------*/
	/* Public Functions
	/*--------------------------------------------------*/

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {

		//load_plugin_textdomain( $this->text_domain , false, POST_SERIES_DIR . '/lang/' );
	    load_plugin_textdomain( $this->text_domain , false, basename( dirname( __FILE__ ) ) . '/lang/' );

	} // end widget_textdomain

	/**
	 * Fired when the plugin is activated.
	 *
	 * @param		boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public function activate( $network_wide ) {
		// TODO define activation functionality here
	} // end activate

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 */
	public function deactivate( $network_wide ) {
		// TODO define deactivation functionality here
	} // end deactivate

	public function register_taxonomy()
	{
	    // Add new taxonomy, NOT hierarchical (like tags)
	    $labels = array(
	        'name'                       => __( 'Series', $this->text_domain ),
	        'singular_name'              => __( 'Series', $this->text_domain, $this->text_domain ),
	        'search_items'               => __( 'Search Series', $this->text_domain ),
	        'popular_items'              => __( 'Popular Series', $this->text_domain ),
	        'all_items'                  => __( 'All Series', $this->text_domain ),
	        'parent_item'                => null,
	        'parent_item_colon'          => null,
	        'edit_item'                  => __( 'Edit Series', $this->text_domain ),
	        'update_item'                => __( 'Update Series', $this->text_domain ),
	        'add_new_item'               => __( 'Add New Series', $this->text_domain ),
	        'new_item_name'              => __( 'New Series Name', $this->text_domain ),
	        'separate_items_with_commas' => __( 'Separate Series with commas', $this->text_domain ),
	        'add_or_remove_items'        => __( 'Add or remove Series', $this->text_domain ),
	        'choose_from_most_used'      => __( 'Choose from the most used Series', $this->text_domain ),
	        'not_found'                  => __( 'No Series found.', $this->text_domain ),
	        'menu_name'                  => __( 'Series', $this->text_domain ),
	    );
	    
	    $args = array(
	        'hierarchical'          => false,
	       'labels'                => $labels,
	        'show_ui'               => true,
	        'show_admin_column'     => true,
	        'update_count_callback' => '_update_post_term_count',
	        'query_var'             => true,
	        'rewrite'               => array( 'slug' => 'series' ),
	    );
	    
	    register_taxonomy( 'series', array('page', 'post'), $args );
	}
	
	public function add_admin_box() 
	{
	    remove_meta_box( 'tagsdiv-series', 'page',' core' );
	    remove_meta_box( 'tagsdiv-series', 'post',' core' );
	    add_meta_box( 'series_box_ID', __( 'Series', 'post-series' ), array( $this, 'style_admin_box' ), 'page', 'side', 'core' );
	    add_meta_box( 'series_box_ID', __( 'Series', 'post-series' ), array( $this, 'style_admin_box' ), 'post', 'side', 'core' );
	    
	}
	
	public function style_admin_box() 
	{
	    $series = get_terms( 'series', 'hide_empty=0' );
	    $active = wp_get_object_terms( get_the_ID(), 'series' );
	    $active = reset( $active );
	    
	    $none = true;
	    
	    foreach ( $series as $single ) {
	        $single->selected = false;
	        if ( !empty( $active ) and $single->term_id = $active->term_id ) {
	            $single->selected = true;
	            $found = false;
	        }
	    }
	    
	    include( POST_SERIES_DIR . '/views/admin-box.php' );
	}
	
	
} // end class

add_action( 'widgets_init', create_function( '', 'register_widget("Post_Series");' ) );
