<div class="wrap">
<div class="icon32" id="icon-edit-pages"><br/>
</div>
<h2>Recipe Share &raquo; Overview</h2>
<div class="postbox" style="width:49%; float:left">
	<h3 class="handl" style="margin:0;padding:3px;cursor:default;">At A Glance</h3>
	<div class="table">
		<table class="widefat">
			<tbody>
				<tr>
					<td class="num" style="width:1px"><a href="/wp-admin/admin.php?page=recipe_share_list"><?php print $this->showCount('recipes'); ?></a></td>
					<td class="text"><a href="/wp-admin/admin.php?page=recipe_share_list"><?php _e('Recipes'); ?></a></td>
					<td class="num" style="width:1px">0</td>
					<td class="text"><?php _e('Comments'); ?></td>
				</tr>
				<tr>
					<td class="num" style="width:1px"><a href="/wp-admin/admin.php?page=recipe_share_list"><?php print $this->showCount('recipes', 'pending'); ?></a></td>
					<td class="text"><a href="/wp-admin/admin.php?page=recipe_share_list&status=pending"><?php _e('Recipes Pending Review'); ?></a></td>
					<td class="num" style="width:1px">0</td>
					<td class="text"><?php _e('Comments Pending Review'); ?></td>
				</tr>
				<tr>
					<td class="num" style="width:1px"><a href="/wp-admin/admin.php?page=recipe_share_categories"><?php print $this->showCount('categories'); ?></a></td>
					<td class="text"><a href="/wp-admin/admin.php?page=recipe_share_categories"><?php _e('Categories'); ?></a></td>
					<td class="num" style="width:1px">0</td>
					<td class="text"><?php _e('Comments Approved'); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="versions" style="padding:5px;"> 
		<p><span>You are using <strong> Recipe Share <?php print $this->showVersion(); ?> </strong> .</span> <br class="clear"/>
		</p>
</div>
</div>

<div class="postbox" style="width:49%; float:right">
	<h3 class="handl" style="margin:0; padding:3pxcursor:default;">About Recipe Share</h3>
	<div style="padding:5px;">
		<p>Recipe Share allows you to share recipes on your web site. This is an early release with the very basic functions. Future enhancements will include comments, photos, ratings and more. Keep up to date at the official <a href="http://wordpress.grandslambert.com/plugins/recipe-share.html" target="_blank">Recipe Share</a> page.</p>
</div>
</div>
<div style="clear:both"></div>
<?php include "footer.php"; ?>
