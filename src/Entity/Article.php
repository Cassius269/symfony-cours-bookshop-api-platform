<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ArticleRepository;
use ApiPlatform\Metadata\GetCollection;
use App\State\CustomGetCollectionProvider;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
// Déclaration de l'entité comme ressource API avec les verbes HTTP autorisées
#[ApiResource(
    // Exposition des champs en phases de serialization et déserialization
    normalizationContext: ['groups' => ['books.read', 'authors.read']],
    denormalizationContext: ['groups' => ['books.write', 'authors.write']],
    // Définition des verbes HTTP autorisées sur l'entité Article
    operations: [
        new Get(), // rendre accessible une ressource grâce à son ID

        new GetCollection( // 1ère route pour rendre accessible l'ensemble des ressources avec une pagination activée 
            paginationEnabled: true, // pagination de la data activée par défaut
            paginationItemsPerPage: 20,  // définir le nombre de ressources articles à afficher par page, 
            paginationClientEnabled: true, // donner la possibilité au client de choisir l'activation de la pagination
            paginationClientItemsPerPage: true, // donner la possibilité au client de choisir le nombre d'objets ressources par page, 
            uriTemplate: '/getarticles', // création d'une route personnalisé
            name: 'getArticles', // donner à la route un nom personnalisé
            provider: CustomGetCollectionProvider::class // utilisation d'un provider personnalisé pour la récupération des ressources en transformant les titres en majuscule
        ),
        new GetCollection( // 2ème route pour rendre accessible l'ensemble des ressources avec une pagination désactivée 
            paginationEnabled: false, // pagination désactivée
            uriTemplate: '/getarticles2', // création d'une route personnalisé
            name: 'getArticles2', // donner à la route un nom personnalisé
            filters: ['article.search_filter']
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
#[ORM\HasLifecycleCallbacks] // Activation de l'utilisation de fonction callback pour ajouter une date de création à toute nouvelle ressource Article
// #[ApiFilter(
//     searchFilter::class,
//     properties: [
//         'title' => 'partial',
//         'content' => 'partial'
//     ]
// )]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['books.read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'L\'article doit avoir un titre')]
    #[Assert\Length(
        min: 6,
        max: 70,
        minMessage: 'Le titre de l\'article doit avoir plus de {{ limit }} cractères',
        maxMessage: 'Le titre de l\'article doit avoir moins de {{ limit }} caractères'
    )]
    #[Groups(['books.read', 'books.write'])]
    // #[ApiFilter(SearchFilter::class, strategy: 'exact')]
    #[ApiFilter(OrderFilter::class)]
    private ?string $title = null;


    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(
        min: 100,
        max: 1200,
        minMessage: 'Le contenu de l\'article doit avoir plus de {{ limit }} cractères',
        maxMessage: 'Le contenu de l\'article doit avoir moins de {{ limit }} caractères'
    )]
    #[Groups(['books.read', 'books.write'])]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'articles', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['authors.read', 'authors.write'])]
    #[ApiFilter( // mise en place de filtre de recherche d'occurences avec une stratégie partielle
        SearchFilter::class,
        properties: ['author.firstname' => 'partial']
    )]
    private ?Author $author = null;

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

    // Création d'un évenement Doctrine pour l'ajout de date de création à chaque nouvelle ressource ajoutée
    #[ORM\PrePersist]
    public function addCreationDateToAnArticle(LifecycleEventArgs $args): void
    {
        // dd($args);
        // Ajout d'une date de création à la date du jour avant l'envoi en base de données de la ressource Article
        $article = $args->getObject();
        $article->setCreatedAt(new \DateTimeImmutable());

        // Envoi de la ressource en base de données
        $args->getObjectManager()->flush();
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): static
    {
        $this->author = $author;

        return $this;
    }
}
