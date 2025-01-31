<?php

namespace App\Entity;

use App\Enum\CommentStatus;
use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(enumType: CommentStatus::class)]
    private ?CommentStatus $statusComment = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?User $userComment = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?Media $media = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'commentResponses')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?self $parentComment = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parentComment')]
    private Collection $commentResponses;

    public function __construct()
    {
        $this->commentResponses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getStatusComment(): ?CommentStatus
    {
        return $this->statusComment;
    }

    public function setStatusComment(CommentStatus $statusComment): static
    {
        $this->statusComment = $statusComment;

        return $this;
    }

    public function getUserComment(): ?User
    {
        return $this->userComment;
    }

    public function setUserComment(?User $userComment): static
    {
        $this->userComment = $userComment;

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): static
    {
        $this->media = $media;

        return $this;
    }

    public function getParentComment(): ?self
    {
        return $this->parentComment;
    }

    public function setParentComment(?self $parentComment): static
    {
        $this->parentComment = $parentComment;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getCommentResponses(): Collection
    {
        return $this->commentResponses;
    }

    public function addCommentResponse(self $commentResponse): static
    {
        if (!$this->commentResponses->contains($commentResponse)) {
            $this->commentResponses->add($commentResponse);
            $commentResponse->setParentComment($this);
        }

        return $this;
    }

    public function removeCommentResponse(self $commentResponse): static
    {
        if ($this->commentResponses->removeElement($commentResponse)) {
            // set the owning side to null (unless already changed)
            if ($commentResponse->getParentComment() === $this) {
                $commentResponse->setParentComment(null);
            }
        }

        return $this;
    }
}
