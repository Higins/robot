<?php
namespace App\Controller;

use App\Entity\Robot;
use App\Form\RobotType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class RobotController extends AbstractController
{
    #[Route('/robot', name: 'robot_list')]
    public function index(EntityManagerInterface $em, ): Response
    {
        $repository = $em->getRepository(Robot::class);
        $robots = $repository->findAllActive();

        return $this->render('robot/index.html.twig', [
            'robots' => $robots,
        ]);
    }
    #[Route('/robot/fight', methods: ['POST'], name: 'robot_fight')]
    public function fight(Request $request, EntityManagerInterface $em): Response
    {
        // Az összes kiválasztott robot azonosítójának lekérése
        $robotIds = $request->request->all('selected_robots');

        if (!is_array($robotIds) || count($robotIds) !== 2) {
            $this->addFlash('error', 'Pontosan két robotot kell kiválasztani!');
            return $this->redirectToRoute('robot_list');
        }

        $robots = $em->getRepository(Robot::class)->findBy(['id' => $robotIds]);

        if (count($robots) !== 2) {
            $this->addFlash('error', 'A kiválasztott robotok nem találhatók!');
            return $this->redirectToRoute('robot_list');
        }

        $winner = $this->determineWinner($robots[0], $robots[1]);

        $this->addFlash('success', sprintf('%s nyert a harcban!', $winner->getName()));

        return $this->render('robot/result.html.twig', [
            'winner' => $winner,
            'robots' => $robots,
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

        // Ha az erő egyenlő, akkor az újabb robot nyer (created_at alapján)
        return $robot1->getCreatedAt() > $robot2->getCreatedAt() ? $robot1 : $robot2;
    }

    #[Route('/robot/delete/{id}', name: 'robot_delete', methods: ['POST'])]
    public function delete(int $id, EntityManagerInterface $em): Response
    {
        $robot = $em->getRepository(Robot::class)->find($id);

        if (!$robot) {
            $this->addFlash('error', 'A robot nem található!');
            return $this->redirectToRoute('robot_list');
        }

        $robot->setDeletedAt(new \DateTimeImmutable());

        $em->flush();

        $this->addFlash('success', sprintf('%s sikeresen törölve lett!', $robot->getName()));

        return $this->redirectToRoute('robot_list');
    }

    #[Route('/robot/new', name: 'robot_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $robot = new Robot();
        $form = $this->createForm(RobotType::class, $robot);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $robot->setCreatedAt(new \DateTimeImmutable());

            $em->persist($robot);
            $em->flush();

            $this->addFlash('success', 'Új robot sikeresen hozzáadva!');

            return $this->redirectToRoute('robot_list');
        }

        return $this->render('robot/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/robot/edit/{id}', name: 'robot_edit')]
    public function edit(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $robot = $em->getRepository(Robot::class)->find($id);

        if (!$robot) {
            $this->addFlash('error', 'A robot nem található!');
            return $this->redirectToRoute('robot_list');
        }

        $form = $this->createForm(RobotType::class, $robot);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $robot->setUpdatedAt(new \DateTimeImmutable());

            $em->flush();

            $this->addFlash('success', sprintf('%s sikeresen módosítva lett!', $robot->getName()));

            return $this->redirectToRoute('robot_list');
        }

        return $this->render('robot/edit.html.twig', [
            'form' => $form->createView(),
            'robot' => $robot,
        ]);
    }
}