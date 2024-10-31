<!-- You can add text before this line, but do not change anything below this! -->
<form class="validate" action="<?php print $formaction; ?>" method="post" id="update" name="update">
    <input type="hidden" value="user-submit" name="action"/>
    <input type="hidden" value="<?php print $recipe->id; ?>" name="ID"/>
    <?php
    if (function_exists('wpmu_create_blog'))
        wp_nonce_field('recipe-press-options');
    else
        wp_nonce_field('update-options');
    ?>
    <?php include ($this->viewsPath . 'recipe-form.php'); ?>
    <p class="submit">
        <input name="status" id="status" type="hidden" value="pending" />
        <?php if (function_exists('wpmu_create_blog')) : ?>
        <input type="hidden" name="option_page" value="recipe_share_categories" />
        <?php  else : ?>
        <input type="hidden" name="page_options" value="title,slug,category,ingredients,instructions,status" />
        <?php endif; ?>
        <input type="submit" value="<?php _e('Submit Recipe'); ?>" name="submit" class="button-primary"/>
    </p>
</form>
<!-- You can add text after this line, but do not change anything above this! -->
