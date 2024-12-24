<?php

namespace App\Dto;

use Symfony\Component\Serializer\Attribute\Groups;

class ArticleDto
{
    private string $title;
    private string $author;

    public function getTitle()
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

    public function getAuthor()
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
