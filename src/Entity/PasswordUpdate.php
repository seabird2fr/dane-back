<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordUpdate
{

    private $id;


    private $oldPassword;

/** 
* @Assert\Length( 
* min = 8, 
* minMessage="Votre mot de passe doit faire un minimun de 8 caractères", 
*)
* @Assert\Regex(
*      pattern="/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$/",
*      message="Erreur de mot de passe: au moins 1 lettre majuscule, au moins 1 lettre minuscule, et au moins 1 chiffre",
* ) 
*/ 
    private $newPassword;

/** 
* @Assert\EqualTo(propertyPath="newPassword",message="Les deux mots de passe ne sont pas identiques") 
*/ 
    private $confirmPassword;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}
