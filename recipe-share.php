<?php
/*
Plugin Name: Recipe Share
Plugin URI: http://recipepress.net
Description: This plugin is no longer suppored. Please look for RecipePress.
Version: 0.6.3
Author: grandslambert
Author URI: http://grandslambert.com/
*/

/* Class Declaration */
class recipeShare
{
	var $version		= '0.6.3';

	// Options page name
	var $optionsName	= 'recipe_share_options';
	var $menuName		= 'recipe-share-overview';
	
	// Settings
	var $submitPage;
	var $submitTitle;
	var $customCSS	= true;
	var $defaultCategory = 1;
	var $displayPage;
	var $widgetTarget;
	var $widgetItems = 10;
	
	// Private Variables
	var $tables	= array();
	var $servingOptions = array('cups','pints','gallons','dozen','servings','pieces');
	var $hourText	= 'Hour';
	var $minText	= 'Minute';
	var $displayTemplate	= 'recipe-display.php';
	
	/**
	 * Class Constructor
	 *
	 * Gets the extension from the options table if exists or uses the .html default
	 */
	function recipeShare()
	{
		
		$this->pluginPath = WP_CONTENT_DIR . '/plugins/' . plugin_basename(dirname(__FILE__));
		$this->pluginDir = '/wp-content/plugins/' . plugin_basename(dirname(__FILE__));
		
		// Set the table names
		global $wpdb;
		$this->tables['recipes'] = $wpdb->prefix . 'recipes';
		$this->tables['categories'] = $wpdb->prefix . 'recipes_cats';

		// Add Options Pages and Links
		add_action('admin_menu', array(&$this, 'addAdminPages'));
		add_filter('plugin_action_links', array(&$this, 'addConfigureLink'), 10, 2);
		add_filter('whitelist_options', array(&$this, 'whitelistOptions'));
		
		// Get the settings
		$this->submitPage = get_option('recipe_share_submit_page');
		$this->customCSS = get_option('recipe_share_custom_css');
		$this->displayPage = get_option('recipe_share_display_page');

		if (!$this->submitTitle = get_option('recipe_share_submit_title') )
			$this->submitTitle = 'Submit A Recipe';

		if (!$this->defaultCategory = get_option('recipe_share_default_category') )
			$this->defaultCategory = 1;
		
		if (!$this->widgetTarget = get_option('recipe_share_widget_target') )
			$this->widgetTarget = '';
		
		if (!$this->widgetItems = get_option('recipe_share_widget_items') )
			$this->widgetItems = 10;
		
		// Add short extended_page_list_widgets
		add_shortcode('recipe-list', array($this, 'listShortcode') );
		add_shortcode('recipe-show', array($this, 'showRecipeShortcode') );
		add_shortcode('recipe-form', array($this, 'formShortcode') );

		// Add filters and hooks.
		add_action('wp_head', array($this, 'addHeader') );
		//add_action('init', array($this, 'recipe_share_addbuttons') );
		add_action('admin_head', array($this, 'adminHeader') );

		// Rewrite Rules
		add_filter('rewrite_rules_array', array($this, 'insertRules') );
		add_filter('query_vars', array($this, 'queryVars') );
		add_filter('init', array($this, 'flushRules') );
	}
	
	function getRewriteRules()
	{
		global $wp_rewrite; // Global WP_Rewrite class object
		return $wp_rewrite->rewrite_rules(); 
	}
	
	/**
	 * Rewrite Rules
	 */
	function flushRules(){
		global $wp_rewrite;
			$wp_rewrite->flush_rules();
	}
	
	// Adding a new rule
	function insertRules($rules)
	{
		global $wp_rewrite;
		
		$page = get_page($this->displayPage);

		$newrules = array();
		$newrules['(' . $page->post_name . ')/(.*)$'] = 'index.php?pagename=$matches[1]&recipe=$matches[2]';
		return $newrules + $rules;
	}
	
