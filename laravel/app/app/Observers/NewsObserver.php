<?php

namespace App\Observers;

use App\Models\News;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NewsObserver
{
    /**
     * Handle the News "created" event.
     */
    public function created(News $news): void
    {
        //
    }

    /**
     * Handle the News "updated" event.
     */
    public function updated(News $news): void
    {
        //
    }

    public function updating(News $news): void
    {
        Log::info("Updating news $news");
    }

    public function saving(News $news): void
    {
        $news->slug = Str::slug($news->title);
    }

    /**
     * Handle the News "deleted" event.
     */
    public function deleted(News $news): void
    {
        //
    }

    /**
     * Handle the News "restored" event.
     */
    public function restored(News $news): void
    {
        //
    }

    /**
     * Handle the News "force deleted" event.
     */
    public function forceDeleted(News $news): void
    {
        //
    }
}
