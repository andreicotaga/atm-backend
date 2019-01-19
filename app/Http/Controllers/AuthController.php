<?php
/**
 * Created by PhpStorm.
 * Cards: Cards
 * Date: 12/9/2018
 * Time: 1:00 AM
 */

namespace App\Http\Controllers;

use App\Exceptions\LoginAttemptHandler;
use Illuminate\Http\Request;
use App\Cards;
use Tymon\JWTAuth\Facades\JWTAuth as JWTAuthFacade;
use Tymon\JWTAuth\JWTAuth as JWTAuth;
use Laravel\Lumen\Routing\Controller as BaseController;

class AuthController extends BaseController
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;
    protected $apiAuth;

    /**
     * AuthController constructor.
     * @param JWTAuth $jwt
     */
    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'card_number'       => 'required|max:255',
            'password'               => 'required|max:4',
        ]);

        $userCard = Cards::where('card_number', '=', $request->input('card_number'))->first()->toArray();

        if(!empty($userCard) && (bool)!$userCard['blocked'])
        {
            LoginAttemptHandler::log($userCard['id']);

            if(LoginAttemptHandler::lock($userCard['id']))
            {
                return response()->json(['error' => 'You have reached the maximum number of login attempts. Please try again in 3 minutes.'], 404);
            }
        }

        try {

            if (!$token = $this->jwt->attempt($request->only('card_number', 'password'))) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent' => $e->getMessage()], 500);
        }

        if(!empty($userCard))
        {
            if((bool)$userCard['blocked'])
            {
                return response()->json(['error' => 'You card was blocked! Please contact customer support.'], 404);
            }
        }

        return response()->json(compact('token'));
    }

    public function getAuthenticatedUser()
    {
        try {

            if (!$user = JWTAuthFacade::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return response()->json(compact('user'));
    }
}