<?php
return [
    'required' => 'Polje :attribute je obvezno.',
    'email' => 'Polje :attribute mora biti veljaven e-poštni naslov.',
    'unique' => 'Polje :attribute je že zasedeno.',
    'min' => [
        'string' => 'Polje :attribute mora biti najmanj :min znakov.',
    ],
    'max' => [
        'string' => 'Polje :attribute ne sme biti daljše od :max znakov.',
    ],
    'confirmed' => 'Potrditev polja :attribute se ne ujema.',
    'password' => 'Geslo je napačno.',
    
    'attributes' => [
        'name' => 'ime',
        'email' => 'e-pošta',
        'password' => 'geslo',
        'password_confirmation' => 'potrditev gesla',
    ],
];
