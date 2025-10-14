<?php

namespace App\Controller;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Contacto;
use App\Repository\ContactoRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry as DoctrineManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController
{
    private $contactos = [
        1 => ["nombre" => "Juan Pérez", "telefono" => "524142432", "email" => "juanp@ieselcaminas.org"],
        2 => ["nombre" => "Ana López", "telefono" => "58958448", "email" => "anita@ieselcaminas.org"],
        5 => ["nombre" => "Mario Montero", "telefono" => "5326824", "email" => "mario.mont@ieselcaminas.org"],
        7 => ["nombre" => "Laura Martínez", "telefono" => "42898966", "email" => "lm2000@ieselcaminas.org"],
        9 => ["nombre" => "Nora Jover", "telefono" => "54565859", "email" => "norajover@ieselcaminas.org"]
    ];

    #[Route('/page', name: 'app_page')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PageController.php',
        ]);
    }

    //inicio
    #[Route('/', name: 'inicio')]
    public function inicio(): Response
    {
        return $this->render('inicio.html.twig');
    }

        #[Route("/contacto/insertar", name: "insertar_contacto")]
    
    public function insertar(ManagerRegistry $doctrine)
    
    {
    $entityManager = $doctrine->getManager();
    foreach ($this->contactos as $c) {
        $contacto = new Contacto();
        $contacto->setNombre($c["nombre"]);
        $contacto->setTelefono($c["telefono"]);
        $contacto->setEmail($c["email"]);
        $entityManager->persist($contacto);
    }

    try {
        // Sólo se necesita realizar flush una vez y confirmará todas las operaciones pendientes
        $entityManager->flush();
        return new Response("Contactos insertados");
    } catch (\Exception $e) {
        return new Response("Error insertando objetos");
    }

}
    

    #[Route('/contacto/{codigo}', name: 'ficha_contacto')]
    public function ficha(ManagerRegistry $doctrine, $codigo): Response
    {
        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto = $repositorio->find($codigo);

            return $this->render('ficha_contacto.html.twig', [
                "contacto" => $contacto]);
        

    
    }

    #[Route('/contacto/buscar/{texto}', name: 'buscar_contacto')]
    public function buscar(ManagerRegistry $doctrine, $texto): Response{

        $repositorio = $doctrine->getRepository(Contacto::class);

        $contactos = $repositorio->findByName($texto);

        return $this->render('ficha_contacto.html.twig', [
            'contactos' => $contactos
        ]);
    }
    
}

