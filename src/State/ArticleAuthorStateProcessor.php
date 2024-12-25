<?php

namespace App\State;

use App\Entity\Article;
use App\Dto\ArticleResponseDto;
use ApiPlatform\Metadata\Operation;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;

class ArticleAuthorStateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AuthorRepository $authorRepository
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        // Création d'un nouvel article
        $article = new Article;
        $article->setCreatedAt(new \DateTimeImmutable())
            ->setTitle($data->getTitle())
            ->setContent($data->getContent());

        // Vérification si l'auteur existe
        $similarAuthor = $this->authorRepository->findOneByEmail($data->getEmail()); // recherche de l'auteur via l'email renseigne dans la charge utile de la requête POST de création d'article

        if (!$similarAuthor) {
            dd('auteur inexistant');
        }

        $article->setAuthor($similarAuthor);

        // Enregistrement et envoi en base de données de l'article avec son auteur renseigné
        $this->entityManager->persist($article);
        $this->entityManager->flush();


        // Retourner une réponse à l'interface de l'API
        $articleDto = new ArticleResponseDto();

        $fullname = $article->getAuthor()->getFullname();

        $articleDto->setTitle($data->getTitle())
            // ->setContent($data->getContent())
            // ->setFirstname($article->getAuthor()->getFirstname())
            ->setAuthor($article->getAuthor()->getFullname());


        return $articleDto;
    }
}
