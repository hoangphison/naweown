<?php

namespace Tests\Functional\Auth;

use Naweown\Link;
use Naweown\User;
use Tests\TestCase;
use function Naweown\carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AccountActivationControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testCannotActivateAnAccountWithANonExistentToken()
    {
        $this->get("account/activate/ss");

        $this->assertResponseStatus(404);
    }

    public function testCannotActivateAnAccountWithAnExpiredToken()
    {
        $user = $this->createUser();

        //Manually set the stage for failure, set the token date to 6 minutes past the current time
        Link::whereUserId(1)
            ->update(['created_at' => carbon()->subMinutes(6)]);

        $this->get("account/activate/{$user->link->token}");

        $this->assertSessionHas('token.expired');

        $this->assertRedirectedToRoute('dashboard');
    }

    public function testAnAccountWasSuccessfullyActivated()
    {
        $user = $this->createUser();

        $this->actingAs($user);

        $this->get("account/activate/{$user->link->token}");

        $this->dontSeeInDatabase('links', ['user_id' => 1]);
        $this->assertSessionMissing('token.expired');
        $this->assertSessionHas('account.activated');
        $this->assertRedirectedToRoute('dashboard');
    }

    public function testAnAccountWasSuccessfullyActivatedEvenIfTheUserIsNotLoggedIn()
    {
        $user = $this->createUser();

        $this->get("account/activate/{$user->link->token}");

        $this->dontSeeInDatabase('links', ['user_id' => 1]);
        $this->assertSessionMissing('token.expired');
        $this->assertSessionHas('account.activated');
        $this->assertRedirectedToRoute('dashboard');
    }

    public function testOnlyUnActivatedAccountsCanBeActivated()
    {
        //Delete the token for this user
        //After which we set the `is_email_validated` property to "true"
        //Then a 404 error MUST be thrown since we do not have this token anymore

        $user = $this->createUser();
        
        $token = $user->link->token;

        $user->link()->delete();
        $user->update(['is_email_validated' => User::EMAIL_VALIDATED]);

        $this->get("account/activate/{$token}");
        $this->assertResponseStatus(404);
    }
}
