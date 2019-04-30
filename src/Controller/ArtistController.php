<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Form\ArtistType;
use App\Services\ArtistEntityConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArtistController extends AbstractController
{
    private $artistEntityConverter;

    public function __construct(ArtistEntityConverter $artistEntityConverter)
    {
        $this->artistEntityConverter = $artistEntityConverter;
    }

    /**
     * @Route("/artist", name="artist.index")
     */
    public function index()
    {
        $artists = $this->getDoctrine()->getRepository(Artist::class)->findAll();
        return $this->render('artist/index.html.twig', $artists);
    }

    /**
     * @Route("/artist/create", name="artist.create")
     * @Security("has_role('ROLE_USER')")
     */
    public function create(Request $request)
    {
        $artist = new Artist();
        $form = $this->createForm(ArtistType::class, $artist);
        if ($form->isValid() && $request->isXmlHttpRequest()) {
            try {
                $artistDto = $this->artistEntityConverter->convert($request->getParameters());
                $em = $this->getDoctrine()->getManager();
                $artist->setFirstName($artistDto->getFirstName());
                $artist->setLastName($artistDto->getLastName());
                $em->merge($artist);
                $em->flush();
                return new Response(json_encode(array('message' => 'success')));
            } catch (\Exception $e) {
                return new Response(json_encode(array('error' => 'item not updated')));
            }
        }

        return $this->render('artist/create.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * @Route("/artist/{id}", name="artist.show", requirements={"id": "\d+"})
     * @ParamConverter("artist", class="Artist", options={"mapping": {"id": "id"}})
     * @Security("has_role('ROLE_USER')")
     * @param Artist $artist
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Artist $artist)
    {
        return $this->render('artist/show.html.twig', $artist);
    }

    /**
     * @Route("/artist/{id}/edit", name="artist.edit", requirements={"id": "\d+"})
     * @ParamConverter("artist", class="Artist", options={"mapping": {"id": "id"}})
     * @Security("has_role('ROLE_USER')")
     * @param Artist $artist
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(Artist $artist, Request $request)
    {
        $form = $this->createForm(ArtistType::class, $artist);
        if ($form->isValid() && $request->isXmlHttpRequest()) {
            try {
                $artistDto = $this->artistEntityConverter->convert($request->getParameters());
                $em = $this->getDoctrine()->getManager();
                $artist->setFirstName($artistDto->getFirstName());
                $artist->setLastName($artistDto->getLastName());
                $em->merge($artist);
                $em->flush();
                return new Response(json_encode(array('message' => 'success')));
            } catch (\Exception $e) {
                return new Response(json_encode(array('error' => 'item not created')));
            }
        }

        return $this->render('artist/edit.html.twig', [
            'form' => $form
        ]);
    }


    /**
     * @Route("/artist/{id}/delete", name="artist.delete", requirements={"id": "\d+"}, methods={"DELETE"})
     * @ParamConverter("artist", class="Artist", options={"mapping": {"id": "id"}})
     * @Security("has_role('ROLE_USER')")
     * @param Artist $artist
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Artist $artist, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            if ($this->isCsrfTokenValid('delete' . $artist->getId(), $request->request->get('_token'))) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->remove($artist);
                    $em->flush();

                    return new JsonResponse([
                        'type' => 'success',
                        'message' => 'item was removed'
                    ], 200);
                } catch (\Exception $e) {
                }
            }
        }
        return new JsonResponse([
            'type' => 'error',
            'message' => 'This is only accesible in AJAX'
        ], 500);
    }
}
