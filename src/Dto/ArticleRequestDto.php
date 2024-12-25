<?php

namespace App\Dto;

class ArticleRequestDto
{
    private string $title;
    private string $author;

    public function getTitle(): string
    {
        return $this->title;
    }


    /**
     * Set the value of title
     *
     * @return  self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Set the value of author
     *
     * @return  self
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }
}
