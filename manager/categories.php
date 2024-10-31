<div class="wrap">
<div class="icon32" id="icon-edit-pages"><br/>
</div>
<h2>Recipe Share &raquo; Categories</h2>
<?php if ( isset($this->message) ) : ?>
<div class="updated fade below-h2" id="message" style="background-color: rgb(255, 251, 204);">
	<p><?php print $this->message; ?></p>
</div>
<?php endif; ?>

<div id="col-container">
	<div id="col-right">
		<div class="col-wrap">
			<form id="posts-filter" method="get" action="?page=recipe_share_categories">
				<h3>Existing Categories</h3>
				<table class="widefat fixed" cellspacing="0">
					<thead>
						<tr>
							<th>Name</th>
							<th>Slug</th>
							<th>Recipes</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th>Name</th>
							<th>Slug</th>
							<th>Recipes</th>
						</tr>
					</tfoot>
					<tbody>
						<?php 
					$cats = $this->getRows('categories', $page);
					foreach ($cats as $cat) : ?>
						<tr id="recipe-cat-<?php print $cat->id; ?>" class="iedit">
							<td class="name column-name"><a href="?page=recipe_share_categories&amp;action=edit&amp;cat_ID=<?php print $cat->id; ?>"><?php print $cat->name; ?></a><br />
								<div class="row-actions">
									<span class="edit"><a href="?page=recipe_share_categories&amp;action=edit&amp;cat_ID=<?php print $cat->id; ?>">Edit</a></span>
									<!--<span class="inline hide-if-no-js"> | <a class="editinline" href="#">Quick Edit</a> | </span>-->
									<?php if ($cat->id != $this->default_category) : ?>
									<span class="delete"> | <a href="?page=recipe_share_categories&amp;action=delete&amp;cat_ID=<?php print $cat->id; ?>" onclick="return onClickDeleteCategory('<?php print $cat->name; ?>')" class="delete:the-list:link-cat-9 submitdelete">Delete</a></span>
									<?php else : ?>
									<span class="delete"> | Default </span>
									<?php endif; ?>
									</div></td>
							<td class="slug column-slug"><?php print $cat->slug; ?></td>
							<td class="links column-links num"><?php print $this->showCount('recipes', 'all', $cat->id); ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</form>
		</div>
	</div>
	<div id="col-left">
		<div class="col-wrap">
			<div class="form-wrap">
				<h3>Add Recipe Category</h3>
				<form action="?page=recipe_share_categories" method="post" class="add:the-list: validate" id="addcat" name="addcat">
					<input type="hidden" value="addcat" name="action"/>
					<?php 
						if (function_exists('wpmu_create_blog'))
							wp_nonce_field('wp_recipe-options');
						else
							wp_nonce_field('update-options');
					?>
					<div class="form-field form-required">
						<label for="name">Recipe Category name</label>
						<input type="text" aria-required="true" size="40" value="" id="name" name="name"/>
					</div>
					<div class="form-field">
						<label for="slug">Recipe Category slug</label>
						<input type="text" size="40" value="" id="slug" name="slug"/>
						<p>The "slug" is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</p>
					</div>
					<div class="form-field">
						<label for="description">Description (optional)</label>
						<textarea cols="40" rows="5" id="description" name="description"> </textarea>
					</div>
					<p class="submit">
						<?php if (function_exists('wpmu_create_blog')) : ?>
						<input type="hidden" name="option_page" value="recipe_share_categories" />
						<?php  else : ?>
						<input type="hidden" name="page_options" value="name,slug,description" />
						<?php endif; ?>
						<input type="submit" value="Add Category" name="submit" class="button"/>
					</p>
				</form>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>
