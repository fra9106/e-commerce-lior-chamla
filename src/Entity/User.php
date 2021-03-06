<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *      fields={"email"},
 *      message = "Ce mail est déjà utilisé !")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     * @Assert\Email(
     *     message = "Cet email '{{ value }}' n'est pas un email valide !."
     * )
     * @Assert\NotBlank(message="email obligatoire !")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Length(
     *      min = 4,
     *      max = 150,
     *      minMessage = "Votre mot de passe doit être au minimum de {{ limit }} caratères de long",
     *      maxMessage = "Votre mot de passe doit être au maximum de {{ limit }} caratères de long"
     * )
     * @Assert\NotBlank(message="Mot de passe obligatoire !")
     */
    private $password;

    /**
     * 
     * @Assert\EqualTo(propertyPath="password") 
     */
    public $confirm_password;

    /**
     * @ORM\Column(type="string", length=225)
     * @Assert\NotBlank(message="Prénom obligatoire !")
     * @Assert\Length(
     *      min = 4,
     *      max = 50,
     *      minMessage = "Votre prénom doit être au minimum de {{ limit }} caratères de long",
     *      maxMessage = "Votre prénom doit être au maximum de {{ limit }} caratères de long"
     * )
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=225)
     * @Assert\NotBlank(message="Nom obligatoire !")
     * @Assert\Length(
     *      min = 4,
     *      max = 50,
     *      minMessage = "Votre nom doit être au minimum de {{ limit }} caratères de long",
     *      maxMessage = "Votre nom doit être au maximum de {{ limit }} caratères de long"
     * )
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=70, nullable=true)
     */
    private $activation_token;

    /**
     * @ORM\OneToMany(targetEntity=Purchase::class, mappedBy="user", orphanRemoval=true)
     * @OrderBy({"purchaseAt" = "DESC"})
     */
    private $purchases;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $picture;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $billingAddress;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $city;

    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
        $this->purchases = new ArrayCollection();
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    public function getActivationToken(): ?string
    {
        return $this->activation_token;
    }

    public function setActivationToken(?string $activation_token): self
    {
        $this->activation_token = $activation_token;

        return $this;
    }

    /**
     * @return Collection|Purchase[]
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(Purchase $purchase): self
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases[] = $purchase;
            $purchase->setUser($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): self
    {
        if ($this->purchases->removeElement($purchase)) {
            // set the owning side to null (unless already changed)
            if ($purchase->getUser() === $this) {
                $purchase->setUser(null);
            }
        }

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getBillingAddress(): ?string
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(string $billingAddress): self
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }
}
