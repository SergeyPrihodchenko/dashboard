<?php

namespace App\Controller;

use App\Entity\Calls;
use App\Form\Calls\CallsCreateType;
use App\Form\Calls\CreateType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboBundle;

class CallsController extends AbstractController
{
    #[Route('/calls', name: 'app_calls')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/calls/create', name: 'app_calls_create')]
    public function create(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createForm(CallsCreateType::class, null, [
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Calls $task */
            $task = $form->getData();

            $calls = $managerRegistry->getRepository(Calls::class)
                ->findOneBy(['date' => $task->getDate(), 'site_id' => $task->getSiteId()->getId()]);

            $template = "success";
            $options = [];

            if (is_null($calls)) {
                $calls = new Calls();

                $calls->setDate($task->getDate());
                $calls->setCount($task->getCount());
                $calls->setSiteId($task->getSiteId());

                $managerRegistry->getManager()->persist($calls);

                try {
                    $managerRegistry->getManager()->flush();
                    $options['message'] = "Звонки успешно добавлены.";

                } catch (\Exception $e) {
                    $options['message'] = "При добавлении произошла ошибка. ". $e->getMessage();
                }
            } else {

                $template = "error";
                $options['message'] = "Звонки уже существуют.";
                $options['form'] = $form;
            }

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

                return $this->render("calls/{$template}.stream.html.twig", $options);
            }
        }

        return $this->render('mails/create.html.twig', [
            'form' => $form
        ]);
    }
}
