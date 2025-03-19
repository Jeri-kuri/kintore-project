<?php

use Fuel\Core\Response;

class Controller_Signin extends Controller
{
    /**
     * Summary of action_index
     * @return Response
     * 
     * サインイン画面を表示するためのメソッド
     * ユーザーが新規登録の時に呼ばれる
     */
    public function action_index()
    {
        return Response::forge(View::forge('entry/signin'));
    }

    /**
     * Summary of action_create
     * @return Response
     * 新しいユーザーを作成するためのメソッド
     * フォーム送信があった場合に実行ユーザー名の重複を確認する
     * パスワードをハッシュ化してDBに保存する
     * 登録成功時にログイン画面にリダイレクト
     */
    public function action_create()
    {
        if(Input::method() == 'POST'){
            //ユーザーのインプットを取得
            $username = Input::post('username');
            $password = Input::post('password');

            //userをDBから取得
            $existing_user = DB::select()
            ->from('users')
            ->where('username', '=', $username)
            ->execute()
            ->as_array();

            //パスワードをハッシュする
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            //DBにデータを入れる
            $result = DB::insert('users')
             ->set(array(
                 'username' => $username,
                 'password' => $hashed_password,
            ))
            ->execute();

            if($result){
                Response::redirect('login');
            }else{
                return Response::forge('Registration failed', 500);
        }
           
        }
    }
}
