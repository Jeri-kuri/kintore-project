<?php

class Model_GoalWeight extends Model
{
    public static function get_latest_goal()
    {
        return DB::select('goal')
            ->from('goal')
            ->order_by('date_inserted', 'desc')
            ->limit(1)
            ->execute()
            ->current();
    }

    public static function get_latest_weight()
    {
        return DB::select('weight')
            ->from('weight')
            ->order_by('created_date', 'desc')
            ->limit(1)
            ->execute()
            ->current();
    }
}
