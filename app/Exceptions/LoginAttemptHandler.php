<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 1/15/2019
 * Time: 11:25 PM
 */

namespace App\Exceptions;

use App\LoginAttempts;
use Illuminate\Support\Facades\Request;

class LoginAttemptHandler
{
    /**
     * @param $cardId
     */
    public static function log($cardId)
    {
        $now = new \DateTime();

        $result = self::get($cardId);

        if(!empty($result))
        {
            $loginAttempt = new \DateTime($result->created_at);
            $interval = $now->diff($loginAttempt);
            $minutes = $interval->format('%i');

            if($minutes <= 3)
            {
                LoginAttempts::where('card_id', $cardId)
                    ->latest()
                    ->first()
                    ->update(['attempts' => $result->attempts + 1]);
            }
            else
            {
                LoginAttempts::insert([
                    'card_id' => $cardId,
                    'created_at' => $now->format('Y-m-d H:i:s'),
                    'updated_at' => $now->format('Y-m-d H:i:s')
                ]);
            }
        }
        else
        {
            LoginAttempts::insert([
                'card_id' => $cardId,
                'created_at' => $now->format('Y-m-d H:i:s'),
                'updated_at' => $now->format('Y-m-d H:i:s')
            ]);
        }

    }

    /**
     * @param $cardId
     * @return mixed
     */
    public static function get($cardId)
    {
        $attemptsData = LoginAttempts::where('card_id', '=', $cardId)
            ->orderBy('created_at', 'desc')
            ->first();

        return $attemptsData;
    }

    /**
     * @param $cardId
     * @return bool
     */
    public static function lock($cardId)
    {
        $now = new \DateTime();

        $attempts = self::get($cardId);

        if(!empty($attempts))
        {
            $loginAttempt = new \DateTime($attempts->created_at);
            $interval = $now->diff($loginAttempt);
            $minutes = $interval->format('%i');

            if($attempts->attempts >= 2 && $minutes <= 3)
            {
                return true;
            }
        }

        return false;
    }
}