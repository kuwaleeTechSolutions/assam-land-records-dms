<?php
namespace Tests\Feature\Security;
use Tests\TestCase;
class SecurityHeadersTest extends TestCase{public function test_security_headers_are_present():void{$response=$this->get('/login');$response->assertHeader('X-Content-Type-Options','nosniff');$response->assertHeader('Referrer-Policy','strict-origin-when-cross-origin');}}
