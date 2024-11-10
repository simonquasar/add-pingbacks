function fetchPosts(postType) {
    if (!postType) {
        document.getElementById('post_list').style.display = 'none';
        return;
    }

    const postList = document.getElementById('post_list');
    postList.style.display = 'block';

    jQuery.ajax({
        url: wpApiSettings.ajaxUrl,
        type: 'GET',
        data: {
            action: 'fetch_posts',
            post_type: postType
        },
        success: function (response) {
            postList.innerHTML = '';
            response.forEach(function (post) {
                const option = document.createElement('option');
                option.value = post.ID;
                option.textContent = post.title;
                postList.appendChild(option);
            });
        },
        error: function (error) {
            console.error('Error fetching posts:', error);
        }
    });
}