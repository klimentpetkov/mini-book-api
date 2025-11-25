<?php

namespace App\Models;

use App\Models\Book;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *   schema="BookReport",
 *   type="object",
 *   @OA\Property(property="book_id", type="integer", example=1),
 *   @OA\Property(property="summary", type="string", example="Some summary"),
 *   @OA\Property(
 *      property="stats",
 *      type="object",
 *      example={"title_length": 17, "author_length": 12, "estimated_word_count": 42000}
 *   ),
 *   @OA\Property(
 *     property="generated_at",
 *     type="string",
 *     format="date-time",
 *     example="2025-11-19T12:27:14Z"
 *   )
 * )
 */
class BookReport extends Model
{
    protected $fillable = [
        'book_id',
        'summary',
        'stats',
        'generated_at',
    ];

    protected $casts = [
        'stats' => 'array',
        'generated_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Book, BookReport>
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
