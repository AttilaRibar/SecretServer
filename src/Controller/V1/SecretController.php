<?php

namespace App\Controller\V1;

use App\Entity\Secret;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\ByteString;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/v1')]
class SecretController extends AbstractController
{

    protected SerializerInterface $serializer;

    protected ValidatorInterface $validator;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    #[Route('/secret', name: 'addSecret', methods: ['POST'])]
    #[OA\Post(
        description: 'Save a secret with restrictions on views or expiration time.',
        summary: 'Add a new secret',
        requestBody: new OA\RequestBody(
            content: [
                'application/x-www-form-urlencoded' => new OA\MediaType(
                    mediaType: 'application/x-www-form-urlencoded',
                    schema: new OA\Schema(
                        required: ['secret', 'expireAfterViews', 'expireAfter'],
                        properties: [
                            new OA\Property(
                                property: 'secret',
                                description: "The secret to save",
                                type: 'string'
                            ),
                            new OA\Property(
                                property: 'expireAfterViews',
                                description: "The secret won't be available after the given number of views. Must be greater than 0.",
                                type: 'integer',
                                format: 'int32'
                            ),
                            new OA\Property(
                                property: 'expireAfter',
                                description: "The secret won't be available after the given time in minutes. 0 means never expires.",
                                type: 'integer',
                                format: 'int32',
                            )
                        ],
                        type: 'object'
                    )
                )
            ]
        ),
    )]
    #[OA\Tag(name: 'secret')]
    #[OA\Response(response: 200,
        description: 'Successful operation',
        content: [
            'application/json' => new OA\JsonContent(
                ref: new Model(type: Secret::class)
            ),
            'application/xml' => new OA\XmlContent(
                ref: new Model(type: Secret::class)
            ),
        ])]
    #[OA\Response(response: 405, description: 'Invalid input')]
    public function addSecret(Request $request, EntityManagerInterface $entityManager): Response
    {
        $secretText = $request->request->get('secret');
        $expireAfterViews = $request->request->get('expireAfterViews');
        $expireAfter = $request->request->get('expireAfter');

        if (!is_string($secretText) || !is_numeric($expireAfterViews) || !is_numeric($expireAfter)) {
            return new JsonResponse(['error' => 'Invalid input'], 405);
        }

        $secretEntity = new Secret();
        $secretEntity->setHash(ByteString::fromRandom(64)->toString());
        $secretEntity->setSecretText($secretText);
        $secretEntity->setCreatedAt(new \DateTimeImmutable());
        $secretEntity->setExpirationTime($expireAfter);
        $secretEntity->setRemainingViews($expireAfterViews);

        if (count($this->validator->validate($secretEntity)) > 0) {
            return new JsonResponse(['error' => 'Invalid input'], 405);
        }

        $entityManager->persist($secretEntity);
        $entityManager->flush();

        $contentType = $request->getAcceptableContentTypes();
        if (in_array('application/xml', $contentType)) {
            $xmlContent = $this->serializer->serialize($secretEntity, 'xml');
            return new Response($xmlContent, 200, ['Content-Type' => 'application/xml']);
        }

        $jsonContent = $this->serializer->serialize($secretEntity, 'json');
        return new Response($jsonContent, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/secret/{hash}', name: 'getSecretByHash', methods: ['GET'])]
    #[OA\Get(description: "Returns a single secret", summary: 'Find a secret by hash')]
    #[OA\Parameter(name: 'hash', description: "Unique hash to identify the secret", in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Tag(name: 'secret')]
    #[OA\Response(
        response: 200,
        description: 'successful operation',
        content: [
            'application/json' => new OA\JsonContent(
                ref: new Model(type: Secret::class)
            ),
            'application/xml' => new OA\XmlContent(
                ref: new Model(type: Secret::class)
            )
        ]
    )]
    #[OA\Response(response: 404, description: "Secret not found")]
    public function getSecretByHash(Request $request, EntityManagerInterface $entityManager, string $hash): Response
    {
        return new Response('Not implemented yet', Response::HTTP_NOT_IMPLEMENTED);
    }
}
