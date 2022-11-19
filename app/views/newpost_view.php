<script src="/assets/js/posts.js"></script>

<section class="section-body bg-light">
    <div class="container bg-light">
        <div class="mt-2">
            <form method="post" action="/posts/addPost">
                <label for="post" class="form-title">Добавить новый пост</label>
                <div class="clearfix"></div>
                <input type="text" class="form-control" name="title" placeholder="Введите заголовок поста">
                <div class="btn-group mt-2 mb-2" role="group">
                    <button type="button" class="btn btn-outline-primary bbcode" id="text-bold" title="Полужирный"><span
                                style="font-weight: bold">B</span></button>
                    <button type="button" class="btn btn-outline-primary bbcode" id="text-italic" title="Курсив"><span
                                style="font-style: italic">I</span></button>
                    <button type="button" class="btn btn-outline-primary bbcode" id="text-underline"
                            title="Подчеркнутый"><span style="text-decoration: underline">U</span></button>
                    <button type="button" class="btn btn-outline-primary bbcode" id="text-line-through"
                            title="Зачеркнутый"><span style="text-decoration: line-through">S</span></button>
                    <button type="button" class="btn btn-outline-primary bbcode" id="text-quote" title="Цитирование">
                        <span style="font-weight: bold">""</span></button>
                    <button type="button" class="btn btn-outline-primary bbcode" id="text-url" title="Гиперссылка">url
                    </button>
                    <button type="button" class="btn btn-outline-primary bbcode" id="text-img" title="Изображение">img
                    </button>
                    <button type="button" class="btn btn-outline-primary bbcode" id="text-color" title="Цвет текста">
                        color
                    </button>
                    <input type="color" class="btn btn-outline-primary" id="text-color-select" title="Цвет">
                </div>
                <textarea class="form-control" name="body" id="post" placeholder="Текст поста" required></textarea>
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
    </div>
</section>

<script>
    let previewData = $("#preview-data");
    previewData.hide();

    let postField = $("#post");

    $("#preview").click(function () {
        previewData.show();
        previewData.find('pre').html(bbCodeDecode(postField.val()));
    });

    $(".bbcode").click(function () {
        let curText = postField.val();

        let curSelectionStart = postField.prop('selectionStart');
        let curSelectionEnd = postField.prop('selectionEnd');

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

        $("#post").val(curText);
    });

</script>