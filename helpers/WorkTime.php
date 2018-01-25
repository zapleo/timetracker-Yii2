<?php
/**
 * Created by ValekS. TimeTracker. ZapleoSoft.
 * File: WorkTime.php
 * Date: 24.01.18
 * Time: 16:05
 */

namespace app\helpers;


class WorkTime
{
    const TIME_START = 8;
    const TIME_END = 18;

    /**
     * @param $datetime
     * @return int
     */
    public static function check($datetime)
    {
        date_default_timezone_set('Europe/Kiev');

        // workTime
        $work_time = 0;

        // hour (int)
        $hour = date('H', $datetime);
        // day (int)
        $day = date('N', $datetime);

        if ($hour >= self::TIME_START && $hour < self::TIME_END)
            $work_time = 1;

        if ($day == 6 || $day == 7)
            $work_time = 0;

        return $work_time;
    }
}