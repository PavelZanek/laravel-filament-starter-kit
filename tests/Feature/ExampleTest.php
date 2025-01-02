<?php

declare(strict_types=1);

it('has a welcome page', function () {
    $this->get('/')->assertStatus(200);
});
