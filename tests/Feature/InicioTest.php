<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InicioTest extends TestCase
{
    public function test_home_page_renders(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Inicio'));
    }

    public function test_unknown_pages_render_the_themed_not_found_page(): void
    {
        $this->get('/ruta-que-no-existe')
            ->assertNotFound()
            ->assertInertia(fn (Assert $page) => $page->component('Error')->where('estado', 404));
    }
}
