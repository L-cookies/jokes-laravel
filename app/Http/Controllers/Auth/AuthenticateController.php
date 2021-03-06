<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2017/5/1
 * Time: 1:25
 */

namespace App\Http\Controllers\Auth;

use App\Tools\RsaUtils;
use App\Transformer\UserTransformer;
use JWTAuth;
//use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\User;
class AuthenticateController extends Controller
{
  public function authenticate(Request $request)
  {
    // grab credentials from the request
    $credentials = $request->only('email', 'password');
    try {
      // attempt to verify the credentials and create a token for the user
      if (! $token = JWTAuth::attempt($credentials)) {
        return response()->json(['error' => 'invalid_credentials'], 401);
      }
    } catch (JWTException $e) {
      // something went wrong whilst attempting to encode the token
      return response()->json(['error' => 'could_not_create_token'], 500);
    }
    $user = User::where('email', $credentials['email'])->first();
    $userTransform = new UserTransformer();
    return ['user'=> $userTransform->transform($user), 'token' => $token];
    // all good so return the token
    //return response()->json(compact('token'));
  }

  public function encryptedAuthenticate(Request $request)
  {
    $credentials['email'] = $request->get('email');
    $credentials['password'] = RsaUtils::dePrivate( $request->get('password'));
    try {
      // attempt to verify the credentials and create a token for the user
      if (! $token = JWTAuth::attempt($credentials)) {
        return response()->json(['error' => 'invalid_credentials'], 401);
      }
    } catch (JWTException $e) {
      // something went wrong whilst attempting to encode the token
      return response()->json(['error' => 'could_not_create_token'], 500);
    }
    $user = User::where('email', $credentials['email'])->first();
    $userTransform = new UserTransformer();
    return ['user'=> $userTransform->transform($user), 'token' => $token];
  }
}