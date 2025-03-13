<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <?php echo Asset::css('login-layout.css'); ?>
</head>
<body>
     
    <div class="title-container">
        <h1 class="main-title">KIN TORE</h1>
        <h2 class="subtitle">WELCOME BACK TRAINEE</h2>
        <header>ログイン</header>
    </div>  

    <div class="container">
        
    <input type="checkbox" id="check">
    <div class="login form">
    <form action="<?php echo Uri::create('login/index'); ?>" method="POST">
        <h3>ユーザーネーム</h3>
        <input type="text" name="username" required>
        <h3>パスワード</h3>
        <input type="password" name="password" required>
        <input type="submit" class="button" value="ログイン">
      </form>
      
    </div>
    
  </div>
    <div class="signup">
        <span class="signup">
            <a for="check" href="<?php echo Uri::create('signin'); ?>">新規会員登録はこちら</a>
        </span>
    </div>
  

</body>
</html>