<?php

namespace App\Http\Controllers\API\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleCollection;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    //
    public function index(Request $request, Response $response){
        try {
            $query = Article::query()->latest('publish_date');
            $keyword = $request->input('title');
            if ($keyword) {
                $query->where('title','like',"%{$keyword}%");
            }

            $articles = $query->paginate(2);

            if ($articles->isEmpty()) {
                return response()->json([
                    'status' => Response::HTTP_NOT_FOUND,
                    'message' => 'Article empty'
                ], Response::HTTP_NOT_FOUND);
            } else {
                return new ArticleCollection($articles);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Server error ' . $th->getMessage,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
