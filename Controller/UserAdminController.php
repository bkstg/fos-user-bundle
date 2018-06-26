<?php

namespace Bkstg\FOSUserBundle\Controller;

use Bkstg\CoreBundle\Controller\Controller;
use Bkstg\FOSUserBundle\Entity\User;
use Bkstg\FOSUserBundle\Form\UserType;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserAdminController extends Controller
{
    /**
     * Show a list of active users.
     *
     * @param  Request            $request   The request.
     * @param  PaginatorInterface $paginator The paginator service.
     * @return Response
     */
    public function indexAction(Request $request, PaginatorInterface $paginator): Response
    {
        // Paginate the user query and render.
        $query = $this->em->getRepository(User::class)->findAllActiveQuery();
        $users = $paginator->paginate($query, $request->query->getInt('page', 1));
        return new Response($this->templating->render('@BkstgFOSUser/UserAdmin/index.html.twig', [
            'users' => $users,
        ]));
    }

    /**
     * Show a list of archived users.
     *
     * @param  Request            $request   The request.
     * @param  PaginatorInterface $paginator The paginator service.
     * @return Response
     */
    public function archiveAction(Request $request, PaginatorInterface $paginator): Response
    {
        // Paginate the user query and render.
        $query = $this->em->getRepository(User::class)->findAllBlockedQuery();
        $users = $paginator->paginate($query, $request->query->getInt('page', 1));
        return new Response($this->templating->render('@BkstgFOSUser/UserAdmin/archive.html.twig', [
            'users' => $users,
        ]));
    }

    /**
     * Create a new user for the backstage.
     *
     * @param  Request                 $request         The request.
     * @param  UserManagerInterface    $user_manager    The user manager service.
     * @param  TokenGeneratorInterface $token_generator The token generator service.
     * @return Response
     */
    public function createAction(
        Request $request,
        UserManagerInterface $user_manager,
        TokenGeneratorInterface $token_generator
    ): Response {
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
                $this->translator->trans('user.created', [
                    '%user%' => $user->getUsername(),
                ])
            );
            return new RedirectResponse($this->url_generator->generate('bkstg_user_admin_index'));
        }

        // Render the form.
        return new Response($this->templating->render('@BkstgFOSUser/UserAdmin/create.html.twig', [
            'form' => $form->createView(),
        ]));
    }

    /**
     * Update a user.
     *
     * @param  integer              $id           The id of the user.
     * @param  Request              $request      The incoming request.
     * @param  UserManagerInterface $user_manager The user manager service.
     * @throws NotFoundHttpException              When the user is not found.
     * @return Response
     */
    public function updateAction(
        int $id,
        Request $request,
        UserManagerInterface $user_manager
    ): Response {
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
                $this->translator->trans('user.updated', [
                    '%user%' => $user->getUsername(),
                ])
            );
            return new RedirectResponse($this->url_generator->generate('bkstg_user_admin_index'));
        }

        // Render the form.
        return new Response($this->templating->render('@BkstgFOSUser/UserAdmin/update.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]));
    }

    /**
     * Delete a user.
     *
     * @param  integer              $id           The id of the user.
     * @param  Request              $request      The incoming request.
     * @param  UserManagerInterface $user_manager The user manager service.
     * @throws NotFoundHttpException              When the user is not found.
     * @return Response
     */
    public function deleteAction(
        int $id,
        Request $request,
        UserManagerInterface $user_manager
    ): Response {
        // Throw not found exception if the user does not exist.
        if (null === $user = $user_manager->findUserBy(['id' => $id])) {
            throw new NotFoundHttpException();
        }

        // Create an empty delete form.
        $form = $this->form->createBuilder()->getForm();
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
            return new RedirectResponse($this->url_generator->generate('bkstg_user_admin_index'));
        }

        // Render the form.
        return new Response($this->templating->render('@BkstgFOSUser/UserAdmin/delete.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]));
    }
}
