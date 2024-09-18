<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\Request;
use Validator;
use App\Http\Resources\ArticleResource;
use Illuminate\Http\JsonResponse;
use DB;

class ArticleController extends BaseController
{
    
    /**
     * Display a listing of the Articles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        $articles = DB::table('articles')->paginate(20);
    
        return $this->sendResponse($articles, 'Articles retrieved successfully.');
    }
   
    /**
     * Display the specified Article.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        $article = Article::find($id);
  
        if (is_null($article)) {
            return $this->sendError('Article not found.');
        }
   
        return $this->sendResponse(new ArticleResource($article), 'Article retrieved successfully.');
    }

    /**
     * Searching the Articles.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request): JsonResponse
    {
        $inputs = $request->all();

        $articles = DB::table('articles');

        if(isset($inputs['category'])){
            $articles = $articles->where('category', $inputs['category']);
        } else if(isset($inputs['source'])){
            $articles = $articles->where('source_name', $inputs['source']);
        }else if(isset($inputs['q'])){
            $articles = $articles->where('content', 'like', '%'.$inputs['q'].'%')
                                ->orwhere('title', 'like','%'.$inputs['q'].'%' )
                                ->orwhere('description', 'like', '%'.$inputs['q'].'%');
        }else if(isset($inputs['published_date'])){
            $articles = $articles->where('published_at', $inputs['published_date']);
        }

        $articles = $articles->get();
    
        return $this->sendResponse($articles, 'Articles retrieved successfully.');
    }
    
}
