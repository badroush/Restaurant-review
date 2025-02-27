<?php

namespace App\Entity;

use App\Repository\RestoCategoriesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RestoCategoriesRepository::class)]
class RestoCategories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $idResto = null;

    #[ORM\ManyToOne]
    private ?categories $idCategories = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdResto(): ?int
    {
        return $this->idResto;
    }

    public function setIdResto(int $idResto): static
    {
        $this->idResto = $idResto;

        return $this;
    }

    public function getIdCategories(): ?categories
    {
        return $this->idCategories;
    }

    public function setIdCategories(?categories $idCategories): static
    {
        $this->idCategories = $idCategories;

        return $this;
    }
}
