<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\Request;
use Validator;
use App\Http\Resources\ArticleResource;
use Illuminate\Http\JsonResponse;
use DB;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="News-aggregator API Documentation",
 *      description="Swagger OpenAPI Description for case study of news aggregator api",
 *      @OA\Contact(
 *          email="iqbal.shaheen0101@gmail.com"
 *      ),
 * )
 * 
 */
class ArticleController extends BaseController
{
    
    /**
     * @OA\Get(
     *     
     *     path="/api/articles",
     *     tags ={"Articles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="returns all articles")
     * )
     */
    public function index(): JsonResponse
    {
        $articles = DB::table('articles')->orderBy('id', 'DESC')->paginate(20);
    
        return $this->sendResponse($articles, 'Articles retrieved successfully.');
    }
   
    /**
     * @OA\Get(
     *     path="/api/article/{id}",
     *     tags ={"Articles"},
     *     security={{"bearerAuth":{}}},
     *      @OA\Parameter  (
     *       description="Article ID",
     *       in="path",
     *       name="id",
     *       example="10",
     * 
     *    ),
     *   
     *     @OA\Response(response="200", description="searched  articles")
     * )
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
     * @OA\Get(
     *     path="/api/articles/search",
     *      tags ={"Articles"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter  (
     *       description="source",
     *       in="query",
     *       name="source",
     *       example="bbc-news",
     *    ),
     *    @OA\Parameter  (
     *       description="published_at",
     *       in="query",
     *       name="published_at",
     *       example="2023-12-03",
     *    ),
     *    
     *    @OA\Parameter  (
     *       description="category",
     *       in="query",
     *       name="category",
     *       example="sport",
     *    ),
     *    @OA\Parameter  (
     *       description="keywords",
     *       in="query",
     *       name="q",
     *       example="Olympics 2024",
     *    ),
     *     @OA\Response(response="200", description="searched  articles")
     * )
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

        $articles = $articles->orderBy('id', 'DESC')->get();
    
        return $this->sendResponse($articles, 'Articles retrieved successfully.');
    }
    
}
