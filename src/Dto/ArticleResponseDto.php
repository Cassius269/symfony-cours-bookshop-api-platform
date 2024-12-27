<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class ArticleResponseDto
{
    #[Assert\NotBlank(message: 'L\'article doit avoir un titre')]
    #[Assert\Length(
        min: 6,
        max: 70,
        minMessage: 'Le titre de l\'article doit avoir plus de {{ limit }} cractères',
        maxMessage: 'Le titre de l\'article doit avoir moins de {{ limit }} caractères'
    )]
    private string $title;

    #[Assert\NotBlank(message: 'L\'article doit avoir un contenu')]
    #[Assert\Length(
        min: 100,
        max: 1200,
        minMessage: 'Le contenu de l\'article doit avoir plus de {{ limit }} cractères',
        maxMessage: 'Le contenu de l\'article doit avoir moins de {{ limit }} caractères'
    )]
    private string $content;

    private string $author;
    private string $email;

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

    /**
     * Get the value of content
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Set the value of content
     *
     * @return  self
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of author
     */
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
