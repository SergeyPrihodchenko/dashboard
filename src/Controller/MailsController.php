<?php

namespace App\Controller;

use App\Entity\Mails;
use App\Form\Mails\MailsCreateType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboBundle;

class MailsController extends AbstractController
{
    #[Route('/mails', name: 'app_mails')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/mails/create', name: 'app_mails_create')]
    public function create(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createForm(MailsCreateType::class, null, [
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();

            $mails = $managerRegistry->getRepository(Mails::class)
                ->findOneBy(['date' => $task->getDate(), 'site_id' => $task->getSiteId()->getId()]);

            $template = "success";
            $options = [];

            if (is_null($mails)) {
                $mails = new Mails();

                $mails->setDate($task->getDate());
                $mails->setCount($task->getCount());
                $mails->setSiteId($task->getSiteId());

                $managerRegistry->getManager()->persist($mails);

                try {
                    $managerRegistry->getManager()->flush();
                    $options['message'] = "Почты успешно добавлены.";

                } catch (\Exception $e) {
                    $options['message'] = "При добавлении произошла ошибка. ". $e->getMessage();
                }
            } else {

                $template = "error";
                $options['message'] = "Почты уже существуют.";
                $options['form'] = $form;
            }

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

                return $this->render("mails/{$template}.stream.html.twig", $options);
            }
        }

        return $this->render('mails/create.html.twig', [
            'form' => $form
        ]);
    }
}
