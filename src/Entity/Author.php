<?php

namespace App\Entity;

use App\Entity\User;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use App\State\InsertUserProcessor;
use App\Repository\AuthorRepository;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
// Exposition de la ressource Author à l'aide des verbes HTTP GET et POST avec des groupes de sérialisation
#[Get(
    normalizationContext: ['groups' => ['authors.read']]
)]
#[GetCollection(
    normalizationContext: ['groups' => ['authors.read']]
)]
#[Post(
    denormalizationContext: ['groups' => ['authors.write']],
    processor: InsertUserProcessor::class
)]
class Author extends User
{
    /**
     * @var Collection<int, Article>
     */
    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setAuthor($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): static
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getAuthor() === $this) {
                $article->setAuthor(null);
            }
        }

        return $this;
    }
}
