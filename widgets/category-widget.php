<?php
/* Widget for the Recipe Press Plugin */

/* Class Declaration */
class RecipePressCategoryWidget extends WP_Widget
{
	var $items;
	
	/**
	 * Constructor
	 */
	function RecipePressCategoryWidget()
	{
		$widget_ops = array('description' => __('List recipe categories on your sidebar. By GrandSlambert.') );
		$this->WP_Widget('recipe_press_category_widget', __('Recipe Press &raquo; Categories'), $widget_ops);
		
		$this->pluginPath = WP_CONTENT_DIR . '/plugins/' . plugin_basename(dirname(__FILE__));
		
		if (!$this->target = get_option('recipe_press_widget_target') )
			$this->target = '_blank';

		if (!$this->items = get_option('recipe_press_widget_items') )
			$this->items = 10;
	}
	
	/**
	 * Widget code
	 */
	function widget($args, $instance) 
	{
		global $RECIPEPRESSOBJ;
		
		if ( isset($instance['error']) && $instance['error'] )
			return;

		extract($args, EXTR_SKIP);
		
		$options = array();
		$options['limit'] = $instance['items'];
                //$options['status'] = 'active';
		
		$title 		= $instance['title'];
		$submitlink	= $instance['submit_link'];
		
		if ($category = $instance['category'])
			$options['category'] = $category;

                $categories = $RECIPEPRESSOBJ->getRecords('categories', $options);

		if ($target = $instance['target'])
			$options['target'] = $target;
			

		$page = get_page($RECIPEPRESSOBJ->submitPage);

		print $before_widget;
		if ( $title )
			print $before_title . $title . $after_title;

		print '<ul class="recipe-widget-list">';
                $object = new rp_Categories;
		print $object->listCategories($categories, $options);
		
		if ($submitlink)
			print '<li class="recipe-submit"><a href="' . get_page_link($page->ID) . '">' . $RECIPEPRESSOBJ->submitTitle . '</a></li>';
			
		print '</ul>';
		print $after_widget;
	}

	/** @see WP_Widget::form */
	function form($instance) 
	{
		global $RECIPEPRESSOBJ;
		
		$title 		= esc_attr($instance['title']);
		$items		= esc_attr($instance['items']);
		
		if (!$linktarget = esc_attr($instance['target']) )
			$linktarget = $this->widgetTarget;

		if ( $items < 1 || 20 < $items ) $items  = $this->items;

		include( $this->pluginPath . '/category-form.php');
	}

	/**
	 * Show the version number
	 */
	function showVersion()
	{
		return $this->version;
	}
}

add_action('widgets_init', create_function('', 'return register_widget("RecipepressCategoryWidget");'));