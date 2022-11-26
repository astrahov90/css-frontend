<section class="section-body bg-light">
    <div class="container bg-light">
        <div class='row'>
            <div class='col-1'>
                <div class='container'>
                    <img class='avatar' src="<?php echo $author['iconPath'] ?>" alt="Аватар автора">
                </div>
            </div>
            <div class='col-10'>
                <div class='container'>
                    <div class='row'>
                        <div class='card'>
                            <div class='card-title'>
                                <div class='container-fluid'>
                                    <div class='row'>
                                        <div class='col-4 fw-bold'><?php echo $author['username'] ?></div>
                                        <div class='col-3 offset-5'>Дата
                                            регистрации: <?php echo $author['created_at'] ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class='card-body'>
                                <p><?php echo $author['description'] ?></p>
                                <form method="post" enctype="multipart/form-data">
                                    <div class="mt-1">
                                        <label for="formFile" class="form-label">Изменить аватар</label>
                                        <div class='clearfix'></div>
                                        <input class="form-control" type="file" id="formFile" name="avatar"
                                               accept="image/jpeg, image/png">
                                        <div class='clearfix'></div>
                                        <button type="submit" class="btn btn-primary mt-1">Сохранить</button>
                                    </div>
                                </form>
                                <div class='card-bottom'></div>
                            </div>
                        </div>
                        <div class='clearfix'></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
