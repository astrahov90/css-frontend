
function getPostsListPromise(newest=false, offset=0, authorId=false) {
    let querystring = "/posts/getPosts"+(location.search?location.search+"&":"?")+"offset="+offset;
    if (authorId)
    {
        querystring += "&authorId="+authorId;
    }
    if (newest)
    {
        querystring += "&newest=";
    }

    return $.get(querystring).catch(
        reason=>{console.log(reason.response.data??reason.responseText);
        });
}

function getAuthorsListPromise(offset=0) {
    let querystring = "/authors/getUsers"+(location.search?location.search+"&":"?")+"offset="+offset;

    return $.get(querystring).catch(
        reason=>{console.log(reason.response.data??reason.responseText);
        });
}

function getPostSetRatePromise(postId, likeStatus, token) {
    let querystring = "/posts/"+postId+"/"+(likeStatus?"like":"dislike");

    return $.post(querystring,{token}).catch(
        reason=>{console.log(reason.response.data??reason.responseText);
        });
}

function getPostGetRatePromise(postId) {
    let querystring = "/posts/"+postId+"/rating";
    return $.get(querystring).catch(
        reason=>{console.log(reason.response.data??reason.responseText);
        });
}

function getCommentsListPromise(postId, offset=0) {
    let querystring = "/comments/getCommentsByPost"+(location.search?location.search+"&":"?")+"offset="+offset+"&postId="+postId;
    return $.get(querystring).catch(
        reason=>{console.log(reason.response.data??reason.responseText);
        });
}

function getPostPromise(postId) {
    let querystring = "/posts/getPost?postId="+postId;

    return $.get(querystring).catch(
        reason=>{console.log(reason.response.data??reason.responseText);
    });
}

function getAuthorPromise(authorId) {
    let querystring = "/authors/getAuthor?authorId="+authorId;

    return $.get(querystring).catch(
        reason=>{console.log(reason.response.data??reason.responseText);
        });
}

export {getPostsListPromise, getCommentsListPromise, getPostPromise, getPostSetRatePromise, getPostGetRatePromise,
    getAuthorsListPromise, getAuthorPromise}
