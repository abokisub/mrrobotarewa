<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MrRobotStart extends Command
{
    protected $signature = 'mrrobot:start';
    protected $description = 'Start the entire MrRobot Ecosystem (Dashboard Server + Background Trading Scheduler) with one command';

    public function handle()
    {
        $this->output->write(chr(27)."[2J".chr(27)."[;H"); // Clear terminal screen
        
        $this->line("<fg=green>🤖============================================================🤖</>");
        $this->line("<fg=green>                      MRROBOT STARTUP SEQUENCE                 </>");
        $this->line("<fg=green>🤖============================================================🤖</>");
        $this->newLine();

        // 1. Start the Background Scheduler (Autopilot Heartbeat)
        $this->info("⚡️ Step 1: Booting Background Trading Engine...");
        
        // This command runs php artisan schedule:work in the background on Mac/Linux
        $command = 'php artisan schedule:work > /dev/null 2>&1 &';
        exec($command, $output, $resultCode);

        if ($resultCode === 0) {
            $this->info("✅ Success: Background Autopilot is ONLINE and watching the market.");
        } else {
            $this->error("❌ Error: Could not launch background scheduler.");
        }

        $this->newLine();

        // 2. Start the Interactive Dashboard
        $this->info("📊 Step 2: Spinning up your Professional Dashboard...");
        $this->line("<fg=gray>Dashboard URL: http://127.0.0.1:8000</>");
        $this->line("<fg=gray>Press CTRL + C to stop the server and the bot.</>");
        $this->newLine();

        // Pass control to the native Laravel serve command
        $this->call('serve');
    }
}
