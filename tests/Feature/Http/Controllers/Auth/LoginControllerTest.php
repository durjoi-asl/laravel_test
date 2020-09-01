<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use App\User;


class LoginControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // public function testExample()
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    // public function createApplication()
    // {
    //     putenv('DB_DEFAULT=mysql');

    //     $app = require __DIR__ . '/../../../../../bootstrap/app.php';

    //     $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

    //     return $app;
    // }

    // public function setUp() :void
    // {
    //     parent::setUp();
    //     Artisan::call('migrate');
    // }

    // public function tearDown() :void
    // {
    //     Artisan::call('migrate:reset');
    //     parent::tearDown();
    // }



    /** @test */
    public function login_displays_the_login_form() 
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);

        $response->assertViewIs('auth.login');
    }

    /** @test */
    public function login_dispplays_validation_errors() 
    {
        $response = $this->post(route('login'), []);

        $response->assertStatus(302);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function login_authenticates_and_redirects_user() 
    {
        $user = factory(User::class)->create();
        
        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function register_creates_and_authenticates_a_user() 
    {
        $name = $this->faker->name;
        $email = $this->faker->safeEmail;
        $password = $this->faker->password(8);

        $response = $this->post(route('register'), [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $response->assertRedirect(route('home'));

        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email,
        ]);

        $user = User::where('email', $email)->where('name', $name)->first();
        $this->assertNotNull($user);

        $this->assertAuthenticatedAs($user);
    }
}
