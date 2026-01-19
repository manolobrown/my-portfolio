<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Comments extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'partials.comments',
    ];

    /**
     * Cache duration in seconds (5 minutes).
     *
     * @var int
     */
    protected $cache_duration = 300;

    /**
     * The comment title with caching.
     */
    public function title(): string
    {
        $post_id = get_the_ID();
        $comments_count = get_comments_number();

        $cache_key = "comments_title_{$post_id}_{$comments_count}";
        $cached = wp_cache_get($cache_key, 'sage_comments_composer');

        if ($cached !== false) {
            return $cached;
        }

        $title = sprintf(
            /* translators: %1$s is replaced with the number of comments and %2$s with the post title */
            _nx('%1$s response to &ldquo;%2$s&rdquo;', '%1$s responses to &ldquo;%2$s&rdquo;', $comments_count, 'comments title', 'sage'),
            $comments_count === 1 ? _x('One', 'comments title', 'sage') : number_format_i18n($comments_count),
            get_the_title()
        );

        wp_cache_set($cache_key, $title, 'sage_comments_composer', $this->cache_duration);

        return $title;
    }

    /**
     * Retrieve the comments with caching.
     */
    public function responses(): ?string
    {
        if (! have_comments()) {
            return null;
        }

        $post_id = get_the_ID();
        $comments_count = get_comments_number();
        $page = get_query_var('cpage') ?: 1;

        $cache_key = "comments_list_{$post_id}_{$comments_count}_page_{$page}";
        $cached = wp_cache_get($cache_key, 'sage_comments_composer');

        if ($cached !== false) {
            return $cached;
        }

        $responses = wp_list_comments([
            'style' => 'ol',
            'short_ping' => true,
            'echo' => false,
        ]);

        wp_cache_set($cache_key, $responses, 'sage_comments_composer', $this->cache_duration);

        return $responses;
    }

    /**
     * The previous comments link with caching.
     */
    public function previous(): ?string
    {
        $post_id = get_the_ID();
        $page = get_query_var('cpage') ?: 1;

        $cache_key = "comments_prev_{$post_id}_page_{$page}";
        $cached = wp_cache_get($cache_key, 'sage_comments_composer');

        if ($cached !== false) {
            return $cached === 'null' ? null : $cached;
        }

        if (! get_previous_comments_link()) {
            wp_cache_set($cache_key, 'null', 'sage_comments_composer', $this->cache_duration);
            return null;
        }

        $link = get_previous_comments_link(
            __('&larr; Older comments', 'sage')
        );

        wp_cache_set($cache_key, $link, 'sage_comments_composer', $this->cache_duration);

        return $link;
    }

    /**
     * The next comments link with caching.
     */
    public function next(): ?string
    {
        $post_id = get_the_ID();
        $page = get_query_var('cpage') ?: 1;

        $cache_key = "comments_next_{$post_id}_page_{$page}";
        $cached = wp_cache_get($cache_key, 'sage_comments_composer');

        if ($cached !== false) {
            return $cached === 'null' ? null : $cached;
        }

        if (! get_next_comments_link()) {
            wp_cache_set($cache_key, 'null', 'sage_comments_composer', $this->cache_duration);
            return null;
        }

        $link = get_next_comments_link(
            __('Newer comments &rarr;', 'sage')
        );

        wp_cache_set($cache_key, $link, 'sage_comments_composer', $this->cache_duration);

        return $link;
    }

    /**
     * Determine if the comments are paginated with caching.
     */
    public function paginated(): bool
    {
        $post_id = get_the_ID();

        $cache_key = "comments_paginated_{$post_id}";
        $cached = wp_cache_get($cache_key, 'sage_comments_composer');

        if ($cached !== false) {
            return (bool) $cached;
        }

        $paginated = get_comment_pages_count() > 1 && get_option('page_comments');

        wp_cache_set($cache_key, $paginated ? 1 : 0, 'sage_comments_composer', $this->cache_duration);

        return $paginated;
    }

    /**
     * Determine if the comments are closed with caching.
     */
    public function closed(): bool
    {
        $post_id = get_the_ID();

        $cache_key = "comments_closed_{$post_id}";
        $cached = wp_cache_get($cache_key, 'sage_comments_composer');

        if ($cached !== false) {
            return (bool) $cached;
        }

        $closed = ! comments_open() && get_comments_number() != '0' && post_type_supports(get_post_type(), 'comments');

        wp_cache_set($cache_key, $closed ? 1 : 0, 'sage_comments_composer', $this->cache_duration);

        return $closed;
    }
}
