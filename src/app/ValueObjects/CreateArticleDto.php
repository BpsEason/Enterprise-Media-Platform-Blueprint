<?php

namespace App\ValueObjects;

use Illuminate\Http\Request;

class CreateArticleDto
{
    public string $title;
    public string $content;

    public function __construct(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        $this->title = $validated['title'];
        $this->content = $validated['content'];
    }
}
