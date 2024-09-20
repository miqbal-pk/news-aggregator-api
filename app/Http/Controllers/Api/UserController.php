<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\JsonResponse;
use RobTrehy\LaravelUserPreferences\UserPreferences;
use App\Models\Article;
use DB;
   
class UserController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags ={"User"},
     *     security={{"bearerAuth":{}}},
     *     summary="Register User",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     description="User name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     description="User email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="set password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="c_password",
     *                     description="Confirm password",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User Registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('NewsApp')->plainTextToken;
        $success['name'] =  $user->name;
   
        return $this->sendResponse($success, 'User register successfully.');
    }
   
    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags ={"User"},
     *     security={{"bearerAuth":{}}},
     *     summary="Login User",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     description="User email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="User password",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logedin successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('NewsApp')->plainTextToken; 
            $success['name'] =  $user->name;
   
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags ={"User"},
     *     security={{"bearerAuth":{}}},
     *     summary="Logout User",
     *     @OA\Response(
     *         response=200,
     *         description="User loged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {   
        $success['name'] =  $request->user()->name;
        $request->user()->tokens()->delete();
        return $this->sendResponse($success, 'User logged out successfully.');

    }

    /**
     * @OA\Get(
     *     path="/api/get-user-preferences",
     *     tags ={"User Preference"},
     *     security={{"bearerAuth":{}}},
     *     summary="Get user preferences",
     *     @OA\Response(response="200", description="An example resource")
     * )
     */
    public function getUserPreferences(Request $request)
    {
        $preferences  = UserPreferences::all();
        $success['preferences'] = $preferences;
        return $this->sendResponse($success, 'User Preferences');
    }

     /**
     * @OA\Post(
     *     path="/api/set-user-preferences",
     *     tags ={"User Preference"},
     *     security={{"bearerAuth":{}}},
     *     summary="Store user preferences",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="preferences[authors][0]",
     *                     description="Author 1",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="preferences[authors][1]",
     *                     description="Author 2",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="preferences[categories][0]",
     *                     description="sport",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="preferences[categories][1]",
     *                     description="art",
     *                     type="string"
     *                 ),
     *                  @OA\Property(
     *                     property="preferences[sources][0]",
     *                     description="bbc-news",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="preferences[sources][1]",
     *                     description="bbc-news",
     *                     type="string"
     *                 ),
     *                 @OA\Property(property="name", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Preferences saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function setUserPreferences(Request $request): JsonResponse
    {
        
        if(isset($request->authors))
            UserPreferences::set('authors', $request->authors);

        if(isset($request->sources))
            UserPreferences::set('sources', $request->sources);

        if(isset($request->categories))
            UserPreferences::set('categories', $request->categories);

            $success['name'] =  $request->user()->name;
   
        return $this->sendResponse($success, 'User Preferences are stored Successfully');


    }

    /**
     * @OA\Get(
     *     path="/api/get-user-feeds",
     *     tags ={"User Preference"},
     *     security={{"bearerAuth":{}}},
     *     summary="Get user feeds based on preferences",
     *     @OA\Response(response="200", description="An example resource")
     * )
     */
    public function getUserFeeds(Request $request)
    {
        $preferences  =  UserPreferences::all();

        if(empty($preferences)){
            $success['status'] = 'OK';
            return $this->sendResponse($success, 'You have no preference');
        }

        $articlesQuery = DB::table('articles');
        if(isset($preferences->authors)){
            $articlesQuery = $articlesQuery->whereIn('author', $preferences->authors);
        } 

        if(isset($preferences->sources)){
            $articlesQuery = $articlesQuery->orWhereIn('source_name', $preferences->sources);
        }

        if(isset($preferences->categories)){
            $articlesQuery = $articlesQuery->orWhereIn('category', $preferences->categories);
        }

        $success ['articles'] =$articlesQuery->orderBy('id', 'DESC')->get();

        return $this->sendResponse($success, 'Your news feed');
    }
}
