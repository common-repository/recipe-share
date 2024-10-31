<div class="wrap">
	<form method="post" action="options.php">
		<div class="icon32" id="icon-edit-pages"><br/>
		</div>
		<h2>Recipe Share &raquo; Settings </h2>
		<?php 
		if (function_exists('wpmu_create_blog'))
			wp_nonce_field('login_configurator-options');
		else
			wp_nonce_field('update-options');
		?>
		<div style="width:49%; float:left">
			<div class="postbox">
				<h3 class="handl" style="margin:0;padding:3px;cursor:default;">
					<?php _e('Display Settings'); ?>
				</h3>
				<div class="table">
					<table class="form-table">
						<tr align="top">
							<th scope="row"><?php _e('Recipe List URL'); ?></th>
							<td>
								<select name="recipe_share_display_page" id="recipe_share_display_page">
									<?php print $this->get_pages($this->displayPage); ?>
								</select>
								<br />
								<?php _e('<strong>Note</strong>: Remember to add the [recipe-list] short code to this page. This requirement may be removed in future versions.'); ?></td>
						</tr>
						<tr align="top">
							<th scope="row"><?php _e('User Submit Page'); ?></th>
							<td>
								<select name="recipe_share_submit_page" id="recipe_share_submit_page">
									<?php print $this->get_pages($this->submitPage); ?>
								</select>
								<br />
								<?php _e('<strong>Note</strong>: Remember to add the [recipe-form] short code to this page. This requirement may be removed in future versions.'); ?></td>
						</tr>
						<tr align="top">
							<th scope="row"><?php _e('User Submit Link Title'); ?></th>
							<td><input type="text" name="recipe_share_submit_title" id="recipe_share_submit_title" value="<?php print $this->submitTitle; ?>" /></td>
						</tr>
						<tr align="top">
							<th scope="row"><?php _e('Use Plugin RSS?'); ?></th>
							<td><input name="recipe_share_custom_css" type="checkbox" value="1" <?php if ($this->customCSS) print "checked"; ?> />
								<br />
								<?php _e('<strong>Note</strong>: Click this option to include the CSS from the plugin.'); ?>							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div  style="width:49%; float:right">
			<div class="postbox">
				<h3 class="handl" style="margin:0;padding:3px;cursor:default;">
					<?php _e('Management Settings'); ?>
				</h3>
				<div class="table">
					<table class="form-table">
						<tr align="top">
							<th scope="row"><?php _e('Default Category'); ?></th>
							<td><select name="recipe_share_default_category" id="recipe_share_default_category">
									<?php print $this->listOptions('categories', $this->defaultCategory); ?>
								</select>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="postbox">
				<h3 class="handl" style="margin:0;padding:3px;cursor:default;">
					<?php _e('Comment Settings'); ?>
				</h3>
				<div class="table">
					<table class="form-table">
						<tr align="top">
							<th scope="row">&nbsp;</th>
							<td>Coming Soon...</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="postbox">
				<h3 class="handl" style="margin:0;padding:3px;cursor:default;">
					<?php _e('Widget Settings'); ?>
				</h3>
				<table class="form-table">
					<tr align="top">
						<th scope="row"><?php _e('Default Link Target'); ?></th>
						<td><select name="recipe_share_widget_target" id="recipe_share_widget_target">
								<option>None</option>
								<option value="_blank" <?php if ($this->widgetTarget == '_blank') echo 'selected'; ?>>New Window</option>
								<option value="_top" <?php if ($this->widgetTarget == '_top') echo 'selected'; ?>>Top Window</option>
							</select></td>
					</tr>
					<tr align="top">
						<th scope="row"><?php _e('Default Items to Display'); ?></th>
						<td><select name="recipe_share_widget_items" id="recipe_share_widget_items">
								<?php
								for ( $i = 1; $i <= 20; ++$i )
									echo "<option value='$i' " . ( $this->widgetItems == $i ? "selected='selected'" : '' ) . ">$i</option>";
								?>
							</select>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div style="clear: both;">
			<p class"submit" align="center">
				<input type="hidden" name="action" value="update" />
				<?php if (function_exists('wpmu_create_blog')) : ?>
				<input type="hidden" name="option_page" value="recipe_share_options" />
				<?php  else : ?>
				<input type="hidden" name="page_options" value="recipe_share_submit_page,recipe_share_submit_title,recipe_share_default_category,recipe_share_display_page,recipe_share_custom_css,recipe_share_widget_target,recipe_share_widget_items" />
				<?php endif; ?>
				<input type="submit" name="Submit" value="<?php _e('Save Settings'); ?>" />
			</p>
		</div>
	</form>
</div>
<?php include "footer.php"; ?>
