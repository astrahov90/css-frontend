import {hidePreloader} from "./commonLogic.js";
import {getAuthorsListPromise, getAuthorPromise} from "./apiLogic.js";

function loadAuthorsListData() {
    let offset = $(".row.authors").length;

    fillAuthorsListData(getAuthorsListPromise(offset));
}

function fillAuthorsListData(dataPromise)
{
    dataPromise.then((result)=>{
        let curCount = $(".row.authors").length;
        result.data.forEach(function (elem, key) {
            let avatarField = "<img class='avatar' src='"+elem.iconPath+"' alt='Аватар автора'>";

            let curIndex = curCount + key;
            let newElement = renderAuthorElement(elem, curIndex, avatarField);

            $(newElement).insertBefore($(".moreAuthors"));
        });

        showHideGetMoreAuthorsButton(result.meta.to<result.meta.total)

        hidePreloader();
    })
}

function renderAuthorElement(elem) {
    return "<div class='row authors'>\n" +
        "                <div class='col-2'>\n" +
        "                    <div class='container'><img class='avatar pt-1' src='" + elem.iconPath + "' alt='Аватар автора'></div>\n" +
        "                </div>\n" +
        "                <div class='col-9'>\n" +
        "                    <div class='container'>\n" +
        "                        <div class='row'>\n" +
        "                            <div class='card'>\n" +
        "                                <div class='card-title'>\n" +
        "                                    <div class='container-fluid'>\n" +
        "                                        <div class='row'>\n" +
        "                                            <div class='col-4 fw-bold'>" + elem.authorName + "</div>\n" +
        "                                            <div class='col-4 offset-4'>Дата регистрации: " + elem.created_at + "</div>\n" +
        "                                        </div>\n" +
        "                                    </div>\n" +
        "                                </div>\n" +
        "                                <div class='card-body'>\n" +
        "                                    <p> Количество постов автора: " + elem.posts_count +
        "                                       <a href='/authors/" + elem.authorId + "/posts'>Перейти</a></p>\n" +
        "                                </div>\n" +
        "                                <div class='card-bottom'></div>\n" +
        "                            </div>\n" +
        "                            <div class='clearfix'></div>\n" +
        "                        </div>\n" +
        "                    </div>\n" +
        "                </div>\n" +
        "            </div>";
}

function loadAuthorInfo(authorId) {
    fillAuthorData(getAuthorPromise(authorId));
}

function fillAuthorData(dataPromise)
{
    dataPromise.then((data)=>{
        let avatarField = "<img class='avatar' src='"+data.iconPath+"' alt='Аватар автора'>";
        let newElement = renderAuthorElement(data, 0, avatarField);
        $(".row.authors").replaceWith($(newElement));
    })
}

function showHideGetMoreAuthorsButton(show=true)
{
    if (show)
        $(".morePosts").show();
    else
        $(".morePosts").hide();
}

export {loadAuthorsListData, loadAuthorInfo}
