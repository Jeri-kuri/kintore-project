<?php

class Controller_Training extends Controller
{
    //トレーニングのデータをDBに送信するための関数
    public function action_save()
    {
        if (Input::method() == 'POST') {
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
    
//トレーニングのデータを削除するための関数
    public function action_delete()
    {
        if(Input::method() == 'POST'){
            $date = Input::post('date');
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

    public function action_update()
    {
        if(Input::method() == "POST"){
            $trainings_detail = Input::post('trainings_detail');
            $training = Input::post('trainings');

            
            foreach($training as $trainingIndex => $sets){
                $date = $sets['date'];
                $category = $sets['category'];
                $exerciseName = $sets['name']; //not array


                $exerciseTypes = DB::select('exercise_id') //array
                ->from('exercise_type')
                ->where('created_at','=', $date)
                ->execute()
                ->as_array();


                DB::update('exercise_type')
                ->set(['name' => $exerciseName])
                ->where('exercise_id',"=", $exerciseTypes[$trainingIndex])
                ->execute();
                
            } 

           
            foreach($trainings_detail as $setIndex => $trainingData){
                $date = $trainingData['date'];
                $sets = $trainingData['sets'];

                //Fetch all of the workout logs on the given date
                $workoutLogs = DB::select('id')
                ->from('workout_log')
                ->where('created_at','=', $date)
                ->execute()
                ->as_array();


                //get the number of the updated and ensure there is the matching number of logs and sets
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
                Session::set_flash('success', 'トレーニングデータの更新が完了しました！');
                Response::redirect('weight');
                
            }

            }

        
    



