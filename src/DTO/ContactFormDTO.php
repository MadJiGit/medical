<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ContactFormDTO
{
    #[Assert\NotBlank(message: 'Name is required')]
    #[Assert\Length(min: 3, max: 255, minMessage: 'Name must be at least 3 characters')]
    public ?string $name = null;

    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Please enter a valid email')]
    public ?string $email = null;

    #[Assert\Length(max: 50)]
    public ?string $phone = null;

    #[Assert\NotBlank(message: 'Message is required')]
    public ?string $message = null;

    public ?string $product_id = null;

}
