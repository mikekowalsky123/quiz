<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ChangePasswordFormType;
use App\Form\ChangePersonalFormType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/edit-profile/{info}", name="app_edit_profile")
     */
    public function editProfile(Request $request, UserPasswordEncoderInterface $passwordEncoder, string $info = 'pass') {
        if(!$this->getUser())
            return $this->redirectToRoute('app_login');
        
        $editAccount = new User();
        switch($info) {
            case 'pass':
                $form = $this->createForm(ChangePasswordFormType::class, $editAccount);
                break;
            case 'personal':
                $form = $this->createForm(ChangePersonalFormType::class, $editAccount);
                break;
            default:
                return $this->redirectToRoute('app_homepage');
        }

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            if($info == 'pass') {
                $entityManager = $this->getDoctrine()->getManager();

                $changePass = $this->getUser();
                $changePass->setPassword(
                    $passwordEncoder->encodePassword(
                        $changePass,
                        $form->get('changePassword')->getData()
                    )
                );

            }
            else if($info == 'personal') {
                $entityManager = $this->getDoctrine()->getManager();

                $changePersonal = $this->getUser();
                $newName = $form->get('firstName')->getData();
                $changePersonal->setFirstName($newName);
            }
            $entityManager->flush();
            return $this->redirectToRoute('app_edit_profile_success');
        }
        /*$changePass = new User();
        $formChangePass = $this->createForm(ChangePasswordFormType::class, $changePass);
        $formChangePass->handleRequest($request);
        
        if ($formChangePass->isSubmitted() && $formChangePass->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $changePass = $this->getUser();
            $changePass->setPassword(
                $passwordEncoder->encodePassword(
                    $changePass,
                    $formChangePass->get('changePassword')->getData()
                )
            );

            $entityManager->flush();

            return $this->redirectToRoute('app_edit_profile_success');
        }

        $changePersonal = new User();
        $formChangePersonal = $this->createForm(ChangePersonalFormType::class, $changePersonal);
        $formChangePersonal->handleRequest($request);

        if($formChangePersonal->isSubmitted() && $formChangePersonal->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $changePersonal = $this->getUser();
            $newName = $formChangePersonal->get('firstName')->getData();
            $changePersonal->setFirstName($newName);

            $entityManager->flush();

            return $this->redirectToRoute('app_edit_profile_success');
        }*/

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'title' => 'Edycja profilu',
        ]);
    }

    /**
     * @Route("/edit-profile/success", name="app_edit_profile_success")
     */
    public function editProfileSuccess() {
        return $this->render('user/success.html.twig', [
            'title' => 'Edycja profilu zakończona sukcesem',
        ]);
    }
}