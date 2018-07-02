<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgCoreBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\FOSUserBundle\Controller;

use Bkstg\CoreBundle\Controller\Controller;
use Bkstg\CoreBundle\Entity\Production;
use Bkstg\FOSUserBundle\Entity\ProductionMembership;
use Bkstg\FOSUserBundle\Entity\User;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProductionDirectoryController extends Controller
{
    /**
     * Show a list of production profiles for a production.
     *
     * @param string                        $production_slug The production slug for this membership.
     * @param AuthorizationCheckerInterface $auth            The authorization checker service.
     * @param PaginatorInterface            $paginator       The paginator service.
     * @param Request                       $request         The request.
     *
     * @throws NotFoundHttpException If the production does not exist.
     * @throws AccessDeniedException If the current user is not a member.
     *
     * @return Response
     */
    public function indexAction(
        string $production_slug,
        AuthorizationCheckerInterface $auth,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        // Lookup the production by production_slug.
        $production_repo = $this->em->getRepository(Production::class);
        if (null === $production = $production_repo->findOneBy(['slug' => $production_slug])) {
            throw new NotFoundHttpException();
        }

        // Check permissions for this action.
        if (!$auth->isGranted('GROUP_ROLE_USER', $production)) {
            throw new AccessDeniedException();
        }

        // Get users for active memberships.
        $user_repo = $this->em->getRepository(User::class);
        $query = $user_repo->findUsersByGroupQuery($production, true);

        // Render the response.
        $users = $paginator->paginate($query, $request->query->getInt('page', 1));

        return new Response($this->templating->render('@BkstgFOSUser/ProductionDirectory/index.html.twig', [
            'production' => $production,
            'users' => $users,
        ]));
    }

    /**
     * Show a profile in the context of a production.
     *
     * @param string $production_slug The production slug.
     * @param string $profile_slug    The profile slug.
     *
     * @throws NotFoundHttpException If the production or profile is not found.
     *
     * @return Response
     */
    public function readAction(string $production_slug, string $profile_slug): Response
    {
        // Lookup the profile.
        $user_repo = $this->em->getRepository(User::class);
        if (null === $user = $user_repo->findOneBy(['slug' => $profile_slug])) {
            throw new NotFoundHttpException();
        }

        // Lookup the production by production_slug.
        $production_repo = $this->em->getRepository(Production::class);
        if (null === $production = $production_repo->findOneBy(['slug' => $production_slug])) {
            throw new NotFoundHttpException();
        }

        // Get the membership for this user in this group.
        $membership_repo = $this->em->getRepository(ProductionMembership::class);
        if (null === $membership = $membership_repo->findOneBy(['member' => $user, 'group' => $production])) {
            throw new NotFoundHttpException();
        }

        // Render the response.
        return new Response($this->templating->render(
            '@BkstgFOSUser/ProductionDirectory/read.html.twig',
            [
                'user' => $user,
                'production' => $production,
                'membership' => $membership,
            ]
        ));
    }
}
