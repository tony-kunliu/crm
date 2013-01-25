<?php

namespace Oro\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\UserBundle\Entity\Group;
use Oro\Bundle\UserBundle\Form\Type\GroupType;

/**
 * @Route("/group")
 */
class GroupController extends Controller
{
   /**
    * Create group form
    *
    * @Route("/create", name="oro_user_group_create")
    * @Template("OroUserBundle:Group:edit.html.twig")
    */
    public function createAction()
    {
        return $this->editAction(new Group());
    }

   /**
    * Edit group form
    *
    * @Route("/edit/{id}", name="oro_user_group_edit", requirements={"id"="\d+"}, defaults={"id"=0})
    * @Template
    */
    public function editAction(Group $entity)
    {
        $request = $this->getRequest();
        $form    = $this->createForm(new GroupType(), $entity);

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($entity);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', 'Group successfully saved');

                return $this->redirect($this->generateUrl('oro_user_group_index'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

   /**
    * @Route("/remove/{id}", name="oro_user_group_remove", requirements={"id"="\d+"})
    */
    public function removeAction(Role $user)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($user);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Group successfully removed');

        return $this->redirect($this->generateUrl('oro_user_group_index'));
    }

    /**
     * @Route("/{page}/{limit}", name="oro_user_group_index", requirements={"page"="\d+","limit"="\d+"}, defaults={"page"=1,"limit"=20})
     * @Template
     */
    public function indexAction($page, $limit)
    {
        $query = $this
            ->getDoctrine()
            ->getEntityManager()
            ->createQuery('SELECT g FROM OroUserBundle:Group g');

        return array(
            'pager'  => $this->get('knp_paginator')->paginate($query, $page, $limit),
        );
    }
}