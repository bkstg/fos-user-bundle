<?php

namespace Bkstg\FOSUserBundle\Controller;

use Bkstg\CoreBundle\Controller\Controller;
use Bkstg\CoreBundle\Entity\Production;
use Bkstg\CoreBundle\Util\ProfileManagerInterface;
use Bkstg\FOSUserBundle\Entity\ProductionMembership;
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

class ProductionDirectoryController extends Controller
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
        PaginatorInterface $paginator,
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

        // Get users for active memberships.
        $user_repo = $this->em->getRepository(User::class);
        $query = $user_repo->findUsersByGroupQuery($production, true);

        // Render the response.
        $users = $paginator->paginate($query, $request->query->getInt('page', 1));
        return new Response($this->templating->render('@BkstgFOSUser/ProductionProfile/index.html.twig', [
            'production' => $production,
            'users' => $users,
        ]));
    }


    /**
     * Render a profile.
     *
     * @param int $id
     *   The profile id to render.
     *
     * @return Response
     *   The rendered profile.
     */
    public function readAction($production_slug, $profile_slug)
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
            '@BkstgFOSUser/ProductionProfile/show.html.twig',
            [
                'user' => $user,
                'production' => $production,
                'membership' => $membership,
            ]
        ));
    }
}
