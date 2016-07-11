<?php

namespace AppBundle\Controller;

use Proxies\__CG__\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Product;
use AppBundle\Form\SearchType;


class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager()->getRepository('AppBundle:Product');
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        $result = $em->createQueryBuilder('p');
        $orderBy = 'name';
        if (!$isAdmin) {
            $userId = $this->getUser()->getId();
            $result
                ->Where('p.user = :user')
                ->setParameter('user', $userId);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $nameSearch = $form['name']->getData();
            $orderBy = $form['isSorting']->getData();
            $result
                ->andWhere('p.name LIKE :name')
                ->setParameter('name', $nameSearch.'%');
        }
        $productsData = $result
            ->orderBy('p.'.$orderBy)
            ->getQuery()
            ->getResult();
        return $this->render('AppBundle:Main:tableProducts.html.twig', array(
            'productsData' => $productsData,
            'form' => $form->createView(),
            ));

    }
}
