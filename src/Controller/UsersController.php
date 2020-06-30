<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    /**
     * @Route("/users/create", name="users_create")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return JsonResponse
     */
    public function create(EntityManagerInterface $em, Request $request)
    {
        $user = new User();
        $user->setUsername($request->request->get('username'));
        $em->persist($user);
        $em->flush();
        return $this->json([
            'success' => true,
            'message' => sprintf('Created %s succesfully!', $request->request->get('username'))
        ]);
    }

    /**
     * @Route("/users", name="users")
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function users(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        return $this->json(array_map(function(User $user) {
            return [
                'username' => $user->getUsername()
            ];
        }, $users));
    }

    /**
     * @Route("/users/{username}", name="user")
     * @param Connection $connection
     * @param string $username
     * @return JsonResponse
     */
    public function user(Connection $connection, string $username)
    {
        $users = $connection->fetchAll('SELECT * FROM "user" WHERE username = :username', ['username' => $username]);
        return $this->json($users);
    }
}
