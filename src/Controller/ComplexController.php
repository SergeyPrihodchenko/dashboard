<?php

namespace App\Controller;

use App\Entity\Calls;
use App\Entity\Mails;
use App\Form\Calls\CallsCreateType;
use App\Form\Mails\MailsCreateType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboBundle;

class ComplexController extends AbstractController
{
    #[Route('/complex/create', name: 'app_complex_create')]
    public function create(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $mailsForm = $this->formProcessed(
            MailsCreateType::class, Mails::class, $request, $managerRegistry, 'mails-form',
            'Почты'
        );

        if (get_class($mailsForm) === "Symfony\Component\HttpFoundation\Response") {
            return $mailsForm;
        }

        $callsForm = $this->formProcessed(
            CallsCreateType::class, Calls::class, $request, $managerRegistry, 'calls-form',
            'Звонки'
        );

        if (get_class($callsForm) === "Symfony\Component\HttpFoundation\Response") {
            return $callsForm;
        }


        return $this->render('complex/index.html.twig', [
            'mails_form' => $mailsForm,
            'calls_form' => $callsForm,
        ]);
    }

    private function formProcessed($form, $class, $request, $manager, $target, $message)
    {
        $form =  $this->createForm($form);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();

            $messageBlock = "";

            $entity = $manager->getRepository($class)
                ->findOneBy(['date' => $task->getDate(), 'site_id' => $task->getSiteId()->getId()]);

            if (is_null($entity)) {
                $entity = new $class();

                $entity->setDate($task->getDate());
                $entity->setCount($task->getCount());
                $entity->setSiteId($task->getSiteId());

                $messageBlock = "$message успешно добавлены.";
            } else {
                $entity->setCount($task->getCount());

                $messageBlock = "$message были обновлены.";
            }

            $manager->getManager()->persist($entity);

            try {
                $manager->getManager()->flush();

            } catch (\Exception $e) {
                $messageBlock = "При добавлении произошла ошибка. ". $e->getMessage();
            }

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

                return $this->render("complex/form.stream.html.twig", [
                    'target' => $target,
                    'form' => $form,
                    'message' => $messageBlock,
                ]);
            }
        }

        return $form;
    }
}
