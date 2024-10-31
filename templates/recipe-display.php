<h3 class="recipe-title"><?php print $recipe->title; ?></h3>
<h4 class="recipe-author">Submitted by: <?php print $this->displayUser($recipe->user_id); ?></h4>

<?php if ($recipe->notes) : ?>
<blockquote class="recipe-notes"><?php print stripslashes($recipe->notes); ?></blockquote>
<?php endif; ?>

<dl class="recipe-list">
	<dt class="recipe-section">Details</dt>
	
	<dd class="recipe-times"><ul>
	<?php if ($recipe->prep_time) : ?>
	<li class="recipe-prep">Prep Time: <?php print $recipe->prep_time; ?> minutes.</li>
	<?php endif; ?>
	
	<?php if ($recipe->cook_time) : ?>
	<li class="recipe-prep">Cook Time: <?php print $recipe->cook_time; ?> minutes.</li>
	<?php endif; ?>
	
	<?php if ($recipe->ready_time) : ?>
	<li class="recipe-total">Ready in <?php print $recipe->ready_time; ?>.</li>
	<?php endif;?>

	<?php if ($recipe->servings) : ?>
	<li class="reipe-servings">Makes: <?php print nl2br($recipe->servings); ?> <?php print ucfirst($recipe->servings_size); ?></li>
	<?php endif; ?>
	
	</ul></dd>
	
	<dt class="recipe-section">Ingredients</dt>
	<dd class="recipe-ingredients"><?php print $this->bulletize($recipe->ingredients); ?></dd>
	<dt class="recipe-section">Directions</dt>
	<dd class="recipe-instructions"><?php print $this->bulletize($recipe->instructions); ?></dd>
</dl>
