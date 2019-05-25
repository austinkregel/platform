<?php

namespace App;

use Illuminate\Support\Str;

class Project
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $author;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $branch;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $language;

    /**
     * @var string
     */
    private $location;

    public function __construct(
        string $name,
        string $author,
        string $url,
        string $branch,
        string $language,
        string $type,
        string $location
    ) {
        $this->name = $name;
        $this->author = $author;
        $this->url = $url;
        $this->branch = $branch;
        $this->language = $language;
        $this->type = $type;
        $this->location = $location;
    }

    public function get(string $property): string
    {
        return $this->{$property};
    }

    public function slug(string $property): string
    {
        return Str::slug($this->{$property});
    }

    public function title(string $property): string
    {
        return Str::title($this->{$property});
    }

    public function pascal(string $property): string
    {
        return Str::studly($this->{$property});
    }
}
