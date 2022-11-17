function getMorePosts(scrollDown=true) {
    getPostsCommon();
    if (scrollDown){
        scrollIntoViewIfNeeded($(".row.post:last"));
    }
}

function getUserPosts(authorId) {
    getPostsCommon(authorId);
}

function getPostElement(elem,curIndex, avatarField="") {
    let newElement = "<div class='row post d-flex'>\n" +
        "                <input type='hidden' class='postId' value='" + elem.id + "'>\n" +
        "                <div class='col-2 d-flex flex-column align-items-stretch'>\n" +
        "                    <div class='flex-grow-0 align-self-start'>Автор: <a href='/authors/" + elem.author_id + "/posts'>" + elem.authorName + "</a>" + "</div>\n" +
        "                    <div class='flex-grow-1 align-self-start pt-1'>" + avatarField + "</div>\n" +
        "                    <div class='white-spaces-pre'>Дата публикации: \n" + elem.created_at + "</div>\n" +
        "                </div>\n" +
        "                <div class='col-9'>\n" +
        "                    <div class='container'>\n" +
        "                        <div class='row'>\n" +
        "                            <div class='card'>\n" +
        "                                <div class='card-title'>\n" +
        "                                    <div class='container-fluid'>\n" +
        "                                        <div class='row'>\n" +
        "                                            <div class='col-9 fw-bold'>" + elem.title + "</div>\n" +
        "                                            <div class='col-3'> Рейтинг: <img class='rating-arrow rating-down' src='/assets/img/down-arrow-red.svg'><span class='rating-count'>" + elem.likes_count + "</span><img class='rating-arrow rating-up' src='/assets/img/up-arrow-green.svg'></div>\n" +
        "                                        </div>\n" +
        "                                    </div>\n" +
        "                                </div>\n" +
        "                                <input type='checkbox' data-more-checker='card-read-more-checker' id='card-read-more-checker-" + curIndex + "'/>\n" +
        "                                <div class='card-body'>\n" +
        "                                    <p>" + bbCodeDecode(elem.body) + "</p>\n" +
        "                                    <div class='card-bottom'>\n" +
        "                                    </div>\n" +
        "                                </div>\n" +
        "                                    <a href='/posts/" + elem.id + "/comments/'>" + elem.comments_count_text + "</a>\n" +
        "                                <label for='card-read-more-checker-" + curIndex + "' class='card-read-more-button'></label>\n" +
        "                            </div>\n" +
        "                            <div class='clearfix'></div>\n" +
        "                        </div>\n" +
        "                    </div>\n" +
        "                </div>\n" +
        "            </div>";
    return newElement;
}

function getPostsCommon(authorId=false) {
    let curCount = $(".row.post").length;
    let querystring = "/posts/getPosts"+(location.search?location.search+"&":"?")+"offset="+curCount;
    if (authorId)
    {
        querystring = "/posts/getPosts"+(location.search?location.search+"&":"?")+"authorId="+authorId+"&offset="+curCount;
    }

    $.get(querystring).done(function (data) {

        data.posts.forEach(function (elem, key) {
            let avatarField = "<img class='avatar' src='"+elem.iconPath+"' alt='Аватар автора'>";
            if (authorId){
                avatarField = "";
            }
            let curIndex = curCount + key;
            let newElement = getPostElement(elem, curIndex, avatarField);

            $(newElement).insertBefore($(".morePosts"));
        });

        if (data.currentCount>=data.totalCount)
        {
            $(".morePosts").hide();
        }
        else {
            $(".morePosts").show();
        }

        $(".loaderBody").remove();

        hidePostsMoreButton();
    });
}
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
            $("html,body").animate({scrollTop: targetFullPosition -
                (window.innerHeight || document.documentElement.clientHeight)
                + $target.height() + 15}
            );
        }
    }
}

function hidePostsMoreButton()
{
    $.each($(".row.post"),function (index,elem) {
        if ($(elem).find('.card-body').height()<parseInt($(elem).find('.card-body').css('max-height')))
        {
            $(elem).find('.card-read-more-button').remove();
        }
    });
}

function getPostComments(postId) {
    let curCount = $(".row.comment").length;
    querystring = "/comments/getCommentsByPost"+(location.search?location.search+"&":"?")+"postId="+postId+"&offset="+curCount;

    $.get(querystring).done(function (data) {

        data.comments.forEach(function (elem, key) {
            let curIndex = ++curCount;
            let newElement = "<div class='row comment d-flex'>\n" +
                "                <div class='col-2 d-flex flex-column align-items-stretch'>\n" +
                "                    <div class='flex-grow-0 align-self-start'>№" + curIndex + "    Автор: <a href='/authors/" + elem.authorId + "/posts'>" + elem.authorName + "</a></div>" +
                "                    <div class='flex-grow-1 align-self-start pt-1'><img class='avatar' src='" + elem.iconPath + "' alt='Аватар автора'></div>" +
                "                    <div class='white-spaces-pre'>Дата комментария: \n" + elem.created_at + "</div>\n" +
                "                </div>\n" +
                "                <div class='col-9'>\n" +
                "                    <div class='container'>\n" +
                "                        <div class='row'>\n" +
                "                            <div class='card'>\n" +
                "                                <input type='checkbox' checked data-more-checker='card-read-more-checker' id='card-read-more-checker-" + curIndex + "'/>\n" +
                "                                <div class='card-body'>\n" +
                "                                    <p>" + bbCodeDecode(elem.body) + "</p>\n" +
                "                                    <div class='card-bottom'>\n" +
                "                                    </div>\n" +
                "                                </div>\n" +
                "                            </div>\n" +
                "                            <div class='clearfix'></div>\n" +
                "                        </div>\n" +
                "                    </div>\n" +
                "                </div>\n" +
                "            </div>";

            $(newElement).insertBefore($(".moreComments"));
        });

        if (curCount > 0) {
            hasComments.show();
        }

        /*scrollIntoViewIfNeeded($(".row.post:last"));*/

        if (data.currentCount>=data.totalCount)
        {
            $(".moreComments").hide();
        }
        else {
            $(".moreComments").show();
        }

        $(".loaderBody").remove();

        hidePostsMoreButton();
    });
}

function getPostInfo(postId) {
    querystring = "/posts/getPost?postId="+postId;

    $.get(querystring).done(function (data) {
        let avatarField = "<img class='avatar' src='"+data.iconPath+"' alt='Аватар автора'>";
        let newElement = getPostElement(data, 0, avatarField);
        $(".row.post").replaceWith($(newElement));
    });
}

$('body').on('click','.rating-arrow', function () {
    let method;
    if ($(this).hasClass('rating-up')){
        method = "PostLike";
    }
    if ($(this).hasClass('rating-down')){
        method = "PostDisLike";
    }

    if (!method)
        return false;

    let curPostId;

    if (typeof postId !== 'undefined')
    {
        curPostId = postId;
    }
    else
    {
        curPostId = $(this).closest(".row.post").find('.postId').val();
    }

    let ratingField = $(this).closest('.row').find('.rating-count');

    $.post("/posts/"+curPostId+"/"+method).done(function (data) {
        if (data.ratingCount !== undefined)
            ratingField.html(data.ratingCount);
        if (data.error !== undefined)
            alert(data.error);
    });
    return false;
});
