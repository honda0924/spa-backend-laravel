<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Task;

class TaskTest extends TestCase
{
    use RefreshDatabase;
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


        // ->assertJsonFragment($task->toArray());
    }
}
