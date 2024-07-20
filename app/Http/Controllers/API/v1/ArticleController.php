<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ArticleController extends Controller
{
    //
    public function index()
    {
        try {
            $articles = Article::latest('publish_date')->get();
            if ($articles->isEmpty()) {
                return response()->json([
                    'status' => Response::HTTP_NOT_FOUND,
                    'message' => 'List of articles'
                ], Response::HTTP_NOT_FOUND);
            } else {
                return response()->json([
                    'data' => $articles->map(function ($art) {
                        return [
                            'title' => $art->title,
                            'content' => $art->content,
                            'publish_date' => $art->publish_date
                        ];
                    }),
                    'message' => 'List of articles',
                    'status' => Response::HTTP_OK
                ], Response::HTTP_OK);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Server error ' . $th->getMessage,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request, Response $response)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'publish_date' => 'required',//Carbon::create($request->publish_date)->toDateString(),
        ]);

        if ($validator->fails()) {
            return response()->json( $validator->errors());
        }
        try {
            $newAticle = Article::create([
                'title' => $request->title,
                'content' => $request->content,
                'publish_date' => $request->publish_date,
            ]);

            return response()->json([
                'data' => [$newAticle],
                'status' => $response::HTTP_OK,
                'message' => 'Data stored to DB'
            ], $response::HTTP_OK);
        } catch (Exception $ex) {
            //throw $th;
            Log::error('Error storing data :' . $ex->getMessage());
            return response()->json([
                'status' => $response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Error: create new article',
            ], $response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id, Request $request, Response $response)
    {
        $article = Article::where('id', $id)->first();
        if ($article) {
            return response()->json([
                'status' => Response::HTTP_OK,
                'data'=> [
                    'title' => $article->title,
                    'content' => $article->content,
                    'publish_date' => $article->publish_date,
                ] ,
            ], $response::HTTP_OK);
        }else{
            return response()->json([
                'status'=> Response::HTTP_NOT_FOUND,
                'message'=> 'article not found',
            ], $response::HTTP_NOT_FOUND);
        }
        
    }

    public function update($id, Request $request, Response $response)
    {
        $article = Article::find($id);
        if(!$article){
            return response()->json([
                'error' => 'Article not found',
               'status' => Response::HTTP_NOT_FOUND,
            ], $response::HTTP_NOT_FOUND);
        } else {
            $validator = Validator::make($request->all(), [
                'title' =>'required',
                'content' =>'required',
                'publish_date' =>'required',
            ]);

            if ($validator->fails()) {
                return response()->json( 
                    $validator->errors(), 
                    $response::HTTP_BAD_REQUEST);
            }

            try {
                $article->update([
                    'title' => $request->title,
                    'content' => $request->content,
                    'publish_date' => $request->publish_date,
                ]);

                return response()->json([
                    'data' => $article,
                    'status' => $response::HTTP_OK,           
                ], Response::HTTP_OK);
            } catch (Exception $ex) {
                return response()->json([
                    'message' => 'Error while publishing' . $ex->getMessage(),
                    'status' => $response::HTTP_INTERNAL_SERVER_ERROR,           
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
}
