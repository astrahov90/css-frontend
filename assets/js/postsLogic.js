import {bbCodeDecode, scrollIntoViewIfNeeded, checkNewestPostsFlag, hidePreloader} from "./commonLogic.js";
import {getPostsListPromise, getPostSetRatePromise, getPostGetRatePromise, getPostPromise, getCommentsListPromise} from "./apiLogic.js"

function loadPostsData(postTemplate, scrollDown=true) {
    let offset = $(".row.post").length;
    let newest = checkNewestPostsFlag();

    fillPostsListData(postTemplate,getPostsListPromise(newest, offset));

    if (scrollDown){
        let scrollToElement = $(".row.post:last");
        scrollIntoViewIfNeeded(scrollToElement);
    }
}

function loadAuthorsPostsData(postTemplate, authorId) {
    let offset = $(".row.post").length;
    let promise = getPostsListPromise(false, offset, authorId);
    fillPostsListData(postTemplate, promise, authorId);
}

function fillPostsListData(postTemplate,dataPromise, authorId=null)
{
    dataPromise.then((result)=>{
        let curCount = $(".row.post").length;
        result.data.forEach(function (elem, key) {
            let avatarField = '';
            if (!authorId)
                avatarField = `<img class='avatar' src='${elem.iconPath}' alt='Аватар автора'>`;

            let curIndex = curCount + key;
            let newElement = postTemplate(curIndex, elem);

            $(newElement).insertBefore($(".morePosts"));
        });

        showHideGetMorePostsButton(result.meta.to<result.meta.total)

        hidePreloader();

        hidePostsExpandButton();
    })
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

function loadCommentsData(commentTemplate, postId) {
    let offset = $(".row.comment").length;

    fillCommentsListData(commentTemplate, getCommentsListPromise(postId, offset));
}

function fillCommentsListData(commentTemplate, dataPromise){

    dataPromise.then(
        result => {
            let curCount = $(".row.comment").length;

            result.data.forEach(function (elem, key) {
                let curIndex = ++curCount;

                let newElement = commentTemplate(curIndex, elem);

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

function loadPostInfo(postTemplate, postId) {
    fillPostData(postTemplate, getPostPromise(postId));
}

function fillPostData(postTemplate, dataPromise)
{
    dataPromise.then((data)=>{
        let avatarField = `<img class='avatar' src='${data.iconPath}' alt='Аватар автора'>`;
        let newElement = postTemplate(0, data);
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

function ratePost(postId, likeStatus, ratingField, token){
    getPostSetRatePromise(postId, likeStatus, token)
        .then(()=>{
            getPostGetRatePromise(postId).then(result=>{
                ratingField.html(result.rating);
            })
        });

}

export {loadPostsData, loadAuthorsPostsData, loadCommentsData, loadPostInfo, ratePost}
