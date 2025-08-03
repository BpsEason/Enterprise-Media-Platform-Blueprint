<?php

namespace App\Http\Controllers;

use App\Application\IArticleService;
use App\Domain\Article;
use App\Http\Resources\ArticleResource;
use App\ValueObjects\CreateArticleDto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class ArticleController extends Controller
{
    private IArticleService $articleService;

    public function __construct(IArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $articles = $this->articleService->getAllArticles();
        return ArticleResource::collection($articles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $dto = new CreateArticleDto($request);
        $article = $this->articleService->createArticle($dto);
        return response()->json(new ArticleResource($article), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): ArticleResource
    {
        $article = $this->articleService->getArticleById($id);
        return new ArticleResource($article);
    }

    /**
     * Search for articles by title or content.
     */
    public function search(Request $request): AnonymousResourceCollection
    {
        $articles = $this->articleService->searchArticles($request->get('query'));
        return ArticleResource::collection($articles);
    }

    /**
     * Ask an AI service a question related to an article.
     */
    public function askAi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => 'required|string',
        ]);
        
        $aiServiceUrl = env('AI_SERVICE_URL') . '/query';

        try {
            $response = Http::post($aiServiceUrl, ['prompt' => $validated['prompt']]);
            $response->throw();
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get response from AI service'], 500);
        }
    }
}
