<?php


class Controller_Category extends Controller
{
    public function post_exercise()
    {
        /**
         * @var mixed
         * 指定された日付のワークアウトを取得して、JSONで返す
         */

        //フロントエンドから送信された日付を取得
        $date = Input::post('date');

        //日付が指定されてるのか確認する
        if($date){

            //modelから指定日付のワークアウトログを取得
            $workout_logs = Model_Traininglog::get_workout_log_by_date($date);

            //ワークアウトろが存在している場合に、詳細情報を取得する
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
            ->on('workout_log.exercise_id', '=', 'exercise_type.exercise_id') //エクササイズの情報と結合
            ->where('workout_log.created_at', 'LIKE', $date . '%') //指定データを取得
            ->order_by('workout_log.created_at', 'DESC')//新しい順に並べる
            ->execute()
            ->as_array();

                //取得したデータをJSON形式で返す
                return json_encode($workout_logs);
            }else{
                //存在しない場合はからの配列を返す
                return json_encode([]);
            }
        }else{
            return json_encode(['error' => 'Invalid date']);
        }
        
    }
    
}
