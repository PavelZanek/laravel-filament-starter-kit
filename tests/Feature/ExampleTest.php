<?php

it('has a welcome page', function () {
    $this->get('/')->assertStatus(200);
});
