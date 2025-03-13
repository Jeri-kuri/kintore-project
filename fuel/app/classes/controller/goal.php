<?php

class Controller_Goal extends Controller
{
    // // フォームを表示し、送信を処理
    public function action_add_goal()
    {
        $latest_weight = Model_GoalWeight::get_latest_weight();

         // 最新のゴールをデータベースから取得する
         $latest_goal = DB::select('goal')
         ->from('goal')
         ->order_by('date_inserted', 'desc')  //新しい順に並び替える
         ->limit(1)  
         ->execute()
         ->current();

        if (Input::method() == 'POST') {
            // 入力された情報を受け取る
            $goal = Input::post('goal');
            
            // データベースに保存する
            $result = DB::insert('goal')->set(array(
                'goal' => $goal,
            ))->execute();
            
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
