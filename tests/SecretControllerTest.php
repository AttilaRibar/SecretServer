<?php

namespace App\Tests;

use App\Controller\V1\SecretController;
use App\Entity\Secret;
use App\Repository\SecretRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecretControllerTest extends WebTestCase
{
    /** @var SecretController Mocked controller */
    private SecretController $secretController;

    /** @var ValidatorInterface Mocked validator */
    private ValidatorInterface $validator;

    /** @var EntityManagerInterface Mocked entity manager */
    private EntityManagerInterface $entityManager;

    /**
     * It runs before test function
     *
     * @throws Exception
     */
    protected function setUp(): void
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->secretController = new SecretController($serializer, $this->validator);
    }

    /**
     * Tests a valid request, checks the status code, content and content header
     *
     * @return void
     */
    public function testAddSecretValidInput(): void
    {
        $request = Request::create('/v1/secret', 'POST', [
            'secret' => 'Very secret',
            'expireAfterViews' => 5,
            'expireAfter' => 10
        ]);
        $request->headers->set('Accept', 'application/json');

        $this->validator->method('validate')->willReturn(new ConstraintViolationList());
        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $response = $this->secretController->addSecret($request, $this->entityManager);

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    /**
     * Tests an invalid request
     *
     * @return void
     */
    public function testAddSecretInvalidInput(): void
    {
        $request = Request::create('/v1/secret', 'POST', [
            'secret' => 'my-secret',
            'expireAfterViews' => 'invalid',
            'expireAfter' => 10
        ]);

        $response = $this->secretController->addSecret($request, $this->entityManager);

        $this->assertEquals(405, $response->getStatusCode());
    }

    /**
     * Test an empty request
     *
     * @return void
     */
    public function testAddSecretWithEmptyRequest(): void
    {
        $request = Request::create('/v1/secret', 'POST', []);

        $response = $this->secretController->addSecret($request, $this->entityManager);

        $this->assertEquals(405, $response->getStatusCode());
    }

    /**
     * Tests the accept header
     *
     * @return void
     */
    public function testAddSecretWithUnknownAcceptHeader(): void
    {
        $request = Request::create('/v1/secret', 'POST', [
            'secret' => 'my-secret',
            'expireAfterViews' => 5,
            'expireAfter' => 10
        ]);
        $request->headers->set('Accept', 'application/unknown');

        $this->validator->method('validate')->willReturn(new ConstraintViolationList());
        $response = $this->secretController->addSecret($request, $this->entityManager);

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    /**
     * Tests the endpoint with an existing hash
     *
     * @return void
     * @throws Exception
     */
    public function testGetSecretByHashFound(): void
    {
        $hash = 'validHash';
        $secret = new Secret('my-secret');
        $secret->setHash($hash);
        $secret->setRemainingViews(5);

        $repository = $this->createMock(SecretRepository::class);
        $this->entityManager->method('getRepository')->willReturn($repository);
        $repository->method('findOneByHash')->with($hash)->willReturn($secret);

        $request = Request::create('/v1/secret/' . $hash, 'GET');
        $request->headers->set('Accept', 'application/xml');

        $response = $this->secretController->getSecretByHash($request, $this->entityManager, $hash);

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals('application/xml', $response->headers->get('Content-Type'));
    }

    /**
     * Tests the endpoint with not existing hash
     *
     * @return void
     * @throws Exception
     */
    public function testGetSecretByHashNotFound(): void
    {
        $hash = 'invalidHash';

        $repository = $this->createMock(SecretRepository::class);
        $this->entityManager->method('getRepository')->willReturn($repository);
        $repository->method('findOneByHash')->with($hash)->willReturn(null);

        $request = Request::create('/v1/secret/' . $hash, 'GET');
        $request->headers->set('Accept', 'application/json');

        $response = $this->secretController->getSecretByHash($request, $this->entityManager, $hash);

        $this->assertEquals(404, $response->getStatusCode());

        $this->assertEmpty($response->getContent());
    }
}