<?php

namespace Jazzyweb\doDocBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Jazzyweb\doDocBundle\Entity\Group
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Jazzyweb\doDocBundle\Entity\GroupRepository")
 */
class Group {

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
     */
    private $name;

    /**
     * @var string $rol
     *
     * @ORM\Column(name="rol", type="string", length=255)
     */
    private $rol;

    ////ASSOCIATIONS////

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="groups")
     */
    private $users;
   

    public function __construct() {
        $this->users = new ArrayCollection();
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
     * Set name
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
     * Set rol
     *
     * @param string $rol
     */
    public function setRol($rol) {
        $this->rol = $rol;
    }

    /**
     * Get rol
     *
     * @return string 
     */
    public function getRol() {
        return $this->rol;
    }

    /**
     * Add user
     *
     * @param Jazzyweb\doDocBundle\Entity\Usuario $usuarios
     */
    public function addUser(\Jazzyweb\doDocBundle\Entity\User $user) {
        $this->usuarios[] = $user;
    }

    /**
     * Get users
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUsers() {
        return $this->users;
    }

    public function __toString() {
        return $this->getName();
    }

}