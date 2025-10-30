<?php

namespace App\Controller;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Contacto;
use App\Entity\Provincia;
use App\Repository\ContactoRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry as DoctrineManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\ContactoFormType as ContactoType;
use Symfony\Component\HttpFoundation\Request;
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


    #[Route('/contacto/insertarConProvincia', name: 'insertar_con_provincia_contacto')]
    public function insertarConProvincia(ManagerRegistry $doctrine): Response{
        $entityManager = $doctrine->getManager();
        $provincia = new Provincia();

        $provincia->setNombre("Alicante");
        $contacto = new Contacto();

        $contacto->setNombre("Insercción de prueba con provinicia");
        $contacto->setTelefono("9000220021");
        $contacto->setEmail("insercion.de.prueba.sin.provincia@contacto.es");
        $contacto->setProvincia($provincia);

    
        $entityManager->persist($provincia);
        $entityManager->persist($contacto);

        $entityManager->flush();
        return $this->render('ficha_contacto.html.twig', [
            'contacto' => $contacto
        ]);
   }
       #[Route('/contacto/nuevo', name: 'nuevo')]
    public function nuevo(ManagerRegistry $doctrine, Request $request){
        
        $this->getUser();


        $contacto = new Contacto();
        $formulario = $this->createForm(ContactoType::class, $contacto);
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()){
            $contacto = $formulario->getData();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($contacto);
            $entityManager->flush();
            return $this->redirectToRoute('ficha_contacto', 
            ['codigo' => $contacto->getId()]);
        }
        return $this->render('nuevo_contacto.html.twig', array(
            'formulario' => $formulario->createView()
        ));

    }

    #[Route('/contacto/editar/{codigo}', name: 'editar', requirements:["codigo"=>"\d+"])]

    public function editar(ManagerRegistry $doctrine, Request $request, int $codigo) {
        $repositorio = $doctrine->getRepository(Contacto::class);
        //En este caso, los datos los obtenemos del repositorio de contactos
        $contacto = $repositorio->find($codigo);

        if ($contacto){
            $formulario = $this->createForm(ContactoType::class, $contacto);
            $formulario->handleRequest($request);
            if ($formulario->isSubmitted() && $formulario->isValid()) {
                //Esta parte es igual que en la ruta para insertar
                $contacto = $formulario->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($contacto);
                $entityManager->flush();
                return $this->redirectToRoute('ficha_contacto', ["codigo" => $contacto->getId()]);

            }

            return $this->render('nuevo_contacto.html.twig', array(
                'formulario' => $formulario->createView()
            ));
        }else{
            return $this->render('ficha_contacto.html.twig', [

                'contacto' => NULL
            ]);
        }
    }

    
    #[Route("/contacto/update/{id}/{nombre}", name:"modificar_contacto")]
     
    public function update(ManagerRegistry $doctrine, $id, $nombre): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(Contacto::class);
        $contacto = $repository->find($id);
        if ($contacto){
            $contacto->setNombre($nombre);
            try {
                $entityManager->flush();
                return $this->render('ficha_contacto.html.twig', [
                    'contacto' => $contacto
                ]);
            }catch (\Exception $e) {
                return new Response("Error modificando el contacto");
            }
        } else {return $this->render('ficha_contacto.html.twig', [
            'contacto' => null
        ]);
        
        }
    }
    
    #[Route("/contacto/delete/{id}", name:"eliminar_contacto")]
     
    public function delete(ManagerRegistry $doctrine, $id): Response{
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(Contacto::class);
        $contacto = $repository->find($id);
        if ($contacto){
            try {
                $entityManager->remove($contacto);
                $entityManager->flush();
                return new Response("Contacto eliminado");
            }catch (\Exception $e) {
                return new Response("Error eliminando el contacto");
            }
        } else 
            return $this->render('ficha_contacto.html.twig', [
                'contacto' => null
            ]);
    }
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
    public function inicio(ManagerRegistry $doctrine): Response
    {
        if ($this->getUser()) {
            return $this->redirect('/index');
        }
        $repo = $doctrine->getRepository(Contacto::class);
        $contactos = $repo->findAll();

        return $this->render('inicio.html.twig',['contactos' => $contactos ]);
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

        return $this->render('lista_contactos.html.twig', [
            'contactos' => $contactos
        ]);
    }


}

