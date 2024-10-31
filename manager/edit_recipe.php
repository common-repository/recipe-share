<div class="wrap">
<div class="icon32" id="icon-edit-pages"><br/>
</div>
<h2>Recipe Share &raquo; <?php print ucfirst($form_action); ?> Recipe</h2>
<form class="validate" action="?page=recipe_share_list" method="post" id="update" name="update">
	<input type="hidden" value="<?php print $form_action; ?>" name="action"/>
	<input type="hidden" value="<?php print $recipe->id; ?>" name="ID"/>
	<?php 
		if (function_exists('wpmu_create_blog'))
			wp_nonce_field('wp_recipe-options');
		else
			wp_nonce_field('update-options');
	?>
	<table class="form-table">
		<tbody>
			<tr class="form-field form-required">
				<th valign="top" scope="row">Contributor</th>
				<td colspan="3"><select name="user_id" id="user_id">
					<?php print $this->userList($recipe->user_id, true); ?>
				</select></td>
			</tr>
			<tr class="form-field form-required">
				<th valign="top" scope="row"><label for="name">Recipe name</label></th>
				<td colspan="3"><input type="text" aria-required="true" size="40" value="<?php print stripslashes($recipe->title); ?>" id="title" name="title"/></td>
			</tr>
			<tr class="form-field">
				<th valign="top" scope="row"><label for="slug">Recipe  slug</label></th>
				<td colspan="3"><input type="text" size="40" value="<?php print $recipe->slug; ?>" id="slug" name="slug"/>
					<br/>
					The "slug" is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</td>
			</tr>
			<tr class="form-field">
				<th valign="top" scope="row">Notes (optional)</th>
				<td colspan="3"><textarea style="width: 97%;" cols="50" rows="5" id="notes" name="notes"><?php print stripslashes($recipe->notes); ?></textarea></td>
			</tr>
			<tr class="form-field">
				<th valign="top" scope="row">Category</th>
				<td colspan="3"><select name="category" id="category">
					<?php print $this->listOptions('categories', isset($recipe->category) ? $recipe->category : $this->defaultCategory); ?>
				</select></td>
			</tr>
			<tr class="form-field">
				<th valign="top" scope="row">Servings</th>
				<td colspan="3"><input name="servings" type="text" id="servings" value="<?php print $recipe->servings; ?>" style="width:25px" />
					<select name="servings_size" id="servings_size">
						<?php print $this->servingSizeOptions($recipe->servings_size); ?>
				</select></td>
			</tr>
			<tr class="form-field">
				<th valign="top" scope="row">Prep Time</th>
				<td><input name="prep_time" type="text" id="prep_time" value="<?php print $recipe->prep_time; ?>" style="width:25px" /> 
				minutes</td>
				<th>Cook Time</th>
				<td><input name="cook_time" type="text" id="cook_time" value="<?php print $recipe->cook_time; ?>" style="width:25px" />
minutes</td>
			</tr>
			<tr class="form-field">
				<th valign="top" scope="row">Ingredients</th>
				<td colspan="3"><textarea style="width: 97%;" cols="50" rows="5" id="ingredients" name="ingredients"><?php print stripslashes($recipe->ingredients); ?></textarea></td>
			</tr>
			<tr class="form-field">
				<th valign="top" scope="row"><label for="description">Instructions</label></th>
				<td colspan="3"><textarea style="width: 97%;" cols="50" rows="5" id="instructions" name="instructions"><?php print stripslashes($recipe->instructions); ?></textarea></td>
			</tr>
			<tr class="form-field">
				<th valign="top" scope="row">Status</th>
				<td colspan="3"><select name="status" id="status">
					<?php print $this->statusOptions($recipe->status); ?>
				</select></td>
			</tr>
		</tbody>
	</table>
	<p class="submit">
	    <?php if (function_exists('wpmu_create_blog')) : ?>
	    <input type="hidden" name="option_page" value="recipe_share_categories" />
	    <?php  else : ?>
	    <input type="hidden" name="page_options" value="title,slug,category,ingredients,instructions,status" />
	    <?php endif; ?>
		<input type="submit" value="Save Recipe" name="submit" class="button-primary"/>
	</p>
</form>
<?php include('footer.php'); ?>
