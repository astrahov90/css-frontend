<section class="section-body bg-light">
    <div class="container bg-light">
        <div class="loaderBody bg-light">
            <div id="loader"></div>
        </div>
        <label class="morePosts">Еще...</label>
    </div>
</section>

<script src="/assets/js/posts.js"></script>

<script>
    let morePostsBtn = $(".morePosts");

    morePostsBtn.hide();

    $(".card-read-more-button").click(function (e) {
        if ($("#" + $(this).attr("for")).is(":not(:checked)")) {
            scrollIntoViewIfNeeded($(e.target));
        }
    });

    function scrollIntoViewIfNeeded($target) {
        if ($target.offset()) {
            let targetOffset = $target.offset();
            let targetPosition = $target.position();

            let targetFullPosition = targetOffset.top + targetPosition.top;

            if (targetFullPosition + $target.height() >
                $(window).scrollTop() + (
                    window.innerHeight || document.documentElement.clientHeight
                )) {
                //scroll down
                $("html,body").animate({
                        scrollTop: targetFullPosition -
                        (window.innerHeight || document.documentElement.clientHeight)
                        + $target.height() + 15
                    }
                );
            }
        }
    }

    $(document).ready(function () {
        getMorePosts(false);
    });

    morePostsBtn.click(function () {
        getMorePosts();
    });

</script>