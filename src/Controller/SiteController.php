<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\Site\SiteCreateType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboBundle;

class SiteController extends AbstractController
{
    #[Route('/site', name: 'app_site')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/site/create', name: 'app_site_create')]
    public function create(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createForm(SiteCreateType::class, null, [
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();

            $site = $managerRegistry->getRepository(Site::class)
                ->findOneBy(['name' => $task->getName()]);

            $template = "success";
            $options = [];

            if (is_null($site)) {

                $site = new Site();
                $site->setName($task->getName());
                $site->setHref($task->getHref());

                $managerRegistry->getManager()->persist($site);
                try {
                    $managerRegistry->getManager()->flush();
                    $options['message'] = "Сайт успешно добавлен.";

                } catch (\Exception $e) {
                    $options['message'] = "При добавлении произошла ошибка. ". $e->getMessage();
                }
            } else {

                $template = "error";
                $options['message'] = "Сайт уже существует.";
                $options['form'] = $form;
            }

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

                return $this->render("site/{$template}.stream.html.twig", $options);
            }
        }

        return $this->render('site/create.html.twig', [
            'form' => $form
        ]);
    }
}
