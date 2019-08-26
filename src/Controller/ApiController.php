<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\XmlService;

/**
 * @Route("/Api")
 */
class ApiController extends AbstractController
{
    /**
     * Api index
     * @Route("/", name="api_index", methods={"POST"})
     */
    public function index(Request $request, XmlService $xmlService): Response
    {
        //get request xml
        $requestXml = $request->getContent();

        //request processing
        $responseXml = $xmlService->processing($requestXml);

        //response
        $response = new Response($responseXml);
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'xml');

        return $response;
    }

}