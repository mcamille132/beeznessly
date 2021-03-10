<?php

namespace App\Entity;

use App\Repository\EbookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EbookRepository::class)
 * @Vich\Uploadable
 */
class Ebook
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="date")
     */
    private $releaseDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $editorName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $author;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isValidated;

    /**
     * @ORM\ManyToOne(targetEntity=Expertise::class, inversedBy="ebooks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $expertise;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ebooks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
    * @ORM\Column(type="string", length=255, nullable=true)
    * @var string
    */
    private $illustration;

    /**
    * @Vich\UploadableField(mapping="ebook_illustration", fileNameProperty="illustration")
    * @Assert\File(
    *     maxSize = "3000k",
    *     mimeTypes = {"image/png", "image/jpeg"},
    *     mimeTypesMessage = "Seuls les formats jpg, jpeg et png sont acceptés"
    * )
    * @var File
    */
    private $illustrationFile;

    /**
    * @ORM\Column(type="string", length=255, nullable=true)
    * @var string
    */
    private $documentEbook;

    /**
    * @Vich\UploadableField(mapping="ebook_file", fileNameProperty="documentEbook")
    * @Assert\File(
    *     maxSize = "2000k",
    *     mimeTypes = {"application/pdf", "application/x-pdf"},
    *     mimeTypesMessage = "Seuls les pdf sont acceptés"
    * )
    * @var File
    */
    private $documentEbookFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var Datetime
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Download::class, mappedBy="ebook")
     * @ORM\JoinColumn(nullable=true)
     */
    private $downloads;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    public function __construct()
    {
        $this->downloads = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTimeInterface $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getEditorName(): ?string
    {
        return $this->editorName;
    }

    public function setEditorName(string $editorName): self
    {
        $this->editorName = $editorName;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getIsValidated(): ?bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(?bool $isValidated): self
    {
        $this->isValidated = $isValidated;

        return $this;
    }

    public function getExpertise(): ?Expertise
    {
        return $this->expertise;
    }

    public function setExpertise(?Expertise $expertise): self
    {
        $this->expertise = $expertise;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setIllustrationFile(File $illustration = null)
    {
        $this->illustrationFile = $illustration;
        if ($illustration) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getIllustrationFile(): ?File
    {
        return $this->illustrationFile;
    }

    public function getIllustration(): ?string
    {
        return $this->illustration;
    }

    public function setIllustration(?string $illustration): self
    {
        $this->illustration = $illustration;

        return $this;
    }

    public function setDocumentEbookFile(File $documentEbook = null)
    {
        $this->documentEbookFile = $documentEbook;
        if ($documentEbook) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getDocumentEbookFile(): ?File
    {
        return $this->documentEbookFile;
    }

    public function getDocumentEbook(): ?string
    {
        return $this->documentEbook;
    }

    public function setDocumentEbook(?string $documentEbook): self
    {
        $this->documentEbook = $documentEbook;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection|Download[]
     */
    public function getDownloads(): Collection
    {
        return $this->downloads;
    }

    public function addDownload(Download $download): self
    {
        if (!$this->downloads->contains($download)) {
            $this->downloads[] = $download;
            $download->setEbook($this);
        }

        return $this;
    }

    public function removeDownload(Download $download): self
    {
        if ($this->downloads->removeElement($download)) {
            // set the owning side to null (unless already changed)
            if ($download->getEbook() === $this) {
                $download->setEbook(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
