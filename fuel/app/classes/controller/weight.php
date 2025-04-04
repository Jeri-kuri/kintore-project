<?php
use Fuel\Core\Response;
use Fuel\Core\Session;
use Fuel\Core\Input;
use Fuel\Core\View;
use Fuel\Core\DB;


class Controller_Weight extends Controller
{
    /**
     * Summary of action_add_weight
     * @return View
     * 体重の追加フォームを表示し、送信を処理する
     */
    public function action_add_weight()
    {   
        
         // 最新のゴールをデータベースから取得する
         $latest_weight = DB::select('weight')
         ->from('weight')
         ->order_by('created_date', 'desc')  //新しい順に並び替える
         ->limit(1)  
         ->execute()
         ->current();

        //フォームが送信された時の処理
        if (Input::method() == 'POST') {

            // 入力された情報を受け取る
            $weight = Input::post('user_weight');
            
            // データベースに保存する
            $result = DB::insert('weight')->set(array(
                'weight' => $weight,
                'created_date' => date('Y-m-d H:i:s'),
            ))->execute();
            
            //成功した時のメッセージ
            if ($result) {
                Session::set_flash('success', 'Weight added successfully.');
            } else {
                Session::set_flash('error', 'There was an error adding the weight.');
            }
            
            // フォーム送信後にリダイレクトする
            Response::redirect('weight');
        }
        //modelを使ってデーターを取得する
        $latest_goal = Model_GoalWeight::get_latest_goal();
        $latest_weight = Model_GoalWeight::get_latest_weight();


       // 最新のゴールをviewにパスする
       $view = View::forge('goal/index', [
        'latest_weight' => $latest_weight,
        'latest_goal' => $latest_goal
    ]);
       return $view;
    }
}
