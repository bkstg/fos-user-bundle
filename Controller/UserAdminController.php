<?php

namespace Bkstg\FOSUserBundle\Controller;

use Bkstg\CoreBundle\Controller\Controller;
use Bkstg\FOSUserBundle\Entity\User;
use Bkstg\FOSUserBundle\Form\Type\UserType;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserAdminController extends Controller
{
    public function indexAction(Request $request)
    {
        // Can show either enabled or blocked.
        if ($request->query->has('status')
            && $request->query->get('status') == 'blocked') {
            $users = $this->em->getRepository(User::class)->findBy(['enabled' => false]);
        } else {
            $users = $this->em->getRepository(User::class)->findBy(['enabled' => true]);
        }

        // Render the list of users.
        return new Response($this->templating->render('@BkstgFOSUser/User/index.html.twig', [
            'users' => $users,
        ]));
    }

    public function createAction(
        Request $request,
        UserManagerInterface $user_manager,
        TokenGeneratorInterface $token_generator
    ) {
        // Create a new user.
        $user = $user_manager->createUser();

        // Create and handle the form.
        $form = $this->form->create(UserType::class, $user);
        $form->handleRequest($request);

        // Form is submitted and valid.
        if ($form->isSubmitted() && $form->isValid()) {
            // Set a random password.
            $user->setPlainPassword(md5(uniqid($user->getUsername(), true)));
            $user->setConfirmationToken($token_generator->generateToken());

            // Persist the user
            $user_manager->updateUser($user);

            // Set success message and redirect.
            $this->session->getFlashBag()->add(
                'success',
                $this->translator->trans('User "%user%" created.', [
                    '%user%' => $user->getUsername(),
                ])
            );
            return new RedirectResponse($this->url_generator->generate('bkstg_user_admin_list'));
        }

        // Render the form.
        return new Response($this->templating->render('@BkstgFOSUser/User/create.html.twig', [
            'form' => $form->createView(),
        ]));
    }

    public function updateAction(
        $id,
        Request $request,
        UserManagerInterface $user_manager
    ) {
        if (null === $user = $user_manager->findUserBy(['id' => $id])) {
            throw new NotFoundHttpException();
        }

        // Create and handle the form.
        $form = $this->form->create(UserType::class, $user);
        $form->handleRequest($request);

        // Form is submitted and valid.
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the user
            $user_manager->updateUser($user);

            // Set success message and redirect.
            $this->session->getFlashBag()->add(
                'success',
                $this->translator->trans('User "%user%" updated.', [
                    '%user%' => $user->getUsername(),
                ])
            );
            return new RedirectResponse($this->url_generator->generate('bkstg_user_admin_list'));
        }

        // Render the form.
        return new Response($this->templating->render('@BkstgFOSUser/User/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]));
    }

    public function deleteAction(
        $id,
        Request $request,
        UserManagerInterface $user_manager
    ) {
        if (null === $user = $user_manager->findUserBy(['id' => $id])) {
            throw new NotFoundHttpException();
        }

        // Create an empty form.
        $form = $this->form->createBuilder()->getForm();
        $form->handleRequest($request);

        // Delete the user.
        if ($form->isValid() && $form->isSubmitted()) {
            $user_manager->deleteUser($user);

            // Set success message and redirect.
            $this->session->getFlashBag()->add(
                'success',
                $this->translator->trans('User "%user%" deleted.', [
                    '%user%' => $user->getUsername(),
                ])
            );
            return new RedirectResponse($this->url_generator->generate('bkstg_user_admin_list'));
        }

        // Render the form.
        return new Response($this->templating->render('@BkstgFOSUser/User/delete.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]));
    }
}
