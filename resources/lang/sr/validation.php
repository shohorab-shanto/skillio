<?php
return [
    'required' => 'Polje :attribute je obavezno.',
    'email' => 'Polje :attribute mora biti valjana e-mail adresa.',
    'unique' => 'Polje :attribute je već zauzeto.',
    'min' => [
        'string' => 'Polje :attribute mora biti najmanje :min znakova.',
    ],
    'max' => [
        'string' => 'Polje :attribute ne smije biti duže od :max znakova.',
    ],
    'confirmed' => 'Potvrda polja :attribute se ne podudara.',
    'password' => 'Lozinka je netočna.',
    
    'attributes' => [
        'name' => 'ime',
        'email' => 'e-mail',
        'password' => 'lozinka',
        'password_confirmation' => 'potvrda lozinke',
    ],
];
