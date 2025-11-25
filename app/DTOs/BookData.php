<?php

namespace App\DTOs;

class BookData
{
    public function __construct(
        public string $title,
        public string $author,
        public ?string $published_at = null,
    ) {
    }

    /**
     * @param array{
     *   title: string,
     *   author: string,
     *   published_at?: string|null
     * } $d
     */
    public static function fromArray(array $d): self
    {
        return new self($d['title'], $d['author'], $d['published_at'] ?? null);
    }

    /** @return array<string, string|null> */
    public function toArray(): array
    {
        return [
            'title'        => $this->title,
            'author'       => $this->author,
            'published_at' => $this->published_at,
        ];
    }
}
