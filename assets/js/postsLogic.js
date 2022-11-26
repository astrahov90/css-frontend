import {bbCodeDecode, scrollIntoViewIfNeeded, checkNewestPostsFlag, hidePreloader} from "./commonLogic.js";
import {getPostsListPromise, getPostSetRatePromise, getPostGetRatePromise, getPostPromise, getCommentsListPromise} from "./apiLogic.js"

function loadPostsData(scrollDown=true) {
    let offset = $(".row.post").length;
    let newest = checkNewestPostsFlag();

    fillPostsListData(getPostsListPromise(newest, offset));

    if (scrollDown){
        let scrollToElement = $(".row.post:last");
        scrollIntoViewIfNeeded(scrollToElement);
    }
}

function loadAuthorsPostsData(authorId) {
    let offset = $(".row.post").length;
    let promise = getPostsListPromise(false, offset, authorId);
    fillPostsListData(promise, authorId);
}

function fillPostsListData(dataPromise, authorId=null)
{
    dataPromise.then((result)=>{
        let curCount = $(".row.post").length;
        result.data.forEach(function (elem, key) {
            let avatarField = '';
            if (!authorId)
                avatarField = "<img class='avatar' src='"+elem.iconPath+"' alt='Аватар автора'>";

            let curIndex = curCount + key;
            let newElement = renderPostElement(elem, curIndex, avatarField);

            $(newElement).insertBefore($(".morePosts"));
        });

        showHideGetMorePostsButton(result.meta.to<result.meta.total)

        hidePreloader();

        hidePostsExpandButton();
    })
}

function renderPostElement(elem,curIndex, avatarField="") {
    return "<div class='row post d-flex'>\n" +
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
        "            </div>"
}

function hidePostsExpandButton()
{
    $.each($(".row.post"),function (index,elem) {
        if ($(elem).find('.card-body').height()<parseInt($(elem).find('.card-body').css('max-height')))
        {
            $(elem).find('.card-read-more-button').remove();
        }
    });
}

function loadCommentsData(postId) {
    let offset = $(".row.comment").length;

    fillCommentsListData(getCommentsListPromise(postId, offset));
}

function fillCommentsListData(dataPromise){

    dataPromise.then(
        result => {
            let curCount = $(".row.comment").length;

            result.data.forEach(function (elem, key) {
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
                $(".hasComments").show();
            }

            showHideGetMoreCommentsButton(result.meta.to<result.meta.total);
            hidePreloader();
        }
    )
}

function loadPostInfo(postId) {
    fillPostData(getPostPromise(postId));
}

function fillPostData(dataPromise)
{
    dataPromise.then((data)=>{
        let avatarField = "<img class='avatar' src='"+data.iconPath+"' alt='Аватар автора'>";
        let newElement = renderPostElement(data, 0, avatarField);
        $(".row.post").replaceWith($(newElement));
    })
}

function showHideGetMorePostsButton(show=true)
{
    if (show)
        $(".morePosts").show();
    else
        $(".morePosts").hide();
}

function showHideGetMoreCommentsButton(show=true)
{
    if (show)
        $(".moreComments").show();
    else
        $(".moreComments").hide();
}

function ratePost(postId, likeStatus, ratingField){
    getPostSetRatePromise(postId, likeStatus)
        .then(()=>{
            getPostGetRatePromise(postId).then(result=>{
                ratingField.html(result);
            })
        });

}

export {loadPostsData, loadAuthorsPostsData, loadCommentsData, loadPostInfo, ratePost}
