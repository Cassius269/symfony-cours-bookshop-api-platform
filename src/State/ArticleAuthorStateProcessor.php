<?php

namespace App\State;

use App\Entity\Article;
use App\Dto\ArticleResponseDto;
use ApiPlatform\Metadata\Operation;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface as ValidatorValidatorInterface;

class ArticleAuthorStateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AuthorRepository $authorRepository,
        private ValidatorValidatorInterface $validator
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
            // Générer une erreur 404 si l'auteur n'existe pas
            throw new NotFoundHttpException('Auteur inexistant');
        }

        $article->setAuthor($similarAuthor);

        // Validation des données avant envoi en base de données
        $errors = $this->validator->validate($article); // rechercher les erreurs ne remplissant pas les contraintes de validation des données de l'entité Article

        if (count($errors) > 0) { // s'il y a des erreurs trouvées
            $errorMessages = [];

            // Générer une erreur 400 (= "bad request") avec les messages d'erreur détaillés
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }
            throw new BadRequestHttpException(json_encode($errorMessages));
        }

        // Enregistrement et envoi en base de données de l'article avec son auteur renseigné
        $this->entityManager->persist($article);
        $this->entityManager->flush();


        // Retourner une réponse à l'interface de l'API
        $articleDto = new ArticleResponseDto();

        $articleDto->setTitle($data->getTitle())
            // ->setContent($data->getContent())
            // ->setFirstname($article->getAuthor()->getFirstname())
            ->setAuthor($article->getAuthor()->getFullname());

        return $articleDto; // renvoyer la ressource nouvellement créé au client (navigateur, utilisateur par exemple) en passant par le DTO
    }
}
