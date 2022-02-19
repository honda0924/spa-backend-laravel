<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Task;
use App\Models\User;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user);

    }
    /**
     * @test
     */
    public function getList()
    {
        $tasks = Task::factory()->count(10)->create();

        $response = $this->getJson('api/tasks');

        $response->assertOk()
            ->assertJsonCount($tasks->count());
    }

    /**
     * @test
     */
    public function successRegister()
    {
        $data = [
            'title' => 'テスト投稿'
        ];
        $response = $this->postJson('api/tasks', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);
    }
    /**
     * @test
     */
    public function successUpdate()
    {
        $task = Task::factory()->create();
        $task->title = '更新タイトル';
        $response = $this->patchJson("api/tasks/{$task->id}", $task->toArray());
        $response->assertOk()
            ->assertJsonFragment($task->toArray());
    }

    /**
     * @test
     */
    public function successDelete()
    {
        $tasks = Task::factory()->count(10)->create();

        $response = $this->deleteJson("api/tasks/{$tasks[0]->id}");
        $response->assertOk();

        $response = $this->getJson('api/tasks');
        $response->assertJsonCount($tasks->count() - 1);
    }
    /**
     * @test
     */
    public function failNoTitleRegister()
    {
        $data = [
            'title' => ''
        ];
        $response = $this->postJson('api/tasks', $data);
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title' => 'タイトルは、必ず指定してください。'
            ]);
    }
    /**
     * @test
     */
    public function failOverMaxCharTitleRegister()
    {
        $data = [
            'title' => str_repeat('あ', 256)
        ];
        $response = $this->postJson('api/tasks', $data);
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title' => 'タイトルは、255文字以下にしてください。'
            ]);
    }
    /**
     * @test
     */
    public function failNoTitleUpdate()
    {
        $task = Task::factory()->create();
        $task->title = '';
        $response = $this->patchJson("api/tasks/{$task->id}", $task->toArray());
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title' => 'タイトルは、必ず指定してください。'
            ]);
    }
    /**
     * @test
     */
    public function failOverMaxCharTitleUpdate()
    {
        $task = Task::factory()->create();
        $task->title = str_repeat('あ', 256);
        $response = $this->patchJson("api/tasks/{$task->id}", $task->toArray());
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title' => 'タイトルは、255文字以下にしてください。'
            ]);
    }
}
