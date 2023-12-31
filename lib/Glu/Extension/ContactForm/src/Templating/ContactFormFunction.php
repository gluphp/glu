<?php

namespace Glu\Extension\ContactForm\Templating;

use Glu\Templating\_Function;

final class ContactFormFunction implements _Function
{
    public function name(): string
    {
        return 'contact_form';
    }

    public function callable(): callable
    {
        return function () {
            return <<<CODE
<form action="/contact-handle" method="post">

    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required aria-label="Enter your name">

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required aria-label="Enter your email">

    <label for="message">Message:</label>
    <textarea id="message" name="message" rows="4" maxlength="300" required aria-label="Type your message"></textarea>

    <button type="submit">Submit</button>

</form>
CODE;
        };
    }

    public function escape(): bool
    {
        return false;
    }
}
