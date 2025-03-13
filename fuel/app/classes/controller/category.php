<?php


class Controller_Category extends Controller
{
    public function post_exercise()
    {
        $date = Input::post('date');

        if($date){
            $workout_logs = Model_Traininglog::get_workout_log_by_date($date);

            if(!empty($workout_logs)){
                $workout_logs = DB::select(
                'exercise_type.name',
                'exercise_type.category',
                'workout_log.weight',
                'workout_log.reps',
                'workout_log.created_at'
            )
            ->from('workout_log')
            ->join('exercise_type', 'INNER')
            ->on('workout_log.exercise_id', '=', 'exercise_type.exercise_id')
            ->where('workout_log.created_at', 'LIKE', $date . '%')
            ->order_by('workout_log.created_at', 'DESC')
            ->execute()
            ->as_array();

                return json_encode($workout_logs);
            }else{
                return json_encode([]);
            }
        }else{
            return json_encode(['error' => 'Invalid date']);
        }
        
    }
    
}
