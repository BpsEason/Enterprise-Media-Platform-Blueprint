<?php

namespace App\Models;

use App\Domain\Article as ArticleDomain;
use App\ValueObjects\ArticleId;
use App\ValueObjects\ArticleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;
    
    protected $table = 'articles';

    protected $fillable = [
        'title',
        'content',
        'status',
    ];

    /**
     * Convert the Eloquent model to a Domain object.
     *
     * @param array $data
     * @return ArticleDomain
     */
    public static function fromArray(array $data): ArticleDomain
    {
        return new ArticleDomain(
            new ArticleId($data['id']),
            $data['title'],
            $data['content'],
            new ArticleStatus($data['status']),
            \Carbon\Carbon::parse($data['created_at']),
            \Carbon\Carbon::parse($data['updated_at'])
        );
    }
}
