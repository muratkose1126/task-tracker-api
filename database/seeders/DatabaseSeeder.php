<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Space;
use App\Models\SpaceMember;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create additional users
        $users = User::factory(5)->create();

        // Create workspaces
        $workspace1 = Workspace::create([
            'name' => 'Acme Inc',
            'slug' => 'acme-inc-'.\Str::random(6),
            'owner_id' => $user->id,
            'description' => 'Main company workspace',
            'settings' => [],
        ]);

        $workspace2 = Workspace::create([
            'name' => 'Beta Team',
            'slug' => 'beta-team-'.\Str::random(6),
            'owner_id' => $user->id,
            'description' => 'Side project workspace',
            'settings' => [],
        ]);

        // Add workspace members
        WorkspaceMember::create([
            'workspace_id' => $workspace1->id,
            'user_id' => $user->id,
            'role' => 'owner',
        ]);

        WorkspaceMember::create([
            'workspace_id' => $workspace2->id,
            'user_id' => $user->id,
            'role' => 'owner',
        ]);

        // Add other users as members
        foreach ($users->take(3) as $member) {
            WorkspaceMember::create([
                'workspace_id' => $workspace1->id,
                'user_id' => $member->id,
                'role' => 'member',
            ]);
        }

        // Create public spaces
        $marketingSpace = Space::create([
            'workspace_id' => $workspace1->id,
            'name' => 'Marketing',
            'visibility' => 'public',
            'color' => '#3b82f6',
            'is_archived' => false,
        ]);

        $engineeringSpace = Space::create([
            'workspace_id' => $workspace1->id,
            'name' => 'Engineering',
            'visibility' => 'public',
            'color' => '#22c55e',
            'is_archived' => false,
        ]);

        // Create private space
        $privateSpace = Space::create([
            'workspace_id' => $workspace1->id,
            'name' => 'Executive',
            'visibility' => 'private',
            'color' => '#ef4444',
            'is_archived' => false,
        ]);

        // Add space members for private space
        SpaceMember::create([
            'space_id' => $privateSpace->id,
            'user_id' => $user->id,
            'role' => 'admin',
        ]);

        // Create groups in marketing space
        $campaignsGroup = Group::create([
            'space_id' => $marketingSpace->id,
            'name' => 'Campaigns',
            'color' => '#8b5cf6',
        ]);

        $contentGroup = Group::create([
            'space_id' => $marketingSpace->id,
            'name' => 'Content',
            'color' => '#ec4899',
        ]);

        // Create lists in groups
        $q1CampaignList = TaskList::create([
            'space_id' => $marketingSpace->id,
            'group_id' => $campaignsGroup->id,
            'name' => 'Q1 Campaign',
            'status_schema' => [],
            'is_archived' => false,
        ]);

        $blogPostsList = TaskList::create([
            'space_id' => $marketingSpace->id,
            'group_id' => $contentGroup->id,
            'name' => 'Blog Posts',
            'status_schema' => [],
            'is_archived' => false,
        ]);

        // Create lists directly under space (no group)
        $sprintBacklogList = TaskList::create([
            'space_id' => $engineeringSpace->id,
            'group_id' => null,
            'name' => 'Sprint Backlog',
            'status_schema' => [],
            'is_archived' => false,
        ]);

        $bugFixesList = TaskList::create([
            'space_id' => $engineeringSpace->id,
            'group_id' => null,
            'name' => 'Bug Fixes',
            'status_schema' => [],
            'is_archived' => false,
        ]);

        // Create tasks
        Task::create([
            'list_id' => $q1CampaignList->id,
            'user_id' => $user->id,
            'title' => 'Design landing page',
            'description' => 'Create mockups for the Q1 campaign landing page',
            'status' => 'in_progress',
            'priority' => 'high',
            'due_date' => now()->addDays(7),
        ]);

        Task::create([
            'list_id' => $q1CampaignList->id,
            'user_id' => $users->first()->id,
            'title' => 'Write email copy',
            'description' => 'Draft email campaign for Q1 launch',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => now()->addDays(10),
        ]);

        Task::create([
            'list_id' => $blogPostsList->id,
            'user_id' => $user->id,
            'title' => 'Write product announcement post',
            'description' => 'Blog post about new features',
            'status' => 'todo',
            'priority' => 'high',
            'due_date' => now()->addDays(5),
        ]);

        Task::create([
            'list_id' => $sprintBacklogList->id,
            'user_id' => $users->get(1)->id,
            'title' => 'Implement user authentication',
            'description' => 'Add JWT token-based authentication',
            'status' => 'in_progress',
            'priority' => 'high',
            'due_date' => now()->addDays(3),
        ]);

        Task::create([
            'list_id' => $bugFixesList->id,
            'user_id' => $users->get(2)->id,
            'title' => 'Fix sidebar collapse issue',
            'description' => 'Sidebar doesnt collapse on mobile',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => now()->addDays(2),
        ]);
    }
}
