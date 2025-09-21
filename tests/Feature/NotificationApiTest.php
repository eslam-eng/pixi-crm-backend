<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\Tenant\TaskEscalationNotification;
use App\Models\Tenant\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create();
        
        // Create a test task
        $this->task = Task::factory()->create([
            'assigned_to_id' => $this->user->id,
            'title' => 'Test Task',
            'description' => 'Test Description',
            'due_date' => now()->subDay()->toDateString(),
            'due_time' => now()->subHour()->toTimeString(),
        ]);
    }

    /** @test */
    public function it_can_get_user_notifications()
    {
        // Send a notification to the user
        $this->user->notify(new TaskEscalationNotification($this->task));

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'type',
                            'type_display',
                            'title',
                            'message',
                            'is_read',
                            'created_at',
                            'action_url',
                            'icon',
                            'priority',
                            'category'
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_can_get_unread_count()
    {
        // Send multiple notifications
        $this->user->notify(new TaskEscalationNotification($this->task));
        $this->user->notify(new TaskEscalationNotification($this->task));

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications/unread-count');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'unread_count' => 2
                ]
            ]);
    }

    /** @test */
    public function it_can_get_notification_statistics()
    {
        // Send notifications
        $this->user->notify(new TaskEscalationNotification($this->task));
        $this->user->notify(new TaskEscalationNotification($this->task));

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'total',
                    'unread',
                    'read',
                    'by_type'
                ]
            ]);
    }

    /** @test */
    public function it_can_mark_notification_as_read()
    {
        // Send a notification
        $this->user->notify(new TaskEscalationNotification($this->task));
        $notification = $this->user->notifications()->first();

        $response = $this->actingAs($this->user, 'sanctum')
            ->patchJson("/api/notifications/{$notification->id}/mark-read");

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Notification marked as read successfully'
            ]);

        // Verify notification is marked as read
        $this->assertNotNull($notification->fresh()->read_at);
    }

    /** @test */
    public function it_can_mark_all_notifications_as_read()
    {
        // Send multiple notifications
        $this->user->notify(new TaskEscalationNotification($this->task));
        $this->user->notify(new TaskEscalationNotification($this->task));

        $response = $this->actingAs($this->user, 'sanctum')
            ->patchJson('/api/notifications/mark-all-read');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'All notifications marked as read successfully'
            ]);

        // Verify all notifications are marked as read
        $this->assertEquals(0, $this->user->unreadNotifications()->count());
    }

    /** @test */
    public function it_can_delete_specific_notification()
    {
        // Send a notification
        $this->user->notify(new TaskEscalationNotification($this->task));
        $notification = $this->user->notifications()->first();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/notifications/{$notification->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Notification deleted successfully'
            ]);

        // Verify notification is deleted
        $this->assertDatabaseMissing('notifications', [
            'id' => $notification->id
        ]);
    }

    /** @test */
    public function it_can_filter_notifications_by_type()
    {
        // Send notifications
        $this->user->notify(new TaskEscalationNotification($this->task));

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications?type=TaskEscalationNotification');

        $response->assertStatus(200);
        
        $notifications = $response->json('data.data');
        $this->assertCount(1, $notifications);
        $this->assertStringContainsString('TaskEscalationNotification', $notifications[0]['type']);
    }

    /** @test */
    public function it_can_filter_unread_notifications_only()
    {
        // Send notifications
        $this->user->notify(new TaskEscalationNotification($this->task));
        $this->user->notify(new TaskEscalationNotification($this->task));
        
        // Mark one as read
        $notification = $this->user->notifications()->first();
        $notification->markAsRead();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications?unread_only=true');

        $response->assertStatus(200);
        
        $notifications = $response->json('data.data');
        $this->assertCount(1, $notifications);
        $this->assertFalse($notifications[0]['is_read']);
    }

    /** @test */
    public function it_can_get_view_all_notifications()
    {
        // Send more than 20 notifications
        for ($i = 0; $i < 25; $i++) {
            $this->user->notify(new TaskEscalationNotification($this->task));
        }

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications?view_all=true');

        $response->assertStatus(200);
        
        $notifications = $response->json('data');
        $this->assertCount(20, $notifications);
        $this->assertIsArray($notifications);
        $this->assertArrayNotHasKey('current_page', $notifications);
    }

    /** @test */
    public function it_returns_paginated_notifications_when_view_all_is_false()
    {
        // Send notifications
        for ($i = 0; $i < 10; $i++) {
            $this->user->notify(new TaskEscalationNotification($this->task));
        }

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications?view_all=false&per_page=5');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('current_page', $data);
        $this->assertArrayHasKey('per_page', $data);
        $this->assertCount(5, $data['data']);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->getJson('/api/notifications');
        $response->assertStatus(401);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_notification()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications/nonexistent-id');

        $response->assertStatus(404);
    }
}
