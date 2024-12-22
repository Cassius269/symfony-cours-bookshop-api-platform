<?php

namespace App\State;

use App\Entity\Author;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class InsertUserProcessor implements ProcessorInterface
{
    // Injection de dépandances
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Response
    {
        //Hasher le mot de passe entré par l'utilisateur
        $hashedPassword = $this->passwordHasher->hashPassword($data, $data->getPassword()); // dans le cadre d'une création d'un nouvel utilisateur, $data correspond à l'objet utilisateur

        // Mettre à jour le mot de passe de l'utilisateur par celui qui est hashé
        $data->setPassword($hashedPassword);

        // Envoyer en base de données le User recemment hashé
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        // Sérialiser les données avec le groupe de sérialisation afin de ne pas exposer les données sensibles
        $jsonData = $this->serializer->serialize($data, 'json', ['groups' => 'authors.read']);

        // Retourner une réponse JSON
        return new JsonResponse($jsonData, Response::HTTP_CREATED, [], true);
    }
}
