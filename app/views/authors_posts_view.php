<section class="section-body bg-light">
    <div class="container bg-light">
        <div class='row authors'>
            <!-- load author info -->
        </div>
        <div class="loaderBody bg-light">
            <div id="loader"></div>
        </div>
        <div class='clearfix'></div>
        <div class="row mt-2 hasPosts">
            <div class="col-12 fw-bold">Посты пользователя:</div>
        </div>
        <label class="morePosts">Еще...</label>
    </div>
</section>

<script type="module">

    let authorId = "<?php echo $authorId ?>";

    let morePostsBtn = $(".morePosts");

    morePostsBtn.hide();

    $(".card-read-more-button").click(function (e) {
        if ($("#" + $(this).attr("for")).is(":not(:checked)")) {
            scrollIntoViewIfNeeded($(e.target));
        }
    });

    $(document).ready(function () {
        loadAuthorInfo(authorId);
        loadAuthorsPostsData(authorId);
    });

    morePostsBtn.click(function () {
        loadAuthorsPostsData(authorId);
    });

    $('body').on('click','.rating-arrow', function () {
        let curPostId;

        if (typeof postId !== 'undefined')
        {
            curPostId = postId;
        }
        else
        {
            curPostId = $(this).closest(".row.post").find('.postId').val();
        }

        let like = true;
        if ($(this).hasClass('rating-down')){
            like = false;
        }

        let ratingField = $(this).closest('.row').find('.rating-count');

        ratePost(curPostId, like, ratingField);

        return false;
    });

</script>