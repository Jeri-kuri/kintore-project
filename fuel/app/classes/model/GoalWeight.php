<?php

/**
 * Summary of Model_GoalWeight
 * ゴールと体重データを取得するためのモデルクラス
 */
class Model_GoalWeight extends Model
{
    /**
     * Summary of get_latest_goal
     * データベースから最新のゴールを取得する
     * @return array|null 最新のゴールデータを配列で返す。データが存在しない場合は null を返す。
     */
    public static function get_latest_goal()
    {
        return DB::select('goal')
            ->from('goal')
            ->order_by('date_inserted', 'desc')//挿入日時が新しい順にする
            ->limit(1)//最新の一件のみ取得
            ->execute()
            ->current();
    }

    /**
     * Summary of get_latest_weight
     * データベースから最新の体重を取得する
     * @return array|null 最新の体重のデータを配列で返す。データが存在しない場合は null を返す。
     */
    public static function get_latest_weight()
    {
        return DB::select('weight')
            ->from('weight')
            ->order_by('created_date', 'desc')//挿入日時が新しい順にする
            ->limit(1)//最新の一件のみ取得
            ->execute()
            ->current();
    }
}
