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
}
