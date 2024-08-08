<?php
namespace App\Controller\Api;

use App\Controller\RobotController;
use App\Entity\Robot;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api', name: 'api_')]
class ApiRobotController extends AbstractController
{
    #[Route('/robot/fight', methods: ['GET'], name: 'robot_fight')]
    public function fight(Request $request, EntityManagerInterface $em, RobotController $robotController): JsonResponse
    {
        $robotId1 = $request->query->get('robot_id1');
        $robotId2 = $request->query->get('robot_id2');

         // Ellenőrizzük, hogy a két ID be van-e állítva
         if (is_int($robotId1) || is_int($robotId2)) {
            return new JsonResponse(['error' => 'Két robot azonosítóját kell megadni, numerikus formátumban!'], 400);
        }

        // Ellenőrizzük, hogy a két ID be van-e állítva
        if (!$robotId1 || !$robotId2) {
            return new JsonResponse(['error' => 'Két robot azonosítóját kell megadni!'], 400);
        }

        // Ellenőrizzük, hogy az ID-k különbözőek-e
        if ($robotId1 === $robotId2) {
            return new JsonResponse(['error' => 'Két különböző robotot kell kiválasztani!'], 400);
        }

        $robots = $em->getRepository(Robot::class)->findBy(['id' => [$robotId1, $robotId2], 'deleted_at' => null]);

        if (count($robots) !== 2) {
            return new JsonResponse(['error' => 'A kiválasztott robotok nem találhatók!'], 404);
        }

        $winner = $this->determineWinner($robots[0], $robots[1]);

        return new JsonResponse([
            'winner' => [
                'id' => $winner->getId(),
                'name' => $winner->getName(),
                'power' => $winner->getPower()
            ]
        ]);
    }
    private function determineWinner(Robot $robot1, Robot $robot2): Robot
    {
        if ($robot1->getPower() > $robot2->getPower()) {
            return $robot1;
        }

        if ($robot2->getPower() > $robot1->getPower()) {
            return $robot2;
        }

    }
}