<?php

namespace Bkstg\FOSUserBundle\Controller;

use Bkstg\CoreBundle\Controller\Controller;
use Bkstg\CoreBundle\Entity\Production;
use Bkstg\FOSUserBundle\BkstgFOSUserBundle;
use Bkstg\FOSUserBundle\Entity\ProductionMembership;
use Bkstg\FOSUserBundle\Form\ProductionMembershipType;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProductionMembershipController extends Controller
{
    /**
     * Show a list of active memberships.
     *
     * @param  string                        $production_slug The production slug.
     * @param  Request                       $request         The incoming request.
     * @param  AuthorizationCheckerInterface $auth            The authorization checker service.
     * @param  PaginatorInterface            $paginator       The paginator service.
     * @throws NotFoundHttpException                          If the production is not found.
     * @throws AccessDeniedException                          If the user is not an admin in the group.
     * @return Response
     */
    public function indexAction(
        string $production_slug,
        Request $request,
        AuthorizationCheckerInterface $auth,
        PaginatorInterface $paginator
    ): Response {
        // Lookup the production by production_slug.
        $production_repo = $this->em->getRepository(Production::class);
        if (null === $production = $production_repo->findOneBy(['slug' => $production_slug])) {
            throw new NotFoundHttpException();
        }

        // Check permissions for this action.
        if (!$auth->isGranted('GROUP_ROLE_ADMIN', $production)) {
            throw new AccessDeniedException();
        }

        // Get active memberships.
        $query = $this->em->getRepository(ProductionMembership::class)->findAllActiveQuery($production);
        $memberships = $paginator->paginate($query, $request->query->getInt('page', 1));

        // Return the membership index.
        return new Response($this->templating->render(
            '@BkstgFOSUser/ProductionMembership/index.html.twig',
            [
                'production' => $production,
                'memberships' => $memberships,
            ]
        ));
    }

    /**
     * Show a list of archived memberships.
     *
     * @param  string                        $production_slug The production slug.
     * @param  Request                       $request         The incoming request.
     * @param  AuthorizationCheckerInterface $auth            The authorization checker service.
     * @param  PaginatorInterface            $paginator       The paginator service.
     * @throws NotFoundHttpException                          If the production is not found.
     * @throws AccessDeniedException                          If the user is not an admin in the group.
     * @return Response
     */
    public function archiveAction(
        string $production_slug,
        Request $request,
        AuthorizationCheckerInterface $auth,
        PaginatorInterface $paginator
    ): Response {
        // Lookup the production by production_slug.
        $production_repo = $this->em->getRepository(Production::class);
        if (null === $production = $production_repo->findOneBy(['slug' => $production_slug])) {
            throw new NotFoundHttpException();
        }

        // Check permissions for this action.
        if (!$auth->isGranted('GROUP_ROLE_ADMIN', $production)) {
            throw new AccessDeniedException();
        }

        // Get archived memberships.
        $query = $this->em->getRepository(ProductionMembership::class)->findAllInactiveQuery($production);
        $memberships = $paginator->paginate($query, $request->query->getInt('page', 1));

        // Return the membership archive.
        return new Response($this->templating->render(
            '@BkstgFOSUser/ProductionMembership/archive.html.twig',
            [
                'production' => $production,
                'memberships' => $memberships,
            ]
        ));
    }

    /**
     * Create a new membership in this group.
     *
     * @param  string                        $production_slug The production slug.
     * @param  Request                       $request         The incoming request.
     * @param  AuthorizationCheckerInterface $auth            The authorization checker service.
     * @throws NotFoundHttpException                          If the production is not found.
     * @throws AccessDeniedException                          If the user is not an admin in the group.
     * @return Response
     */
    public function createAction(
        string $production_slug,
        Request $request,
        AuthorizationCheckerInterface $auth
    ): Response {
        // Lookup the production by production_slug.
        $production_repo = $this->em->getRepository(Production::class);
        if (null === $production = $production_repo->findOneBy(['slug' => $production_slug])) {
            throw new NotFoundHttpException();
        }

        // Check permissions for this action.
        if (!$auth->isGranted('GROUP_ROLE_ADMIN', $production)) {
            throw new AccessDeniedException();
        }

        // Create a new production_membership.
        $membership = new ProductionMembership();
        $membership->setGroup($production);

        // Create and handle the form.
        $form = $this->form->create(ProductionMembershipType::class, $membership);
        $form->handleRequest($request);

        // Form is submitted and valid.
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the production
            $this->em->persist($membership);
            $this->em->flush();

            // Set success message and redirect.
            $this->session->getFlashBag()->add(
                'success',
                $this->translator->trans('production_membership.created', [
                    '%user%' => $membership->getMember()->__toString(),
                    '%production%' => $production->getName(),
                ], BkstgFOSUserBundle::TRANSLATION_DOMAIN)
            );
            return new RedirectResponse($this->url_generator->generate('bkstg_production_membership_index', [
                'production_slug' => $production->getSlug(),
            ]));
        }

        // Render the form.
        return new Response($this->templating->render('@BkstgFOSUser/ProductionMembership/create.html.twig', [
            'production' => $production,
            'form' => $form->createView(),
        ]));
    }

    /**
     * Update an existing group membership.
     *
     * @param  string                        $production_slug The production slug.
     * @param  integer                       $id              The membership id.
     * @param  Request                       $request         The incoming request.
     * @param  AuthorizationCheckerInterface $auth            The authorization checker service.
     * @throws NotFoundHttpException                          If the production is not found.
     * @throws AccessDeniedException                          If the user is not an admin in the group.
     * @return Response
     */
    public function updateAction(
        string $production_slug,
        int $id,
        Request $request,
        AuthorizationCheckerInterface $auth
    ): Response {
        // Lookup the production by production_slug.
        $production_repo = $this->em->getRepository(Production::class);
        if (null === $production = $production_repo->findOneBy(['slug' => $production_slug])) {
            throw new NotFoundHttpException();
        }

        // Lookup the membership by production and id.
        $membership_repo = $this->em->getRepository(ProductionMembership::class);
        if (null === $membership = $membership_repo->findOneBy(['group' => $production, 'id' => $id])) {
            throw new NotFoundHttpException();
        }

        // Ensure this membership is for this group.
        if ($membership->getGroup() !== $production) {
            throw new NotFoundHttpException();
        }

        // Check permissions for this action.
        if (!$auth->isGranted('GROUP_ROLE_ADMIN', $production)) {
            throw new AccessDeniedException();
        }

        // Create an index of production roles for checking later.
        $production_roles = new ArrayCollection();
        foreach ($membership->getProductionRoles() as $production_role) {
            $production_roles->add($production_role);
        }

        // Create and handle the form.
        $form = $this->form->create(ProductionMembershipType::class, $membership);
        $form->handleRequest($request);

        // Form is submitted and valid.
        if ($form->isSubmitted() && $form->isValid()) {
            // Remove unneeded production_roles.
            foreach ($production_roles as $production_role) {
                if (false === $membership->getProductionRoles()->contains($production_role)) {
                    $this->em->remove($production_role);
                }
            }

            // Persist the production
            $this->em->persist($membership);
            $this->em->flush();

            // Set success message and redirect.
            $this->session->getFlashBag()->add(
                'success',
                $this->translator->trans('production_membership.updated', [
                    '%user%' => $membership->getMember()->__toString(),
                ], BkstgFOSUserBundle::TRANSLATION_DOMAIN)
            );
            return new RedirectResponse($this->url_generator->generate('bkstg_production_membership_index', [
                'production_slug' => $production->getSlug(),
            ]));
        }

        // Render the form.
        return new Response($this->templating->render('@BkstgFOSUser/ProductionMembership/update.html.twig', [
            'membership' => $membership,
            'production' => $production,
            'form' => $form->createView(),
        ]));
    }

    /**
     * Delete an existing group membership.
     *
     * @param  string                        $production_slug The production slug.
     * @param  integer                       $id              The membership id.
     * @param  Request                       $request         The incoming request.
     * @param  AuthorizationCheckerInterface $auth            The authorization checker service.
     * @throws NotFoundHttpException                          If the production is not found.
     * @throws AccessDeniedException                          If the user is not an admin in the group.
     * @return Response
     */
    public function deleteAction(
        string $production_slug,
        int $id,
        Request $request,
        AuthorizationCheckerInterface $auth
    ): Response {
        // Lookup the production by production_slug.
        $production_repo = $this->em->getRepository(Production::class);
        if (null === $production = $production_repo->findOneBy(['slug' => $production_slug])) {
            throw new NotFoundHttpException();
        }

        // Lookup the membership by production and id.
        $membership_repo = $this->em->getRepository(ProductionMembership::class);
        if (null === $membership = $membership_repo->findOneBy(['group' => $production, 'id' => $id])) {
            throw new NotFoundHttpException();
        }

        // Ensure this membership is for this group.
        if ($membership->getGroup() !== $production) {
            throw new NotFoundHttpException();
        }

        // Check permissions for this action.
        if (!$auth->isGranted('GROUP_ROLE_ADMIN', $production)) {
            throw new AccessDeniedException();
        }

        // Create an empty form.
        $form = $this->form->createBuilder()->getForm();
        $form->handleRequest($request);

        // Delete the user.
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->remove($membership);
            $this->em->flush();

            // Set success message and redirect.
            $this->session->getFlashBag()->add(
                'success',
                $this->translator->trans('production_membership.deleted', [
                    '%user%' => $membership->getMember()->__toString(),
                ], BkstgFOSUserBundle::TRANSLATION_DOMAIN)
            );
            return new RedirectResponse($this->url_generator->generate('bkstg_production_membership_index', [
                'production_slug' => $production->getSlug(),
            ]));
        }

        // Render the form.
        return new Response($this->templating->render('@BkstgFOSUser/ProductionMembership/delete.html.twig', [
            'production' => $production,
            'membership' => $membership,
            'form' => $form->createView(),
        ]));
    }
}
