<!DOCTYPE html>
  <html>
    <head>
    <meta charset="UTF-8">
    <title>Signin</title>
    <?php echo Asset::css('login-layout.css'); ?>
  </head>
  <body>
    <!--タイトルとサブタイトル-->
    <div class="title-container">
      <h1 class="main-title">KIN TORE</h1>
      <h2 class="subtitle">WELCOME NEW TRAINEE</h2>
      <header>新規会員登録</header>
    </div>  
    <!--会員登録フォームのコンテナ-->
    <div class="container">
      <input type="checkbox" id="check">
      <div class="login form">
        <!--サインインフォーム-->
        <form action="<?php echo Uri::create('signin/create'); ?>" method="POST">
          <h3>ユーザーネーム</h3>
          <input type="text" name="username" required>
          <h3>パスワード</h3>
          <input type="password" name="password" required>
          <input type="submit" class="button" value="登録">
        </form>
      </div> 
    </div>
    <!--ログインページへのリンク-->
    <div class="signup">
        <span class="signup">
         <a for="check" href="<?php echo Uri::create('login'); ?>">ログインはこちら</a>
        </span>
    </div>
  </body>
</html>