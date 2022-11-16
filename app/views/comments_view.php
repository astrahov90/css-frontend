<script src="/assets/js/posts.js"></script>

<section class="section-body bg-light">
    <div class="container bg-light">
        <div class='row post'>
            <!-- load post info -->
        </div>
        <div class="loaderBody bg-light">
            <div id="loader"></div>
        </div>
        <div class='clearfix'></div>
        <div class="row mt-2 hasComments">
            <div class="col-12 fw-bold">Комментарии пользователей:</div>
        </div>
        <label class="moreComments">Еще...</label>
        <?php if (!$_SESSION['isAuthorized']): ?>
            <span>Для добавления комментария необходимо </span><a class="login-link" href="/login/">авторизоваться</a>
        <?php else: ?>
            <div class="mt-2">
                <form method="post" action="/comments/addCommentToPost">
                    <input type="hidden" name="id" value="<?php echo $post['id'] ?>">
                    <label for="comment" class="form-label">Добавить комментарий</label>
                    <div class="clearfix"></div>
                    <div class="btn-group mt-2 mb-2" role="group">
                        <button type="button" class="btn btn-outline-primary bbcode" id="text-bold" title="Полужирный">
                            <span style="font-weight: bold">B</span></button>
                        <button type="button" class="btn btn-outline-primary bbcode" id="text-italic" title="Курсив">
                            <span style="font-style: italic">I</span></button>
                        <button type="button" class="btn btn-outline-primary bbcode" id="text-underline"
                                title="Подчеркнутый"><span style="text-decoration: underline">U</span></button>
                        <button type="button" class="btn btn-outline-primary bbcode" id="text-line-through"
                                title="Зачеркнутый"><span style="text-decoration: line-through">S</span></button>
                        <button type="button" class="btn btn-outline-primary bbcode" id="text-quote"
                                title="Цитирование"><span style="font-weight: bold">""</span></button>
                        <button type="button" class="btn btn-outline-primary bbcode" id="text-url" title="Гиперссылка">
                            url
                        </button>
                        <button type="button" class="btn btn-outline-primary bbcode" id="text-img" title="Изображение">
                            img
                        </button>
                        <button type="button" class="btn btn-outline-primary bbcode" id="text-color"
                                title="Цвет текста">color
                        </button>
                        <input type="color" class="btn btn-outline-primary" id="text-color-select" title="Цвет">
                    </div>
                    <textarea class="form-control" name="comment" id="comment" placeholder="Текст комментария"
                              required></textarea>
                    <div class="clearfix"></div>
                    <button class="btn btn-outline-primary mt-2" type="submit">Отправить</button>
                    <button class="btn btn-outline-primary mt-2" type="button" id="preview">Предварительный просмотр
                    </button>
                </form>
                <div id="preview-data">
                    <label for="preview-data">Предпросмотр комментария</label>
                    <pre>Предпросмотр</pre>
                </div>
            </div>
        <?php endif ?>
    </div>
</section>

<script>loadCSS("/assets/css/posts.css")</script>
<script>
    let postId = "<?php echo $post['id'] ?>";

    let moreCommentsBtn = $(".moreComments");
    moreCommentsBtn.hide();

    let hasComments = $(".hasComments");
    hasComments.hide();

    let previewData = $("#preview-data");
    previewData.hide();

    let commentField = $("#comment");

    $(".card-read-more-button").click(function (e) {
        if ($("#" + $(this).attr("for")).is(":not(:checked)")) {
            scrollIntoViewIfNeeded($(e.target));
        }
    });

    $("#preview").click(function () {
        previewData.show();
        previewData.find('pre').html(bbCodeDecode($("#comment").val()));
    });

    $(document).ready(function () {
        getPostInfo(postId);
        getPostComments(postId);
    });

    moreCommentsBtn.click(function () {
        getPostComments(postId);
    });

    $(".bbcode").click(function () {
        let curText = commentField.val();

        let curSelectionStart = commentField.prop('selectionStart');
        let curSelectionEnd = commentField.prop('selectionEnd');

        let tag;
        let url;
        let color;

        switch ($(this).attr('id')) {
            case "text-bold":
                tag = 'b';
                break;
            case "text-italic":
                tag = 'i';
                break;
            case "text-underline":
                tag = 'u';
                break;
            case "text-line-through":
                tag = 's';
                break;
            case "text-quote":
                tag = 'quote';
                break;
            case "text-url":
                tag = 'url';
                break;
            case "text-img":
                tag = 'img';
                break;
            case "text-color":
                tag = 'color';
                break;
        }

        if (tag === "url") {
            url = prompt("Введите url");
            if (!url) {
                return false;
            }
        }

        if (tag === "img") {
            url = prompt("Введите ссылку на изображение");
            if (!url) {
                return false;
            }
            curSelectionStart = curSelectionEnd;
        }

        if (tag === "color") {
            color = $("#text-color-select").val();
        }

        let curSelection = "[" + tag + (curSelectionStart !== curSelectionEnd && url ? "=" + url : "") + (color ? "='" + color + "'" : "") + "]" + (curSelectionStart === curSelectionEnd && url ? url : curText.slice(curSelectionStart, curSelectionEnd)) + "[/" + tag + "]";

        curText = curText.slice(0, curSelectionStart) + curSelection + curText.slice(curSelectionEnd);

        $("#comment").val(curText);
    });

</script>