<?php

namespace App\Http\Controllers\V1;

use App\DTOs\BookData;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookStoreRequest;
use App\Http\Requests\BookUpdateRequest;
use App\Http\Resources\BookReportResource;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *   name="Books",
 *   description="Books CRUD"
 * )
 */
class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api','throttle:api']);
        $this->authorizeResource(Book::class, 'book');
    }

    /**
     * Books list (pagination)
     *
     * @OA\Get(
     *   path="/api/v1/books",
     *   tags={"Books"},
     *   security={{"BearerAuth":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Book")),
     *       @OA\Property(property="links", type="object"),
     *       @OA\Property(property="meta", type="object")
     *     )
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        $books = Book::query()->orderByDesc('id')->paginate();

        return BookResource::collection($books);
    }

    /**
     * Shows a specific book
     *
     * @OA\Get(
     *   path="/api/v1/books/{id}",
     *   tags={"Books"},
     *   security={{"BearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Book")),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(Book $book): BookResource
    {
        return new BookResource($book);
    }

    /**
     * Creates a new book
     *
     * @OA\Post(
     *   path="/api/v1/books",
     *   tags={"Books"},
     *   security={{"BearerAuth":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/BookStoreRequest")
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Created",
     *     @OA\JsonContent(ref="#/components/schemas/Book")
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=403, description="Forbidden"),
     *   @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(BookStoreRequest $request, BookService $service): JsonResponse
    {
        $book = $service->create(BookData::fromArray($request->validated()));

        return (new BookResource($book))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Updates a book
     *
     * @OA\Patch(
     *   path="/api/v1/books/{id}",
     *   tags={"Books"},
     *   security={{"BearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *     required=true,
     *     description="Fields to update (any subset)",
     *     @OA\JsonContent(ref="#/components/schemas/BookUpdateRequest")
     *   ),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Book")),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=403, description="Forbidden"),
     *   @OA\Response(response=404, description="Not found"),
     *   @OA\Response(response=422, description="Validation error")
     * )
     * @OA\Put(
     *   path="/api/v1/books/{id}",
     *   tags={"Books"},
     *   security={{"BearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *      required=true,
     *      description="Full update of Book, all fields required",
     *      @OA\JsonContent(ref="#/components/schemas/BookUpdateRequest")
     *   ),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Book")),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=403, description="Forbidden"),
     *   @OA\Response(response=404, description="Not found"),
     *   @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(BookUpdateRequest $request, Book $book): BookResource
    {
        $book->fill($request->validated())->save();

        return new BookResource($book->refresh());
    }

    /**
     * Book deletion
     *
     * @OA\Delete(
     *   path="/api/v1/books/{id}",
     *   tags={"Books"},
     *   security={{"BearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=204, description="No Content"),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=403, description="Forbidden"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy(Book $book): JsonResponse
    {
        $book->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Returns generated report for a book.
     *
     * @OA\Get(
     *   path="/api/v1/books/{id}/report",
     *   tags={"Books"},
     *   security={{"BearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       @OA\Property(property="data", ref="#/components/schemas/BookReport")
     *     )
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=404, description="Report not ready")
     * )
     */
    public function report(Book $book): JsonResponse
    {
        // same policy, like in show()
        $this->authorize('view', $book);

        $report = $book->report;

        if (! $report) {
            return response()->json([
                'message' => 'Report not ready yet',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => new BookReportResource($report),
        ], Response::HTTP_OK);
    }
}
