<?php

namespace Jazzyweb\doDocBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Jazzyweb\doDocBundle\Entity\User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Jazzyweb\doDocBundle\UserRepository")
 * 
 * @UniqueEntity(fields="email", message="The mail is already in use")
 * @UniqueEntity(fields="username", message="The username is already in use")
 */
class User implements AdvancedUserInterface {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     * 
     * @Assert\NotBlank()
     * @Assert\MaxLength(255)
     */
    private $name;

    /**
     * @var string $surname
     *
     * @ORM\Column(name="surname", type="string", length=255)
     */
    private $surname;

    /**
     * @var string $salt
     *
     * @ORM\Column(name="salt", type="string", length=255)
     * 
     * @Assert\MaxLength(255)
     */
    private $salt;

    /**
     * @var string $username
     *
     * @ORM\Column(name="username", type="string", length=255)
     * 
     * @Assert\NotBlank()
     * @Assert\MaxLength(255)
     * @Assert\Regex(
     *     pattern="/^[\w-]+$/",
     *     message="The username field must contain just alphanumeric characters")
     * 
     */
    private $username;

    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length=255)
     * 
     * @Assert\NotBlank()
     * @Assert\MaxLength(255)
     * @Assert\Regex(
     *     pattern="/^[\w-]+$/",
     *     message="The password field must contain just alphanumeric characters")
     */
    private $password;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255)
     * 
     * @Assert\NotBlank()
     * @Assert\MaxLength(255)
     * @Assert\Email(
     *     message = "Address '{{ value }}' isn't valid.")
     */
    private $email;

    /**
     * @var boolean $isActive
     *
     * @ORM\Column(name="isActive", type="boolean")
     * 
     * @Assert\Type(type="bool", message="Value {{ value }} must be {{ type }}.")
     */
    private $isActive;

        
    /**
     * @var string $password_again
     * 
     * @Assert\NotBlank()
     * @Assert\MaxLength(255)
     * @Assert\Regex(
     *     pattern="/^[\w-]+$/",
     *     message="The password field must contain just alphanumeric characters")
     */
    private $password_again;

    /**
     * Set passwordAgain
     *
     * @param string $password
     */
    public function setPasswordAgain($password) {
        $this->password_again = $password;
    }

    /**
     * Get passwordAgain
     *
     * @return string 
     */
    public function getPasswordAgain() {
        return $this->password_again;
    }

    ////ASSOCIATIONS////
   
    /**
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="users")
     */
    private $groups;
    
    /**
     * @ORM\ManyToMany(targetEntity="Book", inversedBy="users")
     */
    private $books;

    ////FIN ASOCIACIONES////

    public function __construct() {        
        $this->books = new ArrayCollection();
        $this->groups = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set surname
     *
     * @param string $surname
     */
    public function setSurname($surname) {
        $this->surname = $surname;
    }

    /**
     * Get apellidos
     *
     * @return string 
     */
    public function getSurname() {
        return $this->surname;
    }

    /**
     * Set salt
     *
     * @param string $salt
     */
    public function setSalt($salt) {
        $this->salt = $salt;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt() {
        return $this->salt;
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username) {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     */
    public function setIsActive($isActive) {
        $this->isActive = $isActive;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive() {
        return $this->isActive;
    }
   
    /**
     * Add books
     *
     * @param Jazzyweb\doDocBundle\Entity\Book $books
     */
    public function addBook(\Jazzyweb\doDocBundle\Entity\Book $book) {
        $this->books[] = $book;
    }
    
    /**
     * Add groups
     *
     * @param Jazzyweb\doDocBundle\Entity\Group $groups
     */
    public function addGroup(\Jazzyweb\doDocBundle\Entity\Group $group) {
        $this->groups[] = $group;
    }

    /**
     * Get books
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getBooks() {
        return $this->books;
    }
    
     /**
     * Get groups
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getGroups() {
        return $this->groups;
    }

    public function eraseCredentials() {
        
    }

    function equals(UserInterface $user) {
        return $user->getUsername() === $this->username;
    }

    public function getRoles() {
        $roles = array();
        foreach ($this->grupos as $g) {
            $roles[] = $g->getRol();
        }

        return $roles;
    }

    public function isAccountNonExpired() {
        return true;
    }

    public function isAccountNonLocked() {
        return true;
    }

    public function isCredentialsNonExpired() {
        return true;
    }

    public function isEnabled() {
        return $this->getIsActive();
    }

    /**
     * @Assert\True(message = "Has escrito dos password distintos")
     */
    public function isPasswordOK() {
        return ($this->password === $this->password_again);
    }
    
    public function __toString()
    {
        return $this->getName().' '.$this->getSurname();
    }

}
