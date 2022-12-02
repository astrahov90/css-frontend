import {hidePreloader} from "./commonLogic.js";
import {getAuthorsListPromise, getAuthorPromise} from "./apiLogic.js";

function loadAuthorsListData(authorTemplate) {
    let offset = $(".row.authors").length;

    fillAuthorsListData(authorTemplate, getAuthorsListPromise(offset));
}

function fillAuthorsListData(authorTemplate, dataPromise)
{
    dataPromise.then((result)=>{
        result.data.forEach(function (elem, key) {
            let newElement = authorTemplate(elem);

            $(newElement).insertBefore($(".moreAuthors"));
        });

        showHideGetMoreAuthorsButton(result.meta.to<result.meta.total)

        hidePreloader();
    })
}

function loadAuthorInfo(authorTemplate,authorId) {
    fillAuthorData(authorTemplate, getAuthorPromise(authorId));
}

function fillAuthorData(authorTemplate, dataPromise)
{
    dataPromise.then((data)=>{
        let newElement = authorTemplate(data);
        $(".row.authors").replaceWith($(newElement));
    })
}

function showHideGetMoreAuthorsButton(show=true)
{
    if (show)
        $(".moreAuthors").show();
    else
        $(".moreAuthors").hide();
}

export {loadAuthorsListData, loadAuthorInfo}
