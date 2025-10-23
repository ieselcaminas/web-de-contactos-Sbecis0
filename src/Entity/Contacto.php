<?php

namespace App\Entity;



use App\Repository\ContactoRepository;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ContactoRepository::class)]

#[UniqueEntity('email', message: 'Ya existe un contacto con este email en la base de datos.')]
#[UniqueEntity('telefono', message: 'Ya existe un contacto con este teléfono en la base de datos.')]
class Contacto
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $nombre = null;

    #[ORM\Column(length: 15)]
    #[Assert\NotBlank]
    private ?string $telefono = null;

    #[ORM\Column(type:"string", length: 255)]
    #[Assert\Email(message: 'El email {{ value }} no es un email válido.',)]
    private $email;


    #[ORM\ManyToOne(inversedBy: 'contactos')]

    #[Assert\NotBlank]

    private ?Provincia $provincia = null;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): self
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): self
    {
        $this->telefono = $telefono;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getProvincia(): ?Provincia
    {
        return $this->provincia;
    }


    public function setProvincia(?Provincia $provincia): self
    {
        $this->provincia = $provincia;
        return $this;
    }
}
