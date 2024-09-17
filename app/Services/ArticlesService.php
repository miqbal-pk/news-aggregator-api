<?php 
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Article;
use Illuminate\Support\Facades\Http;

class ArticlesService {


    const CATEGORIES = ['business','entertainment', 'general','health', 'science', 'sports', 'technology'];
    
    public static function fetchArticles() :void
    {
        foreach (config('app.news_sources') as $newsSource) {
            $today = date("Y-m-d", strtotime("yesterday"));

            $params = [
                'apiKey' =>$newsSource['news_api_key'],
                'to' =>$today,
                'from' =>$today,
            ];
            
            foreach (self::CATEGORIES as $category) {
                $params['category'] = $category;
                $articles = self::makeApiCall('get', $newsSource['news_api_url'] , $params);
                if($articles['status'] == 'ok'){
                    self::saveArticles( $articles['articles'], $category );
                }
            }             
        }

        
    }

    protected static function makeApiCall($method='get', $url, $params = []) :mixed 
    {
        $response = Http::$method($url, $params);

        return $response->json();
    }

    protected static function saveArticles ($articles, $category) :void
    {
        foreach ($articles as $article){
            $article_obj = new Article();
            $article_obj->title = $article['title'];
            $article_obj->author = $article['author'];
            $article_obj->description = $article['description'];
            $article_obj->content = $article['content'];
            $article_obj->url_to_image = $article['urlToImage'];
            $article_obj->source_id = $article['source']['id'];
            $article_obj->source_name = $article['source']['name'];
            $article_obj->url = $article['url'];
            $article_obj->category = $category;
            $article_obj->published_at = date("Y-m-d", strtotime($article['publishedAt']));

            $article_obj->save();

        }
    }
}