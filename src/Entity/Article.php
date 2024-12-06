<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ArticleRepository;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
// Déclaration de l'entité comme ressource API avec les verbes HTTP autorisées
#[ApiResource(
    operations: [
        new Get(), // rendre accessible une ressource grâce à son ID

        new GetCollection( // rendre accessible l'ensemble des ressources
            paginationEnabled: true, // pagination de la data activée par défaut
            paginationItemsPerPage: 20,  // définir le nombre de ressources articles à afficher par page, 
            paginationClientEnabled: true, // donner la possibilité au client de choisir l'activation de la pagination
            paginationClientItemsPerPage: true, // donner la possibilité au client de choisir le nombre d'objets ressources par page, 
            uriTemplate: '/getarticles', // création d'une route personnalisé
            name: 'getArticles' // donner à la route un nom personnalisé
        ),
        new GetCollection(
            paginationEnabled: false, // pagination désactivée
            uriTemplate: '/getarticles2', // création d'une route personnalisé
            name: 'getArticles2' // donner à la route un nom personnalisé
        ),
        new Post(), // créer une nouvelle ressource
        new Put(), // mettre à jour une ressource complètement
        new Patch(), // mettre à jour une ressource en particulière de façon partielle
        new Delete() // supprimer une ressource Article
    ]
)]
// Vérifier séparemment si la donnée titre d'un article est unique dans la base de données
#[UniqueEntity(
    fields: 'title',
    message: 'Un titre similaire existe déjà'
)]
// Vérifier séparemment si la donnée contenu d'un article est unique dans la base de données
#[UniqueEntity(
    fields: 'content',
    message: 'Un contenu similaire existe déjà'
)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'L\'article doit avoir un titre')]
    #[Assert\Length(
        min: 6,
        max: 70,
        minMessage: 'Le titre de l\'article doit avoir plus de {{ limit }} cractères',
        maxMessage: 'Le titre de l\'article doit avoir moins de {{ limit }} caractères'
    )]
    private ?string $title = null;


    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(
        min: 100,
        max: 1200,
        minMessage: 'Le contenu de l\'article doit avoir plus de {{ limit }} cractères',
        maxMessage: 'Le contenu de l\'article doit avoir moins de {{ limit }} caractères'
    )]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}