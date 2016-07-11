<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;

class ProductController extends Controller
{

    public function addAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $user = $this->getUser();
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $product->getThumbnail();
            if(isset($file)) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $this->container->getParameter('thumbnail_dir'),
                    $fileName
                );
                $product->setThumbnail($fileName);
            }
            $product->setUser($user);
            $em->persist($product);
            $em->flush();

            return $this->redirect($this->generateUrl('homePage'));
        }

        return $this->render('AppBundle:Product:add.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
            'form' => $form->createView(),
        ));
    }
    public function editAction(Request $request, $id)
    {
        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        $em = $this->getDoctrine()->getManager();
        $product = $this->getDoctrine()
            ->getRepository('AppBundle:Product')
            ->find($id);
        $userId = $this->getUser()->getId();
        $productUserId = $product->getUser()->getId();

        if($userId == $productUserId or $isAdmin) {
            //форма ругалась на файл (не лучшее решение)
            $lastFile = $product->getThumbnail();
            if (isset($lastFile)) {
                $fs = new Filesystem();
                if($fs->exists($this->getParameter('thumbnail_dir') . '/' . $lastFile)) {
                    $product->setThumbnail(
                        new File($this->getParameter('thumbnail_dir') . '/' . $lastFile)
                    );
                }
                else {
                    $fs->touch($this->getParameter('thumbnail_dir') . '/nofile.jpg');
                    $product->setThumbnail(
                        new File($this->getParameter('thumbnail_dir') . '/nofile.jpg'));
                }

            }
            $form = $this->createForm(ProductType::class, $product);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $file = $product->getThumbnail();
                if (isset($file)) {
                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                    $file->move(
                        $this->container->getParameter('thumbnail_dir'),
                        $fileName
                    );
                    $product->setThumbnail($fileName);
                }
                $em->persist($product);
                $em->flush();

                return $this->redirect($this->generateUrl('homePage'));
            }

            return $this->render('AppBundle:Product:add.html.twig', array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..'),
                'form' => $form->createView(),
            ));
        }
        else {
            throw $this->createAccessDeniedException('Access Denied');
        }
    }
    public function deleteAction(Request $request, $id)
    {
        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        $em = $this->getDoctrine()->getManager();
        if (!$id) {
            throw $this->createNotFoundException('No product found');
        }

        $product = $this->getDoctrine()
            ->getRepository('AppBundle:Product')
            ->find($id);
        $userId = $this->getUser()->getId();
        $productId = $product->getUser()->getId();
        if($userId == $productId or $isAdmin) {
            $em->remove($product);
            $em->flush();
        }
        else {
            throw $this->createAccessDeniedException('Access Denied');
        }

        return $this->redirect($this->generateUrl('homePage'));
    }
}