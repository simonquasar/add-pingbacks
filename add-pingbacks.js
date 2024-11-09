function fetchPosts(postType) {
    var postList = document.getElementById('post_list');
    if (!postType) {
        postList.style.display = 'none';
        return;
    }

    // Using wpApiSettings.ajaxUrl which will be localized
    fetch(wpApiSettings.ajaxUrl + '?action=fetch_posts&post_type=' + postType)
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            var options = '';
            data.forEach(function(post) {
                options += '<option value="' + post.ID + '">' + post.title + '</option>';
            });
            postList.innerHTML = options;
            postList.style.display = 'block';
        })
        .catch(function(error) {
            console.error('Error fetching posts:', error);
            postList.innerHTML = '<option value="">Error loading posts</option>';
            postList.style.display = 'block';
        });
}
