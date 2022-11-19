<section class="section-body bg-light">
    <div class="container bg-light">
        <div class="loaderBody bg-light">
            <div id="loader"></div>
        </div>
        <label class="moreAuthors">Еще...</label>
    </div>
</section>

<script src="/assets/js/authors.js"></script>
<script>
    let moreAuthorsBtn = $(".moreAuthors");
    moreAuthorsBtn.hide();

    $(document).ready(function () {
        loadUsers();
    });

    moreAuthorsBtn.click(function () {
        loadUsers();
    });

</script>