	// Adding the id var so that WP recognizes it
	function queryVars($vars)
	{
		 array_push($vars, 'recipe');
		 return $vars;
	}
	
	/**
	 * Header actions
	 */
	function addHeader()
	{
		if ($this->customCSS)
		{
			if (file_exists( get_theme_root() . '/' . get_template() . '/recipe-share.css' ) )
				$file = get_bloginfo('template_url') . '/recipe-share.css';
			else
				$file = $this->pluginDir . '/templates/recipe-share.css';
								
			print '<link rel="stylesheet" media="screen" type="text/css" href="' . $file .'" />' . "\n";
		}
		
	}
	
	/**
	 * Admin Header Stuff
	 */
	function adminHeader()
	{
		print '<script type="text/javascript" src="' . $this->pluginDir . '/js/scripts.js"></script>' . "\n";

	}
	
	/**
	 * Add all options to the whitelist for the NONCE
	 * Required for Wordpress MU support
	 */
	function whitelistOptions($whitelist)
	{
		if (is_array($whitelist))
		{
			$option_array = array('recipe_share' => array(
				'recipe_share_default_category',
				'recipe_share_display_page',
				'recipe_share_submit_page',
				'recipe_share_submit_title',
				'recipe_share_custom_css',
				'recipe_share_widget_target',
				'recipe_share_widget_items',
			));
			$whitelist = array_merge($whitelist, $option_array);
		}

		return $whitelist;
	}

	/**
	 * Outputs the overview sub panel
	 */
	function overviewSubPanel()
	{
		include($this->pluginPath . '/manager/overview.php');
	}

	/**
	 * Add Form
	 */
	function addRecipeSubpanel()
	{
		$form_action = 'add';
		include($this->pluginPath . '/manager/edit_recipe.php');
	}

	/**
	 * Outputs the recipes sub panel
	 */
	function recipesSubpanel()
	{
		global $wpdb;
		
		switch ($_REQUEST['action'])
		{
			case 'delete':
				$recipe = $this->getRecipe($_GET['id'], false);
				$this->message = $recipe->title . ' recipe successfully deleted.';
				$wpdb->query('delete from `' . $this->tables['recipes'] . '` where `id` = ' . $_GET['id'] . ' limit 1');
				include ($this->pluginPath . '/manager/recipes.php');
				break;
			case 'update':
				$results = $wpdb->update( $this->tables['recipes'], $this->recipeInput(), array('id'=>$_POST['ID']) );
				
				if ($results)
					$this->message = '"' . $_POST['title'] . '" recipe successfully updated.';
				else
					$this->message = 'There was an error trying to save the recipe. Perhaps you forgot to make any changes. Try again later, OK?';
					
				include ($this->pluginPath . '/manager/recipes.php');
				break;
			case 'edit':
				$recipe = $wpdb->get_row('select * from ' . $this->tables['recipes'] . ' where `id` = ' . $_GET['id']);
				$form_action = 'update';
				include($this->pluginPath . '/manager/edit_recipe.php');
				break;
			case 'add':
				global $current_user;
				$slug = $this->slugify($_POST['slug'], $_POST['title']);
				$user = get_currentuserinfo();
				
				$data = $this->recipeInput();
				$data['added'] = date('Y-m-d H:i:s');
				$results = $wpdb->insert( $this->tables['recipes'], $data);
				
				if ($results)
					$this->message = '"' . $_POST['title'] . '" recipe successfully added.';
				else
					$this->message = 'There was an error trying to save the recipe. Perhaps you forgot to make any changes. Try again later, OK?';
					
				include ($this->pluginPath . '/manager/recipes.php');
				break;
			default:
				include($this->pluginPath . '/manager/recipes.php');
				break;
		}
	}
	
