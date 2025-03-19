<?php

class Controller_Counter extends Controller
{
    /**
     * Summary of post_Counter
     * @return bool|string
     * 
     * 指定された年と月の記録がある日数を取得し、JSONで返す
     */
    public function post_Counter()
    {   
        //フロントから送信された年と月を取得
        $year = Input::post('year');
        $month = Input::post('month');

        //指定された記録のある日を取得（重複を排除）
        $result = DB::select(DB::expr('DISTINCT DATE(created_at) as date'))
                ->from('exercise_type')
                ->where(DB::expr('YEAR(created_at)'), '=', $year) //年の条件
                ->and_where(DB::expr('MONTH(created_at)'), '=', $month) //月の条件
                ->distinct(true)
                ->execute()
                ->as_array();

        //記録のある日を数える
        $uniqueDaysCount = count($result);
        //JSON形式で日数とエントリーリストを返す
        return json_encode(['count' => $uniqueDaysCount, 'entries' => $result]);
    } 
    
    /**
     * Summary of post_Big3max
     * @return bool|string
     * BIG3(ベンチプレス、スクワット、デッドリフト)の最大重量を取得し、合計を計算して返す
     */
    public function post_Big3max()
    {
        try {
            // ユーザーによって呼び方が違うため、インプットを予測する
            $benchKeywords = ['ベンチプレス', 'ベンチ', 'benchpress', 'bp'];
            $squatKeywords = ['スクワット', 'squat', 'sq'];
            $deadliftKeywords = ['デッドリフト', 'デッド', 'dl'];

            /**
             * Summary of whereClause
             * @param mixed $keywords
             * @return string
             * 指定したキーワードリストに基づいて部分一致検索の条件を作成
             */
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
            //SQLクエリの実行
            $result = DB::query($query)->execute()->as_array();

            // 合計を計算する
            $benchTotal = $result[0]['bench_max'] ?? 0;
            $squatTotal = $result[0]['squat_max'] ?? 0;
            $deadliftTotal = $result[0]['deadlift_max'] ?? 0;
            $big3Total = $benchTotal + $squatTotal + $deadliftTotal;

            //結果をJSON形式で返す
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

