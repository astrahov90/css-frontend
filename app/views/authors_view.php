<section class="section-body bg-light">
    <div class="container bg-light">
        <div class="loaderBody bg-light">
            <div id="loader"></div>
        </div>
        <label class="moreAuthors">Еще...</label>
    </div>
</section>

<script type="module">
    let moreAuthorsBtn = $(".moreAuthors");
    moreAuthorsBtn.hide();

    $(document).ready(function () {
        loadAuthorsListData();
    });

    moreAuthorsBtn.click(function () {
        loadAuthorsListData();
    });

</script>