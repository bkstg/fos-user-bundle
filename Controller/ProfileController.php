<?php

namespace Bkstg\FOSUserBundle\Controller;

use Bkstg\CoreBundle\Controller\Controller;
use Bkstg\CoreBundle\Util\ProfileManagerInterface;
use Bkstg\FOSUserBundle\Entity\User;
use Bkstg\FOSUserBundle\Form\Type\ProfileType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends Controller
{
    /**
     * Show a directory listing of users with profile information.
     *
     * @param  Request            $request   The request.
     * @param  PaginatorInterface $paginator The paginator service.
     * @return Response                      The rendered response.
     */
    public function indexAction(
        Request $request,
        PaginatorInterface $paginator,
        TokenStorageInterface $token_storage
    ) {
        // Get the user repo and find all active users with profiles.
        $user_repo = $this->em->getRepository(User::class);
        $query = $user_repo->findAllActive(true);

        // If the current user has no profile set a message.
        $user = $token_storage->getToken()->getUser();
        if (!$user->hasProfile()) {
            $this->session->getFlashBag()->add(
                'warning',
                $this->translator->trans(
                    'You have not created a profile yet, <a href="%url%">click here</a> to create one now.',
                    ['%url%' => $this->url_generator->generate('bkstg_profile_edit', ['id' => $user->getId()])]
                )
            );
        }

        $users = $paginator->paginate($query, $request->query->getInt('page', 1));
        return new Response($this->templating->render('@BkstgFOSUser/Profile/index.html.twig', [
            'users' => $users,
        ]));
    }

    /**
     * Render a global profile.
     *
     * @param int $id
     *   The profile id to render.
     *
     * @return Response
     *   The rendered profile.
     */
    public function readAction($profile_slug)
    {
        // Lookup the profile.
        $user_repo = $this->em->getRepository(User::class);
        if (null === $user = $user_repo->findOneBy(['slug' => $profile_slug])) {
            throw new NotFoundHttpException();
        }

        // Render the response.
        return new Response($this->templating->render(
            '@BkstgFOSUser/Profile/show.html.twig',
            ['user' => $user]
        ));
    }

    /**
     * Edit an existing global profile.
     *
     * To edit an existing global profile the user must be either:
     * - The author of the profile, OR
     * - Have the global ROLE_ADMIN role.
     *
     * @param int $id
     *   The id of the profile to edit.
     * @param Request $request
     *   The Request for form processing.
     *
     * @return Response
     *   The rendered form or a redirect.
     */
    public function updateAction(
        $id,
        Request $request,
        AuthorizationCheckerInterface $auth
    ) {
        $user_repo = $this->em->getRepository(User::class);
        if (null === $user = $user_repo->findOneBy(['id' => $id])) {
            throw new NotFoundHttpException();
        }

        if (!$auth->isGranted('edit', $user)) {
            throw new AccessDeniedException();
        }


        $form = $this->form->create(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the profile and flush.
            $user->setHasProfile(true);
            $this->em->persist($user);
            $this->em->flush();

            // Set success message and redirect.
            $this->session->getFlashBag()->add(
                'success',
                $this->translator->trans('Your global profile has been edited.')
            );

            return new RedirectResponse($this->url_generator->generate(
                'bkstg_profile_show',
                ['profile_slug' => $user->getSlug()]
            ));
        }

        return new Response($this->templating->render(
            '@BkstgFOSUser/Profile/edit.html.twig',
            [
                'form' => $form->createView(),
                'profile' => $user,
            ]
        ));
    }
}
