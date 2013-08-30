<?php

namespace CoastersWorld\Bundle\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CoastersWorld\Bundle\SiteBundle\Form\Type\CommentType;
use CoastersWorld\Bundle\SiteBundle\Entity\Comment;

class CommentController extends Controller
{
    public function newAction($id)
    {
        $news = $this->getNews($id);

        if (! $this->get('security.context')->isGranted('ROLE_USER')) {
            $uri = $this->get('router')->generate('coasters_world_news_view', array('slug' => $news->getSlug()), true);
            $this->getRequest()->getSession()->set('_security.secured_area.target_path', $uri);
            return $this->render('CoastersWorldSiteBundle:Security:redirectLogin.html.twig');
        }

        $comment = new Comment();
        $comment->setNews($news);
        $comment->setAuthor($this->getUser());
        $form   = $this->createForm(new CommentType(), $comment);

        return $this->render('CoastersWorldSiteBundle:Comment:edit.html.twig', array(
            'comment' => $comment,
            'form' => $form->createView()
        ));
    }

    public function createAction($id)
    {
        $news = $this->getNews($id);

        $comment  = new Comment();
        $comment->setNews($news);
        $comment->setAuthor($this->getUser());
        $request = $this->getRequest();
        $form    = $this->createForm(new CommentType(), $comment);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();

            $em->persist($comment);
            $em->flush();

            return $this->redirect($this->generateUrl('coasters_world_news_view', array(
                'slug' => $news->getSlug()
            )));
        }

        return $this->render('CoastersWorldSiteBundle:Comment:create.html.twig', array(
            'comment' => $comment,
            'form'    => $form->createView()
        ));
    }

    protected function getNews($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $news = $em->getRepository('CoastersWorldSiteBundle:News')->find($id);

        return $news;
    }

}
