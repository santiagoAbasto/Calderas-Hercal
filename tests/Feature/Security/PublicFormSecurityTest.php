<?php

namespace Tests\Feature\Security;

use App\Models\Contact;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PublicFormSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_honeypot_submission_is_discarded(): void
    {
        config(['security.forms.honeypot_field' => 'company']);

        $response = $this->from('/contacto')->post('/contacto', [
            'name' => 'Bot',
            'last_name' => 'Runner',
            'email' => 'bot@example.com',
            'phone' => '+54 11 1234 1234',
            'message' => 'spam',
            'company' => 'I am a bot',
        ]);

        $response->assertRedirect('/contacto');
        $this->assertDatabaseCount('contacts', 0);
    }

    public function test_contact_submission_stores_a_hashed_ip_fingerprint(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.5'])
            ->post('/contacto', [
                'name' => 'Ana',
                'last_name' => 'Perez',
                'email' => 'ANA@Example.com',
                'phone' => '+54 11 1234 1234',
                'message' => '<b>Necesito info</b>',
            ])
            ->assertRedirect();

        $contact = Contact::query()->firstOrFail();

        $this->assertSame('ana@example.com', $contact->email);
        $this->assertSame('Necesito info', $contact->message);
        $this->assertNotSame('203.0.113.5', $contact->ip_address);
        $this->assertSame(64, strlen((string) $contact->ip_address));
    }

    public function test_public_form_rate_limit_applies_per_email_and_ip(): void
    {
        config([
            'security.forms.max_attempts' => 2,
            'security.forms.decay_seconds' => 300,
        ]);

        $payload = [
            'name' => 'Ana',
            'last_name' => 'Perez',
            'email' => 'ana@example.com',
            'phone' => '+54 11 1234 1234',
            'message' => 'Consulta',
            'subject_type' => 'service',
        ];

        $this->from('/presupuesto')->post('/presupuesto', $payload)->assertRedirect('/presupuesto');
        $this->from('/presupuesto')->post('/presupuesto', $payload)->assertRedirect('/presupuesto');

        $this->from('/presupuesto')
            ->post('/presupuesto', $payload)
            ->assertSessionHasErrors('email');
    }

    public function test_security_headers_are_sent_on_web_responses(): void
    {
        $response = $this->get('/admin/login');

        $response->assertHeader('Content-Security-Policy');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    public function test_page_section_media_rejects_svg_uploads(): void
    {
        config(['security.admin_allowed_emails' => ['admin@example.com']]);

        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
        ]);

        $page = Page::create([
            'slug' => 'home',
            'name' => 'Home',
            'is_active' => true,
        ]);

        $section = PageSection::create([
            'page_id' => $page->id,
            'section_key' => 'hero',
            'title' => 'Hero',
            'order' => 'A',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post("/admin/sections/{$section->id}/media", [
            'page_section_id' => $section->id,
            'type' => 'image',
            'file' => UploadedFile::fake()->create('icon.svg', 5, 'image/svg+xml'),
        ]);

        $response->assertSessionHasErrors('file');
    }
}
