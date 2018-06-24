<?php

namespace Bkstg\FOSUserBundle\Controller;

use Bkstg\CoreBundle\Controller\Controller;
use Bkstg\FOSUserBundle\Entity\User;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DirectoryController extends Controller
{
    /**
     * Show a directory listing of users with profile information.
     *
     * @param  Request               $request       The request.
     * @param  PaginatorInterface    $paginator     The paginator service.
     * @param  TokenStorageInterface $token_storage The token storage service.
     * @return Response
     */
    public function indexAction(
        Request $request,
        PaginatorInterface $paginator,
        TokenStorageInterface $token_storage
    ): Response {
        // Get the user repo and find all active users with profiles.
        $user_repo = $this->em->getRepository(User::class);
        $query = $user_repo->findAllActiveQuery(true);

        // If the current user has no profile set a message.
        $user = $token_storage->getToken()->getUser();
        if (!$user->hasProfile()) {
            $this->session->getFlashBag()->add(
                'warning',
                $this->translator->trans(
                    'profile.not_created',
                    ['%url%' => $this->url_generator->generate('bkstg_profile_update', ['id' => $user->getId()])]
                )
            );
        }

        // Paginate and return the output.
        $users = $paginator->paginate($query, $request->query->getInt('page', 1));
        return new Response($this->templating->render('@BkstgFOSUser/Directory/index.html.twig', [
            'users' => $users,
        ]));
    }

    /**
     * Show a single profile.
     *
     * @param  string                $profile_slug  The profile slug to look for.
     * @param  TokenStorageInterface $token_storage The token storage service.
     * @throws NotFoundHttpException When the user is not found.
     * @return Response
     */
    public function readAction(
        string $profile_slug,
        TokenStorageInterface $token_storage
    ): Response {
        // Lookup the profile.
        $user_repo = $this->em->getRepository(User::class);
        if (null === $user = $user_repo->findOneBy(['slug' => $profile_slug])) {
            throw new NotFoundHttpException();
        }

        // If this is the current user redirect to the profile handler.
        if ($token_storage->getToken()->getUser() === $user) {
            return new RedirectResponse($this->url_generator->generate('bkstg_profile_read'));
        }

        // Render the response.
        return new Response($this->templating->render(
            '@BkstgFOSUser/Directory/read.html.twig',
            ['user' => $user]
        ));
    }
}
