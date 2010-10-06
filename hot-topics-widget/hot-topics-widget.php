<?php
/*
Plugin Name: Hot Topics Widget
Plugin URI: http://github.com/BryanH/Hot-Topics-Widget
Description: Displays a clickable Hot Topics list
Version: 1.1
Author: Bryan Hanks, PMP
Author URI: http://www.chron.com/apps/adbxh0/wordpress/
License: GPL (see http://www.gnu.org/copyleft/gpl.html)

Instructions: copy this file into wp-content/plugins and activate on each blog

*/

class Widget_Hot_Topics extends WP_Widget {

	function Widget_Hot_Topics() {
		/* Widget settings. */
		$widget_ops = array (
			'classname' => 'hot_topics',
			'description' => __('Displays a clickable Hot Topics list', 'hot_topics')
		);
		$control_ops = array (
			'width' => 150,
			'height' => 150,
			'id_base' => 'hot_topics'
		);
		$this->WP_Widget('hot_topics', __('Hot Topics', 'hot_topics'), $widget_ops, $control_ops);
	}
	/**
	 * Public View
	 */
	function widget($args, $instance) {
		extract($args);
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title']);
		$topics = $instance['topics'];
		/* REQUIRED */
		echo $before_widget;
		/* 'before' and 'after' are REQUIRED */
		if ($title) {
			echo $before_title . $title . $after_title . '&nbsp;';
		}
		/* Display array separated by delimiter
		 * TODO: make delimiter a configurable option?
		 * */
		if ($topics && isset ($topics)) {
			foreach ($topics as & $topic) {
				$category_id = get_cat_ID($topic);
				if (0 < $category_id) {
					// modified the category link for the niche sites "site category" functionality
					// @TODO: this should change in the future as we fix the niche site theme
					$category = get_category($category_id);
					$category_link = get_bloginfo('url') . '/category/' . $category->slug ;
					$topic = '<a href="' . $category_link . '" title="' . $topic . '">' . $topic . '</a>';
				} else {
					//	topic is not a link
				}
			}
			echo implode(' | ', $topics);
		}
		/* REQUIRED */
		echo $after_widget;
	}
	/**
	 * Save/update settings
	 */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags(trim($new_instance['title']));
		$topics = explode("\n", strip_tags(trim($new_instance['topics'])));
		$instance['topics'] = $topics;
		return $instance;
	}
	/**
	 * Widget options form
	 */
	function form($instance) {
		$defaults = array (
			'title' => __('Hot Topics', 'hot_topics'),
			'topics' => array (
				"General",
				"Featured",
				"News"
			)
		);
		$instance = wp_parse_args((array) $instance, $defaults);
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Caption:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'topics' ); ?>"><?php _e('Topics (one per line):', 'hot_topics'); ?></label><br />
			 <textarea id="<?php echo $this->get_field_id( 'topics' ); ?>" name="<?php echo $this->get_field_name( 'topics' ); ?>" rows="6" cols="15" style="width:100%"><?php echo implode("\n", $instance['topics'] ); ?></textarea>
		</p>
		<?php
	}
	/**
	 * Register widget
	 */
	function register() {
		register_widget('Widget_Hot_Topics');
	}
}
add_action('widgets_init', array ('Widget_Hot_Topics', 'register'));

?>
