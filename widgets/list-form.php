
<p>
    <label for="<?php echo $this->get_field_id('title'); ?>">
        <?php _e('Widget Title (optional):'); ?>
    </label>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</p>
<p>
    <label for="rss-items-4">How many items would you like to display?</label>
    <select name="<?php echo $this->get_field_name('items'); ?>" id="<?php echo $this->get_field_id('items'); ?>">
        <?php
        for ( $i = 1; $i <= 20; ++$i )
            echo "<option value='$i' " . ( $items == $i ? "selected='selected'" : '' ) . ">$i</option>";
        ?>
    </select>
</p>
<p>
    <label for="<?php echo $this->get_field_id('category'); ?>">
        <?php _e('Recipe Category:'); ?>
    </label>
    <select name="<?php echo $this->get_field_name('category'); ?>" id="<?php echo $this->get_field_id('category'); ?>">
        <option value="all">Show All Categories</option>
        <?php print $RECIPEPRESSOBJ->listOptions('categories', $category); ?>
    </select>
</p>
<p>
    <label for="<?php echo $this->get_field_id('target'); ?>">
        <?php _e('Link Target:'); ?>
    </label>
    <select name="<?php echo $this->get_field_name('target'); ?>" id="<?php echo $this->get_field_id('target'); ?>">
        <option value="0">None</option>
        <option value="_blank" <?php if ($linktarget == '_blank') echo 'selected'; ?>>New Window</option>
        <option value="_top" <?php if ($linktarget == '_top') echo 'selected'; ?>>Top Window</option>
    </select>
</p>
<p>
    <label for="<?php echo $this->get_field_id('submit_link'); ?>">Add link to Submit form?</label>
    <input name="<?php echo $this->get_field_name('submit_link'); ?>" type="checkbox" id="<?php echo $this->get_field_id('submit_link'); ?>" value="Y" <?php if ($submitlink == 'Y') print 'checked="checked"'; ?> />
</p>
