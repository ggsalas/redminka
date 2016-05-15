<?php

/**
 * Adds a simple text widget to help promote Barley.
 */
class Barley_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'barley_widget', // Base ID
			__('Promote Barley for WordPress', 'barley_for_wordpress'), // Name
			array( 'description' => __( 'A simple text widget to help promote Barley for WordPress', 'barley_for_wordpress' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		echo __( 'Written using <a href="http://getbarley.com/" target="_blank" title="Barley, the inline editor for WordPress">Barley, the inline editor for WordPress</a>.', 'barley_for_wordpress' );
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Written with Barley', 'barley_for_wordpress' );
		}
		?>
		<p>
		Thanks for helping to promote Barley for WordPress!
		<!--
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /> -->
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

} // class Barley_Widget

// register Barley_Widget widget
function register_barley_widget() {
    register_widget( 'Barley_Widget' );
}
add_action( 'widgets_init', 'register_barley_widget' );


?>