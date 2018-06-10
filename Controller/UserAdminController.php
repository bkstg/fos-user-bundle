<?php

namespace Bkstg\FOSUserBundle\Controller;

use Bkstg\CoreBundle\Controller\Controller;
use Bkstg\FOSUserBundle\Entity\User;
use Bkstg\FOSUserBundle\Form\Type\UserType;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * User admin controller.
 *
 * Allows admins to manage the users within this backstage. This controller
 * should be protected by a firewall rule, as there are no access checks within
 * the actions.
 */
class UserAdminController extends Controller
{
    /**
     * Show a list of users.
     *
     * @param  Request            $request   The request.
     * @param  PaginatorInterface $paginator The paginator service.
     *
     * @return Response                      The rendered response.
     */
    public function indexAction(Request $request, PaginatorInterface $paginator)
    {
        // Can show either enabled or blocked.
        if ($request->query->has('status')
            && $request->query->get('status') == 'blocked') {
            $query = $this->em->getRepository(User::class)->getAllBlockedQuery();
        } else {
            $query = $this->em->getRepository(User::class)->getAllActiveQuery();
        }

        // Paginate the user query and render.
        $users = $paginator->paginate($query, $request->query->getInt('page', 1));
        return new Response($this->templating->render('@BkstgFOSUser/User/index.html.twig', [
            'users' => $users,
        ]));
    }

    /**
     * Create a new user for the backstage.
     *
     * @param  Request                 $request         The request.
     * @param  UserManagerInterface    $user_manager    The user manager service.
     * @param  TokenGeneratorInterface $token_generator The token generator service.
     *
     * @return ResponseInterface                        The response.
     */
    public function createAction(
        Request $request,
        UserManagerInterface $user_manager,
        TokenGeneratorInterface $token_generator
    ) {
        // Create a new enabled user.
        $user = $user_manager->createUser();
        $user->setEnabled(true);
        $user->setHasProfile(false);

        // Create and handle the form.
        $form = $this->form->create(UserType::class, $user);
        $form->handleRequest($request);

        // Form is submitted and valid.
        if ($form->isSubmitted() && $form->isValid()) {
            // Set a random password and default slug.
            $user->setPlainPassword(md5(uniqid($user->getUsername(), true)));
            $user->setConfirmationToken($token_generator->generateToken());
            $user->setSlug($user->getUsername());

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

    /**
     * Update a user.
     *
     * @param  int                  $id           The user id.
     * @param  Request              $request      The request.
     * @param  UserManagerInterface $user_manager The user manager service.
     *
     * @throws NotFoundHttpException              If the user is not found.
     *
     * @return ResponseInterface                  The response.
     */
    public function updateAction(
        $id,
        Request $request,
        UserManagerInterface $user_manager
    ) {
        // If the user doesn't exist throw a not found exception.
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

    /**
     * Delete a user.
     *
     * @param  int                  $id           The user id.
     * @param  Request              $request      The request.
     * @param  UserManagerInterface $user_manager The user manager service.
     *
     * @throws NotFoundHttpException              If the user is not found.
     *
     * @return ResponseInterface                  The response.
     */
    public function deleteAction(
        $id,
        Request $request,
        UserManagerInterface $user_manager
    ) {
        // Throw not found exception if the user does not exist.
        if (null === $user = $user_manager->findUserBy(['id' => $id])) {
            throw new NotFoundHttpException();
        }

        // Create an empty delete form.
        $form = $this
            ->form
            ->createBuilder()
            ->add('id', HiddenType::class, ['data' => $id])
            ->getForm();
        $form->handleRequest($request);

        // Delete the user.
        if ($form->isSubmitted() && $form->isValid()) {
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
