function fetchPosts(postType) {
    if (!postType) {
        jQuery('#post_list').hide();
        return;
    }

    jQuery.ajax({
        url: wpApiSettings.ajaxUrl,
        data: {
            action: 'fetch_posts',
            post_type: postType
        },
        success: function(response) {
            var postList = jQuery('#post_list');
            postList.empty();
            postList.append('<option value="">- select Post -</option>');
            
            response.forEach(function(post) {
                postList.append('<option value="' + post.ID + '">' + post.title + '</option>');
            });
            
            postList.show();
        }
    });
}
