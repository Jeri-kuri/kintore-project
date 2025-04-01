<!DOCTYPE html>
    <html>
        <head>
        <meta charset="UTF-8">
        <title>KIN TORE</title>
        <?php echo Asset::css('home.css'); ?>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    
        <!--自作JSファイル-->
        <?php echo Asset::js('calendar.js')?> <!--カレンダのjs-->
        <?php echo Asset::js('record.js')?> <!--記録記入欄のjs-->
        <?php echo Asset::js('training.js')?> <!--記録用のjs-->
    </head>
    <body>  
        <!--　ページのヘッダー　-->
        <div class="top">
            <h3>KIN TORE</h3>
            <a href="<?php echo Uri::create('login/logout'); ?>" class="logout-button">LOGOUT</a>
        </div>
        <hr class ="custom-divider">

        <!--ゴール記入欄-->
        <div class ="welcome">
            <h3>WELCOME <?php echo isset($username) ? htmlspecialchars(Session::get('username', 'USER')) : 'USER'; ?></h3>
        </div>

        <!--ユーザーが設定したゴールを記入、編集フォーム-->
        <div class="goal-container">
            <form action="<?php echo Uri::create('goal'); ?>" method="POST" class="goal-form">
                <input type="hidden" name="fuel_csrf_token" value="<?php echo Security::fetch_token(); ?>" />
                <h1>目標:</h1>
                <input type="text" class="input-box" name="goal" id="goal" 
                    value="<?php echo isset($latest_goal['goal']) ? $latest_goal['goal'] : ''; ?>" required>
                <button type="submit" class="input-button" id="edit-goal-button">
                <?php echo Asset::img('edit.png', ['alt' => 'Edit', 'style' => 'height: 40px; width: 40px;']); ?>
                </button>
            </form>
        </div>
        <!--カレンダーとトレーニングログ表示のセクション-->
        <div class = "header-wrapper">
            <!--カレンダー-->
            <div class ="header-container">
                <button id="prev" class="arrow-left">
                    <?php echo Asset::img('arrow.png', ['alt' => 'left arrow', 'style' => 'height: 30px; width: 30px;']); ?>
                </button> 
                    <p class="current-date">12月</p>
                <button id="next" class="arrow-right">
                    <?php echo Asset::img('right-arrow.png', ['alt' => 'right arrow', 'style' => 'height: 30px; width: 30px;']); ?>
                </button>
            </div>

            <!--登録した日のカウンターとビッグ３を表示する-->
            <div class = "counter-container">
                <p class= "counter"></p>
            </div>

            <!--体重表示-->
            <div class="weight-container">
                <p class = "CurWeight">体重：<?php echo isset($latest_weight['weight']) ? $latest_weight['weight'] : ''; ?>kg</p>
            </div>
             <!--BIG３合計表示-->
            <div class="big3-container">
                <p class = "CurBig3"> BIG 3</p>
            </div>
        </div> 

        <!--カレンダー表示-->
        <div class="calendar-log-container">
            <div class="wrapper">
                <div class="calendar">
                    <ul class="weeks">
                        <li>Sun</li>
                        <li>Mon</li>
                        <li>Tue</li>
                        <li>Wed</li>
                        <li>Thu</li>
                        <li>Fri</li>
                        <li>Sat</li>
                    </ul>
                    <ul class="days">
                    </ul>
                </div>   
            </div>

            <!--ログの記録を表示できる-->
            <div class="scrollable-container">
                <div class="log-data">
                    <h2 id="date-log"></h2>
                    <h2 id="training-log"></h2>
                </div>
            <!--ログ欄-->
                <div class= "menu-wrapper">
                    <h3 id ="exercise-name">筋トレ記録ログ欄</h3>
                    <h3 id="exercise-name" data-bind="text: exercise">日付を選択してください</h3>
                </div>
            <!--ボタン-->
                <button type="button" class="log_edit" >編集</button>
                <button type="button" class="log_delete" >削除</button>
            </div>
        </div>  


        <?php
        $deleteImg = Asset::get_file('x-mark.png', 'img');
        $addImg = Asset::get_file('add.png', 'img');
        ?>

        <!--部位の選択-->
        <div class="nav-bar">
            <button class="nav-button" data-training="胸">胸</button>
            <button class="nav-button" data-training="肩">肩</button>
            <button class="nav-button" data-training="背中">背中</button>
            <button class="nav-button" data-training="足">足</button>
            <button class="nav-button" data-training="腕">腕</button>
        </div>

        <!-- 画像のURLをデータ属性に格納する-->
        <div class="input-container"
            data-delete-img="<?= htmlspecialchars($deleteImg, ENT_QUOTES, 'UTF-8') ?>"
            data-add-img="<?= htmlspecialchars($addImg, ENT_QUOTES, 'UTF-8') ?>">
            <div class= "training-container" >
                <div class="form-container">
                    <!--種目のインプットフォーム-->
                    <form id="training-form" action="<?php echo Uri::create('training/save'); ?>" method="POST">
                        <input type="hidden" id="selected-training" name="trainings[0][training_type]">
                            <div class="training-name-wrapper">
                                <div class="training-name-container">
                                <input type="text" id="name" name="trainings[0][name]" placeholder="種目" required>
                                </div>
                                <button type="delete" class="deletion">
                                    <?php echo Asset::img('x-mark.png', ['alt' => 'x-mark', 'style' => 'height: 40px; width: 40px;']); ?>
                                </button>
                            </div>
                        <!--回数と重量のインプットフォーム-->
                        <div class="weight-rep-wrapper">
                            <div class="training-details">
                                <div class="sets">
                                    <span class="set-num">1</span>
                                    <input type="number" id="weight" name="trainings_detail[0][sets][0][weight]" placeholder="重量" > 
                                    <span class="separator">&nbsp;x&nbsp;</span>
                                    <input type="number" id="reps" name="trainings_detail[0][sets][0][reps]" placeholder="レップ数" > 
                                </div>
                            </div> 
                            <button type="add-set" class ="add-rep">
                                <?php echo Asset::img('add.png', ['alt' => 'add', 'style' => 'height: 40px; width: 40px;']); ?>
                            </button>
                        </div> 
                
                        <button type="button" class="add-set">追加</button>
                        <button type="submit" class="submit-button">完了</button>
                    </form>
                </div>
            </div>
            <!--体重のインプット欄-->
            <div class="weight-input">
                <form action="<?php echo Uri::create('weight/add_weight'); ?>" method="POST" class="weight-form" id=weight-form>
                    <div class = weight-input-container>
                            <label for="weight"> 体  重</label>
                    </div>
                    <input type="number" class="input-box" name="user_weight" id="user-weight" 
                        value="<?php echo isset($latest_weight['user_weight']) ? $latest_weight['user_weight'] : ''; ?>" required>
                    <button type="submit" class="submit-weight-button" id="submit-weight-button"> 完了</button>
                </form>
            </div>
        </div>    
    </body>
</html>

