<?php

use Fuel\Core\Response;
use Fuel\Core\Cookie;
use Fuel\Core\HttpException;
use Fuel\Core\Security;
use Fuel\Core\ForbiddenHttpException;

class Controller_Login extends Controller
{

    public function before()
    {
        parent::before();
        
        $csrf_token = Security::fetch_token();
        $expiration_time = 60*60*24*30;
        Cookie::set('fuel_csrf_token', $csrf_token, $expiration_time);
    }

    /**
     * Summary of action_index
     * @return Response
     * 
     * ログイン処理を行うメソッド
     * ユーザーからのインプットを取得
     * ユーザー名を取得し、DBから一致するアカウントがあるか確認
     * パスワードを検証し、正しければ作成してリダイレクトする
     */
    public function action_index()
    {   
        //ユーザがフォームを送信した場合
        if(Input::method() == 'POST'){
            try {

                // CSRFトークンの検証
                $token = Input::post('fuel_csrf_token');
                $stored_token = Security::fetch_token();

                 // トークンの検証
                 if (!$token || $token !== $stored_token) {
                    throw new \SecurityException('Invalid CSRF token');
                }

                //ユーザーのインプットを取得
                $username = Input::post('username');
                $password = Input::post('password');

                //DBからユーザーを探す
                $query = DB::query("SELECT * FROM users WHERE username = :username")
                    ->parameters(array('username' => $username))
                    ->execute();

                //見つかった場合
                if(count($query) > 0){
                    $user = $query->current();
                    
                    //パスワードをハッシュと比較して検証する
                    if(password_verify($password,$user['password'])){
                        //セッションにユーザーの情報を保存する
                        Session::set('id', $user['id']);
                        Session::set('username', $user['username']); 
                        Session::set('logged_in', true);
                      
                        //ログイン成功したらweightページにリダイレクトする
                        Response::redirect('weight');
                    }else{
                        Session::set_flash('error','Invalid password');
                    }
                }else{
                    Session::set_flash('error', 'User not found');
                }
            } catch (\SecurityException $e) {
                Session::set_flash('error', 'セッションが切れました。もう一度お試しください。');
            }
        }
        //見つからなかった時ログインのviewを表示する
        return Response::forge(View::forge('entry/login'));
    }

    /**
     * Summary of action_logout
     * @return void
     * ログアウト処理を行うメソッド
     * セッションとクッキーを削除して、ログインページにリダイレクトする
     */
    public function action_logout()
    {   
        //セッション情報を削除する
        Session::delete('id');
        Session::delete('username');
        Session::delete('logged_in');
        Session::delete('fuel_csrf_token');
        Session::destroy();

        //セッションIDを保持するクッキーを削除
        Cookie::delete('fuelcid'); 

        //ログアウト後にログインページにリダイレクト
        Response::redirect('login');
    }
}
