<?php

class Model_Traininglog extends Model
{
    public static function get_workout_log_by_date($date)
    {

        return DB::select('category')
        ->from('exercise_type')
        ->where('created_at', '=', $date) 
        ->order_by('created_at', 'desc')  
        ->limit(1)  
        ->execute()
        ->as_array();
    }


}
