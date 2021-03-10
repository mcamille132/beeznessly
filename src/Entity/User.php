<?php

namespace App\Entity;

use Serializable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @Vich\Uploadable
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $companyName;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $siretNumber;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isValidated;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $town;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $zipcode;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $adress;

    /**
     * @ORM\ManyToOne(targetEntity=Provider::class, inversedBy="users")
     */
    private $provider;

    /**
     * @ORM\ManyToMany(targetEntity=Expertise::class, inversedBy="users")
     */
    private $expertise;

    /**
     * @ORM\OneToMany(targetEntity=Contact::class, mappedBy="user")
     */
    private $contacts;

    /**
     * @ORM\OneToMany(targetEntity=Service::class, mappedBy="user")
     */
    private $services;

    /**
     * @ORM\OneToMany(targetEntity=Ebook::class, mappedBy="user")
     */
    private $ebooks;

    /**
    * @ORM\Column(type="string", length=255, nullable=true)
    * @var string
    */
    private $logo;

    /**
    * @Vich\UploadableField(mapping="logo_file", fileNameProperty="logo")
    * @Assert\File(
    *     maxSize = "3000k",
    *     mimeTypes = {"image/png"},
    *     mimeTypesMessage = "Seul le format png est accepté"
    * )
    * @var File
    */
    private $logoFile;

    /**
    * @ORM\Column(type="string", length=255, nullable=true)
    * @var string
    */
    private $banner;

    /**
    * @Vich\UploadableField(mapping="banner_file", fileNameProperty="banner")
    * @Assert\File(
    *     maxSize = "3000k",
    *     mimeTypes = {"image/png", "image/jpeg"},
    *     mimeTypesMessage = "Seuls les formats jpg, jpeg et png sont acceptés"
    * )
    * @var File
    */
    private $bannerFile;

    /**
    * @ORM\Column(type="string", length=255, nullable=true)
    * @var string
    */
    private $profilePicture;

    /**
    * @Vich\UploadableField(mapping="profile_picture_file", fileNameProperty="profile_picture")
    * @Assert\File(
    *     maxSize = "3000k",
    *     mimeTypes = {"image/png", "image/jpeg"},
    *     mimeTypesMessage = "Seuls les formats jpg, jpeg et png sont acceptés"
    * )
    * @var File
    */
    private $profilePictureFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var Datetime
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Download::class, mappedBy="user")
     */
    private $downloads;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $rgpdAccepted;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    public function __construct()
    {
        $this->expertise = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->ebooks = new ArrayCollection();
        $this->downloads = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(?int $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getSiretNumber(): ?string
    {
        return $this->siretNumber;
    }

    public function setSiretNumber(?string $siretNumber): self
    {
        $this->siretNumber = $siretNumber;

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

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function setTown(?string $town): self
    {
        $this->town = $town;

        return $this;
    }

    public function getZipcode(): ?int
    {
        return $this->zipcode;
    }

    public function setZipcode(?int $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(?string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return Collection|Expertise[]
     */
    public function getExpertise(): Collection
    {
        return $this->expertise;
    }

    public function addExpertise(Expertise $expertise): self
    {
        if (!$this->expertise->contains($expertise)) {
            $this->expertise[] = $expertise;
        }

        return $this;
    }

    public function removeExpertise(Expertise $expertise): self
    {
        $this->expertise->removeElement($expertise);

        return $this;
    }

    /**
     * @return Collection|Contact[]
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setUser($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getUser() === $this) {
                $contact->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Service[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->setUser($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getUser() === $this) {
                $service->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ebook[]
     */
    public function getEbooks(): Collection
    {
        return $this->ebooks;
    }

    public function addEbook(Ebook $ebook): self
    {
        if (!$this->ebooks->contains($ebook)) {
            $this->ebooks[] = $ebook;
            $ebook->setUser($this);
        }

        return $this;
    }

    public function removeEbook(Ebook $ebook): self
    {
        if ($this->ebooks->removeElement($ebook)) {
            // set the owning side to null (unless already changed)
            if ($ebook->getUser() === $this) {
                $ebook->setUser(null);
            }
        }

        return $this;
    }

    public function setLogoFile(File $logo = null)
    {
        $this->logoFile = $logo;
        if ($logo) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getLogoFile(): ?File
    {
        return $this->logoFile;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function setBannerFile(File $banner = null)
    {
        $this->bannerFile = $banner;
        if ($banner) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getBannerFile(): ?File
    {
        return $this->bannerFile;
    }

    public function getBanner(): ?string
    {
        return $this->banner;
    }

    public function setBanner(?string $banner): self
    {
        $this->banner = $banner;

        return $this;
    }

    public function setProfilePictureFile(File $profilePicture = null)
    {
        $this->profilePictureFile = $profilePicture;
        if ($profilePicture) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getProfilePictureFile(): ?File
    {
        return $this->profilePictureFile;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;

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

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
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
            $download->setUser($this);
        }

        return $this;
    }

    public function removeDownload(Download $download): self
    {
        if ($this->downloads->removeElement($download)) {
            // set the owning side to null (unless already changed)
            if ($download->getUser() === $this) {
                $download->setUser(null);
            }
        }

        return $this;
    }

    public function getRgpdAccepted(): ?bool
    {
        return $this->rgpdAccepted;
    }

    public function setRgpdAccepted(?bool $rgpdAccepted): self
    {
        $this->rgpdAccepted = $rgpdAccepted;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
