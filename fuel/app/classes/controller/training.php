<?php

class Controller_Training extends Controller
{
    /**
     * Summary of action_save
     * @return void
     * 
     * トレーニングのデータをDBに保存する
     * ユーザーがトレーニングを入力して送信すると実行される
     * 'exercise_type'と'exercise_log'テーブルに情報を追加
     */
    public function action_save()
    {
        if (Input::method() == 'POST') {


            //フロントエンドからインプットを取得する
            $trainings = Input::post('trainings');
            $trainings_detail = Input::post('trainings_detail');

            
            if(!empty($trainings) && is_array($trainings)){
                //各トレーニングデーターを処理
                foreach($trainings as $index => $training){
                    if(!empty($training['name'])) {
                        //'exercise_type'テーブルに新しい種目を挿入する
                        list($exercise_id,) = DB::insert('exercise_type')->set(array(
                            'category' => $training['training_type'] ?? null,
                            'name' => $training['name'],
                            'created_at' => date('Y-m-d H:i:s')
                        ))->execute();


                            //セットの情報をDBに挿入
                        if(!empty($trainings_detail[$index]['sets']) && is_array($trainings_detail[$index]['sets'])){
                            //各セットのデータを'workout_log'テーブルに挿入する
                            foreach($trainings_detail[$index]['sets'] as $detail){
                                    DB::insert('workout_log')->set(array(
                                        'exercise_id' => $exercise_id,
                                        'weight' => $detail['weight'] ?? 0,
                                        'reps' => $detail['reps'] ?? 0,
                                        'created_at' => date('Y-m-d H:i:s')
                                    ))->execute();
                                
                            }
                        }
                    }
                }
            }
            // フォーム送信後にリダイレクトする
            Response::redirect('weight');
        }
    } 
    
    /**
     * Summary of action_delete
     * @return bool|string
     * 指定された日付のトレーニングデータを削除する
     * 該当する'workout_log'と'exercise_type'のデータを削除
     */
    public function action_delete()
    {
        if(Input::method() == 'POST'){

          

            $date = Input::post('date');

            //指定された日付のトレーニングデータを取得
            $workouts = DB::select('id')
            ->from('workout_log')
            ->where('created_at', 'LIKE', "$date%")
            ->execute()
            ->as_array();

            $exercisetype = DB::select('exercise_id')
            ->from('exercise_type')
            ->where('created_at', 'LIKE', "$date%")
            ->execute()
            ->as_array();



            if(!empty($workouts) && !empty($exercisetype)){
                //種目データを削除する
                foreach($exercisetype as $exercise){
                    DB::delete('exercise_type')
                    ->where('exercise_id','=',$exercise['exercise_id'])
                    ->execute();
                }
                
                 return json_encode(['status' => 'success']);
            }else{
                return json_encode(['status' => 'error', 'message' => 'workout not found']);
            }
        }
        return json_encode(['status' => 'error', 'message' => 'Invalid request']);
    }

    /**
     * Summary of action_update
     * @return void
     * トレーニングデータを更新する
     * ユーザーが入力した最新のデータをデータベースに反映
     */
    public function action_update()
    {
        if(Input::method() == "POST"){

            //更新モードのログ欄からのユーザーインプット
            $trainings_detail = Input::post('trainings_detail');
            $training = Input::post('trainings');

            //トレーニング種目の更新
            foreach($training as $trainingIndex => $sets){
                $date = $sets['date'];
                $category = $sets['category'];
                $exerciseName = $sets['name']; //一つの種目

                //指定された日付の情報を取得
                $exerciseTypes = DB::select('exercise_id') //array
                ->from('exercise_type')
                ->where('created_at','=', $date)
                ->execute()
                ->as_array();

                //種目名を更新
                DB::update('exercise_type')
                ->set(['name' => $exerciseName])
                ->where('exercise_id',"=", $exerciseTypes[$trainingIndex])
                ->execute();
                
            } 

           //セット情報の更新
            foreach($trainings_detail as $setIndex => $trainingData){
                $date = $trainingData['date'];
                $sets = $trainingData['sets'];

                //指定された日付のデータを取得
                $workoutLogs = DB::select('id')
                ->from('workout_log')
                ->where('created_at','=', $date)
                ->execute()
                ->as_array();


                //セット数とログの数を比較して範囲を超えないようにする
                $logCount = count($workoutLogs);
                $setCount = count($sets);
                $minCount = min($logCount, $setCount); //avoiding out-of-bounds issues


                for($i = 0; $i < $minCount; $i++){
                    $workoutLog = $workoutLogs[$i];
                    $set = $sets[$i];

                    DB::update('workout_log')
                    ->set([
                       'weight' => $set['weight'],
                       'reps' =>  $set['reps']
                   ])
                   ->where('id', '=', $workoutLog['id'])
                   ->execute();
                }
            }

                    
        }
        //更新完了メッセージ
        Session::set_flash('success', 'トレーニングデータの更新が完了しました！');
        Response::redirect('weight');
    
    }

}

        
    



