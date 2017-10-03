<?php

namespace Bkstg\FOSUserBundle\Controller;

use Bkstg\CoreBundle\Controller\Controller;
use Bkstg\CoreBundle\Entity\Production;
use Bkstg\FOSUserBundle\Entity\ProductionMembership;
use Bkstg\FOSUserBundle\Form\Type\ProductionMembershipType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProductionMembershipController extends Controller
{
    public function indexAction(
        $production_slug,
        Request $request,
        AuthorizationChecker $auth
    ) {
        // Lookup the production by production_slug.
        $production_repo = $this->em->getRepository(Production::class);
        if (null === $production = $production_repo->findOneBy(['slug' => $production_slug])) {
            throw new NotFoundHttpException();
        }

        // Check permissions for this action.
        if (!$auth->isGranted('GROUP_ROLE_ADMIN', $production)) {
            throw new AccessDeniedException();
        }

        // Can show active or inactive.
        if ($request->query->has('status')
            && $request->query->get('status') == 'inactive') {
            $memberships = $this->em
                ->getRepository(ProductionMembership::class)
                ->findAllInactive($production);
        } else {
            $memberships = $this->em
                ->getRepository(ProductionMembership::class)
                ->findAllActive($production);
        }

        // Return the membership index.
        return new Response($this->templating->render(
            '@BkstgFOSUser/ProductionMembership/index.html.twig',
            [
                'production' => $production,
                'memberships' => $memberships,
            ]
        ));
    }

    public function createAction(
        $production_slug,
        Request $request,
        AuthorizationCheckerInterface $auth
    ) {
        // Lookup the production by production_slug.
        $production_repo = $this->em->getRepository(Production::class);
        if (null === $production = $production_repo->findOneBy(['slug' => $production_slug])) {
            throw new NotFoundHttpException();
        }

        // Check permissions for this action.
        if (!$auth->isGranted('GROUP_ROLE_ADMIN', $production)) {
            throw new AccessDeniedException();
        }

        // Create a new membership.
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
                $this->translator->trans('"%user%" added to "%production%".', [
                    '%user%' => $membership->getMember()->getUsername(),
                    '%production%' => $production->getName(),
                ])
            );
            return new RedirectResponse($this->url_generator->generate('bkstg_membership_list', [
                'production_slug' => $production->getSlug(),
            ]));
        }

        // Render the form.
        return new Response($this->templating->render('@BkstgFOSUser/ProductionMembership/create.html.twig', [
            'production' => $production,
            'form' => $form->createView(),
        ]));
    }

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

        // Lookup the membership by production and id.
        $membership_repo = $this->em->getRepository(ProductionMembership::class);
        if (null === $membership = $membership_repo->findOneBy(['group' => $production, 'id' => $id])) {
            throw new NotFoundHttpException();
        }

        // Check permissions for this action.
        if (!$auth->isGranted('GROUP_ROLE_ADMIN', $production)) {
            throw new AccessDeniedException();
        }

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
                $this->translator->trans('Membership for "%user%" edited.', [
                    '%user%' => $membership->getMember()->getUsername(),
                ])
            );
            return new RedirectResponse($this->url_generator->generate('bkstg_membership_list', [
                'production_slug' => $production->getSlug(),
            ]));
        }

        // Render the form.
        return new Response($this->templating->render('@BkstgFOSUser/ProductionMembership/edit.html.twig', [
            'membership' => $membership,
            'production' => $production,
            'form' => $form->createView(),
        ]));
    }
}
