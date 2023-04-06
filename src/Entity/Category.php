<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 55)]
    private ?string $name = null;

    #[ORM\ManyToMany(mappedBy: 'categories', targetEntity: MicroPost::class)]
    private Collection $microPosts;

    public function __construct()
    {
        $this->microPosts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, MicroPost>
     */
    public function getMicroPosts(): Collection
    {
        return $this->microPosts;
    }

    public function addMicroPost(MicroPost $microPost): self
    {
        if (!$this->microPosts->contains($microPost)) {
            $this->microPosts[]= $microPost;
            // $this->microPosts->add($microPost);
           
        }

        return $this;
    }


    public function removePost(MicroPost $post) : self {
        $this->microPosts->removeElement($post);

        return $this;
        }
   
}
