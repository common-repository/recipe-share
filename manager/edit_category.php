<div class="wrap">
<div class="icon32" id="icon-edit-pages"><br/>
</div>
<h2>Recipe Share &raquo; Edit Category</h2>
<form class="validate" action="?page=recipe_share_categories" method="post" id="editcat" name="editcat">
	<input type="hidden" value="editedcat" name="action"/>
	<input type="hidden" value="<?php print $category->id; ?>" name="cat_ID"/>
	<?php 
		if (function_exists('wpmu_create_blog'))
			wp_nonce_field('wp_recipe-options');
		else
			wp_nonce_field('update-options');
	?>
	<table class="form-table">
		<tbody>
			<tr class="form-field form-required">
				<th valign="top" scope="row"><label for="name">Recipe Category name</label></th>
				<td><input type="text" aria-required="true" size="40" value="<?php print $category->name; ?>" id="name" name="name"/></td>
			</tr>
			<tr class="form-field">
				<th valign="top" scope="row"><label for="slug">Recipe Category slug</label></th>
				<td><input type="text" size="40" value="<?php print $category->slug; ?>" id="slug" name="slug"/>
					<br/>
					The "slug" is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</td>
			</tr>
			<tr class="form-field">
				<th valign="top" scope="row"><label for="description">Description (optional)</label></th>
				<td><textarea style="width: 97%;" cols="50" rows="5" id="description" name="description"><?php print $category->description; ?></textarea></td>
			</tr>
		</tbody>
	</table>
	<p class="submit">
	    <?php if (function_exists('wpmu_create_blog')) : ?>
	    <input type="hidden" name="option_page" value="recipe_share_categories" />
	    <?php  else : ?>
	    <input type="hidden" name="page_options" value="name,slug,description" />
	    <?php endif; ?>
		<input type="submit" value="Update Category" name="submit" class="button-primary"/>
	</p>
</form>
<?php include('footer.php'); ?>
