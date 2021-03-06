<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgFOSUserBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\FOSUserBundle\Controller;

use Bkstg\CoreBundle\Controller\Controller;
use Bkstg\FOSUserBundle\Entity\User;
use Bkstg\FOSUserBundle\Form\ProfileType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProfileController extends Controller
{
    /**
     * Render the current user's profile.
     *
     * @param TokenStorageInterface $token_storage The token storage service.
     *
     * @return Response
     */
    public function readAction(TokenStorageInterface $token_storage): Response
    {
        // Render the response.
        return new Response($this->templating->render(
            '@BkstgFOSUser/Profile/read.html.twig',
            ['user' => $token_storage->getToken()->getUser()]
        ));
    }

    /**
     * Edit the current user's profile.
     *
     * @param Request               $request       The incoming request.
     * @param TokenStorageInterface $token_storage The token storage service.
     *
     * @return Response
     */
    public function updateAction(
        Request $request,
        TokenStorageInterface $token_storage
    ): Response {
        // Get the current user.
        $user = $token_storage->getToken()->getUser();

        // Create a form for the current user.
        $form = $this->form->create(ProfileType::class, $user);
        $form->handleRequest($request);

        // Process the form if it is good.
        if ($form->isSubmitted() && $form->isValid()) {
            // Cascade active property to image.
            if (null !== $image = $user->getImage()) {
                $image->setActive(true);
            }

            // Persist the profile and flush.
            $user->setHasProfile(true);
            $this->em->persist($user);
            $this->em->flush();

            // Set success message and redirect.
            $this->session->getFlashBag()->add(
                'success',
                $this->translator->trans('profile.edited')
            );

            return new RedirectResponse($this->url_generator->generate(
                'bkstg_directory_read',
                ['profile_slug' => $user->getSlug()]
            ));
        }

        return new Response($this->templating->render(
            '@BkstgFOSUser/Profile/update.html.twig',
            [
                'form' => $form->createView(),
                'profile' => $user,
            ]
        ));
    }
}
