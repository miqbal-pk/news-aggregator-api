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
   
class UserController extends BaseController
{
    /**
     * Api Register function
     *
     * @return \Illuminate\Http\Response
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
     * Api login function
     *
     * @return \Illuminate\Http\Response
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
     * Api logout function
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request): JsonResponse
    {   
        $success['name'] =  $request->user()->name;
        $request->user()->tokens()->delete();
        return $this->sendResponse($success, 'User logged out successfully.');

    }

    public function getUserPreferences(Request $request)
    {
        $preferences  = UserPreferences::all();
        $success['preferences'] = $preferences;
        return $this->sendResponse($success, 'User Preferences');
    }

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

    public function getUserFeeds(Request $request)
    {
        $preferences  =  UserPreferences::all();

        if(empty($preferences)){
            $success['status'] = 'OK';
            return $this->sendResponse($success, 'You have no preference');
        }

        $articlesQuery = Article::all();
        if(isset($preferences->authors)){
            $articlesQuery = $articlesQuery->whereIn('author', $preferences->authors);
        } 

        if(isset($preferences->sources)){
            $articlesQuery = $articlesQuery->whereIn('source_name', $preferences->sources);
        }

        if(isset($preferences->categories)){
            $articlesQuery = $articlesQuery->whereIn('category', $preferences->categories);
        }

        $success ['articles'] =$articlesQuery;

        return $this->sendResponse($success, 'Your news feed');
    }
}
