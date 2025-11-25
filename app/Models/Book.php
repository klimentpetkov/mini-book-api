<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *   schema="Book",
 *   type="object",
 *   required={"title", "author", "published_at"},
 *   @OA\Property(property="title", type="string", example="A book title"),
 *   @OA\Property(property="author", type="string", example="Some Author"),
 *   @OA\Property(
 *     property="published_at",
 *     type="string",
 *     format="date-time",
 *     example="2025-11-19T12:27:14Z"
 *   )
 * )
 *
 * @extends Model<\Database\Factories\BookFactory>
 */
class Book extends Model
{
    use HasFactory;
    protected $fillable = ['title','author','published_at'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * @return HasOne<BookReport, Book>
     */
    public function report(): HasOne
    {
        return $this->hasOne(BookReport::class);
    }
}
