<?php

namespace Bkstg\FOSUserBundle\Controller;

use Bkstg\CoreBundle\Controller\Controller;
use Bkstg\CoreBundle\Entity\Production;
use Bkstg\CoreBundle\Util\ProfileManagerInterface;
use Bkstg\FOSUserBundle\Entity\Profile;
use Bkstg\FOSUserBundle\Form\Type\ProfileType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProductionProfileController extends Controller
{
    /**
     * Show a list of production profiles for a production.
     *
     * @throws NotFoundHttpException    If the production does not exist.
     * @throws AccessDeniedException    If the current user is not a member.
     *
     * @param  string  $production_slug The production slug for this membership.
     * @param  Request $request         The request.
     *
     * @return Response                 The response.
     */
    public function indexAction(
        string $production_slug,
        AuthorizationCheckerInterface $auth,
        Request $request
    ) {
        // Lookup the production by production_slug.
        $production_repo = $this->em->getRepository(Production::class);
        if (null === $production = $production_repo->findOneBy(['slug' => $production_slug])) {
            throw new NotFoundHttpException();
        }

        // Check permissions for this action.
        if (!$auth->isGranted('GROUP_ROLE_USER', $production)) {
            throw new AccessDeniedException();
        }

        // Get profiles for active memberships.
        $profile_repo = $this->em->getRepository(Profile::class);
        $profiles = $profile_repo->findAllEnabled($production);

        // Render the response.
        return new Response($this->templating->render('@BkstgFOSUser/Profile/index.html.twig', [
            'profiles' => $profiles,
        ]));
    }

    /**
     * Creates a new global profile.
     *
     * To create a global profile the user must not already have a global
     * profile.
     *
     * @param  Request $request
     *   The request for form processing.
     * @param  TokenStorageInterface $token_storage
     *   The token storage for getting the current user.
     *
     * @return Response
     *   A rendered form.
     */
    public function createAction($production_slug, Request $request, TokenStorageInterface $token_storage)
    {
        // Lookup the production by production_slug.
        $production_repo = $this->em->getRepository(Production::class);
        if (null === $production = $production_repo->findOneBy(['slug' => $production_slug])) {
            throw new NotFoundHttpException();
        }

        // Check permissions for this action.
        if (!$auth->isGranted('GROUP_ROLE_USER', $production)) {
            throw new AccessDeniedException();
        }

        // Get the current user and check for a global profile.
        $user = $token_storage->getToken()->getUser();
        $profile_repo = $this->em->getRepository(Profile::class);
        if (null !== $profile = $profile_repo->findProfile($user)) {
            throw new AccessDeniedException($this->translator->trans('You can only have one global profile.'));
        }

        // Create a new profile with this user as the author.
        $profile = new Profile();
        $profile->setAuthor($user->getUsername());

        // Create and handle the form.
        $form = $this->form->create(ProfileType::class, $profile);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the profile and flush.
            $this->em->persist($profile);
            $this->em->flush();

            // Set success message and redirect.
            $this->session->getFlashBag()->add(
                'success',
                $this->translator->trans('Your global profile has been created.')
            );
            return new RedirectResponse($this->url_generator->generate(
                'bkstg_profile_show',
                ['profile_slug' => $profile->getSlug()]
            ));
        }

        // Render the profile form.
        return new Response($this->templating->render(
            '@BkstgFOSUser/Profile/create.html.twig',
            ['form' => $form->createView()]
        ));
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
    public function readAction($production_slug, $profile_slug)
    {

        // Lookup the production by production_slug.
        $production_repo = $this->em->getRepository(Production::class);
        if (null === $production = $production_repo->findOneBy(['slug' => $production_slug])) {
            throw new NotFoundHttpException();
        }

        // Check permissions for this action.
        if (!$auth->isGranted('GROUP_ROLE_USER', $production)) {
            throw new AccessDeniedException();
        }

        // Lookup the profile.
        $profile_repo = $this->em->getRepository(Profile::class);
        if (null === $profile = $profile_repo->findOneBy(['slug' => $profile_slug])) {
            throw new NotFoundHttpException();
        }

        // Render the response.
        return new Response($this->templating->render(
            '@BkstgFOSUser/Profile/show.html.twig',
            ['profile' => $profile]
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
        $production_slug,
        $id,
        Request $request,
        AuthorizationCheckerInterface $auth
    ) {

        // Lookup the production by production_slug.
        $production_repo = $this->em->getRepository(Production::class);
        if (null === $production = $production_repo->findOneBy(['slug' => $production_slug])) {
            throw new NotFoundHttpException();
        }

        // Check permissions for this action.
        if (!$auth->isGranted('GROUP_ROLE_USER', $production)) {
            throw new AccessDeniedException();
        }

        $profile_repo = $this->em->getRepository(Profile::class);
        if (null === $profile = $profile_repo->findOneBy(['id' => $id])) {
            throw new NotFoundHttpException();
        }

        if (!$auth->isGranted('edit', $profile)) {
            throw new AccessDeniedException();
        }

        $form = $this->form->create(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the profile and flush.
            $this->em->persist($profile);
            $this->em->flush();

            // Set success message and redirect.
            $this->session->getFlashBag()->add(
                'success',
                $this->translator->trans('Your global profile has been edited.')
            );

            return new RedirectResponse($this->url_generator->generate(
                'bkstg_profile_show',
                ['profile_slug' => $profile->getSlug()]
            ));
        }

        return new Response($this->templating->render(
            '@BkstgFOSUser/Profile/edit.html.twig',
            [
                'form' => $form->createView(),
                'profile' => $profile,
            ]
        ));
    }

    public function deleteAction($id)
    {
    }
}
