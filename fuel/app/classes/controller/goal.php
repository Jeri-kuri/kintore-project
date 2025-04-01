<?php

class Controller_Goal extends Controller
{
    /**
     * Summary of action_add_goal
     * ユーザーのゴールを追加する処理
     * 最新の体重とゴールを取得し、新しいゴールをDBに保存する
     */
    public function action_add_goal()
    {   
       // if (!Session::get('fuel_csrf_token')) {
         //   Session::set('fuel_csrf_token', Security::generate_token());
        //}

        if (!Session::instance()) {
            echo "Session is not initialized.";
        } else {
            echo "Session is active.";
        }

        $csrf_token = Session::get('fuel_csrf_token');
if ($csrf_token !== null) {
    echo "CSRF Token exists: " . htmlspecialchars($csrf_token);
} else {
    echo "CSRF Token is missing.";
}   
        
        if (Session::get('fuel_csrf_token') !== null) {
            echo "Session is active, and CSRF token exists.";
        } else {
            echo "Session might not be started, or CSRF token is missing.";
            var_dump(Session::get('fuel_csrf_token')); // Check only the CSRF token
        }

        //最新のデータを取得
        $latest_weight = Model_GoalWeight::get_latest_weight();

         // 最新のゴールをデータベースから取得する
         $latest_goal = DB::select('goal')
         ->from('goal')
         ->order_by('date_inserted', 'desc')  //新しい順に並び替える
         ->limit(1)  //最新の一件のみ取得
         ->execute()
         ->current();

         //フォームが送信された時の処理
        if (Input::method() == 'POST') {

            // 入力された情報を受け取る
            $goal = Input::post('goal');
            
            // データベースに保存する
            $result = DB::insert('goal')->set(array(
                'goal' => $goal,
            ))->execute();
            
            //挿入成功と失敗時のメッセージ
            if ($result) {
                Session::set_flash('success', 'Goal added successfully.');
            } else {
                Session::set_flash('error', 'There was an error adding the goal.');
            }
            
            // フォーム送信後にリダイレクトする
            Response::redirect('goal');
        }

       // 最新のゴールをviewにパスする
       $view = View::forge('goal/index', ['latest_goal' => $latest_goal,'latest_weight' => $latest_weight ,]);
       return $view;
    }
}