	/**
	 * Get Recipe Input
	 */
	function recipeInput ()
	{
		return array(
			'title'			=> trim($_POST['title']),
			 'slug'			=> $this->slugify($_POST['slug'], $_POST['title']), 
			 'user_id'		=> $_POST['user_id'],
			 'category'		=> $_POST['category'],
			 'notes'			=> $_POST['notes'],
			 'prep_time'	=> $_POST['prep_time'],
			 'cook_time'	=> $_POST['cook_time'],
			 'ready_time'	=> $this->readyTime(),
			 'ingredients'	=> $_POST['ingredients'],
			 'instructions'=> $_POST['instructions'],
			 'servings'		=> $_POST['servings'],
			 'servings_size'=> $_POST['servings_size'],
			 'status'		=> $_POST['status'],
		);
	}
	
	/**
	 * Get the list of users on the site
	 */
	function userList($selected = NULL, $default = false)
	{
		global $wpdb, $current_user;

		if ( !$selected and $default)
		{
			get_currentuserinfo();
			$selected = $current_user->ID;
		}
			
		$authors = $wpdb->get_results("SELECT ID, display_name from $wpdb->users ORDER BY display_name ASC");
		
		foreach ($authors as $author)
		{
			$list.= '<option value="' . $author->ID . '"';
			if ($author->ID == $selected) $list.= ' selected="selected"';
			$list.= '>' . $author->display_name . '</option>';
		}
		
		return $list;
	}
	
	/**
	 * Calculate ready time
	 */
	function readyTime($prep = NULL, $cook = NULL)
	{
		if ( !isset($prep) ) $prep = $_POST['prep_time'];
		if ( !isset($cook) ) $cook = $_POST['cook_time'];

		$total = $prep + $cook;
		
		if ($total > 60)
		{
			$hours = floor($total / 60);

			if ($hours > 1)
				$hplural = 's';
			else
				$mplural = '';
			
			$hours =  $hours . ' ' . $this->hourText . $hplural . ', ';
		}

		$mins = $total -( $hours * 60);

		if ($mins > 1)
			$mplural = 's';
		else
			$mplural = '';

		return $hours . $mins . ' ' . $this->minText . $mplural;
	}

	/**
	 * Outputs the setings sub panel
	 */
	function settingsSubpanel()
	{
		include($this->pluginPath . '/manager/settings.php');
	}

	/**
	 * Outputs the categories sub panel
	 */
	function categoriesSubpanel()
	{
		global $wpdb;
		
		switch ($_REQUEST['action'])
		{
			case 'delete':
				$moved = $wpdb->query('update ' . $this->tables['recipes'] . ' set `category` = ' . $this->defaultCategory . ' where `category` = ' . $_GET['cat_ID']);
				$deleted = $wpdb->query('delete from `' . $this->tables['categories'] . '` where `id` = ' . $_GET['cat_ID'] . ' limit 1');
				$this->message = 'Category deleted. ' . $moved . ' recipes were moved to the default category.';
				include ($this->pluginPath . '/manager/categories.php');
				break;
			case 'editedcat':
				$slug = $this->slugify($_POST['slug'], $_POST['name']);
				$results = $wpdb->update( $this->tables['categories'], array( 'name'=>$_POST['name'], 'slug'=>$slug, 'description'=>$_POST['description']), array('id'=> $_POST['cat_ID']) );
				
				if ($results)
					$this->message = 'Category updated.';
				else
					$this->message = 'There was an error trying to save the category. Perhaps you forgot to make any changes. Try again later, OK?';
					
				include ($this->pluginPath . '/manager/categories.php');
				break;
			case 'addcat':
				$slug = $this->slugify($_POST['slug'], $_POST['name']);
				$results = $wpdb->insert( $this->tables['categories'], array( 'name'=>$_POST['name'], 'slug'=>$slug, 'description'=>$_POST['description'], 'created'=>date('Y-m-d H:i:s') ) );

				if ($results)
					$this->message = 'Category created.';
				else
					$this->message = 'There was an error trying to save the category. Perhaps you forgot to make any changes. Try again later, OK?';
					
				include ($this->pluginPath . '/manager/categories.php');
				break;
			case 'edit':
				$category = $wpdb->get_row('select * from ' . $this->tables['categories'] . ' where `id` = ' . $_GET['cat_ID']);
				include($this->pluginPath . '/manager/edit_category.php');
				break;
			default:
				include($this->pluginPath . '/manager/categories.php');
		}
	}
	
