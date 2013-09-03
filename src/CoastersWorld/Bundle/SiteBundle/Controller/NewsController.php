<?php

namespace CoastersWorld\Bundle\SiteBundle\Controller;

use CoastersWorld\Bundle\SiteBundle\Entity\News;
use CoastersWorld\Bundle\SiteBundle\Entity\Comment;
use CoastersWorld\Bundle\SiteBundle\Form\Type\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class NewsController extends Controller
{
    public function listAction($page)
    {
        $queryNews = $this->getDoctrine()
            ->getEntityManager()
            ->getRepository('CoastersWorldSiteBundle:News')
            ->findAllOrderedByDateDesc()
        ;

        $paginator = $this->get('knp_paginator');
        $listNews = $paginator->paginate(
            $queryNews,
            $page,
            1
        );

        if (count($listNews) == 0) {
            //@todo exception
        }

        return $this->render('CoastersWorldSiteBundle:News:list.html.twig', array(
            'listNews' => $listNews
        ));
    }

    public function editAction($id = null)
    {
        $em = $this->getDoctrine()->getEntityManager();
        if (null !== $id) {
            $news = $em->getRepository('CoastersWorldSiteBundle:News')->find($id);
            $action = $this->generateUrl('coasters_world_news_edit', array('id' => $id));
        } else {
            $news = new News();
            $action = $this->generateUrl('coasters_world_news_new');
        }

        $form    = $this->createForm('news_type', $news, array(
            'action' => $action,
            'method' => 'POST',
        ));
        $request = $this->getRequest();

        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                $em->persist($news);
                $em->flush();

                return $this->redirect($this->generateUrl('coasters_world_news_list'));
            }
        }

        return $this->render('CoastersWorldSiteBundle:News:edit.html.twig', array(
            'form' => $form->createView(),
            'news' => $news,
        ));
    }

    public function viewAction($slug)
    {
        $news = $this->getDoctrine()
            ->getEntityManager()
            ->getRepository('CoastersWorldSiteBundle:News')
            ->findOneBy(array('slug' => $slug))
        ;

        if (count($news) == 0) {
            throw new NotFoundHttpException("No news was found");
        }

        return $this->render('CoastersWorldSiteBundle:News:view.html.twig', array(
            'news' => $news
        ));
    }
}