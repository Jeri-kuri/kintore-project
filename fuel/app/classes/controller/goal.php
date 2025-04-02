<?php

class Controller_Goal extends Controller
{
    public function before()
    {
        parent::before();
        
        $csrf_token = Security::fetch_token();
        $expiration_time = 60*60*24*30;
        Cookie::set('fuel_csrf_token', $csrf_token, $expiration_time);
    }

    /**
     * Summary of action_add_goal
     * ユーザーのゴールを追加する処理
     * 最新の体重とゴールを取得し、新しいゴールをDBに保存する
     */
    public function action_add_goal()
    {   
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
            try {
                // CSRFトークンの検証
                $token = Input::post('fuel_csrf_token');
                $stored_token = Security::fetch_token();
                

                // トークンの検証
                if (!$token || $token !== $stored_token) {
                    throw new \SecurityException('Invalid CSRF token');
                }

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
            } catch (\SecurityException $e) {
                // CSRF validation failed
                Log::error('CSRF Validation Failed: ' . $e->getMessage());
                Session::set_flash('error', 'セッションが切れました。もう一度お試しください。');
                Response::redirect('goal');
            } catch (\Exception $e) {
                // その他のエラー
                Log::error('Goal Addition Failed: ' . $e->getMessage());
                Session::set_flash('error', 'エラーが発生しました。もう一度お試しください。');
                Response::redirect('goal');
            }
        }

        // 最新のゴールをviewにパスする
        $view = View::forge('goal/index', ['latest_goal' => $latest_goal,'latest_weight' => $latest_weight ,]);
        return $view;
    }
}