	/**
	 * Comments Subpanel
	 */
	function commentsSubpanel()
	{
			include($this->pluginPath . '/manager/comments.php');
	}
	
	/**
	 * Create a slug and format it correctly
	 */
	function slugify($slug, $alternate)
	{
		if (!$slug)
			$slug = trim($alternate);
		
		return str_replace( ' ', '-', preg_replace( '/[^a-z0-9- ]/', '', strtolower( $slug ) ) );
	}
	
	/**
	 * Create list options for table
	 */
	function listOptions($table, $selected = NULL, $key = 'id', $value = 'name')
	{
		global $wpdb;
		
		$options = $wpdb->get_results('select * from `' . $this->tables[$table]);
		
		foreach ($options as $option)
		{
			$output.= '<option value="' . $option->$key . '"';
			if ($option->$key == $selected) $output.= ' selected="selected"';
			$output.= '>' . $option->$value . '</option>';
		}
		
		return $output;
	}
	
	function statusOptions($selected = NULL)
	{
		$options = array('draft'=>'Draft', 'pending'=>'Pending Review', 'active'=>'Active', 'hidden'=>'Hidden');
		
		foreach ($options as $key=>$value)
		{
			$output.= '<option value="' . $key . '"';
			if ($key == $selected) $output.= ' selected="selected"';
			$output.= '>' . $value . '</option>';
		}
		
		return $output;
	}

	/**
	 * Adds Disclaimer options tab to admin menu
	 */
	function addAdminPages()
	{
		global $wp_version;
		
		add_menu_page('Recipe Share &raquo; Overview', 'Recipe Share', 8, $this->menuName, array(&$this, 'overviewSubPanel'), $this->pluginDir . '/icons/menu_icon.png' ); 
		add_submenu_page($this->menuName, 'Recipe Share &raquo; Recipes', 'Recipes', 8, 'recipe_share_list', array(&$this, 'recipesSubpanel')); 
		add_submenu_page($this->menuName, 'Recipe Share &raquo; Add Recipe', 'Add Recipe', 8, 'recipe_share_add', array(&$this, 'addRecipeSubpanel')); 
		add_submenu_page($this->menuName, 'Recipe Share &raquo; Comments', 'Comments', 8, 'recipe_share_comments', array(&$this, 'commentsSubpanel')); 
		add_submenu_page($this->menuName, 'RecipeRecipe Share &raquo; Categories', 'Categories', 8, 'recipe_share_categories', array(&$this, 'categoriesSubpanel')); 
		add_submenu_page($this->menuName, 'Recipe Share &raquo; Settings', 'Settings', 8, 'recipe_share_settings', array(&$this, 'settingsSubpanel')); 

		// Use the bundled jquery library if we are running WP 2.5 or above
		if (version_compare($wp_version, '2.5', '>=')) {
			wp_enqueue_script('jquery', false, false, '1.2.3');
		}
	}

