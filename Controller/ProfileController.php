<?php

namespace Bkstg\FOSUserBundle\Controller;

use Bkstg\CoreBundle\Controller\Controller;
use Bkstg\FOSUserBundle\Entity\User;
use Bkstg\FOSUserBundle\Form\Type\ProfileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProfileController extends Controller
{
    /**
     * Render a global profile.
     *
     * @param int $id
     *   The profile id to render.
     *
     * @return Response
     *   The rendered profile.
     */
    public function readAction(TokenStorageInterface $token_storage)
    {
        // Render the response.
        return new Response($this->templating->render(
            '@BkstgFOSUser/Profile/show.html.twig',
            ['user' => $token_storage->getToken()->getUser()]
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
        Request $request,
        TokenStorageInterface $token_storage
    ) {
        $user = $token_storage->getToken()->getUser();

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
                'bkstg_directory_show',
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
