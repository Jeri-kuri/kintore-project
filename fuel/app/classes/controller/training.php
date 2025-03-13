<?php

class Controller_Training extends Controller
{
    public function action_save()
    {
        if (Input::method() == 'POST') {
            $trainings = Input::post('trainings');
            $trainings_detail = Input::post('trainings_detail');

            
            if(!empty($trainings) && is_array($trainings)){
                foreach($trainings as $index => $training){
                    if(!empty($training['name'])) {
                        list($exercise_id,) = DB::insert('exercise_type')->set(array(
                            'category' => $training['training_type'] ?? null,
                            'name' => $training['name'],
                            'created_at' => date('Y-m-d H:i:s')
                        ))->execute();

                        if(!empty($trainings_detail[$index]['sets']) && is_array($trainings_detail[$index]['sets'])){
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

    public function action_edit(){
        if (Input::method() == 'POST') {

            Log::debug("Received POST Data: " . json_encode(Input::post()));

            $exercise_id = Input::post('exercise_id'); 
            $exercise_name = Input::post('exercise_name');
            $category = Input::post('category');
            $workout_details = Input::post('workout_details'); 


           
            Log::debug("Searching for Exercise ID: " . $exercise_id);
    
            
            $exercise = DB::select()
                ->from('exercise_type')
                ->where('exercise_id', '=', $exercise_id)
                ->execute()
                ->current();
    
            if (!$exercise) {
                return json_encode(['status' => 'error', 'message' => 'Exercise not found']);
            }
    
           
            DB::update('exercise_type')
                ->set([
                    'name' => $exercise_name,
                    'category' => $category
                ])
                ->where('exercise_id', '=', $exercise_id)
                ->execute();
    
           
            if (!empty($workout_details) && is_array($workout_details)) {
                foreach ($workout_details as $workout) {
                    if (!empty($workout['id'])) { 
                        DB::update('workout_log')
                            ->set([
                                'weight' => $workout['weight'],
                                'reps' => $workout['reps']
                            ])
                            ->where('id', '=', $workout['id'])
                            ->execute();
                    }
                }
            }
    
            return json_encode(['status' => 'success', 'message' => 'Workout updated successfully']);
        }
    
        return json_encode(['status' => 'error', 'message' => 'Invalid request']);
    
}
}
