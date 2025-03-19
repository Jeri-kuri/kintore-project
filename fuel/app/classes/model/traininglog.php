<?php

/**
 * Summary of Model_Traininglog
 * トレーニングログに関連するデータを取得するためのモデルクラス
 */
class Model_Traininglog extends Model
{
    /**
     * Summary of get_workout_log_by_date
     * @param mixed $date
     * @return array トレーニングデータ（カテゴリー）を含む配列。該当するデータがなければ空の配列を返す。
     * 指定した日付のトレーニングデータを取得する
     */
    public static function get_workout_log_by_date($date)
    {
        return DB::select('category')
        ->from('exercise_type')
        ->where('created_at', '=', $date) //指定された日付のデータを絞り込み
        ->order_by('created_at', 'desc')  //作成された日の新しい順に並び変える
        ->limit(1)  
        ->execute()
        ->as_array();
    }


}
