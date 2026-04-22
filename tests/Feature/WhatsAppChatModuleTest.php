<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\WhatsAppMessage;
use App\Livewire\Admin\Whatsapp\WhatsAppChatModule;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class WhatsAppChatModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user with the necessary permission
        $this->user = User::factory()->create();
        
        // Give the user whatsapp access permission if using Spatie permissions
        try {
            if (class_exists(\Spatie\Permission\Models\Permission::class)) {
                $permission = \Spatie\Permission\Models\Permission::firstOrCreate(
                    ['name' => 'access whatsapp'],
                    ['guard_name' => 'web']
                );
                $this->user->givePermissionTo($permission);
            }
        } catch (\Exception $e) {
            // Skip permission setup if not available
        }
    }

    /** @test */
    public function chat_module_route_exists()
    {
        $this->actingAs($this->user);
        
        $response = $this->get(route('admin.whatsapp.chat-module'));
        
        $this->assertNotEquals(404, $response->getStatusCode());
    }

    /** @test */
    public function chat_module_component_can_render()
    {
        $this->actingAs($this->user);

        Http::fake([
            '*/api/whatsapp/status' => Http::response([
                'connectionState' => 'connected',
                'user' => ['name' => 'Test User', 'id' => '584241234567'],
            ]),
            '*/api/whatsapp/conversations' => Http::response([
                'conversations' => [],
            ]),
        ]);

        Livewire::actingAs($this->user)
            ->test(WhatsAppChatModule::class)
            ->assertViewIs('livewire.admin.whatsapp.whatsapp-chat-module')
            ->assertSee('Nuevo Chat')
            ->assertSee('WhatsApp Chat');
    }

    /** @test */
    public function chat_module_shows_connection_status()
    {
        $this->actingAs($this->user);

        Http::fake([
            '*/api/whatsapp/status' => Http::response([
                'connectionState' => 'disconnected',
            ]),
            '*/api/whatsapp/conversations' => Http::response([
                'conversations' => [],
            ]),
        ]);

        Livewire::actingAs($this->user)
            ->test(WhatsAppChatModule::class)
            ->assertSee('Desconectado');
    }

    /** @test */
    public function chat_module_can_filter_conversations()
    {
        $this->actingAs($this->user);

        Http::fake([
            '*/api/whatsapp/status' => Http::response([
                'connectionState' => 'connected',
            ]),
            '*/api/whatsapp/conversations' => Http::response([
                'conversations' => [
                    ['peer' => '584241234567@s.whatsapp.net', 'name' => 'Contact 1', 'unreadCount' => 2, 'createdAt' => now()->toISOString()],
                    ['peer' => '584241234568@s.whatsapp.net', 'name' => 'Contact 2', 'unreadCount' => 0, 'createdAt' => now()->toISOString()],
                    ['peer' => 'group@g.us', 'name' => 'Test Group', 'unreadCount' => 0, 'createdAt' => now()->toISOString()],
                ],
            ]),
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(WhatsAppChatModule::class);

        // Filter by contacts
        $component->call('setFilter', 'contacts')
            ->assertSet('activeFilter', 'contacts');

        // Filter by groups
        $component->call('setFilter', 'groups')
            ->assertSet('activeFilter', 'groups');

        // Filter by unread
        $component->call('setFilter', 'unread')
            ->assertSet('activeFilter', 'unread');

        // Reset to all
        $component->call('setFilter', 'all')
            ->assertSet('activeFilter', 'all');
    }

    /** @test */
    public function chat_module_can_select_conversation()
    {
        $this->actingAs($this->user);

        Http::fake([
            '*/api/whatsapp/status' => Http::response([
                'connectionState' => 'connected',
            ]),
            '*/api/whatsapp/conversations' => Http::response([
                'conversations' => [],
            ]),
            '*/api/whatsapp/thread*' => Http::response([
                'messages' => [
                    [
                        'id' => 'msg1',
                        'from' => '584241234567@s.whatsapp.net',
                        'message' => json_encode(['conversation' => 'Hello!']),
                        'status' => 'delivered',
                        'createdAt' => now()->toISOString(),
                        'key' => ['fromMe' => false],
                    ],
                ],
            ]),
        ]);

        Livewire::actingAs($this->user)
            ->test(WhatsAppChatModule::class)
            ->call('selectConversation', '584241234567@s.whatsapp.net', 'Test Contact')
            ->assertSet('currentPeer', '584241234567@s.whatsapp.net')
            ->assertSet('currentPeerName', 'Test Contact');
    }

    /** @test */
    public function chat_module_can_send_message()
    {
        $this->actingAs($this->user);

        Http::fake([
            '*/api/whatsapp/status' => Http::response([
                'connectionState' => 'connected',
            ]),
            '*/api/whatsapp/conversations' => Http::response([
                'conversations' => [],
            ]),
            '*/api/whatsapp/send' => Http::response([
                'success' => true,
                'messageId' => 'msg_test_123',
            ]),
            '*/api/whatsapp/thread*' => Http::response([
                'messages' => [],
            ]),
        ]);

        Livewire::actingAs($this->user)
            ->test(WhatsAppChatModule::class)
            ->set('currentPeer', '584241234567@s.whatsapp.net')
            ->set('messageText', 'Hello World!')
            ->call('sendMessage')
            ->assertSet('messageText', '');
    }

    /** @test */
    public function chat_module_prevents_sending_when_disconnected()
    {
        $this->actingAs($this->user);

        Http::fake([
            '*/api/whatsapp/status' => Http::response([
                'connectionState' => 'disconnected',
            ]),
            '*/api/whatsapp/conversations' => Http::response([
                'conversations' => [],
            ]),
        ]);

        Livewire::actingAs($this->user)
            ->test(WhatsAppChatModule::class)
            ->set('currentPeer', '584241234567@s.whatsapp.net')
            ->set('messageText', 'Test message')
            ->call('sendMessage')
            ->assertDispatched('notify');
    }

    /** @test */
    public function chat_module_can_close_chat()
    {
        $this->actingAs($this->user);

        Http::fake([
            '*/api/whatsapp/status' => Http::response(['connectionState' => 'connected']),
            '*/api/whatsapp/conversations' => Http::response(['conversations' => []]),
            '*/api/whatsapp/thread*' => Http::response(['messages' => []]),
        ]);

        Livewire::actingAs($this->user)
            ->test(WhatsAppChatModule::class)
            ->set('currentPeer', '584241234567@s.whatsapp.net')
            ->call('closeChat')
            ->assertSet('currentPeer', null)
            ->assertSet('currentPeerName', null)
            ->assertSet('messages', []);
    }

    /** @test */
    public function chat_module_can_toggle_contact_info()
    {
        $this->actingAs($this->user);

        Http::fake([
            '*/api/whatsapp/status' => Http::response(['connectionState' => 'connected']),
            '*/api/whatsapp/conversations' => Http::response(['conversations' => []]),
        ]);

        Livewire::actingAs($this->user)
            ->test(WhatsAppChatModule::class)
            ->assertSet('showContactInfo', false)
            ->call('toggleContactInfo')
            ->assertSet('showContactInfo', true)
            ->call('toggleContactInfo')
            ->assertSet('showContactInfo', false);
    }

    /** @test */
    public function chat_module_can_search_conversations()
    {
        $this->actingAs($this->user);

        Http::fake([
            '*/api/whatsapp/status' => Http::response(['connectionState' => 'connected']),
            '*/api/whatsapp/conversations' => Http::response([
                'conversations' => [
                    ['peer' => '584241234567@s.whatsapp.net', 'name' => 'John Doe', 'createdAt' => now()->toISOString()],
                    ['peer' => '584241234568@s.whatsapp.net', 'name' => 'Jane Smith', 'createdAt' => now()->toISOString()],
                ],
            ]),
        ]);

        Livewire::actingAs($this->user)
            ->test(WhatsAppChatModule::class)
            ->set('searchQuery', 'John')
            ->assertSet('searchQuery', 'John');
    }

    /** @test */
    public function chat_module_loads_daily_stats()
    {
        $this->actingAs($this->user);

        Http::fake([
            '*/api/whatsapp/status' => Http::response(['connectionState' => 'connected']),
            '*/api/whatsapp/conversations' => Http::response(['conversations' => []]),
        ]);

        Livewire::actingAs($this->user)
            ->test(WhatsAppChatModule::class)
            ->assertSet('stats.sent_today', 0)
            ->assertSet('stats.received_today', 0);
    }

    /** @test */
    public function chat_module_can_start_new_chat()
    {
        $this->actingAs($this->user);

        Http::fake([
            '*/api/whatsapp/status' => Http::response(['connectionState' => 'connected']),
            '*/api/whatsapp/conversations' => Http::response(['conversations' => []]),
            '*/api/whatsapp/thread*' => Http::response(['messages' => []]),
            '*/api/whatsapp/send' => Http::response(['success' => true]),
        ]);

        Livewire::actingAs($this->user)
            ->test(WhatsAppChatModule::class)
            ->set('showNewChatModal', true)
            ->set('newChatPhone', '+584241234567')
            ->set('newChatMessage', 'Hola!')
            ->call('startNewChat')
            ->assertSet('showNewChatModal', false)
            ->assertNotNull('currentPeer');
    }

    /** @test */
    public function format_timestamp_returns_correct_format()
    {
        $this->actingAs($this->user);

        Http::fake([
            '*/api/whatsapp/status' => Http::response(['connectionState' => 'connected']),
            '*/api/whatsapp/conversations' => Http::response(['conversations' => []]),
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(WhatsAppChatModule::class);

        // Today should return time
        $today = now()->format('Y-m-d H:i:s');
        $result = $component->instance()->formatTimestamp($today);
        $this->assertMatchesRegularExpression('/^\d{2}:\d{2}$/', $result);

        // Yesterday should return 'Ayer'
        $yesterday = now()->subDay()->format('Y-m-d H:i:s');
        $result = $component->instance()->formatTimestamp($yesterday);
        $this->assertEquals('Ayer', $result);
    }

    /** @test */
    public function whatsapp_message_model_has_correct_statuses()
    {
        $message = new WhatsAppMessage([
            'status' => 'sent',
            'direction' => 'outbound',
        ]);
        $this->assertTrue($message->isSent());

        $message->status = 'delivered';
        $this->assertTrue($message->isDelivered());

        $message->status = 'read';
        $this->assertTrue($message->isRead());

        $message->status = 'failed';
        $this->assertTrue($message->isFailed());
    }

    /** @test */
    public function whatsapp_message_retry_logic_works()
    {
        $message = new WhatsAppMessage([
            'status' => 'failed',
            'direction' => 'outbound',
            'retry_count' => 0,
            'error_message' => 'Connection timeout',
        ]);

        $this->assertTrue($message->isRetryable());

        $message->retry_count = 3;
        $this->assertFalse($message->isRetryable());
    }
}
