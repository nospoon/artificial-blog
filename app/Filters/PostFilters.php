<?php

namespace App\Filters;

class PostFilters extends QueryFilters
{
    public function myPosts()
    {
        return $this->builder->where('user_id', auth()->user()->id);
    }

    public function user(int $userId)
    {
        return $this->builder->where('user_id', $userId);
    }
}
