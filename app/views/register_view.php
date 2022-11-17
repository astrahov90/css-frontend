<section class="section-body bg-light section-middle">
    <div class="container bg-light">
        <form method="post" id="register-form" enctype="multipart/form-data">
            <h2>Регистрация нового пользователя</h2>
            <label for="login" class="form-label">Логин</label>
            <input class="form-control" name="username" type="text" id="username" placeholder="Введите логин" required>
            <div class="clearfix"></div>
            <label for="password" class="form-label">Пароль</label>
            <input class="form-control" name="password" type="password" id="password" placeholder="Введите пароль"
                   required>
            <div class="clearfix"></div>
            <label for="password" class="form-label">Пароль еще раз</label>
            <input class="form-control" name="password_again" type="password" id="password_again"
                   placeholder="Повторите пароль" required>
            <div class="clearfix"></div>
            <label for="name" class="form-label">Электронная почта</label>
            <input class="form-control" name="email" type="email" id="email" placeholder="Введите электронную почту"
                   required>
            <div class="clearfix"></div>
            <label for="description" class="form-label">О себе</label>
            <input class="form-control" name="description" type="text" id="description" placeholder="Кратко о себе"
                   required>
            <div class="clearfix"></div>
            <label for="avatar" class="form-label">Аватар</label>
            <input class="form-control" type="file" id="formFile" name="avatar" accept="image/jpeg, image/png">
            <div class="clearfix"></div>
            <div class="btn-group">
                <button type="submit" class='btn btn-primary mt-2'>Зарегистрироваться</button>
            </div>
            <?php if ($error): ?>
                <span class="error-msg"><?php echo $error ?></span>
            <?php endif ?>
        </form>
    </div>

</section>

<script>
    $("#username").keydown(function (e) {
        if (!e.key.match(/\w/gi)) {
            return false;
        }
    });

    $("#password,#password_again").keydown(function (e) {
        if (!e.key.match(/\w/gi)) {
            return false;
        }
    });

    $("#register-form").validate({
        rules: {
            email: {
                email: true, required: true
            },
            password: {required: true},
            username: {required: true},
            description: {required: true},
            password_again: {
                required: true,
                equalTo: "#password"
            }
        },
        messages: {
            email: "Введите электронную почту пользователя",
            username: "Введите имя пользователя",
            description: "Кратко опишите себя",
            password: "Введите пароль",
            password_again: {
                required: "Повторите пароль",
                equalTo: "Пароли должны совпадать"
            }
        },
        submitHandler: function (form) {
            if (form.valid())
                return false;
            form.submit();
        }
    });
</script>