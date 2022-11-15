<section class="section-body bg-light">
    <div class="container bg-light">
        <div class='row authors'>
            <!-- load author info -->
        </div>
        <div class="loaderBody bg-light">
            <div id="loader"></div>
        </div>
        <label class="morePosts">Еще...</label>
    </div>
</section>
<script src="/assets/js/authors.js"></script>
<script src="/assets/js/posts.js"></script>
<script>loadCSS("/assets/css/authors.css")</script>
<script>loadCSS("/assets/css/posts.css")</script>

<script>
    let userId = "<?php echo $author['authorId'] ?>";

    let mortPostsBtn = $(".morePosts");
    mortPostsBtn.hide();

    $(".card-read-more-button").click(function (e) {
        if ($("#"+$(this).attr("for")).is(":not(:checked)")){
            scrollIntoViewIfNeeded($(e.target));
        }
    });

    $(document).ready(function () {
        getUserInfo(userId);
        getUserPosts(userId);
    });

    mortPostsBtn.click(function () {
        getUserPosts(userId);
    });

</script>