<?php

namespace Tests\Feature;

use App\Post;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function authenticatedUserCanViewTheirPost()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user, ['view-posts']);

        $post = factory(Post::class)->create();

        $this->json('GET', route('post.show', $post))
            ->assertSuccessful()
            ->assertJson([
                'title' => $post->title,
                'content' => $post->content,
            ]);
    }

    /** @test */
    public function userCanViewAllTheirPosts()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user, ['view-posts']);

        $posts = factory(Post::class, 5)->create();

        $this->json('GET', route('post.index'))
            ->assertSuccessful()
            ->assertJsonCount($posts->count());
    }

    /** @test */
    public function userCanCreatePosts()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user, ['create-posts']);

        $post = factory(Post::class)->make(); // Only creates the object, without saving it to db

        $this->json('POST', route('post.store'), [
            'title' => $post->title,
            'content' => $post->content,
        ])->assertSuccessful();

        $this->assertDatabaseHas('posts', [
            'title' => $post->title,
            'user_id' => $user->id
        ]);
    }

    /** @test */
    public function userCanUpdateTheirPost()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user, ['update-posts']);

        $post = factory(Post::class)->create();

        $this->json('PUT', route('post.update', $post), [
            'title' => $newTitle = $this->faker->sentence(3),
        ])->assertSuccessful();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => $newTitle
        ]);
    }

    /** @test */
    public function userCanDeleteTheirPost()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user, ['delete-posts']);

        $post = factory(Post::class)->create();

        $this->json('DELETE', route('post.update', $post))
            ->assertSuccessful();

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    /** @test */
    public function userCanNotUpdateAnotherUsersPost()
    {
        $owner = factory(User::class)->create();
        $user = factory(User::class)->create();
        $post = factory(Post::class)->create(['user_id' => $owner->id]);

        Passport::actingAs($user, ['update-posts']);

        $this->json('PUT', route('post.update', $post), [
            'title' => $newTitle = $this->faker->sentence(3),
        ])->assertForbidden();

        $this->assertDatabaseMissing('posts', ['title' => $newTitle]);
    }

    /** @test */
    public function userCanNotDeleteAnotherUsersPost()
    {
        $owner = factory(User::class)->create();
        $user = factory(User::class)->create();
        $post = factory(Post::class)->create(['user_id' => $owner->id]);

        Passport::actingAs($user, ['delete-posts']);

        $this->json('DELETE', route('post.update', $post))
            ->assertForbidden();

        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    /** @test */
    public function aModeratorCanEditOtherUsersPosts()
    {
        $owner = factory(User::class)->create();
        $moderator = factory(User::class)->create();
        $post = factory(Post::class)->create(['user_id' => $owner->id]);

        Passport::actingAs($moderator, ['moderate-posts']);

        $this->json('PUT', route('post.update', $post), [
            'title' => $newTitle = $this->faker->sentence(3),
        ])->assertSuccessful();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => $newTitle
        ]);
    }
}
