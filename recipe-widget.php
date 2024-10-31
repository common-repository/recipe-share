<?php
/* Widget for the Recipe Share Plugin */

/* Class Declaration */
class RecipeShareWidget extends WP_Widget
{
	var $target		= '_blank';
	var $items		= 10;
	
	/**
	 * Constructor
	 */
	function RecipeShareWidget()
	{
		$widget_ops = array('description' => __('List recipes on your sidebar. By GrandSlambert.') );
		$this->WP_Widget('recipe_share_widget', __('Recipe Share Widget'), $widget_ops);
		
		$this->pluginPath = WP_CONTENT_DIR . '/plugins/' . plugin_basename(dirname(__FILE__));
		
		if (!$this->target = get_option('recipe_share_widget_target') )
			$this->target = '_blank';

		if (!$this->items = get_option('recipe_share_widget_items') )
			$this->items = 10;
	}
	
	/**
	 * Widget code
	 */
	function widget($args, $instance) 
	{
		global $RECIPESHAREOBJ;
		
		if ( isset($instance['error']) && $instance['error'] )
			return;

		extract($args, EXTR_SKIP);
		
		$options = array();
		
		$title = $instance['title'];
		
		if ($category = $instance['category'])
			$options['category'] = $category;

		$recipes = $RECIPESHAREOBJ->getRecipes($options);
		
		print $before_widget;
		if ( $title )
			print $before_title . $title . $after_title;

		print '<ul class="recipe-widget-list">';
		print $RECIPESHAREOBJ->listRecipes($recipes);
		print '</ul>';
		print $after_widget;
	}

	/** @see WP_Widget::form */
	function form($instance) 
	{
		global $RECIPESHAREOBJ;
		
		$title 	= esc_attr($instance['title']);
		$items	= esc_attr($instance['items']);
		$category = esc_attr($instance['category']);
		
		if (!$linktarget = esc_attr($instance['target']) )
			$linktarget = $this->target;

		if ( $items < 1 || 20 < $items ) $items  = $this->items;

		include( $this->pluginPath . '/widget-form.php');
	}

	/**
	 * Show the version number
	 */
	function showVersion()
	{
		return $this->version;
	}
}

add_action('widgets_init', create_function('', 'return register_widget("RecipeShareWidget");'));