import * as commonLogic from './commonLogic.js';
window.scrollIntoViewIfNeeded = commonLogic.scrollIntoViewIfNeeded;
window.bbCodeDecode = commonLogic.bbCodeDecode;
window.onscroll = function() {commonLogic.scrollFunction()};
$('#toTopBtn').click(()=>commonLogic.topFunction());

import * as postsLogic from './postsLogic.js';
window.loadPostsData = postsLogic.loadPostsData;
window.ratePost = postsLogic.ratePost;
window.loadPostInfo = postsLogic.loadPostInfo;
window.loadCommentsData = postsLogic.loadCommentsData;
window.loadAuthorsPostsData = postsLogic.loadAuthorsPostsData;

import * as authorsLogic from './authorsLogic.js';
window.loadAuthorInfo = authorsLogic.loadAuthorInfo;
window.loadAuthorsListData = authorsLogic.loadAuthorsListData;