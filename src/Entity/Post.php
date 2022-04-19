<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\Length(
        min:3,
        max:100
    )]
    private $title;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message:"Ce champs doit être rempli")]
    private $content;

    #[ORM\Column(type: 'datetime')]
    // #[Assert\DateTime()]
    // TODO: Modifier la contrainte pour être acceptée par l'update
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private $category;

    #[ORM\Column(type: 'string', length: 40, nullable: true)]
    #[Assert\File(mimeTypes:['image/jpg', 'image/png'])]
    private $picture;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getPicture(): string|File|null
    {
        return $this->picture;
    }

    public function setPicture(string|File|null $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    #[ORM\PostRemove]
    public function deletePicture(): void
    {
        // 1. On vérifie que le fichier existe
        if (file_exists(__DIR__.'/../../public/img/upload/'. $this->picture)) {

            // 2. On supprime le fichier
            unlink(__DIR__.'/../../public/img/upload/'. $this->picture);
        }
        // 3. On indique quand utiliser cette méthode grâce aux évènements:
        // #[ORM\HasLifecycleCallbacks]à ajouter sur la class
        // #[ORM\PostRemove] à ajouter sur la méthode qui prend l'évènement

    }
}
