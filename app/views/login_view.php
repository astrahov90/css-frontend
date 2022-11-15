<section class="section-body bg-light section-middle">
    <div class="container bg-light">
        <form method="post" id="login-form">
            <label for="login" class="form-label">Логин</label>
            <input class="form-control" name="username" type="text" id="username" placeholder="Введите логин" required>
            <div class="clearfix"></div>
            <label for="password" class="form-label">Пароль</label>
            <input class="form-control" name="password" type="password" id="password" placeholder="Введите пароль" required>
            <div class="clearfix"></div>
            <div class="btn-group">
                <button class='btn btn-primary mt-2' type="submit">Войти</button>
                <a href="/login/register/" class='btn btn-primary mt-2'>Зарегистрироваться</a>
            </div>
            <?php if ($error): ?>
                <span class="error-msg"><?php echo $error ?></span>
            <?php endif ?>
        </form>
    </div>

</section>

<script>
    $("#username").keydown(function (e) {
        if (!e.key.match(/\w/gi))
        {
            return false;
        }
    });

    $("#password").keydown(function (e) {
        if (!e.key.match(/\w/gi))
        {
            return false;
        }
    });
</script>