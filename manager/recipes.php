<div class="wrap">
<div class="icon32" id="icon-edit-pages"><br/>
</div>
<h2>Recipe Share &raquo; Recipes</h2>
<div class="col-wrap">
<h3>Recipes</h3>
<?php if ( isset($this->message) ) : ?>
<div class="updated fade below-h2" id="message" style="background-color: rgb(255, 251, 204);">
	<p><?php print $this->message; ?></p>
</div>
<?php endif; ?>
<form id="recipe_form" name="recipe_form">
	<table class="widefat post fixed" cellspacing="0">
		<thead>
			<tr>
				<th style="" class="manage-column column-cb check-column" id="cb" scope="col">&nbsp;</th>
				<th><input name="recipe_search" type="text" id="recipe_search" value="<?php print $_GET['recipe']; ?>" /></th>
				<th><input name="slug_search" type="text" id="slug_search" value="<?php print $_GET['slug']; ?>" /></th>
				<th><select name="user_search" id="user_search" onchange="recipeSearch()">
						<option value="all">All</option>
						<?php print $this->userList($_GET['user']); ?>
					</select>
				</th>
				<th> <select name="category_search" id="category_search" onchange="recipeSearch()">
						<option value="all">All</option>
						<?php print $this->listOptions('categories', $_GET['category']); ?>
					</select>
				</th>
				<th><select name="status_search" id="status_search" onChange="recipeSearch()">
						<option value="all">All</option>
						<?php print $this->statusOptions($_GET['status']); ?>
				</select></th>
				<th><input name="filter" type="button" id="filter" onclick="recipeSearch()" value="Filter" /></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
			<tr>
				<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"/></th>
				<th>Recipe Name</th>
				<th>Slug</th>
				<th>Contributor</th>
				<th>Category</th>
				<th>Status</th>
				<th>Views</th>
				<th>Comments</th>
				<th>Date</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"/></th>
				<th>Recipe Name</th>
				<th>Slug</th>
				<th>Contributor</th>
				<th>Category</th>
				<th>Status</th>
				<th>Views</th>
				<th>Comments</th>
				<th>Date</th>
			</tr>
		</tfoot>
		<tbody>
			<?php 
			$recipes = $this->getRecipes(array('recipe'=>$_GET['recipe'], 'slug'=>$_GET['slug'], 'category'=>$_GET['category'], 'user'=>$_GET['user'], 'status'=>$_GET['status']) );
			foreach ($recipes as $recipe) : ?>
			<tr>
				<th class="check-column" scope="row"><input type="checkbox" value="<?php print $cat->id; ?>" name="linkcheck[]"/></th>
				<td class="name column-name"><a href="?page=recipe_share_list&amp;action=edit&amp;id=<?php print $recipe->id; ?>"><?php print $recipe->title; ?></a><br />
					<div class="row-actions"><span class="edit"><a href="?page=recipe_share_list&amp;action=edit&amp;id=<?php print $recipe->id; ?>">Edit</a> | </span>
						<!--<span class="inline hide-if-no-js"><a class="editinline" href="#">Quick Edit</a> | </span>-->
						<span class="delete"><a href="?page=recipe_share_list&amp;action=delete&amp;id=<?php print $recipe->id; ?>" class="delete:the-list:link-cat-9 submitdelete" onclick="return onClickDeleteRecipe('<?php print $recipe->title; ?>')">Delete</a></span></div></td>
				<td class="slug column-slug"><?php print $recipe->slug; ?></td>
				<td class="user column-user"><?php print $this->displayUser($recipe->user_id); ?></td>
				<td class="category column-category"><?php print $recipe->category_name; ?></td>
				<td class="status column-status"><?php print ucfirst($recipe->status); ?></td>
				<td class="views column-views num"><?php print $recipe->views_total; ?></td>
				<td class="comments column-comments num"><?php print $recipe->comment_total; ?></td>
				<td class="recipes column-recipes num"><?php print date("F d, Y", strtotime($recipe->added)); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</form>
<?php include('footer.php'); ?>
