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
        Passport::actingAs($user);

        $post = factory(Post::class)->create();

        $this->json('GET', route('post.show', $post))
            ->assertSuccessful()
            ->assertJsonFragment([
                'title' => $post->title,
                'content' => $post->content,
            ]);
    }

    /** @test */
    public function userCanViewAllTheirPosts()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $posts = factory(Post::class, 5)->create();
        $otherPosts = factory(Post::class, 10)->create(['user_id' => factory(User::class)->create()->id]);

        $this->json('GET', route('post.index', ['my-posts']))
            ->assertSuccessful()
            ->assertJsonCount($posts->count(), 'data');
    }

    /** @test */
    public function userCanViewAllPosts()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $posts = factory(Post::class, 5)->create();
        $otherPosts = factory(Post::class, 10)->create(['user_id' => factory(User::class)->create()->id]);

        $this->json('GET', route('post.index'))
            ->assertSuccessful()
            ->assertJsonCount($posts->count() + $otherPosts->count(), 'data');
    }

    /** @test */
    public function userCanCreatePosts()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

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
        Passport::actingAs($user);

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
        Passport::actingAs($user);

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

        Passport::actingAs($user);

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

        Passport::actingAs($user);

        $this->json('DELETE', route('post.update', $post))
            ->assertForbidden();

        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    /** @test */
    public function aModeratorCanEditOtherUsersPosts()
    {
        $owner = factory(User::class)->create();
        $moderator = factory(User::class)->states('admin')->create();
        $post = factory(Post::class)->create(['user_id' => $owner->id]);

        Passport::actingAs($moderator);

        $this->json('PUT', route('post.update', $post), [
            'title' => $newTitle = $this->faker->sentence(3),
        ])->assertSuccessful();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => $newTitle
        ]);
    }

    /** @test */
    public function postsCanBeFilteredByAuthor()
    {
        $author = factory(User::class)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $posts = factory(Post::class, 5)->create();
        $postsToFind = factory(Post::class, 6)->create(['user_id' => $author->id]);

        $this->json('GET', route('post.index', ['user' => $author->id]))
            ->assertSuccessful()
            ->assertJsonCount($postsToFind->count(), 'data')
            ->assertJsonFragment([
                'id' => $postsToFind->random()->id
            ]);
    }

    /** @test */
    public function postsCanBeSplitIntoMultiplePages()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $posts = factory(Post::class, 50)->create();

        $this->json('GET', route('post.index'))
            ->assertSuccessful()
            ->assertJsonFragment([
                'current_page' => 1,
                'total' => $posts->count()
            ]);

        $this->json('GET', route('post.index', ['page' => 2]))
            ->assertSuccessful()
            ->assertJsonFragment([
                'current_page' => 2,
            ]);
    }
}
