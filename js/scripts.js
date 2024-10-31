// JavaScript Document

function onClickDeleteCategory(catName) {
    return confirm('Warning: You are about to delete the "' + catName + '" category. Any recipes in this category will be moved to the default category. Are you sure?');
}

function onClickDeleteRecipe(name) {
    return confirm('Warning: You are about to delete the "' + name + '" recipe. Any posts or pages with short codes for this recipe will now appear blank. Are you sure?');
}

function recipeSearch() {
    var recipe = urlencode(document.recipe_form.recipe_search.value);
    var slug = urlencode(document.recipe_form.slug_search.value);
    var user = document.recipe_form.user_search.options[document.recipe_form.user_search.selectedIndex].value;
    var category = document.recipe_form.category_search.options[document.recipe_form.category_search.selectedIndex].value;
    var status = document.recipe_form.status_search.options[document.recipe_form.status_search.selectedIndex].value;
	
    var url = document.recipe_form.method + '&recipe=' + recipe + '&slug=' + slug + '&user=' + user + '&category=' + category + '&status=' + status;
	
    window.location.href = url;
}

function urlencode(str) {
    return escape(str).replace(/\+/g,'%2B').replace(/%20/g, '+').replace(/\*/g, '%2A').replace(/\//g, '%2F').replace(/@/g, '%40');
}	

/* Set up the Autocompleter field */
jQuery(document).ready(function(){
    /* Recipe AJAX Stuff */
    jQuery("input#recipe_search").autocomplete(ajaxurl +"?action=recipe_press_recipe_title" );
    jQuery("input#recipe_search").result(function(event, data, formatted){
        var url = document.recipe_form.method + "&action=edit&id=" + data[1];
        window.location.href = url;
        return false;
    });

    jQuery("input#slug_search").autocomplete(ajaxurl +"?action=recipe_press_recipe_slug" );
    jQuery("input#slug_search").result(function(event, data, formatted){
        var url = document.recipe_form.method + "&action=edit&id=" + data[1];
        window.location.href = url;
        return false;
    });


});
