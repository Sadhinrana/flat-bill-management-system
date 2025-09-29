<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Set up common test data or configurations
    $this->artisan('migrate:fresh');
});



