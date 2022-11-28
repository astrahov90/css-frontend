<header class="bg-light">
    <a id="logo" href="/">
        <img width="40px" height="32px" src="/assets/img/logo.svg">
    </a>
    <div class="profile-menu">
        <?php if (isset($_SESSION['isAuthorized']) && $_SESSION['isAuthorized']): ?>
            <a class="dropdown-toggle" href="#"><?php echo $_SESSION['userName'] ?></a>
            <div class="flex-column d-flex dropdown-menu">
                <div><a href="/profile/">Профиль</a></div>
                <div>
                    <form method="post" action="/login/logout/">
                        <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">
                        <button type="submit" class="logout-link asText">Выйти</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <a class="login-link clear-link" href="/login/">Войти</a>
        <?php endif ?>
    </div>
    <nav>
        <ul>
            <li <?php if (isset($best)) echo 'class="active"' ?>>
                <a href="/?best">Лучшее</a>
            </li>
            <li <?php if (isset($newest)) echo 'class="active"' ?>>
                <a href="/?newest">Свежее</a>
            </li>
            <li <?php if (isset($authors)) echo 'class="active"' ?>>
                <a href="/authors/">Авторы</a>
            </li>
            <?php if (isset($_SESSION['isAuthorized']) && $_SESSION['isAuthorized']): ?>
                <li <?php if (isset($newPost)) echo 'class="active"' ?> >
                    <a href="/?newPost">Новый пост</a>
                </li>
            <?php endif ?>
        </ul>
    </nav>

    <div onclick="topFunction()" id="toTopBtn">
        <img width="20px" height="20px" src="/assets/img/up-arrow.svg" alt="В начало">
    </div>
</header>
