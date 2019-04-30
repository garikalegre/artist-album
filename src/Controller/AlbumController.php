<?php

namespace App\Controller;

use App\Entity\Album;
use App\Form\AlbumType;
use App\Services\AlbumEntityConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlbumController extends AbstractController
{
    private $albumEntityConverter;

    public function __construct(AlbumEntityConverter $albumEntityConverter)
    {
        $this->albumEntityConverter = $albumEntityConverter;
    }

    /**
     * @Route("/album", name="album.index")
     */
    public function index()
    {
        $albums = $this->getDoctrine()->getRepository(Album::class)->findAll();
        return $this->render('album/index.html.twig', $albums);
    }

    /**
     * @Route("/album/create", name="album.create")
     * @Security("has_role('ROLE_USER')")
     */
    public function create(Request $request)
    {
        $album = new Album();
        $form = $this->createForm(AlbumType::class, $album);
        if ($form->isValid() && $request->isXmlHttpRequest()) {
            try {
                $albumDto = $this->albumEntityConverter->convert($request->getParameters());
                $em = $this->getDoctrine()->getManager();
                $album->setName($albumDto->getName());
                $album->setDescription($albumDto->getDescription());
                $album->setArtist($albumDto->getArtist());
                $em->merge($album);
                $em->flush();
                return new Response(json_encode(array('message' => 'success')));
            } catch (\Exception $e) {
                return new Response(json_encode(array('error' => 'item not created')));
            }
        }
        return $this->render('album/create.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * @Route("/album/{id}", name="album.show", requirements={"id": "\d+"})
     * @ParamConverter("album", class="Album", options={"mapping": {"id": "id"}})
     * @Security("has_role('ROLE_USER')")
     * @param Album $album
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Album $album)
    {
        return $this->render('album/show.html.twig', $album);
    }

    /**
     * @Route("/album/{id}/edit", name="album.edit", requirements={"id": "\d+"})
     * @ParamConverter("album", class="Album", options={"mapping": {"id": "id"}})
     * @Security("has_role('ROLE_USER')")
     * @param Album $album
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Album $album, Request $request)
    {
        $form = $this->createForm(AlbumType::class, $album);
        if ($form->isValid() && $request->isXmlHttpRequest()) {
            try {
                $albumDto = $this->albumEntityConverter->convert($request->getParameters());
                $em = $this->getDoctrine()->getManager();
                $album->setName($albumDto->getName());
                $album->setDescription($albumDto->getDescription());
                $album->setArtist($albumDto->getArtist());
                $em->merge($album);
                $em->flush();
                return new Response(json_encode(array('message' => 'success')));
            } catch (\Exception $e) {
                return new Response(json_encode(array('error' => 'item not created')));
            }
        }

        return $this->render('album/edit.html.twig', $album);
    }


    /**
     * @Route("/album/{id}/delete", name="album.delete", requirements={"id": "\d+"}, methods={"DELETE"})
     * @ParamConverter("album", class="Album", options={"mapping": {"id": "id"}})
     * @Security("has_role('ROLE_USER')")
     * @param Album $album
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Album $album, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            if ($this->isCsrfTokenValid('delete' . $album->getId(), $request->request->get('_token'))) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->remove($album);
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