	/**
	 * Adds a settings link next to Login Configurator on the plugins page
	 */
	function addConfigureLink($links, $file)
	{
		static $this_plugin;

		if (!$this_plugin) 
		{
			$this_plugin = plugin_basename(__FILE__);
		}

		if ($file == $this_plugin) 
		{
			$settings_link = '<a href="edit-pages.php?page=' . $this->menuName . '">' . __('Settings') . '</a>';
			array_unshift($links, $settings_link);
		}

		return $links;
	}

	
	/**
	 * Plugin activation function
	 *
	 * Registers the custom extension when the plugin is activated.
	 */
	function activate()
	{
		global $wpdb;
		
		/* Create the recipe table */
		$table_name = $wpdb->prefix . "recipes";
				
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
		{
			$sql = "
				CREATE TABLE IF NOT EXISTS `" . $table_name. "` (
				  `id` int(10) NOT NULL AUTO_INCREMENT,
				  `user_id` int(10) unsigned NOT NULL,
				  `category` int(10) unsigned NOT NULL DEFAULT '1',
				  `title` tinytext COLLATE utf8_unicode_ci NOT NULL,
				  `slug` text COLLATE utf8_unicode_ci NOT NULL,
				  `prep_time` int(10) unsigned NOT NULL,
				  `cook_time` int(10) unsigned NOT NULL,
				  `ready_time` tinytext COLLATE utf8_unicode_ci NOT NULL,
				  `notes` text COLLATE utf8_unicode_ci NOT NULL,
				  `ingredients` longtext COLLATE utf8_unicode_ci NOT NULL,
				  `instructions` longtext COLLATE utf8_unicode_ci NOT NULL,
				  `servings` int(11) NOT NULL,
				  `servings_size` tinytext COLLATE utf8_unicode_ci NOT NULL,
				  `views_total` int(10) unsigned NOT NULL,
				  `comment_total` int(10) NOT NULL,
				  `status` tinytext COLLATE utf8_unicode_ci NOT NULL,
				  `added` datetime NOT NULL,
				  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0 ;
			";

			dbDelta($sql);
		}
		else
		{
			$sql = 'ALTER TABLE `' . $table_name . '` CHANGE `status` `status` TINYTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ';
			$wpdb->query($sql);
		}

		/* Create the category table. */
		$table_name = $wpdb->prefix . "recipes_cats";
				
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
		{
			$sql = "
				CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
				  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `name` tinytext COLLATE utf8_unicode_ci NOT NULL,
				  `slug` tinytext COLLATE utf8_unicode_ci NOT NULL,
				  `description` text COLLATE utf8_unicode_ci NOT NULL,
				  `status` tinytext COLLATE utf8_unicode_ci NOT NULL,
				  `created` datetime NOT NULL,
				  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0 ;
			";

			dbDelta($sql);
			
			// Add default categories.
			$wpdb->insert($table_name, array('name'=>'Main Dishes', 'slug'=>'main-dishes', 'status'=>'active', 'created'=>date("Y-m-d H:i:s") ) );
			$wpdb->insert($table_name, array('name'=>'Side Dishes', 'slug'=>'side-dishes', 'status'=>'active', 'created'=>date("Y-m-d H:i:s") ) );
			$wpdb->insert($table_name, array('name'=>'Desserts', 'slug'=>'desserts', 'status'=>'active', 'created'=>date("Y-m-d H:i:s") ) );
		}
		else
		{
			$sql = 'ALTER TABLE `' . $table_name . '` CHANGE `status` `status` TINYTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ';
			$wpdb->query($sql);
		}
	}	
	
	/**
	 * Plugin deactivation function
	 *
	 * Removes the custom extension when the plugin is deactivated.
	 */
	function deactivate()
	{
	}
	
	/**
	 * Dipslay User
	 */
	function displayUser($id)
	{
		$user_info = get_userdata($id); 
		return $user_info->display_name;
	}
	
	/**
	 * Display the total number of recipes
	 */
	function showCount($table = 'recipes', $status = 'all', $category = 0)
	{
		global $wpdb;
		$query = "SELECT COUNT(*) FROM " . $this->tables[$table] . ' where id > 0 ';

		if ($status != 'all')
			$query.= ' and `status` = "' . $status . '"';
			
		if ($category != 0)
			$query.= ' and `category` = ' . $category;
			
		return $wpdb->get_var($query);
	}
	
	/**
	 * Get rows from database
	 */
	function getRows($table = 'recipes', $page = 0)
	{
		global $wpdb;
		$query = 'select * from ' . $this->tables[$table];
		$results = $wpdb->get_results($query);
		return $results;
	}
	
	/**
	 * Show the version number
	 */
	function showVersion()
	{
		return $this->version;
	}
	
	/**
	 * Get serving size options
	 */
	function servingSizeOptions($selected = NULL)
	{
		foreach ($this->servingOptions as $size)
		{
			$output.= '<option value=' . $size;
			if ($size == $selected) $output.= ' selected="selected"';
			$output.= '>' . ucfirst($size) . '</option>';
		}
		
		return $output;
	}
	
	/**
	 * Get Recipe
	 */
	function getRecipe($slug, $countview = false)
	{
		global $wpdb;
		
		if ( is_numeric($slug) )
			$where = $this->tables['recipes'] . '`.`id` = "' . $slug . '"';
		else
			$where = $this->tables['recipes'] . '`.`slug` = "' . $slug . '"';
			
		
		$query = '
			select 
				`' . $this->tables['recipes'] . '`.*,
				`' . $this->tables['categories'] . '`.`name` as `category_name`,
				`' . $this->tables['categories'] . '`.`slug` as `category_slug`
			from `' . $this->tables['recipes'] . '` 
			left join `' . $this->tables['categories'] . '` on `' . $this->tables['categories'] . '`.`id` = `' . $this->tables['recipes'] . '`.`category`
			where `' . $where;
			
		$recipe = $wpdb->get_row($query);
		
		if ($countview)
			$wpdb->update($this->tables['recipes'], array('views_total' => $recipe->views_total + 1), array('id'=>$recipe->id) );

		return $recipe;
	}

	/**
	 * Get Recipe
	 */
	function getRecipes($options = array())
	{
		global $wpdb;
		
		extract($options);
		
		if (!$orderby)
			$orderby = 'title';
			
		if (!$sortby)
			$sortby = 'asc';
			
		if ( isset($recipe) and $recipe != '' )
			$where.= ' and `' . $this->tables['recipes'] . '`.`title` like "%' . $recipe . '%"';

		if ( isset($slug) and $slug != '' )
			$where.= ' and `' . $this->tables['recipes'] . '`.`slug` like "%' . $slug . '%"';
			
		if ( isset($category) and $category != 'all')
			$where.= ' and `' . $this->tables['recipes'] . '`.`category` = ' . $category;

		if ( isset($user) and $user != 'all')
			$where.= ' and `' . $this->tables['recipes'] . '`.`user_id` = ' . $user;
			
		if ( isset($status) and $status != 'all')
			$where.= ' and `' . $this->tables['recipes'] . '`.`status` = "' . $status . '"';
			
		if ( isset($limit) )
			$limit = ' limit ' . $limit;
		
		$query = '
			select 
				`' . $this->tables['recipes'] . '`.*,
				`' . $this->tables['categories'] . '`.`name` as `category_name`,
				`' . $this->tables['categories'] . '`.`slug` as `category_slug`
			from `' . $this->tables['recipes'] . '` 
			left join `' . $this->tables['categories'] . '` on `' . $this->tables['categories'] . '`.`id` = `' . $this->tables['recipes'] . '`.`category`
			where `' . $this->tables['recipes'] . '`.`id` > 0 ' . $where . '
			order by `' . $orderby . '` ' . $sortby . $limit . '
			';
			
		return $wpdb->get_results($query);
	}
	
	/**
	 * Show Recipe Shortcode
	 */
	function showRecipeShortcode($atts)
	{
		global $wpdb;
		
		extract(shortcode_atts(array(
			// Query Attributes
			'recipe'	=> NULL,
		), $atts));
		
		$recipe = $this->getRecipe($recipe, true);
		
		if ( isset($recipe) and isset($recipe->title) )
		{
			if (file_exists( get_theme_root() . '/' . get_template() . '/' . $this->displayTemplate ) )
				$file = get_bloginfo('template_url') . '/' . $this->displayTemplate;
			else
				$file = 'templates/' . $this->displayTemplate;
		
			ob_start();
			require ($file);
			$output = ob_get_contents();
   		ob_end_clean();
			
			return $output;
		}
		else
		{
			return 'Sorry, we could not find the ' . $recipe . ' recipe in our records.';
		}
	}
	
	/**
	 * Shortcode function
	 */
	function listShortcode($atts)
	{
		global $wpdb, $wp_query;
		
		if ( $recipe = $wp_query->get('recipe') )
		{
			if ($recipe == 'submitted')
			{
				$data = $this->recipeInput();
				$data['added'] = date('Y-m-d H:i:s');
				$results = $wpdb->insert( $this->tables['recipes'], $data);
				
				if (file_exists( get_theme_root() . '/' . get_template() . '/recipe-thankyou.php' ) )
					$file = get_bloginfo('template_url') . '/recipe-thankyou.php';
				else
					return 'Thank you for your recipe submission';
			}
			elseif ($recipe == $this->submitLink)
			{
				global $current_user;
				get_currentuserinfo();
				extract($this->getSubmitForm());
			}
			else
			{
				$recipe = $this->getRecipe($recipe, true);
				
				if ( isset($recipe) and isset($recipe->title) )
				{
					if (file_exists( get_theme_root() . '/' . get_template() . '/' . $this->displayTemplate ) )
						$file = get_bloginfo('template_url') . '/' . $this->displayTemplate;
					else
						$file = 'templates/' . $this->displayTemplate;
				
					ob_start();
					require ($file);
					$output = ob_get_contents();
						ob_end_clean();
					return $output;
				}
				else
				{
					return 'Sorry, we could not find the ' . $recipe . ' recipe in our records.';
				}
			}
			
			ob_start();
			require($file);
			$output = ob_get_contents();
   		ob_end_clean();
			
			return $output;
		}
			
		
		extract(shortcode_atts(array(
			// Query Attributes
			'category'	=> NULL,
			'tag' 		=> NULL,
			'sort_column'	=> 'title',
			'sort_order'	=> 'asc',
			'limit'		=> NULL,
			
			// Display Attributes
			'title'		=> 'Recipes',
			'ul_class'	=> 'recipe-list',
			'li_class'	=> 'recipe-item',
			'group_cat'	=> false,
			'show_author'	=> false,
		), $atts));
		
		$query = '
			select 
				`' . $this->tables['recipes'] . '`.*,
				`' . $this->tables['categories'] . '`.`name` as `category_name`,
				`' . $this->tables['categories'] . '`.`slug` as `category_slug`
			from `' . $this->tables['recipes'] . '` 
			left join `' . $this->tables['categories'] . '` on `' . $this->tables['categories'] . '`.`id` = `' . $this->tables['recipes'] . '`.`category`
			where `' . $this->tables['recipes'] . '`.`status` = "active"';
		
		if ( isset($category) )
		{
			$query.= ' and `' . $this->tables['categories'] . '`.`slug` = "' . $category . '"';
		}
		
		if ($group_cat)
		{
			$orderPrefix =  ' `category_name` ' . $group_cat . ', ';
		}
		
		$query.= ' order by ' . $orderPrefix . '`' . $sort_column . '` ' . $sort_order;
		
		if ( isset($limit) )
			$query.= ' limit ' . $limit;
		
		$recipes = $wpdb->get_results($query);
		
		if (!$group_cat) 
		{
			$output.= '<h3 class="recipe-title">' . $title . '</h3>';
			$output.= '<ul class="' . $ul_class . '">';
		}
		
		$output.= $this->listRecipes($recipes, array('group_cat'=>$group_cat) );
		$output.= '</ul>';

		return $output;
	}

	/**
	 * Recipe Form Short Code
	 */
	function formShortcode($atts)
	{
		extract(shortcode_atts(array(
			// Query Attributes
			'category'	=> NULL,
		), $atts));

		extract($this->getSubmitForm());
		
		ob_start();
		require($file);
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	}
	
	/**
	 * Get Submit Form
	 */
	function getSubmitForm()
	{
		if (file_exists( get_theme_root() . '/' . get_template() . '/recipe-submit.php' ) )
			$file = get_bloginfo('template_url') . '/recipe-submit.php';
		else
			$file = 'templates/recipe-submit.php';
		
		$page = get_page($this->displayPage);
		$formaction = '/' . $page->post_name . '/submitted';
		
		return array('file'=>$file, 'formaction'=>$formaction);
	}
	
	/**
	 * List Recipes
	 */
	function listRecipes($recipes = NULL, $options = NULL)
	{
		if ( !is_array($recipes) )
			return;
		
		if (!get_option('permalink_structure') )
		{
			$linkpre = get_page_link() .'&amp;recipe=';
		}
		else
		{
			$page = get_page($this->displayPage);
			$linkpre = get_option('siteurl') . '/' . $page->post_name . '/';
		}
		
		if ($options['target'])
			$target = 'target="' . $options['target'] . '"';
		else
			unset ($target);
				
		foreach ($recipes as $recipe)
		{
			if ($options['group_cat'] and $lastCat != $recipe->category_name)
			{
				if ($started) $output.= '</ul>';
				$output.= '<h3 class="recipe-title">' . $recipe->category_name . '</h3>';
				$output.= '<ul class="' . $ul_class . '" id="recipes-' . $recipe->category_slug . '">';
				$started = true;
			}
			$output.= '<li class="' . $li_class . '"><a href="' . $linkpre . $recipe->slug . '" '. $target . '>' . $recipe->title . '</a></li>';
			$lastCat = $recipe->category_name;
		}
				
		return $output;
	}
	
	/**
	 * Show Ingredients
	 */
	function bulletize($text)
	{
		$lines = split("\n", $text);

		$output = '<ul>';
		
		foreach ($lines as $line)
		{
			if ($line) $output.= '<li>' . $line . '</li>';
		}
		
		$output.= '</ul>';
		
		return $output;
	}

	/**
	 * Get list of pages as select options
	 */
	function get_pages($selected = NULL)
	{
		if ( !is_array($selected) )
			$selected = array($selected);
		
		$pages = get_pages();
		
		$output = '';
		
		foreach ($pages as $page)
		{
			$output.= '<option value="' . $page->ID . '"';
			if ( in_array($page->ID, $selected) ) $output.= ' selected';
			$output.= '>' . $page->post_title . "</option>\n";
		}
		
		return $output;
	}
	
	/* Editor Button Code */
	function recipe_share_addbuttons() 
	{
	   // Don't bother doing this stuff if the current user lacks permissions
	   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
		return;
	 
	   // Add only in Rich Editor mode
	   if ( get_user_option('rich_editing') == 'true') 
	   {
			add_filter("mce_external_plugins", array($this, "add_recipe_share_tinymce_plugin") );
			add_filter('mce_buttons', array($this, 'register_recipe_share_button') );
	   }
	}
	 
	function register_recipe_share_button($buttons) 
	{
		array_push($buttons, "separator", "recipeshare");
		return $buttons;
	}
	 
	// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
	function add_recipe_share_tinymce_plugin($plugin_array) 
	{
		print "Adding plugin";
		$plugin_array['recipe_share'] = $this->pluginPath.'/js/editor_plugin.js';
		return $plugin_array;
	}


}

// Pre 2.6 Compatibility
if ( !defined('WP_CONTENT_DIR') )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );


$RECIPESHAREOBJ = new recipeShare;

register_activation_hook(__FILE__, array($RECIPESHAREOBJ, 'activate') );
register_deactivation_hook(__FILE__, array($RECIPESHAREOBJ, 'deactivate') );

// Include Widget
include_once('widgets/list-widget.php');
