<?php

class Controller_Counter extends Controller{
 public function post_Counter()
{
    $year = Input::post('year');
    $month = Input::post('month');

    //記録のある日を取得
    $result = DB::select(DB::expr('DISTINCT DATE(created_at) as date'))
            ->from('exercise_type')
            ->where(DB::expr('YEAR(created_at)'), '=', $year)
            ->and_where(DB::expr('MONTH(created_at)'), '=', $month)
            ->distinct(true)
            ->execute()
            ->as_array();

    //記録のある日を数える
    $uniqueDaysCount = count($result);
    return json_encode(['count' => $uniqueDaysCount, 'entries' => $result]);
} 

public function post_Big3max()
{
    try {
        // ユーザーによって呼び方が違うため、インプットを予測する
        $benchKeywords = ['ベンチプレス', 'ベンチ', 'benchpress', 'bp'];
        $squatKeywords = ['スクワット', 'squat', 'sq'];
        $deadliftKeywords = ['デッドリフト', 'デッド', 'dl'];

        // SQLのwhereのための関数
        function whereClause($keywords) {
            $clauses = [];
            foreach ($keywords as $keyword) {
                $clauses[] = "exercise_type.name LIKE '%$keyword%'";
            }
            return implode(" OR ", $clauses);
        }

        // 種目による最大の重量をSQLクエリによって取得
        $query = "SELECT 
                    MAX(CASE WHEN " . whereClause($benchKeywords) . " THEN workout_log.weight ELSE NULL END) AS bench_max,
                    MAX(CASE WHEN " . whereClause($squatKeywords) . " THEN workout_log.weight ELSE NULL END) AS squat_max,
                    MAX(CASE WHEN " . whereClause($deadliftKeywords) . " THEN workout_log.weight ELSE NULL END) AS deadlift_max
                  FROM workout_log
                  INNER JOIN exercise_type ON workout_log.exercise_id = exercise_type.exercise_id";


        
        $result = DB::query($query)->execute()->as_array();

        error_log(print_r($result, true)); // Debugging output

        
        // 合計を計算する
        $benchTotal = $result[0]['bench_max'] ?? 0;
        $squatTotal = $result[0]['squat_max'] ?? 0;
        $deadliftTotal = $result[0]['deadlift_max'] ?? 0;
        $big3Total = $benchTotal + $squatTotal + $deadliftTotal;

        return json_encode([
            'bench' => $benchTotal,
            'squat' => $squatTotal,
            'deadlift' => $deadliftTotal,
            'big3Total' => $big3Total
        ]);
    } catch (Exception $e) {
        return json_encode(['error' => $e->getMessage()]);
    }
}
}

