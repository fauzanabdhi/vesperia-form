<?php

namespace Tests\Feature;

use Tests\TestCase;

class FormPageTest extends TestCase
{
    public function test_form_page_is_available(): void
    {
        $this->withoutVite();

        $this->get('/')
            ->assertOk()
            ->assertSee('Loading form…');
    }
}